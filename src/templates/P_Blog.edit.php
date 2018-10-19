<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node $Node */
/** @var \Stationer\Pencil\models\Article $Content */
/** @var string $formHeader */
/** @var string $formAction */
echo $View->render('header');
?>
	<form class="m-flex" action="<?= $formAction ?>" method="post">
		<section class="l-two-thirds">
			<div class="c-card">
				<div class="header">
                    <h5><?= $formHeader ?></h5>
				</div>
				<div class="content">
					<div class="form-group">
						<label for="label">Label</label>
						<input class="form-control" type="text" name="label" id="label" value="<?php echo $Node->label ?? ''; ?>">
					</div>

					<div class="form-group">
						<label for="description">Description</label>
						<textarea class="form-control" name="description" id="description"><?php echo $Node->description; ?></textarea>
					</div>

					<div class="form-group">
						<label for="title">Title</label>
						<input type="text" name="title" id="title" class="form-control" value="<?php echo $Node->File->title; ?>">
					</div>

					<div class="form-group">
						<label for="body"></label>
						<textarea class="wysiwyg" name="body" id="body"><?php echo $Node->File->body; ?></textarea>
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
						<button class="c-btn" type="submit">Save</button>
					</div>
				</div>
			</div>
		</section>
	</form>

<?php echo $View->render('footer');
