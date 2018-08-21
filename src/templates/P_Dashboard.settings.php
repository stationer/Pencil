<?php use Stationer\Graphite\G;
echo $View->render('header'); ?>

    <h1>Site Settings</h1>

<?php
\croak($SiteNode->toArray());
\croak($Site->toArray());
\croak(array_keys($Themes));
\croak(array_keys($Pages));
?>
<?php echo $View->render('footer');
