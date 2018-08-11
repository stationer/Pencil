<?php
/**
 * Tag - For Tagging Nodes
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
 * Class Tag
 * @package Stationer\Pencil\models
 * @author Andrew Leach
 *
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

        'label'       => ['type' => 's', 'strict' => true, 'min' => 0, 'max' => 255],
        'type'        => ['type' => 's', 'strict' => true, 'min' => 0, 'max' => 255],
    ];
}
