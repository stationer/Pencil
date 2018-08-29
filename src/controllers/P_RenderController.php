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
        $SiteNode = $this->Tree->load('')->loadContent()->getFirst();
        $url = $argv['url'] ?? '/';
        if ('/' == $url) {
            $Node = $this->Tree->descendants('/', [
                'contentType' => 'Page',
                'node_id' => $SiteNode->File->defaultPage_id,
                ])->first()->loadContent()->getFirst();
        } else {
            $Nodes = $this->Tree->getByURL($url);
            $Nodes = [reset($Nodes)];
            $Nodes = $this->Tree->getFilesForNodes($Nodes);
            $Node = reset($Nodes);
        }

        /** @var PaperWorkflow $Paper */
        $Paper = G::build(PaperWorkflow::class, $this->Tree);
        $result = $Paper->render($Node);

        echo $result;
        die;
        croak(htmlspecialchars($result));
    }
}
