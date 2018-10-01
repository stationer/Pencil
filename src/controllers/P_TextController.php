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

    /** @var string The Node->contentType this Controller works on */
    const CONTENT_TYPE = 'Text';

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
            $Nodes = [];
        } else {
            $Nodes = $this->Tree->descendants('', ['contentType' => static::CONTENT_TYPE])->loadContent()->get();
        }

        $this->View->Texts    = $Nodes;

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
                    'File'        => $Text,
                    'label'       => $Node->label,
                    'creator_id'  => G::$S->Login->login_id ?? 0,
                    'published'   => isset($request['published']),
                    'description' => $request['description'],
                    'trashed'     => isset($request['trashed']),
                    'featured'    => isset($request['featured']),
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

        $Nodes                  = $this->Tree->subtree('')->get();
        $this->View->Nodes      = $Nodes;
        $this->View->Node       = $Node;
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
        $Node = $this->getNode($argv[1]);
        // If we didn't get the Node, show error and delegate to do_list
        if (empty($Node)) {
            G::msg('Requested '.static::CONTENT_TYPE.' not found: '.$argv[1], 'error');
            $this->_redirect('/P_Text/list');
        }

        if ('POST' === $this->method) {
            $result = $this->updateNode($Node, $request);
            $this->resultMessage($result);
        }

        $Nodes             = $this->Tree->subtree('')->get();
        $this->View->Nodes = $Nodes;

        $this->View->Node = $Node;

        return $this->View;
    }
}
