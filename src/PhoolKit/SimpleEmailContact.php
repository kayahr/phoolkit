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
 * A simple default implementation of the EmailContact interface.
 *
 * @author Klaus Reimer (k@ailis.de)
 */
class SimpleEmailContact implements EmailContact
{
    /** The full real name of the contact. */
    private $name;

    /** The email address of the contact. */
    private $email;
    
    /**
     * Creates a new email contact.
     * 
     * @param string $email
     *            The email address of the contact. Must not be null.
     * @param string $name
     *            Optional full real name of the contact. Default is NULL
     *            which means that no real name is available.
     */
    public function __construct($email, $name = NULL)
    {
        $this->email = $email;
        $this->name = $name;
    }
    
    /**
     * @see EmailContact::getName()
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @see EmailContact::getEmail()
     */    
    public function getEmail()
    {
        return $this->email;
    }
}
