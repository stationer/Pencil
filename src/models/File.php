<?php
/**
 * File - For tracking uploaded files
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
 * Class File
 * @package Stationer\Pencil\models
 * @author Andrew Leach
 *
 * @property int    file_id
 * @property string created_uts
 * @property int    updated_dts
 * @property string type
 * @property string path
 */
class File extends PassiveRecord {
    protected static $table = G_DB_TABL . 'File';
    protected static $pkey = 'file_id';
    protected static $query = '';
    protected static $vars = [
        'file_id'     => ['type' => 'i', 'min' => 0, 'guard' => true],
        'created_uts' => ['type' => 'ts', 'min' => 0, 'guard' => true],
        'updated_dts' => ['type' => 'dt', 'min' => NOW, 'def' => NOW, 'guard' => true],

        'type'        => ['type' => 's', 'max' => 30],
        'path'        => ['type' => 's', 'strict' => true, 'max' => 255],
    ];
}
