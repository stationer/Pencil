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

use Stationer\Graphite\G;
use Stationer\Graphite\data\DataBroker;
use Stationer\Pencil\data\TreeMySQLDataProvider;
use Stationer\Pencil\models\Node;
use Stationer\Pencil\models\Tag;
use Stationer\Pencil\reports\AncestorsByPathReport;
use Stationer\Pencil\reports\DescendantsByPathReport;

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
     * @param Node   $Node
     * @param string $mode Render mode
     *
     * @return string
     */
    public function render(Node $Node, $mode = 'html') {
        $result   = '';
        $SiteNode = $this->Tree->load('')->loadContent()->getFirst();
        switch ($mode) {
            case 'html':
                $pagePath = str_replace($this->Tree->getRoot(), '', $Node->path);
                $objects = [
                    'site' => array_merge($SiteNode->getAll(), $SiteNode->File->getAll(), ['path' => '/']),
                    'page' => array_merge($Node->getAll(), $Node->File->getAll(),
                        [
                            'path'      => $pagePath,
                            'bodyClass' => $this->pathClasses($pagePath),
                        ]),
                ];
                switch ($Node->contentType) {
                    case 'Page':
                        if (0 < $Node->File->template_id) {
                            $Template = $this->Tree->descendants(PencilController::TEMPLATES, [
                                'contentType' => 'Template',
                                'node_id'     => $Node->File->template_id,
//                    'published'   => true,
//                    'trashed'     => false,
                            ])->first()->loadContent()->getFirst();
                            if (false === $Template) {
                                trigger_error('Template Node not found');
                                header("HTTP/1.0 500 Internal Server Error");
                                die;
                            }
                            $objects['template'] = array_merge(
                                $Template->getAll(),
                                $Template->File->getAll(),
                                ['path' => str_replace($this->Tree->getRoot(), '', $Template->path)]);
                        }

                        $Theme    = $this->Tree->descendants(PencilController::THEMES, [
                            'contentType' => 'Theme',
                            'node_id'     => $SiteNode->File->theme_id,
//                    'published'   => true,
//                    'trashed'     => false,
                        ])->first()->loadContent()->getFirst();
                        if (false === $Theme) {
                            trigger_error('Theme Node not found');
                            header("HTTP/1.0 500 Internal Server Error");
                            die;
                        }
                        $objects['theme'] = array_merge($Theme->getAll(), $Theme->File->getAll(),
                            ['path' => str_replace($this->Tree->getRoot(), '', $Theme->path)]);

                        $result   = $Theme->File->document;
                        do {
                            $codes = $this->mergeCodes($result);
                            foreach ($codes as $code) {
                                if (!isset($objects[$code[1]][$code[2]])) {
                                    trigger_error('Unresolvable merge code: '.$code[0]);
                                    $objects[$code[1]][$code[2]] = '';
                                }
                                $result = str_replace($code[0], $objects[$code[1]][$code[2]], $result);
                            }
                        } while (!empty($codes));
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
                    $types = [0 => 'text/plain', 'css' => 'text/css'];
                    header('Content-Type: '.($types[$mode] ?? $types[0]));
                    header('Content-Length: '.strlen($vars[$mode]));
                    header('Last-Modified: '.gmdate('D, d M Y H:i:s', strtotime($vars['updated_dts'])).' GMT');
                    header('Expires: '.date('D, d M Y H:i:s', NOW + 86400));
                    header('Cache-Control: private');

                    echo $vars[$mode];

                    exit;
                }
                break;
        }

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
