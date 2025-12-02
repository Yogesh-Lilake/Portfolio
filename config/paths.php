<?php
/**
 * PATHS.PHP
 * Absolute filesystem paths + URL paths.
 * Works on localhost + InfinityFree perfectly.
 */

/* ---------------------------------------------
   1. ROOT PATH (ABSOLUTE SERVER FILESYSTEM PATH)
---------------------------------------------- */
define('ROOT_PATH', str_replace('\\', '/', realpath(__DIR__ . '/../')) . '/');

/* ---------------------------------------------
   2. PUBLIC PATH (PUBLIC DOCUMENT ROOT)
---------------------------------------------- */
define('PUBLIC_PATH', ROOT_PATH . 'public/');

/* ---------------------------------------------
   3. APP PATHS
---------------------------------------------- */
define('APP_PATH', ROOT_PATH . 'app/');
define('CORE_PATH', APP_PATH . 'core/');
define('CONTROLLERS_PATH', APP_PATH . 'controllers/');
define('MODELS_PATH', APP_PATH . 'models/');
define('RESOURCES_PATH', APP_PATH . 'resources/');
define('VIEWS_PATH', APP_PATH . 'views/');
define('SERVICES_PATH', APP_PATH . 'services/');
define('HELPERS_PATH', APP_PATH . 'helpers/');

define('APP_FILE', CORE_PATH . 'app.php');
define('CONTROLLER_FILE', CORE_PATH . 'Controller.php');
define('ERROR_HANDLER_FILE', CORE_PATH . 'ErrorHandler.php');

define('HOME_MODEL_FILE', MODELS_PATH . 'HomeModel.php');
define('ABOUT_MODEL_FILE', MODELS_PATH . 'AboutModel.php');
define('SKILL_MODEL_FILE', MODELS_PATH . 'SkillModel.php');
define('PROJECT_MODEL_FILE', MODELS_PATH . 'ProjectModel.php');
define('CONTACT_MODEL_FILE', MODELS_PATH . 'ContactModel.php');
define('NOTE_MODEL_FILE', MODELS_PATH . 'NoteModel.php');

define('DEFAULTS_PATH', RESOURCES_PATH . 'defaults/');
define('HOME_DEFAULTS_PATH', DEFAULTS_PATH . 'home/');
define('HOME_DEFAULTS_FILE', HOME_DEFAULTS_PATH . 'home.json');

/* VIEWS SUBFOLDERS */
define('LAYOUTS_PATH', VIEWS_PATH . 'layouts/');
define('COMPONENTS_PATH', VIEWS_PATH . 'components/');
define('PAGES_PATH', VIEWS_PATH . 'pages/');
define('HOME_PATH', VIEWS_PATH . 'home/');

define('HOME_VIEW_FILE', HOME_PATH . 'index.php');

define('CACHESERVICE_FILE', SERVICES_PATH . 'CacheService.php');

/* ---------------------------------------------
   4. CONFIG FOLDER
---------------------------------------------- */
define('CONFIG_PATH', ROOT_PATH . 'config/');

define('CONFIG_FILE', CONFIG_PATH . 'config.php');

/* ---------------------------------------------
   5. INCLUDES FOLDER
---------------------------------------------- */
define('INCLUDES_PATH', ROOT_PATH . 'includes/');

/* ---------------------------------------------
   6. ROUTES FOLDER
---------------------------------------------- */
define('ROUTES_PATH', ROOT_PATH . 'routes/');

/* ---------------------------------------------
   7. LOGS FOLDER
---------------------------------------------- */
define('LOGS_PATH', ROOT_PATH . 'logs/');
define('LOG_FILE', LOGS_PATH . 'app.log');

define('STORAGE_PATH', ROOT_PATH . 'storage/');

define('CACHE_PATH', STORAGE_PATH . 'cache/');

/* ---------------------------------------------
   8. ASSETS (FILESYSTEM PATHS)
---------------------------------------------- */
define('ASSETS_PATH', PUBLIC_PATH . 'assets/');
define('CSS_PATH', ASSETS_PATH . 'css/');
define('JS_PATH', ASSETS_PATH . 'js/');
define('IMG_PATH', ASSETS_PATH . 'images/');
define('RESUME_PATH', ASSETS_PATH . 'resume/');

define('ABOUT_VIEW_FILE', PAGES_PATH . 'about.php');
define('PROJECT_VIEW_FILE', PAGES_PATH . 'projects.php');
define('NOTES_VIEW_FILE', PAGES_PATH . 'notes.php');
define('CONTACT_VIEW_FILE', PAGES_PATH . 'contact.php');

