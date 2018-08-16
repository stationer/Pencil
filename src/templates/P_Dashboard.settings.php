<?php use Stationer\Graphite\G;
echo $View->render('header'); ?>

    <h1>Site Settings</h1>

<?php
G::croak($SiteNode->toArray());
G::croak($Site->toArray());
G::croak(array_keys($Themes));
G::croak(array_keys($Pages));
?>
<?php echo $View->render('footer');
