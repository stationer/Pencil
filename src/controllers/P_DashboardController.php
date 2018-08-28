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

        // If the site-root doesn't have a contentType, it didn't exist, so create Site record, also
        $SiteNode = $this->Tree->create('')->getFirst();
        if (empty($SiteNode->contentType)) {
            /** @var Site $Site */
            $Site = G::build(Site::class);
            $Site->created_uts = NOW;
            $this->DB->insert($Site);
            $SiteNode->contentType = Site::getTable();
            $SiteNode->content_id  = $Site->site_id;
        } else {
            $Site = $this->DB->byPK(Site::class, $SiteNode->content_id);
        }

        if ('POST' == $this->method) {
            $Site->theme_id = $request['theme_id'];
            $Site->defaultPage_id = $request['defaultPage_id'];
            $this->DB->save($Site);
        }

        // Ensure other key nodes exist
        $this->Tree->create(self::WEBROOT);
        $this->Tree->create(self::BLOG);
        $this->Tree->create(self::COMPONENTS);
        $this->Tree->create(self::TEMPLATES);
        $this->Tree->create(self::FORMS);
        $this->Tree->create(self::MEDIA);
        $this->Tree->create(self::LANDING);
        $this->Tree->create(self::ERROR);
        $this->Tree->create(self::THEMES);

        // Get Themes
        $Themes = $this->Tree->setPath(self::THEMES)->descendants()->get();

        // Get Pages
        $Pages = $this->Tree->setPath(self::WEBROOT)->descendants()->get();

        $this->View->Themes   = $Themes;
        $this->View->Pages    = $Pages;
        $this->View->Site     = $Site;
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
}
