<?php

class ContactModel {

    private $cacheKey = "contact";

    public function get()
    {
        if ($cache = CacheService::load($this->cacheKey)) return $cache;

        $row = safe_fetch(
            safe_query("SELECT * FROM contact_section WHERE is_active = 1 LIMIT 1")
        );

        if (!$row) $row = [
            "title" => "Let's Talk",
            "subtitle" => "Feel free to reach out for collaboration!",
            "button_text" => "Contact Me",
            "button_link" => "contact.php"
        ];

        CacheService::save($this->cacheKey, $row);

        return $row;
    }
}
