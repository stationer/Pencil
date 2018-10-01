<?php
/**
 * P_ThemeController - Theme Controller
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
use Stationer\Pencil\models\Theme;
use Stationer\Pencil\PencilController;

/**
 * Class P_ThemeController
 *
 * @package  Stationer\Pencil\controllers
 * @category Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */
class P_ThemeController extends PencilController {
    /** @var string Default action */
    protected $action = 'list';

    /** @var string The Node->contentType this Controller works on */
    const CONTENT_TYPE = 'Theme';

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
     * List available themes
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

        $Themes = $this->Tree->children(self::THEMES, ['contentType' => 'Theme'])->loadContent()->get();

        $this->View->Nodes = $Themes;

        return $this->View;
    }

    /**
     * Build a theme
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

        $Theme = G::build(Theme::class);
        $Node = G::build(Node::class);
        if ('POST' === $this->method) {
            $Theme->setAll($request);
            $Theme->created_uts = strtotime('now');
            $result = $this->DB->insert($Theme);

            $Node->label = $request['label'];
            if (false !== $result) {
                $this->Tree->create(PencilController::THEMES.'/'.$Node->label, [
                    'File' => $Theme,
                    'creator_id' => G::$S->Login->login_id,
                    'published' => isset($request['published']),
                    'description' => $request['description'],
                    'trashed' => isset($request['trashed']),
                ]);

                $Node = $this->Tree->getFirst();
                if (is_a($Node, Node::class)) {
                    G::msg("The theme has been successfully created", 'success');
                    $this->_redirect('/P_Theme/edit/'.$Node->node_id);
                }
            }

            G::msg("There was a problem creating this theme.", 'error');
        }

        $this->View->Node = $Node;
        $this->View->Theme = $Theme;

        return $this->View;
    }

    /**
     * Update a theme
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
            $this->_redirect('/P_Theme/list');
        }

        if ('POST' === $this->method) {
            $result = $this->updateNode($Node, $request);
            $this->resultMessage($result);
        }

        $this->View->Node = $Node;

        return $this->View;
    }
}
