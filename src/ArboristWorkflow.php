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
use Stationer\Pencil\models\Article;
use Stationer\Pencil\models\Asset;
use Stationer\Pencil\models\Node;
use Stationer\Pencil\models\Page;
use Stationer\Pencil\models\Site;
use Stationer\Pencil\models\Submission;
use Stationer\Pencil\models\Tag;
use Stationer\Pencil\reports\AncestorsByPathReport;
use Stationer\Pencil\reports\DescendantsByPathReport;
use Stationer\Pencil\reports\NodeReport;

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
    /** @var Node */
    protected $RootNode = null;
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
        $this->root  = Node::cleanPath($path);
        $this->Nodes = [];
        $result = $this->DB->fetch(Node::class, ['path' => $this->root]);
        if (!empty($result)) {
            $this->RootNode = reset($result);
        }

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
        // If the path starts with the root, assume it's an absolute path and make it relative
        if (0 === strpos($path, $this->getRoot())) {
            $path = substr($path, strlen($this->getRoot()));
        }

        $this->path  = Node::cleanPath($path);
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
     * @param int $node_id ID of Node to load
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
     * Load a node by ID, set it as current Node
     *
     * @param array $filters Search Parameters
     *
     * @return $this
     */
    public function search(array $filters) {
        if (!$this->RootNode) {
            return $this;
        }

        $filters['min_left'] = $this->RootNode->left_index;
        $filters['max_right'] = $this->RootNode->right_index;
        /** @var Node[] $Nodes */
        $Nodes = $this->DB->fetch(NodeReport::class, $filters);

        $this->Nodes = $Nodes;

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
     * @param string $path    Optional new current path
     * @param array  $filters Optional additional filters
     *
     * @return $this
     */
    public function children(string $path = null, array $filters = []) {
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
            // Adding the star will indicate the previous path is no longer current
            // $this->path.='/*';
            $filters['parent_id'] = $node_id;
            $this->Nodes = $this->DB->fetch(Node::class, $filters);
            $this->pathCache += array_combine(array_column($this->Nodes, 'path'),
                array_column($this->Nodes, 'node_id'));
        }

        return $this;
    }

    /**
     * Load Nodes containing the current path, including the current path
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
     * Reduce the current Nodes to only the first Node
     *
     * @param string $path Optional new current path
     *
     * @return $this
     */
    public function first(string $path = null) {
        if (null !== $path) {
            $this->setPath($path);
        }
        $this->Nodes = [reset($this->Nodes)];

        return $this;
    }

    /**
     * Reduce the current Nodes to only the first Node
     *
     * @param string $path Optional new current path
     *
     * @return $this
     */
    public function last(string $path = null) {
        if (null !== $path) {
            $this->setPath($path);
        }
        $this->Nodes = [end($this->Nodes)];

        return $this;
    }

    /**
     * Load Nodes containing the current path, but not the current path
     *
     * @param string $path Optional new current path
     *
     * @return $this
     */
    public function line(string $path = null) {
        if (null !== $path) {
            $this->setPath($path);
        }
        $this->Nodes = $this->DB->fetch(AncestorsByPathReport::class, ['line' => $this->getFullPath()]) ?: [];

        return $this;
    }

    /**
     * Load Nodes containing the current path.
     *
     * @param string $path    Optional new current path
     * @param array  $filters Optional additional filters
     *
     * @see DescendantsByPathReport
     *
     * @return $this
     */
    public function descendants(string $path = null, array $filters = []) {
        if (null !== $path) {
            $this->setPath($path);
        }

        $filters['path'] = $this->getFullPath();
        // Adding the stars will indicate the previous path is no longer current
        // $this->path .= '/*';
        $this->Nodes = $this->DB->fetch(DescendantsByPathReport::class, $filters) ?: [];

        return $this;
    }

    /**
     * Load Nodes containing the current path.
     *
     * @param string $path    Optional new current path
     * @param array  $filters Optional additional filters
     *
     * @see DescendantsByPathReport
     *
     * @return $this
     */
    public function subtree(string $path = null, array $filters = []) {
        if (null !== $path) {
            $this->setPath($path);
        }

        $filters['line'] = $this->getFullPath();
        // Adding the stars will indicate the previous path is no longer current
        // $this->path .= '/*';
        $this->Nodes = $this->DB->fetch(DescendantsByPathReport::class, $filters) ?: [];

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
        return $this->descendants($path, ['tag' => $tag]);
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
        $path = $this->getFullPath();

        // Get ancestors to requested path, sorted by deepest first
        $AncestorNodes = $this->line()->get();

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
            trigger_error('Tree Root Node not found, attempting to create. '.
                ' If this is your first run of Pencil, you can safely ignore this message.');
            $this->createRootNode();

            $progress  = '';
            $parent_id = 1;
        }
        // If we have a specified root which is not found in the ancestors, fail hard
        if ('/' != $this->getRoot() && 0 !== strpos($progress, $this->getRoot())) {
            // TODO: Have a debate about Exceptions
            trigger_error("Cannot create node outside of specified root: ".$this->getRoot()
                ." while trying to create $path", E_USER_ERROR);
            die;
        }

        // Climb the tree, creating as we go
        $progress = substr($progress, strlen($this->root));
        $labels   = explode('/', trim(substr($path, strlen($this->root) + strlen($progress)), '/'));
        foreach ($labels as $label) {
            $progress .= "/$label";
            // Instantiate the next child node
            $Node = G::build(Node::class, ['label' => $label, 'parent_id' => $parent_id]);
            // Insert the Node
            $result = $this->DB->insert($Node);
            // TODO Elegantly handle duplicate parent_id-label pairs
            // If $result is still false, Fail
            if (false === $result) {
                $this->Nodes = [false];
                break;
            }
            // Add the last Node to the collection
            $this->Nodes = [$Node];
            // Update the parent_id for the next insert
            $parent_id = $this->pathCache[$progress] = $Node->node_id;
        }

        if (isset($Node) && is_a($Node, Node::class) && $Node->path == $this->getFullPath()) {
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
            //$this->load();
        }
        if (empty($this->Nodes)) {
            return $this;
        }

        $fetchList = [];
        // Group the content_ids for quicker fetching
        /** @var Node $Node */
        foreach ($this->Nodes as $Node) {
            if (false === $Node) {
                break;
            }
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
            if (false === $Node) {
                break;
            }
            $this->Nodes[$key]->File = $fetchList[$Node->contentType][$Node->content_id];
        }

        return $this;
    }

    /**
     * Add specified tag to current Nodes
     *
     * @param string $tag
     * @param string $type
     *
     * @return $this
     */
    public function tag(string $tag, string $type = null) {
        $Tag = G::build(Tag::class, ['label' => $tag, 'type' => $type]);
        $this->DB->fill($Tag);
        if (0 == $Tag->tag_id) {
            $this->DB->insert($Tag);
        }
        if (0 == $Tag->tag_id) {
            return $this;
        }

        // TODO: Make a better way to do this
        $query  = "
INSERT IGNORE INTO `".G_DB_TABL."Node_Tag` (`tag_id`, `node_id`, `created_uts`)
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
     * @param string $type
     *
     * @return $this
     */
    public function untag(string $tag, string $type = null) {
        // Fetch the tag
        $Tag = $this->DB->fetch(Tag::class, ['label' => $tag, 'type' => $type]);
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
        if (is_a($newParent, Node::class)) {
            $Parent = $newParent;
        } elseif (is_numeric($newParent)) {
            $Parent = $this->getById($newParent);
        } else {
            $Parent = $this->getByPath($newParent);
        }
        if (!is_a($Parent, Node::class)) {
            trigger_error("Invalid parent specified in ".__METHOD__);

            return $this;
        }
        if (1 < count($this->Nodes)) {
            trigger_error('Cowardly refusing to move more than one Node.');
            croak($this->Nodes);
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
     * Update current nodes with passed values
     *
     * @param string|null $path
     * @param             $values
     *
     * @return $this
     */
    public function update(string $path, $values) {
        if (null !== $path) {
            $this->load($path);
        }
        if (empty($this->Nodes)) {
            $this->load();
        }

        foreach ($this->Nodes as $Node) {
            $Node->setAll($values, true);
            $this->DB->update($Node);
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
            TreeMySQLDataProvider::$nextDeleteRecursive = $recursive;
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
        return array_slice($this->Nodes, 0, $count, true);
    }

    /**
     * Simply return the first Node
     *
     * @return Node
     */
    public function getFirst() {
        return reset($this->Nodes);
    }

    /**
     * Simply return the first Node
     *
     * @return Node
     */
    public function getLast() {
        return end($this->Nodes);
    }

    /**
     * Fetch the tree Node corresponding to the specified path
     *
     * @param string $path Path to seek
     *
     * @return Node|bool
     */
    public function getByPath(string $path) {
        // If the path starts with the root, assume it's an absolute path and make it relative
        if (0 === strpos($path, $this->getRoot())) {
            $path = substr($path, strlen($this->getRoot()));
        }

        $result = $this->DB->fetch(Node::class, ['path' => '/'.trim($this->root.$path, '/')], [], 1);
        if (false !== $result && !empty($result)) {
            return array_pop($result);
        }

        return false;
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
        if (false !== $result && !empty($result)) {
            return array_pop($result);
        }

        $result = $this->getByPath(PencilController::WEBROOT.$url);
        if (false !== $result && !empty($result)) {
            return $result;
        }

        $result = $this->getByPath($url);
        if (false !== $result && !empty($result)) {
            return $result;
        }

        return false;
    }

    /**
     * Get Nodes by Content type and Id
     *
     * @param string $type Class of content to seek
     * @param int    $id   Optional content_id to seek
     *
     * @return Node[]|bool
     */

    public function getByContent(string $type, int $id = null) {
        $pos = strrpos($type, '\\');
        if (false !== $pos) {
            $type = substr($type, $pos + 1);
        }

        return $this->DB->fetch(Node::class, ['content_id' => $id, 'contentType' => $type]);
    }

    /**
     * Given a list of Nodes, fetch and attach their respective content records
     *
     * @param $Nodes
     *
     * @return Node[]
     */
    public function getFilesForNodes(array $Nodes) {
        if (!empty($Nodes)) {
            $fetchList = [];
            // Group the content_ids for quicker fetching
            /** @var Node $Node */
            foreach ($Nodes as $Node) {
                if (false === $Node) {
                    break;
                }
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
                if (false === $Node) {
                    break;
                }
                $Nodes[$key]->File = $fetchList[$Node->contentType][$Node->content_id];
            }
        }

        return $Nodes;
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

    /**
     * Create the aboslute root node of the tree
     *
     * @return $this
     */
    public function createRootNode() {
        G::$M->query("CALL `usp_Tree_insert`(0, '', 1)");
        while (G::$M->more_results()) {
            G::$M->next_result();
        }

        return $this;
    }

    /**
     * WIP - not working
     * Rebuild the tree's indexing and paths by touching every Node
     *
     * @param int $parent_id Node to start with
     */
    public function rebuild($parent_id = 1) {
        if (1) {
            return;
        }
        $token = sha1(uniqid());
        $Nodes = $this->DB->fetch(Node::class, ['parent_id' => $parent_id], ['ordinal' => true, 'left_index' => true]);
        foreach ($Nodes as $Node) {
            $label = $Node->label;
            $Node->label = $token;
            $this->DB->save($Node);
            $Node->label = $label;
            $this->DB->save($Node);
            $this->rebuild($Node->node_id);
        }
    }

    /**
     * WIP - not working
     * Rebuild the tree's indexing and paths by touching every Node
     *
     * @return void
     */
    public function reindex() {
        if (1) {
            return;
        }

        // reset all indexes
        $query = "UPDATE `".Node::getTable()."` SET path='', left_index=0, right_index=0";
        G::$M->query($query);

        $Node              = $this->DB->byPK(Node::class, 1);
        $Node->left_index  = 1;
        $index             = $this->reindex_sub($Node, 2);
        $Node->right_index = $index;
        if ($Node->getDiff()) {
            $query = "UPDATE `".Node::getTable()."` SET"
                ." `path` = '".G::$M->escape_string($Node->path)."'"
                .",`left_index`=".$Node->left_index
                .",`right_index`=".$Node->right_index
                ." WHERE `node_id`=".$Node->node_id;
            echo $query.';<br>';
        }
        croak(G::$M->getQueries());
        exit;
    }

    /**
     * WIP - not working
     * Rebuild the tree's indexing and paths by touching every Node
     *
     * @param Node $ParentNode Node to work under
     * @param int  $index      Current traversal index
     *
     * @return mixed|void
     */
    protected function reindex_sub($ParentNode, $index) {
        if (1) {
            return;
        }

        /** @var Node[] $Nodes */
        $Nodes = $this->DB->fetch(Node::class, ['parent_id' => $ParentNode->node_id], ['ordinal' => true]);
        foreach ($Nodes as $kid) {
            $path        = $kid->path;
            $left_index  = $kid->left_index;
            $right_index = $kid->right_index;

            $kid->path        = $ParentNode->path.'/'.$kid->label;
            $kid->left_index  = $index++;
            $index            = $this->reindex_sub($kid, $index);
            $kid->right_index = $index++;

            if ($kid->getDiff()) {
                $query = [];
                if ($path != $kid->path) {
                    $query[] = " `path` = '".G::$M->escape_string($kid->path)."'";
//                    echo $path.' -> '.$kid->path.'<br>';
                }
                if ($left_index != $kid->left_index) {
                    $query[] = " `left_index` = '".G::$M->escape_string($kid->left_index)."'";
//                    echo $left_index.' -> '.$kid->left_index.'<br>';
                }
                if ($right_index != $kid->right_index) {
                    $query[] = " `right_index` = '".G::$M->escape_string($kid->right_index)."'";
//                    echo $right_index.' -> '.$kid->right_index.'<br>';
                }

                $query = "UPDATE `".Node::getTable()."` SET"
                    .implode(',', $query)
                    ." WHERE `node_id`=".$kid->node_id;
                echo $query.';<br>';
                G::$M->query($query);
            }
        }
        flush();

        return $index;
    }

    /**
     * Generate a Zip archive for current tree root
     *
     * @return string Filename of the generated Zip archive
     */
    public function getExport() {
        $Zip = new \ZipArchive();
        // Get all Nodes, without Files
        $Nodes = $this->subtree()->get();
        $filename = SITE.AssetManager::$uploadPath.'/'.reset($Nodes)->label.'.zip';
        $result = $Zip->open($filename, \ZipArchive::CREATE);
        if (true !== $result) {
            return false;
        }
        $Zip->addFromString('tables/Node.csv', array_to_csv($Nodes));
        $contentTypes = [];
        foreach ($Nodes as $Node) {
            $contentTypes[$Node->contentType][] = $Node->content_id;
        }
        unset($contentTypes['']);
        foreach ($contentTypes as $contentType => $content_ids) {
            $Files = $this->DB->byPK('\\Stationer\\Pencil\\models\\'.$contentType, $content_ids);
            if (!empty($Files)) {
                $Zip->addFromString('tables/'.$contentType.'.csv', array_to_csv($Files));
            }
        }
        $Tags = $this->DB->fetch(Tag::class, []);
        if (!empty($Tags)) {
            $Zip->addFromString('tables/Tag.csv', array_to_csv($Tags));
        }
        // TODO get Node_Tag data

        $assetPath = SITE.AssetManager::$uploadPath.$this->getRoot();
        $assetPathLen = strlen($assetPath);
        exec('find '.$assetPath, $fileList);
        foreach ($fileList as $file) {
            if (is_file($file) && is_readable($file)) {
                $Zip->addFile($file, substr($file, $assetPathLen));
            }
        }

        $Zip->close();

        return $filename;
    }

    /**
     * WIP
     * Return an exportable format of all tree data.
     *
     * @return array to be JSON encoded
     */
    public function _getExport() {
        $data = [];
        $nodeKeys = [
            'contentType' => 1, 'label' => 1, 'keywords' => 1, 'description' => 1, 'published' => 1, 'trashed' => 1,
            'featured'    => 1, 'pathAlias' => 1, 'ordinal' => 1,
        ];
        $paths = [];
        $rootLen = strlen($this->getRoot());
        $assetLen = strlen(AssetManager::$uploadPath) + $rootLen;

        $Nodes = $this->load()->loadContent()->get();

        while (!empty($Nodes)) {
            // Get the next Node
            $Node = array_shift($Nodes);
            // Add the Node to the export
            $paths[$Node->node_id] = $Node->path;
            $node = array_intersect_key($Node->getAll(), $nodeKeys);
            $node['path'] = substr($Node->path, $rootLen);
            // Add the File
            if ($Node->File) {
                $node['File'] = $Node->File->getAll();
            }
            $data[$Node->node_id] = $node;

            // Add child Nodes to the loop array
            $this->setPath($Node->path);
            $this->Nodes = [$Node];
            $Nodes += $this->children()->loadContent()->get();
        }

        foreach ($data as $key => $node) {
            switch (get_class($node['contentType'])) {
                case 'Article':
                    // TODO Figure how to translate login_id
                    break;
                case 'Asset':
                    $node['File']['path'] = substr($node['File']['path'], $assetLen);
                    break;
                case 'Page':
                    $node['File']['template_id'] = $data[$node['File']['template_id']]['path'];
                    break;
                case 'Site':
                    $node['File']['theme_id'] = $data[$node['File']['theme_id']]['path'];
                    $node['File']['defaultPage_id'] = $data[$node['File']['defaultPage_id']]['path'];
                    break;
                case 'Submission':
                    $node['File']['form_id'] = $data[$node['File']['form_id']]['path'];
                    break;
                default:
                    break;
            }
            $data[$key] = $node;
        }

        return $data;
    }
}
