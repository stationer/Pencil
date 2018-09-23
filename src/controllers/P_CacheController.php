<?php
/**
 * P_CacheController - Cache Controller
 *
 * PHP version 7.0
 *
 * @package  Stationer\Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */

namespace Stationer\Pencil\controllers;

use Stationer\Graphite\Controller;
use Stationer\Graphite\G;
use Stationer\Graphite\View;
use Stationer\Pencil\AssetManager;

/**
 * Class P_CacheController
 *
 * @package  Stationer\Pencil\controllers
 * @category Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */
class P_CacheController extends Controller {
    /** @var string Default action */
    protected $action = 'create';

    public function action() {
        return $this->action;
    }

    /**
     * List all components
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_create(array $argv = [], array $request = []) {
        /** @var AssetManager $AssetManager */
        $AssetManager = G::build(AssetManager::class);
        ob_start();
        $location = $AssetManager->resize($request['_path']);
        ob_end_clean();

        if (false === $location) {
            return (G::build(P_RenderController::class))->do_404($argv, $request);
        }

        http_response_code(307);
        header('Location: /'.trim($location, '/'));
        G::close();
        exit;
    }
}
