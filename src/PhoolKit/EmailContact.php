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
 * Interface for a mail contact. If you need to create a simple instance of
 * this interface then use the SimpleEmailContact class.
 *
 * @author Klaus Reimer (k@ailis.de)
 */
interface EmailContact
{
    /**
     * Returns the full real name of the contact or NULL if this is not
     * available.
     * 
     * @return string
     *             The full real name of the contact or NULL if not available.
     */
    function getName();
    
    /**
     * Returns the email address of the contact.
     * 
     * @return string
     *             The email address of the contact. Never null.
     */    
    function getEmail();
}
