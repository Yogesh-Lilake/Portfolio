<?php
/**
 * GLOBAL <head> + auto header loader
 */

require_once dirname(__DIR__, 3) . '/config/paths.php';
require_once CONFIG_PATH . 'config.php';

// Page dynamic values
$page_title = $page_title ?? SITE_TITLE;
$custom_css = $custom_css ?? [];
$extra_css  = $extra_css ?? [];
$custom_js  = $custom_js ?? [];
$extra_js   = $extra_js ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title><?= htmlspecialchars($page_title) ?></title>

    <!-- FAVICON -->
    <link rel="icon" type="image/png" href="<?= FAVICON_PATH ?>">

    <!-- GLOBAL CSS + ANIMATION CSS-->
    <link rel="stylesheet" href="<?= GLOBAL_CSS ?>">
    <link rel="stylesheet" href="<?= ANIMATIONS_CSS ?>">

    <!-- Page-level CSS -->
    <?php foreach ($custom_css as $css): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
    <?php endforeach; ?>

    <!-- Extra CSS -->
    <?php foreach ($extra_css as $css): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
    <?php endforeach; ?>

    <!-- Tailwind + Config -->
    <script src="<?= TAILWIND_CDN ?>"></script>
    <script src="<?= TAILWIND_CONFIG_GLOBAL ?>"></script>

    <!-- Icons + Animations -->
    <script src="<?= LOTTIE_PLAYER ?>"></script>
    <link rel="stylesheet" href="<?= FONT_AWESOME ?>">

    <!-- Extra JS -->
    <?php foreach ($extra_js as $js): ?>
        <script src="<?= htmlspecialchars($js) ?>"></script>
    <?php endforeach; ?>
</head>

<body class="bg-darkbg text-color font-sans scroll-smooth min-h-screen flex flex-col">
    <div id="scroll-progress"></div>

    <!-- AUTO HEADER -->
    <?php include HEADER_FILE; ?>

    <main class="flex-grow">
