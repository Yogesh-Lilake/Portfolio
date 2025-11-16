<?php

class HomeModel {

    private $cacheKey = "home";

    public function get()
    {
        // 1. Load from cache
        if ($cache = CacheService::load($this->cacheKey)) {
            return $cache;
        }

        // 2. Fetch from DB
        $sql = "SELECT * FROM home_section WHERE is_active = 1 LIMIT 1";
        $row = safe_fetch(safe_query($sql));

        // 3. Fallback
        if (!$row) $row = $this->defaults();

        // 4. Save to cache
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
