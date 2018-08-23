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

        $this->View->Page = $Page = G::build(Page::class);
        $this->View->Node = $Node = G::build(Node::class);

        if ('POST' === $this->method) {
            $Page->title = $request['title'];
            $Page->template_id = 1;
            $Page->created_uts = strtotime('now');
            $result = $this->DB->insert($Page);
            $Node->label = $request['label'];
            if (false !== $result) {
                $this->Tree->create(PencilController::WEBROOT.'/'.$Node->label, [
                    'File' => $Page,
                    'creator_id' => G::$S->Login->login_id,
                    'published' => isset($request['published']),
                    'description' => $request['description'],
                    'trashed' => isset($request['trashed']),
                    'featured' => isset($request['featured']),
                    'keywords' => $request['keywords']
                ]);

                $Node = $this->Tree->getFirst();
                if (is_a($Node, Node::class)) {
                    G::msg("The page has been successfully created", 'success');
                    $this->_redirect('/P_Page/edit/'.$Page->page_id);
                }

            }

            G::msg("There was a problem creating this page.", 'error');
        }

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

        $Node = $this->Tree->setPath(self::WEBROOT)->loadFiles()->get();

        if ('POST' === $this->method) {
            $Node->label       = $request['node_label'];
            $Node->published   = $request['published'];
            $Node->trashed     = $request['trashed'];
            $Node->featured    = $request['featured'];
            $Node->keywords    = $request['keywords'];
            $Node->description = $request['description'];

            $Node->File->title = $request['title'];
            $Node->File->body  = $request['body'];

            $result  = $this->DB->save($Node);
            $result2 = $this->DB->save($Node->File);

            if (in_array($result, [null, true]) && in_array($result2, [null, true])) {
                G::msg('The changes to this page have been successfully saved.', 'success');
            } else {
                G::msg('There was a problem saving your page.', 'error');
            }
        }

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
