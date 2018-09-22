<?php
/**
 * P_CacheController - Cache Controller
 *
 * PHP version 7.0
 *
 * @package  Stationer\Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */

namespace Stationer\Pencil\controllers;

use Stationer\Graphite\Controller;
use const Stationer\Graphite\DATETIME_HTTP;
use Stationer\Graphite\G;
use Stationer\Graphite\View;

/**
 * Class P_CacheController
 *
 * @package  Stationer\Pencil\controllers
 * @category Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */
class P_CacheController extends Controller {
    /** @var string Default action */
    protected $action = 'create';

    public function action() {
        return $this->action;
    }

    /**
     * List all components
     *
     * @param array $argv    Argument list passed from Dispatcher
     * @param array $request Request_method-specific parameters
     *
     * @return View
     */
    public function do_create(array $argv = [], array $request = []) {
        ob_start();
        // $pattern = '~^P_Cache/(?:(?P<max>\d+)|(?P<width>\d+)x(?P<height>\d+))/(.*)$~';
        $pattern = '~^P_Cache/(\d+)x(\d+)(/.*)$~';
        $valid   = preg_match($pattern, $request['_path'], $matches);
        if (false === $valid) {
            trigger_error("Invalid P_Cache URL: ".$request['_path']);

            return (G::build(P_RenderController::class))->do_404($argv, $request);
        }
        list(, $req_width, $req_height, $path) = $matches;
        if (2 > strlen($path)) {
            trigger_error("Invalid P_Cache URL: ".$request['_path']);

            return (G::build(P_RenderController::class))->do_404($argv, $request);
        }
        $original = SITE.$path;
        if (!file_exists($original)) {
            trigger_error("P_Cache URL Original Not Found: ".$request['_path']);

            return (G::build(P_RenderController::class))->do_404($argv, $request);
        }

        list($og_width, $og_height) = $info = getimagesize($original);
        $mimetype = $info['mime'];

        // if the request max size is wider than the original, use height as the limit
        if ($req_width / $req_height > $og_width / $og_height) {
            $new_height = $req_height;
            $new_width  = $og_width * $req_height / $og_height;
        } else {
            $new_width  = $req_width;
            $new_height = $og_height * $req_width / $og_width;
        }

        $fullPath = dirname(SITE.'/'.$request['_path']);
        if (!is_dir($fullPath) && !mkdir($fullPath, 0755, true) && !is_dir($fullPath)) {
            trigger_error("Unable to create directory: ".$request['_path']);

            return (G::build(P_RenderController::class))->do_404($argv, $request);
        }
        switch ($mimetype) {
            case 'image/png':
                $image = imagecreatefrompng($original);
                $image = imagescale($image, $new_width, $new_height);
                imagepng($image, SITE.'/'.$request['_path']);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($original);
                $image = imagescale($image, $new_width, $new_height);
                imagegif($image, SITE.'/'.$request['_path']);
                break;
            case 'image/jpeg':
                $image = imagecreatefromjpeg($original);
                $image = imagescale($image, $new_width, $new_height);
                imagejpeg($image, SITE.'/'.$request['_path']);
                break;
            default:
                trigger_error("P_Cache URL Original Not a supported image: ".$request['_path']);

                return (G::build(P_RenderController::class))->do_404($argv, $request);
        }
        ob_end_clean();

        header('HTTP/1.0 200 OK');
        header('Content-Type: '.$mimetype);
        header('Content-Length: '.filesize(SITE.'/'.$request['_path']));
        header('Last-Modified: '.gmdate(DATETIME_HTTP));
        header_remove('Cache-Control');
        header_remove('Expires');
        header_remove('Pragma');
        readfile(SITE.'/'.$request['_path']);

        ob_start();

        G::close();
        exit;
    }

}
