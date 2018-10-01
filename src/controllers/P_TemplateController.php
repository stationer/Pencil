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

/**
 * Class P_TemplateController
 *
 * @package  Stationer\Pencil\controllers
 * @category Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */
class P_TemplateController extends PencilController {
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

        $Templates = $this->Tree->children(self::TEMPLATES, ['contentType' => 'Template'])->loadContent()->get();

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

        $Template = G::build(Template::class);
        $Node = G::build(Node::class);
        if ('POST' === $this->method) {
            $Template->body = $request['body'];
            $Template->css = $request['css'];
            $Template->type = $request['type'];
            $Template->created_uts = strtotime('now');
            $result = $this->DB->insert($Template);

            $Node->label = $request['label'];
            if (false !== $result) {
                $this->Tree->create(PencilController::TEMPLATES.'/'.$Node->label, [
                    'File' => $Template,
                    'creator_id' => G::$S->Login->login_id,
                    'published' => isset($request['published']),
                    'description' => $request['description'],
                    'trashed' => isset($request['trashed']),
                ]);

                $Node = $this->Tree->getLast();
                if (is_a($Node, Node::class)) {
                    G::msg("The template has been successfully created", 'success');
                    $this->_redirect('/P_Template/edit/'.$Node->node_id);
                }
            }

            G::msg("There was a problem creating this template.", 'error');
        }

        $this->View->Template = $Template;
        $this->View->Node = $Node;

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
