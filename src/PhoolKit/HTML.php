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
 * Helper methods for printing certain HTML snippets. It is recommended to
 * inlude this file in all pages and create an alias for it:
 *
 * <pre>
 * use PhoolKit/HTML as h;
 * </pre>
 *
 * Then the methods of this class can be accessed in a very short way:
 *
 * <pre>
 * <?h::text("Some text to escape")?>
 * <?h::url("login")?>
 * </pre>
 *
 * This class is not final so you can use it as a base class for your own
 * HTML helper classes.
 *
 * @author Klaus Reimer (k@ailis.de)
 */
class HTML
{
    /** The currently bound form. */
    private static $form = NULL;

    /** If auto focus was already set in a form. */
    private static $alreadySetAutoFocus;

    /**
     * Private constructor to prevent instantiation.
     */
    private function __construct()
    {
        // Nothing to do here
    }

    /**
     * Prints the specified text into the HTML output. The text is
     * property escaped.
     *
     * @param string $text
     *            The text to print.
     */
    public final static function text($text)
    {
        echo htmlspecialchars($text);
    }

    /**
     * Prints the specified text into the JavaScript output. The text is
     * property escaped.
     *
     * @param string $text
     *            The text to print.
     */
    public final static function jsText($text)
    {
        echo StringUtils::escapeJS($text);
    }

    /**
     * Prints the specified URL to the HTML output in a resolved form. The
     * URL must be relative to the web root and this method takes care of
     * adding the base URL, no matter in what sub directory the page actually
     * was in or what PATH_INFO was specified. The printed URL is properly
     * escaped.
     *
     * @param string $url
     *            The URL to print. If not specified then only the base
     *            directory is printed (Useful for linking to the root index
     *            page)
     */
    public final static function url($url = "")
    {
        $baseUrl = Request::getBaseUrl();
        if (($baseUrl != ".") || !$url)
        {
            echo htmlspecialchars($baseUrl);
            echo "/";
        }
        echo htmlspecialchars($url);
    }

    /**
     * Same as the url() method but intended to be used inside of javascript
     * blocks. It is properly escaped.
     *
     * @param string $url
     *            The URL to print. If not specified then only the base
     *            directory is printed (Useful for linking to the root index
     *            page)
     */
    public final static function jsUrl($url = "")
    {
        $baseUrl = Request::getBaseUrl();
        if (($baseUrl != ".") || !$url)
        {
            echo StringUtils::escapeJS($baseUrl);
            echo "/";
        }
        echo StringUtils::escapeJS($url);
    }

    /**
     * Prints the attributes to be put into the form tag for the currently
     * bound form.
     */
    public final static function form()
    {
        echo "onsubmit=\"return new phoolkit.Form(this).submit(function(){\n";
        foreach (self::getForm()->getValidators() as $validator)
        {
            echo htmlspecialchars($validator->getScript());
        }
        echo "})\"";

        // Reset form specific flags
        self::$alreadySetAutoFocus = false;
    }

    /**
     * Prints the attributes for initially autofocusing a form field.
     */
    public final static function autoFocus()
    {
        if (!self::getForm()->hasErrors()) echo " autofocus";
    }

    /**
     * Prints the attributes for a normal form input field (text, password and
     * hidden).
     *
     * @param string $name
     *            The form field name.
     * @param string $id
     *            Optional ID. Defaults to field name.
     */
    public final static function input($name, $id = NULL)
    {
        $form = self::getForm();
        $value = $form->readProperty($name);
        if (!$id) $id = $name;
        $setAutoFocus = !self::$alreadySetAutoFocus && $form->hasErrors($name);
        if ($setAutoFocus) self::$alreadySetAutoFocus = $setAutoFocus;
        printf("id=\"%s\" name=\"%s\" value=\"%s\"%s",
            htmlspecialchars($id),
            htmlspecialchars($name),
            htmlspecialchars($value),
            $setAutoFocus ? " autofocus" : ""
        );
    }

    /**
     * Prints the attributes for a form checkbox input field.
     *
     * @param string $name
     *            The form field name.
     * @param string $id
     *            Optional ID. Defaults to field name.
     */
    public final static function checkbox($name, $id = NULL)
    {
        $form = self::getForm();
        $value = $form->readProperty($name);
        if (!$id) $id = $name;
        $setAutoFocus = !self::$alreadySetAutoFocus && $form->hasErrors($name);
        if ($setAutoFocus) self::$alreadySetAutoFocus = $setAutoFocus;
        printf("type=\"checkbox\" id=\"%s\" name=\"%s\" value=\"1\"%s%s",
            htmlspecialchars($id),
            htmlspecialchars($name),
            $value ? " checked" : "",
            $setAutoFocus ? " autofocus" : ""
        );
    }

