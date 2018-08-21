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
use Stationer\Pencil\data\TreeMySQLDataProvider;
use Stationer\Pencil\models\Node;
use Stationer\Pencil\models\Tag;
use Stationer\Pencil\reports\AncestorsByPathReport;
use Stationer\Pencil\reports\DescendantsByPathReport;

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
    /** @var array [path => node_id] */
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
     * Returns the current root path
     *
     * @return string
     */
    public function getRoot() {
        return $this->root;
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
     * Returns the current relative path
     *
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Returns the current absolute path
     *
     * @return string
     */
    public function getFullPath() {
        return '/'.trim($this->root.$this->path, '/');
    }

    /**
     * Load a node by ID, set it as current Node
     *
     * @return $this
     */
    public function loadID(int $node_id) {
        /** @var Node $Node */
        $Node = $this->DB->byPK(Node::class, $node_id);
        if (empty($Node)) {
            return $this;
        }

        // Ensure node is within current root
        if (0 !== strpos($Node->path, $this->root)) {
            return $this;
        }

        // OK, we found a Node and it's under our root.  Use it.
        $this->path                   = $Node->path;
        $this->pathCache[$Node->path] = $Node->node_id;
        $this->Nodes                  = [$Node];

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
        $path = $this->getFullPath();

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
        $this->Nodes = $this->DB->fetch(AncestorsByPathReport::class, ['path' => $this->getFullPath()]) ?: [];
        return $this;
    }

    /**
     * Load Nodes containing the current path.
     *
     * @param string $path Optional new current path
     *
     * @return $this
     */
    public function descendants(string $path = null) {
        if (null !== $path) {
            $this->setPath($path);
        }
        $this->Nodes = $this->DB->fetch(DescendantsByPathReport::class, ['path' => $this->getFullPath()]) ?: [];

        return $this;
    }

    /**
     * Load Nodes containing the current path.
     *
     * @param string $tag  Single tag to search for under the current path
     * @param string $path Optional new current path
     *
     * @return $this
     */
    public function tagged(string $tag, string $path = null) {
        if (null !== $path) {
            $this->setPath($path);
        }
        $this->Nodes = $this->DB->fetch(DescendantsByPathReport::class, [
            'path' => $this->getFullPath(),
            'tag' => $tag,
        ]) ?: [];

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
        $path = $this->getFullPath();

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
     * @param string $path   Optional new current path
     * @param array  $values Optional attributes for created Node
     *
     * @return $this
     */
    public function create(string $path = null, $values = []) {
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

        if (is_a($Node, Node::class) && $Node->path == $this->getFullPath()) {
            $Node->setAll($values, true);
            if (isset($values['File'])) {
                $Node->File($values['File']);
            }
            $this->DB->update($Node);
        }

        return $this;
    }

    /**
     * Load the content associated with the current Nodes, attach to Node->File
     *
     * return $this
     */
    public function loadContent() {
        if (empty($this->Nodes)) {
            $this->load();
        }
        if (empty($this->Nodes)) {
            return $this;
        }

        $fetchList = [];
        // Group the content_ids for quicker fetching
        /** @var Node $Node */
        foreach ($this->Nodes as $Node) {
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
        $Tag = G::build(Tag::class, ['label' => $tag]);
        $this->DB->fill($Tag);
        if (0 == $Tag->tag_id) {
            $this->DB->insert($Tag);
        }
        if (0 == $Tag->tag_id) {
            return $this;
        }

        // TODO: Make a better way to do this
        $query = "
INSERT INTO `".G_DB_TABL."Node_Tag` (`tag_id`, `node_id`, `created_uts`)
VALUES ";
        $values = [];
        foreach ($this->Nodes as $Node) {
            $values[] = sprintf("('%d', '%d', '%d')", $Tag->tag_id, $Node->node_id, NOW);
        }
        G::$M->query($query.implode(', ', $values));

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
        // Fetch the tag
        $Tag = $this->DB->fetch(Tag::class, ['label' => $tag]);
        // If we didn't find the tag, we're done
        if (empty($Tag)) {
            return $this;
        }
        $Tag = reset($Tag);

        // TODO: Make a better way to do this
        $query = "
DELETE FROM `".G_DB_TABL."Node_Tag`
WHERE `tag_id` = '".((int)$Tag->tag_id)."'
  AND `node_id` IN (".implode(',', array_filter_ids(array_column($this->Nodes, 'node_id'))).")
";
        G::$M->query($query);

        return $this;
    }

    /**
     * Move current node
     *
     * @param string|int $newParent Path or node_id of new parent
     *
     * @return $this
     */
    public function move($newParent) {
        // Prepare current Node(s)
        if (empty($this->Nodes)) {
            $this->load();
        }
        // Prepare destination Node
        if (is_numeric($newParent)) {
            $Parent = $this->getById($newParent);
        } else {
            $Parent = $this->getByPath($newParent);
        }
        if (!is_a($Parent, Node::class)) {
            return $this;
        }
        foreach ($this->Nodes as $Child) {
            if (!is_a($Child, Node::class)) {
                continue;
            }

            $Child->parent_id = $Parent->node_id;
            $this->DB->update($Child);
        }

        return $this;
    }

    /**
     * Delete the current Node(s)
     *
     * @param string $path      Optional new current path
     * @param bool   $recursive Whether deletes should include nodes with children
     *
     * @return $this
     */
    public function delete(string $path = null, bool $recursive = false) {
        if (null !== $path) {
            $this->load($path);
        }
        if (empty($this->Nodes)) {
            $this->load();
        }
        foreach ($this->Nodes as $key => $Node) {
            TreeMySQLDataProvider::$nextDeleteRecursive = true;
            $result                                     = $this->DB->delete($Node);
            if (true === $result) {
                unset($this->Nodes[$key]);
            }
        }

        return $this;
    }

    public function copy($newLabel) {

    }

    /**
     * Simply return the current Nodes array
     *
     * @param int $count How many nodes to return, null for all
     *
     * @return Node[]
     */
    public function get(int $count = null) {
        return array_slice($this->Nodes, 0, $count);
    }

    /**
     * Simply return the first Node
     *
     * @return Node
     */
    public function first() {
        return reset($this->Nodes);
    }

    /**
     * Fetch the tree Node corresponding to the specified path
     *
     * @param string $path Path to seek
     *
     * @return Node|bool
     */
    public function getByPath(string $path) {
        return $this->DB->fetch(Node::class, ['path' => $this->getFullPath()]);
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
     * Given a list of Nodes, fetch and attach their respective content records
     *
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

    /**
     * Produce a summary of nodes expressed as an array of paths
     *
     * @param array|null $Nodes
     *
     * @return array
     */
    public function getNodeSummary(array $Nodes = null) {
        if (null === $Nodes) {
            $Nodes = $this->Nodes;
        }
        $result = [];
        foreach ($Nodes as $Node) {
            $result[$Node->node_id] = $Node->path;
        }

        return $result;
    }
}
