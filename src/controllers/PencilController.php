<?php
/**
 * PencilController - Theme Controller
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
 * Class P_ThemeController
 *
 * @package  Stationer\Pencil\controllers
 * @category Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */
abstract class PencilController extends Controller {
    /**
     * Controller constructor
     *
     * @param array         $argv Argument list passed from Dispatcher
     * @param IDataProvider $DB   DataProvider to use with Controller
     * @param View          $View Graphite View helper
     */
    public function __construct(array $argv = [], IDataProvider $DB = null, View $View = null) {
        parent::__construct($argv, $DB, $View);

        $this->View->setTemplate('header', 'Pencil._header.php');
        $this->View->setTemplate('footer', 'Pencil._footer.php');
        $this->View->_style(dirname(__DIR__).'/css/Pencil.css');
        $this->View->_script(dirname(__DIR__).'/js/Pencil.js');
    }
}
