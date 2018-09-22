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
    const allowedTypes = [
        'application/pdf', 'application/postscript',
        'audio/basic', 'audio/mid', 'audio/mpeg', 'audio/x-aiff', 'audio/x-wav',
        'image/png', 'image/gif', 'image/jpeg', 'image/svg+xml',
        'text/css', 'text/csv', 'text/html', 'text/plain', 'text/richtext', 'text/x-vcard',
        'video/mpeg', 'video/mp4', 'video/quicktime',
    ];

    public $error = '';

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
}
