<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node[] $Nodes */

echo $View->render('header');
?>
<div class="container">
    <h1>List Themes</h1>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>Theme</th>
            <th>Last Updated</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($Nodes as $Node): ?>
        <tr>
            <td>
                <strong>
                    <a href="/P_Theme/edit/<?php echo $Node->node_id; ?>">
                        <?php echo $Node->label; ?>
                    </a>
                </strong>
            </td>
            <td><?php echo $Node->File->updated_dts; ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php echo $View->render('footer');
