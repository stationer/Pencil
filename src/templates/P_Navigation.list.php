<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Navigation[] $Navigations */
echo $View->render('header');
?>
	<section>
		<div class="c-card">
			<div class="header">
				<h5>Manage Navigation</h5>
			</div>
			<div class="content">
				<table>
                    <thead>
                    <tr>
                        <th class="sort" data-sort="label">Label</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($Navigations)): ?>
                        <?php foreach ($Navigations as $Nav): ?>
                            <tr>
                                <td>
                                    <a href="/P_Navigation/edit/<?php echo $Nav->node_id; ?>">
                                        <?php echo $Nav->label; ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
				</table>
			</div>
		</div>
	</section>

<?php echo $View->render('footer');
