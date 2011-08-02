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
 * Validates a form field with a regular expression mask.
 *
 * @author Klaus Reimer (k@ailis.de)
 */
final class MaskValidator implements Validator
{
    /** The fields to validate. */
    private $fields;

    /** The mask as a regular expression. */
    private $mask;

    /**
     * Constructor
     *
     * @param number $mask
     *            The mask as a regular expression. Must be enclosed with
     *            slashes and can use switches after the expression.
     *            Example: /[a-z]{5}/i
     *
     * @param string... $fields___
     *            The field names to validate.
     */
    public function __construct($mask, $fields___)
    {
        $args = func_get_args();
        $this->mask = array_shift($args);
        $this->fields = $args;
    }

    /**
     * @see Validator::validate()
     */
    public function validate(Form $form)
    {
        foreach ($this->fields as $field)
        {
            if (!preg_match($this->mask, $form->readProperty($field)))
                $form->addError($field, I18N::getMessage(
                    "phoolkit.validation.mask", $this->mask));
        }
    }

    /**
     * @see phable.Validator::getScript()
     */
    public function getScript()
    {
        $mask = $this->mask;
        $message = StringUtils::escapeJS(I18N::getMessage(
            "phoolkit.validation.mask", $mask));
        $script = "var m = '$message';\n";
        $script .= "var v = $mask;\n";
        foreach ($this->fields as $field)
        {
            $property = StringUtils::escapeJS($field);
            $script .= "if (!this.get('$property').match(v)) " .
                "this.error('$property', m);\n";
        }
        return $script;
    }
}
