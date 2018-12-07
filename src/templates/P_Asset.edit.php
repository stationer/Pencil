<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node $Node */
/** @var string $formHeader */
/** @var string $formAction */

echo $View->render('header');
?>
    <form class="m-flex" action="<?= $formAction ?>" method="post" enctype="multipart/form-data">
        <section class="l-two-thirds">
            <div class="c-card">
                <div class="header">
                    <h5><?= $formHeader ?></h5>
                </div>
                <div class="content">
                    <div class="form-group">
                        <label for="label">Label</label>
                        <input type="text" name="label" id="label" class="form-control"
                               value="<?php echo $Node->label; ?>">
                    </div>

                    <div class="form-group">
                        <label for="keywords">Keywords</label>
                        <input type="text" name="keywords" id="keywords" class="form-control"
                               value="<?php echo $Node->keywords ?? ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" name="description" id="description"><?php html($Node->description); ?></textarea>
                    </div>

                    <?php // TODO: Can we add file name to display when a user is uploading a file ?>
                    <div class="form-group">
                        <label>File Name</label>
                        <?php if (strlen($Node->File->path) == 0) { ?>
                            <p class="m-sm">No File Found</p>
                        <?php } else { ?>
                            <p class="m-sm"><?= $Node->File->path ?></p>
                        <?php } ?>
                    </div>

                    <div class="form-group">
                        <label for="Media File">Media File</label>
                        <label for="upload" class="c-upload">Upload File</label>
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
                    <?php include 'P_Dashboard._nodeFormOptions.php'; ?>
                </div>
                <div class="footer">
                    <div class="buttons">
                        <button type="submit" class="c-btn">Save</button>
                    </div>
                </div>
            </div>
            <?php if ('image/' == substr($Node->File->type, 0, 6)) : ?>
                <div class="c-card">
                    <div class="header">
                        <h5>Preview</h5>
                    </div>
                    <div class="content">

                        <?php // TODO: revisit inline styles ?>
                        <img src="/P_Cache/600x600<?= $Node->File->path ?>"
                             style="width: 100%;">

                    </div>
                </div>
            <?php endif; ?>
        </section>
    </form>

<?php echo $View->render('footer');
