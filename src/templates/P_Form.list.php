<?php echo $View->render('header'); ?>

    <main class="content" style="padding:20px;">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1>List Forms</h1>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Author</th>
                            <th>Visibility</th>
                            <th>Date</th>
                            <th>Quick Options</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($Forms as $Form) : ?>
                            <tr>
                                <td><input type="checkbox" name="input[]" /></td>
                                <td>
                                    <a href="/P_Blog/edit/<?php echo $Form->node_id; ?>">
                                        <?php echo $Form->label ?? 'No Title'; ?>
                                    </a>
                                </td>
                                <td><?php echo $Form->published ? 'Public' : 'Private'; ?></td>
                                <td><?php echo date('F j, Y g:i a', $Form->created_uts); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php if(empty($Forms)) : ?>
                        <p>There are currently no forms.  Would you like to <a href="/P_Form/add">create one</a>?</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>


<?php echo $View->render('footer');
