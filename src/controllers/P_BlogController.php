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
use Stationer\Pencil\models\Content;
use Stationer\Pencil\models\Node;
use Stationer\Pencil\PencilController;

/**
 * Class P_BlogController
 *
 * @package  Stationer\Pencil\controllers
 * @category Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */
class P_BlogController extends PencilController {
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

        $Articles = $this->Tree->setPath(self::BLOG)->children()->get();

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

        $this->View->Page = $Node = G::build(Node::class);

        if ('POST' === $this->method) {
            $Content           = G::build(Content::class);
            $Node->label       = $request['node_label'];
            $Node->published   = $request['published'];
            $Node->trashed     = $request['trashed'];
            $Node->featured    = $request['featured'];
            $Node->keywords    = $request['keywords'];
            $Node->creator_id  = G::$S->Login->login_id;
            $Node->description = $request['description'];
            $Node->contentType = 'Blog';
            $Content->title    = $request['title'];
            $Content->body     = $request['body'];

            $result  = $this->DB->insert($Content);
            $result2 = $this->DB->insert($Node);

            // If successful redirect to the edit screen
            if (in_array($result, [null,true]) && in_array($result2, [null,true])) {
                G::msg('The changes to this post have been successfully saved.', 'success');
                $this->_redirect('/P_Blog/edit/'.$Node->node_id);
            } else {
                G::msg('There was a problem saving your new post.', 'error');
            }
        }

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

        $Node = $this->Tree->setPath(self::BLOG)->loadFiles()->get();

        if ('POST' === $this->method) {
            $Node->label       = $request['node_label'];
            $Node->setAll($request);
            $Node->File->title    = $request['title'];
            $Node->File->body     = $request['body'];

            $result = $this->DB->save($Content);
            $result2 = $this->DB->save($Node->File);

            if (in_array($result, [null,true]) && in_array($result2, [null,true])) {
                G::msg('The changes to this page have been successfully saved.', 'success');
            }
        }

        $this->View->Post = $Node;

        return $this->View;
    }

    /**
     * Search blog posts
     *
     * @param array $argv    Argument list passed from Dispatcher
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
