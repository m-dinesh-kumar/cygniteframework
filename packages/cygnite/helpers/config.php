<?php
namespace Cygnite\Helpers;

use InvalidArgumentException;

if (defined('CF_SYSTEM') === false) {
    exit('No External script access allowed');
}

/**
 * Cygnite Framework
 *
 *  An open source application development framework for PHP 5.3x or newer
 *
 *   License
 *
 *   This source file is subject to the MIT license that is bundled
 *   with this package in the file LICENSE.txt.
 *   http://www.cygniteframework.com/license.txt
 *   If you did not receive a copy of the license and are unable to
 *   obtain it through the world-wide-web, please send an email
 *   to sanjoy@hotmail.com so I can send you a copy immediately.
 *
 * @Package              :  Packages
 * @Sub Package          :  Library
 * @Filename             :  CF_Config
 * @Description          :  This file is used to load all framework configurations
 *                          via Registry and store it in order to use it later.
 * @Author               :  Cygnite dev team
 * @Copyright            :  Copyright (c) 2013 - 2014,
 * @Link	                :  http://www.cygniteframework.com
 * @Since	             :  Version 1.0
 * @Filesource
 * @Warning              :  Any changes in this library can cause
 *                          abnormal behaviour of the framework.
 *
 *
 */

$config = array();

// Get the configuration variables.
$config = Config::appsConfig();

Config::setConfigItems('config_items', $config);
unset($config);

// Set URL base path.
Url::setBase(Config::getConfig('global_config', 'base_path'));

/**
 * Class Config
 * @author Sanjoy Dey
 * @package Cygnite\Helpers
 */

class Config
{

    private static $_config = array();


    /**
     * Get the configuration.
     *
     * @param string $arrKey get config based on key
     *
     * @param bool $keyValue get config value
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public static function getConfig($arrKey, $keyValue = false)
    {
        $config = array();

        $config = self::getConfigItems('config_items');

        if ($arrKey === null) {
            throw new InvalidArgumentException(
                'Cannot pass null argument to '.__METHOD__
            );
        }

        if (is_array($config)) {
            if (false !== array_key_exists($arrKey, $config) && $keyValue === false) {
                return $config[$arrKey];
            }

            if (false !== array_key_exists($arrKey, $config) && $keyValue !== false) {
                return $config[$arrKey][$keyValue];
            }
        }

    }//end of getConfig()


    public static function setConfigItems($name, $values = array())
    {
        self::$_config[$name] = $values;

    }//end setConfigItems()

    public static function getConfigItems($key)
    {
        if (is_null($key) == true) {
            throw new InvalidArgumentException(
                'Cannot pass null argument to '.__METHOD__
            );
        }

        return isset(self::$_config[strtolower($key)]) ?
            self::$_config[strtolower($key)] :
            null
            ;

    }

    public static function appsConfig()
    {
        $config = array();
        $config['global_config'] = include_once strtolower(APPPATH).DS.'configs'.DS.'application'.EXT;
        $config['db_config'] = include_once strtolower(APPPATH).DS.'configs'.DS.'database'.EXT;
        $config['session_config'] = include_once strtolower(APPPATH).DS.'configs'.DS.'session'.EXT;
        $config['autoload_config'] = include_once strtolower(APPPATH).DS.'configs'.DS.'autoload'.EXT;
        $config['routing_config'] = include_once strtolower(APPPATH).DS.'routerconfig'.EXT;

      return $config;
    }
}