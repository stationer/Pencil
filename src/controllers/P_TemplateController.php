<?php
/**
 * P_TemplateController - Template Controller
 *
 * PHP version 7.0
 *
 * @package  Stationer\Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */

namespace Stationer\Pencil\controllers;

use Stationer\Graphite\View;
use Stationer\Graphite\Controller;
use Stationer\Graphite\data\IDataProvider;

/**
 * Class P_TemplateController
 *
 * @package  Stationer\Pencil\controllers
 * @category Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */
class P_TemplateController extends Controller {

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
     * List all templates
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_list(array $argv = [], array $request = []) {
        if (!G::$S->roleTest('Admin/Login')) {
            return parent::do_403($argv);
        }

        return $this->View;
    }

    /**
     * Build a new template
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_add(array $argv = [], array $request = []) {
        if (!G::$S->roleTest('Admin/Login')) {
            return parent::do_403($argv);
        }

        return $this->View;
    }
}
