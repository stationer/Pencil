<?php
/**
 * TreeMySQLDataProvider - Provide Tree data from MySQL
 * File : /src/data/TreeMySQLDataProvider.php
 *
 * PHP version 7.0
 *
 * @package  Stationer\Graphite
 * @author   Tyler Uebele
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */

namespace Stationer\Pencil\data;

use Stationer\Graphite\data\MySQLDataProvider;
use Stationer\Graphite\data\PassiveRecord;
use Stationer\Graphite\G;
use Stationer\Pencil\models\Node;

/**
 * TreeMySQLDataProvider class - Runs CRUD to MySQL for Node models
 *
 * @package  Stationer\Graphite
 * @author   Tyler Uebele
 * @license  MIT https://github.com/stationer/Graphite/blob/master/LICENSE
 * @link     https://github.com/stationer/Graphite
 * @see      /src/data/mysqli_.php
 * @see      /src/data/PassiveRecord.php
 */
class TreeMySQLDataProvider extends MySQLDataProvider {
    /**
     * Save data for passed model
     *
     * @param PassiveRecord $Model Model to save, passed by reference
     *
     * @return bool|null True on success, False on failure, Null on invalid attempt
     */
    public function insert(PassiveRecord &$Model) {
        if (!is_a($Model, Node::class)) {
            return parent::insert($Model);
        }
        /** @var Node $Model */
        $diff = $Model->getDiff();

        // If no fields were set, this is unexpected
        if (0 == count($diff)) {
            return null;
        }

        $Model->oninsert();
        $query = sprintf("CALL `usp_Tree_insert` (%d, '%s', %d)",
            $Model->parent_id,
            G::$M->escape_string($Model->label),
            $Model->creator_id
        );
        $result = G::$M->query($query);
        if (false === $result) {
            return false;
        }
        $row = $result->fetch_assoc();
        if (0 != $row['@@LAST_INSERT_ID']) {
            $Model->{$Model->getPkey()} = $row['@@LAST_INSERT_ID'];
            // The stored procedure only accepts a few fields, use update for the rest
            parent::update($Model);
        }

        if (is_object($result)) {
            $result->close();
        }
        while (G::$M->more_results()) {
            G::$M->next_result();
        }

        return $Model->{$Model->getPkey()};
    }

    /**
     * Save data for passed model
     *
     * @param PassiveRecord $Model Model to save, passed by reference
     *
     * @return bool|null True on success, False on failure, Null on invalid attempt
     */
    public function update(PassiveRecord &$Model) {
        if (!is_a($Model, Node::class)) {
            return parent::insert($Model);
        }

        // If the PKey is not set, what would we update?
        if (null === $Model->{$Model->getPkey()}) {
            return null;
        }
        $diff = $Model->getDiff();

        // If no fields were set, this is unexpected
        if (0 == count($diff)) {
            return null;
        }

        if (isset($diff['parent_id']) || isset($diff['label'])) {
            // `usp_Tree_update`(IN _node_Id int, IN _login_id int, IN new_parent_id int, IN new_label varchar(255))
            $query  = sprintf("CALL `usp_Tree_update` (%d, %d, %d, '%s')",
                $Model->node_id,
                $Model->creator_id,
                $Model->parent_id,
                G::$M->escape_string($Model->label)
            );
            $result = G::$M->query($query);
            if (false === $result) {
                return false;
            }
        }

        return parent::update($Model);
    }

}
