<?php
use Stationer\Graphite\G;
/** @var string $_title Site title, for title bar */
/** @var array $_meta List of meta tag values to include */
/** @var array $_script List of scripts to include */
/** @var array $_link List of linked resources, such as CSS to include */
/** @var string $_controller Name of current controller */
/** @var string $_action Name of current controller action */
/** @var string $_loginURL URL of Login page */
/** @var string $_logoutURL URL of Logout page */
/** @var string $_siteName Name of site, used in header */
/** @var string $_loginname Name of current user */
/** @var int $_login_id ID of current user */

if (!isset($_controller)) {
    $_controller = '';
}
if (!isset($_action)) {
    $_action = '';
}
if (!isset($_title)) {
    $_title = '';
}
if (!isset($_head)) {
    $_head = '';
}
if (!isset($_bodyClass)) {
    if (!empty($_controller)) {
        $_bodyClass = $_controller . (!empty($_action) ? ' '.$_controller.'-'.$_action.' ' : '');
    }
}

?><!DOCTYPE HTML>
<html>
<head>
    <title><?php html($_title); ?></title>
    <base href="<?php html($_siteURL); ?>">
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <?php foreach ($_meta as $k => $v) { ?>
        <meta name="<?php html($k) ?>" content="<?php html($v) ?>">
    <?php }
    foreach ($_script as $v) { ?>
        <script type="text/javascript" src="<?php html($v) ?>"></script>
    <?php }
    foreach ($_link as $v) { ?>
        <link rel="<?php html($v['rel']) ?>" type="<?php html($v['type']) ?>" href="<?php html($v['href']) ?>"
              title="<?php html($v['title']) ?>">
    <?php }
    echo $_head;
    ?>
</head>
<body class="<?php echo $_bodyClass; ?>">
<header id="header">
    <h1 id="logo"><span><?php html($_siteName) ?></span></h1>
    <div id="login"><?php
        if ($_login_id) {
            echo 'Hello, '.$_loginname
                .'. (<a href="'.$_logoutURL.'">Logout</a> | '
                .'<a href="/Account/edit" title="Your Account Settings">Account</a>)';
        } else {
            echo '(<a id="_loginLink" href="'.$_loginURL.'?_Lbl=Back&amp;_URI='.urlencode($_SERVER["REQUEST_URI"]).'">Login</a>)'
                .'<script type="text/javascript">document.getElementById(\'_loginLink\').href += encodeURIComponent(location.hash);</script>';
        }
        ?></div>
    <nav>
        <a href="/P_Dashboard" title="Pencil Dashboard">Dashboard</a>
    </nav>
    <div class="clear"></div>
</header>

<?php echo $View->render('subheader');

if (0 < $v = count($a = G::msg())) { ?>
    <details id="msg" open="open">
        <summary><?php echo $v; ?> Messages:</summary>
        <ul>
            <?php foreach ($a as $v) { ?>
                <li class="<?php echo $v[1]; ?>"><?php echo $v[0]; ?></li>
            <?php } ?>
        </ul>
    </details>
<?php } ?>

<section id="body" class="container-full">
