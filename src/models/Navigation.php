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
 *
 * @package Stationer\Pencil\models
 * @author  Andrew Leach
 *
 * @property int    navigation_id
 * @property string created_uts
 * @property int    updated_dts
 * @property string source
 * @property string rendered
 */
class Navigation extends PassiveRecord {
    protected static $table = G_DB_TABL.'Navigation';
    protected static $pkey = 'navigation_id';
    protected static $query = '';
    protected static $vars = [
        'navigation_id'     => ['type' => 'i', 'min' => 0, 'guard' => true],
        'created_uts' => ['type' => 'ts', 'min' => 0, 'guard' => true],
        'updated_dts' => ['type' => 'dt', 'def' => NOW, 'guard' => true],

        'source'      => ['type' => 's', 'strict' => true, 'max' => 655350, 'def' => ''],
        'rendered'    => ['type' => 's', 'strict' => true, 'max' => 655350, 'def' => ''],
    ];
}
