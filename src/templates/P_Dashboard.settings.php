<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node $Node */
/** @var \Stationer\Pencil\models\Theme[] $Themes */
/** @var \Stationer\Pencil\models\Page[] $Pages */
/** @var \Stationer\Pencil\models\Asset[] $Assets */
/** @var \Stationer\Pencil\models\Site $Site */
$Site = $Node->File;

echo $View->render('header'); ?>
    <form class="m-flex" action="<?= $formAction ?>" method="post">
        <section class="l-two-thirds">
            <div class="c-card">
                <div class="header">
                    <h5><?= $formHeader ?></h5>
                </div>
                <div class="content">
                    <?php include 'P_Dashboard._nodeFormElements.php'; ?>

                    <div class="form-group">
                        <label for="title">Site Title</label>
                        <input class="form-control" type="text" id="title" name="title"
                               value="<?php echo $Site->title ?: ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="theme_id">Site Theme</label>
                        <select name="theme_id" id="theme_id" class="form-control">
                            <option disabled selected value="">--- Select a Theme ---</option>
                            <?php foreach ($Themes as $Theme) : ?>
                                <option value="<?php echo $Theme->node_id; ?>" <?php
                                echo $Theme->node_id == $Site->theme_id ? 'selected' : '';
                                ?>><?php echo $Theme->label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="defaultPage_id">Default Page</label>
                        <select name="defaultPage_id" id="defaultPage_id" class="form-control">
                            <option disabled selected value="">--- Select a Page ---</option>
                            <?php foreach ($Pages as $Page) : ?>
                                <option value="<?php echo $Page->node_id; ?>" <?php
                                echo $Page->node_id == $Site->defaultPage_id ? 'selected' : '';
                                ?>><?php echo '"'.$Page->File->title.'" : ('.$Page->path.')'; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="dashLogo_id">Dashboard Logo</label>
                        <select name="dashLogo_id" id="dashLogo_id" class="form-control">
                            <option disabled selected value="">--- Select an Asset ---</option>
                            <?php foreach ($Assets as $Asset) : ?>
                                <option value="<?php echo $Asset->node_id; ?>" <?php
                                echo $Asset->node_id == $Site->dashLogo_id ? 'selected' : '';
                                ?>><?php echo $Asset->path; ?></option>
                            <?php endforeach; ?>
                        </select>
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
                        <button class="c-btn" type="submit">Save Changes</button>
                    </div>
                </div>
            </div>
        </section>
    </form>

<?php echo $View->render('footer');
