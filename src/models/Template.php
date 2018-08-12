<?php
/**
 * Template - For Page and sub-Page layouts
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
 * Class Template
 * @package Stationer\Pencil\models
 * @author Andrew Leach
 *
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
        'body'        => ['type' => 's', 'max' => 65535],
        'css'         => ['type' => 's', 'max' => 65535],
    ];

    const PAGE = 1;
    const COMPONENT = 2;
}
