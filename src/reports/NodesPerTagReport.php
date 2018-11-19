<?php
/**
 * NodesPerTagReport -
 *
 * PHP version 7.0
 *
 * @package  Stationer\Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */

namespace Stationer\Pencil\reports;

use Stationer\Graphite\data\Report;
use Stationer\Pencil\models\Node;
use Stationer\Pencil\models\Tag;

/**
 * Class NodesPerTagReport
 *
 * @package Stationer\Pencil\reports
 * @author  Tyler Uebele
 *
 */
class NodesPerTagReport extends Report {
    protected static $query = "";
    protected static $vars = [
        'path'        => ['type' => 's', 'sql' => "t.`path` LIKE '%s/%%'"],
        'tag'         => ['type' => 's', 'sql' => "t2.`label` = '%s'"],
        'contentType' => ['type' => 's', 'sql' => "t.`contentType` = '%s'"],
        'published'   => ['type' => 'b', 'sql' => "t.`published` = b'%d'"],
        'trashed'     => ['type' => 'b', 'sql' => "t.`trashed` = b'%d'"],
        'featured'    => ['type' => 'b', 'sql' => "t.`featured` = b'%d'"],
    ];

    public function __construct($a = null, bool $b = null) {
        $table  = Node::getTable();
        $joiner = Node::getTable('Tag');

        static::$query = "
SELECT COUNT(t.`node_id`) AS `count`, IFNULL(t2.`label`, '[no tag]') as `tag`
FROM `$table` t
LEFT JOIN `$joiner` j ON t.`node_id` = j.`node_id`
LEFT JOIN `".Tag::getTable()."` t2 ON j.`tag_id` = t2.`tag_id`
WHERE %s
GROUP BY `tag`
";
        parent::__construct($a, $b);
    }
}
