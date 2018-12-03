<?php
/**
 * BlogWorkflow -
 *
 * PHP version 7.0
 *
 * @package  Stationer\Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */

namespace Stationer\Pencil;

use Stationer\Graphite\data\DataBroker;
use Stationer\Graphite\G;
use Stationer\Pencil\reports\ArticlesPerMonthReport;
use Stationer\Pencil\reports\NodesPerTagReport;

/**
 * BlogWorkflow -
 *
 * PHP version 7.0
 *
 * @package  Stationer\Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */
class BlogWorkflow {
    /** @var ArboristWorkflow */
    protected $Tree;

    /** @var DataBroker */
    protected $DB;

    /**
     * constructor.
     *
     * @param ArboristWorkflow $Tree
     * @param DataBroker|null  $DB
     */
    public function __construct(ArboristWorkflow $Tree, DataBroker $DB = null) {
        $this->Tree = $Tree;
        $this->DB   = $DB ?? G::build(DataBroker::class);
    }

    public function getCategorySelector() {
        $options = $this->DB->fetch(NodesPerTagReport::class, [
            'path'        => $this->Tree->getRoot(),
            'published'   => 1,
            'contentType' => 'Article',
        ]);

        $result = '<select name="categorySelector" id="categorySelector" class="form-control categorySelector"
                    onchange="this.form.submit();">'
            .'<option value="">[Any Category]</option>';
        if (is_array($options)) {
            foreach ($options as $key => $row) {
                $options[$key] = '<option value="'.$row['tag'].'">'
                    .$row['tag'].' ('.$row['count'].')</option>';
            }
        }
        $result .= implode('', $options).'</select>';

        return $result;
    }

    public function getArchiveSelector() {
        $options = $this->DB->fetch(ArticlesPerMonthReport::class, [
            'path'      => $this->Tree->getRoot(),
            'published' => 1,
        ]);

        $result = '<select name="archiveSelector" id="archiveSelector" class="form-control archiveSelector"
                    onchange="this.form.submit();">'
            .'<option value="">[Any Date]</option>';
        if (is_array($options)) {
            foreach ($options as $key => $row) {
                $options[$key] = '<option value="'.$row['year'].' '.$row['month'].'">'
                    .$row['year'].' '.$row['monthname'].' ('.$row['count'].')</option>';
            }
        }
        $result .= implode('', $options).'</select>';

        return $result;
    }
}
