<?php

require_once "../common.php";

use PhoolKit\Request;
use AddressBook\ChangeAddressForm;
use AddressBook\Address;
use AddressBook\Addresses;

$form = ChangeAddressForm::parse("../address.php");

$address = new Address(
    $form->firstName,
    $form->lastName,
    $form->street,
    $form->zipCode,
    $form->city,
    $form->country,
    $form->gender,
    $form->public);
Addresses::instance()->set($form->id, $address);

Request::redirect("../index.php");
