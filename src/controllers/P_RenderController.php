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

    public function __construct(array $argv = [], IDataProvider $DB = null, View $View = null) {
        parent::__construct($argv, $DB, $View);

        // Force the action, in spite of the parent constructor
        $this->action = 'page';
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

        $SiteNode = $this->Tree->load('')->loadContent()->getFirst();
        $url      = '/'.trim($argv['_path'] ?? $_SERVER['REQUEST_URI'] ?? '', '/');
        if ('/sitemap.xml' == $url) {
            return $this->do_sitemap($argv, $request);
        }
        $parts    = explode('.', $url);
        $path     = array_shift($parts);
        $mode     = 'html';
        if (count($parts)) {
            $mode = array_pop($parts);
        }
        if ('/' == $path) {
            $Node = $this->Tree->descendants('/', [
                'contentType' => 'Page',
                'node_id'     => $SiteNode->File->defaultPage_id,
            ])->first()->loadContent()->getFirst();
        } else {
            $Node  = $this->Tree->getByURL($path);
            $Nodes = $this->Tree->getFilesForNodes([$Node]);
            $Node  = reset($Nodes);
        }

        if (!is_a($Node, Node::class)) {
            return $this->do_404($argv, $request);
        }
        if (!$Node->published) {
            trigger_error('Request to unpublished page at '.$url);

            return $this->do_404($argv, $request);
        }
        if ($Node->trashed) {
            trigger_error('Request to unpublished page at '.$url);

            return $this->do_404($argv, $request);
        }
        /** @var PaperWorkflow $Paper */
        $Paper  = G::build(PaperWorkflow::class, $this->Tree);
        $result = $Paper->render($Node, $mode);

        echo $result;
        G::close();
        die;
    }

    /**
     * Default action for handling 404 errors
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_sitemap(array $argv = [], array $request = []) {
        $this->View->_template = 'P_Render.sitemap.php';

        $Nodes = $this->Tree->descendants(self::WEBROOT, [
            'contentType' => 'Page',
            'published'   => true,
            'trashed'     => false,
        ])->loadContent()->get()
            + $this->Tree->descendants(self::LANDING, [
            'contentType' => 'Page',
            'published'   => true,
            'trashed'     => false,
        ])->loadContent()->get();

        $this->View->Pages = $Nodes;
        $this->View->root = $this->Tree->getRoot();

        return $this->View;
    }

    /**
     * Default action for handling 404 errors
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_404(array $argv = [], array $request = []) {
        header("HTTP/1.0 404 File Not Found");

        /** @var Node $Node */
        $Node = $this->Tree->load(PencilController::ERROR.'/404')->loadContent()->getFirst();
        /** @var PaperWorkflow $Paper */
        $Paper  = G::build(PaperWorkflow::class, $this->Tree);
        $result = $Paper->render($Node);

        echo $result;
        G::close();
        die;
    }
    /**
     * Default action for handling 404 errors
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_500(array $argv = [], array $request = []) {
        header("HTTP/1.0 500 Internal Server Error");

        /** @var Node $Node */
        $Node = $this->Tree->load(PencilController::ERROR.'/500')->loadContent()->getFirst();
        /** @var PaperWorkflow $Paper */
        $Paper  = G::build(PaperWorkflow::class, $this->Tree);
        $result = $Paper->render($Node);

        echo $result;
        G::close();
        die;
    }
}
