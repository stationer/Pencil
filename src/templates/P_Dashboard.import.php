<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node $Node */
$formAction = '/P_Dashboard/import';
$formHeader = 'Import Pencil Data';
echo $View->render('header');
?>

    <form class="m-flex" action="<?= $formAction ?>" method="post" enctype="multipart/form-data">
        <section class="l-two-thirds">
            <div class="c-card">
                <div class="header">
                    <div>
                        <h5><?= $formHeader ?></h5>
                    </div>
                </div>
                <div class="content">
                    <div class="form-group">
                        <label for="upload">Upload</label>
                        <input type="file" class="form-control" name="upload" id="upload">
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
                    <a href="/P_Dashboard/export">Download Export</a>
                </div>
                <div class="footer">
                    <div class="buttons">
                        <button type="submit" class="c-btn m-primary">Import</button>
                    </div>
                </div>
            </div>
        </section>
    </form>

<?php echo $View->render('footer');
