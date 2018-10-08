<?php
/**
 * P_AssetController - Asset Controller
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
use Stationer\Pencil\AssetManager;
use Stationer\Pencil\models\Asset;
use Stationer\Pencil\models\Node;
use Stationer\Pencil\PencilController;
use Stationer\Pencil\PencilDashboardController;

/**
 * Class P_AssetController
 *
 * @package  Stationer\Pencil\controllers
 * @category Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */
class P_AssetController extends PencilDashboardController {
    /** @var string Default action */
    protected $action = 'list';

    /** @var string The Node->contentType this Controller works on */
    const CONTENT_TYPE = 'Asset';

    /**
     * Controller constructor
     *
     * @param array         $argv Argument list passed from Dispatcher
     * @param IDataProvider $DB   DataProvider to use with Controller
     * @param View          $View Graphite View helper
     */
    public function __construct(array $argv = [], IDataProvider $DB = null, View $View = null) {
        parent::__construct($argv, $DB, $View);
    }

    /**
     * List assets items
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_list(array $argv = [], array $request = []) {
        if (!G::$S->roleTest($this->role)) {
            return parent::do_403($argv);
        }

        if (isset($request['search'])) {
            // TODO: the search thing
            $Assets = [];
        } else {
            $Assets = $this->Tree->descendants(self::ASSETS, ['contentType' => 'Asset'])->loadContent()->get();
        }

        $this->View->Assets = $Assets;

        return $this->View;
    }

    /**
     * Add a new assets item
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_add(array $argv = [], array $request = []) {
        if (!G::$S->roleTest($this->role)) {
            return parent::do_403($argv);
        }

        /** @var Node $Node */
        $Node       = G::build(Node::class);
        $Node->File = G::build(Asset::class);;

        if ('POST' === $this->method && isset($_FILES['upload'])) {
            $assetPath = $this->Tree->setPath(PencilController::ASSETS)->getFullPath();
            /** @var AssetManager $AssetManager */
            $AssetManager    = G::build(AssetManager::class);
            $assetPath       = $AssetManager->upload($_FILES['upload'], $assetPath);
            $request['path'] = $assetPath;
            $request['type'] = $_FILES['upload']['type'];

            // Only attempt the save if we got an assetPath
            if (false === $assetPath) {
                $error = $AssetManager->error ?: 'Upload not detected.  Please select a file.';
                G::msg($error, 'error');
            } else {
                $request['label']      = $request['label'] ?: basename($assetPath);
                $request['parentPath'] = PencilController::ASSETS;
                $Node                  = $this->insertNode($request, $Node->File);
                $result                = is_a($Node, Node::class);
                $this->resultMessage($result);
                if ($result) {
                    $this->_redirect('/P_Asset/edit/'.$Node->node_id);
                }
            }
        }

        $this->View->Node = $Node;

        return $this->View;
    }

    public function do_edit(array $argv = [], array $request = []) {
        if (!G::$S->roleTest($this->role)) {
            return parent::do_403($argv);
        }

        $Node = $this->Tree->loadID($argv[1])->loadContent()->getFirst();

        if ('POST' === $this->method) {
            $assetPath = $this->Tree->setPath(PencilController::ASSETS)->getFullPath();
            /** @var AssetManager $AssetManager */
            $AssetManager = G::build(AssetManager::class);
            $assetPath    = $AssetManager->upload($_FILES['upload'], $assetPath);
            $error        = $AssetManager->error;
            if (!empty($error)) {
                G::msg($error, 'error');
            }
            $request['path'] = $assetPath;
            $request['type'] = $_FILES['upload']['type'];

            $result = $this->updateNode($Node, $request);
            $this->resultMessage($result);
        }

        $this->View->Node = $Node;

        return $this->View;
    }

    /**
     * Add new assets from the filesystem
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_import(array $argv = [], array $request = []) {
        if (!G::$S->roleTest($this->role)) {
            return parent::do_403($argv);
        }
        $assetPath = $this->Tree->setPath(PencilController::ASSETS)->getFullPath();
        /** @var AssetManager $AssetManager */
        $AssetManager = G::build(AssetManager::class);
        // Get permissible files list
        $fileList = $AssetManager->scan($assetPath);
        // Handle Post
        if ('POST' === $this->method) {
            if (!empty($request['import'])) {
                foreach ($request['import'] as $file => $_) {
                    // Ensure submitted path is within the list fetched above
                    if (!isset($fileList[SITE.AssetManager::$uploadPath.$this->Tree->getRoot().$file])) {
                        G::msg('Cannot import the specified file: '.htmlspecialchars($file), 'error');
                        continue;
                    }
                    G::msg('Importing file: '.htmlspecialchars($file), 'error');
                    /** @var Node $Node */
                    $Node = G::build(Node::class);
                    /** @var Asset $Asset */
                    $Asset       = G::build(Asset::class);
                    $Node->File  = $Asset;
                    $Asset->path = AssetManager::$uploadPath.$this->Tree->getRoot().$file;
                    $Asset->type = $fileList[SITE.AssetManager::$uploadPath.$this->Tree->getRoot().$file];
                    $this->DB->insert($Asset);
                    $Node->label = basename($file);
                    $this->Tree->create(dirname($file).'/'.$Node->label, [
                        'File'       => $Asset,
                        'label'      => $Node->label,
                        'creator_id' => G::$S->Login->login_id ?? 0,
                    ]);

                    $Node = $this->Tree->setPath(dirname($file).'/'.$Node->label)->load()->getFirst();
                    if (is_a($Node, Node::class)) {
                        G::msg("The asset has been successfully created", 'success');
                    }
                }
            }
        }

        // Prepare View
        $data = [];
        foreach ($fileList as $file => $mimetype) {
            $file = substr($file, strlen(SITE));
            // Check whether we already have the file imported
            /** @var Asset $Asset */
            $Asset = $this->DB->fetch(Asset::class, ['path' => $file]);
            $file  = substr($file, strlen(AssetManager::$uploadPath.$this->Tree->getRoot()));
            $Nodes = [];
            if (!empty($Asset)) {
                $Asset = reset($Asset);
                $Nodes = $this->Tree->descendants('', ['contentType' => 'Asset', 'content_id' => $Asset->asset_id])
                                    ->get();
            }
            $data[] = ['path' => $file, 'Asset' => $Asset, 'Nodes' => $Nodes, 'mimetype' => $mimetype];
        }

        $this->View->fileList = $data;

        return $this->View;
    }
}
