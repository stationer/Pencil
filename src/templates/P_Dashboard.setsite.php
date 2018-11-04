<?php
/** @var \Stationer\Graphite\View $View */
/** @var string[] $sites */
/** @var int $treeRoot Root path of current site */
/** @var string $formHeader */
/** @var string $formAction */

echo $View->render('header'); ?>
    <form class="m-flex" action="<?= $formAction ?>" method="post">
        <section class="l-two-thirds">
            <div class="c-card">
                <div class="header">
                    <h5><?= $formHeader ?></h5>
                </div>
                <div class="content">

                    <div class="form-group">
                        <label for="site">Available Sites</label>
                        <select name="site" id="site" class="form-control">
                            <?php foreach ($sites as $site) : ?>
                                <option value="<?php echo $site; ?>" <?php
                                echo $site == $treeRoot ? 'selected' : '';
                                ?>><?php echo $site; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button class="c-btn" type="submit">Change Site</button>
                </div>
            </div>
        </section>
    </form>
<?php echo $View->render('footer');
