<?php

use const \Stationer\Graphite\DATE_HUMAN;
use const \Stationer\Graphite\TIME_HUMAN;

/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node[] $Nodes */
/** @var string $root Tree root path */

echo $View->render('header');
?>
<section>
	<div class="c-card">
		<div class="header">
			<h5>Full tree</h5>
		</div>
		<div class="content">
			<?php if (!empty($Nodes)) : ?>
				<table>
                    <thead>
                    <tr>
                        <th class="sort" data-sort="path">Path</th>
                        <th class="sort" data-sort="content">Content Type</th>
                        <th class="sort" data-sort="published">Published</th>
                        <th class="sort" data-sort="trashed">Trashed</th>
                        <th class="sort" data-sort="featured">Featured</th>
                        <th class="sort" data-sort="modified">Modified</th>
                        <th class="table-actions">Options</th>
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
                                    <a href="/P_<?= $Node->contentType; ?>/edit/<?= $Node->node_id; ?>"><i data-feather="edit">Edit</i></a>
                                <?php else: ?>
                                    <i class="danger" data-feather="slash">Edit</i>
                                <?php endif; ?>
                                <a href="/P_Text/add?parentPath=<?= urlencode($Node->path); ?>"><i data-feather="plus-square">Add Text</i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
				</table>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php echo $View->render('footer');
