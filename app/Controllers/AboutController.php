<?php

class AboutController extends Controller
{
    private AboutModel $about;

    public function __construct()
    {
        // Load AboutModel
        require_once ROOT_PATH . "app/Models/AboutModel.php";
        require_once ROOT_PATH . "app/Services/CacheService.php";

        $this->about = new AboutModel();
    }

    /**
     * ABOUT PAGE CONTROLLER
     * Loads everything from cache → DB → fallback
     */
    public function index()
    {
        try {
            // 1. Try to load from cache
            $cached = CacheService::load("about_page");

            if ($cached) {
                return $cached;
            }

            // 2. Load from DB (slow version)
            $data = [
                "hero"       => $this->about->getHero(),
                "content"    => $this->about->getContent(),
                "skills"     => $this->about->getSkills(),
                "experience" => $this->about->getExperience(),
                "education"  => $this->about->getEducation(),
                "stats"      => $this->about->getStats(),
            ];

            // 3. Save to cache
            CacheService::save("about_page", $data);

            return $data;

        } catch (Throwable $e) {

            app_log("AboutController@index failed", "error", [
                "error" => $e->getMessage()
            ]);

            // 4. Emergency fallback (never break UI)
            return [
                "hero"       => [],
                "content"    => ["greeting_title" => "About Me"],
                "skills"     => [],
                "experience" => [],
                "education"  => [],
                "stats"      => [],
            ];
        }
    }
}
