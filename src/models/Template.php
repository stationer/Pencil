<?php

namespace Stationer\Pencil\Models;

use Stationer\Graphite\data\PassiveRecord;

/**
 * Class Template
 * @package Stationer\Pencil\Models
 * @property int    template_id
 * @property string created_uts
 * @property int    updated_dts
 * @property int    type
 * @property string body
 * @property string css
 */
class Template extends PassiveRecord {
    protected static $table = G_DB_TABL . 'Template';
    protected static $pkey = 'template_id';
    protected static $query = '';
    protected static $vars = [
        'template_id' => ['type' => 'i', 'min' => 0],
        'created_uts' => ['type' => 'ts', 'min' => 0],
        'updated_dts' => ['type' => 'dt', 'min' => NOW, 'def' => NOW],

        'type'        => ['type' => 'i', 'min' => 0],
        'body'        => ['type' => 's', 'min' => 3],
        'css'         => ['type' => 's', 'min' => 3],
    ];
}
