<?php
/**
 * Page - For describing a Page of content
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
 * Class Page
 * @package Stationer\Pencil\models
 * @author Andrew Leach
 *
 * @property int    page_id
 * @property string created_uts
 * @property int    updated_dts
 * @property string title
 * @property int    template_id
 */
class Page extends PassiveRecord {
    protected static $table = G_DB_TABL . 'Page';
    protected static $pkey = 'page_id';
    protected static $query = '';
    protected static $vars = [
        'page_id'     => ['type' => 'i', 'min' => 0],
        'created_uts' => ['type' => 'ts', 'min' => 0],
        'updated_dts' => ['type' => 'dt', 'min' => NOW, 'def' => NOW],

        'title'       => ['type' => 's', 'max' => 255],
        'template_id' => ['type' => 'i', 'strict' => true, 'min' => 0],
    ];
}
