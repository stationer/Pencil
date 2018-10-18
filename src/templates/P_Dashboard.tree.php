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
			<h1>Full tree</h1>
		</div>
		<div class="content">
			<?php if (!empty($Nodes)) : ?>
				<table>
					<tr>
						<th>Path</th>
						<th>Content Type</th>
						<th>Published</th>
						<th>Trashed</th>
						<th>Featured</th>
						<th>Modified</th>
						<th>Options</th>
					</tr>
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
									<i data-feather="slash">Edit</i>
								<?php endif; ?>
								<a href="/P_Text/add?parentPath=<?= urlencode($Node->path); ?>"><i data-feather="plus-square">Add Text</i></a>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php echo $View->render('footer');
