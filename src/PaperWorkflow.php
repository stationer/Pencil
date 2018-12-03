<?php
/**
 * PaperWorkflow - For rendering content
 *
 * PHP version 7.0
 *
 * @package  Stationer\Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */

namespace Stationer\Pencil;

use const Stationer\Graphite\DATETIME_HTTP;
use const Stationer\Graphite\DATETIME_HUMAN;
use Stationer\Graphite\G;
use Stationer\Pencil\models\Node;
use Stationer\Pencil\models\Page;
use Stationer\Pencil\models\Site;
use Stationer\Pencil\models\Theme;

/**
 * PaperWorkflow - A workflow for rendering content
 *
 * PHP version 7.0
 *
 * @package  Stationer\Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 * @see      /src/models/Node.php
 */
class PaperWorkflow {
    /** @var ArboristWorkflow */
    protected $Tree;

    /**
     * Have a Tree
     *
     * @param ArboristWorkflow $Tree
     */
    public function __construct(ArboristWorkflow $Tree) {
        $this->Tree = $Tree;
    }

    /**
     * Render a given Node
     *
     * @param Node   $Node      Node to render
     * @param string $mode      Render mode
     * @param array  $overrides Values to override what's fetched by default
     *
     * @return string
     */
    public function render(Node $Node, $mode = 'html', $overrides = []) {
        $result   = '';
        $SiteNode = $this->Tree->load('')->loadContent()->getFirst();
        /** @var Site $Site */
        $Site = $SiteNode->File;
        $pagePath = str_replace($this->Tree->getRoot(), '', $Node->path);
        $objects = [
            'tree' => [
                'root'    => $this->Tree->getRoot(),
                'uploads' => AssetManager::$uploadPath.$this->Tree->getRoot(),
            ],
            'site' => array_merge($SiteNode->getAll(), $SiteNode->File->getAll(), ['path' => '/']),
            'page' => array_merge($Node->getAll(), $Node->File->getAll(),
                [
                    'path'      => $pagePath,
                    'bodyClass' => $this->pathClasses($pagePath),
                ]),
        ];
        switch ($mode) {
            case 'html':
                switch ($Node->contentType) {
                    case 'Article':
                        $ArticleNode = $Node;
                        $objects['article'] = array_merge($ArticleNode->getAll(), $ArticleNode->File->getAll());
                        $objects['article']['path'] = str_replace($this->Tree->getRoot(), '', $ArticleNode->path);
                        $objects['article']['releaseDate'] = date(DATETIME_HUMAN, $ArticleNode->File->release_uts);
                        $Node = $this->Tree->load(PencilController::BLOG)->loadContent()->getFirst();
                        $objects['page'] = array_merge($Node->getAll(), $Node->File->getAll(),
                        [
                            'path'      => str_replace($this->Tree->getRoot(), '', $Node->path),
                            'bodyClass' => $this->pathClasses($pagePath),
                        ]);

                    // Fall through
                    case 'Page':
                        /** @var Page $Page */
                        $Page = $Node->File;
                        if (0 < $Page->template_id) {
                            $Template = $this->Tree->descendants(PencilController::TEMPLATES, [
                                'contentType' => 'Template',
                                'node_id'     => $Page->template_id,
//                    'published'   => true,
//                    'trashed'     => false,
                            ])->first()->loadContent()->getFirst();
                            if (false === $Template) {
                                trigger_error('Template Node '.$Page->template_id.' not found');
                                header("HTTP/1.0 500 Internal Server Error");
                                die;
                            }
                            $objects['template'] = array_merge(
                                $Template->getAll(),
                                $Template->File->getAll(),
                                ['path' => str_replace($this->Tree->getRoot(), '', $Template->path)]);
                        }

                        $ThemeNode = $this->Tree->descendants(PencilController::THEMES, [
                            'contentType' => 'Theme',
                            'node_id'     => $Site->theme_id,
//                    'published'   => true,
//                    'trashed'     => false,
                        ])->first()->loadContent()->getFirst();
                        if (false === $ThemeNode) {
                            trigger_error('Theme Node '.$Site->theme_id.' not found');
                            header("HTTP/1.0 500 Internal Server Error");
                            die;
                        }
                        /** @var Theme $Theme */
                        $Theme            = $ThemeNode->File;
                        $objects['theme'] = array_merge($ThemeNode->getAll(), $Theme->getAll(),
                            ['path' => str_replace($this->Tree->getRoot(), '', $ThemeNode->path)]);

                        $ContentNodes = $this->Tree->children($Node->path, [
                            'contentType' => 'Text',
                        ])->loadContent()->get();
                        $objects['content'] = [];
                        foreach ($ContentNodes as $ContentNode) {
                            $objects['content'][$ContentNode->label] = $ContentNode->File->body;
                        }
                        $result = $Theme->document;
                        break;
                    default:
                        break;
                }
                break;
            default:

                $vars = $Node->getAll();
                if (!isset($vars[$mode])) {
                    $vars = $Node->File->getAll();
                }
                if (isset($vars[$mode])) {
                    $types = [0 => $vars['mimeType'] ?? 'text/plain', 'css' => 'text/css'];
                    header('Content-Type: '.($types[$mode] ?? $types[0]));

                    // This doesn't make it to the browser
                    // header('Content-Length: '.strlen($vars[$mode]));
                    header('Last-Modified: '.gmdate(DATETIME_HTTP, strtotime($vars['updated_dts'])));
                    header('Expires: '.gmdate(DATETIME_HTTP, NOW + 86400));
                    // This mysteriously causes the browser to not get the entire response.
                    // header('Cache-Control: private');
                    // This too.
                    // header('Cache-Control: max-age=84600');

                    $result = $vars[$mode];
                }
                break;
        }

        // Apply Overrides
        $objects = array_replace_recursive($objects, $overrides);

        // Process merge codes
        do {
            $codes = $this->mergeCodes($result);
            foreach ($codes as $code) {
                if (!isset($objects[$code[1]][$code[2]])) {
                    switch ($code[0]) {
                        case '[blog.categorySelector]':
                            /** @var BlogWorkflow $BlogWF */
                            $BlogWF = G::build(BlogWorkflow::class, $this->Tree);
                            $objects[$code[1]][$code[2]] = $BlogWF->getCategorySelector();
                            break;
                        case '[blog.archiveSelector]':
                            /** @var BlogWorkflow $BlogWF */
                            $BlogWF = G::build(BlogWorkflow::class, $this->Tree);
                            $objects[$code[1]][$code[2]] = $BlogWF->getArchiveSelector();
                            break;
                        default:
                            if (!isset($objects[$code[1]])) {
                                // TODO: Change the merge code syntax to prevent errors in Javascript
                                $objects[$code[1]][$code[2]] = "[ {$code[1]}.{$code[2]} ]";
                            } else {
                                trigger_error('Unresolvable merge code: '.$code[0]);
                                $objects[$code[1]][$code[2]] = '';
                            }
                            break;
                    }
                }
                $result = str_replace($code[0], $objects[$code[1]][$code[2]], $result);
            }
        } while (!empty($codes));

        return $result;
    }

    /**
     * Find valid merge codes in given document
     *
     * @param string $document Content containing merge codes
     *
     * @return mixed
     */
    public function mergeCodes(string $document) {
        preg_match_all('~\[(\w+)\.(\w+)\]~', $document, $matches, PREG_SET_ORDER);

        return $matches;
    }

    /**
     * Generate a series of css classes based on given path
     *
     * @param string $path Source for classes
     *
     * @return string
     */
    public function pathClasses($path) {
        $labels   = explode('/', trim($path, '/'));
        $result   = '';
        $progress = '';
        foreach ($labels as $label) {
            $progress = ($progress ? ' '.$progress.'_' : '').$label;
            $result   .= $progress;
        }

        return $result;
    }
}
