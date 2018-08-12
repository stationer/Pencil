<?php
/**
 * ArboristWorkflow - For working the Node tree
 *
 * PHP version 7.0
 *
 * @package  Stationer\Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */

namespace Stationer\Pencil\libs;

use Stationer\Graphite\G;
use Stationer\Graphite\data\DataBroker;
use Stationer\Pencil\controllers\PencilController;
use Stationer\Pencil\models\Node;

/**
 * ArboristWorkflow - A workflow for handling trees
 *
 * PHP version 7.0
 *
 * @package  Stationer\Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 * @see      /src/models/Node.php
 */
class ArboristWorkflow {
    /** @var string */
    protected $root = '';
    /** @var string */
    protected $path = '';
    /** @var Node */
    protected $Node;
    /** @var array */
    protected $pathCache = ['' => 0];
    /** @var DataBroker */
    protected $DB;

    /**
     * ArboristWorkflow constructor.
     *
     * @param DataBroker $DB Optional DataBroker
     */
    public function __construct(DataBroker $DB = null) {
        $this->DB = $DB ?? G::build(DataBroker::class);
    }

    /**
     * Set the root path
     *
     * @param string $path Root path for all operations
     *
     * @return $this
     */
    public function setRoot(string $path) {
        $this->root = \fakepath($path, '/');
        $this->Node = null;

        return $this;
    }

    /**
     * Set the current path, relative to root path
     *
     * @param string $path New current path
     *
     * @return $this
     */
    public function setPath(string $path) {
        $this->path = \fakepath($path, '/');
        $this->Node = null;

        return $this;
    }

    /**
     * Create a Node at the current path
     *
     * @return bool|Node
     */
    public function create() {
        if (empty($this->path)) {
            return false;
        }

        $this->Node = $this->getByPath($this->path, true);

        return $this->Node;
    }

    /**
     * Fetch the tree Node corresponding to the specified path
     *
     * @param string $path   Path to seek
     * @param bool   $create Whether to create the Node when not found
     *
     * @return Node|bool
     */
    public function getByPath(string $path, bool $create = false) {
        $path = $this->root.\fakepath($path);
        // If we already have the node_id cached, load it.
        if (isset($this->pathCache[$path])) {
            return $this->getById($this->pathCache[$path]);
        }

        // We didn't have the node_id cached, climb the tree
        $labels    = explode('/', trim($path, '/'));
        $path      = '';
        $parent_id = 0;
        foreach ($labels as $label) {
            $path .= "/$label";
            // If the path-so-far is found, grab the node_id from cache
            if (isset($this->pathCache[$path])) {
                $parent_id = $this->pathCache[$path];
                continue;
            }
            // We're not cached, Instantiate the next child node
            $Node = G::build(Node::class, ['label' => $label, 'parent_id' => $parent_id]);
            // Try to fetch the Node from the DB
            $result = $this->DB->fill($Node);
            // If the Node doesn't exist, but we're supposed to create it
            if (false === $result && true == $create) {
                // Insert the Node
                $result = $this->DB->insert($Node);
            }
            // If $result is still false, Fail
            if (false === $result) {
                return false;
            }
            $parent_id = $this->pathCache[$path] = $Node->node_id;
        }

        return $Node;
    }

    /**
     * Return array of Nodes directly included in the current path.
     *
     * @return Node[]
     */
    public function getChildren() {
        if (null === $this->Node) {
            $this->Node = $this->getByPath($this->path);
        }
        $children = $this->DB->fetch(Node::class, ['parent_id' => $this->Node->node_id]);

        return $children;
    }

    /**
     * Get a Node by node_id
     *
     * @param int $node_id Node ID
     *
     * @return Node
     */
    public function getById(int $node_id) {
        return $this->DB->byPK(Node::class, $node_id);
    }

    /**
     * First, seek Node by its slug, then by its path
     *
     * @param string $url The slug or path to seek
     *
     * @return Node|bool
     */
    public function getByURL(string $url) {
        $result = $this->DB->fetch(Node::class, ['permalink' => $url]);
        if (false !== $result) {
            return array_pop($result);
        }

        return $this->getByPath(PencilController::WEBROOT.$url);
    }

    /**
     * Get Nodes by Content type and Id
     *
     * @param string $type Type of content to seek
     * @param int    $id   Optional content_id to seek
     *
     * @return Node{}|bool
     */
    public function getByContent(string $type, int $id = null) {
        return $this->DB->fetch(Node::class, ['content_id' => $id, 'contentType' => $type]);
    }

    /**
     * Move current node
     *
     * @param string|int $newParent Path or node_id of new parent
     *
     * @return bool
     */
    public function move($newParent) {
        if (null === $this->Node) {
            $this->Node = $this->getByPath($this->path);
        }
        if (!is_a($this->Node, Node::class)) {
            return false;
        }

        $Child = $this->Node;
        if (is_numeric($newParent)) {
            $Parent = $this->getById($newParent);
        } else {
            $Parent = $this->getByPath($newParent);
        }
        if (!is_a($Parent, Node::class)) {
            return false;
        }
        $Child->parent_id = $Parent->node_id;

        return true;
    }

    public function getContent() {


    }

    /**
     * Delete the node at the current path
     *
     * @return bool
     */
    public function delete(string $path = null) {
        // TODO: Handle recursive Delete
        if (null !== $path) {
            $this->setPath($path);
        }
        $this->Node = $this->getByPath($this->path, false);
        $ChildNodes = $this->getChildren();
        if (!empty($ChildNodes)) {
            return false;
        }
        if ($this->Node != false) {
            $this->DB->delete($this->Node);
        }

        $this->Node = null;

        return true;
    }

    public function copy($newLabel) {

    }
}
