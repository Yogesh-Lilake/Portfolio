<?php

class HomeController extends Controller
{
    private HomeModel $home;
    private AboutModel $about;
    private SkillModel $skills;
    private ProjectModel $projects;
    private ContactModel $contact;

    public function __construct()
    {
        // Load all models
        require_once ROOT_PATH . "app/Models/HomeModel.php";
        require_once ROOT_PATH . "app/Models/AboutModel.php";
        require_once ROOT_PATH . "app/Models/SkillModel.php";
        require_once ROOT_PATH . "app/Models/ProjectModel.php";
        require_once ROOT_PATH . "app/Models/ContactModel.php";

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
            return [
                "home"     => $this->home->get(),
                "about"    => $this->about->get(),
                "skills"   => $this->skills->all(),
                "projects" => $this->projects->featured(),
                "contact"  => $this->contact->get(),
            ];
        } catch (Throwable $e) {
            app_log("HomeController@index failed", "error", [
                "error" => $e->getMessage()
            ]);

            // Absolute fallback: NO page break
            return [
                "home"     => $this->home->get(),
                "about"    => $this->about->get(),
                "skills"   => [],
                "projects" => [],
                "contact"  => $this->contact->get(),
            ];
        }
    }
}
