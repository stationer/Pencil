<?php
echo $View->render('header'); ?>
<div class="container">
    <h1>Add Theme</h1>

    <form action="/P_Theme/add/" method="POST">

        <div class="form-group">
            <label for="title">Label</label>
            <input class="form-control" type="text" name="label" value="<?php echo $Node->label ?? ''; ?>">
        </div>

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
            <label for="header">Theme Header</label>
            <textarea class="form-control" name="header"><?php echo $Theme->header; ?></textarea>
        </div>

        <div class="form-group">
            <label for="footer">Theme Footer</label>
            <textarea class="form-control" name="footer"><?php echo $Theme->footer; ?></textarea>
        </div>

        <div class="form-group">
            <label for="css">Theme CSS</label>
            <textarea class="form-control" name="css"><?php echo $Theme->css; ?></textarea>
        </div>

        <button class="btn btn-primary" type="submit">Add Theme</button>
    </form>
</div>

<?php echo $View->render('footer');
