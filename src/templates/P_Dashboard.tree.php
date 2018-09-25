<?php

use const \Stationer\Graphite\DATE_HUMAN;
use const \Stationer\Graphite\TIME_HUMAN;

/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node[] $Nodes */
/** @var string $root Tree root path */

echo $View->render('header');
?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Full tree</h1>

                <?php if (!empty($Nodes)) : ?>
                    <table>
                        <thead>
                        <tr>
                            <th>Path</th>
                            <th>Content Type</th>
                            <th>Published</th>
                            <th>Trashed</th>
                            <th>Featured</th>
                            <th>Modified</th>
                            <th>Options</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($Nodes as $Node) : ?>
                            <tr>
                                <td><?= substr($Node->path, strlen($root)) ?: '/'; ?></td>
                                <td><?= $Node->contentType; ?></td>
                                <td><?= $Node->published ? 'Y' : 'N'; ?></td>
                                <td><?= $Node->trashed ? 'Y' : 'N'; ?></td>
                                <td><?= $Node->featured ? 'Y' : 'N'; ?></td>
                                <td><?= date(DATE_HUMAN.' '.TIME_HUMAN, strtotime($Node->updated_dts)); ?></td>
                                <td>
                                    <?php if ('' != $Node->contentType): ?>
                                        <a href="/P_<?= $Node->contentType; ?>/edit/<?= $Node->node_id; ?>">Edit</a>
                                    <?php else: ?>
                                        <s>Edit</s>
                                    <?php endif; ?>
                                    <a href="/P_Text/add?parentPath=<?= urlencode($Node->path); ?>">Add Text</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php echo $View->render('footer');
