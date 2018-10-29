<div class="form-group">
    <label>Visibility</label>
	<div class="c-radio-btn">
		<div class="form-check">
			<label class="form-check-label" for="featured"><input type="checkbox" name="featured" id="featured" class="form-control" <?php echo $Node->featured ? 'checked="checked"' : ''; ?>>Featured</label>
		</div>
		<div class="form-check">
			<label class="form-check-label" for="published"><input type="checkbox" name="published" id="published" class="form-control" <?php echo $Node->published ? 'checked="checked"' : ''; ?>>Published</label>
		</div>
		<div class="form-check">
			<label class="form-check-label" for="trashed"><input type="checkbox" name="trashed" id="trashed" class="form-control" <?php echo $Node->trashed ? 'checked="checked"' : ''; ?>>Trashed</label>
		</div>
	</div>
</div>