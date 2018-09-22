<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node $Node */
echo $View->render('header');
?>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Edit Asset</h1>

                <form action="/P_Asset/edit/<?php echo $Node->node_id; ?>" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="label">Label</label>
                        <input type="text" name="label" id="label" class="form-control"
                               value="<?php echo $Node->label; ?>">
                    </div>

                    <div class="form-group">
                        <label for="keywords">Keywords</label>
                        <input type="text" name="keywords" id="keywords" class="form-control"
                               value="<?php echo $Node->keywords ?? ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" name="description" id="description"><?php echo $Node->description; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="featured">Featured</label>
                        <input type="checkbox" name="featured" id="featured" <?php echo $Node->featured
                            ? 'checked="checked"' : ''; ?>>
                    </div>

                    <div class="form-group">
                        <label for="published">Published</label>
                        <input type="checkbox" name="published" id="published" <?php echo $Node->published
                            ? 'checked="checked"' : ''; ?>>
                    </div>

                    <div class="form-group">
                        <label for="trashed">Trashed</label>
                        <input type="checkbox" name="trashed" id="trashed" <?php echo $Node->trashed
                            ? 'checked="checked"' : ''; ?>>
                    </div>

                    <div class="form-group">
                        <label for="upload">Replace File</label>
                        <input type="file" class="form-control" name="upload" id="upload">
                    </div>

                    <button type="submit" class="btn btn-primary">Update Asset</button>
                </form>
            </div>
        </div>
    </div>


<?php echo $View->render('footer');
