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
if (!isset($_avatarURL)) {
    if (!empty(G::$S->Login->email)) {
        $_avatarURL = 'https://www.gravatar.com/avatar/'.md5(strtolower(trim(G::$S->Login->email))).'?s=90&d=identicon';
    } else {
        $_avatarURL = '/vendor/stationer/pencil/src/images/avatar-1.jpg';
    }
}
?><!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php html($_title); ?></title>
    <base href="<?php html($_siteURL); ?>">
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

<div id="wrapper" class="toggled">

    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <a class="sidebar-brand" href="#">
            <img src="/vendor/stationer/pencil/src/images/logo.jpg">
        </a>
        <ul class="sidebar-nav">
            <li class="has-subnav-items link-item">
                <a data-toggle="collapse" href="#subnav-menu-dashboard" role="button" aria-expanded="false" aria-controls="collapseExample">
                    <i data-feather="home"></i>Dashboard
                </a>
                <div class="collapse" id="subnav-menu-dashboard">
                    <ul class="subnav list-unstyled">
                        <li class="subnav-link-item">
                            <a href="/P_Dashboard">Home</a>
                        </li>
                        <li class="subnav-link-item">
                            <a href="/P_Dashboard/settings">Site Settings</a>
                        </li>
                        <li class="subnav-link-item">
                            <a href="/P_Dashboard/tree">Site Tree</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="has-subnav-items link-item">
                <a data-toggle="collapse" href="#subnav-menu-navigation" role="button" aria-expanded="false"
                   aria-controls="collapseExample">
                    <i data-feather="navigation"></i>Navigation
                </a>
                <div class="collapse" id="subnav-menu-navigation">
                    <ul class="subnav list-unstyled">
                        <li class="subnav-link-item">
                            <a href="/P_Navigation/list">All Navigations</a>
                        </li>
                        <li class="subnav-link-item">
                            <a href="/P_Navigation/add">Add New Navigation</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="has-subnav-items link-item">
                <a data-toggle="collapse" href="#subnav-menu-pages" role="button" aria-expanded="false" aria-controls="collapseExample">
                    <i data-feather="file"></i>Pages
                </a>
                <div class="collapse" id="subnav-menu-pages">
                    <ul class="subnav list-unstyled">
                        <li class="subnav-link-item">
                            <a href="/P_Page/list">All Pages</a>
                        </li>
                        <li class="subnav-link-item">

                            <a data-toggle="collapse" href="#subnav-menu-addpages" role="button" aria-expanded="false" aria-controls="collapseExample">
                                Add New Page
                            </a>

                            <div class="collapse" id="subnav-menu-addpages">
                                <ul class="subnav list-unstyled">
                                    <li class="subnav-link-item">
                                        <a href="#">From Template</a>
                                    </li>
                                    <li class="subnav-link-item">
                                        <a href="#">From Component</a>
                                    </li>
                                    <li class="subnav-link-item">
                                        <a href="/P_Page/add">(advanced)</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="has-subnav-items link-item">
                <a data-toggle="collapse" href="#subnav-menu-templates" role="button" aria-expanded="false" aria-controls="collapseExample">
                    <i data-feather="layout"></i>Templates
                </a>
                <div class="collapse" id="subnav-menu-templates">
                    <ul class="subnav list-unstyled">
                        <li class="subnav-link-item">
                            <a href="/P_Template/list">All Templates</a>
                        </li>
                        <li class="subnav-link-item">
                            <a href="/P_Template/add">Add New Template</a>
                        </li>
                        <li class="subnav-link-item">
                            <a href="/P_Component/list">All Component</a>
                        </li>
                        <li class="subnav-link-item">
                            <a href="/P_Component/add">Add New Component</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="has-subnav-items link-item">
                <a data-toggle="collapse" href="#subnav-menu-themes" role="button" aria-expanded="false" aria-controls="collapseExample">
                    <i data-feather="package"></i>Themes
                </a>
                <div class="collapse" id="subnav-menu-themes">
                    <ul class="subnav list-unstyled">
                        <li class="subnav-link-item">
                            <a href="/P_Theme/list">All Themes</a>
                        </li>
                        <li class="subnav-link-item">
                            <a href="/P_Theme/add">Add New Theme</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="has-subnav-items link-item">
                <a data-toggle="collapse" href="#subnav-menu-blog" role="button" aria-expanded="false" aria-controls="collapseExample">
                    <i data-feather="message-square"></i>Blog
                </a>
                <div class="collapse" id="subnav-menu-blog">
                    <ul class="subnav list-unstyled">
                        <li class="subnav-link-item">
                            <a href="/P_Blog/list">All Articles</a>
                        </li>
                        <li class="subnav-link-item">
                            <a href="/P_Blog/add">Add New Article</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="has-subnav-items link-item">
                <a data-toggle="collapse" href="#subnav-menu-assets" role="button" aria-expanded="false" aria-controls="collapseExample">
                    <i data-feather="image"></i>Assets
                </a>
                <div class="collapse" id="subnav-menu-assets">
                    <ul class="subnav list-unstyled">
                        <li class="subnav-link-item">
                            <a href="/P_Asset/list">All Assets</a>
                        </li>
                        <li class="subnav-link-item">
                            <a href="/P_Asset/add">Add New Asset</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="has-subnav-items link-item">
                <a data-toggle="collapse" href="#subnav-menu-forms" role="button" aria-expanded="false" aria-controls="collapseExample">
                    <i data-feather="mail"></i>Forms
                </a>
                <div class="collapse" id="subnav-menu-forms">
                    <ul class="subnav list-unstyled">
                        <li class="subnav-link-item">
                            <a href="/P_Form/list">All Forms</a>
                        </li>
                        <li class="subnav-link-item">
                            <a href="/P_Form/add">Add New Form</a>
                        </li>
                        <li class="subnav-link-item">
                            <a href="/P_Form/submissions">Submissions</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="has-subnav-items link-item">
                <a data-toggle="collapse" href="#subnav-menu-texts" role="button" aria-expanded="false" aria-controls="collapseExample">
                    <i data-feather="mail"></i>Texts
                </a>
                <div class="collapse" id="subnav-menu-texts">
                    <ul class="subnav list-unstyled">
                        <li class="subnav-link-item">
                            <a href="/P_Text/list">All Texts</a>
                        </li>
                        <li class="subnav-link-item">
                            <a href="/P_Text/add">Add New Text</a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>

    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper">
        <nav class="navbar  navbar-top d-md-flex">
            <div class="container-fluid">

                <a href="#menu-toggle" id="menu-toggle" class="btn btn-outline-secondary border-0 mr-2"><i data-feather="menu"></i></a>
                <!-- Brand -->
                <div class="mr-auto">
                    <span class="navbar-brand " href="index.html">
                        Dashboard
                    </span>
                    <span class="navbar-breadcrumb d-none"><a href="dashboard.html" class="d-inline-block">Home</a></span>
                </div>

                <!-- User -->
                <div class="navbar-user">



                    <!-- Dropdown -->
                    <div class="dropdown">

                        <!-- Toggle -->
                        <a href="#" class="avatar avatar-sm avatar-online dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="<?= $_avatarURL?>" alt="..." class="avatar-img rounded-circle">
                        </a>
                        <!-- Menu -->
<?php if (false !== G::$S->Login): ?>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="/Account/edit" class="dropdown-item">Account Settings</a>
                            <hr class="dropdown-divider">
                            <a href="/Account/logout" class="dropdown-item">Logout</a>
                        </div>
<?php else: ?>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="/Account/login?_URI=/P_Dashboard" class="dropdown-item">Login</a>
                        </div>
<?php endif; ?>
                    </div>

                </div>

            </div> <!-- / .container-fluid -->
        </nav>
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
        <main class="main">
