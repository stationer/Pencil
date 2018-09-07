<?php
/**
 * Asset - For tracking uploaded files
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
 * Class Asset
 *
 * @package Stationer\Pencil\models
 * @author  Tyler Uebele
 *
 * @property int    $asset_id
 * @property string $created_uts
 * @property int    $updated_dts
 * @property string $type
 * @property string $path
 */
class Asset extends PassiveRecord {
    protected static $table = G_DB_TABL.'Asset';
    protected static $pkey = 'asset_id';
    protected static $query = '';
    protected static $vars = [
        'asset_id'    => ['type' => 'i', 'min' => 0, 'guard' => true],
        'created_uts' => ['type' => 'ts', 'min' => 0, 'guard' => true],
        'updated_dts' => ['type' => 'dt', 'def' => NOW, 'guard' => true],

        'type' => ['type' => 's', 'max' => 30],
        'path' => ['type' => 's', 'strict' => true, 'max' => 255],
    ];
}
