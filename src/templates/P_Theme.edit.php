<?php
echo $View->render('header'); ?>
<div class="container">
    <h1>Edit Theme</h1>

    <form action="/P_Theme/edit/<?php echo $Node->node_id; ?>" method="POST">
        <div class="form-group">
            <label for="published">Published</label>
            <input class="form-control" type="checkbox" name="published" <?php echo $Node->published ? 'checked="checked"':''; ?>>
        </div>

        <div class="form-group">
            <label for="trashed">Trashed</label>
            <input class="form-control" type="checkbox" name="trashed" <?php echo $Node->trashed ? 'checked="checked"':''; ?>>
        </div>

        <div class="form-group">
            <label for="header">Description</label>
            <textarea class="form-control" name="description"><?php echo $Node->description; ?></textarea>
        </div>

        <div class="form-group">
            <label for="header">Theme Root Document</label>
            <textarea class="form-control" name="document"><?php echo $Node->File->document; ?></textarea>
        </div>

        <div class="form-group">
            <label for="header">Theme Header</label>
            <textarea class="form-control" name="header"><?php echo $Node->File->header; ?></textarea>
        </div>

        <div class="form-group">
            <label for="footer">Theme Footer</label>
            <textarea class="form-control" name="footer"><?php echo $Node->File->footer; ?></textarea>
        </div>

        <div class="form-group">
            <label for="css">Theme CSS</label>
            <textarea class="form-control" name="css"><?php echo $Node->File->css; ?></textarea>
        </div>

        <button class="btn btn-primary" type="submit">Update Theme</button>
    </form>
</div>

<?php echo $View->render('footer');
