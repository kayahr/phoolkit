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
 * Internationalization support.
 *
 * @author Klaus Reimer (k@ailis.de)
 */
final class I18N
{
    /**
     * Map from message key to message
     * (Pre-filled with default PhoolKit messages).
     */
    private static $messages = array(
        "phoolkit.validation.required" => "This field is required.",
        "phoolkit.validation.minLength" => "Please enter at least %d characters",
        "phoolkit.validation.maxLength" => "Please enter no more than %d characters",
        "phoolkit.validation.mask" => "Please enter a valid value"
    );

    /**
     * Private constructor to prevent instantiation.
     */
    private function __construct()
    {
        // Nothing to do here
    }

    /**
     * Returns the message for the given message key. If no message was found
     * for the key then the key itself is returned surrounded with question
     * marks as an indicator that this message is missing.
     *
     * @param string $key
     *            The message key.
     * @param mixed $args___
     *            Variable number of optional arguments used by the message.
     * @return The resolved message.
     */
    public static function getMessage($key, $args___ = NULL)
    {
        if (!array_key_exists($key, self::$messages))
            return "???$key???";
        $message = self::$messages[$key];
        if (func_num_args() == 1) return $message;
        $args = func_get_args();
        array_shift($args);
        return vsprintf($message, $args);
    }

    /**
     * Adds the given messages.
     *
     * @param array $messages
     *            Map from keys to messages.
     */
    public static function addMessages($messages)
    {
        foreach ($messages as $key => $message)
            self::$messages[$key] = $message;
    }

    /**
     * Adds a single message.
     *
     * @param string $key
     *            The message key.
     * @param string $message
     *            The message.
     */
    public static function addMessage($key, $message)
    {
        self::$messages[$key] = $message;
    }

    /**
     * Loads messages from a PHP file which returns an array (Map from keys
     * to messages.)
     *
     * @param string $filename
     *            The filename of the PHP file to load the messages from.
     */
    public static function loadMessages($filename)
    {
        self::addMessages(include($filename));
    }
}
