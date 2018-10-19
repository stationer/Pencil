<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Page[] $Pages */
/** @var string $treeRoot */

echo $View->render('header');
?>
<section>
	<div class="c-card">
		<div class="header">
			<div>
				<ul class="tabs">
					<li class="active">
						<a href="">All Pages</a>
					</li>
					<li>
						<a href="">Published Pages</a>
					</li>
				</ul>
			</div>
			<div class="buttons">
				<button type="button" class="c-btn">
					Add New
				</button>
			</div>
		</div>
		<div class="content">
			<form class="c-search" action="">
				<div class="l-filters">
					<input type="checkbox" title=""/>
					<select name="" id="" title="">
						<option value="">Bulk Actions</option>
					</select>
				</div>
				<div class="l-search">
					<input type="text" placeholder="Search">
				</div>
			</form>
			<table>
				<tr>
					<th></th>
					<th>Title</th>
					<th>Path</th>
					<th>Visibility</th>
					<th>Date</th>
					<th>Quick Options</th>
				</tr>
				<?php foreach($Pages as $Page) : ?>
					<tr>
						<td><input type="checkbox" name="input[]" id="input[]" title="" /></td>
						<td>
							<a href="/P_Page/edit/<?php echo $Page->node_id; ?>">
								<?php echo $Page->File->title ?: 'No Title'; ?>
							</a>
						</td>
						<td><?= substr($Page->path, strlen($treeRoot)) ?></td>
						<td class="visibility">
							<span><?php echo $Page->published ? 'Public' : 'Private'; ?></span>
						</td>
						<td><?php echo date('m-d-Y', $Page->created_uts); ?></td>
						<td>
							<a href="">
								<i data-feather="edit"></i>
							</a>
							<a href="">
								<i data-feather="eye"></i>
							</a>
							<a href="">
								<i data-feather="trash"></i>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php if(empty($Pages)) : ?>
				<p>There are currently no pages.  Would you like to <a href="/P_Page/add">create one</a>?</p>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php echo $View->render('footer');
