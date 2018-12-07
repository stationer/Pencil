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
/** @var string $_siteURL URL, used in header */
/** @var string $_loginname Name of current user */
/** @var int $_login_id ID of current user */
/** @var int $treeRoot Root path of current site */
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
        $_bodyClass = $_controller.(!empty($_action) ? ' '.$_controller.'-'.$_action.' ' : '');
    }
}
if (!isset($_avatarURL)) {
    if (!empty(G::$S->Login->email)) {
        $_avatarURL = 'https://www.gravatar.com/avatar/'.md5(strtolower(trim(G::$S->Login->email))).'?s=90&d=identicon';
    } else {
        $_avatarURL = '/vendor/stationer/pencil/src/images/avatar-1.jpg';
    }
}
if(empty($_logoURL)) {
    $_logoURL = '/vendor/stationer/pencil/src/images/logo.jpg';
}
?><!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php html($_title); ?></title>
    <?php if (!empty($_baseURL)) { ?>
        <base href="<?php html($_baseURL); ?>">
    <?php } ?>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">

    <?php foreach ($_meta as $k => $v) { ?>
        <meta name="<?php html($k) ?>" content="<?php html($v) ?>">
    <?php }
    foreach ($_link as $v) { ?>
        <link rel="<?php html($v['rel']) ?>" type="<?php html($v['type']) ?>" href="<?php html($v['href']) ?>"
              title="<?php html($v['title']) ?>">
    <?php }
    echo $_head;
    ?>

    <!-- Custom styles for this template -->
    <link href="https://fonts.googleapis.com/css?family=Muli:400,900|Roboto:300,400,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.rawgit.com/luizbills/feather-icon-font/v4.7.0/dist/feather.css">

</head>

<body class="<?php echo $_bodyClass; ?>">
<input type="checkbox" id="l-drawer-toggle" name="l-drawer-toggle"/>
<label for="l-drawer-toggle" id="l-drawer-label"><i data-feather="menu"></i></label>
<header class="l-header-right">
    <div>
        <h1>Pencil Dashboard: <?= basename($treeRoot) ?></h1>
    </div>
    <div>
        <a href="/" class="c-btn m-outline">View Site</a>
        <div class="c-dropdown">
            <div role="button" data-toggle="dropdown">
                <div class="c-avatar m-sm"><img src="<?= $_avatarURL ?>"></div>
            </div>
            <ul class="dropdown-menu">
                <?php if (false !== G::$S->Login): ?>
                    <li><a href="/Account/edit">Account Settings</a></li>
                    <li><a href="/Account/logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="/Account/login?_URI=/P_Dashboard">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</header>
<nav class="l-nav-left">
    <?php // TODO Add a setting for which uploaded asset to display here ?>
    <div class="c-brand"><img src="<?= $_logoURL ?>"></div>
    <ul class="c-side-nav">
        <li><a href="#"><i data-feather="home"></i> Dashboard
                <div><i data-feather="chevron-down"></i></div>
            </a>
            <ul>
                <li><a href="/P_Dashboard">Home</a></li>
                <li><a href="/P_Dashboard/settings">Site Settings</a></li>
                <li><a href="/P_Dashboard/setsite">Select Site</a></li>
                <li><a href="/P_Dashboard/tree">Site Tree</a></li>
                <li><a href="/P_Dashboard/import">Import / Export</a></li>
            </ul>
        </li>
        <li><a href="#"><i data-feather="navigation"></i> Navigation
                <div><i data-feather="chevron-down"></i></div>
            </a>
            <ul>
                <li><a href="/P_Navigation/list">All Navigations</a></li>
                <li><a href="/P_Navigation/add">Add New Navigation</a></li>
            </ul>
        </li>
        <li><a href="#"><i data-feather="file"></i> Pages
                <div><i data-feather="chevron-down"></i></div>
            </a>
            <ul class="sub">
                <li><a href="/P_Page/list">All Pages</a></li>
                <li><a href="#">Add New
                        <div><i data-feather="chevron-down"></i></div>
                    </a>
                    <ul>
                        <li><a href="#">From Template</a></li>
                        <li><a href="#">From Component</a></li>
                        <li><a href="/P_Page/add">(advanced)</a></li>
                    </ul>
                </li>
            </ul>
        </li>
        <li><a href="#"><i data-feather="layout"></i> Templates
                <div><i data-feather="chevron-down"></i></div>
            </a>
            <ul>
                <li><a href="/P_Template/list">All Templates</a></li>
                <li><a href="/P_Template/add">Add New Template</a></li>
                <li><a href="/P_Component/list">All Components</a></li>
                <li><a href="/P_Component/add">Add New Component</a></li>
            </ul>
        </li>
        <li><a href="#"><i data-feather="package"></i> Themes
                <div><i data-feather="chevron-down"></i></div>
            </a>
            <ul>
                <li><a href="/P_Theme/list">All Themes</a></li>
                <li><a href="/P_Theme/add">Add New Theme</a></li>
            </ul>
        </li>
        <li><a href="#"><i data-feather="message-square"></i> Blog
                <div><i data-feather="chevron-down"></i></div>
            </a>
            <ul>
                <li><a href="/P_Blog/list">All Articles</a></li>
                <li><a href="/P_Blog/add">Add New Article</a></li>
            </ul>
        </li>
        <li><a href="#"><i data-feather="image"></i> Assets
                <div><i data-feather="chevron-down"></i></div>
            </a>
            <ul>
                <li><a href="/P_Asset/list">All Assets</a></li>
                <li><a href="/P_Asset/add">Add New Asset</a></li>
                <li><a href="/P_Asset/import">Import Assets</a></li>
            </ul>
        </li>
        <li><a href="#"><i data-feather="mail"></i> Forms
                <div><i data-feather="chevron-down"></i></div>
            </a>
            <ul>
                <li><a href="/P_Form/list">All Forms</a></li>
                <li><a href="/P_Form/add">Add New Form</a></li>
                <li><a href="/P_Form/submissions">Submissions</a></li>
            </ul>
        </li>
    </ul>
</nav>
<main class="l-main-left">
    <div class="container-fluid">
        <div class="row">
            <?php if (!empty(G::msg())): ?>
                <section class="messages">
                    <?php if (0 < $v = count($a = G::msg())) { ?>
                        <details id="msg" open="open">
                            <summary><?php echo $v; ?> Messages:</summary>
                            <ul>
                                <?php foreach ($a as $v) { ?>
                                    <li class="<?php echo $v[1]; ?>"><?php echo $v[0]; ?></li>
                                <?php } ?>
                            </ul>
                        </details>
                    <?php } ?>
                </section>
            <?php endif; ?>
