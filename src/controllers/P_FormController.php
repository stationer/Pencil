<?php
/**
 * P_FormController - Form Controller
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
use Stationer\Pencil\models\Form;
use Stationer\Pencil\models\Node;
use Stationer\Pencil\PencilDashboardController;

/**
 * Class P_FormController
 *
 * @package  Stationer\Pencil\controllers
 * @category Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */
class P_FormController extends PencilDashboardController {
    /** @var string Default action */
    protected $action = 'list';

    /** @var string The Node->contentType this Controller works on */
    const CONTENT_TYPE = 'Form';

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
     * List forms
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

        $Forms = $this->Tree->children(self::FORMS, ['contentType' => 'Form'])->loadContent()->get();

        $this->View->Forms = $Forms;

        return $this->View;
    }

    /**
     * Add a new form
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
        $Node->File = G::build(Form::class);

        if ('POST' === $this->method) {
            // Handle add
        }

        $this->View->Node       = $Node;
        $this->View->formAction = '/P_Form/add';
        $this->View->formHeader = 'Add Form';

        return $this->View;
    }

    /**
     * Edit a form
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
            $this->_redirect('/P_Form/list');
        }

        if ('POST' === $this->method) {
            // TODO: handle edit form
            $result = false; // $this->updateNode($Node, $request);
            $this->resultMessage($result);
        }

        $this->View->Node       = $Node;
        $this->View->formAction = '/P_Form/edit/'.$Node->node_id;
        $this->View->formHeader = 'Edit Form';

        return $this->View;
    }

    /**
     * Search form submission items
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_submissions(array $argv = [], array $request = []) {
        if (!G::$S->roleTest($this->role)) {
            return parent::do_403($argv);
        }

        return $this->View;
    }
}
