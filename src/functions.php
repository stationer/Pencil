<?php
/**
 * Useful functions to use
 *
 * PHP version 7.0
 *
 * @package  Stationer\Pencil
 * @author   Tyler Uebele
 * @license  MIT https://github.com/stationer/Pencil/blob/master/LICENSE
 * @link     https://github.com/stationer/Pencil
 */


/**
 * Fakes a path, similar to realpath, but, fake
 *
 * @param string $path      Path
 * @param string $separator Directory Separator, defaults to system DIRECTORY_SEPARATOR
 *
 * @return string
 */
function fakepath($path, $separator = DIRECTORY_SEPARATOR) {
    $parts = explode($separator, str_replace(['/', '\\'], $separator, $path));
    $path  = [];

    foreach ($parts as $part) {
        // Ignore references to current directory
        if ('' == $part || '.' == $part) {
            continue;
        }
        // Step back for references to parent directory
        if ('..' == $part) {
            array_pop($path);
            continue;
        }
        // Accept new parts
        $path[] = $part;
    }

    return $separator.implode($separator, $path);
}
