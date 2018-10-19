<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node[] $Nodes */

echo $View->render('header');
?>
	<section>
		<div class="c-card">
			<div class="header">
				<h5>List Themes</h5>
			</div>
			<div class="content">
				<table>
					<tr>
						<th>Theme</th>
						<th>Last Updated</th>
					</tr>
					<?php foreach ($Nodes as $Node): ?>
						<tr>
							<td>
								<strong>
									<a href="/P_Theme/edit/<?php echo $Node->node_id; ?>">
										<?php echo $Node->label; ?>
									</a>
								</strong>
							</td>
							<td><?php echo $Node->File->updated_dts; ?></td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>
		</div>
	</section>

<?php echo $View->render('footer');
