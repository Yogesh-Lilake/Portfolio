<?php

class SkillModel {

    private $cacheKey = "skills";

    public function all()
    {
        if ($cache = CacheService::load($this->cacheKey)) return $cache;

        $rows = safe_fetch_all(safe_query("SELECT * FROM skills WHERE is_active = 1 ORDER BY id ASC"));

        CacheService::save($this->cacheKey, $rows);

        return $rows;
    }
}
