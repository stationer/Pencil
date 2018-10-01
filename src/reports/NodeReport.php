<?php
/**
 * NodeReport - Fetch Nodes
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

/**
 * Class NodeReport
 *
 * @package Stationer\Pencil\reports
 * @author  Tyler Uebele
 *
 */
class NodeReport extends Report {
    protected static $query = "";
    protected static $vars = [
        'label'       => ['type' => 's', 'sql' => "t.`label` = '%s'"],
        'contentType' => ['type' => 's', 'sql' => "t.`contentType` = '%s'"],
        'content_id'  => ['type' => 'i', 'sql' => "t.`content_id` = '%d'"],
        'node_id'     => ['type' => 'i', 'sql' => "t.`node_id` = '%d'"],
        'left_max'    => ['type' => 'i', 'sql' => "t.`left_index` <= '%d'"],
        'left_min'    => ['type' => 'i', 'sql' => "t.`left_index` >= '%d'"],
        'right_max'   => ['type' => 'i', 'sql' => "t.`right_index` <= '%d'"],
        'right_min'   => ['type' => 'i', 'sql' => "t.`right_index` >= '%d'"],
        'published'   => ['type' => 'b', 'sql' => "t.`published` = b'%d'"],
        'trashed'     => ['type' => 'b', 'sql' => "t.`trashed` = b'%d'"],
        'featured'    => ['type' => 'b', 'sql' => "t.`featured` = b'%d'"],
    ];

    public function __construct($a = null, bool $b = null) {
        $fields = array_keys(Node::getFieldList());
        $table  = Node::getTable();

        static::$query = "
SELECT t.`".join('`, t.`', $fields)."`
FROM `$table` t
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
