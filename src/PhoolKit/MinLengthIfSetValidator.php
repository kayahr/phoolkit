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
 * Checks for minimum length of submitted data when data was submitted at all.
 * This validator is useful for checking a minimum length of a password for
 * example in forms where the password field can be left empty when no
 * password change should be performed.
 *
 * @author Klaus Reimer (k@ailis.de)
 */
final class MinLengthIfSetValidator implements Validator
{
    /** The fields to validate. */
    private $fields;

    /** The minimum length to check for. */
    private $minLength;

    /**
     * Constructor
     *
     * @param number $minLength
     *            The minimum length to check for.
     * @param string... $fields___
     *            The field names to validate.
     */
    public function __construct($minLength, $fields___)
    {
        $args = func_get_args();
        $this->minLength = array_shift($args);
        $this->fields = $args;
    }

    /**
     * @see Validator::validate()
     */
    public function validate(Form $form)
    {
        foreach ($this->fields as $field)
        {
            $value = $form->readProperty($field);
            if (strlen($value) > 0 && strlen($value) < $this->minLength)
                $form->addError($field, I18N::getMessage(
                    "phoolkit.validation.minLength", $this->minLength));
        }
    }

    /**
     * @see phable.Validator::getScript()
     */
    public function getScript()
    {
        $minLength = $this->minLength;
        $message = StringUtils::escapeJS(I18N::getMessage(
            "phoolkit.validation.minLength", $minLength));
        $script = "var m = '$message';\n";
        foreach ($this->fields as $field)
        {
            $property = StringUtils::escapeJS($field);
            $script .= "if (this.get('$property').length > 0 && this.get('$property').length < $minLength) " .
                "this.error('$property', m);\n";
        }
        return $script;
    }
}
