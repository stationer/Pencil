<?php echo $View->render('header'); ?>

    <main class="content" style="padding:20px;">
        <h1 class="page-title">Edit Page</h1>

        <form action="/P_Page/edit/<?php echo $Page->node_id; ?>" method="post">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" class="form-control" value="<?php echo $Content->title; ?>">
            </div>

            <div class="form-group">
                <label for="body">Body</label>
                <textarea name="body" class="form-control" ><?php echo $Content->body; ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Update Page.</button>
        </form>
    </main>

<?php echo $View->render('footer');
