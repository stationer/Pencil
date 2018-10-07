<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node $Node */
/** @var \Stationer\Pencil\models\Theme $Theme */
$Theme = $Node->File;
echo $View->render('header');
?>
<div class="container">
    <h1>Add Theme</h1>

    <form action="/P_Theme/add/" method="POST">

        <div class="form-group">
            <label for="label">Label</label>
            <input class="form-control" type="text" name="label" id="label" value="<?php echo $Node->label ?? ''; ?>">
        </div>

        <div class="form-group">
            <label for="published">Published</label>
            <input class="form-control" type="checkbox" name="published" id="published" <?php echo $Node->published ? 'checked="checked"':''; ?>>
        </div>

        <div class="form-group">
            <label for="trashed">Trashed</label>
            <input class="form-control" type="checkbox" name="trashed" id="trashed" <?php echo $Node->trashed ? 'checked="checked"':''; ?>>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" name="description" id="description"><?php echo $Node->description; ?></textarea>
        </div>

        <div class="form-group">
            <label for="document">Theme Root Document</label>
            <textarea class="form-control" name="document" id="document"><?php echo $Theme->document ?: '<!doctype html>
<html lang="en-US">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>[page.title] - [site.title]</title>
  <link rel="shortcut icon" href="/favicon.ico">
  <link rel="icon" href="/favicon.ico">
  <link rel="stylesheet" type="text/css" href="[theme.css_url]">
</head>
<body class="[page.bodyClass]">
[theme.header]
[page.template]
[theme.footer]
</body>
</html>'; ?></textarea>
        </div>

        <div class="form-group">
            <label for="header">Theme Header</label>
            <textarea class="form-control wysiwyg" name="header" id="header"><?php echo $Theme->header; ?></textarea>
        </div>

        <div class="form-group">
            <label for="footer">Theme Footer</label>
            <textarea class="form-control wysiwyg" name="footer" id="footer"><?php echo $Theme->footer; ?></textarea>
        </div>

        <div class="form-group">
            <label for="css">Theme CSS</label>
            <textarea class="form-control" name="css" id="css"><?php echo $Theme->css; ?></textarea>
        </div>

        <button class="btn btn-primary" type="submit">Add Theme</button>
    </form>
</div>

<?php echo $View->render('footer');
