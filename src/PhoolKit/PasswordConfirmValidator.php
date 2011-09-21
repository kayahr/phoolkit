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
 * Checks a password field against its confirmation field.
 *
 * @author Klaus Reimer (k@ailis.de)
 */
final class PasswordConfirmValidator implements Validator
{
    /** The field to validate. */
    private $passwordField;
    
    /** The field to compare to. */
    private $passwordConfirmationField;

    /**
     * Constructor
     *
     * @param string $passwordField
     *            The field to validate.
     * @param string $passwordConfirmationField
     *            The field to compare to.
     */
    public function __construct($passwordField, $passwordConfirmationField)
    {
        $this->passwordField = $passwordField;
        $this->passwordConfirmationField = $passwordConfirmationField;
    }

    /**
     * @see Validator::validate()
     */
    public function validate(Form $form)
    {
        if ($form->readProperty($this->passwordField) !=
            $form->readProperty($this->passwordConfirmationField))
        {
            $form->addError($field, I18N::getMessage(
                "phoolkit.validation.passwordConfirm"));
        }
    }

    /**
     * @see phable.Validator::getScript()
     */
    public function getScript()
    {
        $message = StringUtils::escapeJS(I18N::getMessage(
            "phoolkit.validation.passwordConfirm"));
        $script = "var m = '$message';\n";
        $property1 = StringUtils::escapeJS($this->passwordField);
        $property2 = StringUtils::escapeJS($this->passwordConfirmationField);
        $script .= "if (this.get('$property1') != this.get('$property2')) " .
            "this.error('$property1', m);\n";
        return $script;
    }
}
