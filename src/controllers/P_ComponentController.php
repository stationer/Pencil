<?php
/**
 * P_ComponentController - Component Controller
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
use Stationer\Pencil\models\Node;
use Stationer\Pencil\models\Template;
use Stationer\Pencil\PencilController;

/**
 * Class P_ComponentController
 *
 * @package  Stationer\Pencil\controllers
 * @category Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */
class P_ComponentController extends PencilController {
    /** @var string Default action */
    protected $action = 'list';

    /** @var string The Node->contentType this Controller works on */
    const CONTENT_TYPE = 'Template';

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
     * List all components
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
            $Components = [];
        } else {
            $Components = $this->Tree->children(self::COMPONENTS, ['contentType' => 'Template'])->loadContent()->get();
        }

        $this->View->Components = $Components;

        return $this->View;
    }

    /**
     * Build a new component
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

        $Component = G::build(Template::class);
        $Node = G::build(Node::class);
        if ('POST' === $this->method) {
            $Component->setAll($request, true);
            $Component->created_uts = strtotime('now');
            $result = $this->DB->insert($Component);

            $Node->label = $request['label'];
            if (false !== $result) {
                $this->Tree->create(PencilController::COMPONENTS.'/'.$Node->label, [
                    'File' => $Component,
                    'creator_id' => G::$S->Login->login_id,
                    'published' => isset($request['published']),
                    'description' => $request['description'],
                    'trashed' => isset($request['trashed']),
                ]);

                $Node = $this->Tree->getFirst();
                if (is_a($Node, Node::class)) {
                    G::msg("The component has been successfully created", 'success');
                    $this->_redirect('/P_Component/edit/'.$Node->node_id);
                }
            }

            G::msg("There was a problem creating this component.", 'error');
        }

        $this->View->Template = $Component;
        $this->View->Node = $Node;

        return $this->View;
    }

    /**
     * Edit a component
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
            $this->_redirect('/P_Component/list');
        }

        if('POST' === $this->method) {
            $result = $this->updateNode($Node, $request);
            $this->resultMessage($result);
        }

        $this->View->Node = $Node;

        return $this->View;
    }
}
