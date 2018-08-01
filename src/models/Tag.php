<?php

namespace Stationer\Pencil\Models;

use Stationer\Graphite\data\PassiveRecord;

/**
 * Class Tag
 * @package Stationer\Pencil\Models
 * @property int    tag_id
 * @property string created_uts
 * @property int    updated_dts
 * @property string label
 * @property string type
 */
class Tag extends PassiveRecord {
    protected static $table = G_DB_TABL . 'Tag';
    protected static $pkey = 'tag_id';
    protected static $query = '';
    protected static $vars = [
        'tag_id'      => ['type' => 'i', 'min' => 0],
        'created_uts' => ['type' => 'ts', 'min' => 0],
        'updated_dts' => ['type' => 'dt', 'min' => NOW, 'def' => NOW],

        'label'       => ['type' => 's', 'strict' => true, 'min' => 3, 'max' => 255],
        'type'        => ['type' => 's', 'strict' => true, 'min' => 3, 'max' => 255],
    ];
}
