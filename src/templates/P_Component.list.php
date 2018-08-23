<?php echo $View->render('header'); ?>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>List Components</h1>

                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Component</th>
                        <th>Last Updated</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($Components as $Component) : ?>
                        <tr>
                            <td>
                                <strong>
                                    <a href="/P_Component/edit/<?php echo $Component->node_id; ?>">
                                        <?php echo $Component->label; ?>
                                    </a>
                                </strong>
                            </td>
                            <td><?php echo $Component->File->updated_dts; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php echo $View->render('footer');
