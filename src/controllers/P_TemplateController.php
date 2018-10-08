<?php
/**
 * P_TemplateController - Template Controller
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
use Stationer\Pencil\PencilDashboardController;

/**
 * Class P_TemplateController
 *
 * @package  Stationer\Pencil\controllers
 * @category Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */
class P_TemplateController extends PencilDashboardController {
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
     * List all templates
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
            $Templates = [];
        } else {
            $Templates = $this->Tree->children(self::TEMPLATES, ['contentType' => 'Template'])->loadContent()->get();
        }
        $this->View->Templates = $Templates;

        return $this->View;
    }

    /**
     * Build a new template
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
        $Node->File = G::build(Template::class);

        if ('POST' === $this->method) {
            $request['parentPath'] = PencilController::TEMPLATES;
            $Node                  = $this->insertNode($request, $Node->File);
            $result                = is_a($Node, Node::class);
            $this->resultMessage($result);
            if ($result) {
                $this->_redirect('/P_Template/edit/'.$Node->node_id);
            }
        }

        $this->View->Template = $Node->File;
        $this->View->Node     = $Node;

        return $this->View;
    }

    /**
     * Edit a existing template
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
            $this->_redirect('/P_Template/list');
        }

        if ('POST' === $this->method) {
            $result = $this->updateNode($Node, $request);
            $this->resultMessage($result);
        }

        $this->View->Node = $Node;

        return $this->View;
    }
}
