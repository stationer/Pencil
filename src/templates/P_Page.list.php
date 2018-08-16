<?php use Stationer\Graphite\G;
echo $View->render('header'); ?>

    <h1>List Pages</h1>

<?php G::croak($View); ?>

<?php echo $View->render('footer');
