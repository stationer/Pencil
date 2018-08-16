<?php echo $View->render('header'); ?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>List Templates</h1>

            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Template</th>
                    <th>Last Updated</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($Templates as $Template) : ?>
                <tr>
                    <td>
                        <strong>
                            <a href="/P_Template/edit/<?php echo $Template->node_id; ?>">
                                <?php echo $Template->label; ?>
                            </a>
                        </strong>
                    </td>
                    <td><?php echo $Template->File->updated_dts; ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php echo $View->render('footer');
