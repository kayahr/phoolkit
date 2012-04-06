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
    
	/** The parsed media types. */
    private static $mediaTypes = NULL;
    
    /** The parsed locales. */
    private static $locales = NULL;
    
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
            // Count the number of folders of REQUEST_URI.
            $requestUriFolders = substr_count($_SERVER["REQUEST_URI"], "/");

            // Count the number of folders of SCRIPT_NAME.
            $scriptNameFolders = substr_count($_SERVER["SCRIPT_NAME"], "/");
            
            // Count the number of folders of SCRIPT_FILENAME.
            $scriptFilenameFolders = substr_count(realpath(dirname(
                $_SERVER["SCRIPT_FILENAME"])), DIRECTORY_SEPARATOR);
                
            // Count the number of folders of the base directory.
            $baseDirFolders = substr_count($baseDir, DIRECTORY_SEPARATOR);
            
            // Calculate the relative base url.
            $baseUrl = str_repeat("/..", $scriptFilenameFolders - 
                $baseDirFolders - $scriptNameFolders + $requestUriFolders);
            if (!$baseUrl)
                $baseUrl = ".";
            else
                $baseUrl = substr($baseUrl, 1);
        }
        else
        {
            // When no base directory was specified then we just assume the
            // base URL is the current path.
            $baseUrl = ".";
        }
        
        // Cache and return the calculated base URL.
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

    /**
     * Returns the list of accepted media types. The list is ordered by the
     * accept quality. So the first entry is the most preferred one.
     *
     * @return array
     *            The list of accepted media types.
     */
    public static function getMediaTypes()
    {
        if (is_null(self::$mediaTypes))
        {
            self::$mediaTypes = self::parseValueRange($_SERVER["HTTP_ACCEPT"]);
        }
        return array_keys(self::$mediaTypes);
    }
    
    /**
     * Ensures the request is for one of the specified media types. If no
     * media type matches then a 406 error is sent back to the client. The
     * first matching media type is returned.
     * 
     */
    public static function requireMediaType($mediaTypes___)
    {
        $allowedMediaTypes = func_get_args();
        $mediaTypes = self::getMediaTypes();
        foreach ($mediaTypes as $mediaType)
        {
            $mediaType = self::matchMediaType($allowedMediaTypes, $mediaType);
            if ($mediaType) return $mediaType; 
        }        
        header("HTTP/1.0 406 Not acceptable");
        header("Content-Type: text/plain");
        echo "Error 406\nNo acceptable media type found.\n";
        echo "Required: " . join(", ", $allowedMediaTypes) . "\n"; 
        echo "Requested: " . join(", ", $mediaTypes) . "\n";
        exit();
    }
    
    /**
     * Returns the list of accepted locales. The list is ordered by the
     * accept quality. So the first entry is the most preferred one.
     *
     * @param array
     *            Optional list of available locales to filter by the accepted
     *            browser locales. If not specified then the browser locales
     *            are returned.
     * @return array
     *            The list of accepted locales.
     */
    public static function getLocales($available = NULL)
    {
        if (!isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) return array();
        if (is_null(self::$locales))
        {
            self::$locales = self::parseValueRange(
                $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
        }
        $locales = array_keys(self::$locales);
        if (is_null($available)) return $locales;
        $result = array();
        foreach ($locales as $locale)
        {
            $locale = self::matchLocale($available, $locale);
            if ($locale)
                if (!in_array($locale, $result)) $result[] = $locale;
        }
        return $result;
    }
    
    /**
     * Tries to match a locale against a list of available locales. Hyphens
     * are converted to underscores. Checks are done case-insensitive.
     * A full locale can match an available language-only locale.
     * 
     * @param array $available
     *            The list of available locales.
     * @param string $locale
     *            The locale to check.
     * @return string
     *            The entry from the available locales which matches the
     *            specified locale. NULL if non matches.
     */
    private static function matchLocale($available, $locale)
    {
        $locale = str_replace("-", "_", strtolower($locale));
        
        // First try to find the full locale
        foreach ($available as $check)
            if (strtolower($check) == $locale) return $check;
        
        // Then try to find only the language
        $pos = strpos($locale, "_");
        if ($pos !== false)
        {
            $locale = substr($locale, 0, $pos);
            foreach ($available as $check)
                if (strtolower($check) == $locale) return $check;
        }
        
        // No match
        return NULL;
    }

    /**
     * Tries to match a media type against a list of available media types.
     *
     * @param array $available
     *            The list of available media types.
     * @param string $mediaType
     *            The media type to check.
     * @return string
     *            The entry from the available media type which matches the
     *            specified media type. NULL if non matches.
     */
    private static function matchMediaType($available, $mediaType)
    {
        $parts = preg_split("/[\\/;]/", $mediaType, 3);
        $type = $parts[0];
        $subtype = $parts[1];
        foreach ($available as $availableMediaType)
        {
            $parts = preg_split("/[\\/;]/", $availableMediaType, 3);
            $availableType = $parts[0];
            $availableSubtype = $parts[1];
            
            if ($type != "*" && $type != $availableType) continue;
            if ($subtype != "*" && $subtype != $availableSubtype) continue;
            
            return $availableMediaType;
        } 
    
        // No match
        return NULL;
    }
    
    /**
     * Returns the preferred locale or the specified default locale if 
     * no locale could be determined automatically.
     * 
     * @param array
     *            Optional list of available locales to filter the accepted
     *            browser locales by.
     * @param default
     *            The default locale to return if no matching locale was
     *            found. Defaults to NULL.
     * @return string
     *             The preferred locale.
     */
    public static function getLocale($available = NULL, $default = NULL)
    {
        $locales = self::getLocales($available);
        return count($locales) > 0 ? $locales[0] : $default;
    }
    
    /**
     * Parses a value range (As used by the "Accept" header for example).
     * Returns an array with the values as key and an array with the
     * quality and the extensions as value.
     *
     * @param string $valueRange
     *            The value range to parse.
     * @return array
     *            The parsed values.
     */
    private static function parseValueRange($valueRange)
    {
        $values = array();
        $ranges = explode(",", $valueRange);
        $index = 0;
        foreach ($ranges as $range)
        {
            $params = explode(";", $range);
            $value = array_shift($params);
            $quality = 1.0;
            $extensions = array();
            foreach ($params as $param)
            {
                $parts = explode("=", $param);
                $extName = array_shift($parts);
                $extValue = array_shift($parts);
                if ($extName == "q")
                    $quality = floatval($extValue);
                else
                    $extensions[$extName] = $extValue;
            }
            $values[$value] = array(
                "quality" => $quality,
                "index" => $index,
                "extensions" => $extensions
            );
            $index++;
        }
        uasort($values, function($a, $b) {
            $q1 = $a["quality"];
            $q2 = $b["quality"];
            if ($q1 == $q2)
            {
                $i1 = $a["index"];
                $i2 = $b["index"];
                return $i1 > $i2 ? 1 : -1;
            }
            if ($q1 > $q2) return -1;
            return 1;
        });
        return $values;
    }

    public static function buildUrl($url = "")
    {
        $baseUrl = self::getBaseUrl();
        if (($baseUrl != ".") || !$url)
            return $baseUrl . "/" . $url;
        return $url;
    }
}
