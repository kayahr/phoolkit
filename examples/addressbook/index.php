<? require_once "common.php" ?>
<? use PhoolKit\HTML as h ?>
<? use AddressBook\Addresses ?>
<? include "header.php" ?>

<h2>Address List</h2>
<ul class="addresses">
  <?foreach (Addresses::instance()->getAll() as $address):?>
    <li>
      <a href="<?h::url("address.php?id=".$address->getId())?>">
        <?h::text($address->getLastName())?>,
        <?h::text($address->getFirstName())?>
      </a>
    </li>
  <?endforeach?>
</ul>
<div class="buttons">
  <a href="<?h::url("addAddress.php")?>">Add address</a>
</div>

<? include "footer.php" ?>