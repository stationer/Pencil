<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node $Node */

$types = [
    0 => 'Header',
    1 => 'Footer',
    2 => 'Page',
    3 => 'Post'
];
echo $View->render('header');
?>
<div class="container">
    <h1>Edit Template</h1>

    <form action="/P_Template/edit/<?php echo $Node->node_id; ?>" method="POST">
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
                <?php foreach ($types as $key=> $type) : ?>
                    <option value="<?php echo $key; ?>" <?php echo $key == $Node->File->type ? 'selected="selected"': ''; ?>>
                        <?php echo $type; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button class="btn btn-primary" type="submit">Update Template</button>
    </form>
</div>


<?php echo $View->render('footer');
