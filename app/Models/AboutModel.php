<?php
/**
 * AboutModel
 *
 * Enterprise-grade About page model.
 * - Returns DB -> cache -> defaults
 * - Never allows the UI to receive an empty/blank response
 * - Only saves non-empty DB results to cache
 */

class AboutModel {

    private string $cacheKey = "about";
    private int $defaultTTL = 3600; // seconds for section caches (tunable)

    public function __construct() {
        require_once ROOT_PATH . "app/Services/CacheService.php";
    }

    /**
     * BASIC ABOUT SECTION (single record)
     */
    public function get()
    {
        $cache = CacheService::load($this->cacheKey);
        if (!empty($cache)) return $cache;

        try {
            $pdo = DB::getInstance()->pdo();
            $stmt = $pdo->prepare("SELECT * FROM about_section WHERE is_active = 1 LIMIT 1");
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

            if (!empty($row)) {
                CacheService::save($this->cacheKey, $row);
            }

            return $row ?: $this->defaults();

        } catch (Throwable $e) {
            app_log("AboutModel@get error: " . $e->getMessage(), "error");
            return $this->defaults();
        }
    }

    /**
     * Load a single-record section (with defaults fallback)
     *
     * @param string $table DB table name
     * @param string $key cache key name
     * @param callable $defaultFn function returning defaults
     */
    private function loadSingleWithDefault(string $table, string $key, callable $defaultFn)
    {
        // 1. cache
        if ($cached = CacheService::load($key)) {
            return $cached;
        }

        // 2. db
        try {
            $pdo = DB::getInstance()->pdo();
            $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE is_active = 1 LIMIT 1");
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

            if (!empty($row)) {
                CacheService::save($key, $row, $this->defaultTTL);
                return $row;
            }

            // 3. fallback default
            return $defaultFn();

        } catch (Throwable $e) {
            app_log("AboutModel@loadSingleWithDefault error ({$table}): " . $e->getMessage(), "error");
            return $defaultFn();
        }
    }

    /**
     * Load multi-row sections (with defaults fallback)
     *
     * @param string $table
     * @param string $key
     * @param callable $defaultFn
     * @return array
     */
    private function loadMultipleWithDefault(string $table, string $key, callable $defaultFn): array
    {
        if ($cached = CacheService::load($key)) {
            return $cached;
        }

        try {
            $pdo = DB::getInstance()->pdo();
            $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE is_active = 1 ORDER BY sort_order ASC");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            if (!empty($rows)) {
                CacheService::save($key, $rows, $this->defaultTTL);
                return $rows;
            }

            return $defaultFn();

        } catch (Throwable $e) {
            app_log("AboutModel@loadMultipleWithDefault error ({$table}): " . $e->getMessage(), "error");
            return $defaultFn();
        }
    }

    // ===========================
    // PUBLIC SECTION GETTERS
    // ===========================

    public function getHero()
    {
        return $this->loadSingleWithDefault("about_hero1", "about_hero", [$this, 'defaultHero']);
    }

    public function getContent()
    {
        return $this->loadSingleWithDefault("about_content1", "about_content", [$this, 'defaultContent']);
    }

    public function getSkills(): array
    {
        return $this->loadMultipleWithDefault("about_skills1", "about_skills", [$this, 'defaultSkills']);
    }

    public function getExperience(): array
    {
        return $this->loadMultipleWithDefault("about_experience1", "about_experience", [$this, 'defaultExperience']);
    }

    public function getEducation(): array
    {
        return $this->loadMultipleWithDefault("about_education1", "about_education", [$this, 'defaultEducation']);
    }

    public function getStats(): array
    {
        return $this->loadMultipleWithDefault("about_stats1", "about_stats", [$this, 'defaultStats']);
    }

    // ===========================
    // DEFAULTS (guaranteed non-empty)
    // ===========================

    public function defaults(): array
    {
        return [
            "title"   => "About Me",
            "content" => "Hi, I'm Yogesh. I build optimized, scalable and user-friendly applications."
        ];
    }

    public function defaultHero(): array
    {
        return [
            "title" => "About",
            "subtitle" => "Full Stack & Android Developer passionate about modern digital experiences.",
            "animation_url" => "https://assets10.lottiefiles.com/packages/lf20_jcikwtux.json",
            "background_opacity" => 0.15,
            "is_active" => 1
        ];
    }

    public function defaultContent(): array
    {
        return [
            "greeting_title" => "Hi, I'm " . SITE_TITLE . " 👋",
            "main_description" => "I build modern web and mobile applications with a focus on performance, usability and clean architecture.",
            "secondary_description" => "I enjoy solving problems end-to-end: from design and product thinking to clean, tested code.",
            "cta_text" => "Download CV",
            "cta_link" => CTA_LINK,
            "profile_image" => IMG_URL . "profile.jpg",
            "is_active" => 1
        ];
    }

    public function defaultSkills(): array
    {
        return [
            ["skill_name" => "PHP", "icon_class" => "fab fa-php", "color_class" => "text-blue-400"],
            ["skill_name" => "MySQL", "icon_class" => "fas fa-database", "color_class" => "text-yellow-400"],
            ["skill_name" => "JavaScript", "icon_class" => "fab fa-js", "color_class" => "text-yellow-300"],
            ["skill_name" => "TailwindCSS", "icon_class" => "fab fa-css3-alt", "color_class" => "text-sky-400"],
            ["skill_name" => "Android", "icon_class" => "fab fa-android", "color_class" => "text-green-400"],
        ];
    }

    public function defaultExperience(): array
    {
        return [
            [
                "title" => "Full Stack Developer",
                "description" => "Built responsive UIs, REST APIs and improved app performance.",
                "company" => "Personal Projects",
                "period" => "2022 — Present",
            ],
            [
                "title" => "Android Developer",
                "description" => "Created performant mobile experiences and shipped production apps.",
                "company" => "Personal Projects",
                "period" => "2020 — 2022",
            ],
        ];
    }

    public function defaultEducation(): array
    {
        return [
            [
                "degree" => "Bachelor of Engineering in Computer Science",
                "institution" => "Pune University",
                "period" => "2019 — 2023",
                "description" => "Graduated with a strong foundation in algorithms, full-stack web development, and mobile technologies.",
            ],
            [
                "degree" => "Higher Secondary Education (HSC)",
                "institution" => "Maharashtra State Board",
                "period" => "2017 — 2019",
                "description" => "Focused on science and mathematics with distinction.",
            ],
            [
                "degree" => "Secondary Education (SSC)",
                "institution" => "Maharashtra State Board",
                "period" => "2017",
                "description" => "Early problem-solving skills and academic excellence.",
            ],
        ];
    }

    public function defaultStats(): array
    {
        return [
            ["label" => "Projects", "value" => "12+"],
            ["label" => "Years Experience", "value" => "3+"],
            ["label" => "Clients", "value" => "5+"],
            ["label" => "Open Source", "value" => "8+"],
        ];
    }
}
