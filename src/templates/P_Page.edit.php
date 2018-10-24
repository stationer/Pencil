<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node $Node */
/** @var \Stationer\Pencil\models\Template[] $Templates */
/** @var \Stationer\Pencil\models\Node[] $ContentNodes */
/** @var \Stationer\Pencil\models\Node[] $Nodes */
/** @var string $formHeader */
/** @var string $formAction */

echo $View->render('header');
?>

	<form class="m-flex" action="<?= $formAction ?>" method="post">
		<section class="l-two-thirds">
			<div class="c-card">
				<div class="header">
					<div>
						<h5><?= $formHeader ?></h5>
					</div>
				</div>
				<div class="content">
					<?php include 'P_Dashboard._nodeFormElements.php'; ?>
					<div class="form-group">
						<label for="title">Title</label>
						<input type="text" name="title" id="title" class="form-control" value="<?php echo $Node->File->title ?: 'No Title'; ?>">
					</div>

					<div class="form-group">
						<label for="template_id">Template</label>
						<select name="template_id" id="template_id" class="form-control">
							<option value="">--- Select a Template ---</option>
							<?php foreach ($Templates as $Template) : ?>
								<option value="<?php echo $Template->node_id; ?>" <?php
								echo $Template->node_id == $Node->File->template_id ? 'selected' : '';
								?>><?php echo $Template->path; ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<?php if (!empty($ContentNodes)) foreach ($ContentNodes as $ContentNode) : ?>
						<div class="form-group">
							<label for="content-<?= $ContentNode->label ?>">[content.<?= $ContentNode->label ?>]</label>
							<textarea class="form-control " name="content[<?= $ContentNode->label ?>]" id="content-<?= $ContentNode->label ?>"><?php echo $ContentNode->File->body; ?></textarea>
						</div>
					<?php endforeach; ?>
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
						<button type="submit" class="c-btn">Update Page</button>
					</div>
				</div>
			</div>
		</section>
	</form>

<?php echo $View->render('footer');
