<?php
/**
 * P_TextController - Text Controller
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
use Stationer\Pencil\models\Text;
use Stationer\Pencil\models\Node;
use Stationer\Pencil\PencilController;

/**
 * Class P_TextController
 *
 * @package  Stationer\Pencil\controllers
 * @category Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */
class P_TextController extends PencilController {
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
     * List Text items
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
            $Texts = [];
        } else {
            $Texts = $this->Tree->descendants('', ['contentType' => 'Text'])->loadContent()->get();
        }

        $this->View->Texts = $Texts;
        $this->View->treeRoot = $this->Tree->getRoot();

        return $this->View;
    }

    /**
     * Add a new Text item
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

        $Text = G::build(Text::class);
        $Node = G::build(Node::class);
        if ('POST' === $this->method) {
            $Text->setAll($request);

            $result = $this->DB->insert($Text);

            // If Text has been save successfully create the node
            if (false !== $result) {
                $Node->label = $request['label'] ?: $request['title'];
                $this->Tree->create($request['parentPath'].'/'.$Node->label, [
                    'File' => $Text,
                    'label' => $Node->label,
                    'creator_id' => G::$S->Login->login_id ?? 0,
                    'published' => isset($request['published']),
                    'description' => $request['description'],
                    'trashed' => isset($request['trashed']),
                    'featured' => isset($request['featured']),
                    //'keywords' => $request['keywords']
                ]);

                // Load the Node
                $Node = $this->Tree->setPath($request['parentPath'].'/'.$Node->label)->load()->getFirst();

                // Alert that it was successfully saved
                if (is_a($Node, Node::class)) {
                    G::msg('The changes to this text have been successfully saved.', 'success');
                    $this->_redirect('/P_Text/edit/'.$Node->node_id);
                }
            }

            G::msg('There was a problem saving your new text.', 'error');
        }

        $Node->File($Text);

        $Nodes = $this->Tree->subtree('')->get();
        $this->View->Nodes = $Nodes;
        $this->View->Node = $Node;
        $this->View->parentPath = $request['parentPath'] ?? $this->Tree->getRoot();

        return $this->View;
    }

    /**
     * Edit a Text item
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
            //$Node->keywords    = $request['keywords'];
            $Node->description = $request['description'];
            // Adjust parent
            if ($request['parentPath'] != dirname($Node->path)) {
                $this->Tree->move($request['parentPath']);
            }

            $result  = $this->DB->save($Node);

            /** @var Text $Text */
            $Text = $Node->File;
            $Text->mimeType = $request['mimeType'];
            $Text->body  = $request['body'];

            // Save and set the Text
            $result2 = $this->DB->save($Text);
            $Node->File($Text);

            // If saved successfully alert the user
            if (in_array($result, [null, true]) && in_array($result2, [null, true])) {
                G::msg('The changes to this Text have been successfully saved.', 'success');
            }
        }
        $Nodes                = $this->Tree->subtree('')->get();
        $this->View->Nodes = $Nodes;

        $this->View->Node = $Node;

        return $this->View;
    }
}
