<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Article[] $Articles */

echo $View->render('header');
?>

    <main class="content" style="padding:20px;">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>List Blog Articles</h1>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Visibility</th>
                        <th>Date</th>
                        <th>Quick Options</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($Articles as $Article) : ?>
                    <tr>
                        <td><input type="checkbox" name="input[]" title="" /></td>
                        <td>
                            <a href="/P_Blog/edit/<?php echo $Article->node_id; ?>">
                                <?php echo $Article->File->title ?? 'No Title'; ?>
                            </a>
                        </td>
                        <td><?php echo $Article->published ? 'Public' : 'Private'; ?></td>
                        <td><?php echo date('F j, Y g:i a', $Article->created_uts); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if(empty($Articles)) : ?>
                    <p>There are currently now articles.  Would you like to <a href="/P_Blog/add">create one</a>?</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php echo $View->render('footer');
