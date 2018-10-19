<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node $Node */
/** @var \Stationer\Pencil\models\Node[] $Nodes */
echo $View->render('header');
?>
    <form class="m-flex" action="/P_Text/edit/<?php echo $Node->node_id; ?>" method="post">
    <section class="l-two-thirds">
        <div class="c-card">
            <div class="header">
              <h5>Edit Text</h5>
            </div>
            <div class="content">
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

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" name="description" id="description"><?php echo $Node->description; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="mimeType">MIME Type</label>
                    <input type="text" name="mimeType" id="mimeType" class="form-control" value="<?php echo $Node->File->mimeType; ?>">
                </div>

                <div class="form-group">
                    <label for="body">Text Body</label>
                    <textarea class="form-control" name="body" id="body"><?php echo $Node->File->body; ?></textarea>
                </div>
            </div>
        </div>
    </section>
    <section class="l-one-third">
        <div class="c-card c-options">
            <div class="header">
                <h5>Options</h5>
            </div>
            <div class="content">
                <?php include 'P_Dashboard._nodeFormOptions.php'; ?>
            </div>
            <div class="footer">
                <div class="buttons">
                    <button class="c-btn" type="submit">Save Text</button>
                </div>
            </div>
        </div>
    </section>
</form>

<?php echo $View->render('footer');
