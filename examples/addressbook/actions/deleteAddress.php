<?php

require_once "../common.php";

use PhoolKit\Request;
use AddressBook\Addresses;

Addresses::instance()->delete(Request::getParam("id"));

Request::redirect("../index.php");
