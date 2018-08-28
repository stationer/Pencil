<?php
/**
 * P_RenderController - Render Controller
 * Renders requested content
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
use Stationer\Pencil\models\Page;
use Stationer\Pencil\models\Theme;
use Stationer\Pencil\PaperWorkflow;
use Stationer\Pencil\PencilController;

/**
 * Class P_RenderController
 *
 * @package  Stationer\Pencil\controllers
 * @category Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */
class P_RenderController extends PencilController {
    /** @var string Default action */
    protected $action = 'page';

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
    public function do_page(array $argv = [], array $request = []) {
        if (!G::$S->roleTest($this->role)) {
            return parent::do_403($argv);
        }

        // TODO: Tie this into the dispatcher as a 404 handler
        $url = $argv['url'];
        $Nodes = $this->Tree->getByURL($url);
        $Nodes = [reset($Nodes)];
        $Nodes = $this->Tree->getFilesForNodes($Nodes);
        $Node = reset($Nodes);

        $Paper = G::build(PaperWorkflow::class, $this->Tree);
        $Paper->render($Node);


        $this->View->Nodes = $Nodes;

        return $this->View;
    }
}
