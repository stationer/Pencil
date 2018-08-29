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
    /** @var ArboristWorkflow  */
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
     * @param Node $Node
     *
     * @return string
     */
    public function render(Node $Node) {
        $result = '';
        switch ($Node->contentType) {
            case 'Page':
                $SiteNode = $this->Tree->load('')->loadContent()->getFirst();
                $Theme    = $this->Tree->descendants(PencilController::THEMES, [
                    'contentType' => 'Theme',
                    'node_id'     => $SiteNode->File->theme_id,
//                    'published'   => true,
//                    'trashed'     => false,
                ])->first()->loadContent()->getFirst();
                $result = $Theme->File->document;
                $objects = ['page' => $Node->File, 'site' => $SiteNode->File, 'theme' => $Theme->File];
                do {
                    $codes = $this->mergeCodes($result);
                    foreach ($codes as $code) {
                        $result = str_replace($code[0], $objects[$code[1]][$code[2]], $result);
                    }
                } while (!empty($codes));
                break;
            default:
                break;
        }

        return $result;
    }

    public function mergeCodes($document) {
        preg_match_all('~\[(\w+)\.(\w+)\]~', $document, $matches, PREG_SET_ORDER);

        return $matches;
    }
}
