<?php

namespace Stationer\Pencil\Models;

use Stationer\Graphite\data\PassiveRecord;

/**
 * Class Page
 * @package Stationer\Pencil\Models
 * @property int    page_id
 * @property string created_uts
 * @property int    updated_dts
 * @property string title
 * @property int    template_id
 */
class Page extends PassiveRecord {
    protected static $table = G_DB_TABL . 'Page';
    protected static $pkey = 'page_id';
    protected static $query = '';
    protected static $vars = [
        'page_id'     => ['type' => 'i', 'min' => 0],
        'created_uts' => ['type' => 'ts', 'min' => 0],
        'updated_dts' => ['type' => 'dt', 'min' => NOW, 'def' => NOW],

        'title'       => ['type' => 's', 'min' => 0],
        'template_id' => ['type' => 'i', 'min' => 0],
    ];
}
