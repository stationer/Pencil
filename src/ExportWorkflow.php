<?php
/**
 * ExportWorkflow - For managing Exports
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
use Stationer\Graphite\models\Login;
use Stationer\Pencil\models\Article;
use Stationer\Pencil\models\Node;

/**
 * ExportWorkflow - For managing Exports; reading and writing export files
 *
 * PHP version 7.0
 *
 * @package  Stationer\Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */
class ExportWorkflow {
    /** @var ArboristWorkflow */
    protected $Tree;
    /** @var DataBroker */
    protected $DB;

    /**
     * ExportWorkflow constructor.
     *
     * @param ArboristWorkflow $Tree
     * @param DataBroker|null  $DB
     */
    public function __construct(ArboristWorkflow $Tree, DataBroker $DB = null) {
        $this->Tree = $Tree;
        $this->DB   = $DB ?? G::build(DataBroker::class);
    }

    /**
     * Parse a WordPress RSS XML file and import its content
     *
     * @param string $xml
     */
    public function importWordPressXML($xml) {
        /** @var \SimpleXMLElement $RSS */
        $RSS = new \SimpleXMLElement($xml);
        croak((string)$RSS->channel[0]->title);
        $contentTypes = ['post' => 'Article', 'page' => 'Page', 'attachment' => 'Asset', '' => 'Text'];
        $categoryTypes = ['post_tag' => 'tag'];
        $data         = [];

        // Loop the item nodes and map the content to our format
        foreach ($RSS->channel[0]->item as $item) {
            $wp     = $item->children('wp', true);
            $dc     = $item->children('dc', true);
            $datum  = [
                'created_uts' => trim($wp->post_date),
                'updated_dts' => trim($wp->post_date),
                'path'        => trim($item->link),
                'pathAlias'   => trim($item->link),
                'contentType' => $contentTypes[trim($wp->post_type ?? '')],
                'published'   => trim($wp->status) == 'publish',
                'creator_id'  => $this->getLoginIdByEmail(trim($dc->creator ?? '')),

                'release_uts' => trim($item->pubDate),
                'author_id'   => $this->getLoginIdByEmail(trim($dc->creator ?? '')),
                'title'       => trim($item->title),
                'body'        => trim($item->children('content', true)->encoded),

                'post_type' => trim($wp->post_type ?? ''),
                'tags' => [],
            ];
            if ($item->category) {
                foreach ($item->category as $cat) {
                    $type = trim($cat['domain']);
                    $datum['tags'][] = [
                        'label' => trim($cat),
                        'type' => $categoryTypes[$type] ?? $type,
                    ];
                }
            }

            $data[] = $datum;
        }

        // Filter the items into three groups
        $assets = array_filter($data, function ($row) {
            return $row['contentType'] == 'Asset';
        });
        $pages  = array_filter($data, function ($row) {
            return $row['contentType'] == 'Page';
        });
        $blogs  = array_filter($data, function ($row) {
            return $row['contentType'] == 'Article';
        });

        // Import the attachments/Assets first so we can translate paths in content
        /** @var AssetManager $AM */
        $AM        = G::build(AssetManager::class);
        $assetPath = $this->Tree->getRoot().PencilController::ASSETS.'/import_'.date('YmdHis').'/';
        $assetMap  = [];
        foreach ($assets as $datum) {
            $localPath                = $AM->download($datum['path'], $assetPath);
            $assetMap[$datum['path']] = $localPath;
            // TODO: Create Node/Asset for each file, workaround: use Asset/import
        }

        // Import the pages
        foreach ($pages as $datum) {
            break;
            // TODO: Import pages

        }

        // Import the blog Articles
        $oldPaths = array_keys($assetMap);
        $newPaths = array_values($assetMap);
        foreach ($blogs as $datum) {
            if ('Article' === $datum['contentType']) {
                // ensure article path starts with standard blog path
                if ('/blog/' == substr($datum['path'], 0, 6)) {
                    $datum['path'] = substr($datum['path'], 5);
                }
                $datum['path'] = PencilController::BLOG.$datum['path'];

                // Ensure we don't clobber an existing Node
                $Node = $this->Tree->load($datum['path'])->getFirst();
                if ($Node) {
                    echo "Node already found at ".$datum['path']."<br>";
                    // For testing, delete existing articles, otherwise skip it
                    if (false) {
                        echo "Deleting Node at ".$datum['path']."<br>";
                        $this->Tree->first()->delete();
                    } else {
                        continue;
                    }
                }
                echo "Creating Node at ".$datum['path']."<br>";

                // Create a new Node
                $Node = $this->Tree->create($datum['path'], $datum)->getFirst();
                echo "Created Node at ".$Node['path']."<br>";
                $File = G::build(Article::class);
                if (is_a($Node, Node::class)) {
                    $datum['body'] = str_replace($oldPaths, $newPaths, $datum['body']);
                    $datum['body'] = preg_replace('~\[/?caption[^]]*\]~i', '', $datum['body']);
                    $File->setAll($datum);
                    $result = $this->DB->insert($File);
                    if (false !== $result) {
                        $Node->File = $File;
                        // Finally, update the node to refer to the new file.
                        $this->DB->update($Node);
                    }
                    foreach ($datum['tags'] as $tag) {
                        $this->Tree->tag($tag['label'], $tag['type']);
                    }
                }
            }
        }

        // TODO: Provide a proper response
        die;
    }

    /**
     * Given an email address, fetch or create a Login for the given email address
     *
     * @param $email
     *
     * @return int|mixed
     */
    public function getLoginIdByEmail($email) {
        static $login_ids = [];
        $DB = G::build(DataBroker::class);

        // If we already know the login_id, return it
        if (isset($login_ids[$email])) {
            return $login_ids[$email];
        }

        // Fetch the Login for the email.  If found, return the login_id
        $Logins = $DB->fetch(Login::class, ['email' => $email]);
        if (!empty($Logins)) {
            $login_ids[$email] = reset($Logins)->login_id;

            return $login_ids[$email];
        }

        // Create a new login, return its login_id
        $Login  = G::build(Login::class, ['email' => $email, 'loginname' => $email, 'disabled' => 1]);
        $result = $DB->insert($Login);
        if (is_int($result)) {
            $login_ids[$email] = $Login->login_id;
        }

        // For some reason all that failed, return the current user
        return G::$S->Login->login_id;
    }
}
