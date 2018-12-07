<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node $Node */
/** @var string $formHeader */
/** @var string $formAction */

echo $View->render('header');
?>
    <form class="m-flex" action="<?= $formAction ?>" method="POST">
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
                        <textarea class="form-control" name="description" id="description"><?php html($Node->description); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="document">Theme Root Document</label>
                        <textarea class="form-control" name="document" id="document"><?php html($Node->File->document ?: '<!doctype html>
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
</html>'); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="header">Theme Header</label>
                        <textarea class="form-control " name="header" id="header"><?php html($Node->File->header); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="footer">Theme Footer</label>
                        <textarea class="form-control " name="footer" id="footer"><?php html($Node->File->footer); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="css">Theme CSS</label>
                        <textarea class="form-control" name="css" id="css"><?php html($Node->File->css); ?></textarea>
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
