<?php
/** @var \Stationer\Graphite\View $View */
$_tail = $tail ?? '';
?>
</div>
</div>

<?php echo $View->render('debug'); ?>
</main>
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<?php foreach ($_script as $v) { ?>
    <script type="text/javascript" src="<?php html($v) ?>"></script>
<?php } ?>
<script>
    // Attach Quilljs to elements of choice
    new Nib().dipByQuery('.wysiwyg');
</script>
<div id="G__tail"><?php echo $_tail; ?></div>
</body>

</html>
