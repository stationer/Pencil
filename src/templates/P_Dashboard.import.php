<?php
/** @var \Stationer\Graphite\View $View */
/** @var \Stationer\Pencil\models\Node $Node */
echo $View->render('header');
?>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Import Pencil Data</h1>

                <form action="/P_Dashboard/import" method="post" enctype="multipart/form-data">

                    <div class="form-group">
                        <label for="upload">Upload</label>
                        <input type="file" class="form-control" name="upload" id="upload">
                    </div>

                    <button type="submit" class="btn btn-primary">Import</button>
                </form>
            </div>
        </div>
    </div>


<?php echo $View->render('footer');
