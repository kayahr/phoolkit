<?php
/*
 * PhoolKit - A PHP toolkit.
 * Copyright (C) 2013  Klaus Reimer <k@ailis.de>
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
 * Static helper methods for number formatting.
 * 
 * This class is not final so you can use it as a base class for your own
 * utility class.
 *
 * @author Klaus Reimer (k@ailis.de)
 */
class FormatUtils
{
    /**
     * Private constructor to prevent instantiation.
     */
    private function __construct()
    {
        // Nothing to do here
    }
    
    /**
     * Converts the specified byte string to an integer value. The byte
     * string can be something like "1024", "1K", "1.2 KB", "20548 MB"). The
     * return value is always an integer measured in bytes. 
     *
     * @param string $value
     *            The bytes as a string.
     * @return number
     *            The bytes as an integer.
     */
    public final static function fromBytes($value)
    {
        // If value is already numeric then we don't have to do anything.
        if (is_numeric($value)) return $value;
        
        // Split value into number and unit
        if (preg_match("/^([0-9.]+)\\s*(.)/", $value, $result))
        {
            $value = $result[1];
            $unit = strtolower($result[2]);
            switch ($unit)
            {
            	case 'k':
            	    $value *= 1024;
            	    break;
            	case 'm':
            	    $value *= 1048576;
            	    break;
            	case 'g':
            	    $value *= 1073741824;
            	    break;
            }
        }
        return $value;
    }
    
    /**
     * Converts the specified byte integer value into a easier to read string.
     * 
     * @param string $value
     *            The bytes as an integer.
     * @return number
     *            The bytes as a string.
     */
    public final static function toBytes($value)
    {
        if ($value < 1024) return round($value) . " B";
        if ($value < 1024 * 1024)
            return round($value / 1024) . " KiB";
        else
            return round($value / 1024 / 1024) . " MiB";
    }
}
