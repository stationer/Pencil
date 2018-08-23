<?php use Stationer\Graphite\G;
echo $View->render('header'); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Site Settings</h1>

                <?php
                \croak($SiteNode->toArray());
                \croak($Site->toArray());
                \croak(array_keys($Themes));
                \croak(array_keys($Pages));
                ?>
            </div>
        </div>
    </div>

<?php echo $View->render('footer');
