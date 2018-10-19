<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Form[] $Forms */
echo $View->render('header');
?>
<section>
	<div class="c-card">
		<div class="header">
			<h5>List Forms</h5>
		</div>
		<div class="content">
			<table>
				<tr>
					<th>Name</th>
					<th>Author</th>
					<th>Visibility</th>
					<th>Date</th>
					<th>Quick Options</th>
				</tr>
				<?php foreach($Forms as $Form) : ?>
					<tr>
						<td><input type="checkbox" name="input[]" title="" /></td>
						<td>
							<a href="/P_Blog/edit/<?php echo $Form->node_id; ?>">
								<?php echo $Form->label ?? 'No Title'; ?>
							</a>
						</td>
						<td><?php echo $Form->published ? 'Public' : 'Private'; ?></td>
						<td><?php echo date('F j, Y g:i a', $Form->created_uts); ?></td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php if(empty($Forms)) : ?>
				<p>There are currently no forms.  Would you like to <a href="/P_Form/add">create one</a>?</p>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php echo $View->render('footer');
