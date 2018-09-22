<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Asset[] $Assets */
echo $View->render('header');
?>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Manage Asset</h1>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Label</th>
                        <th>Preview</th>
                    </tr>
                    </thead>
                    <?php foreach ($Assets as $Asset): ?>
                        <tr>
                            <td>
                                <a href="/P_Asset/edit/<?= $Asset->node_id; ?>"><?= $Asset->label; ?></a>
                            </td>
                            <td><?php if ('image/' == substr($Asset->File->type, 0, 6)) : ?>
                                    <img src="/P_Cache/100x50<?= $Asset->File->path ?>">
                                <?php endif; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>

<?php echo $View->render('footer');
