<?php

namespace Stationer\Pencil\Models;

use Stationer\Graphite\data\PassiveRecord;

/**
 * Class Content
 * @package Stationer\Pencil\Models
 * @author Andrew Leach
 *
 * @property int    content_id
 * @property string created_uts
 * @property int    updated_dts
 * @property string title
 * @property string body
 */
class Content extends PassiveRecord {
    protected static $table = G_DB_TABL . 'Content';
    protected static $pkey = 'content_id';
    protected static $query = '';
    protected static $vars = [
        'content_id'  => ['type' => 'i', 'min' => 0],
        'created_uts' => ['type' => 'ts', 'min' => 0],
        'updated_dts' => ['type' => 'dt', 'min' => NOW, 'def' => NOW],

        'title'       => ['type' => 's', 'strict' => true, 'max' => 255],
        'body'        => ['type' => 's', 'strict' => true, 'max' => 65535],
    ];
}
