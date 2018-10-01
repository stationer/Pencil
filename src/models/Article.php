<?php
/**
 * Content - For blocks of content
 *
 * PHP version 7.0
 *
 * @package  Stationer\Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */

namespace Stationer\Pencil\models;

use Stationer\Graphite\data\PassiveRecord;

/**
 * Class Content
 *
 * @package Stationer\Pencil\models
 * @author  Andrew Leach
 *
 * @property int    $article_id
 * @property string $created_uts
 * @property int    $updated_dts
 * @property int    $release_uts
 * @property int    $author_id
 * @property string $title
 * @property string $body
 */
class Article extends PassiveRecord {
    protected static $table = G_DB_TABL.'Article';
    protected static $pkey = 'article_id';
    protected static $query = '';
    protected static $vars = [
        'article_id'  => ['type' => 'i', 'min' => 0, 'guard' => true],
        'created_uts' => ['type' => 'ts', 'min' => 0, 'guard' => true],
        'updated_dts' => ['type' => 'dt', 'def' => NOW, 'guard' => true],

        'release_uts' => ['type' => 'ts', 'min' => 0],
        'author_id'   => ['type' => 'i', 'min' => 0],
        'title'       => ['type' => 's', 'strict' => true, 'max' => 255, 'def' => ''],
        'body'        => ['type' => 's', 'strict' => true, 'max' => 655350, 'def' => ''],
    ];
}
