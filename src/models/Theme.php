<?php
/**
 * Theme - For styling Sites
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
 * Class Theme
 * @package Stationer\Pencil\models
 * @author Andrew Leach
 *
 * @property int    content_id
 * @property string created_uts
 * @property int    updated_dts
 * @property string footer
 * @property string header
 * @property string css
 */
class Theme extends PassiveRecord {
    protected static $table = G_DB_TABL . 'Theme';
    protected static $pkey = 'theme_id';
    protected static $query = '';
    protected static $vars = [
        'theme_id'    => ['type' => 'i', 'min' => 0, 'guard' => true],
        'created_uts' => ['type' => 'ts', 'min' => 0, 'guard' => true],
        'updated_dts' => ['type' => 'dt', 'min' => NOW, 'def' => NOW, 'guard' => true],

        'document'    => ['type' => 's', 'max' => 65535],
        'footer'      => ['type' => 's', 'max' => 65535],
        'header'      => ['type' => 's', 'max' => 65535],
        'css'         => ['type' => 's', 'max' => 65535],
    ];
}
