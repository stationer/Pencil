<?php use Stationer\Graphite\G;

echo $View->render('header'); ?>

<main class="content" style="padding:20px;">
    <h1>List Pages</h1>
    <table class="table table-striped">
        <thead>
        <tr>
            <th><input type="checkbox" name="input[]" /></th>
            <th>Title</th>
            <th>Visibility</th>
            <th>Date</th>
            <th>Quick Options</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($Pages['Public'] as $Page) : ?>
            <tr>
                <td><input type="checkbox" name="input[]" /></td>
                <td>
                    <a href="/P_Blog/edit/<?php echo $Page->node_id; ?>">
                        <?php echo $Page->File->title ?? 'No Title'; ?>
                    </a>
                </td>
                <td><?php echo $Page->published ? 'Public' : 'Private'; ?></td>
                <td><?php echo date('F j, Y g:i a', $Page->created_uts); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?php echo $View->render('footer');
