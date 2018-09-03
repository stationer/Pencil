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
use Stationer\Pencil\models\Content;
use Stationer\Pencil\models\Page;
use Stationer\Pencil\PencilController;
use Stationer\Pencil\reports\AncestorsByPathReport;

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

    /**
     * Controller constructor
     *
     * @param array $argv Argument list passed from Dispatcher
     * @param IDataProvider $DB DataProvider to use with Controller
     * @param View $View Graphite View helper
     */
    public function __construct(array $argv = [], IDataProvider $DB = null, View $View = null) {
        parent::__construct($argv, $DB, $View);
    }

    /**
     * Page for listing all pages
     *
     * @param array $argv Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_list(array $argv = [], array $request = []) {
        if (!G::$S->roleTest($this->role)) {
            return parent::do_403($argv);
        }

        $Pages['Public']  = $this->Tree->setPath(self::WEBROOT)->children()->loadContent()->get();
        $Pages['Landing'] = $this->Tree->setPath(self::LANDING)->children()->loadContent()->get();
        $Pages['Error']   = $this->Tree->setPath(self::ERROR)->children()->loadContent()->get();

        $this->View->Pages = $Pages;

        return $this->View;
    }

    /**
     * Page for adding a new page
     *
     * @param array $argv Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_add(array $argv = [], array $request = []) {
        if (!G::$S->roleTest($this->role)) {
            return parent::do_403($argv);
        }

        $Page = G::build(Page::class);
        $Node = G::build(Node::class);
        // Get the list of Template Nodes, without the Templates
        $Templates = $this->Tree->descendants(PencilController::TEMPLATES, [
            'contentType' => 'Template',
        ])->get();

        if ('POST' === $this->method) {
            $Page->title = $request['title'];
            if (isset($Templates[$request['template_id'] ?? null])) {
                $Page->template_id = $request['template_id'];
            }
            $result = $this->DB->insert($Page);

            if (false !== $result) {
                $Node->label = $request['label'] ?: $request['title'];
                $this->Tree->create(PencilController::WEBROOT.'/'.$Node->label, [
                    'File' => $Page,
                    'label' => $Node->label,
                    'creator_id' => G::$S->Login->login_id ?? 0,
                    'published' => isset($request['published']),
                    'description' => $request['description'],
                    'trashed' => isset($request['trashed']),
                    'featured' => isset($request['featured']),
                    'keywords' => $request['keywords']
                ]);

                $Node = $this->Tree->setPath(PencilController::WEBROOT.'/'.$Node->label)->load()->getFirst();
                if (is_a($Node, Node::class)) {
                    G::msg("The page has been successfully created", 'success');
                    $this->_redirect('/P_Page/edit/'.$Node->node_id);
                }
            }

            G::msg("There was a problem creating this page.", 'error');
        }

        $this->View->Templates = $Templates;
        $this->View->Page = $Page;
        $this->View->Node = $Node;


        return $this->View;
    }

    /**
     * Page for editing a page
     *
     * @param array $argv Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_edit(array $argv = [], array $request = []) {
        if (!G::$S->roleTest($this->role)) {
            return parent::do_403($argv);
        }

        //$Node = $this->Tree->loadID($argv[1])->getFirst();
        // Get the Page's Node, with the Page record
        $Node = $this->Tree->descendants('', [
            'contentType' => 'Page',
            'node_id' => $argv[1],
        ])->loadContent()->getFirst();

        // Get the list of Template Nodes, without the Templates
        $Templates = $this->Tree->descendants(PencilController::TEMPLATES, [
            'contentType' => 'Template',
        ])->get();

        if ('POST' === $this->method) {
            $Node->label       = $request['node_label'];
            $Node->published   = $request['published'] ?? 0;
            $Node->trashed     = $request['trashed'] ?? 0;
            $Node->featured    = $request['featured'] ?? 0;
            $Node->keywords    = $request['keywords'];
            $Node->description = $request['description'];
            $result  = $this->DB->save($Node);

            $Node->File->title = $request['title'];
            $Node->File->body  = $request['body'];
            if (isset($Templates[$request['template_id'] ?? null])) {
                $Node->File->template_id = $request['template_id'];
            }
            $result2 = $this->DB->save($Node->File);

            if (in_array($result, [null, true]) && in_array($result2, [null, true])) {
                G::msg('The changes to this page have been successfully saved.', 'success');
            } else {
                G::msg('There was a problem saving your page.', 'error');
            }
        }

        $this->View->Templates = $Templates;
        $this->View->Page = $Node;

        return $this->View;
    }

    /**
     * Search for a page or pages
     *
     * @param array $argv Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_search(array $argv = [], array $request = []) {
        if (!G::$S->roleTest($this->role)) {
            return parent::do_403($argv);
        }

        return $this->View;
    }
}
