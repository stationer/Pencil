<?php

namespace Stationer\Pencil\Models;

use Stationer\Graphite\data\PassiveRecord;

/**
 * Class Revision
 * @package Stationer\Pencil\Models
 * @property int    revision_id
 * @property string created_uts
 * @property int    updated_dts
 * @property string revisedType
 * @property int    revised_id
 * @property int    editor_id
 * @property string changes
 */
class Revision extends PassiveRecord {
    protected static $table = G_DB_TABL . 'Revision';
    protected static $pkey = 'revision_id';
    protected static $query = '';
    protected static $vars = [
        'revision_id' => ['type' => 'i', 'min' => 0],
        'created_uts' => ['type' => 'ts', 'min' => 0],
        'updated_dts' => ['type' => 'dt', 'min' => NOW, 'def' => NOW],

        'revisedType' => ['type' => 's', 'min' => 0, 'max' => 255],
        'revised_id'  => ['type' => 's', 'strict' => true, 'min' => 3, 'max' => 255],
        'editor_id'   => ['type' => 'i', 'min' => 0],
        'changes'     => ['type' => 's', 'strict' => true, 'min' => 3],
    ];
}
