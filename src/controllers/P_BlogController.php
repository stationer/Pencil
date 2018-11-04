<?php
/**
 * P_BlogController - Blog Controller
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
use Stationer\Pencil\models\Article;
use Stationer\Pencil\models\Node;
use Stationer\Pencil\PencilController;
use Stationer\Pencil\PencilDashboardController;

/**
 * Class P_BlogController
 *
 * @package  Stationer\Pencil\controllers
 * @category Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */
class P_BlogController extends PencilDashboardController {
    /** @var string Default action */
    protected $action = 'list';

    /** @var string The Node->contentType this Controller works on */
    const CONTENT_TYPE = 'Article';

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
     * List blog items
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_list(array $argv = [], array $request = []) {
        if (!G::$S->roleTest($this->role)) {
            return parent::do_403($argv);
        }

        if (isset($request['search'])) {
            // TODO: the search thing
            $Articles = [];
        } else {
            $Articles = $this->Tree->descendants(self::BLOG, ['contentType' => 'Article'])->loadContent()->get();
        }

        $this->View->Articles = $Articles;

        return $this->View;
    }

    /**
     * Add a new blog item
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_add(array $argv = [], array $request = []) {
        if (!G::$S->roleTest($this->role)) {
            return parent::do_403($argv);
        }

        /** @var Node $Node */
        $Node       = G::build(Node::class);
        $Node->File = G::build(Article::class);

        if ('POST' === $this->method) {
            // If it's been published set publish date
            if ('on' == $request['published']) {
                $request['release_uts'] = NOW;
            }
            $request['parentPath'] = PencilController::BLOG;
            $request['label']      = $request['label'] ?: $request['title'];

            $Node   = $this->insertNode($request, $Node->File);
            $result = is_a($Node, Node::class);
            $this->resultMessage($result);
            if ($result) {
                $this->_redirect('/P_Blog/edit/'.$Node->node_id);
            }
        }

        $this->View->Node       = $Node;
        $this->View->formAction = '/P_Blog/add';
        $this->View->formHeader = 'Add Article';

        return $this->View;
    }

    /**
     * Edit a blog item
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_edit(array $argv = [], array $request = []) {
        if (!G::$S->roleTest($this->role)) {
            return parent::do_403($argv);
        }

        // Load the existing node
        $Node = $this->getNode($argv[1]);
        // If we didn't get the Node, show error and delegate to do_list
        if (empty($Node)) {
            G::msg('Requested '.static::CONTENT_TYPE.' not found: '.$argv[1], 'error');
            $this->_redirect('/P_Blog/list');
        }

        if ('POST' === $this->method) {
            /** @var Article $Article */
            $Article = $Node->File;
            // If a article is marked as published and doesn't have a released date, set it
            if (isset($request['published']) && $Article->release_uts != null) {
                $request['release_uts'] = NOW;
            }
            $result = $this->updateNode($Node, $request);
            $this->resultMessage($result);
        }

        $Nodes                  = $this->Tree->subtree(PencilController::BLOG, ['contentType' => ['']])->get();
        $this->View->Nodes      = $Nodes;
        $this->View->Node       = $Node;
        $this->View->formAction = '/P_Blog/edit/'.$Node->node_id;
        $this->View->formHeader = 'Edit Article';

        return $this->View;
    }
}
