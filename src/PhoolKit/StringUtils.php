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
 * Static helper methods for strings.
 * 
 * This class is not final so you can use it as a base class for your own
 * string utility class.
 *
 * @author Klaus Reimer (k@ailis.de)
 */
class StringUtils
{
    /**
     * Private constructor to prevent instantiation.
     */
    private function __construct()
    {
        // Nothing to do here
    }

    /**
     * Escapes the specified string so it can be sagely printed into
     * JavaScript output.
     *
     * @param string $text
     *            The text to escape.
     * @return string
     *            The escaped text.
     */
    public final static function escapeJS($text)
    {
        return preg_replace(
            array(
                "/\\\\/",
                "/\r/",
                "/\n/",
                "/\"/",
                "/'/"),
            array(
                "\\\\\\\\",
                "\\r",
                "\\n",
                "\\\"",
                "\\'"),
            $text);
    }
}
