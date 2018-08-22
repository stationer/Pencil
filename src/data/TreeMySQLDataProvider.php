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
    /** @var bool Whether next delete should be recursive */
    public static $nextDeleteRecursive = false;

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
        $query  = sprintf("CALL `usp_Tree_insert` (%d, '%s', %d)",
            $Model->parent_id,
            G::$M->escape_string($Model->label),
            $Model->creator_id
        );
        $result = G::$M->query($query);
        while (G::$M->more_results()) {
            G::$M->next_result();
        }
        if (false === $result) {
            return false;
        }
        $row = $result->fetch_assoc();

        if (0 != $row['@@LAST_INSERT_ID']) {
            $Model->{$Model->getPkey()} = $row['@@LAST_INSERT_ID'];
            // Unset the three values covered by the stored procedure
            unset($diff['node_id']);
            unset($diff['parent_id']);
            unset($diff['label']);
            // The stored procedure only accepts a few fields, use update for the rest
            if (!empty($diff)) {
                parent::update($Model);
            }
        }
        if (!empty($row['new_path'])) {
            $Model->path = $row['new_path'];
            $Model->parent_id = $row['new_parent_id'];
            $Model->left_index = $row['new_left_index'];
            $Model->right_index = $row['new_right_index'];
            $Model->undiff(['parent_id', 'label', 'path', 'left_index', 'right_index']);
        }

        if (is_object($result)) {
            $result->close();
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

        /** @var Node $Model */
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
            while (G::$M->more_results()) {
                G::$M->next_result();
            }
            if (false === $result) {
                return false;
            }
            $row = $result->fetch_assoc();
            $Model->path = $row['path'];
            $Model->left_index = $row['left_index'];
            $Model->right_index = $row['right_index'];
            $Model->undiff(['parent_id', 'label', 'path', 'left_index', 'right_index']);
        }

        return parent::update($Model);
    }

    /**
     * Delete data for passed model
     *
     * @param PassiveRecord $Model Model to delete, passed by reference
     *
     * @return bool|null True on success, False on failure, Null on invalid attempt
     */
    public function delete(PassiveRecord &$Model) {
        if (!is_a($Model, Node::class)) {
            return parent::delete($Model);
        }

        // If the PKey is not set, what would we delete?
        if (null === $Model->{$Model->getPkey()}) {
            return null;
        }

        // `usp_Tree_delete`(IN old_node_Id int, IN _login_id int, IN recursive bool)
        $query = sprintf("CALL `usp_Tree_delete` (%d, %d, %d)",
            $Model->node_id,
            $Model->creator_id,
            static::$nextDeleteRecursive ? 1 : 0
        );

        static::$nextDeleteRecursive = false;

        $result = G::$M->query($query);
        while (G::$M->more_results()) {
            G::$M->next_result();
        }

        return $result;
    }
}
