<?php
/**
 * DescendantsByPathReport - Fetch all nodes with paths under specified
 *
 * PHP version 7.0
 *
 * @package  Stationer\Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */

namespace Stationer\Pencil\reports;

use Stationer\Graphite\data\Report;
use Stationer\Graphite\G;
use Stationer\Pencil\models\Node;
use Stationer\Pencil\models\Tag;

/**
 * Class DescendantsByPathReport
 *
 * @package Stationer\Pencil\reports
 * @author  Tyler Uebele
 *
 */
class DescendantsByPathReport extends Report {
    protected static $query = "";
    protected static $vars = [
        'path' => ['type' => 's', 'sql' => "t.`path` LIKE '%s%%'"],
        'tag'  => ['type' => 's', 'sql' => "t2.`label` = '%s'"],
    ];

    public function __construct($a = null, bool $b = null) {
        $fields = array_keys(Node::getFieldList());
        $table = Node::getTable();
        $joiner = Node::getTable('Tag');

        static::$query = "
SELECT t.`".join('`, t.`', $fields)."`
FROM `$table` t
LEFT JOIN `$joiner` j ON t.`node_id` = j.`node_id`
LEFT JOIN `".Tag::getTable()."` t2 ON j.`tag_id` = t2.`tag_id`
WHERE %s
ORDER BY `left_index` ASC
";
        parent::__construct($a, $b);
    }

    public function onload() {
        $Nodes = [];
        foreach ($this->_data as $row) {
            $Nodes[$row[Node::getPkey()]] = G::build(Node::class);
            $Nodes[$row[Node::getPkey()]]->load_array($row);
        }
        $this->_data = $Nodes;
    }
}
