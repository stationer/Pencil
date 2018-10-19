<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node $Node */
echo $View->render('header');
?>
    <form class="m-flex" action="/P_Dashboard/import" method="post" enctype="multipart/form-data">
        <section>
            <div class="c-card">
                <div class="header">
                    <h5>Import Pencil Data</h5>
                </div>
                <div class="content">
                    <div class="form-group">
                        <label for="upload">Upload</label>
                        <input type="file" class="form-control" name="upload" id="upload">
                    </div>
                    <button type="submit" class="c-btn">Import</button>
                </div>
            </div>
        </section>
    </form>

<?php echo $View->render('footer');
