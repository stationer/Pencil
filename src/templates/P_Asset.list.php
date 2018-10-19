<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Asset[] $Assets */
/** @var string $treeRoot */

echo $View->render('header');
?>
<section>
	<div class="c-card">
		<div class="header">
			<h5>Manage Asset</h5>
		</div>
		<div class="content">
			<table class="table table-striped">
				<tr>
					<th>Label</th>
					<th>MIME Type</th>
					<th>Preview</th>
				</tr>
				<?php foreach ($Assets as $Asset): ?>
					<tr>
						<td>
							<a href="/P_Asset/edit/<?= $Asset->node_id; ?>"><?= substr($Asset->path, strlen($treeRoot)); ?></a>
						</td>
						<td><?= $Asset->File->type ?></td>
						<td><?php if ('image/' == substr($Asset->File->type, 0, 6)) : ?>
								<img src="/P_Cache/200x100<?= $Asset->File->path ?>"
								     style="max-height:50px;max-width:100px;">
							<?php endif; ?></td>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>
	</div>
</section>

<?php echo $View->render('footer');
