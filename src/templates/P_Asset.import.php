<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Asset[] $Assets */
/** @var array $fileList */
echo $View->render('header');
?>
    <section>
        <div class="c-card">
            <div class="header">
                <div>
                    <h5>Import Assets</h5>
                </div>
                <div class="buttons">
                    <button class="c-btn" type="submit">Import Checked</button>
                </div>
            </div>
            <div class="content">
                <form action="/P_Asset/import" method="post">
                    <table class="js-sort-table">
                        <thead>
                        <tr>
                            <th></th>
                            <th>File Path</th>
                            <th>MIME Type</th>
                            <th class="table-actions">Options</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($fileList as $file): ?>
                            <tr>
                                <td><?php if (empty($file['Asset'])) : ?>
                                        <label><input type="checkbox" name="import[<?= $file['path'] ?>]"
                                                      title="import <?= $file['path'] ?>"> Import</label>
                                    <?php endif; ?>
                                </td>
                                <td><?= $file['path'] ?></td>
                                <td><?= $file['mimetype'] ?></td>
                                <td>
                                    <?php if (!empty($file['Nodes'])) : foreach ($file['Nodes'] as $Node) : ?>
                                        <a href="/P_<?= $Node->contentType; ?>/edit/<?= $Node->node_id; ?>"><i
                                                data-feather="edit">Edit</i></a>
                                    <?php endforeach; endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </section>

<?php //croak($fileList);
echo $View->render('footer');
