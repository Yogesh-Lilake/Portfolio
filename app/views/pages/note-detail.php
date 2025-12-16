<?php
$note    = $data['note'];
$tags    = $data['tags'] ?? [];
$related = $data['related'] ?? [];

$page_title = safe($note['title']) . " | Notes";
require_once LAYOUT_HEAD_FILE;
?>

<link rel="canonical" href="<?= url('notes/' . $note['slug']) ?>">

<section class="px-6 md:px-20 py-16 max-w-4xl mx-auto">

    <h1 class="text-4xl font-bold text-white mb-4">
        <?= safe($note['title']) ?>
    </h1>

    <p class="text-gray-400 mb-6">
        <?= safe($note['category_name']) ?> · <?= date('M d, Y', strtotime($note['created_at'])) ?>
    </p>

    <article class="prose prose-invert max-w-none">
        <?= nl2br(safe($note['description'])) ?>
    </article>

    <?php if ($tags): ?>
        <div class="mt-8 flex flex-wrap gap-2">
            <?php foreach ($tags as $tag): ?>
                <span class="bg-gray-700 px-3 py-1 rounded-full text-sm">
                    #<?= safe($tag['name']) ?>
                </span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php if ($related): ?>
    <section class="px-6 md:px-20 pb-20">
        <h2 class="text-xl font-semibold mb-4 text-white">Related Notes</h2>
        <ul class="space-y-2">
            <?php foreach ($related as $r): ?>
                <li>
                    <a href="<?= url('notes/' . $r['slug']) ?>" class="text-accent hover:underline">
                        <?= safe($r['title']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
<?php endif; ?>

<?php require_once LAYOUT_FOOT_FILE; ?>
