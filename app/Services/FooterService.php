<?php
namespace app\Services;

use app\Services\CacheService;
use app\Core\DB;

use app\JsonValidators\Global\FooterSettingJsonValidator;
use app\JsonValidators\Shared\NavigationLinksJsonValidator;
use app\JsonValidators\Shared\SocialLinksJsonValidator;

use Throwable;

class FooterData
{
    private string $cacheKeyFooter = "footer_settings";
    private string $cacheKeyLinks  = "footer_quick_links";
    private string $cacheKeySocial = "footer_social_links";

    private ?string $defaultFooterPath = null;
    private ?string $defaultLinksPath  = null;
    private ?string $defaultSocialPath = null;

    private array $requiredFooterKeys = [
        "brand_name",
        "footer_description",
        "developer_name",
        "accent_color"
    ];

    public function __construct()
    {
        $this->defaultFooterPath = safe_path('FOOTER_DEFAULT_FILE');
        $this->defaultLinksPath  = safe_path('FOOTER_LINKS_DEFAULT_FILE');
        $this->defaultSocialPath = safe_path('FOOTER_SOCIAL_DEFAULT_FILE');
    }

    /* ============================================================
       PUBLIC API (HomeModel-style)
    ============================================================ */
    public function get(): array
    {
        return [
            "footer" => $this->getFooterFallbackMode(),
            "links"  => $this->getLinksFallbackMode(),
            "social" => $this->getSocialFallbackMode(),
        ];
    }

    /* ============================================================
       FOOTER SETTINGS
    ============================================================ */

    private function getFooterOnlyDB(): array
    {
        try {
            $pdo = DB::getInstance()->pdo();

            if (!$pdo) {
                app_log("DC-03: FooterService@getFooterOnlyDB DB unavailable", "error");
                return ["source" => "empty", "data" => []];
            }

            $stmt = $pdo->query(
                "SELECT * FROM footer_settings WHERE is_active = 1 LIMIT 1"
            );
            $row = $stmt->fetch() ?: [];

            if (!empty($row)) {
                return ["source" => "db", "data" => $this->normalizeFooter($row)];
            }

            return ["source" => "empty", "data" => []];

        } catch (Throwable $e) {
            app_log("FooterData@getFooter DB error: ".$e->getMessage(), "error");
            return ["source" => "error", "data" => []];
        }
    }

    private function getFooterFallbackMode(): array
    {
        if ($cache = CacheService::load($this->cacheKeyFooter)) {
            return ["source" => "cache", "data" => $this->normalizeFooter($cache)];
        }

        $row = $this->getFooterOnlyDB();
        if ($row["source"] === "db") {
            CacheService::save($this->cacheKeyFooter, $row["data"]);
            return $row;
        }

        if ($row["source"] === "empty" && $this->defaultFooterPath && file_exists($this->defaultFooterPath)) {
            
            $raw = file_get_contents($this->defaultFooterPath);
            $json = json_decode($raw, true);

            // DC-01: silent
            if (!is_array($json)) {
                goto HARD_FALLBACK;
            }

            // DC-02: silent
            if (array_is_list($json)) {
                goto HARD_FALLBACK;
            }

            $validator = new FooterSettingJsonValidator();

            if ($validator->validate($json)) {
                return [
                    "source" => "json",
                    "data"   => $this->normalizeFooter($json)
                ];
            }

            // DC-09+
            app_log($validator->getErrorCode(), 'warning');
        }

        HARD_FALLBACK:
        return ["source" => "fallback", "data" => $this->normalizeFooter($this->defaultFooter())];
    }

    /* ============================================================
       FOOTER QUICK LINKS
    ============================================================ */

