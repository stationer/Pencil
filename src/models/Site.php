<?php
/**
 * Site - For Site defaults
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
 * Class Site
 * @package Stationer\Pencil\models
 * @author Andrew Leach
 *
 * @property int    site_id
 * @property string created_uts
 * @property int    updated_dts
 * @property int    theme_id
 * @property int    defaultPage_id
 * @property string options
 */
class Site extends PassiveRecord {
    protected static $table = G_DB_TABL . 'Site';
    protected static $pkey = 'site_id';
    protected static $query = '';
    protected static $vars = [
        'site_id'        => ['type' => 'i', 'min' => 0, 'guard' => true],
        'created_uts'    => ['type' => 'ts', 'min' => 0, 'guard' => true],
        'updated_dts'    => ['type' => 'dt', 'min' => NOW, 'def' => NOW, 'guard' => true],

        'theme_id'       => ['type' => 'i', 'min' => 0],
        'defaultPage_id' => ['type' => 'i', 'min' => 0],
    ];
}
