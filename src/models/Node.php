<?php
/**
 * Node - For organizing all Pencil data
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
 * Class Node
 *
 * @package Stationer\Pencil\models
 * @author  Andrew Leach
 *
 * @property int    $node_id
 * @property string $created_uts
 * @property int    $updated_dts
 * @property int    $parent_id
 * @property int    $content_id
 * @property string $contentType
 * @property string $label
 * @property int    $creator_id
 * @property string $keywords
 * @property string $description
 * @property bool   $published
 * @property bool   $trashed
 * @property bool   $featured
 * @property string $pathAlias
 * @property int    $ordinal
 * @property string $path
 * @property int    $left_index
 * @property int    $right_index
 *
 * @property PassiveRecord $File
 */
class Node extends PassiveRecord {
    protected static $table = G_DB_TABL.'Node';
    protected static $pkey = 'node_id';
    protected static $ukeys = [['parent_id', 'label']];
    protected static $keys = [['left_index', 'right_index'], ['right_index', 'left_index'], '`path`(64)'];
    protected static $query = '';
    protected static $vars = [
        'node_id'     => ['type' => 'i', 'min' => 0, 'guard' => true],
        'created_uts' => ['type' => 'ts', 'min' => 0, 'guard' => true],
        'updated_dts' => ['type' => 'dt', 'def' => NOW, 'guard' => true],

        'parent_id'   => ['type' => 'i', 'min' => 0],
        'content_id'  => ['type' => 'i', 'min' => 0],
        'contentType' => ['type' => 's', 'strict' => true, 'max' => 255],
        'label'       => ['type' => 's', 'strict' => true, 'max' => 255],
        'creator_id'  => ['type' => 'i', 'min' => 0],
        'keywords'    => ['type' => 's', 'strict' => true, 'max' => 255],
        'description' => ['type' => 's', 'strict' => true, 'max' => 255],
        'published'   => ['type' => 'b', 'def' => 0],
        'trashed'     => ['type' => 'b', 'def' => 0],
        'featured'    => ['type' => 'b', 'def' => 0],
        'pathAlias'   => ['type' => 's', 'strict' => true, 'min' => 0, 'max' => 255],
        'ordinal'     => ['type' => 'i', 'min' => 0, 'max' => 65535],
        'path'        => ['type' => 's', 'def' => '', 'max' => 65535, 'guard' => true],
        'left_index'  => ['type' => 'i', 'min' => 1, 'guard' => true],
        'right_index' => ['type' => 'i', 'min' => 1, 'guard' => true],
    ];
    /** @var PassiveRecord The record identified by contentType and content_id */
    protected $File;

    /**
     * Attach a record to this Node, set the content type and _id
     *
     * @param PassiveRecord|null $File Record to attach
     *
     * @return PassiveRecord|null Currently attached file, if attached
     */
    public function File(PassiveRecord $File = null) {
        $argv = func_get_args();
        if (count($argv)) {
            $this->File = null;
            $this->__set('contentType', '');
            $this->__set('content_id', 0);
            if ($File !== null) {
                $type = get_class($File);
                $type = substr($type, strrpos($type, '\\') + 1);
                $this->__set('contentType', $type);
                $this->__set('content_id', $File->{$File->getPkey()});
                $this->File = $File;
            }
        }

        return $this->File;
    }

    /**
     * Scrub Node label to only dash, underscore, and word characters
     *
     * @return mixed
     */
    public function label() {
        $argv = func_get_args();
        if (count($argv)) {
            $this->_s(__FUNCTION__, static::cleanLabel($argv[0]));
        }

        return $this->_s(__FUNCTION__);
    }

    /**
     * Scrub Node type to only word characters
     *
     * @return mixed
     */
    public function contentType() {
        $argv = func_get_args();
        if (count($argv)) {
            $val = preg_replace('~[^\w]~', '_', $argv[0]);
            $this->_s(__FUNCTION__, $val);
        }

        return $this->_s(__FUNCTION__);
    }

    /**
     * Scrub Node path to only word characters and {/, -, _, .} characters
     *
     * @return mixed
     */
    public function path() {
        $argv = func_get_args();
        if (count($argv)) {
            $this->_s(__FUNCTION__, static::cleanPath($argv[0]));
        }

        return $this->_s(__FUNCTION__);
    }

    /**
     * Replace space with underscore and other non-word chars with dash
     * Then clean up with \fakepath()
     *
     * @param string $path Path to clean
     *
     * @return string
     */
    public static function cleanPath(string $path) {
        $path = str_replace(' ', '_', $path);
        $path = preg_replace('~[^-\w/]~', '-', $path);
        $path = \fakepath($path, '/');

        return $path;
    }

    /**
     * Replace space with underscore and other non-word chars with dash
     *
     * @param string $label Label to clean
     *
     * @return string
     */
    public static function cleanLabel(string $label) {
        $label = str_replace(' ', '_', $label);
        $label = preg_replace('~[^-\w]~', '-', $label);

        return $label;
    }
}
