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
use Stationer\Pencil\models\Article;
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

        if (isset($request['search'])) {
            // TODO: the search thing
            $Articles = [];
        } else {
            $Articles = $this->Tree->setPath(self::BLOG)->children()->loadContent()->get();
        }

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

        $Article = G::build(Article::class);
        $Node = G::build(Node::class);
        if ('POST' === $this->method) {
            $Article->setAll($request);
            $Article->author_id = G::$S->Login->login_id;

            // If it's been published set publish date
            if ('on' == $request['published']) {
                $Article->release_uts = NOW;
            }
            $result = $this->DB->insert($Article);

            // If article has been save successfully create the node
            if (false !== $result) {
                $Node->label = $request['label'] ?: $request['title'];
                $this->Tree->create(PencilController::BLOG.'/'.$Node->label, [
                    'File' => $Article,
                    'label' => $Node->label,
                    'creator_id' => G::$S->Login->login_id ?? 0,
                    'published' => isset($request['published']),
                    'description' => $request['description'],
                    'trashed' => isset($request['trashed']),
                    'featured' => isset($request['featured']),
                    'keywords' => $request['keywords']
                ]);

                // Load the Node
                $Node = $this->Tree->setPath(PencilController::BLOG.'/'.$Node->label)->load()->getFirst();

                // Alert that it was successfully saved
                if (is_a($Node, Node::class)) {
                    G::msg('The changes to this post have been successfully saved.', 'success');
                    $this->_redirect('/P_Blog/edit/'.$Node->node_id);
                }
            }

            G::msg('There was a problem saving your new post.', 'error');
        }

        $Node->File($Article);
        $this->View->Node = $Node;

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

        // Load the existing node
        $Node = $this->Tree->loadID($argv[1])->loadContent()->getFirst();

        if ('POST' === $this->method) {
            // Set Node values and save
            $Node->label       = $request['label'];
            $Node->published   = $request['published'] ?? 0;
            $Node->trashed     = $request['trashed'] ?? 0;
            $Node->featured    = $request['featured'] ?? 0;
            $Node->keywords    = $request['keywords'];
            $Node->description = $request['description'];
            $result  = $this->DB->save($Node);

            /** @var Article $Article */
            $Article = $Node->File;
            $Article->title = $request['title'];
            $Article->body  = $request['body'];
            $Article->author_id = G::$S->Login->login_id;

            // If a article is marked as published and doesn't have a released date set it
            if ('on' == $request['published'] && $Article->released_uts != null) {
                $Article->release_uts = NOW;
            }

            // Save and set the article
            $result2 = $this->DB->save($Article);
            $Node->File($Article);

            // If saved successfully alert the user
            if (in_array($result, [null, true]) && in_array($result2, [null, true])) {
                G::msg('The changes to this article have been successfully saved.', 'success');
            }
        }

        $this->View->Node = $Node;

        return $this->View;
    }
}