    private function getLinksOnlyDB(): array
    {
        try {
            $pdo = DB::getInstance()->pdo();
            if (!$pdo) {
                app_log("DC-03: FooterService@getLinksOnlyDB DB unavailable", "error");
                return ["source" => "empty", "data" => []];
            }
            $stmt = $pdo->query("
                SELECT label, url
                FROM navigation_links
                WHERE is_active = 1
                ORDER BY order_no ASC
            ");
            $rows = $stmt->fetchAll() ?: [];

            if (!empty($rows)) {
                return ["source" => "db", "data" => $rows];
            }

            return ["source" => "empty", "data" => []];

        } catch (Throwable $e) {
            app_log("FooterData@getLinks DB error: ".$e->getMessage(), "error");
            return ["source" => "error", "data" => []];
        }
    }

    private function getLinksFallbackMode(): array
    {
        if ($cache = CacheService::load($this->cacheKeyLinks)) {
            return ["source" => "cache", "data" => $cache];
        }

        $row = $this->getLinksOnlyDB();
        if ($row["source"] === "db") {
            CacheService::save($this->cacheKeyLinks, $row["data"]);
            return $row;
        }

        if ($row["source"] === "empty" && $this->defaultLinksPath && file_exists($this->defaultLinksPath)) {

            $raw = file_get_contents($this->defaultLinksPath);
            $json = json_decode($raw, true);

            // DC-01
            if (!is_array($json)) {
                goto HARD_FALLBACK;
            }

            // DC-02
            if (!array_is_list($json)) {
                goto HARD_FALLBACK;
            }

            $validator = new NavigationLinksJsonValidator();

            if ($validator->validate($json)) {
                return [
                    "source" => "json",
                    "data"   => $json
                ];
            }

            // DC-09+
            app_log($validator->getErrorCode(), 'warning');
        }

        HARD_FALLBACK:
        return ["source" => "fallback", "data" => $this->defaultLinks()];
    }

    /* ============================================================
       SOCIAL LINKS
    ============================================================ */

    private function getSocialOnlyDB(): array
    {
        try {
            $pdo = DB::getInstance()->pdo();
            if (!$pdo) {
                app_log("DC-03: FooterService@getSocialOnlyDB DB unavailable", "error");
                return ["source" => "empty", "data" => []];
            }
            $stmt = $pdo->query("
                SELECT platform, url, icon_class
                FROM social_links
                WHERE is_active = 1
            ");
            $rows = $stmt->fetchAll() ?: [];

            if (!empty($rows)) {
                return ["source" => "db", "data" => $rows];
            }

            return ["source" => "empty", "data" => []];

        } catch (Throwable $e) {
            app_log("FooterData@getSocial DB error: ".$e->getMessage(), "error");
            return ["source" => "error", "data" => []];
        }
    }

    private function getSocialFallbackMode(): array
    {
        if ($cache = CacheService::load($this->cacheKeySocial)) {
            return [
                "source" => "cache", 
                "data" => $cache
            ];
        }

        $row = $this->getSocialOnlyDB();
        if ($row["source"] === "db") {
            CacheService::save($this->cacheKeySocial, $row["data"]);
            return $row;
        }

        if ($row["source"] === "empty" && $this->defaultSocialPath && file_exists($this->defaultSocialPath)) {
            
            $raw  = file_get_contents($this->defaultSocialPath);
            $json = json_decode($raw, true);

            // DC-01 — silent
            if (!is_array($json)) {
                goto HARD_FALLBACK;
            }

            $validator = new SocialLinksJsonValidator();

            if ($validator->validate($json)) {
                return [
                    "source" => "json",
                    "data"   => $json
                ];
            }

            // DC-09+
            app_log($validator->getErrorCode(), 'warning');
        }

        /** D. Hard fallback */
        HARD_FALLBACK:
        return [
            "source" => "fallback", 
            "data" => $this->defaultSocial()
        ];
    }

    /* ============================================================
       DEFAULTS + NORMALIZATION
    ============================================================ */
    private function defaultFooter(): array
    {
        return [
            "brand_name"         => SITE_TITLE,
            "footer_description" => "DDFull Stack & Android Developer — Crafting digital solutions.",
            "developer_name"     => SITE_TITLE,
            "accent_color"       => ACCENT_COLOR
        ];
    }

    private function defaultLinks(): array
    {
        return [
            ["label" => "DDHome",     "url" => HOME_URL_NO_BASE],
            ["label" => "About",    "url" => ABOUT_URL_NO_BASE],
            ["label" => "Projects", "url" => PROJECTS_URL_NO_BASE],
            ["label" => "Notes",    "url" => NOTES_URL_NO_BASE],
            ["label" => "Contact",  "url" => CONTACT_URL_NO_BASE],
        ];
    }

    private function defaultSocial(): array
    {
        return [
            ["platform" => "GitHub",   "url" => "https://github.com",   "icon_class" => "fa-github"],
            ["platform" => "LinkedIn", "url" => "https://linkedin.com", "icon_class" => "fa-linkedin"],
            ["platform" => "Twitter",  "url" => "https://twitter.com",  "icon_class" => "fa-twitter"],
            ["platform" => "Email",    "url" => "https://mail.google.com/mail/?view=cm&fs=1&to=yogeshlilakedev02@gmail.com&su=Portfolio%20Contact&body=Hi%20Yogesh,%0A%0AI%20visited%20your%20portfolio%20and%20would%20like%20to%20connect.%0A%0ARegards,", "icon_class" => "fa-envelope"],
        ];
    }

    /* ============================================================
       NORMALIZATION (NO array_filter BUGS)
    ============================================================ */
    private function normalizeFooter(array $footer): array
    {
        foreach ($this->requiredFooterKeys as $key) {
            if (!array_key_exists($key, $footer) || $footer[$key] === null) {
                $footer[$key] = "";
            }
        }

        return $footer;
    }
}
