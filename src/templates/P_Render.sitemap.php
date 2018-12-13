<?php
/** @var \Stationer\Pencil\models\Node[] $Pages */
/** @var string $root */
header('Content-Type: text/xml;Charset=UTF-8');
use Stationer\Graphite\G;
use Stationer\Pencil\PencilController;
?>
<?xml version="1.0" encoding="utf-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<?php foreach ($Pages as $Page) : ?>
    <url>
        <loc><?= G::$G['VIEW']['_siteURL'].str_replace([$root.PencilController::WEBROOT, $root], '', $Page->pathAlias ?: $Page->path) ?></loc>
        <lastmod><?=date(DATE_ATOM, strtotime($Page->updated_dts))?></lastmod>
        <priority><?=$Page->featured ? 0.8 : 0.5 ?></priority>
    </url>
<?php endforeach; ?>
</urlset>
