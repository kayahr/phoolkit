<?php

namespace AddressBook;

use PhoolKit\RequireValidator;

class ChangeAddressForm extends AddressForm
{
    public $id;

    public function init($id)
    {
        $address = Addresses::instance()->get($id);
        $this->id = $id;
        $this->firstName = $address->getFirstName();
        $this->lastName = $address->getLastName();
        $this->street = $address->getStreet();
        $this->zipCode = $address->getZipCode();
        $this->city = $address->getCity();
        $this->country = $address->getCountry();
        $this->gender = $address->getGender();
        $this->public = $address->getPublic();
    }

    public function getValidators()
    {
        $validators = parent::getValidators();
        $validators[] = new RequireValidator("id");
        return $validators;
    }
}