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

namespace Stationer\Pencil;

use Stationer\Graphite\G;
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
    const WEBROOT = '/webroot';
    const ERROR = '/error';
    const LANDING = '/landing';
    const BLOG = '/webroot/blog';
    const COMPONENTS = '/components';
    const TEMPLATES = '/templates';
    const FORMS = '/forms';
    const MEDIA = '/media';
    const THEMES = '/themes';
    const NAVIGATION = '/nagivation';

    /** @var string Required Role, TODO set to false for no requirement while testing */
    protected $role = false; // 'Pencil';

    /** @var string Tree path of current site root */
    protected $siteRoot = '';

    /** @var ArboristWorkflow */
    protected $Tree;

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

        $this->Tree = G::build(ArboristWorkflow::class);
        // Set the default root path to the current site.
        $this->Tree->setRoot('/'.$_SERVER['SERVER_NAME']);
    }
}
