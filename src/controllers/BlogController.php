<?php
/**
 * BlogController - Blog Render Controller
 * Renders requested blog content
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
use Stationer\Pencil\models\Tag;
use Stationer\Pencil\PaperWorkflow;
use Stationer\Pencil\PencilController;
use Stationer\Pencil\reports\ArticleSearchReport;

/**
 * Class BlogController
 *
 * @package  Stationer\Pencil\controllers
 * @category Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */
class BlogController extends PencilController {
    /** @var string Default action */
    protected $action = 'list';

    public function __construct(array $argv = [], IDataProvider $DB = null, View $View = null) {
        parent::__construct($argv, $DB, $View);

        // Force the action, in spite of the parent constructor
        $this->action = 'list';
    }

    /**
     * List available themes
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return void|View
     */
    public function do_list(array $argv = [], array $request = []) {
        // TODO: Solve getting /blog to match /Blog - problem with case sensitive autoloading?
        $params = [
            'path' => $this->Tree->getRoot(),
            'published' => true,
        ];

        // Sanitize the inputs!
        if (!empty($request['categorySelector'])) {
            $params['tag'] = G::build(Tag::class, ['label' => $request['categorySelector']])->label;
        }
        if (!empty($request['archiveSelector'])) {
            $tmp = explode(' ', $request['archiveSelector']);
            $params['year'] = (int)$tmp[0];
            $params['month'] = (int)$tmp[1];
        }
        if (!empty($request['blog_search'])) {
            $params['search'] = substr($request['blog_search'], 0, 255);
        }

        // TODO: Handle Paging
        $Articles = $this->DB->fetch(ArticleSearchReport::class, $params,
            ['featured' => false, 'release_uts' => true], 10);

        // Load teasers
        // TODO: fetch teaser component according to Site->blogTeaserComponent_id and use a View here
        $html = '';
        foreach ($Articles as $Article) {
            $html .= '<div>'.$Article->File->title.'</div>';
        }
        $overrides['content']['html1'] = $html;

        // Load Blog Page
        $Node = $this->Tree->load(PencilController::BLOG)->loadContent()->getFirst();
        /** @var PaperWorkflow $Paper */
        $Paper  = G::build(PaperWorkflow::class, $this->Tree);
        $result = $Paper->render($Node, 'html', $overrides);

        // TODO: Run this through the View.
        echo $result;
        G::close();
        die;
    }
}
