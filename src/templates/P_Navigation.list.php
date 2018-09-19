<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Navigation[] $Navigations */
echo $View->render('header');
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Manage Navigation</h1>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Label</th>
                </tr>
                </thead>
            <?php foreach($Navigations as $Nav): ?>
                <tr>
                    <td>
                        <a href="/P_Navigation/edit/<?php echo $Nav->node_id; ?>">
                            <?php echo $Nav->label; ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>

<?php echo $View->render('footer');
