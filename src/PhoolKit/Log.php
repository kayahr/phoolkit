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
 * A simple logger which logs to the PHP error log as configured in the 
 * php.ini or to stdout/stderr if running on the command line.
 *
 * @author Klaus Reimer (k@ailis.de)
 */
class Log
{
    /** Constant for debug log level. */
    const DEBUG = 1;
    
    /** Constant for info log level. */
    const INFO = 2;
    
    /** Constant for warn log level. */
    const WARN = 3;
    
    /** Constant for error log level. */
    const ERROR = 4;
    
    /** The current log level. */
    private static $level = self::INFO;
    
    /** The application name. */
    private static $name = NULL;
    
    /**
     * Private constructor to prevent instantiation.
     */
    private function __construct()
    {
        // Empty
    }
    
    /**
     * Sets the log level.
     * 
     * @param number $level
     *            The log level to set.
     */
    public static function setLevel($level)
    {
        self::$level = $level;
    }
    
    /**
     * Returns the current log level.
     * 
     * @return number
     *            The current log level.
     */
    public static function getLevel()
    {
        return self::$level;
    }
    
    /**
     * Sets the application name. This is only used in the apache error log
     * to distinguish error messages from different applications and from 
     * PHP errors. If not set, then no application name is printed.
     * 
     * @param string $name
     *            The application name.
     */
    public static function setName($name)
    {
        self::$name = $name;
    }
    
    /**
     * Returns the currently set application name,
     * 
     * @return string
     *            The application name. May be NULL if not set.
     */
    public static function getName()
    {
        return self::$name;
    }
    
    /**
     * Logs a message to STDOUT or STDERR when running in the CLI or to the
     * error log as configured in the php.ini.
     * 
     * @param number $level
     *            The log level.
     * @param string $format
     *            The message format string.
     * @param array $args
     *            Array with message arguments.
     */
    private static function log($level, $format, $args)
    {
        $message = vsprintf($format, $args);
        
        if (php_sapi_name() == "cli")
        {
            if ($level <= self::DEBUG) $levelStr = "DEBUG";
            else if ($level == self::INFO) $levelStr = "INFO";
            else if ($level == self::WARN) $levelStr = "WARN";
            else $levelStr = "ERROR";
            $timestamp = strftime("%Y-%m-%d %T");
            $out = sprintf("%s %-5s %s\n", $timestamp, $levelStr, $message); 
            if ($level == self::ERROR)
                fprintf(STDERR, $out);
            else
                fprintf(STDOUT, $out);
        }
        else
        {
            if ($level <= self::DEBUG) $levelStr = "Debug";
            else if ($level == self::INFO) $levelStr = "Info";
            else if ($level == self::WARN) $levelStr = "Warning";
            else $levelStr = "Error";
            $out = sprintf("%s: %s", ucfirst(strtolower($levelStr)), $message);
            if (self::$name) $out = self::$name . " $out";
            error_log($out);
        }
    }
        
    /**
     * Logs a debug message to STDOUT or the apache error log.
     * 
     * @param string $message
     *            The message to log.
     * @param mixed $args___
     *            Variable number of message arguments.
     */
    public static function debug($message, $args___ = NULL)
    {
        if (self::$level > self::DEBUG) return;
        $args = func_get_args();
        array_shift($args);
        self::log(self::DEBUG, $message, $args);
    }
        
    /**
     * Logs an info message to STDOUT or the apache error log.
     * 
     * @param string $message
     *            The message to log.
     * @param mixed $args___
     *            Variable number of message arguments.
     */
    public static function info($message, $args___ = NULL)
    {
        if (self::$level > self::INFO) return;
        $args = func_get_args();
        array_shift($args);
        self::log(self::INFO, $message, $args);
    }
    
    /**
     * Logs an warning to STDOUT or the apache error log.
     * 
     * @param string $message
     *            The message to log.
     * @param mixed $args___
     *            Variable number of message arguments.
     */
    public static function warn($message, $args___ = NULL)
    {
        if (self::$level > self::WARN) return;
        $args = func_get_args();
        array_shift($args);
        self::log(self::WARN, $message, $args);
    }
    
    /**
     * Logs an error message to STDERR or the apache error log.
     * 
     * @param string $message
     *            The message to log.
     * @param mixed $args___
     *            Variable number of message arguments.
     */
    public static function error($message, $args___ = NULL)
    {
        if (self::$level > self::ERROR) return;
        $args = func_get_args();
        array_shift($args);
        self::log(self::ERROR, $message, $args);
    }
}
