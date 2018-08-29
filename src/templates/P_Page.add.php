<?php echo $View->render('header'); ?>
<div class="container">
    <h1 class="page-title">Add Page</h1>

    <form action="/P_Page/add/" method="post">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" class="form-control"
                   value="<?php echo $Page->title ?: 'No Title'; ?>">
        </div>

        <div class="form-group">
            <label for="template_id">Template</label>
            <select name="template_id" class="form-control">
                <option value="">--- Select a Template ---</option>
                <?php foreach ($Templates as $Template) : ?>
                    <option value="<?php echo $Template->node_id; ?>" <?php
                    echo $Template->node_id == $Page->template_id ? 'selected' : '';
                    ?>><?php echo $Template->path; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="label">Label</label>
            <input type="text" name="label" class="form-control"
                   value="<?php echo $Node->label; ?>">
        </div>

        <div class="form-group">
            <label for="keywords">Keywords</label>
            <input type="text" name="keywords" class="form-control" value="<?php echo $Node->keywords ?? ''; ?>">
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" name="description"></textarea>
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

        <button type="submit" class="btn btn-primary">Add Page</button>
    </form>
</div>
<?php echo $View->render('footer');
