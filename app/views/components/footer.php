<?php
/**
 * FOOTER COMPONENT
 * Safe-mode aware, zero-crash
 */

$safeMode = $data['safe_mode'] ?? false;

$footer = [];
$footer_links = [];
$social_links = [];

if (!$safeMode) {
    try {
        require_once FOOTERSERVICE_FILE;

        /*
        use app\core\DB;
        use app\Services\FooterData;

        $db = DB::getInstance()->pdo();

        $data = (new FooterData($db))->get();
        */
        // ✅ Use fully qualified class names (NO use statements in views)
        $db = \app\Core\DB::getInstance()->pdo();
        $footerData = (new \app\Services\FooterData($db))->get();

        $footer       = $footerData['footer'] ?? [];
        $footer_links = $footerData['links'] ?? [];
        $social_links = $footerData['social'] ?? [];

    } catch (Throwable $e) {
        app_log("Footer fallback triggered: " . $e->getMessage(), "error");
        $safeMode = true;
    }
}

if ($safeMode):
?>
<footer class="mt-24 py-12 text-center text-gray-400 border-t border-[#ffffff08]">
    <p>© <?= date('Y') ?> Yogesh Lilake</p>
    <p class="mt-2 text-sm">Some features are temporarily unavailable.</p>
</footer>
<?php return; endif; ?>

<!-- FOOTER CSS -->
<link rel="stylesheet" href="<?= FOOTER_CSS ?>">

<footer class="text-color font-medium mt-24 border-t border-[#ffffff08] relative">

    <div class="relative z-10 max-w-7xl mx-auto px-6 py-14 grid gap-10 sm:grid-cols-2 lg:grid-cols-3">

        <div>
            <h2 class="text-white text-2xl font-bold">
                <?= htmlspecialchars($footer['brand_name'] ?? '') ?>
            </h2>
            <p class="text-gray-400 mt-3">
                <?= htmlspecialchars($footer['footer_description'] ?? '') ?>
            </p>
        </div>

        <div>
            <h3 class="text-white font-semibold mb-3">Quick Links</h3>
            <?php foreach ($footer_links as $link): ?>
                <a href="<?= url($link['url']) ?>" class="block hover:text-accent">
                    <?= htmlspecialchars($link['label']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div>
            <h3 class="text-white font-semibold mb-3">Connect</h3>
            <div class="flex gap-4">
                <?php foreach ($social_links as $s): ?>
                    <a href="<?= htmlspecialchars($s['url']) ?>" target="_blank">
                        <i class="fab <?= htmlspecialchars($s['icon_class']) ?>"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <div class="text-center text-gray-500 py-6 border-t border-[#222]">
        © <?= date('Y') ?> <?= htmlspecialchars($footer['developer_name'] ?? '') ?>
    </div>
</footer>

<script src="<?= FOOTER_JS ?>"></script>