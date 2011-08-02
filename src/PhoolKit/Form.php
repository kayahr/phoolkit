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

use ReflectionClass;
use InvalidArgumentException;

/**
 * Base class for forms.
 *
 * @author Klaus Reimer (k@ailis.de)
 */
abstract class Form
{
    /** The form storage. */
    private static $forms = array();

    /** Validation errors. */
    private $errors = array();

    /**
     * Private constructor to prevent instantiation from outside.
     */
    private function __construct()
    {
        // Nothing to do here.
    }

    /**
     * Parses the form. Use this method to retrieve a form inside of an
     * action class where you want to process the submitted form. This
     * method parses all request parameters into the defined form properties
     * and also validates them.
     *
     * If validation fails then the specified input page is displayed.
     * This should be the page from which the form was submitted so the form
     * is redisplayed together with the error messages.
     *
     * If no input page is specified then the caller must check for errors
     * itself by calling the hasErrors() method for example.
     *
     * @param string $inputPage
     *             Optional input page.
     * @return Form
     *             The parsed form.
     */
    public final static function parse($inputPage = NULL)
    {
        $form = new static();
        foreach (Request::getParams() as $param => $value)
        {
            try
            {
                $form->writeProperty($param, $value);
            }
            catch (InvalidArgumentException $e)
            {
                // Ignored because this simply means that the request param
                // is not used by this action.
            }
        }
        self::$forms[get_called_class()] = $form;

        // Validate the form.
        $form->validate();

        // When validation failed and input page has been specified then
        // forward to it.
        if ($inputPage && $form->hasErrors())
        {
            $func = function($file)
            {
                include $file;
            };
            $func($inputPage);
            exit();
        }

        // Return the form.
        return $form;
    }

    /**
     * Returns the action class for the specified action name. If this
     * action is already in the storage because it was referenced before in the
     * current request then this cached action is returned. Otherwise a new
     * action is instantiated, the init() method is called and the action is
     * cached in the storage.
     *
     * You can pass a variable number of arguments to this method. They will
     * be passed to the init() method of the form if a new form is created.
     *
     * @return Action
     *            The action.
     */
    public final static function get()
    {
        $className = get_called_class();

        // Return action from storage if already cached there.
        if (array_key_exists($className, self::$forms))
        return self::$forms[$className];

        // Create and return a new form.
        $form = new $className;
        call_user_func_array(array($form, "init"), func_get_args());
        self::$forms[$className] = $form;
        return $form;
    }

    /**
     * Validates the form.
     */
    private function validate()
    {
        foreach ($this->getValidators() as $validator)
            $validator->validate($this);
    }

    /**
     * Writes a value to a property. It first tries to set a public property.
     * If this fails then it tries to invoke a public setter. If this fails
     * too then an exception is thrown.
     *
     * @param string $name
     *            The property name.
     * @param mixed $value
     *            The value to set.
     * @throws IllegalArgumentException
     *            When property could not be written.
     */
    public final function writeProperty($name, $value)
    {
        $class = new ReflectionClass($this);

        // If a public property exists then use this.
        if ($class->hasProperty($name))
        {
            $property = $class->getProperty($name);
            if ($property->isPublic())
            {
                $property->setValue($this, $value);
                return;
            }
        }

        // If a public setter exists then use this.
        $methodName = "set" . ucfirst($name);
        if ($class->hasMethod($methodName))
        {
            $method = $class->getMethod($methodName);
            if ($method->isPublic())
            {
                $method->invoke($this, $value);
                return;
            }
        }

        // Can't access property.
        throw new InvalidArgumentException("No property '$name' found in '" .
            get_called_class() . "'");
    }

    /**
     * Returns a property from the object. If the property is public then
     * it is accessed directly. Otherwise a getter method is searched and
     * used.
     *
     * @param string $name
     *            The property name.
     * @return mixed
     *            The property value.
     * @throws InvalidArgumentException
     *            When specified name does not reference an existing
     *            property.
     */
    public final function readProperty($name)
    {
        $class = new ReflectionClass($this);

        // If a public property exists then use this.
        if ($class->hasProperty($name))
        {
            $property = $class->getProperty($name);
            if ($property->isPublic())
                return $property->getValue($this);
        }

        // If a public getter exists then use this.
        $methodName = "get" . ucfirst($name);
        if ($class->hasMethod($methodName))
        {
            $method = $class->getMethod($methodName);
            if ($method->isPublic())
                return $method->invoke($this);
        }

        // Can't access property.
        throw new InvalidArgumentException("No property '$name' found in '" .
            get_called_class() . "'");
    }

    /**
     * Adds an error message for a field.
     *
     * @param string $name
     *            The field name.
     * @param string $message
     *            The error message to add.
     */
    public final function addError($name, $message)
    {
        if (!array_key_exists($name, $this->errors))
            $this->errors[$name] = array();
        $this->errors[$name][] = $message;
    }

    /**
     * Returns the error messages for a field.
     *
     * @param string $name
     *            The field name.
     * @return array
     *            The error messages. Never null. Maybe empty.
     */
    public final function getErrors($name)
    {
        if (!array_key_exists($name, $this->errors))
            return array();
        return $this->errors[$name];
    }

    /**
     * Checks if errors for the specified field are available. If no field
     * is specified then this method checks if at least one field of the
     * form has errors.
     *
     * @param string $name
     *            The field name. Optional. If not specified then all fields
     *            are checked.
     * @return boolean
     *            True if errors are available, false if not.
     */
    public final function hasErrors($name = NULL)
    {
        if ($name) return array_key_exists($name, $this->errors);
        return !!$this->errors;
    }

    /**
     * Initializes the form.
     */
    public function init()
    {
        // Default implementation does nothing.
    }

    /**
     * Returns the list of validators to use for this form.
     *
     * @return array
     *            The list of validators to use. Must not be null.
     */
    public function getValidators()
    {
        return array();
    }
}
