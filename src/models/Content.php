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
 * @package Stationer\Pencil\models
 * @author Andrew Leach
 *
 * @property int    content_id
 * @property string created_uts
 * @property int    updated_dts
 * @property string title
 * @property string body
 */
class Content extends PassiveRecord {
    protected static $table = G_DB_TABL . 'Content';
    protected static $pkey = 'content_id';
    protected static $query = '';
    protected static $vars = [
        'content_id'  => ['type' => 'i', 'min' => 0, 'guard' => true],
        'created_uts' => ['type' => 'ts', 'min' => 0, 'guard' => true],
        'updated_dts' => ['type' => 'dt', 'def' => NOW, 'guard' => true],

        'title'       => ['type' => 's', 'strict' => true, 'max' => 255, 'def' => ''],
        'body'        => ['type' => 's', 'strict' => true, 'max' => 655350, 'def' => ''],
    ];
}
