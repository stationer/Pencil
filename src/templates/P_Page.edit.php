<?php echo $View->render('header'); ?>

<main class="content" style="padding:20px;">
    <h1 class="page-title">Edit Page</h1>
    <?php d($Page); ?>
    <form action="/P_Page/edit/<?php echo $Page->node_id; ?>" method="post">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" class="form-control" value="<?php echo $Page->File->title ?? 'No Title'; ?>">
        </div>

        <div class="form-group">
            <label for="body">Body</label>
            <textarea name="body" class="form-control" ><?php echo $Page->File->body ?? ''; ?></textarea>
        </div>

        <div class="form-group">
            <label for="node_label">Node Label</label>
            <input type="text" name="node_label" class="form-control" value="<?php echo $Page->label ?? 'No Label'; ?>">
        </div>

        <div class="form-group">
            <label for="keywords">Keywords</label>
            <input type="text" name="keywords" class="form-control" placeholder="keywords" value="<?php echo $Page->keywords ?? ''; ?>">
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" name="description" class="form-control" placeholder="description" value="<?php echo $Page->description ?? ''; ?>">
        </div>

        <div class="form-group">
            <label for="published">Published</label>
            <input type="checkbox" name="published" class="form-control" <?php echo 1 == $Page->published ? 'checked="checked"': ''; ?>>
        </div>

        <div class="form-group">
            <label for="trashed">Trashed</label>
            <input type="checkbox" name="trashed" class="form-control" <?php echo 1 == $Page->trashed ? 'checked="checked"': ''; ?>>
        </div>

        <div class="form-group">
            <label for="featured">Featured</label>
            <input type="checkbox" name="featured" class="form-control" <?php echo 1 == $Page->featured ? 'checked="checked"': ''; ?>>
        </div>

        <button type="submit" class="btn btn-primary">Update Page.</button>
    </form>
</main>

<?php echo $View->render('footer');
