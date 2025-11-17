<?php

class AboutController extends Controller
{
    private AboutModel $about;

    public function __construct()
    {
        require_once ROOT_PATH . "app/Models/AboutModel.php";
        require_once ROOT_PATH . "app/Services/CacheService.php";

        $this->about = new AboutModel();
    }

    /**
     * ABOUT PAGE CONTROLLER
     */
    public function index()
    {
        try {
            // LOAD FROM CACHE
            $cached = CacheService::load("about_page");

            // Only return cache if ANY section has real data
            if (!empty($cached) && $this->hasRealData($cached)) {
                return $cached;
            }

            // LOAD FROM DATABASE
            $data = [
                "hero"       => $this->about->getHero(),
                "content"    => $this->about->getContent(),
                "skills"     => $this->about->getSkills(),
                "experience" => $this->about->getExperience(),
                "education"  => $this->about->getEducation(),
                "stats"      => $this->about->getStats(),
            ];

            // Save only if actual data exists
            if ($this->hasRealData($data)) {
                CacheService::save("about_page", $data);
            }

            return $data;

        } catch (Throwable $e) {

            app_log("AboutController@index failed: " . $e->getMessage(), "error");

            // EMERGENCY FALLBACK
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

    /**
     * Checks if at least one section has content
     */
    private function hasRealData(array $data): bool
    {
        foreach ($data as $section) {
            if (!empty($section)) return true;
        }
        return false;
    }
}
