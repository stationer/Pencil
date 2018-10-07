<?php
/**
 * P_PageController - Page Controller
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
use Stationer\Pencil\models\Node;
use Stationer\Pencil\models\Page;
use Stationer\Pencil\models\Text;
use Stationer\Pencil\PencilController;

use Stationer\Graphite\data\IDataProvider;

/**
 * Class P_PageController
 *
 * @package  Stationer\Pencil\controllers
 * @category Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */
class P_PageController extends PencilController {
    /** @var string Default action */
    protected $action = 'list';

    /** @var string The Node->contentType this Controller works on */
    const CONTENT_TYPE = 'Page';

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
    public function do_list(array $argv = [], array $request = []) {
        if (!G::$S->roleTest($this->role)) {
            return parent::do_403($argv);
        }

        if (isset($request['search'])) {
            // TODO: the search thing
            $Nodes = [];
        } else {
            $Nodes = $this->Tree->descendants('', ['contentType' => static::CONTENT_TYPE])->loadContent()->get();
        }

        $this->View->Pages = $Nodes;

        return $this->View;
    }

    /**
     * Page for adding a new page
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
        $Node->File = G::build(Page::class);

        // Get the list of Template Nodes, without the Templates
        $Templates = $this->Tree->descendants(PencilController::TEMPLATES, [
            'contentType' => 'Template',
        ])->get();

        if ('POST' === $this->method) {
            // Page-specific sanitizing
            if (!isset($Templates[$request['template_id']])) {
                unset($request['template_id']);
            }
            $request['parentPath'] = PencilController::LANDING;
            $request['label']      = $request['label'] ?: $request['title'];

            $Node   = $this->insertNode($request, $Node->File);
            $result = is_a($Node, Node::class);
            $this->resultMessage($result);
            if ($result) {
                $this->_redirect('/P_Page/edit/'.$Node->node_id);
            }
        }

        $this->View->Templates = $Templates;
        $this->View->Node      = $Node;

        return $this->View;
    }

    /**
     * Page for editing a page
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

        // Get the Page's Node, with the Page record
        $Node = $this->getNode($argv[1]);

        // If we didn't get the Node, show error and delegate to do_list
        if (empty($Node)) {
            G::msg('Requested '.static::CONTENT_TYPE.' not found: '.$argv[1], 'error');
            $this->_redirect('/P_Page/list');
        }

        // Get associated data required for editing
        // Get the list of Template Nodes, without the Templates
        $Templates = $this->Tree->descendants(PencilController::TEMPLATES, [
            'contentType' => 'Template',
        ])->get();

        // Get the Page's Template to get a list of content requirements
        /** @var Page $Page */
        $Page          = $Node->File;
        $TemplateNode  = $this->Tree->loadID($Page->template_id)->loadContent()->getFirst();
        $contentLabels = $TemplateNode->File->getContentLabels();
        $ContentNodes  = $this->getContentNodes($Node->path, $contentLabels);

        if (!isset($Templates[$request['template_id'] ?? null])) {
            unset($request['template_id']);
        }

        if ('POST' === $this->method) {
            // Special for Pages, update the Text Nodes under it
            foreach ($contentLabels as $label) {
                if (isset($request['content'][$label])) {
                    $Text       = &$ContentNodes[$Node->path.'/'.$label]->File;
                    $Text->body = $request['content'][$label];
                    $this->DB->save($Text);
                }
            }

            $result = $this->updateNode($Node, $request);
            $this->resultMessage($result);
        }

        // TODO resolve mysqli::escape_string() expects parameter 1 to be string, array given
        $Nodes                     = $this->Tree->subtree('', ['contentType' => ['', static::CONTENT_TYPE]])->get();
        $this->View->Nodes         = $Nodes;
        $this->View->ContentNodes  = $ContentNodes;
        $this->View->contentLabels = $contentLabels;
        $this->View->Templates     = $Templates;
        $this->View->Page          = $Node;

        return $this->View;
    }

    /**
     * Get or create a Text node for each content label
     *
     * @param string   $path
     * @param string[] $contentLabels
     *
     * @return Node[]
     */
    public function getContentNodes($path, $contentLabels) {
        /** @var Node[] $ContentNodes */
        $ContentNodes = $this->Tree->children($path, ['contentType' => 'Text'])->loadContent()->get();
        // Re-index the nodes by path
        $tmp = [];
        foreach ($ContentNodes as $ContentNode) {
            $tmp[$ContentNode->path] = $ContentNode;
        }
        $ContentNodes = $tmp;

        // Verify each label has a node, or create it.
        foreach ($contentLabels as $label) {
            if (!isset($ContentNodes[$path.'/'.$label])) {
                $Text = G::build(Text::class, true);
                $this->DB->insert($Text);
                $ContentNode = $this->Tree->create($path.'/'.$label, ['File' => $Text])->getLast();
                $this->DB->insert($ContentNode);
                $ContentNodes[$path.'/'.$label] = $ContentNode;
            }
        }

        return $ContentNodes;
    }
}
