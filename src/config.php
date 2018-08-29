<?php
/**
 * Module config.
 * File : /src/config.php
 *
 * Namespace to Stationer\Graphite
 *
 * PHP version 7.0
 *
 * @package  Stationer\Pencil
 * @author   Tyler Uebele
 * @license  MIT https://github.com/stationer/Graphite/blob/master/LICENSE
 * @link     https://github.com/stationer/Graphite
 */

namespace Stationer\Graphite;

G::$G['namespaces'][] = '\\Stationer\\Pencil\\';
G::$G['namespaces'][] = '\\Stationer\\Pencil\\models\\';
G::$G['namespaces'][] = '\\Stationer\\Pencil\\reports\\';
G::$G['namespaces'][] = '\\Stationer\\Pencil\\controllers\\';

G::$G['db']['ProviderDict'][\Stationer\Pencil\models\Node::class] = \Stationer\Pencil\data\TreeMySQLDataProvider::class;
G::$G['CON']['controller404'] = 'P_Render';
