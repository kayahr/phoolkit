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
 * Validator interface.
 *
 * @author Klaus Reimer (k@ailis.de)
 */
interface Validator
{
    /**
     * Performs server-side validation on the specified form.
     *
     * @param Form $action
     *            The form to validate.
     */
    function validate(Form $form);

    /**
     * Returns the JavaScript code used to perform this validation on
     * the client-side. Returns NULL if no client-side validation code is
     * used for this validation.
     *
     * The JavaScript code can use the "this" qualifier to access the form.
     * Use 'this.get("firstName")' to access the value of the input field 
     * "firstName". Use 'this.error("firstName", "First name is required") 
     * to report an error for the field "firstName").
     *
     * @return string
     *            The validation expression or NULL if no client-side
     *            validation is used.
     */
    function getScript();
}
