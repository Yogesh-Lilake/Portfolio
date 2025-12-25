<?php
/**
 * PROJECTS PAGE
 * Safe-mode aware
 */

// Normalize input
$projects   = $data['projects']   ?? [];
$techList   = $data['techList']   ?? [];
$page       = $data['page']       ?? 1;
$totalPages = $data['totalPages'] ?? 1;
$total      = $data['total']      ?? 0;

$safeMode = $data['safe_mode'] ?? false;

// Meta
$page_title = "Projects | " . SITE_TITLE;
$custom_js  = $safeMode ? [] : [PROJECTS_JS];

require_once LAYOUT_HEAD_FILE;
?>

<?php if ($safeMode): ?>
    <div class="bg-yellow-500 text-black text-center py-3 px-4 font-semibold">
        Projects are currently in limited view mode.
    </div>
<?php endif; ?>

<!-- HERO -->
<section class="relative py-20 sm:py-24 px-4 text-center overflow-hidden">
    <lottie-player
        src="https://assets10.lottiefiles.com/packages/lf20_pwohahvd.json"
        background="transparent"
        speed="1"
        loop autoplay
        style="position:absolute; inset:0; opacity:0.15; z-index:-1;">
    </lottie-player>

    <h1 class="font-extrabold text-accent text-[clamp(2rem,4vw,3.5rem)] mb-4 <?= $safeMode ? '' : 'fade-up' ?>">Featured 
        <span class="text-white">Projects</span>
    </h1>
    <p class="text-gray-300 max-w-2xl mx-auto text-[clamp(0.9rem,1.2vw,1.25rem)] <?= $safeMode ? '' : 'fade-up' ?>">A showcase of my web and mobile projects — combining creativity, scalability, and clean code.</p>
</section>

<!-- PROJECT GRID -->
<section class="py-16 sm:py-20 px-4 sm:px-8 lg:px-16 <?= $safeMode ? '' : 'fade-up' ?>">
    <div class="3xl:max-w-[1600px] mx-auto max-w-screen-2xl grid xs:grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 3xl:grid-cols-4 gap-8 sm:gap-10">

        <?php if ($total === 0): ?>
            <div class="col-span-full bg-[#0f1724] p-10 rounded-xl text-center">
                <h2 class="text-2xl font-bold mb-2">No projects found</h2>
                <p class="text-gray-400">Try different filters or check back later.</p>
            </div>
        <?php else: ?>
            <?php foreach ($projects as $p): ?>
                <div class="bg-[#1E293B] rounded-2xl overflow-hidden shadow-lg hover:scale-[1.03] transition">

                    <!-- IMAGE -->
                    <div class="relative aspect-[4/3]">
                        <img
                            src="<?= asset($p['image_path'] ?? '') ?>"
                            alt="<?= safe($p['title'] ?? '') ?>"
                            class="absolute inset-0 w-full h-full object-cover"
                            loading="lazy"
                            onerror="this.onerror=null;this.src='<?= asset('project-placeholder.png') ?>';">

                        <?php if (!$safeMode): ?>
                            <div class="absolute inset-0 bg-black/40 opacity-0 hover:opacity-100 flex items-center justify-center transition">
                                <a href="<?= url('projects/' . ($p['slug'] ?? '')) ?>"
                                   class="bg-accent text-darkbg px-5 py-2 rounded-full font-semibold hover:bg-red-600">
                                    View Project
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- CONTENT -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-accent mb-2">
                            <?= safe($p['title'] ?? '') ?>
                        </h3>

                        <p class="text-gray-300 text-sm mb-4">
                            <?= safe($p['description'] ?? '') ?>
                        </p>

                        <!-- TECH STACK -->
                        <?php if (!$safeMode && !empty($techList[$p['id']] ?? [])): ?>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($techList[$p['id']] as $t): ?>
                                    <span class="<?= safe($t['color_class'] ?? 'bg-gray-600') ?> px-3 py-1 rounded-full text-xs font-semibold">
                                        <?= safe($t['tech_name'] ?? '') ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- PAGINATION -->
    <?php if (!$safeMode && $totalPages > 1): ?>
        <div class="mt-10 flex justify-center gap-2">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>"
                   class="px-4 py-2 rounded
                   <?= $i == $page ? 'bg-accent text-darkbg' : 'bg-gray-700 text-gray-200' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</section>

<?php if ($safeMode): ?>
    <!-- SAFE MODE QUICK LINKS (TRUST + NAVIGATION) -->
    <section class="py-12 text-center text-gray-400">
        <p class="mb-4">You can still explore:</p>
        <div class="flex justify-center gap-6 font-medium">
            <a href="<?= url('') ?>" class="hover:text-accent">Home</a>
            <a href="<?= url('about') ?>" class="hover:text-accent">About</a>
            <a href="<?= url('notes') ?>" class="hover:text-accent">Notes</a>
            <a href="<?= url('contact') ?>" class="hover:text-accent">Contact</a>
        </div>
    </section>
<?php endif; ?>

<?php require_once LAYOUT_FOOT_FILE; ?>
