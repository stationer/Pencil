<?php

namespace Stationer\Pencil\Models;

use Stationer\Graphite\data\PassiveRecord;

/**
 * Class Site
 * @package Stationer\Pencil\Models
 * @author Andrew Leach
 *
 * @property int    site_id
 * @property string created_uts
 * @property int    updated_dts
 * @property int    theme_id
 * @property int    defaultPage_id
 * @property string options
 */
class Site extends PassiveRecord {
    protected static $table = G_DB_TABL . 'Site';
    protected static $pkey = 'site_id';
    protected static $query = '';
    protected static $vars = [
        'site_id'        => ['type' => 'i', 'min' => 0],
        'created_uts'    => ['type' => 'ts', 'min' => 0],
        'updated_dts'    => ['type' => 'dt', 'min' => NOW, 'def' => NOW],

        'theme_id'       => ['type' => 'i', 'min' => 0],
        'defaultPage_id' => ['type' => 'i', 'min' => 0],
    ];
}
