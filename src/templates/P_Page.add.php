<?php echo $View->render('header'); ?>

    <h1 class="page-title">Edit Page</h1>

    <form action="/P_Page/edit/<?php echo $Page->node_id; ?>" method="post">
        <div class="form-group">
            <label for="keywords">Keywords</label>
            <input type="text" name="keywords" class="form-control" value="<?php echo $Node->keywords ?? ''; ?>">
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" name="description"></textarea>
        </div>

        <div class="form-group">
            <label for="trashed">Trashed</label>
            <input type="checkbox" name="trashed" <?php echo $Node->trashed ? 'checked="checked"': ''; ?>>
        </div>

        <div class="form-group">
            <label for="featured">Featured</label>
            <input type="checkbox" name="featured" <?php echo $Node->featured ? 'checked="checked"': ''; ?>>
        </div>

        <div class="form-group">
            <label for="published">Published</label>
            <input type="checkbox" name="published" <?php echo $Node->published ? 'checked="checked"': ''; ?>>
        </div>

        <div class="form-group">
            <label for="trashed">Trashed</label>
            <input type="checkbox" name="trashed" <?php echo $Node->trashed ? 'checked="checked"': ''; ?>>
        </div>

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" class="form-control">
        </div>

        <div class="form-group">
            <label for="body">Body</label>
            <textarea name="body" class="form-control" ></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update Page.</button>
    </form>

<?php echo $View->render('footer');
