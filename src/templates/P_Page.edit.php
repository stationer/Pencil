<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node $Page */
/** @var \Stationer\Pencil\models\Template[] $Templates */
echo $View->render('header');
?>

<main class="content" style="padding:20px;">
    <div class="container">
        <h1 class="page-title">Edit Page</h1>
        <form action="/P_Page/edit/<?php echo $Page->node_id; ?>" method="post">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" class="form-control" value="<?php echo $Page->File->title ?: 'No Title'; ?>">
            </div>

            <div class="form-group">
                <label for="template_id">Template</label>
                <select name="template_id" id="template_id" class="form-control">
                    <option value="">--- Select a Template ---</option>
                    <?php foreach ($Templates as $Template) : ?>
                        <option value="<?php echo $Template->node_id; ?>" <?php
                        echo $Template->node_id == $Page->File->template_id ? 'selected' : '';
                        ?>><?php echo $Template->path; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="node_label">Node Label</label>
                <input type="text" name="node_label" id="node_label" class="form-control" value="<?php echo $Page->label ?? 'No Label'; ?>">
            </div>

            <div class="form-group">
                <label for="keywords">Keywords</label>
                <input type="text" name="keywords" id="keywords" class="form-control" placeholder="keywords" value="<?php echo $Page->keywords ?? ''; ?>">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" name="description" id="description" class="form-control" placeholder="description" value="<?php echo $Page->description ?? ''; ?>">
            </div>

            <div class="form-group">
                <label for="published">Published</label>
                <input type="checkbox" name="published" id="published" class="form-control" <?php echo 1 == $Page->published ? 'checked="checked"': ''; ?>>
            </div>

            <div class="form-group">
                <label for="trashed">Trashed</label>
                <input type="checkbox" name="trashed" id="trashed" class="form-control" <?php echo 1 == $Page->trashed ? 'checked="checked"': ''; ?>>
            </div>

            <div class="form-group">
                <label for="featured">Featured</label>
                <input type="checkbox" name="featured" id="featured" class="form-control" <?php echo 1 == $Page->featured ? 'checked="checked"': ''; ?>>
            </div>

            <div class="form-group">
                <label for="body">Page Body / Content Map</label>
                <textarea class="form-control wysiwyg" name="body" id="body"><?php echo $Page->File->body; ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Update Page</button>
        </form>
    </div>
</main>

<?php croak($Page);echo $View->render('footer');
