<?php
/**
 * PencilWorkflow - For creating content
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
use Stationer\Pencil\models\Node;
use Stationer\Pencil\models\Page;
use Stationer\Pencil\models\Site;
use Stationer\Pencil\models\Template;
use Stationer\Pencil\models\Text;
use Stationer\Pencil\models\Theme;

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
class WebsiteWorkflow {
    /** @var ArboristWorkflow */
    protected $Tree;
    /** @var DataBroker */
    protected $DB;

    /**
     * Have a Tree
     *
     * @param ArboristWorkflow $Tree
     * @param DataBroker       $DB
     */
    public function __construct(ArboristWorkflow $Tree, DataBroker $DB = null) {
        $this->DB   = $DB ?? G::build(DataBroker::class);
        $this->Tree = $Tree;
    }

    /**
     * Fetch and return the Node for the root of the website
     *
     * @return Node
     */
    public function getSiteRoot() {
        $SiteNode = $this->Tree->load('')->loadContent()->getFirst();
        if (!is_a($SiteNode, Node::class)) {
            $this->initSite();
            $SiteNode = $this->Tree->load('')->loadContent()->getFirst();
        }

        return $SiteNode;
    }

    /**
     * Completely delete the Node tree for the current website
     */
    public function resetSite() {
        $root = $this->Tree->getRoot();
        $this->Tree->setRoot('')->delete($root, true)->create($root)->setRoot($root)->setPath('');
    }

    /**
     * Build a new blank / default website
     *
     * @return Node
     */
    public function initSite() {
        // If the site-root doesn't have a contentType, it didn't exist, so create Site record, also
        $SiteNode = $this->Tree->create('')->getFirst();

        // Ensure other key nodes exist
        $this->Tree->create(PencilController::WEBROOT);
        $this->Tree->create(PencilController::COMPONENTS);
        $this->Tree->create(PencilController::TEMPLATES);
        $this->Tree->create(PencilController::FORMS);
        $this->Tree->create(PencilController::MEDIA);
        $this->Tree->create(PencilController::LANDING);
        $this->Tree->create(PencilController::ERROR);
        $this->Tree->create(PencilController::THEMES);

        $ThemeNode    = $this->createDefaultTheme();
        $TemplateNode = $this->createDefaultTemplate();
        $PageNode     = $this->createDefaultPages($TemplateNode);
        $this->createErrorPages($TemplateNode);

        if (empty($SiteNode->contentType)) {
            /** @var Site $Site */
            $Site                 = G::build(Site::class);
            $Site->title          = 'Newly Drawn Pencil Website';
            $Site->defaultPage_id = $PageNode->node_id;
            $Site->theme_id       = $ThemeNode->node_id;
            $this->DB->insert($Site);
            $SiteNode->contentType = Site::getTable();
            $SiteNode->content_id  = $Site->site_id;
            $this->DB->update($SiteNode);
        }
        $SiteNode = $this->Tree->loadContent()->getFirst();

        return $SiteNode;
    }

    /**
     * Create the default Theme in the tree
     *
     * @return Node
     */
    public function createDefaultTheme() {
        /** @var Theme $Theme */
        $Theme = G::build(Theme::class);
        /** @var Node $Node */
        $Node = G::build(Node::class);

        $Theme->document = <<<'EOT'
<!doctype html>
<html lang="en-US">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>[page.title] - [site.title]</title>
  <link rel="shortcut icon" href="/favicon.ico">
  <link rel="icon" href="/favicon.ico">
  <link rel="stylesheet" type="text/css" href="[theme.path].css">
  <link rel="stylesheet" type="text/css" href="[template.path].css">
</head>
<body class="[page.bodyClass]">
[theme.header]
[template.body]
[theme.footer]
</body>
</html>
EOT;
        $Theme->header   = '<h1>[page.title] - [site.title]</h1>';
        $result          = $this->DB->insert($Theme);

        $Node->label = 'Default Theme';
        if (false !== $result) {
            $this->Tree->create(PencilController::THEMES.'/'.$Node->label, [
                'File'        => $Theme,
                'creator_id'  => G::$S->Login->login_id ?? 0,
                'published'   => 1,
                'description' => 'A default theme',
                'trashed'     => 0,
            ]);

            $Node = $this->Tree->setPath(PencilController::THEMES.'/'.$Node->label)->load()->loadContent()->getFirst();
            if (is_a($Node, Node::class)) {
                G::msg("The theme has been successfully created", 'success');
            }
        }

        return $Node;
    }

    /**
     * Create the default Template in the tree
     *
     * @return Node
     */
    public function createDefaultTemplate() {
        /** @var Template $Template */
        $Template = G::build(Template::class);
        /** @var Node $Node */
        $Node = G::build(Node::class);

        $Template->body = <<<'EOT'
<div class="template-[template.label]">
<header>[content.header]</header>
<main>[content.html1]</main>
<footer>[content.footer]</footer>
</div>
EOT;
        $result         = $this->DB->insert($Template);
        $Node->label    = 'Single Cell Template';
        if (false !== $result) {
            $this->Tree->create(PencilController::TEMPLATES.'/'.$Node->label, [
                'File'        => $Template,
                'creator_id'  => G::$S->Login->login_id ?? 0,
                'published'   => 1,
                'description' => 'A default template',
                'trashed'     => 0,
            ]);

            $Node = $this->Tree->load(PencilController::TEMPLATES.'/'.$Node->label)->loadContent()->getFirst();
            if (is_a($Node, Node::class)) {
                G::msg("The template has been successfully created", 'success');
            }
        }

        return $Node;
    }

    /**
     * Create error pages for 403, 404, 500
     *
     * @param Node $TemplateNode Node for Template for Page
     */
    public function createErrorPages($TemplateNode) {
        $this->createSimplePage(PencilController::ERROR, 403, [
            'title'       => 'Access Denied',
            'body'        => '<h1>[page.title]</h1>',
            'template_id' => $TemplateNode->node_id,
        ]);
        $this->createSimplePage(PencilController::ERROR, 404, [
            'title'       => 'Page Not Found',
            'body'        => '<h1>[page.title]</h1>',
            'template_id' => $TemplateNode->node_id,
        ]);
        $this->createSimplePage(PencilController::ERROR, 500, [
            'title'       => 'Mysterious Error',
            'body'        => '<h1>[page.title]</h1>',
            'template_id' => $TemplateNode->node_id,
        ]);
    }

    /**
     * Create default Pages for blog and home
     *
     * @param Node $TemplateNode Node for Template for Page
     *
     * @return Node The Node object for the home page
     */
    public function createDefaultPages($TemplateNode) {
        $this->createSimplePage(PencilController::WEBROOT, 'blog', [
            'title'       => 'Blog',
            'body'        => '<p>This is the default blog page.  Login to edit.</p>',
            'template_id' => $TemplateNode->node_id,
        ]);

        return $this->createSimplePage(PencilController::WEBROOT, 'home', [
            'title'       => 'Default Page',
            'body'        => '<p>This is the default page.  Login to edit.</p>',
            'template_id' => $TemplateNode->node_id,
        ]);
    }

    /**
     * Create a simple page.  If a template is not specified, the body is
     * assumed HTML
     *
     * @param string $path  Page path relative to site root
     * @param string $label Node label for new page
     * @param array  $data  All other (optional) data
     *
     * @return Node
     */
    public function createSimplePage(string $path, string $label, array $data) {
        /** @var Page $Page */
        $Page = G::build(Page::class);
        /** @var Node $Node */
        $Node = G::build(Node::class);

        $Page->title       = $data['title'] ?? '';
        $Page->template_id = $data['template_id'] ?? 0;
        $result            = $this->DB->insert($Page);

        if (isset($data['template_id'])) {
            // Get the list of Template Nodes, without the Templates
            $Templates = $this->Tree->descendants(PencilController::TEMPLATES, [
                'contentType' => 'Template',
            ])->get();
            // If the specified template actually exists, use it
            if (isset($Templates[$data['template_id'] ?? null])) {
                $Page->template_id = $data['template_id'];
            }
        }

        if (false !== $result) {
            $Node->label = $label ?: $data['title'];
            $this->Tree->create($path.'/'.$Node->label, [
                'File'        => $Page,
                'label'       => $Node->label,
                'creator_id'  => G::$S->Login->login_id ?? 0,
                'published'   => $data['published'] ?? 1,
                'description' => $data['description'] ?? '',
                'trashed'     => $data['trashed'] ?? 0,
                'featured'    => $data['featured'] ?? 0,
                'keywords'    => $data['keywords'] ?? '',
            ]);

            $Node = $this->Tree->setPath($path.'/'.$Node->label)->load()->loadContent()->getFirst();
            if (is_a($Node, Node::class)) {
                G::msg("The page node has been successfully created", 'success');
                $this->createContentNode($Node->path, 'html1', $data);
            }
        }

        return $Node;
    }

    /**
     * Create a simple page.  If a template is not specified, the body is
     * assumed HTML
     *
     * @param string $path  Page path relative to site root
     * @param string $label Node label for new page
     * @param array  $data  All other (optional) data
     *
     * @return Node
     */
    public function createContentNode(string $path, string $label, array $data) {
        /** @var Text $Text */
        $Text = G::build(Text::class);
        /** @var Node $Node */
        $Node = G::build(Node::class);

        $Text->body = $data['body'] ?? '';
        $result     = $this->DB->insert($Text);

        if (false !== $result) {
            $Node->label = $label ?: $data['title'];
            $this->Tree->create($path.'/'.$Node->label, [
                'File'        => $Text,
                'label'       => $Node->label,
                'creator_id'  => G::$S->Login->login_id ?? 0,
                'published'   => $data['published'] ?? 1,
                'description' => $data['description'] ?? '',
                'trashed'     => $data['trashed'] ?? 0,
                'featured'    => $data['featured'] ?? 0,
                'keywords'    => $data['keywords'] ?? '',
            ]);

            $Node = $this->Tree->setPath($path.'/'.$Node->label)->load()->loadContent()->getFirst();
            if (is_a($Node, Node::class)) {
                G::msg("The text node has been successfully created", 'success');
            }
        }

        return $Node;
    }
}
