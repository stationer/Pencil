<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Text[] $Texts */
/** @var string $treeRoot */

echo $View->render('header');
?>
    <section>
        <div class="c-card">
            <div class="header">
                <h5>List Text Nodes</h5>
            </div>
            <div class="content">
                <table>
                    <tr>
                        <th></th>
                        <th>Path</th>
                        <th>MIME Type</th>
                        <th>Preview</th>
                    </tr>
                    <?php foreach ($Texts as $Text) : ?>
                        <tr>
                            <td><input type="checkbox" name="input[]" title=""/></td>
                            <td>
                                <a href="/P_Text/edit/<?php echo $Text->node_id; ?>"><?= substr($Text->path, strlen($treeRoot)) ?></a>
                            </td>
                            <td><?= $Text->File->mimeType ?></td>
                            <td><?= html(substr($Text->File->body, 0, 50)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </section>

<?php echo $View->render('footer');
