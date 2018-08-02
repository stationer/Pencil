<?php

namespace Stationer\Pencil\Models;

use Stationer\Graphite\data\PassiveRecord;

/**
 * Class Submission
 * @package Stationer\Pencil\Models
 * @author Andrew Leach
 *
 * @property int    submission_id
 * @property string created_uts
 * @property int    updated_dts
 * @property int    form_id
 * @property string ip
 * @property string ua
 * @property string data
 */
class Submission extends PassiveRecord {
    protected static $table = G_DB_TABL . 'Submission';
    protected static $pkey = 'submission_id';
    protected static $query = '';
    protected static $vars = [
        'submission_id' => ['type' => 'i', 'min' => 0],
        'created_uts'   => ['type' => 'ts', 'min' => 0],
        'updated_dts'   => ['type' => 'dt', 'min' => NOW, 'def' => NOW],

        'form_id' => ['type' => 'i', 'min' => 0],
        'ip'      => ['type' => 'ip'],
        'ua'      => ['type' => 's', 'max' => 255],
        'data'    => ['type' => 's', 'strict' => true, 'max' => 65535],
    ];
}
