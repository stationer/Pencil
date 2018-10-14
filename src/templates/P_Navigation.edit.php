<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node $Node */
/** @var string $formHeader */
/** @var string $formAction */
echo $View->render('header');
?>
<section class="l-two-thirds">
    <div class="c-card">
        <div class="header">
            <div>
                <h5><?= $formHeader ?></h5>
            </div>
        </div>
        <div class="content">
            <form action="<?= $formAction ?>" method="post">
                <div class="form-container">
<?php include 'P_Dashboard._nodeFormElements.php'; ?>
                    <div class="form-group">
                        <label for="source">Source</label>
                        <textarea style="height:150px;" class="form-control" name="source" id="source"><?php echo $Node->File->source; ?></textarea>
                    </div>

                    <button type="submit" class="c-btn">Save Navigation</button>
                </div>
            </form>
        </div>
    </div>
</section>
<?php echo ($Node->File->rendered); ?>

<?php echo $View->render('footer');
