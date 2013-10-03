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
 * Validates required fields.
 *
 * @author Klaus Reimer (k@ailis.de)
 */
final class RequireValidator implements Validator
{
    /** The fields to validate. */
    private $fields;

    /**
     * Constructs a new require validator.
     *
     * @param string... $fields___
     *            The field names to validate.
     */
    public function __construct($fields___)
    {
        $this->fields = func_get_args();
    }

    /**
     * @see Validator::validate()
     */
    public function validate(Form $form)
    {
        foreach ($this->fields as $field)
        {
            $value = $form->readProperty($field);
            if (is_string($value) && strlen($value)) continue;
            if (!$form->readProperty($field)) $form->addError($field, 
                I18N::getMessage("phoolkit.validation.required"));
        }
    }

    /**
     * @see phable.Validator::getScript()
     */
    public function getScript()
    {
        $message = StringUtils::escapeJS(I18N::getMessage(
            "phoolkit.validation.required"));
        $script = "var m = '$message';\n";
        foreach ($this->fields as $field)
        {
            $property = StringUtils::escapeJS($field);
            $script .= "if (!this.get('$property')) this.error('$property', m);\n";
        }
        return $script;
    }
}
