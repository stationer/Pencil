<?php
/**
 * P_TestController - Test Controller
 *
 * PHP version 7.0
 *
 * @package  Stationer\Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */

namespace Stationer\Pencil\controllers;

use Stationer\Graphite\G;
use Stationer\Graphite\View;
use Stationer\Graphite\data\IDataProvider;
use Stationer\Pencil\PencilController;
use Stationer\Pencil\reports\DescendantsByPathReport;

/**
 * Class P_TestController
 *
 * @package  Stationer\Pencil\controllers
 * @category Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */
class P_TestController extends PencilController {
    /** @var string Default action */
    protected $action = 'tree';

    /**
     * Controller constructor
     *
     * @param array         $argv Argument list passed from Dispatcher
     * @param IDataProvider $DB   DataProvider to use with Controller
     * @param View          $View Graphite View helper
     */
    public function __construct(array $argv = [], IDataProvider $DB = null, View $View = null) {
        parent::__construct($argv, $DB, $View);
    }

    /**
     * Page for listing all pages
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_tree(array $argv = [], array $request = []) {
        if (!G::$S->roleTest($this->role)) {
            return parent::do_403($argv);
        }

        $results = [];
        // This should fail on a duplicate entry for key, if the root node exists already
        $this->Tree->setRoot('');
        $Nodes = $this->Tree->ancestors('/')->get();
        if (empty($Nodes)) {
            $this->Tree->createRootNode();
            $results[] = ['create root', G::$M->getLastQuery()['rows']];
        } else {
            $results[] = ['found root', end($Nodes)->toArray()];
        }

        // This block should delete the /test tree and recreate a /test node
        $this->Tree->setRoot('');
        $results[] = ['setRoot/getRoot', $this->Tree->getRoot()];
        $this->Tree->setPath('/test');
        $results[] = ['setPath/getPath', $this->Tree->getPath()];
        $results[] = ['getFullPath', $this->Tree->getfullPath()];
        $this->Tree->delete(null, true)->create();
        $results[] = ['recreate', null];
        $Nodes     = $this->DB->fetch(DescendantsByPathReport::class, ['path' => $this->Tree->getRoot()]);
        $results[] = ['Nodes', array_column($Nodes, 'path')];

        // This block should create a single node at /tree/node1
        $this->Tree->setRoot('/test');
        $results[] = ['setRoot/getRoot', $this->Tree->getRoot()];
        $this->Tree->setPath('/node1');
        $results[] = ['setPath/getPath', $this->Tree->getPath()];
        $results[] = ['getFullPath', $this->Tree->getfullPath()];
        $this->Tree->create();
        $results[] = ['recreate', $this->Tree->getFirst()->toArray()];
        $Nodes     = $this->DB->fetch(DescendantsByPathReport::class, ['path' => $this->Tree->getRoot()]);
        $results[] = ['Nodes under '.$this->Tree->getRoot(), array_column($Nodes, 'path')];

        // This block should create a single node at /tree/node22
        $this->Tree->setRoot('/test');
        $results[] = ['setRoot/getRoot', $this->Tree->getRoot()];
        $this->Tree->setPath('/node22');
        $results[] = ['setPath/getPath', $this->Tree->getPath()];
        $results[] = ['getFullPath', $this->Tree->getfullPath()];
        $this->Tree->create();
        $results[] = ['recreate', null];
        $Nodes     = $this->DB->fetch(DescendantsByPathReport::class, ['path' => $this->Tree->getRoot()]);
        $results[] = ['Nodes under '.$this->Tree->getRoot(), array_column($Nodes, 'path')];

        // This block should create a chain of nodes in one go
        $this->Tree->create('/node2/3/4/5/6/7');
        $results[] = ['create /node2/3/4/5/6/7', null];
        $Nodes     = $this->DB->fetch(DescendantsByPathReport::class, ['path' => $this->Tree->getRoot()]);
        $results[] = ['Nodes under '.$this->Tree->getRoot(), $this->Tree->getNodeSummary($Nodes)];

        // This block should tag the descendants of /node2
        $this->Tree->descendants('/node2')->tag('test');
        $results[] = ['tag', G::$M->getLastQuery()['rows']];
        $Nodes     = $this->Tree->setPath('')->tagged('test')->get();
        $results[] = ['Nodes tagged test', $this->Tree->getNodeSummary($Nodes)];

        // This block should untag the descendants of /node2/3
        $this->Tree->descendants('/node2/3')->untag('test');
        $results[] = ['untag', G::$M->getLastQuery()['rows']];
        $Nodes     = $this->Tree->setPath('')->tagged('test')->get();
        $results[] = ['Nodes still tagged test', $this->Tree->getNodeSummary($Nodes)];

        // This block fetch ancestors of /node2/3/4/5
        $Nodes     = $this->Tree->ancestors('/node2/3/4/5')->get();
        $results[] = ['Nodes above '.$this->Tree->getPath(), $this->Tree->getNodeSummary($Nodes)];

        // This block fetch line of /node2/3/4/5
        $Nodes     = $this->Tree->line('/node2/3/4/5')->get();
        $results[] = ['Nodes above and including '.$this->Tree->getPath(), $this->Tree->getNodeSummary($Nodes)];

        // This block tests case-insensitivity
        $Nodes     = $this->Tree->load('/NodE2')->get();
        $results[] = ['Case-insensitively load /NodE2', $this->Tree->getNodeSummary($Nodes)];

        // This block tests move
        $Nodes     = $this->Tree->descendants('/node2/3')->move('/node1')->get();
        $results[] = ['Move nodes to /node1', $this->Tree->getNodeSummary($Nodes)];

        // Delete the test nodes, but not the test root
        $this->Tree->setPath('')->descendants()->delete(null, true);
        $results[] = ['delete', G::$M->getLastQuery()['rows']];
        $Nodes = $this->DB->fetch(DescendantsByPathReport::class, ['path' => $this->Tree->getRoot()]);
        $results[] = ['Nodes under '.$this->Tree->getRoot(), $this->Tree->getNodeSummary($Nodes)];

        // This block tests the Ancestor Report
        $this->Tree->create('/line1')->create('/line2/line3')->create('/line4')->create('/line2.line4');
        $Nodes     = $this->Tree->ancestors('/line1')->get();
        $results[] = ['Ancestor Test of Line1', $this->Tree->getNodeSummary($Nodes)];
        $Nodes     = $this->Tree->ancestors('/line2')->get();
        $results[] = ['Ancestor Test of Line2', $this->Tree->getNodeSummary($Nodes)];
        $Nodes     = $this->Tree->line('/line2')->get();
        $results[] = ['Line Test of Line2', $this->Tree->getNodeSummary($Nodes)];
        $Nodes     = $this->Tree->ancestors('/line2/line3')->get();
        $results[] = ['Ancestor Test of Line3', $this->Tree->getNodeSummary($Nodes)];
        $Nodes     = $this->Tree->line('/line2/line3')->get();
        $results[] = ['Line Test of Line3', $this->Tree->getNodeSummary($Nodes)];
        $Nodes     = $this->Tree->descendants('/line1')->get();
        $results[] = ['Descendents Test of Line1', $this->Tree->getNodeSummary($Nodes)];
        $Nodes     = $this->Tree->descendants('/line2')->get();
        $results[] = ['Descendents Test of Line2', $this->Tree->getNodeSummary($Nodes)];
        $Nodes = $this->DB->fetch(DescendantsByPathReport::class, ['path' => $this->Tree->getRoot()]);
        $results[] = ['Nodes under '.$this->Tree->getRoot(), $this->Tree->getNodeSummary($Nodes)];

        // Finally, delete the test tree
        $this->Tree->setRoot('')->delete('/test', true);
        $results[] = ['delete', G::$M->getLastQuery()['rows']];

        $this->View->results = $results;

        return $this->View;
    }

    public function do_quill() {

        if($this->method === "POST") {
            dd($request);
        }

    }
}
