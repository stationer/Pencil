<?php
/**
 * P_FormController - Form Controller
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

/**
 * Class P_FormController
 * @package Stationer\Pencil\controllers
 * @category Pencil
 * @license  CC BY-NC-SA http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @link     http://g.lonefry.com
 */
class P_FormController extends Controller {

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
     * List forms
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_list(array $argv = array(), array $request = array()) {
        if (!G::$S->roleTest('Admin/Login')) {
            return parent::do_403($argv);
        }

        return $this->View;
    }

    /**
     * Add a new form
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_add(array $argv = array(), array $request = array()) {
        if (!G::$S->roleTest('Admin/Login')) {
            return parent::do_403($argv);
        }

        return $this->View;
    }

    /**
     * Search form submission items
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_submissions(array $argv = array(), array $request = array()) {
        if (!G::$S->roleTest('Admin/Login')) {
            return parent::do_403($argv);
        }

        return $this->View;
    }
}
