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

/**
 * Class P_AssetController
 *
 * @package  Stationer\Pencil\controllers
 * @category Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */
class P_AssetController extends PencilController {
    /** @var string Default action */
    protected $action = 'list';

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

        $Assets = $this->Tree->descendants(self::ASSETS, ['contentType' => 'Asset'])->loadContent()->get();

        $this->View->Assets = $Assets;
        $this->View->treeRoot = $this->Tree->getRoot();

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
        $Node = G::build(Node::class);
        /** @var Asset $Asset */
        $Asset      = G::build(Asset::class);
        $Node->File = $Asset;

        if ('POST' === $this->method && isset($_FILES['upload'])) {
            $assetPath = $this->Tree->setPath(PencilController::ASSETS)->getFullPath();
            /** @var AssetManager $AssetManager */
            $AssetManager = G::build(AssetManager::class);
            $assetPath    = $AssetManager->upload($_FILES['upload'], $assetPath);
            $Asset->path  = $assetPath;
            $Asset->type  = $_FILES['upload']['type'];
            // Only attempt the save if we got an assetPath
            if (false === $assetPath) {
                $error = $AssetManager->error ?: 'Upload not detected.  Please select a file.';
                G::msg($error, 'error');
                $result = false;
            } else {
                $result = $this->DB->insert($Asset);
            }
            if (false !== $result) {
                $Node->label = $request['label'] ?: basename($assetPath);
                G::msg($Node->label);
                $this->Tree->create(PencilController::ASSETS.'/'.$Node->label, [
                    'File'        => $Asset,
                    'label'       => $Node->label,
                    'creator_id'  => G::$S->Login->login_id ?? 0,
                    'published'   => isset($request['published']),
                    'description' => $request['description'],
                    'trashed'     => isset($request['trashed']),
                    'featured'    => isset($request['featured']),
                    'keywords'    => $request['keywords'],
                ]);

                $Node = $this->Tree->setPath(PencilController::ASSETS.'/'.$Node->label)->load()->getFirst();
                if (is_a($Node, Node::class)) {
                    G::msg("The asset has been successfully created", 'success');
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

            $error = $AssetManager->error;
            if (!empty($error)) {
                G::msg($error, 'error');
            }

            $request['path'] = $assetPath;
            $request['type'] = $_FILES['upload']['type'];

            $Node->setAll($request, true);
            $Asset = $Node->File;
            $Asset->setAll($request, true);
            $Node->File($Asset);

            $result1 = $this->DB->save($Node);
            $result2 = $this->DB->save($Asset);

            if (false !== $result1 && false !== $result2) {
                G::msg("The asset has been successfully updated.", 'success');
            } else {
                G::msg("There was a problem updating this asset.", 'error');
            }
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
        $fileList     = $AssetManager->scan($assetPath);
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
                    $Asset      = G::build(Asset::class);
                    $Node->File = $Asset;
                    $Asset->path = AssetManager::$uploadPath.$this->Tree->getRoot().$file;
                    $Asset->type = $fileList[SITE.AssetManager::$uploadPath.$this->Tree->getRoot().$file];
                    $result = $this->DB->insert($Asset);
                    $Node->label = basename($file);
                    $this->Tree->create(dirname($file).'/'.$Node->label, [
                        'File'        => $Asset,
                        'label'       => $Node->label,
                        'creator_id'  => G::$S->Login->login_id ?? 0,
                    ]);

                    $Node = $this->Tree->setPath(dirname($file).'/'.$Node->label)->load()->getFirst();
                    if (is_a($Node, Node::class)) {
                        G::msg("The asset has been successfully created", 'success');
                    }
                }
            }
        }

        // Prepare View
        $data         = [];
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
    }
}
