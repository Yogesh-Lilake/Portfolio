</main>

<?php include INCLUDES_PATH . "footer.php"; ?>

<!-- Global JS -->
<script src="<?= SCROLL_PROGRESS_JS ?>"></script>

<!-- Page-specific JS -->
<?php foreach ($custom_js as $js): ?>
    <script src="<?= htmlspecialchars($js) ?>"></script>
<?php endforeach; ?>

</body>
</html>
