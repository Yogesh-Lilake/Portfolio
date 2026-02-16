<?php
namespace app\Models;

use app\Services\CacheService;
use app\Core\DB;

use app\Services\AssetHealthService;
use app\Services\CVActionResolver;

use app\JsonValidators\Pages\Home\HomeSectionJsonValidator;

use Throwable;

class HomeModel
{
    private string $cacheKey = "home";
    private ?string $defaultPath = null;  // This path may legitimately not exist
    // private int $defaultTTL = 3600; // 1 hour (tunable)

    private string $default_lottie = DEFAULT_LOTTIE;

    public function __construct()
    {

        // Path to /resources/defaults/home.json
        $this->defaultPath = safe_path('HOME_DEFAULT_FILE');
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

            if (!$pdo) {
                app_log("DC-03: HomeModel@get DB unavailable", "error");
                return [
                    "source" => "empty",
                    "data"   => []
                ];
            }

            $stmt = $pdo->query("SELECT * FROM home_section WHERE is_active=0 LIMIT 1");

            // $row = $stmt->fetch(PDO::FETCH_ASSOC); // It tries to resolve from app\Models\PDO class and fails. (Reolve by without PDO:: prefix)
            $row = $stmt->fetch() ?: [];

            // Save ONLY if DB returned meaningful data
            if (!empty($row)) {
                CacheService::save($this->cacheKey, $row);
                return [
                    "source" => "db",
                    "data"   => $row
                ];
            }

            return [
                "source" => "empty",
                "data"   => []
            ];

        } catch (Throwable $e) {

            // Never crash the home page — log and fallback
            app_log("HomeModel@get DB error: " . $e->getMessage(), "error");

            return [
                "source" => "error",
                "data"   => []
            ];
        }
    }



    private function getFallbackMode(): array{
        /** A. Try cache */
        if ($cache = CacheService::load($this->cacheKey)) {
            return [
                "source" => "cache",
                "data"   => $cache
            ];
        }

        /** B. Try DB */
        $row = $this->getOnlyDB();
        if ($row["source"] === "db") {
            CacheService::save($this->cacheKey, $row["data"]);
            return $row;
        }

        /** C. Try default JSON */
        if ($row["source"] === "empty") {
            if ($this->defaultPath && file_exists($this->defaultPath)) {

                $raw = file_get_contents($this->defaultPath);
                $json = json_decode($raw, true);

                /**
                 * DC-01: Invalid JSON syntax
                 *  - json_decode() failed
                 *  - Silent igonre 
                 */
                if (!is_array($json)) {
                    goto HARD_FALLBACK;
                }

                /**
                 * DC-02: Root structure invalid (must be object)
                 *  - Silent ignore
                 */
                if(array_is_list($json)) {
                    goto HARD_FALLBACK;
                }

                /**
                * Schema + semantic validation (DC-09+)
                */
                $validator = new HomeSectionJsonValidator();

                if ($validator->validate($json)) {
                    return [
                        "source" => "json",
                        "data"   => $this->normalize($json)
                    ];
                }
                // DC-09 / DC-10 / DC-11 → log
                app_log($validator->getErrorCode(), 'warning');
            }
        }

        /** D. Hard-coded fallback */
        HARD_FALLBACK:
        return [
            "source" => "fallback",
            "data"   => $this->normalize($this->defaultHome())
        ];
    }

    private function normalize(array $home): array
    {
        $home['background_lottie'] = AssetHealthService::resolveLottieUrl(
            $home['background_lottie'] ?? null,
            DEFAULT_LOTTIE,
            'home_page',
            'home_section'
        );

        // Resolve CTA secondary action → route
        $secondaryAction = $home['cta_secondary_link'] ?? null;
        $home['cta_secondary_link'] = $secondaryAction
            ? CVActionResolver::resolve(trim($secondaryAction))
            : null;

        return $home;
    }



    /* ============================================================
     * DEFAULTS (GUARANTEED SAFE, NON-EMPTY)
     * ============================================================ */

    public function defaultHome(): array
    {
        return [
            "hero_heading"      => "Welcome to My Portfolio",
            "hero_subheading"   => "Full Stack Developer",
            "hero_description"  => "Building scalable, high-performance applications.",
            "background_image"  => IMG_URL . "default-hero.jpg",
            "background_lottie" => "https://assets10.lottiefiles.com/packages/lf20_kyu7xb1v.json",
            "profile_image"     => IMG_URL . "profile-default.png",

            "cta_primary_text"  => "View Projects",
            "cta_primary_link"  => "projects",

            "cta_secondary_text" => "Download CV",
            "cta_secondary_link" => "DOWNLOAD_CV",

            "cv_file_path"      => "downloads/Yogesh_Lilake_Resume.pdf",

            "seo_title"         => "Avinash Portfolio | Full Stack & Android Developer",
            "seo_description"   => "Portfolio of Avinash, a Full Stack Developer building high-performance web and mobile applications using PHP, JS, MySQL and modern frameworks.",

            "is_active"   => 1,
            "is_default"  => true
        ];
    }
}
