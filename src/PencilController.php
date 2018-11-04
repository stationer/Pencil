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
use Stationer\Graphite\Session;
use Stationer\Graphite\View;
use Stationer\Graphite\Controller;
use Stationer\Graphite\data\IDataProvider;

/**
 * Class PencilController
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
    const ASSETS = '/assets';
    const THEMES = '/themes';
    const NAVIGATION = '/navigation';

    const TREE_ROOT_KEY = 'siteTreeRoot';

    /** @var string Required Role, set to false for no requirement */
    protected $role = false;

    /** @var string Tree path of current site root */
    protected $siteRoot = '';

    /** @var ArboristWorkflow */
    protected $Tree;

    /** @var WebsiteWorkflow */
    protected $Website;

    /**
     * Controller constructor
     *
     * @param array         $argv Argument list passed from Dispatcher
     * @param IDataProvider $DB   DataProvider to use with Controller
     * @param View          $View Graphite View helper
     */
    public function __construct(array $argv = [], IDataProvider $DB = null, View $View = null) {
        parent::__construct($argv, $DB, $View);

        // Get or set the site root
        $Session = Session::getInstance();
        if ($Session->exists(self::TREE_ROOT_KEY)) {
            $root = $Session->get(self::TREE_ROOT_KEY);
        } else {
            // Set the default root path to the current site.
            $root = '/sites/'.$_SERVER['SERVER_NAME'];
            $Session->set(self::TREE_ROOT_KEY, $root);
        }

        $this->Tree = G::build(ArboristWorkflow::class, $this->DB);
        $this->Tree->setRoot('')->create($root)->setRoot($root)->setPath('');
        $this->Website = G::build(WebsiteWorkflow::class, $this->Tree, $this->DB);

        $this->View->treeRoot = $this->Tree->getRoot();
    }
}
