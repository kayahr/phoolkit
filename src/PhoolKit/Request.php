<?php
/*
 * PhoolKit - A PHP toolkit.
 * Copyright (C) 2011  Klaus Reimer <k@ailis.de>
 *
 * This library is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at
 * your option) any later version.
 *
 * This library is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser
 * General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace PhoolKit;

/**
 * This class provides helper functions to work with the current request.
 *
 * @author Klaus Reimer (k@ailis.de)
 */
final class Request
{
    /** The base directory. */
    private static $baseDir;

    /** The base URL. */
    private static $baseUrl;
    
    /**
     * Returns the value of the parameter with the specified name. When
     * the parameter was not found then the specified default value or NULL
     * if no default is specified is returned.
     *
     * This method always returns the unescaped parameter value, no matter
     * how PHP's magic quotes feature is configured.
     *
     * @param string $name
     *            The parameter name.
     * @param string $default
     *            Optional default value.
     * @return string
     *            The parameter value or the default value.
     */
    public static function getParam($name, $default = NULL)
    {
        if (isset($_REQUEST[$name]))
            return get_magic_quotes_gpc() ? stripslashes($_REQUEST[$name]) :
                $_REQUEST[$name];
        else return $default;
    }

    /**
     * Returns all request parameters. All parameter values are correclty
     * unescaped, no matter how PHP's magic quotes feature is configured.
     *
     * @return array
     *            The request parameters.
     */
    public static function getParams()
    {
        $params = array();
        foreach ($_REQUEST as $name => $value)
            $params[$name] = self::getParam($name);
        return $params;
    }

    /**
     * Returns the base directory. This can't be automatically detected and
     * must be set by the application using PhoolKit. Simply call
     * Request::setBaseDir(dirname(__FILE__)) in your bootstrap PHP
     * file.
     *
     * @return string
     *            The base directory or NULL when no base directory was set.
     */
    public static function getBaseDir()
    {
        return self::$baseDir;
    }

    /**
     * Sets the base directory. This can't be automatically detected and
     * must be set by the application using PhoolKit. Simply call
     * Request::setBaseDir(dirname(__FILE__)) in your bootstrap PHP
     * file.
     *
     * @param string $baseDir
     *            The base directory to set.
     */
    public static function setBaseDir($baseDir)
    {
        self::$baseDir = $baseDir;
    }

    /**
     * Returns the base URL. This URL does not have a trailing slash. If this
     * URL is not set manually (With the setBaseUrl method) then it is
     * automatically calculated by looking at the set base directory, the
     * SCRIPT_FILENAME and the PATH_INFO variables.
     *
     * @return string
     *            The base URL.
     */
    public static function getBaseUrl()
    {
        if (self::$baseUrl) return self::$baseUrl;
        $baseDir = self::getBaseDir();
        if ($baseDir)
        {
            $baseUrl = str_repeat("/..",
                substr_count(realpath(dirname($_SERVER["SCRIPT_FILENAME"])),
                DIRECTORY_SEPARATOR) - substr_count($baseDir,
                DIRECTORY_SEPARATOR) + (isset($_SERVER["PATH_INFO"]) ?
                substr_count($_SERVER["PATH_INFO"], "/") : 0));
            if (!$baseUrl)
                $baseUrl = ".";
            else
                $baseUrl = substr($baseUrl, 1);
        }
        else
        {
            $baseUrl = ".";
        }
        self::$baseUrl = $baseUrl;
        return $baseUrl;
    }

    /**
     * Sets the base URL. This is normally not needed but can be set to
     * skip the auto-calculation of the base URL on each request.
     *
     * @param string $baseUrl
     *            The base URL to set. Must not have a trailing slash.
     */
    public static function setBaseUrl($baseUrl)
    {
        self::$baseUrl = $baseUrl;
    }

    /**
     * Redirects the request to the specified target.
     *
     * TODO Target should be transformed into an absolute URL.
     *
     * @param string $target
     *            The target to redirect to.
     */
    public static function redirect($target)
    {
        header("Location: " . $target);
        exit();
    }
}
