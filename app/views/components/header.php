<?php
/**
 * GLOBAL HEADER COMPONENT
 *
 * Rules:
 * - NEVER use htmlspecialchars() directly in views
 * - ALWAYS use view helpers (safe, field, url, asset, logo)
 * - Assume data is validated + normalized by Service + JSON Validators
 */

require_once HEADERSERVICE_FILE;

use app\Core\DB;
use app\Services\HeaderData;

// Load header + navigation data (Cache → DB → JSON → Fallback)
$data = (new HeaderData())->get();

$headerPayload = $data['header'] ?? [];
$navPayload    = $data['nav'] ?? [];

$header    = $headerPayload['data'] ?? [];
$nav_links = $navPayload['data'] ?? [];

// Header assets
$header_css = [HEADER_CSS];
$header_js  = [HEADER_JS];

/**
 * Detect base path (supports /Portfolio/public, subfolders, prod)
 */
$BASE_URL = dirname($_SERVER['SCRIPT_NAME']);
if ($BASE_URL === '/') {
    $BASE_URL = '';
}

/**
 * Detect current route (used for active link highlighting)
 */
$currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($BASE_URL !== '' && str_starts_with($currentUri, $BASE_URL)) {
    $currentUri = substr($currentUri, strlen($BASE_URL));
}

$currentRoute = '/' . trim($currentUri, '/');
if ($currentRoute === '//') {
    $currentRoute = '/';
}
?>

<!-- HEADER CSS -->
<?php foreach ($header_css as $css): ?>
  <!-- asset() is NOT required for CSS constants; safe() escapes output -->
  <link rel="stylesheet" href="<?= safe($css) ?>">
<?php endforeach; ?>

<script src="<?= safe(TAILWIND_CONFIG_JS) ?>"></script>

<header id="siteHeader" class="text-color font-medium select-none">

  <!-- TOP BAR -->
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center">

    <!-- LOGO -->
    <a href="<?= HOME_URL ?>" class="flex items-center space-x-2 group">
      <img
        src="<?= logo($header['logo_path'] ?? null) ?>"
        alt="<?= field($header, 'site_title', 'Site Logo') ?>"
        class="h-9 w-9 rounded-full transition-transform group-hover:rotate-12 duration-300 shadow-md
               shadow-[<?= safe($header['accent_color'] ?? '#000') ?>55]"
      >
      <span class="logo-text gradient-text tracking-wide duration-300">
        <?= field($header, 'site_title', SITE_TITLE) ?>
      </span>
    </a>

    <!-- DESKTOP NAVIGATION -->
    <nav class="hidden md:flex space-x-6 text-clamp">
      <?php foreach ($nav_links as $link): ?>

        <?php
          /**
           * URL handling:
           * - Navigation URLs are INTERNAL routes
           * - They are validated in JSON + DB layer
           * - We normalize but do NOT escape URLs as text
           */
          $linkPath = '/' . trim($link['url'] ?? '', '/');
          $finalUrl = $BASE_URL . $linkPath;
          $isActive = ($currentRoute === $linkPath);
        ?>

        <a href="<?= safe($finalUrl) ?>"
           class="<?= $isActive
             ? 'text-accent underline underline-offset-8 decoration-2 font-semibold'
             : 'hover:text-accent'
           ?>">
          <?= field($link, 'label') ?>
        </a>

      <?php endforeach; ?>
    </nav>

    <?php
      /**
       * CTA Button URL
       * - Must be an INTERNAL route
       * - Already validated by JsonValidator
       */
      $ctaPath = '/' . trim($header['button_link'] ?? '', '/');
      $ctaUrl  = $BASE_URL . $ctaPath;
    ?>

    <!-- CTA BUTTON -->
    <a href="<?= safe($ctaUrl) ?>"
       class="hidden sm:inline-block bg-gradient-to-r from-[#d32f2f] via-[#ff5a5a] to-[#ff8c5a]
              text-darkbg font-bold px-5 py-2 rounded-md btn-glow">
      <?= field($header, 'button_text', 'Contact') ?>
    </a>

    <!-- MOBILE MENU BUTTON -->
    <button id="menuBtn"
            class="md:hidden flex items-center p-2 rounded hover:text-accent transition-transform duration-300 hover:rotate-90">
      <i class="fa-solid fa-bars text-xl"></i>
    </button>

  </div>

  <!-- MOBILE MENU -->
  <div id="mobileMenu" class="md:hidden hidden bg-[#111] border-t border-[#333] text-white">
    <nav class="flex flex-col p-4 space-y-3 font-medium text-base">
      <?php foreach ($nav_links as $link): ?>
        <?php $mobileUrl = $BASE_URL . '/' . trim($link['url'] ?? '', '/'); ?>
        <a href="<?= safe($mobileUrl) ?>" class="hover:text-accent">
          <?= field($link, 'label') ?>
        </a>
      <?php endforeach; ?>
    </nav>
  </div>

</header>

<!-- HEADER JS -->
<?php foreach ($header_js as $js): ?>
  <script src="<?= safe($js) ?>"></script>
<?php endforeach; ?>
