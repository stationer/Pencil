<?php
/**
 * P_PageController - Page Controller
 *
 * PHP version 7.0
 *
 * @category Graphite
 * @package  Pencil
 * @author   Andrew Leach <andrew@leachcreative.com>
 * @license  CC BY-NC-SA http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @link     http://g.lonefry.com
 */
namespace Stationer\Pencil\controllers;

use Stationer\Graphite\View;
use Stationer\Graphite\Controller;
use Stationer\Graphite\data\IDataProvider;

/**
 * Class P_PageController
 * @package Stationer\Pencil\controllers
 * @category Pencil
 * @author   Andrew Leach <andrew@leachcreative.com>
 * @license  CC BY-NC-SA http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @link     http://g.lonefry.com
 */
class P_PageController extends Controller {

    /**
     * Controller constructor
     *
     * @param array         $argv Argument list passed from Dispatcher
     * @param IDataProvider $DB   DataProvider to use with Controller
     * @param View          $View Graphite View helper
     */
    public function __construct(array $argv = [], IDataProvider $DB = null, View $View = null) {
        parent::__construct($argv, $DB, $View);

        if (!G::$S->roleTest('Admin/Login')) {
            return parent::do_403($argv);
        }
    }

    /**
     * Page for listing all pages
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_list(array $argv = array(), array $request = array()) {


        return $this->View;
    }

    /**
     * Page for adding a new page
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_add(array $argv = array(), array $request = array()) {


        return $this->View;
    }

    /**
     * Search for a page or pages
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_search(array $argv = array(), array $request = array()) {


        return $this->View;
    }
}