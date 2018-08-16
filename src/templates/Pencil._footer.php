<?php echo $View->render('debug'); ?>
<?php $_tail = $tail ?? ''; ?>
<div id="G__tail"><?php echo $_tail; ?></div>

</main>
</div>
<!-- /#page-content-wrapper -->

</div>
<!-- /#wrapper -->

<!-- Bootstrap core JavaScript -->
<?php foreach ($_script as $v) { ?>
    <script type="text/javascript" src="<?php html($v) ?>"></script>
<?php } ?>
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script>
    feather.replace()
</script>
</body>

</html>
