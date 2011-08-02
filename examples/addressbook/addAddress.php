<? require_once "common.php" ?>
<? use PhoolKit\HTML as h ?>
<? use AddressBook\AddressForm ?>
<? include "header.php" ?>

<h2>Add Address</h2>

<?h::bindForm(AddressForm::get())?>
<form action="<?h::url("actions/addAddress.php")?>" method="post" novalidate <?h::form()?>>

  <? include "addressForm.php" ?>

  <div class="buttons">
    <input type="submit" value="Add address" />
  <a href="<?h::url("index.php")?>">Cancel</a>
  </div>

</form>

<? include "footer.php" ?>