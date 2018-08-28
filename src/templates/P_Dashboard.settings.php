<?php use Stationer\Graphite\G;
echo $View->render('header'); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Site Settings</h1>

                <form action="/P_Dashboard/settings" method="post">

                    <div class="form-group">
                        <label for="theme">Site Theme</label>
                        <select name="theme_id" class="form-control">
                            <?php foreach($Themes as $Theme) : ?>
                                <option value="<?php echo $Theme->node_id; ?>" <?php echo $Theme->node_id == $Site->theme_id ? 'selected':''; ?>><?php echo $Theme->label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="defaultPage_id">Default Page</label>
                        <select name="defaultPage_id" class="form-control">
                            <?php foreach($Pages as $Page) : ?>
                                <option value="<?php echo $Page->node_id; ?>"><?php echo $Page->label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button class="btn btn-primary" type="submit">Update Theme.</button>
                </form>
            </div>
        </div>
    </div>

<?php echo $View->render('footer');
