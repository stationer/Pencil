<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node $Node */
/** @var string $formHeader */
/** @var string $formAction */

$types = [
    0 => 'Header',
    1 => 'Footer',
    2 => 'Page',
    3 => 'Post',
];
echo $View->render('header');
?>
    <form class="m-flex" action="<?= $formAction ?>" method="post">
        <section class="l-two-thirds">
            <div class="c-card">
                <div class="header">
                    <h5><?= $formHeader ?></h5>
                </div>
                <div class="content">
                    <div class="form-group">
                        <label for="label">Label</label>
                        <input class="form-control" type="text" name="label" id="label" value="<?php echo $Node->label ?? ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" name="description" id="description"><?php echo $Node->description; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="body">Template Body</label>
                        <textarea class="form-control" name="body" id="body"><?php echo $Node->File->body; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="css">Template CSS</label>
                        <textarea class="form-control" name="css" id="css"><?php echo $Node->File->css; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="type">Template Type</label>
                        <select class="form-control" name="type" id="type">
                            <?php foreach ($types as $key => $type) : ?>
                                <option value="<?php echo $key; ?>" <?php echo $key == $Node->File->type ? 'selected="selected"' : ''; ?>>
                                    <?php echo $type; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
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
        </section>
    </form>

<?php echo $View->render('footer');
