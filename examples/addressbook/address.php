<? require_once "common.php" ?>
<? use PhoolKit\HTML as h ?>
<? use AddressBook\ChangeAddressForm ?>
<? include "header.php" ?>

<h2>Address</h2>

<?h::bindForm(ChangeAddressForm::get(h::param("id")))?>
<form action="<?h::url("actions/changeAddress.php")?>" method="post" novalidate <?h::form()?>>
  <input type="hidden" <?h::input("id")?> />

  <? include "addressForm.php" ?>

  <div class="buttons">
    <input type="submit" value="Save changes" />
    <a href="<?h::url("index.php")?>">Cancel</a>
    <a href="<?h::url("actions/deleteAddress.php?id=" . h::param("id"))?>">Delete address</a>
  </div>

</form>

<? include "footer.php" ?>