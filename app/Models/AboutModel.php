<?php

class AboutModel {

    private $cacheKey = "about";

    public function get()
    {
        if ($cache = CacheService::load($this->cacheKey)) return $cache;

        $row = safe_fetch(safe_query("SELECT * FROM about_section WHERE is_active = 1 LIMIT 1"));

        if (!$row) $row = [
            "title" => "About Me",
            "content" => "Hi, I'm Yogesh. I build optimized, scalable and user-friendly applications."
        ];

        CacheService::save($this->cacheKey, $row);

        return $row;
    }
}
