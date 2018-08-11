<?php
/**
 * Node - For organizing all Pencil data
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
 * Class Node
 * @package Stationer\Pencil\models
 * @author Andrew Leach
 *
 * @property int    node_id
 * @property string created_uts
 * @property int    updated_dts
 * @property int    parent_id
 * @property int    content_id
 * @property string contentType
 * @property string label
 * @property int    creator_id
 * @property string keywords
 * @property string description
 * @property bool   published
 * @property bool   trashed
 * @property bool   featured
 * @property string permalink
 * @property int    ordinal
 */
class Node extends PassiveRecord {
    protected static $table = G_DB_TABL . 'Node';
    protected static $pkey = 'node_id';
    protected static $ukeys = [['parent_id', 'label']];
    protected static $query = '';
    protected static $vars = [
        'node_id'     => ['type' => 'i', 'min' => 0],
        'created_uts' => ['type' => 'ts', 'min' => 0],
        'updated_dts' => ['type' => 'dt', 'min' => NOW, 'def' => NOW],

        'parent_id'   => ['type' => 'i', 'min' => 0],
        'content_id'  => ['type' => 'i', 'min' => 0],
        'contentType' => ['type' => 's', 'strict' => true, 'max' => 255],
        'label'       => ['type' => 's', 'strict' => true, 'max' => 255],
        'creator_id'  => ['type' => 'i', 'min' => 0],
        'keywords'    => ['type' => 's', 'strict' => true, 'max' => 255],
        'description' => ['type' => 's', 'strict' => true, 'max' => 255],
        'published'   => ['type' => 'b', 'def' => 0],
        'trashed'     => ['type' => 'b', 'def' => 0],
        'featured'    => ['type' => 'b', 'def' => 0],
        'permalink'   => ['type' => 's', 'strict' => true, 'min' => 0, 'max' => 255],
        'ordinal'     => ['type' => 'i', 'min' => 0, 'max' => 65535],
    ];
}