    /**
     * Prints the attributes for a form radio input field.
     *
     * @param string $name
     *            The form field name.
     * @param string $value
     *            The value of the radio button.
     * @param string $id
     *            Optional ID. Defaults to field name.
     */
    public final static function radio($name, $value, $id = NULL)
    {
        $form = self::getForm();
        $realValue = $form->readProperty($name);
        if (!$id) $id = $name . "-" . $value;
        $setAutoFocus = !self::$alreadySetAutoFocus && $form->hasErrors($name);
        if ($setAutoFocus) self::$alreadySetAutoFocus = $setAutoFocus;
        printf("type=\"radio\" id=\"%s\" name=\"%s\" value=\"%s\"%s%s",
            htmlspecialchars($id),
            htmlspecialchars($name),
            htmlspecialchars($value),
            $value == $realValue ? " checked" : "",
            $setAutoFocus ? " autofocus" : ""
        );
    }

    /**
     * Prints the attributes for a form select field.
     *
     * @param string $name
     *            The form field name.
     * @param string $id
     *            Optional ID. Defaults to field name.
     */
    public final static function select($name, $id = NULL)
    {
        $form = self::getForm();
        if (!$id) $id = $name;
        $setAutoFocus = !self::$alreadySetAutoFocus && $form->hasErrors($name);
        if ($setAutoFocus) self::$alreadySetAutoFocus = $setAutoFocus;
        printf(" id=\"%s\" name=\"%s\"%s",
            htmlspecialchars($id),
            htmlspecialchars($name),
            $setAutoFocus ? " autofocus" : ""
        );
    }

    /**
     * Prints the options for a form select field.
     *
     * @param $name
     *            The field name.
     * @param $options
     *            A map with the options. Map key is the value used in the
     *            form. Map value is the displayed text.
     */
    public final static function options($name, $options)
    {
        $form = self::getForm();
        $realValue = $form->readProperty($name);
        foreach ($options as $value => $caption)
        {
            printf("<option value=\"%s\"%s>%s</option>\n",
                htmlspecialchars($value),
                $value == $realValue ? " selected" : "",
                htmlspecialchars($caption)
            );
        }
    }

    /**
     * Prints the HTML code for the error list of a form field.
     *
     * @param string $name
     *            The form field name.
     */
    public final static function errors($name = NULL)
    {
        $errors = $name ? self::getForm()->getErrors($name) : array();
        printf("<ul class=\"errors%s\">",
            $name ? " " . htmlspecialchars($name) : "");
        foreach ($errors as $error)
        {
            echo "<li>";
            echo htmlspecialchars($error);
            echo "</li>";
        }
        echo "</ul>";
    }

    /**
     * Binds the specified form.
     *
     * @param Form $form
     *           The form to bind.
     */
    public final static function bindForm($form)
    {
        self::$form = $form;
    }

    /**
     * Returns the bound form. If no form is bound then an exception is thrown.
     *
     * @return Form
     *            The bound form. Never null.
     * @throws LogicException
     *            When no form is bound.
     */
    private static function getForm()
    {
        if (!self::$form)
            throw new LogicException("No form bound to HTML");
        return self::$form;
    }

    /**
     * Resolves a message key into a message and prints it. The output is
     * HTML escaped.
     *
     * @param string $key
     *            The message key.
     * @param mixed $args___
     *            Variable number of optional arguments used by the message.
     * @return The resolved message.
     */
    public final static function msg($key, $args___ = NULL)
    {
        echo htmlspecialchars(call_user_func_array(
            array("\PhoolKit\I18N", "getMessage"), func_get_args()));
    }

    /**
     * Resolves a message key into a message and prints it. The output is
     * JavaScript escaped.
     *
     * @param string $key
     *            The message key.
     * @param mixed $args___
     *            Variable number of optional arguments used by the message.
     * @return The resolved message.
     */
    public final static function jsMsg($key, $args___ = NULL)
    {
        echo StringUtils::escapeJS(call_user_func_array(
            array("\PhoolKit\I18N", "getMessage"), func_get_args()));
    }

    /**
     * Resolves a message key into a message and prints it. The output is
     * not escaped in any way. Use this for messages which might contain
     * HTML tags and you are sure it doesn't harm your website.
     *
     * @param string $key
     *            The message key.
     * @param mixed $args___
     *            Variable number of optional arguments used by the message.
     * @return The resolved message.
     */
    public final static function rawMsg($key, $args___ = NULL)
    {
        echo StringUtils::escapeJS(call_user_func_array(
            array("\PhoolKit\I18N", "getMessage"), func_get_args()));
    }

    /**
     * Shortcut for Request::getParam()
     *
     * @param string $name
     *            The parameter name.
     * @param string $default
     *            Optional default value.
     * @return string
     *            The parameter value or the default value.
     */
    public final static function param($name, $default = NULL)
    {
        return Request::getParam($name, $default);
    }
}
