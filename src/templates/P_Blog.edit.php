<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node $Node */
/** @var \Stationer\Pencil\models\Article $Content */
/** @var string $formHeader */
/** @var string $formAction */
echo $View->render('header');
?>
    <form class="m-flex" action="<?= $formAction ?>" method="post">
        <section class="l-two-thirds">
            <div class="c-card">
                <div class="header">
                    <h5><?= $formHeader ?></h5>
                </div>
                <div class="content">
                    <?php include 'P_Dashboard._nodeFormElements.php'; ?>

                    <div class="form-group">
                        <label for="body"></label>
                        <textarea name="body" id="body"><?php echo $Node->File->body; ?></textarea>
                    </div>
                </div>
            </div>
        </section>
        <section class="l-one-third">
            <div class="c-card c-options">
                <div class="header">
                    <h5>Options</h5>
                </div>
                <div class="content">
                    <?php include 'P_Dashboard._nodeFormOptions.php'; ?>
                </div>
                <div class="footer">
                    <div class="buttons">
                        <button class="c-btn" type="submit"><?= $formHeader ?></button>
                    </div>
                </div>
            </div>
        </section>
    </form>

<?php echo $View->render('footer');
