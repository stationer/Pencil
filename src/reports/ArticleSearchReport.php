<?php
/**
 * ArticleSearchReport -
 *
 * PHP version 7.0
 *
 * @package  Stationer\Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */

namespace Stationer\Pencil\reports;

use Stationer\Graphite\data\Report;
use Stationer\Graphite\G;
use Stationer\Pencil\models\Article;
use Stationer\Pencil\models\Node;
use Stationer\Pencil\models\Tag;

/**
 * Class ArticleSearchReport
 *
 * @package Stationer\Pencil\reports
 * @author  Tyler Uebele
 *
 */
class ArticleSearchReport extends Report {
    protected static $query = "";
    protected static $vars = [
        'path'      => ['type' => 's', 'sql' => "t.`path` LIKE '%s/%%'"],
        'tag'       => ['type' => 's', 'sql' => "t2.`label` = '%s'"],
        'label'     => ['type' => 's', 'sql' => "t.`label` = '%s'"],
        'published' => ['type' => 'b', 'sql' => "t.`published` = b'%d'"],
        'trashed'   => ['type' => 'b', 'sql' => "t.`trashed` = b'%d'"],
        'featured'  => ['type' => 'b', 'sql' => "t.`featured` = b'%d'"],
        'month'     => ['type' => 'i', 'sql' => "MONTH(FROM_UNIXTIME(a.`release_uts`)) = %d"],
        'year'      => ['type' => 'i', 'sql' => "YEAR(FROM_UNIXTIME(a.`release_uts`)) = %d"],
        'search'    => ['type' => 's', 'sql' => "(a.`title` LIKE '%%%1\$s%%' OR a.`body` LIKE '%%%1\$s%%')"],
    ];

    public function __construct($a = null, bool $b = null) {
        $fields  = array_keys(Node::getFieldList());
        $fields2  = array_keys(Article::getFieldList());
        $table   = Node::getTable();
        $joiner  = Node::getTable('Tag');
        $article = Article::getTable();
        $tag = Tag::getTable();

        static::$query = "
SELECT t.`".join('`, t.`', $fields)."`,
    a.`".join('`, a.`', $fields2)."`
FROM `$table` t
    INNER JOIN `$article` a ON t.`content_id` = a.`article_id` AND t.`contentType` = 'Article'
    LEFT JOIN `$joiner` j ON t.`node_id` = j.`node_id`
    LEFT JOIN `$tag` t2 ON j.`tag_id` = t2.`tag_id`
WHERE %s
GROUP BY t.`node_id`
ORDER BY t.`featured` DESC, a.`release_uts` DESC
";
        parent::__construct($a, $b);
    }

    public function onload() {
        $Nodes = [];
        foreach ($this->_data as $row) {
            $Article = G::build(Article::class);
            $Article->load_array($row);
            $Nodes[$row[Node::getPkey()]] = G::build(Node::class);
            $Nodes[$row[Node::getPkey()]]->load_array($row);
            $Nodes[$row[Node::getPkey()]]->File = $Article;
        }
        $this->_data = $Nodes;
    }
}
