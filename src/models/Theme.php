<?php

namespace Stationer\Pencil\Models;

use Stationer\Graphite\data\PassiveRecord;

/**
 * Class Theme
 * @package Stationer\Pencil\Models
 * @author Andrew Leach
 *
 * @property int    content_id
 * @property string created_uts
 * @property int    updated_dts
 * @property string footer
 * @property string header
 * @property string css
 */
class Theme extends PassiveRecord {
    protected static $table = G_DB_TABL . 'Theme';
    protected static $pkey = 'theme_id';
    protected static $query = '';
    protected static $vars = [
        'theme_id'    => ['type' => 'i', 'min' => 0],
        'created_uts' => ['type' => 'ts', 'min' => 0],
        'updated_dts' => ['type' => 'dt', 'min' => NOW, 'def' => NOW],

        'footer'      => ['type' => 's', 'max' => 65535],
        'header'      => ['type' => 's', 'min' => 65535],
        'css'         => ['type' => 's', 'min' => 65535],
    ];
}
