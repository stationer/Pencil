<?php
/**
 * Text - For blocks of text content
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
 * Class Text
 *
 * @package Stationer\Pencil\models
 * @author  Tyler Uebele
 *
 * @property int    $text_id
 * @property string $created_uts
 * @property int    $updated_dts
 * @property string $title
 * @property string $body
 */
class Text extends PassiveRecord {
    protected static $table = G_DB_TABL.'Text';
    protected static $pkey = 'text_id';
    protected static $query = '';
    protected static $vars = [
        'text_id'     => ['type' => 'i', 'min' => 0, 'guard' => true],
        'created_uts' => ['type' => 'ts', 'min' => 0, 'guard' => true],
        'updated_dts' => ['type' => 'dt', 'def' => NOW, 'guard' => true],

        'mimeType' => ['type' => 's', 'strict' => true, 'max' => 255, 'def' => 'text/plain'],
        'body'     => ['type' => 's', 'strict' => true, 'max' => 655350, 'def' => ''],
    ];
}
