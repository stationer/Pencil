<?php
/**
 * P_DashboardController - Dashboard Controller
 *
 * PHP version 7.0
 *
 * @category Graphite
 * @package  Pencil
 * @license  CC BY-NC-SA http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @link     http://g.lonefry.com
 */

namespace Stationer\Pencil\controllers;

use Stationer\Graphite\View;
use Stationer\Graphite\Controller;
use Stationer\Graphite\data\IDataProvider;

class P_DashboardController extends Controller {
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
     * Page for updating website settings
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_settings(array $argv = array(), array $request = array()) {
        if (!G::$S->roleTest('Admin/Login')) {
            return parent::do_403($argv);
        }

        return $this->View;
    }

    /**
     * Page for viewing fancy graphs ;-)
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_fancyGraphs(array $argv = array(), array $request = array()) {
        if (!G::$S->roleTest('Admin/Login')) {
            return parent::do_403($argv);
        }

        return $this->View;
    }
}
