<?php
/**
 * ArticlesPerMonthReport - Fetch Counts of Nodes published each month
 *
 * PHP version 7.0
 *
 * @package  Stationer\Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */

namespace Stationer\Pencil\reports;

use Stationer\Graphite\data\Report;
use Stationer\Pencil\models\Article;
use Stationer\Pencil\models\Node;

/**
 * Class ArticlesPerMonthReport
 *
 * @package Stationer\Pencil\reports
 * @author  Tyler Uebele
 *
 */
class ArticlesPerMonthReport extends Report {
    protected static $query = "";
    protected static $vars = [
        'path'      => ['type' => 's', 'sql' => "n.`path` LIKE '%s/%%'"],
        'published' => ['type' => 'b', 'sql' => "n.`published` = b'%d'"],
        'trashed'   => ['type' => 'b', 'sql' => "n.`trashed` = b'%d'"],
        'featured'  => ['type' => 'b', 'sql' => "n.`featured` = b'%d'"],
    ];

    public function __construct($a = null, bool $b = null) {
        $table  = Article::getTable();
        $table2 = Node::getTable();

        static::$query = "
SELECT COUNT(`article_id`) AS `count`, YEAR(FROM_UNIXTIME(`release_uts`)) AS `year`,
    MONTHNAME(FROM_UNIXTIME(`release_uts`)) AS `monthname`,
    MONTH(FROM_UNIXTIME(`release_uts`)) AS `month`
FROM `$table` a
INNER JOIN `$table2` n ON a.`article_id` = n.`content_id` AND n.`contentType` = 'Article'
WHERE %s
GROUP BY YEAR(FROM_UNIXTIME(`release_uts`)) DESC, MONTH(FROM_UNIXTIME(`release_uts`)) DESC
";
        parent::__construct($a, $b);
    }
}
