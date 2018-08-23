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

        $Components = $this->Tree->setPath(self::COMPONENTS)->children()->loadContent()->get();

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

        $Node = $this->Tree->loadID($argv[1])->loadContent()->getFirst();
        if('POST' === $this->method) {
            $Node->setAll($request, true);
            $Component = $Node->File;
            $Component->setAll($request, true);
            $Node->File($Component);

            $result1 = $this->DB->save($Node);
            $result2 = $this->DB->save($Component);

            if (false !== $result1 && false !== $result2) {
                G::msg("The component has been successfully updated.", 'success');
            } else {
                G::msg("There was a problem updating this component.", 'error');
            }
        }

        $this->View->Node = $Node;

        return $this->View;
    }
}
