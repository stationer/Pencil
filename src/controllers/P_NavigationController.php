<?php
/**
 * P_NavigationController - Navigation Controller
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
use Stationer\Pencil\models\Navigation;
use Stationer\Pencil\models\Node;
use Stationer\Pencil\NavigationWorkflow;
use Stationer\Pencil\PencilController;

/**
 * Class P_NavigationController
 *
 * @package  Stationer\Pencil\controllers
 * @category Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */
class P_NavigationController extends PencilController {
    /** @var string Default action */
    protected $action = 'list';

    /** @var string The Node->contentType this Controller works on */
    const CONTENT_TYPE = 'Navigation';

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
     * Manage Navigation
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

        // TODO support nested nav
        $Navigations = $this->Tree->children(self::NAVIGATION, ['contentType' => 'Navigation'])->loadContent()->get();

        $this->View->Navigations = $Navigations;

        return $this->View;
    }

    /**
     * Manage Navigation
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

        $Node = G::build(Node::class);
        $Navigation = G::build(Navigation::class);
        $Node->File = $Navigation;

        if ('POST' === $this->method) {
            $Navigation->source = $request['source'];
            $Navigation->rendered = G::build(NavigationWorkflow::class)->render($Navigation->source);
            $result = $this->DB->insert($Navigation);
            if (false !== $result) {
                $Node->label = $request['label'] ?? '';
                $this->Tree->create(PencilController::NAVIGATION.'/'.$Node->label, [
                    'File' => $Navigation,
                    'label' => $Node->label,
                    'creator_id' => G::$S->Login->login_id ?? 0,
                    'published' => isset($request['published']),
                    'description' => $request['description'],
                    'trashed' => isset($request['trashed']),
                    'featured' => isset($request['featured']),
                    'keywords' => $request['keywords']
                ]);

                $Node = $this->Tree->setPath(PencilController::NAVIGATION.'/'.$Node->label)->load()->getFirst();
                if (is_a($Node, Node::class)) {
                    G::msg("The navigation has been successfully created", 'success');
                    $this->_redirect('/P_Navigation/edit/'.$Node->node_id);
                }
            }
        }

        $this->View->Node = $Node;

        return $this->View;
    }

    public function do_edit(array $argv = [], array $request = []) {
        if (!G::$S->roleTest($this->role)) {
            return parent::do_403($argv);
        }

        // Get the Page's Node, with the Page record
        $Node = $this->getNode($argv[1]);

        // If we didn't get the Node, show error and delegate to do_list
        if (empty($Node)) {
            G::msg('Requested '.static::CONTENT_TYPE.' not found: '.$argv[1], 'error');
            $this->_redirect('/P_Navigation/list');
        }

        if ('POST' === $this->method) {
            $NW = G::build(NavigationWorkflow::class);
            $request['rendered'] = $NW->render($request['source']);

            $result = $this->updateNode($Node, $request);
            $this->resultMessage($result);
        }

        $this->View->Node = $Node;

        return $this->View;
    }
}
