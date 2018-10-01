<?php
/**
 * NavigationWorkflow - For working with navigations
 *
 * PHP version 7.0
 *
 * @package  Stationer\Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */

namespace Stationer\Pencil;

use Stationer\Graphite\G;

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
class NavigationWorkflow {
    public $Tree;
    public function __construct() {
        $this->Tree = G::build(ArboristWorkflow::class);
        $this->Tree->setRoot('/sites/'.$_SERVER['SERVER_NAME']);
    }

    public function render($json) {
        $data = json_decode($json);

        $html = '';

        if (!empty($data->links)) {
            $links = $data->links;
            $html  = $this->renderLinks($links);
        }

        return $html;
    }

    /**
     * @param $links
     *
     * @return string
     */
    public function renderLinks($links): string {
        $html = '<ul>';
        foreach ($links as $link) {
            unset($url, $text);

            //$html .= '<li>'.ob_var_dump($link).'</li>';
            if (isset($link->url)) {
                $url = $link->url;
            } elseif (isset($link->path)) {
                // If a node with the path doesn't exist it is set to false which removes the link
                $Node = $this->Tree->getByPath($this->Tree->getRoot().PencilController::WEBROOT.$link->path);
                $url = str_replace($this->Tree->getRoot().PencilController::WEBROOT, '', $Node->path);
            } elseif (isset($link->node_id)) {
                // Fetch by the node id
                $Node = $this->Tree->loadID((int)$link->node_id)->loadContent()->getFirst();
                $url = str_replace($this->Tree->getRoot().PencilController::WEBROOT, '', $Node->path);
                if (!isset($link->text)) {
                    $text = $Node->File->title;
                }
            }

            if (isset($link->text)) {
                $text = $link->text;
            }
            if (isset($text)) {
                $html .= '<li>';
                if (isset($url)) {
                    $html .= '<a href="'.$url.'">'.$text.'</a>';
                } else {
                    $html .= $text;
                }
                if (isset($link->links)) {
                    $html .= $this->renderLinks($link->links);
                }

                $html .= '</li>';
            }
        }
        $html .= '</ul>';

        return $html;
    }
}
