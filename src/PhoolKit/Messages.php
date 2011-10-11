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

use LogicException;

/**
 * Container for error and info messages. Errors are stored at request scope
 * but info messages are stored in the HTTP session and are automatically
 * removed after first retrieval.
 *
 * @author Klaus Reimer (k@ailis.de)
 */
final class Messages
{
    /** Global error messages. */
    private static $errors = array();
    
    /**
     * Private constructor to prevent instantiation.
     */
    private function __construct()
    {
        // Nothing to do.
    }    
    
    /**
     * Cloning a singleton is not allowed.
     */
    public function __clone()
    {
        throw new LogicException("Can't clone singleton");
    }
    
    /**
     * Adds a global error message.
     *
     * @param string $message
     *            The error message to add.
     */
    public static function addError($message)
    {
        self::$errors[] = $message;
    }
    
    /**
     * Returns (and creates if neccessary) the array with the info
     * messages.
     * 
     * @return array
     *            The array with the info messages as reference so adding
     *            to it is possible.
     */
    private static function &initInfos()
    {
        if (isset($_SESSION["PhoolKit_Messages_infos"]))
        {
            $infos = &$_SESSION["PhoolKit_Messages_infos"];
        }
        else
        {
            $infos = array();
            $_SESSION["PhoolKit_Messages_infos"] = &$infos;
        }
        return $infos;
    }

    /**
     * Returns the array with the info messages.
     * 
     * @return array
     *            The array with the info messages.
     */
    public static function getInfos()
    {
        return self::initInfos();
    }
    
    /**
     * Adds a new info message.
     * 
     * @param string $message
     *            The info message to add.
     */
    public static function addInfo($message)
    {
        $infos = &self::initInfos();
        $infos[] = $message;
    } 
    
    /**
     * Removes all info messages.
     */
    public static function removeInfos()
    {
        unset($_SESSION["PhoolKit_Messages_infos"]);
    }
    
    /**
     * Checks if info messages are available.
     * 
     * @return boolean
     *            True if info messages are available, false if not.
     */
    public static function hasInfos()
    {
        return !!self::getInfos();
    }

    /**
     * Returns all global error messages.
     *
     * @return array
     *            The error messages. Never null. Maybe empty.
     */
    public static function getErrors()
    {
        return self::$errors;
    }

    /**
     * Checks if global errors are available.
     *
     * @return boolean
     *            True if errors are available, false if not.
     */
    public static function hasErrors()
    {
        return !!$this->errors;
    }
}
