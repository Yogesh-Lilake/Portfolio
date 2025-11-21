<?php

/**
 * HomeModel
 *
 * Enterprise-level model with:
 * - DB → cache → defaults fallback
 * - Guaranteed return structure (never empty)
 * - Protection against DB failure, missing table, missing columns
 * - Matches AboutModel architecture
 */

class HomeModel
{
    private string $cacheKey = "home";
    private string $defaultPath;
    // private int $defaultTTL = 3600; // 1 hour (tunable)

    public function __construct()
    {
        require_once ROOT_PATH . "app/Services/CacheService.php";

        // Path to /resources/defaults/home.json
        $this->defaultPath = ROOT_PATH . "app/resources/defaults/home/home.json";
    }


    /* ============================================================
     * PUBLIC: Returns the hero/home section
     * ============================================================ */

    public function get(bool $pure = false): array
    {
        // Return pure DB result (no fallback mixing) Or Normal fallback system
        return $pure ? $this->getOnlyDB() : $this->getFallbackMode();

    }

    private function getOnlyDB(): array{
        // 2. DB FETCH
        try {
            $pdo = DB::getInstance()->pdo();
            // Actual table name `home_section`
            $stmt = $pdo->prepare("
                SELECT * 
                FROM home_section
                WHERE is_active = 1 
                LIMIT 1
            ");
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

            // Save ONLY if DB returned meaningful data
            if (!empty($row)) {
                CacheService::save($this->cacheKey, $row);
                return $row;
            }

        } catch (Throwable $e) {

            // Never crash the home page — log and fallback
            app_log("HomeModel@get error: " . $e->getMessage(), "error");
            return [];
        }
    }

    private function getFallbackMode(): array{
        /** A. Try cache */
        if ($cache = CacheService::load($this->cacheKey)) {
            return $cache;
        }

        /** B. Try DB */
        $row = $this->getOnlyDB();
        if (!empty($row)) {
            CacheService::save($this->cacheKey, $row);
            return $row;
        }

        /** C. Try default JSON */
        if (file_exists($this->defaultPath)) {
            $json = json_decode(file_get_contents($this->defaultPath), true);
            if (!empty($json)) {
                return $json; // DO NOT CACHE THIS
            }
        }

        /** D. Hard-coded fallback */
        return $this->defaultHome();
    }


    /* ============================================================
     * DEFAULTS (GUARANTEED SAFE, NON-EMPTY)
     * ============================================================ */

    public function defaultHome(): array
    {
        return [
            "hero_heading"       => "Hi, I’m " . SITE_TITLE . " 👋",
            "hero_subheading"    => "Full Stack & Android Developer | Turning ideas into scalable digital products.",
            "projects_link"    => "projects.php",
            "cv_link"          => "assets/cv/Yogesh-CV.pdf",
            "hero_description" => "I build fast, modern, scalable applications using PHP, MySQL, JavaScript, TailwindCSS, and Android.",
            "cta_projects"     => "View Projects",
            "cta_contact"      => "Contact Me",
            "animation_url"    => "https://assets10.lottiefiles.com/packages/lf20_kyu7xb1v.json",
            "background_image" => IMG_URL . "default-hero-bg.jpg",
            "is_active"        => true
        ];
    }
}
