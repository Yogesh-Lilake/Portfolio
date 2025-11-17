<?php

class HomeModel {

    private $cacheKey = "home";

    public function get()
    {
        // 1. Try cache
        if ($cache = CacheService::load($this->cacheKey)) {
            return $cache;
        }

        // 2. Fetch from DB
        try {
            $pdo = DB::getInstance()->pdo();

            $stmt = $pdo->prepare("SELECT * FROM home_section WHERE is_active = 1 LIMIT 1");
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            app_log("HomeModel DB error: " . $e->getMessage(), "error");
            $row = null;
        }

        // 3. Fallback defaults (never break page)
        if (!$row) {
            $row = $this->defaults();
        }

        // 4. Save to cache (DB or fallback)
        CacheService::save($this->cacheKey, $row);

        return $row;
    }

    private function defaults()
    {
        return [
            "hero_heading"    => "Welcome to My Portfolio",
            "hero_subheading" => "Building Modern & Scalable Applications",
            "background_image" => IMG_URL . "default-bg.jpg",
            "projects_link"   => "projects.php",
            "cv_link"         => CTA_LINK
        ];
    }
}
