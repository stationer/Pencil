<?php
/** @var \Stationer\Pencil\models\Node $Node */
/** @var \Stationer\Pencil\models\Node[] $Nodes */
?>
<?php if (!empty($Nodes)) : ?>
    <div class="form-group">
        <label for="parentPath">Parent Path</label>
        <select name="parentPath" id="parentPath" class="form-control">
            <option value="">--- Select a Parent ---</option>
            <?php foreach ($Nodes as $ParentNode) : ?>
                <option value="<?php echo $ParentNode->path; ?>" <?php
                echo $ParentNode->path == dirname($Node->path) ? 'selected' : '';
                ?>><?php echo $ParentNode->path; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
<?php endif; ?>

<div class="form-group">
    <label for="label">Node Label</label>
    <input type="text" name="label" id="label" class="form-control" value="<?php echo $Node->label ?? 'No Label'; ?>">
</div>

<div class="form-group">
    <label for="keywords">Keywords</label>
    <input type="text" name="keywords" id="keywords" class="form-control" placeholder="keywords" value="<?php echo $Node->keywords ?? ''; ?>">
</div>

<div class="form-group">
    <label for="description">Description</label>
    <textarea name="description" id="description" class="form-control" placeholder="description"><?php echo $Node->description; ?></textarea>
</div>

                    <div class="form-group">
                        <label for="featured">Featured</label>
                        <input type="checkbox" name="featured" id="featured" class="form-control" <?php echo $Node->featured ? 'checked="checked"': ''; ?>>
                    </div>

                    <div class="form-group">
                        <label for="published">Published</label>
                        <input type="checkbox" name="published" id="published" class="form-control" <?php echo $Node->published ? 'checked="checked"': ''; ?>>
                    </div>

                    <div class="form-group">
                        <label for="trashed">Trashed</label>
                        <input type="checkbox" name="trashed" id="trashed" class="form-control" <?php echo $Node->trashed ? 'checked="checked"': ''; ?>>
                    </div>
