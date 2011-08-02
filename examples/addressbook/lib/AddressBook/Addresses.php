<?php

namespace AddressBook;

use InvalidArgumentException;

final class Addresses
{
    private $addresses;

    private $nextId;

    private function __construct()
    {
         if (!isset($_SESSION["addresses"]))
             $_SESSION["addresses"] = array();
         $this->addresses = &$_SESSION["addresses"];
    }

    public static function instance()
    {
        static $instance;

        if (!$instance) $instance = new Addresses();
        return $instance;
    }

    private function getNextId()
    {
        if ($this->nextId == NULL)
        {
            $this->nextId = 1;
            foreach ($this->addresses as $id => $address)
                $this->nextId = max($this->nextId, $id);
        }
        return ++$this->nextId;
    }

    public function getAll()
    {
        return $this->addresses;
    }

    public function get($id)
    {
        if (!array_key_exists($id, $this->addresses))
            throw new InvalidArgumentException("No address with ID $id found");
        return $this->addresses[$id];
    }

    public function delete($id)
    {
        if (!array_key_exists($id, $this->addresses))
            throw new InvalidArgumentException("No address with ID $id found");
        unset($this->addresses[$id]);
    }

    public function add($address)
    {
        $id = $this->getNextId();
        $address->setId($id);
        $this->addresses[$id] = $address;
    }

    public function set($id, $address)
    {
        $address->setId($id);
        $this->addresses[$id] = $address;
    }
}
