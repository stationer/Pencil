<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Template[] $Templates */
echo $View->render('header');
?>
    <section>
        <div class="c-card">
            <div class="header">
                <h5>List Templates</h5>
            </div>
            <div class="content">
                <table class="js-sort-table">
                    <thead>
                    <tr>
                        <th class="sortable">Template</th>
                        <th class="sortable js-sort-date">Last Updated</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($Templates as $Template) : ?>
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
    </section>

<?php echo $View->render('footer');
