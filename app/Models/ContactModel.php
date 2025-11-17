<?php

class ContactModel {

    private $cacheKey = "contact";

    public function get()
    {
        if ($cache = CacheService::load($this->cacheKey)) return $cache;

        try{
            $pdo = DB::getInstance()->pdo();

            $stmt = $pdo->prepare("SELECT * FROM contact_section WHERE is_active = 1 LIMIT 1");
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        }catch (Throwable $e){
            app_log("ContactModel DB error: " . $e->getMessage(), "error");
            $row = null;
        }

        if (!$row) $row = $this->defaults();

        CacheService::save($this->cacheKey, $row);

        return $row;
    }

    private function defaults()
    {
        return [
            "title"        => "Get In Touch",
            "subtitle"     => "Feel free to contact me for collaborations, projects, or job opportunities.",
            "button_text"  => "Contact Me",
            "button_link"  => "contact.php"
        ];
    }
}
