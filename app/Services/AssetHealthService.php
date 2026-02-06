<?php
namespace app\Services;

/**
 * AssetHealthService
 * ------------------------------------------------------------------
 * Centralized media health resolver for external assets (Lottie, etc.)
 *
 * WHY THIS EXISTS:
 * - JSON/schema validation cannot detect runtime CDN failures (403, expired URLs)
 * - Client-side (JS) detection is unreliable for Lottie XML/JSON responses
 * - UI must NEVER render broken or inaccessible media
 *
 * DESIGN PRINCIPLES:
 * - Server-side, deterministic validation
 * - Section-aware logging for observability
 * - Zero logic in views
 * - Reusable across Home, About, and future pages
 *
 * CURRENT RESPONSIBILITY:
 * - Validate accessibility of Lottie animation URLs
 * - Replace inaccessible URLs with DEFAULT_LOTTIE
 *
 * FUTURE EXTENSIONS (no breaking changes):
 * - Cache health results (TTL-based)
 * - Multi-tier fallback (lottie → svg → image)
 * - Severity-based logging (notice/warning/critical)
 */
final class AssetHealthService
{
    /**
     * Resolve a Lottie animation URL into a guaranteed-usable asset.
     *
     * This method performs a lightweight HTTP HEAD request to verify
     * that the asset is reachable and accessible (2xx status).
     *
     * FAILURE CASES HANDLED:
     * - Missing URL
     * - Invalid URL format
     * - 403 / AccessDenied (common with Lottie CDN links)
     * - 404 / expired asset
     * - Any non-2xx HTTP response
     *
     * OBSERVABILITY:
     * Logs include logical page and section identifiers so that
     * production issues can be traced immediately without guessing.
     *
     * @param string|null $url
     *        Lottie animation URL from DB / JSON defaults
     *
     * @param string $fallback
     *        Absolute fallback animation URL (DEFAULT_LOTTIE)
     *
     * @param string $page
     *        Logical page identifier (e.g. "home", "about")
     *
     * @param string $section
     *        Logical section identifier (e.g. "hero", "banner")
     *
     * @return string
     *         Guaranteed-safe Lottie URL for rendering
     */
    public static function resolveLottieUrl(
        ?string $url,
        string $fallback,
        string $page,
        string $section
    ): string {

        /* ----------------------------------------------------------
         * AH-01: Missing or invalid URL
         * ---------------------------------------------------------- */
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            app_log(
                "AH-01: Missing or invalid lottie URL | page={$page} section={$section}",
                "warning"
            );
            return $fallback;
        }

        /* ----------------------------------------------------------
         * Perform lightweight HEAD request (no body download)
         * ---------------------------------------------------------- */
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_NOBODY         => true,   // HEAD request only
            CURLOPT_FOLLOWLOCATION => true,   // Follow redirects
            CURLOPT_TIMEOUT        => 2,      // Fast fail
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,  // Avoid local SSL issues
        ]);

        curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        /* ----------------------------------------------------------
         * AH-02: Asset unreachable / AccessDenied / expired
         * ---------------------------------------------------------- */
        if ($status < 200 || $status >= 300) {
            app_log(
                "AH-02: Lottie inaccessible | status={$status} | page={$page} section={$section} | url={$url}",
                "warning"
            );
            return $fallback;
        }

        /* ----------------------------------------------------------
         * Asset is healthy and usable
         * ---------------------------------------------------------- */
        return $url;
    }
}
