<?php

class ProjectModel {

    private $cacheKey = "featured_projects";

    public function featured()
    {
        if ($cache = CacheService::load($this->cacheKey)) return $cache;

        $rows = safe_fetch_all(
            safe_query("SELECT * FROM projects WHERE is_active = 1 AND is_featured = 1 ORDER BY id DESC")
        );

        CacheService::save($this->cacheKey, $rows);

        return $rows;
    }
}
