<?php
require_once __DIR__ . '/../config/config.php';
require_once INCLUDES_PATH . 'logger.php';
require_once CORE_PATH . 'Controller.php';
require_once CORE_PATH . 'App.php';

// Run controller
$data = App::run("HomeController@index");

// Extract data
$home     = $data["home"];
$about    = $data["about"];
$skills   = $data["skills"];
$projects = $data["projects"];
$contact  = $data["contact"];

$page_title = "Yogesh Portfolio | Full Stack Developer";
$custom_css = [INDEX_CSS];
$custom_js  = [INDEX_JS];

require_once LAYOUT_HEAD_FILE;
?>

<!-- HERO SECTION -->
<section class="text-center py-24 sm:py-28 relative overflow-hidden parallax-bg"
    style="background-image:url('<?= htmlspecialchars($home['background_image']) ?>')">
    
    <lottie-player 
        src="https://assets10.lottiefiles.com/packages/lf20_jcikwtux.json"
        background="transparent"
        speed="1" loop autoplay
        style="position:absolute; inset:0; opacity:0.15; z-index:-1;">
    </lottie-player>

    <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold mb-6 text-accent reveal">
        <?= $home['hero_heading'] ?>
    </h1>

    <p class="text-base sm:text-lg md:text-xl text-gray-300 mb-8 reveal max-w-2xl mx-auto">
        <?= htmlspecialchars($home['hero_subheading']) ?>
    </p>

    <div class="flex flex-wrap justify-center gap-4 reveal">
        <a href="<?= htmlspecialchars($home['projects_link']) ?>" 
            class="bg-accent text-darkbg px-6 py-3 rounded-full font-semibold hover:bg-red-600 transition">
            View Projects
        </a>
        <a href="<?= htmlspecialchars($home['cv_link']) ?>" 
            class="border border-accent text-accent px-6 py-3 rounded-full font-semibold hover:bg-accent hover:text-darkbg transition">
            Download CV
        </a>
    </div>
</section>

<!-- ABOUT -->
<section class="py-20 px-4 sm:px-10 lg:px-20 text-center bg-[#111827] reveal-left">
    <h2 class="text-3xl sm:text-4xl font-bold mb-8 text-accent">
        <?= htmlspecialchars($about['title']) ?>
    </h2>

    <p class="max-w-3xl mx-auto text-gray-300 leading-relaxed text-base sm:text-lg lg:text-xl">
        <?= $about['content'] ?>
    </p>
</section>

<!-- SKILLS -->
<section class="py-20 px-4 sm:px-10 lg:px-20 text-center reveal-right">
    <div class="max-w-6xl mx-auto">
        <h3 class="text-3xl sm:text-4xl font-bold mb-10 text-accent">Skills</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4 sm:gap-6">
            <?php if(empty($skills)): ?>
                <p>No skills found.</p>
            <?php else: ?>
                <?php foreach ($skills as $s): ?>
                    <div class="p-4 bg-[#1E293B] rounded-xl hover:bg-accent hover:text-darkbg transition transform hover:scale-105">
                        <i class="<?= $s['icon_class'] ?> <?= $s['color_class'] ?> text-3xl sm:text-4xl md:text-5xl mb-3"></i>
                        <h4 class="font-semibold"><?= htmlspecialchars($s['skill_name']) ?></h4>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- PROJECTS -->
<section class="py-20 px-4 sm:px-10 lg:px-20 bg-[#111827] reveal-left">
    <h2 class="text-3xl sm:text-4xl font-bold mb-12 text-center text-accent">Featured Projects</h2>
    <div class="grid gap-8 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 max-w-screen-xl mx-auto">
        <?php if(empty($projects)): ?>
            <p>No featured projects found.</p>
        <?php else: ?>
            <?php foreach ($projects as $p): ?>
                <div class="bg-[#1E293B] rounded-xl overflow-hidden shadow-lg hover:scale-105 transition">
                    <img src="<?= htmlspecialchars($p['image_path']) ?>" alt="<?= htmlspecialchars($p['title']) ?>" class="aspect-video w-full object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($p['title']) ?></h3>
                        <p class="text-gray-400 text-sm mb-4"><?= htmlspecialchars($p['description']) ?></p>

                        <a href="<?= htmlspecialchars($p['project_link']) ?>" class="text-accent hover:underline">
                            View →
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="text-center mt-10">
        <a href="projects.php" class="border border-accent text-accent px-6 py-3 rounded-full hover:bg-accent hover:text-darkbg transition">
            See All Projects
        </a>
    </div>
</section>

<!-- CONTACT -->
<section class="py-20 px-4 sm:px-10 lg:px-20 text-center reveal-right">
    <h3 class="text-3xl sm:text-4xl font-bold mb-8 text-accent">
        <?= htmlspecialchars($contact['title']) ?>
    </h3>

    <p class="text-gray-300 mb-10 max-w-xl sm:max-w-2xl mx-auto text-base sm:text-lg">
        <?= htmlspecialchars($contact['subtitle']) ?>
    </p>

    <a href="<?= htmlspecialchars($contact['button_link']) ?>" 
       class="bg-accent text-darkbg px-6 py-3 rounded-full font-semibold hover:bg-red-600 transition">
       <?= htmlspecialchars($contact['button_text']) ?>
    </a>
</section>

<?php require_once LAYOUT_FOOT_FILE; ?>
