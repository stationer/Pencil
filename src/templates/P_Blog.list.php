<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Article[] $Articles */

echo $View->render('header');
?>
    <section>
        <div class="c-card">
            <div class="header">
                <h5>List Blog Articles</h5>
            </div>
            <div class="content">
                <table class="js-sort-table">
                    <thead>
                    <tr>
                        <th></th>
                        <th class="sortable">Title</th>
                        <th class="sortable">Author</th>
                        <th class="sortable">Visibility</th>
                        <th class="sortable js-sort-date">Created</th>
                        <th class="sortable js-sort-date">Published</th>
                        <th class="table-actions">Quick Options</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($Articles as $Article) : ?>
                        <tr>
                            <td><input type="checkbox" name="input[]" title=""/></td>
                            <td>
                                <a href="/P_Blog/edit/<?php echo $Article->node_id; ?>">
                                    <?php
                                    if ('' != $Article->File->title) {
                                        echo $Article->File->title;
                                    } else {
                                        echo 'No Title';
                                    }
                                    ?>
                                </a>
                            </td>
                            <td><?php echo $Article->File->author_id; ?></td>
                            <td><?php echo $Article->published ? 'Public' : 'Private'; ?></td>
                            <td><?php echo date('F j, Y g:i a', $Article->created_uts); ?></td>
                            <td><?php echo date('F j, Y g:i a', $Article->File->release_uts); ?></td>
                            <td></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (empty($Articles)) : ?>
                    <p>There are currently now articles. Would you like to <a href="/P_Blog/add">create one</a>?</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

<?php echo $View->render('footer');
