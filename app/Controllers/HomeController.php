<?php

class HomeController extends Controller
{
    private HomeModel $home;
    private AboutModel $about;
    private SkillModel $skills;
    private ProjectModel $projects;
    private ContactModel $contact;

    private string $cacheKey = "home_page";

    public function __construct()
    {
        // Load all models
        require_once ROOT_PATH . "app/Models/HomeModel.php";
        require_once ROOT_PATH . "app/Models/AboutModel.php";
        require_once ROOT_PATH . "app/Models/SkillModel.php";
        require_once ROOT_PATH . "app/Models/ProjectModel.php";
        require_once ROOT_PATH . "app/Models/ContactModel.php";
        require_once ROOT_PATH . "app/Services/CacheService.php";

        $this->home     = new HomeModel();
        $this->about    = new AboutModel();
        $this->skills   = new SkillModel();
        $this->projects = new ProjectModel();
        $this->contact  = new ContactModel();
    }

    /**
     * Home page controller
     * Returns ALL SECTIONS as cached + DB fallback data.
     */
    public function index()
    {
        try {

            /* ---------------------------------------------------
             * 1. Try loading full page from cache
             * --------------------------------------------------- */
            /* Cache → whole page */
            if ($cached = CacheService::load($this->cacheKey)) {
                return $cached;
            }

            /* ---------------------------------------------------
             * 2. SAFE DB LOAD (per section)
             * --------------------------------------------------- */
            $data = [
                "home"     => $this->safeLoad(fn() => $this->home->get(),       "home"),
                "about"    => $this->safeLoad(fn() => $this->about->get(),      "about"),
                "skills"   => $this->safeLoad(fn() => $this->skills->all(),     "skills"),
                "projects" => $this->safeLoad(fn() => $this->projects->getFeatured(), "projects"),
                "contact"  => $this->safeLoad(fn() => $this->contact->get(),    "contact"),
            ];


            // /* ---------------------------------------------------
            //  * 3. Save to cache only if ALL data came from DB
            //  * --------------------------------------------------- */
            if ($this->hasRealData($data)) {
                CacheService::save($this->cacheKey, $data);
            }

            return $data;

        } catch (Throwable $e) {

            /* ---------------------------------------------------
             * 4. PAGE-WIDE EMERGENCY FALLBACK
             * --------------------------------------------------- */
            app_log("HomeController@index FAILED: " . $e->getMessage(), "error");

            return [
                "home"     => $this->fallback("home"),
                "about"    => $this->fallback("about"),
                "skills"   => [],
                "projects" => [],
                "contact"  => $this->fallback("contact"),
            ];
        }
    }


    /* ============================================================
     * SECTION WRAPPERS (safe load)
     * ============================================================ */

    /**
     * Safely loads a model section.
     * Prevents any model failure from breaking the home page.
     */
    private function safeLoad(callable $fn, string $label)
    {
        try {
            $data = $fn();

            // If model returned non-array → fix it
            if (!is_array($data)) {
                // return ["from_db" => false] + $this->fallback($label);
                // convert to bootom return statement because create 1 extra black field in skills and others section
                return [
                    "from_db" => false,
                    "data"    => $this->fallback($label)
                ];
            }

            // CASE 1: Model returned fallback defaults (contains is_default)
            if (isset($data["is_default"]) && $data["is_default"] === true) {
                return [
                    "from_db" => false,
                    "data"    => $data
                ];
            }

            // CASE 2: Real DB data
            if (!empty($data)) {
                return [
                    "from_db" => true,
                    "data"    => $data
                ];
            }

            // CASE 3: Nothing returned → fallback
            return [
                "from_db" => false,
                "data"    => $this->fallback($label)
            ];
        } catch (Throwable $e) {
            app_log("HomeController: Failed loading section {$label}: " . $e->getMessage(), "warning");
            
            // return ["from_db" => false] + $this->fallback($label);
            return [
                "from_db" => false,
                "data"    => $this->fallback($label)
            ];
        }
    }


    /* ============================================================
     * FALLBACKS
     * ============================================================ */

    /**
     * Returns minimal guaranteed-safe fallback for each section
     */
    private function fallback(string $section)
    {
        return match ($section) {

            "home" => [
                "hero_heading"       => "Hi, I’m " . SITE_TITLE . " 👋",
                "hero_subheading"    => "Full Stack & Android Developer | Turning ideas into scalable digital products.",
                "hero_description"   => "I build fast, modern, scalable applications using PHP, MySQL, JavaScript, TailwindCSS, and Android.",
                "projects_link"      => "project.php",
                "cv_link"            => "assets/cv/Yogesh-CV.pdf",
                "animation_url"    => "https://assets10.lottiefiles.com/packages/lf20_kyu7xb1v.json",
                "background_image"   => IMG_URL . "default-hero-bg.jpg",
                "cta_projects"       => "View Projects",
                "cta_contact"        => "Contact Me",
                "is_active"        => true
            ],

            "about" => [
                "title"   => "About Me",
                "content" => "Hi, I'm Yogesh. I build optimized, scalable and user-friendly applications.",
            ],

            "contact" => [
                "title"       => "Contact Me",
                "subtitle"    => "You can reach me anytime.",
                "button_text" => "Email Us",
                "button_link" => "mailto:contact@example.com",
            ],

            default => []
        };
    }


    /* ============================================================
     * REAL DATA CHECK
     * Prevents cache poisoning & ensures non-empty payload
     * ============================================================ */

    private function hasRealData(array $data): bool
    {
        foreach ($data as $section) {
            // fallback sections contain "is_default" OR empty
            if (!is_array($section) || (isset($section["from_db"]) && $section["from_db"] !== true)) {
                return false; // fallback → do not cache
            }
        }
        return true;
    }
}
