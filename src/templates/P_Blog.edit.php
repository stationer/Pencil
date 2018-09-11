<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node $Node */
/** @var \Stationer\Pencil\models\Article $Content */

echo $View->render('header');
?>

    <main class="content" style="padding:20px;">
        <h1 class="page-title">Edit Page</h1>

        <form action="/P_Blog/edit/<?php echo $Node->node_id; ?>" method="post">
            <div class="form-group">
                <label for="label">Label</label>
                <input class="form-control" type="text" name="label" id="label" value="<?php echo $Node->label ?? ''; ?>">
            </div>

            <div class="form-group">
                <label for="featured">Featured</label>
                <input class="form-control" type="checkbox" name="featured" id="featured" <?php echo $Node->featured ? 'checked="checked"': ''; ?>>
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
                <label for="title">Title</label>
                <input type="text" name="title" class="form-control" value="<?php echo $Node->File->title; ?>">
            </div>

            <div class="form-group">
                <label for="body"></label>
                <textarea class="wysiwyg" name="body" id="body"><?php echo $Node->File->body; ?></textarea>
            </div>

            <button class="btn btn-primary" type="submit">Add Article</button>
        </form>
    </main>

<?php echo $View->render('footer');
