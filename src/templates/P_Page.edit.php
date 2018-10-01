<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node $Page */
/** @var \Stationer\Pencil\models\Template[] $Templates */
/** @var \Stationer\Pencil\models\Node[] $ContentNodes */
/** @var \Stationer\Pencil\models\Node[] $Nodes */
echo $View->render('header');
?>

<main class="content" style="padding:20px;">
    <div class="container">
        <h1 class="page-title">Edit Page</h1>
        <form action="/P_Page/edit/<?php echo $Page->node_id; ?>" method="post">
            <div class="form-group">
                <label for="parentPath">Parent Path</label>
                <select name="parentPath" id="parentPath" class="form-control">
                    <option value="">--- Select a Parent ---</option>
                    <?php foreach ($Nodes as $ParentNode) : ?>
                        <option value="<?php echo $ParentNode->path; ?>" <?php
                        echo $ParentNode->path == dirname($Page->path) ? 'selected' : '';
                        ?>><?php echo $ParentNode->path; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

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
                <label for="label">Node Label</label>
                <input type="text" name="label" id="label" class="form-control" value="<?php echo $Page->label ?? 'No Label'; ?>">
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

            <?php foreach ($ContentNodes as $ContentNode) : ?>
            <div class="form-group">
                <label for="content-<?= $ContentNode->label ?>">[content.<?= $ContentNode->label?>]</label>
                <textarea class="form-control " name="content[<?= $ContentNode->label ?>]" id="content-<?= $ContentNode->label ?>"><?php echo $ContentNode->File->body; ?></textarea>
            </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary">Update Page</button>
        </form>
    </div>
</main>

<?php echo $View->render('footer');
