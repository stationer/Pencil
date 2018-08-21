<?php
/**
 * AncestorsByPathReport - Fetch all nodes with paths parent to specified
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
 * Class AncestorsByPathReport
 *
 * @package Stationer\Pencil\reports
 * @author  Tyler Uebele
 *
 */
class AncestorsByPathReport extends Report {
    protected static $query = "";
    protected static $vars = [
        'path' => ['type' => 's', 'sql' => "'%s' LIKE CONCAT(t.`path`, '/%%')"],
        'line' => ['type' => 's', 'sql' => "('%1\$s' = t.`path` OR '%1\$s/' LIKE CONCAT(t.`path`, '%%'))"],
    ];

    public function __construct($a = null, bool $b = null) {
        $fields = array_keys(Node::getFieldList());
        $table  = Node::getTable();

        static::$query = "
SELECT t.`".join('`, t.`', $fields)."`
FROM `$table` t
WHERE %s
ORDER BY `left_index` DESC
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
