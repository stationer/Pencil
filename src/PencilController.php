<?php
/**
 * PencilController - Theme Controller
 *
 * PHP version 7.0
 *
 * @package  Stationer\Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */

namespace Stationer\Pencil;

use Stationer\Graphite\data\PassiveRecord;
use Stationer\Graphite\G;
use Stationer\Graphite\View;
use Stationer\Graphite\Controller;
use Stationer\Graphite\data\IDataProvider;
use Stationer\Pencil\models\Node;

/**
 * Class P_ThemeController
 *
 * @package  Stationer\Pencil\controllers
 * @category Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */
abstract class PencilController extends Controller {
    const WEBROOT = '/webroot';
    const ERROR = '/error';
    const LANDING = '/landing';
    const BLOG = '/webroot/blog';
    const COMPONENTS = '/components';
    const TEMPLATES = '/templates';
    const FORMS = '/forms';
    const ASSETS = '/assets';
    const THEMES = '/themes';
    const NAVIGATION = '/navigation';

    /** @var string Required Role, TODO set to false for no requirement while testing */
    protected $role = 'Pencil';

    /** @var string Tree path of current site root */
    protected $siteRoot = '';

    /** @var ArboristWorkflow */
    protected $Tree;

    /** @var WebsiteWorkflow */
    protected $Website;

    /**
     * Controller constructor
     *
     * @param array         $argv Argument list passed from Dispatcher
     * @param IDataProvider $DB   DataProvider to use with Controller
     * @param View          $View Graphite View helper
     */
    public function __construct(array $argv = [], IDataProvider $DB = null, View $View = null) {
        parent::__construct($argv, $DB, $View);

        // Set the default root path to the current site.
        $root = '/sites/'.$_SERVER['SERVER_NAME'];
        $this->Tree = G::build(ArboristWorkflow::class, $this->DB);
        $this->Tree->setRoot('')->create($root)->setRoot($root)->setPath('');
        $this->Website = G::build(WebsiteWorkflow::class, $this->Tree, $this->DB);

        $this->View->setTemplate('header', 'Pencil._header.php');
        $this->View->setTemplate('footer', 'Pencil._footer.php');
        $this->View->setTemplate('debug', 'footer.debug.php');
        $this->View->_style(str_replace(SITE, '', __DIR__.'/css/Pencil.css'));
        $this->View->_style('https://cdn.quilljs.com/1.0.0/quill.snow.css');
        $this->View->_script(str_replace(SITE, '', __DIR__.'/js/Pencil.js'));
        $this->View->_script('https://cdn.quilljs.com/1.0.0/quill.js');
        $this->View->_script(str_replace(SITE, '', __DIR__.'/js/Nib.js'));

        $this->View->treeRoot = $this->Tree->getRoot();
    }

    public function insertNode(array $request, PassiveRecord $File) {
        // Set the checkbox values according to whether they were checked
        $request['published'] = !empty($request['published']);
        $request['trashed']   = !empty($request['trashed']);
        $request['featured']  = !empty($request['featured']);

        // Sanitize the destination path
        $request['label'] = Node::cleanLabel($request['label']);
        $request['parentPath'] = Node::cleanPath($request['parentPath']);

        // Create the new Node
        $request['created_uts'] = NOW;
        $request['creator_id']  = G::$S->Login->login_id ?? 0;
        $Node = $this->Tree->create($request['parentPath'].'/'.$request['label'], $request)->getFirst();

        // If we created a Node, insert the File, too!
        if (is_a($Node, Node::class)) {
            $File->setAll($request);
            $result = $this->DB->insert($File);
            if (false !== $result) {
                $Node->File = $File;
                // Finally, update the node to refer to the new file.
                $this->DB->update($Node);
                return $Node;
            }
        }
        return false;
    }

    public function getNode($node_id) {
        return $this->Tree->search([
            'contentType' => static::CONTENT_TYPE,
            'node_id'     => $node_id
        ])->loadContent()->getFirst();
    }

    public function updateNode(Node $Node, array $request) {
        // Set the checkbox values according to whether they were checked
        $request['published'] = !empty($request['published']);
        $request['trashed']   = !empty($request['trashed']);
        $request['featured']  = !empty($request['featured']);
        // Accept and Save changes to Node
        $Node->setAll($request, true);
        $result = $this->DB->save($Node);

        // If we got a new parent path, move the Node
        if (isset($request['parentPath']) && $request['parentPath'] != dirname($Node->path)) {
            $this->Tree->setPath($Node->path)->move($request['parentPath']);
        }

        $File = $Node->File;
        $File->setAll($request, true);
        $result2    = $this->DB->save($File);
        $Node->File = $File;

        // Return success if both saves were successful.
        return (in_array($result, [null, true]) && in_array($result2, [null, true]));
    }

    public function resultMessage($result) {
        if ($result) {
            G::msg('The changes to this '.static::CONTENT_TYPE.' have been successfully saved.', 'success');
        } else {
            G::msg('There was a problem saving your '.static::CONTENT_TYPE.'.', 'error');
        }
    }
}
