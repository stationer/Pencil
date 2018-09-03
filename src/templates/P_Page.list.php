<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Page[] $Pages */

echo $View->render('header');
?>

<main class="content">
    <div class="container">
        <h1>List Pages</h1>
        <table class="table table-striped">
            <thead>
            <tr>
                <th><input type="checkbox" name="input[]" id="input[]" title="" /></th>
                <th>Title</th>
                <th>Visibility</th>
                <th>Date</th>
                <th>Quick Options</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($Pages['Public'] as $Page) : ?>
                <tr>
                    <td><input type="checkbox" name="input[]" id="input[]" title="" /></td>
                    <td>
                        <a href="/P_Page/edit/<?php echo $Page->node_id; ?>">
                            <?php echo $Page->File->title ?: 'No Title'; ?>
                        </a>
                    </td>
                    <td><?php echo $Page->published ? 'Public' : 'Private'; ?></td>
                    <td><?php echo date('F j, Y g:i a', $Page->created_uts); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php if(empty($Pages)) : ?>
            <p>There are currently no pages.  Would you like to <a href="/P_Page/add">create one</a>?</p>
        <?php endif; ?>
    </div>
</main>

<?php echo $View->render('footer');
