<?php

namespace Stationer\Pencil\Models;

use Stationer\Graphite\data\PassiveRecord;

/**
 * Class File
 * @package Stationer\Pencil\Models
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
        'file_id'     => ['type' => 'i', 'min' => 0],
        'created_uts' => ['type' => 'ts', 'min' => 0],
        'updated_dts' => ['type' => 'dt', 'min' => NOW, 'def' => NOW],

        'type'        => ['type' => 's', 'max' => 30],
        'path'        => ['type' => 's', 'strict' => true, 'max' => 255],
    ];
}
