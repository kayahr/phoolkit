<?php

require_once "../common.php";

use PhoolKit\Request;
use AddressBook\AddressForm;
use AddressBook\Address;
use AddressBook\Addresses;

$form = AddressForm::parse("../addAddress.php");

$address = new Address(
    $form->firstName,
    $form->lastName,
    $form->street,
    $form->zipCode,
    $form->city,
    $form->country,
    $form->gender,
    $form->public);
Addresses::instance()->add($address);

Request::redirect("../index.php");
