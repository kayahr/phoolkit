<?php

namespace AddressBook;

use LogicException;

final class Address
{
    private $id = NULL;

    private $firstName;

    private $lastName;

    private $street;

    private $zipCode;

    private $city;

    private $country;

    private $gender;

    private $public;

    public function __construct($firstName, $lastName, $street, $zipCode,
        $city, $country, $gender, $public)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->street = $street;
        $this->zipCode = $zipCode;
        $this->city = $city;
        $this->country = $country;
        $this->gender = $gender;
        $this->public = $public;
    }

    public function setId($id)
    {
        if ($this->id !== NULL)
            throw new LogicException("Changing ID is not allowed.");
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getStreet()
    {
        return $this->street;
    }

    public function getZipCode()
    {
        return $this->zipCode;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function getPublic()
    {
        return $this->public;
    }
}