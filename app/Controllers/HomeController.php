<?php
namespace app\Controllers;

use app\core\Controller;
use app\Models\HomeModel;
use app\Models\AboutModel;
use app\Models\SkillModel;
use app\Models\ProjectModel;
use app\Models\ContactModel;
use app\Services\CacheService;
use Throwable;

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
                return $this->view("home/index", $cached);
            }

            /* ---------------------------------------------------
             * 2. SAFE DB LOAD (per section)
             * --------------------------------------------------- */
            $data = [
                "home"     => $this->wrap($this->home->get(), "home"),
                "about"    => $this->wrap($this->about->get(), "about"),
                "skills"   => $this->wrap($this->skills->all(), "skills"),
                "projects" => $this->wrapProject($this->projects->getFeatured()),
                "contact"  => $this->wrap($this->contact->get(), "contact"),
            ];


            // /* ---------------------------------------------------
            //  * 3. Save to cache only if ALL data came from DB
            //  * --------------------------------------------------- */
            if ($this->hasRealData($data)) {
                CacheService::save($this->cacheKey, $data);
            }

            return $this->view("home/index", $data);

        } catch (Throwable $e) {

            /* ---------------------------------------------------
             * 4. PAGE-WIDE EMERGENCY FALLBACK
             * --------------------------------------------------- */
            app_log("HomeController@index FAILED: " . $e->getMessage(), "error");

            return $this->view("home/index", [
                "home"     => ["from_db" => false, "data" => []],
                "about"    => ["from_db" => false, "data" => []],
                "skills"   => ["from_db" => false, "data" => []],
                "projects" => ["from_db" => false, "data" => []],
                "contact"  => ["from_db" => false, "data" => []],
            ]);
        }
    }


    /* ============================================================
     * STANDARD WRAPPER FOR ALL MODELS
     * ============================================================ */
    private function wrap($data, string $label): array
    {
        if (!is_array($data) || empty($data) || isset($data["is_default"])) {
            return ["from_db" => false, "data" => $data ?: []];
        }

        return ["from_db" => true, "data" => $data];
    }

    /* ============================================================
     * PROJECT MODEL HAS EXPLICIT SOURCE
     * ============================================================ */
    private function wrapProject(array $payload): array
    {
        return [
            "from_db" => ($payload["source"] ?? "") === "db",
            "data"    => $payload["data"] ?? []
        ];
    }


    /* ============================================================
     * PAGE CACHE POLICY
     * ============================================================ */

    private function hasRealData(array $data): bool
    {
        foreach ($data as $section) {
            // fallback sections contain "is_default" OR empty
            if (($section["from_db"] ?? false) !== true) {
                return false; // fallback → do not cache
            }
        }
        return true;
    }
}
