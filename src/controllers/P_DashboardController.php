<?php
/**
 * P_DashboardController - Dashboard Controller
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
use Stationer\Pencil\models\Site;
use Stationer\Pencil\PencilController;

/**
 * Class P_DashboardController
 *
 * @package  Stationer\Pencil\controllers
 * @category Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */
class P_DashboardController extends PencilController {
    /** @var string Default action */
    protected $action = 'home';

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
    public function do_settings(array $argv = [], array $request = []) {
        if (!G::$S->roleTest($this->role)) {
            return parent::do_403($argv);
        }

        $SiteNode = $this->Website->getSiteRoot();

        if ('POST' == $this->method) {
            /** @var Site $Site */
            $Site = $SiteNode->File;
            $Site->title = $request['title'];
            $Site->theme_id = $request['theme_id'];
            $Site->defaultPage_id = $request['defaultPage_id'];
            $result = $this->DB->save($Site);
            if (false !== $result) {
                G::msg('Saved Site Settings.');
            } else {
                G::msg('Failed to save site settings!', 'error');
            }
        }

        // Get Themes without Files
        $Themes = $this->Tree->descendants(self::THEMES, ['contentType' => 'Theme'])->get();

        // Get Pages with Files
        $Pages = $this->Tree->descendants(self::WEBROOT, ['contentType' => 'Page'])->loadContent()->get();

        $this->View->Themes   = $Themes;
        $this->View->Pages    = $Pages;
        $this->View->SiteNode = $SiteNode;

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
    public function do_home(array $argv = [], array $request = []) {
        if (!G::$S->roleTest($this->role)) {
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
    public function do_tree(array $argv = [], array $request = []) {
        if (!G::$S->roleTest($this->role)) {
            return parent::do_403($argv);
        }

        $Nodes = $this->Tree->subtree('')->get();

        usort($Nodes, function ($a, $b) {
            return strcmp($a["path"], $b["path"]);
        });
        $this->View->Nodes = $Nodes;
        $this->View->root = $this->Tree->getRoot();

        return $this->View;
    }

    /**
     * Invoke the tree rebuild
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_rebuild(array $argv = [], array $request = []) {
        if (!G::$S->roleTest($this->role)) {
            return parent::do_403($argv);
        }

        $this->Tree->reindex();
        die;
        return $this->do_home($argv, $request);
    }

    /**
     * Invoke the tree rebuild
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_export(array $argv = [], array $request = []) {
        if (!G::$S->roleTest($this->role)) {
            return parent::do_403($argv);
        }

        $this->View->data = $this->Tree->getExport();
        $this->View->_template = 'JSON.php';

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
    public function do_devreset(array $argv = [], array $request = []) {
        if (!G::$S->roleTest($this->role)) {
            return parent::do_403($argv);
        }

        $this->Website->resetSite();

        return $this->do_settings();
    }
}
