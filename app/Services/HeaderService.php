<?php
namespace app\Services;

use app\Services\CacheService;
use app\Core\DB;

use app\JsonValidators\Global\HeaderSettingJsonValidator;
use app\JsonValidators\Shared\NavigationLinksJsonValidator;

use Throwable;

class HeaderData {
    private string $cacheKeyHeader = "header_settings";
    private string $cacheKeyNav    = "header_navigation";

    // config paths may legitimately not exist
    private ?string $defaultHeaderPath = null;
    private ?string $defaultNavPath    = null;

    private array $requiredKeys = [
        "site_title",
        "logo_path",
        "button_text",
        "button_link",
        "accent_color"
    ];

    public function __construct()
    {
        // CONFIG-SAFE (no fatal error if missing)
        $this->defaultHeaderPath = safe_path('HEADER_DEFAULT_FILE');
        $this->defaultNavPath    = safe_path('NAV_DEFAULT_FILE');
    }

    /* ============================================================
       PUBLIC API (MATCHES HomeModel)
    ============================================================ */
    public function get(): array
    {
        return [
            "header" => $this->getHeaderFallback(),
            "nav"    => $this->getNavFallback()
        ];
    }

    /* ============================================================
       HEADER — HOME MODEL PARITY
    ============================================================ */

    private function getHeaderFallback(): array
    {
        /** A. Cache */
        if ($cache = CacheService::load($this->cacheKeyHeader)) {
            return [
                "source" => "cache",
                "data"   => $this->normalize($cache)
            ];
        }

        /** B. DB */
        $row = $this->getHeaderOnlyDB();
        if ($row["source"] === "db") {
            CacheService::save($this->cacheKeyHeader, $row["data"]);
            return $row;
        }

        /** C. JSON */
        if ($this->defaultHeaderPath && file_exists($this->defaultHeaderPath)) {

            $raw  = file_get_contents($this->defaultHeaderPath);
            $json = json_decode($raw, true);

            /**
             * DC-01: Invalid JSON syntax
             * - json_decode() failed
             * - Silent ignore
            */
            if (!is_array($json)) {
                // DO NOT log
                goto HARD_FALLBACK;
            }

            /**
             * DC-02: Root structure invalid (list instead of object)
             * - Silent ignore
            */
            if (array_is_list($json)) {
                // DO NOT log
                goto HARD_FALLBACK;
            }

            /**
             * Schema + semantic validation (DC-09+)
            */
            $validator = new HeaderSettingJsonValidator();

            if ($validator->validate($json)) {
                return [
                    'source' => 'json',
                    'data'   => $this->normalize($json)
                ];
            }

            // DC-09 / DC-10 / DC-11 → log
            app_log($validator->getErrorCode(), 'warning');
        }

        /** D. Hard fallback */
        HARD_FALLBACK:
        return [
            "source" => "fallback",
            "data"   => $this->normalize($this->defaultHeader())
        ];
    }

    private function getHeaderOnlyDB(): array
    {
        try {
            $pdo = DB::getInstance()->pdo();

            if (!$pdo) {
                app_log("DC-03: HeaderService@getHeaderOnlyDB DB unavailable", "error");
                return [
                    "source" => "empty",
                    "data"   => []
                ];
            }

            $stmt = $pdo->query(
                "SELECT * FROM header_settings WHERE is_active = 1 LIMIT 1"
            );

            $row = $stmt->fetch() ?: [];

            if (!empty($row)) {
                return [
                    "source" => "db",
                    "data"   => $this->normalize($row)
                ];
            }

            return [
                "source" => "empty",
                "data"   => []
            ];

        } catch (Throwable $e) {
            app_log("HeaderData DB error: " . $e->getMessage(), "error");

            return [
                "source" => "error",
                "data"   => []
            ];
        }
    }

    /* ============================================================
       NAV — SAME STATE MACHINE
    ============================================================ */

    private function getNavFallback(): array
    {
        /** A. Cache */
        if ($cache = CacheService::load($this->cacheKeyNav)) {
            return [
                "source" => "cache",
                "data"   => $cache
            ];
        }

        /** B. DB */
        $rows = $this->getNavOnlyDB();
        if ($rows["source"] === "db") {
            CacheService::save($this->cacheKeyNav, $rows["data"]);
            return $rows;
        }

        /** C. JSON */
        if ($this->defaultNavPath && file_exists($this->defaultNavPath)) {
            
            $raw = file_get_contents($this->defaultNavPath);
            $json = json_decode($raw, true);

            // DC-01: invalid JSON => silent
            if (!is_array($json)) {
                goto HARD_FALLBACK;
            }

            // DC-02: root not list => silent
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

            // DC-09+ only
            app_log($validator->getErrorCode(), 'warning');
        }

        /** D. Hard fallback */
        HARD_FALLBACK:
        return [
            "source" => "fallback",
            "data"   => $this->defaultNav()
        ];
    }

    private function getNavOnlyDB(): array
    {
        try {
            $pdo = DB::getInstance()->pdo();

            if (!$pdo) {
                app_log("DC-03: HeaderService@getNavOnlyDB DB unavailable", "error");
                return [
                    "source" => "empty",
                    "data"   => []
                ];
            }

            $stmt = $pdo->query(
                "SELECT label, url FROM navigation_links
                 WHERE is_active = 1 ORDER BY order_no ASC"
            );

            $rows = $stmt->fetchAll() ?: [];

            if (!empty($rows)) {
                return [
                    "source" => "db",
                    "data"   => $rows
                ];
            }

            return [
                "source" => "empty",
                "data"   => []
            ];

        } catch (Throwable $e) {
            app_log("HeaderData NAV DB error: " . $e->getMessage(), "error");

            return [
                "source" => "error",
                "data"   => []
            ];
        }
    }

    /* ============================================================
       DEFAULTS (Bulletproof Hardcoded)
    ============================================================ */

    private function defaultHeader(): array
    {
        return [
            "site_title"   => SITE_TITLE,
            "logo_path"    => SITE_LOGO,
            "button_text"  => CTA_TEXT,
            "button_link"  => CTA_LINK,
            "accent_color" => ACCENT_COLOR
        ];
    }

    private function defaultNav(): array
    {
        return [
            ["label" => "DHome",    "url" => HOME_URL_NO_BASE],
            ["label" => "About",   "url" => ABOUT_URL_NO_BASE],
            ["label" => "Projects","url" => PROJECTS_URL_NO_BASE],
            ["label" => "Notes",    "url" => NOTES_URL_NO_BASE],
            ["label" => "Contact", "url" => CONTACT_URL_NO_BASE]
        ];
    }

    /* ============================================================
       NORMALIZATION (100% NO ERRORS EVER)
    ============================================================ */
    private function normalize(array $header): array
    {
        $header = array_filter($header); // remove null/empty

        foreach ($this->requiredKeys as $key) {
            if (!array_key_exists($key, $header)) {
                $header[$key] = ""; // safe empty value
            }
        }

        return $header;
    }
}
