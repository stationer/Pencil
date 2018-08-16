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

namespace Stationer\Pencil;

use Stationer\Graphite\G;
use Stationer\Graphite\data\DataBroker;
use Stationer\Pencil\models\Node;
use Stationer\Pencil\reports\NodeParentByPathReport;

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
    protected $path = '/';
    /** @var Node[] */
    protected $Nodes = [];
    /** @var array */
    protected $pathCache = ['' => 1];
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
        $this->root  = \fakepath($path, '/');
        $this->Nodes = [];

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
        $this->path  = \fakepath($path, '/');
        $this->Nodes = [];

        return $this;
    }

    /**
     * Set the current path, relative to root path. to the parent of the current path
     *
     * @param string $path Optional new current path
     *
     * @return $this
     */
    public function parent(string $path = null) {
        if (null !== $path) {
            $this->setPath($path);
        }

        // Take the substring of the path from the start to the last slash
        // $this->path = substr($this->path, 0, strrpos($this->path, '/')) ?: '/';
        $this->path  = dirname($this->path) ?: '/';
        $this->Nodes = [];

        return $this;
    }

    /**
     * Load Nodes directly included in the current path.
     *
     * @param string $path Optional new current path
     *
     * @return $this
     */
    public function children(string $path = null) {
        if (null !== $path) {
            $this->setPath($path);
        }

        // If we have the current path's node_id cached, use it
        if (isset($this->pathCache[$path])) {
            $node_id = $this->pathCache[$path];
        } else {
            // else load the current path to get the node_id
            if (empty($this->Nodes)) {
                $this->load();
            }
            $Node = reset($this->Nodes);
            if (is_a($Node, Node::class)) {
                $node_id = reset($this->Nodes)->node_id;
            }
        }

        // Use false to indicate a failure to load, distinct from empty success
        $this->Nodes = [false];
        if (isset($node_id)) {
            $this->Nodes = $this->DB->fetch(Node::class, ['parent_id' => $node_id]);
        }


        return $this;
    }

    /**
     * Load Nodes containing the current path.
     *
     * @param string $path Optional new current path
     *
     * @return $this
     */
    public function ancestors(string $path = null) {
        if (null !== $path) {
            $this->setPath($path);
        }
        $this->Nodes = $this->DB->fetch(NodeParentByPathReport::class, ['path' => $this->root.$this->path]) ?: [];

        return $this;
    }

    /**
     * Fetch the tree Node corresponding to the current path
     *
     * @param string $path Optional new current path
     *
     * @return $this
     */
    public function load(string $path = null) {
        if (null !== $path) {
            $this->setPath($path);
        }
        $path = $this->root.\fakepath($this->path);

        // If we already have the node_id cached, load it.
        if (isset($this->pathCache[$path])) {
            $this->Nodes = [$this->getById($this->pathCache[$path])];

            return $this;
        }

        // Try to load the node by the indexed path
        $result = $this->DB->fetch(Node::class, ['path' => $path]);
        if (!empty($result)) {
            $Node = array_pop($result);
            if (is_a($Node, Node::class)) {
                $this->pathCache[$path] = $Node->node_id;
                $this->Nodes            = [$Node];

                return $this;
            }
        }

        return $this;
    }

    /**
     * Create a Node at the current path
     *
     * @param string $path Optional new current path
     *
     * @return $this
     */
    public function create(string $path = null) {
        if (null !== $path) {
            $this->setPath($path);
        }
        $path = $this->path;

        // Get ancestors to requested path, sorted by deepest first
        $AncestorNodes = $this->ancestors()->get();

        // If No nodes came back, we are starting from the bottom
        if (!empty($AncestorNodes)) {
            $Node = reset($AncestorNodes);
            // If the path already exists we are done
            if ($path == $Node->path) {
                return $this;
            }
            $progress  = $Node->path;
            $parent_id = $Node->node_id;
        } else {
            $progress  = '';
            $parent_id = 1;
        }
        $progress = substr($progress, strlen($this->root));

        // Climb the tree, creating as we go
        $labels = explode('/', trim(substr($path, strlen($progress)), '/'));
        foreach ($labels as $label) {
            $progress .= "/$label";
            // Instantiate the next child node
            $Node = G::build(Node::class, ['label' => $label, 'parent_id' => $parent_id]);
            // Insert the Node
            $result = $this->DB->insert($Node);
            // If $result is still false, Fail
            if (false === $result) {
                $this->Nodes[] = false;
                break;
            }
            // Add the next Node to the collection
            $this->Nodes[] = $Node;
            // Update the parent_id for the next insert
            $parent_id = $this->pathCache[$progress] = $Node->node_id;
        }

        return $this;
    }

    /**
     * @param $Nodes
     */
    public function loadFiles() {
        if (empty($this->Nodes)) {
            return $this;
        }
        G::croak($this->Nodes);
        $fetchList = [];
        // Group the content_ids for quicker fetching
        /** @var Node $Node */
        foreach ($this->Nodes as $Node) {
            $fetchList[$Node->contentType][$Node->content_id] = null;
            echo $Node->contentType;
        }
        echo "<br>";
        // Fetch all records for each type
        foreach ($fetchList as $type => $ids) {
            if ('' == $type) {
                continue;
            }
            echo $type;
            $fetchList[$type] = $this->DB->byPK('\\Stationer\\Pencil\\models\\'.$type, array_keys($ids));
            G::croak($fetchList[$type]);
        }
        echo "<br>";
        // Add the records to their Nodes
        foreach ($this->Nodes as $key => $Node) {
            $this->Nodes[$key]->File = $fetchList[$Node->contentType][$Node->content_id];
        }

        return $this;
    }

    /**
     * Add specified tag to current Nodes
     *
     * @param string $tag
     *
     * @return $this
     */
    public function tag(string $tag) {
        trigger_error("Unfinished function ".__METHOD__);

        return $this;
    }

    /**
     * Remove specified tag from current Nodes
     *
     * @param string $tag
     *
     * @return $this
     */
    public function untag(string $tag) {
        trigger_error("Unfinished function ".__METHOD__);

        return $this;
    }

    /**
     * Simply return the current Nodes array
     *
     * @return Node[]
     */
    public function get() {
        return $this->Nodes;
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
        $this->load($path);
        if (!empty($this->Nodes)) {
            return reset($this->Nodes);
        }

        // If we didn't find the node, and we're not supposed to create it, fail
        if (false === $create) {
            return false;
        }

        $this->create($path);
        if (!empty($this->Nodes)) {
            return reset($this->Nodes);
        }

        return false;
    }

    /**
     * Return array of Nodes directly included in the current path.
     *
     * @param bool $fetchFiles Whether to also fetch content files
     *
     * @return Node[]
     */
    public function getChildren($fetchFiles = false) {
        if (null === $this->Node) {
            $this->Node = $this->getByPath($this->path);
        }
        $children = $this->DB->fetch(Node::class, ['parent_id' => $this->Node->node_id]);

        if ($fetchFiles) {
            $this->getFilesForNodes($children);
        }

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
        $result = $this->DB->fetch(Node::class, ['pathAlias' => $url]);
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

    /**
     * @param $Nodes
     */
    public function getFilesForNodes(array $Nodes) {
        if (!empty($Nodes)) {
            $fetchList = [];
            // Group the content_ids for quicker fetching
            /** @var Node $Node */
            foreach ($Nodes as $Node) {
                $fetchList[$Node->contentType][$Node->content_id] = null;
            }
            // Fetch all records for each type
            foreach ($fetchList as $type => $ids) {
                if ('' == $type) {
                    continue;
                }
                $fetchList[$type] = $this->DB->byPK('\\Stationer\\Pencil\\models\\'.$type, array_keys($ids));
            }
            // Add the records to their Nodes
            foreach ($Nodes as $key => $Node) {
                $Nodes[$key]->File = $fetchList[$Node->contentType][$Node->content_id];
            }
        }
    }
}
