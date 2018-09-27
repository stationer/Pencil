<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Text[] $Texts */
/** @var string $treeRoot */

echo $View->render('header');
?>

    <main class="content" style="padding:20px;">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1>List Text Nodes</h1>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Path</th>
                            <th>MIME Type</th>
                            <th>Preview</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($Texts as $Text) : ?>
                            <tr>
                                <td><input type="checkbox" name="input[]" title=""/></td>
                                <td><a href="/P_Text/edit/<?php echo $Text->node_id; ?>"><?= substr($Text->path, strlen($treeRoot)) ?></a></td>
                                <td><?= $Text->File->mimeType ?></td>
                                <td><?= html(substr($Text->File->body, 0, 50));?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

<?php echo $View->render('footer');
