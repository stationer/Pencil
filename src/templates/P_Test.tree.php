<?php echo $View->render('header'); ?>

    <h1>Test Tree</h1>

    <table>
<?php foreach ($results as $result): ?>
        <tr><th><?=$result[0]?></th>
            <td><pre><?=ob_var_dump($result[1])?></pre></td>
        </tr>
<?php endforeach; ?>
    </table>
<?php echo $View->render('footer');
