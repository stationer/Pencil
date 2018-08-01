<?php

namespace Stationer\Pencil\Models;

use Stationer\Graphite\data\PassiveRecord;

/**
 * Class Form
 * @package Stationer\Pencil\Models
 * @author Andrew Leach
 *
 * @property int    form_id
 * @property string created_uts
 * @property int    updated_dts
 * @property string fields
 */
class Form extends PassiveRecord {
    protected static $table = G_DB_TABL . 'Form';
    protected static $pkey = 'form_id';
    protected static $query = '';
    protected static $vars = [
        'form_id'     => ['type' => 'i', 'min' => 0],
        'created_uts' => ['type' => 'ts', 'min' => 0],
        'updated_dts' => ['type' => 'dt', 'min' => NOW, 'def' => NOW],

        'fields'      => ['type' => 's', 'max' => 65535],
    ];
}
