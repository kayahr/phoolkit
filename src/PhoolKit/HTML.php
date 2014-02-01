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

    /** The currently bound field. */
    private static $field = NULL;

    /** The index inside the currently bound field. */
    private static $fieldIndex = NULL;

    /** If auto focus was already set in a form. */
    private static $alreadySetAutoFocus;
    
    /** The bbCode parser. */
    private static $bbParser = NULL; 

    /**
     * Private constructor to prevent instantiation.
     */
    private function __construct()
    {
        // Empty
    }
    
    /**
     * Returns the bbCode parser if available
     * 
     * @return mixed
     *            The bbCode parser or false if not available.
     */
    private final static function getBBParser()
    {
        if (is_null(self::$bbParser))
        {
            if (extension_loaded("bbcode"))
            {
                $code = array(
                    "" => array(
                        "type" => BBCODE_TYPE_ROOT,
                		"childs" => "!li"),
                    "noparse" => array(
                        "type" => BBCODE_TYPE_NOARG,
                		"childs" => ""),
                    "br" => array(
                        "type" => BBCODE_TYPE_SINGLE,
                        "open_tag" => "<br />",
                        "close_tag" => ""),
                	"b" => array(
                        "type" => BBCODE_TYPE_NOARG,
                        "open_tag" => "<b>",
                        "close_tag" => "</b>"),
                    "i" => array(
                        "type" => BBCODE_TYPE_NOARG,
                        "open_tag" => "<i>",
                        "close_tag" => "</i>"),
                    "u" => array(
                        "type" => BBCODE_TYPE_NOARG,
                        "open_tag" => "<u>",
                        "close_tag" => "</u>"),
                    "s" => array(
                        "type" => BBCODE_TYPE_NOARG,
                        "open_tag" => "<s>",
                        "close_tag" => "</s>"),
                    "code" => array(
                        "type" => BBCODE_TYPE_NOARG,
                        "open_tag" => "<code>",
                        "close_tag" => "</code>"),
                	"url" => array(
                        "type" => BBCODE_TYPE_OPTARG,
                        "open_tag" => "<a href=\"{PARAM}\">",
                        "close_tag" => "</a>",
                        "default_arg" => "{CONTENT}"),
                	"img" => array(
                        "type" => BBCODE_TYPE_NOARG,
                        "open_tag" => "<img src=\"",
                        "close_tag" => "\" alt=\"\" />"),
                	"color" => array(
                        "type" => BBCODE_TYPE_ARG,
                        "open_tag" => "<span style=\"color:{PARAM}\">",
                        "close_tag" => "</span>"),
                	"size" => array(
                        "type" => BBCODE_TYPE_ARG,
                        "open_tag" => "<span style=\"font-size:{PARAM}px\">",
                        "close_tag" => "</span>"),
                	"list" => array(
                        "type" => BBCODE_TYPE_NOARG,
                        "open_tag" => "<ul>",
                        "close_tag" => "</ul>",
                        "childs" => "li"),
                	"ul" => array(
                        "type" => BBCODE_TYPE_NOARG,
                        "open_tag" => "<ul>",
                        "close_tag" => "</ul>",
                        "childs" => "li"),
                	"ol" => array(
                        "type" => BBCODE_TYPE_NOARG,
                        "open_tag" => "<ol>",
                        "close_tag" => "</ol>",
                        "childs" => "li"),
                	"li" => array(
                        "type" => BBCODE_TYPE_NOARG,
                        "open_tag" => "<li>",
                        "close_tag" => "</li>")
                );
                self::$bbParser = bbcode_create($code);
            }
            else
            { 
                self::$bbParser = false;
            }
        }        
        return self::$bbParser;
    }

    /**
     * Prints the specified text into the HTML output. The text is
     * property escaped.
     *
     * @param string $text
     *            The text to print.
     * @param mixed $args___
     *            Variable number of optional arguments used by the text.
     */
    public final static function text($text, $args___ = NULL)
    {
        echo htmlspecialchars(call_user_func_array("sprintf", func_get_args()));
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
        echo htmlspecialchars(Request::buildUrl($url));
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
     * Prints a class attribute with the specified class names. If bound to
     * a form field then the "error" class is automatically added when the
     * form field has detected a validation error.
     *
     * @param string $classes
     *           The class names to add (Space-separated). Defaults to empty
     *           string.
     */
    public final static function classes($classes = "")
    {
        if (self::hasBoundField() &&
            self::getForm()->hasErrors(self::getField()))
        {
            if ($classes) $classes .= " ";
            $classes .= "error";
        }
        if ($classes)
        {
            echo " class=\"";
            echo htmlspecialchars($classes);
            echo "\"";
        }
    }

    /**
     * Prints the attributes for a normal form input field (text, password and
     * hidden).
     *
     * @param string $id
     *            Optional ID. Defaults to field name.
     */
    public final static function input($id = NULL)
    {
        $form = self::getForm();
        $name = self::getField();
        $value = $form->readProperty($name, self::$fieldIndex);
        $index = self::$fieldIndex;
        if (!$id)
        {
            $id = $name;
            if (!is_null($index)) $id .= "-$index";
        }
        if (!is_null($index)) $name .= "[$index]";
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
     * Prints the attributes for a form textarea.
     *
     * @param string $id
     *            Optional ID. Defaults to field name.
     */
    public final static function textarea($id = NULL)
    {
        $form = self::getForm();
        $name = self::getField();
        $index = self::$fieldIndex;
        if (!$id)
        {
            $id = $name;
            if (!is_null($index)) $id .= "-$index";
        }
        if (!is_null($index)) $name .= "[$index]";
        $setAutoFocus = !self::$alreadySetAutoFocus && $form->hasErrors($name);
        if ($setAutoFocus) self::$alreadySetAutoFocus = $setAutoFocus;
        printf("id=\"%s\" name=\"%s\"%s",
            htmlspecialchars($id),
            htmlspecialchars($name),
            $setAutoFocus ? " autofocus" : ""
        );
    }
    
    /**
     * Prints the value for a form textarea.
     */
    public final static function textareaValue()
    {
        $form = self::getForm();
        $name = self::getField();
        $value = $form->readProperty($name, self::$fieldIndex);
        $index = self::$fieldIndex;
        echo htmlspecialchars($value);
    }
    
    /**
     * Prints the attributes for a form field label.
     *
     * @param string $id
     *            Optional ID. Defaults to field name.
     */
    public final static function label($id = NULL)
    {
        if (!$id)
        {
            $id = self::getField();
            $index = self::$fieldIndex;
            if (!is_null($index)) $id .= "-$index";
        }
        printf("for=\"%s\"", htmlspecialchars($id));
    }

    /**
     * Prints the attributes for a form checkbox input field.
     *
     * @param string $id
     *            Optional ID. Defaults to field name.
     */
    public final static function checkbox($id = NULL)
    {
        $form = self::getForm();
        $name = self::getField();
        $value = $form->readProperty($name, self::$fieldIndex);
        $index = self::$fieldIndex;
        if (!$id)
        {
            $id = $name;
            if (!is_null($index)) $id .= "-$index";
        }
        if (!is_null($index)) $name .= "[$index]";
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
     * @param string $value
     *            The value of the radio button.
     * @param string $id
     *            Optional ID. Defaults to field name.
     */
    public final static function radio($value, $id = NULL)
    {
        $form = self::getForm();
        $name = self::getField();
        $realValue = $form->readProperty($name, self::$fieldIndex);
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
     * @param string $id
     *            Optional ID. Defaults to field name.
     */
    public final static function select($id = NULL)
    {
        $form = self::getForm();
        $name = self::getField();
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
     * @param $options
     *            A map with the options. Map key is the value used in the
     *            form. Map value is the displayed text.
     */
    public final static function options($options)
    {
        $form = self::getForm();
        $name = self::getField();
        $realValue = $form->readProperty($name, self::$fieldIndex);
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
     *            The form field name. If not specified then the global
     *            errors are displayed.
     */
    public final static function messages()
    {
        if (self::hasBoundField())
        {
            $errors = self::getForm()->getErrors(self::getField());
            $infos = array();
        }
        else
        {
            $errors = Messages::getErrors();
            $infos = Messages::getInfos();
            Messages::removeInfos();
        }
        echo "<ul class=\"messages ";
        if (self::hasBoundField())
            echo self::getField();
        else
            echo "global";
        echo "\">";
        foreach ($infos as $info)
        {
            echo "<li class=\"info\">";
            echo htmlspecialchars($info);
            echo "</li>";
        }
        foreach ($errors as $error)
        {
            echo "<li class=\"error\">";
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
     * @return Form
     *           The bound form.
     */
    public final static function bindForm($form)
    {
        self::$form = $form;
        self::$field = NULL;
        self::$fieldIndex = NULL;
        return $form;
    }

    /**
     * Binds the specified field.
     *
     * @param string $field
     *           The field to bind.
     * @param mixed $index
     *           The optional field index. Can be a number (For array access),
     *           a string (For map access) or NULL if no index is used.
     */
    public final static function bindField($field, $index = NULL)
    {
        self::$field = $field;
        self::$fieldIndex = $index;
    }

    /**
     * Unbinds from the current field.
     */
    public final static function unbindField()
    {
        self::$field = NULL;
        self::$fieldIndex = NULL;
    }

    /**
     * Unbinds from the current form.
     */
    public final static function unbindForm()
    {
        self::$field = NULL;
        self::$fieldIndex = NULL;
        self::$form = NULL;
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
     * Returns the bound field. If no field is bound then an exception is
     * thrown.
     *
     * @return string
     *            The bound field. Never null.
     * @throws LogicException
     *            When no field is bound.
     */
    private static function getField()
    {
        if (!self::$field)
            throw new LogicException("No field bound to HTML");
        return self::$field;
    }

    /**
     * Checks if bound to field.
     *
     * @return boolean
     *            True if bound to field, false if not.
     */
    private static function hasBoundField()
    {
        return !!self::$field;
    }
    
    /**
     * Parses the specified text as bbCode if a bbCode parser is available.
     * If not then the text is returned unmodified.
     * 
     * @param string $text
     *            The text to parse.
     * @return The parsed text or the original text if parser not available.
     */
    private final static function bbParse($text)
    {
        // Get the bbCode parser. Return text as-is if not available.
        $bbParser = self::getBBParser();
        if (!$bbParser) return $text;
        return bbcode_parse($bbParser, $text);
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
        $text = htmlspecialchars(call_user_func_array(
            array("\PhoolKit\I18N", "getMessage"), func_get_args()));
        echo self::bbParse($text);
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
    
    /**
     * Displays the HTML code for intelligent paging.
     * 
     * @param integer $page
     *            The current page number (Starting with 0).
     * @param integer $pages
     *            The number of existing pages.
     * @param string $paramName
     *            Optional page parameter name. If not specified then "page"
     *            is used.
     * @param string $url
     *            Optional URL to use for the paging links. If not specified
     *            then current URL is used.
     */
    public final static function paging($page, $pages, $paramName = "page", 
        $url = null)
    {
        // Do nothing if there is only one page
        if ($pages < 2) return;
        
        if (is_null($url))
        {
            $url = $_SERVER["REQUEST_URI"];
        }
        $url = str_replace("%", "%%", $url);        
        $url = preg_replace("/([?&]$paramName=)[0-9]+/", "\\1%d", $url, 1, $count);
        if (!$count)
        {
            if (strpos($url, "?") === false)
                $url .= "?$paramName=%d";
            else
                $url .= "&$paramName=%d";
        }
        
        if ($page)
        {
            printf('<a class="previous" href="%s">%s</a> ',
                htmlspecialchars(Request::buildUrl(sprintf($url, $page - 1))), 
                htmlspecialchars(I18N::getMessage("phoolkit.paging.previous")));
        }
              
        for ($i = 0; $i < $pages; $i += 1)
        {
            if ($i == $page)
            {
                printf('<span class="current">%d</span> ', $i + 1);
            }
            else
            {
                printf('<a href="%s">%d</a> ', 
                    htmlspecialchars(Request::buildUrl(sprintf($url, $i))), 
                    $i + 1);
            }
            
            // Skip entries between entry 3 and and 3 entries before current
            if ($i == 2 && $i < $page - 5)
            {
                echo "... ";
                $i = $page - 4;
            }
            
            // Skip entries 3 behind current one and 3 before last one
            if ($i > $page + 2 && $i < $pages - 5)
            {
                echo "... ";
                $i = $pages - 4;
            }
        }
            
        if ($page < $pages - 1)
        {
            printf('<a class="next" href="%s">%s</a>', 
                htmlspecialchars(Request::buildUrl(sprintf($url, $page + 1))), 
                htmlspecialchars(I18N::getMessage("phoolkit.paging.next")));
        }
    }
}
