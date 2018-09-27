<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node $Node */
echo $View->render('header');
?>
<div class="container">
    <h1>Edit Theme</h1>

    <form action="/P_Theme/edit/<?php echo $Node->node_id; ?>" method="POST">
        <div class="form-group">
            <label for="label">Label</label>
            <input class="form-control" type="text" name="label" id="label" value="<?php echo $Node->label ?? ''; ?>">
        </div>

        <div class="form-group">
            <label for="published">Published</label>
            <input class="form-control" type="checkbox" name="published" id="published" <?php echo $Node->published ? 'checked="checked"':''; ?>>
        </div>

        <div class="form-group">
            <label for="trashed">Trashed</label>
            <input class="form-control" type="checkbox" name="trashed" id="trashed" <?php echo $Node->trashed ? 'checked="checked"':''; ?>>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" name="description" id="description"><?php echo $Node->description; ?></textarea>
        </div>

        <div class="form-group">
            <label for="document">Theme Root Document</label>
            <textarea class="form-control" name="document" id="document"><?php html($Node->File->document); ?></textarea>
        </div>

        <div class="form-group">
            <label for="header">Theme Header</label>
            <textarea class="form-control wysiwyg" name="header" id="header"><?php html($Node->File->header); ?></textarea>
        </div>

        <div class="form-group">
            <label for="footer">Theme Footer</label>
            <textarea class="form-control wysiwyg" name="footer" id="footer"><?php html($Node->File->footer); ?></textarea>
        </div>

        <div class="form-group">
            <label for="css">Theme CSS</label>
            <textarea class="form-control" name="css" id="css"><?php echo $Node->File->css; ?></textarea>
        </div>

        <button class="btn btn-primary" type="submit">Update Theme</button>
    </form>
</div>

<?php echo $View->render('footer');
