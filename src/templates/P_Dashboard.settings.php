<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node $SiteNode */
/** @var \Stationer\Pencil\models\Theme[] $Themes */
/** @var \Stationer\Pencil\models\Page[] $Pages */
/** @var \Stationer\Pencil\models\Site $Site */
$Site = $SiteNode->File;

echo $View->render('header'); ?>
<section class="l-two-thirds">
    <div class="c-card">
        <div class="header">
            <div>
                <h5>Site Settings</h5>
            </div>
        </div>
        <div class="content">
            <form action="/P_Dashboard/settings" method="post">
                <div class="form-container">
                <div class="form-group">
                    <label for="title">Site Title</label>
                    <input class="form-control" type="text" id="title" name="title" value="<?php echo $Site->title ?: ''; ?>">
                </div>

                <div class="form-group">
                    <label for="theme_id">Site Theme</label>
                    <select name="theme_id" id="theme_id" class="form-control">
                        <option disabled selected value="">--- Select a Theme ---</option>
                        <?php foreach($Themes as $Theme) : ?>
                            <option value="<?php echo $Theme->node_id; ?>" <?php
                            echo $Theme->node_id == $Site->theme_id ? 'selected':'';
                            ?>><?php echo $Theme->label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="defaultPage_id">Default Page</label>
                    <select name="defaultPage_id" id="defaultPage_id" class="form-control">
                        <option disabled selected value="">--- Select a Page ---</option>
                        <?php foreach($Pages as $Page) : ?>
                            <option value="<?php echo $Page->node_id; ?>" <?php
                            echo $Page->node_id == $Site->defaultPage_id ? 'selected':'';
                            ?>><?php echo '"'.$Page->File->title.'" : ('.$Page->path.')'; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button class="c-btn" type="submit">Update Site Settings</button>
            </form>
        </div>
    </div>
</section>
<?php echo $View->render('footer');