/* ---------------------------------------------
   9. URL BASE DETECTION (WORKS LOCAL + LIVE)
---------------------------------------------- */
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    ? 'https://' : 'http://';

$host = $_SERVER['HTTP_HOST'];

/* LOCALHOST CASE (custom folder name allowed) */
if ($host === 'localhost') {
    // Change "Portfolio" to your actual localhost folder name
    define('BASE_URL', $protocol . $host . '/Portfolio/public/');
} else {
    // InfinityFree & live domain use root public folder
    define('BASE_URL', $protocol . $host . '/');
}

/* ---------------------------------------------
   10. ASSETS (URL PATHS)
---------------------------------------------- */
define('ASSETS_URL', BASE_URL . 'assets/');
define('CSS_URL', ASSETS_URL . 'css/');
define('JS_URL', ASSETS_URL . 'js/');
define('IMG_URL', ASSETS_URL . 'images/');
define('RESUME_URL', ASSETS_URL . 'resume/');

define('DB_CONNECTION_FILE', CORE_PATH . 'db_connection.php');
define('LOGGER_PATH', HELPERS_PATH . 'logger.php');

/* ---------------------------------------------
   11. PAGE ROUTES (URL)
---------------------------------------------- */
define('HOME_URL', BASE_URL . 'index.php');
define('ABOUT_URL', BASE_URL . 'about.php');
define('PROJECTS_URL', BASE_URL . 'projects.php');
define('NOTES_URL', BASE_URL . 'notes.php');
define('CONTACT_URL', BASE_URL . 'contact.php');

/* ---------------------------------------------
   12. LAYOUT FILES
---------------------------------------------- */
define('LAYOUT_HEAD_FILE', LAYOUTS_PATH . 'layout_head.php');
define('LAYOUT_FOOT_FILE', LAYOUTS_PATH . 'layout_foot.php');
define('HEADER_FILE', LAYOUTS_PATH . 'header.php');
define('FOOTER_FILE', LAYOUTS_PATH . 'footer.php');

/* ---------------------------------------------
   13. OTHER CONSTANTS
---------------------------------------------- */
define('SITE_TITLE', 'Yogesh Lilake');
define('SITE_LOGO', IMG_URL . 'logo.png');
define('FAVICON_PATH', IMG_URL . 'favicon.png');
define('CTA_TEXT', 'Download CV');
define('CTA_LINK', ASSETS_URL . 'Yogesh_Lilake_Resume.pdf');
define('ACCENT_COLOR', '#ff5a5a');

// =============================
// EXTERNAL LIBRARY URLs (Tailwind, Font Awesome, Lottie)
// =============================
define('TAILWIND_CDN', 'https://cdn.tailwindcss.com');
define('AOS_CSS', 'https://unpkg.com/aos@2.3.4/dist/aos.css');
define('AOS_JS', 'https://unpkg.com/aos@2.3.4/dist/aos.js');
define('FONT_AWESOME', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css');
define('LOTTIE_PLAYER', 'https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js');

// ====================================================
// PAGE ROUTES
// ====================================================
// Global asset paths
define('GLOBAL_CSS', CSS_URL . 'global.css');
define('ANIMATIONS_CSS', CSS_URL . 'animations.css');

// Tailwind config paths
define('TAILWIND_CONFIG_JS', JS_URL . 'tailwind-config.js');
define('TAILWIND_CONFIG_GLOBAL', JS_URL . 'tailwind-config-global.js');

// Page-specific asset paths
// CSS paths
define('HEADER_CSS', CSS_URL . 'header.css');
define('FOOTER_CSS', CSS_URL . 'footer.css');
define('INDEX_CSS', CSS_URL . 'index.css');
define('ABOUT_CSS', CSS_URL . 'about.css');
define('PROJECTS_CSS', CSS_URL . 'projects.css');
define('NOTES_CSS', CSS_URL . 'notes.css');
define('CONTACT_CSS', CSS_URL . 'contact.css');

// Footer asset paths
define('HEADER_JS', JS_URL . 'header.js');
define('FOOTER_JS', JS_URL . 'footer.js');
define('SCROLL_PROGRESS_JS', JS_URL . 'scroll-progress.js');
define('INDEX_JS', JS_URL . 'index.js');
define('ABOUT_JS', JS_URL . 'about.js');
define('PROJECTS_JS', JS_URL . 'projects.js');
define('NOTES_JS', JS_URL . 'notes.js');
define('CONTACT_JS', JS_URL . 'contact.js');

?>
