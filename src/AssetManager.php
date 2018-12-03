<?php
/**
 * AssetManager - For managing assets
 *
 * PHP version 7.0
 *
 * @package  Stationer\Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */

namespace Stationer\Pencil;

/**
 * AssetManager - For managing assets
 *
 * PHP version 7.0
 *
 * @package  Stationer\Pencil
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 * @see      /src/models/Node.php
 */
class AssetManager {
    /** @var string Path relative to site root where we will put uploaded files */
    public static $uploadPath = '/p.uploads';

    // TODO Create a friendly name list for these
    /** @var array Whitelist of file MIME types */
    const allowedTypes = [
        'application/pdf', 'application/postscript',
        'audio/basic', 'audio/mid', 'audio/mpeg', 'audio/x-aiff', 'audio/x-wav',
        'image/png', 'image/gif', 'image/jpeg', 'image/svg+xml', 'image/vnd.microsoft.icon',
        'text/css', 'text/csv', 'text/html', 'text/plain', 'text/richtext', 'text/x-vcard',
        'video/mpeg', 'video/mp4', 'video/quicktime',
    ];

    /** @var array Whitelist of file extensions */
    const importExtentions = [
        '.pdf', '.ps',
        '.au', '.mid', '.mp3', '.aiff', '.wav',
        '.png', '.gif', '.jpg', '.jpeg', '.svg', '.ico',
        '.css', '.csv', '.html', '.txt',
        '.mpeg', '.mp4', '.mov',
    ];

    public $error = '';

    /**
     * Download remote file
     *
     * @TODO: polish
     * @TODO: URL DECODE PATHS
     *
     * @param string $remotePath
     * @param string $assetPath
     *
     * @return string
     */
    public function download($remotePath, $assetPath) {
        // Validate file extension
        $ext = strtolower(pathinfo($remotePath, PATHINFO_EXTENSION));
        if (!in_array(".".$ext, self::importExtentions)) {
            echo "Rejecting invalid extension '$ext' in $remotePath<br>";

            return false;
        }

        // Create local directory
        $assetPath = fakepath(static::$uploadPath.$assetPath.preg_replace('~^\w+://~', '', $remotePath));
        $fullPath  = SITE.$assetPath;
        $filename  = basename($fullPath);
        $dirPath   = dirname($fullPath);
        if (!is_dir($dirPath) && !mkdir($dirPath, 0755, true) && !is_dir($dirPath)) {
            echo "Failed to create local directory at $dirPath<br>";

            return false;
        }

        // Download file
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $remotePath);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        $file = curl_exec($curl_handle);
        curl_close($curl_handle);
        if (false === $file) {
            echo "Failed to open remote file at $remotePath<br>";

            return false;
        }

        // Write file to local server
        $result = file_put_contents($fullPath, $file);
        if (false === $result) {
            echo "Failed to write file to $fullPath<br>";

            return false;
        }

        // On success, return the path of the saved file, relative to SITE root
        return $assetPath;
    }

    /**
     * Update
     *
     * @param array  $file
     * @param string $assetPath
     *
     * @return bool|string
     */
    public function upload(array $file, string $assetPath) {
        $this->error = '';
        if (empty($file)) {
            return false;
        }
        if (!empty($file['error'])) {
            trigger_error($this->error = 'Error uploading file: '.$file['error']);

            return false;
        }
        if (!in_array($file['type'], self::allowedTypes)) {
            trigger_error($this->error = 'Rejecting upload of unsupported type: '.$file['type']);

            return false;
        }

        $fullPath = SITE.static::$uploadPath.$assetPath;
        // redundant check tests whether another process created it
        if (!is_dir($fullPath) && !mkdir($fullPath, 0755, true) && !is_dir($fullPath)) {
            trigger_error($this->error = 'Unable to create upload directory: '.SITE.static::$uploadPath.$assetPath);

            return false;
        }

        $destination = static::$uploadPath.$assetPath.'/'.static::cleanFilename($file['name']);
        $result      = move_uploaded_file($file['tmp_name'], SITE.$destination);
        if (!$result) {
            $this->error = 'Failed to upload assets, I don\'t know why.';

            return false;
        }

        return $destination;
    }

    /**
     * Given a requested cache image
     * Determine the requested size and original file
     * Produce the requested resized image and save in requested path.
     *
     * @param string $req_path Path requested by browser
     *
     * @return bool false on failure
     */
    public function resize(string $req_path) {
        $pattern = '~^P_Cache/(\d+)x(\d+)(/.*)$~';
        $valid   = preg_match($pattern, $req_path, $matches);
        if (false === $valid) {
            trigger_error($this->error = "Invalid P_Cache URL: ".$req_path);

            return false;
        }
        list(, $req_width, $req_height, $path) = $matches;
        if (2 > strlen($path)) {
            trigger_error($this->error = "Invalid P_Cache URL: ".$req_path);

            return false;
        }
        $original = SITE.$path;
        if (!file_exists($original)) {
            trigger_error($this->error = "P_Cache URL Original Not Found: ".$req_path);

            return false;
        }

        list($og_width, $og_height) = $info = getimagesize($original);
        // If the original has no dimensions, use the original file
        if ($og_height < 1 || $og_width < 1) {
            return $path;
        }
        $mimetype = $info['mime'];

        // if the request max size is wider than the original, use height as the limit
        if ($req_width / $req_height > $og_width / $og_height) {
            $new_height = $req_height;
            $new_width  = $og_width * $req_height / $og_height;
        } else {
            $new_width  = $req_width;
            $new_height = $og_height * $req_width / $og_width;
        }
        // If the original is smaller than the request, use the original size
        if ($og_height < $new_height || $og_width < $new_width) {
            return $path;
        }

        $fullPath = dirname(SITE.'/'.$req_path);
        // redundant check tests whether another process created it
        if (!is_dir($fullPath) && !mkdir($fullPath, 0755, true) && !is_dir($fullPath)) {
            trigger_error($this->error = "Unable to create directory: ".$req_path);

            return false;
        }
        switch ($mimetype) {
            case 'image/png':
                $image = imagecreatefrompng($original);
                $image = imagescale($image, $new_width, $new_height);
                imagepng($image, SITE.'/'.$req_path);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($original);
                $image = imagescale($image, $new_width, $new_height);
                imagegif($image, SITE.'/'.$req_path);
                break;
            case 'image/jpeg':
                $image = imagecreatefromjpeg($original);
                $image = imagescale($image, $new_width, $new_height);
                imagejpeg($image, SITE.'/'.$req_path);
                break;
            default:
                trigger_error($this->error = "P_Cache URL Original Not a supported image: ".$req_path);

                return false;
        }

        return $req_path;
    }

    /**
     * Replace space with underscore and other non-word chars with dash
     *
     * @param string $fileName Label to clean
     *
     * @return string
     */
    public static function cleanFilename(string $fileName) {
        $fileName = basename($fileName);
        $fileName = str_replace(' ', '_', $fileName);
        $fileName = preg_replace('~[^-\w.]~', '-', $fileName);

        return $fileName;
    }

    public function scan($assetPath) {
        exec('find '.SITE.self::$uploadPath.$assetPath, $fileList);

        $result = [];
        foreach ($fileList as $key => $file) {
            // Make sure it's a regular file of a supported mimetime
            $mimetype = mime_content_type($file);
            if (is_file($file) && in_array($mimetype, self::allowedTypes)) {
                $result[$file] = $mimetype;
            }
        }

        return $result;
    }
}
