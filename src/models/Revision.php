<?php
/**
 * Revision - For recording Revision history
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
 * Class Revision
 *
 * @package Stationer\Pencil\models
 * @author  Andrew Leach
 *
 * @property int    revision_id
 * @property string created_uts
 * @property int    updated_dts
 * @property string revisedType
 * @property int    revised_id
 * @property int    editor_id
 * @property string changes
 */
class Revision extends PassiveRecord {
    protected static $table = G_DB_TABL.'Revision';
    protected static $pkey = 'revision_id';
    protected static $query = '';
    protected static $vars = [
        'revision_id' => ['type' => 'i', 'min' => 0, 'guard' => true],
        'created_uts' => ['type' => 'ts', 'min' => 0, 'guard' => true],
        'updated_dts' => ['type' => 'dt', 'def' => NOW, 'guard' => true],

        'revisedModel' => ['type' => 's', 'min' => 0, 'max' => 255],
        'revised_id'   => ['type' => 'i', 'min' => 0],
        'editor_id'    => ['type' => 'i', 'min' => 0],
        'changes'      => ['type' => 's', 'strict' => true, 'max' => 655350],
    ];
}
