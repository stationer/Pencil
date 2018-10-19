<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Asset[] $Assets */
/** @var array $fileList */
echo $View->render('header');
?>
<section>
	<div class="c-card">
		<div class="header">
			<h5>Import Assets</h5>
		</div>
		<div class="content">
			<form action="/P_Asset/import" method="post">

				<table class="table table-striped">
					<thead>
					<tr>
						<th>File Path</th>
						<th>MIME Type</th>
						<th>Options</th>
						<th></th>
					</tr>
					</thead>
					<?php foreach ($fileList as $file): ?>
						<tr>
							<td><?= $file['path'] ?></td>
							<td><?= $file['mimetype'] ?></td>
							<td><?php if (empty($file['Asset'])) :
									?><label><input type="checkbox" name="import[<?= $file['path'] ?>]"
									                title="import <?= $file['path'] ?>"> Import</label><?php endif; ?></td>
							<td><?php if (!empty($file['Nodes'])) : foreach ($file['Nodes'] as $Node) : ?>
									<a href="/P_Asset/edit/<?= $Node->node_id ?>">Edit</a>
								<?php endforeach; endif; ?></td>
						</tr>
					<?php endforeach; ?>
				</table>
				<button class="c-btn" type="submit">Import Checked</button>
			</form>
		</div>
	</div>
</section>

<?php croak($fileList);
echo $View->render('footer');
