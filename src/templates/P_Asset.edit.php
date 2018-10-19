<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node $Node */
/** @var string $formHeader */
/** @var string $formAction */
echo $View->render('header');
?>
	<form class="m-flex" action="<?= $formAction ?>" method="post" enctype="multipart/form-data">
		<section class="l-two-thirds">
			<div class="c-card">
				<div class="header">
                    <h5><?= $formHeader ?></h5>
				</div>
				<div class="content">
					<div class="form-group">
						<label for="label">Label</label>
						<input type="text" name="label" id="label" class="form-control"
						       value="<?php echo $Node->label; ?>">
					</div>

					<div class="form-group">
						<label for="keywords">Keywords</label>
						<input type="text" name="keywords" id="keywords" class="form-control"
						       value="<?php echo $Node->keywords ?? ''; ?>">
					</div>

					<div class="form-group">
						<label for="description">Description</label>
						<textarea class="form-control" name="description" id="description"><?php echo $Node->description; ?></textarea>
					</div>

					<div class="form-group">
                        <label for="Media File">Media File</label>
                        <label for="upload" class="c-upload">Replace File</label>
						<input type="file" class="form-control" name="upload" id="upload">
					</div>
				</div>
			</div>
		</section>
		<section class="l-one-third">
			<div class="c-card c-options">
				<div class="header">
					<h5>Options</h5>
				</div>
				<div class="content">
					<?php include 'P_Dashboard._nodeFormOptions.php'; ?>
				</div>
				<div class="footer">
					<div class="buttons">
						<button type="submit" class="c-btn">Save</button>
					</div>
				</div>
			</div>
		</section>
	</form>

<?php echo $View->render('footer');
