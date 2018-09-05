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

    public function render($json) {
        $data = json_decode($json);
        croak($data);
        $html = '';

        if ($data->links) {
            $links = $data->links;
            $html  = $this->renderLinks($links);
        }

        $html = '<nav class="pencil-navigation">'.$html.'</nav>';

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
                $url = $link->path; // look up the node, though
            } elseif (isset($link->node_id)) {
                $url = $link->node_id; // look up the node, though
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
