<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Template[] $Components */
echo $View->render('header');
?>
    <section>
        <div class="c-card">
            <div class="header">
                <h5>List Components</h5>
            </div>
            <div class="content">
                <table class="js-sort-table">
                    <thead>
                    <tr>
                        <th class="sortable">Component</th>
                        <th class="sortable js-sort-date">Last Updated</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($Components as $Component) : ?>
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
    </section>

<?php echo $View->render('footer');
