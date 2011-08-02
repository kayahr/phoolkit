<?php

namespace AddressBook;

use PhoolKit\Form;
use PhoolKit\RequireValidator;
use PhoolKit\MinLengthValidator;
use PhoolKit\MaxLengthValidator;
use PhoolKit\MaskValidator;

class AddressForm extends Form
{
    public $firstName;

    public $lastName;

    public $street;

    public $zipCode;

    public $city;

    public $country;

    public $gender;

    public $public;

    public function init()
    {
        $this->gender = "male";
    }

    public function getValidators()
    {
        return array(
            new RequireValidator("firstName", "lastName", "street", "zipCode",
                "city", "country"),
            new MinLengthValidator(2, "firstName", "lastName"),
            new MaxLengthValidator(15, "firstName", "lastName"),
            new MaskValidator("/^[0-9]{5}\$/", "zipCode")
        );
    }
}