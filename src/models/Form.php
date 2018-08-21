<?php
/**
 * Form - For managing HTML forms
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
 * Class Form
 * @package Stationer\Pencil\models
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
        'form_id'     => ['type' => 'i', 'min' => 0, 'guard' => true],
        'created_uts' => ['type' => 'ts', 'min' => 0, 'guard' => true],
        'updated_dts' => ['type' => 'dt', 'min' => NOW, 'def' => NOW, 'guard' => true],

        'fields'      => ['type' => 's', 'max' => 65535],
    ];
}
