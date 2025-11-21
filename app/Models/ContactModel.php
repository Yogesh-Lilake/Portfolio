<?php

class ContactModel {

    private string $cacheKey = "contact";
    private string $defaultJson = ROOT_PATH . "app/resources/defaults/home/contact.json";

    public function get()
    {
        /** ----------------------------------------------------
         * A. TRY CACHE
         * ----------------------------------------------------*/
        if ($cache = CacheService::load($this->cacheKey)) {
            return $cache;
        }

        /** ----------------------------------------------------
         * B. TRY DATABASE
         * ----------------------------------------------------*/
        try {
            $pdo = DB::getInstance()->pdo();

            $stmt = $pdo->prepare("
                SELECT title, subtitle, button_text, button_link
                FROM contact_section
                WHERE is_active = 1
                LIMIT 1
            ");
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!empty($row)) {
                CacheService::save($this->cacheKey, $row);
                return $row;
            }

        } catch (Throwable $e) {
            app_log("ContactModel DB error: " . $e->getMessage(), "error");
        }

        /** ----------------------------------------------------
         * C. TRY DEFAULT JSON FILE
         * ----------------------------------------------------*/
        if (file_exists($this->defaultJson)) {
            $json = json_decode(file_get_contents($this->defaultJson), true);
            if (!empty($json) && is_array($json)) {
                return $json;
            }
        }

        /** ----------------------------------------------------
         * D. HARD-CODED DEFAULT FALLBACK
         * ----------------------------------------------------*/
        return $this->defaults();
    }

    private function defaults(): array
    {
        return [
            "is_default"  => true,
            "title"       => "Get In Touch",
            "subtitle"    => "Feel free to contact me for collaborations, projects, or job opportunities.",
            "button_text" => "Contact Me",
            "button_link" => "contact.php"
        ];
    }
}
