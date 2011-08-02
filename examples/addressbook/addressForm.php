<? use PhoolKit\HTML as h ?>
<div class="fields">
  <label for="firstName">First name</label>
  <input type="text" <?h::input("firstName")?><?h::autoFocus()?> />
  <?h::errors("firstName")?>

  <label for="lastName">Last name</label>
  <input type="text" <?h::input("lastName")?> />
  <?h::errors("lastName")?>

  <label for="street">Street</label>
  <input type="text" <?h::input("street")?> />
  <?h::errors("street")?>

  <label for="zipCode">ZIP code</label>
  <input type="text" <?h::input("zipCode")?> />
  <?h::errors("zipCode")?>

  <label for="city">City</label>
  <input type="text" <?h::input("city")?> />
  <?h::errors("city")?>

  <label for="country">Country</label>
  <select <?h::select("country")?>>
    <?h::options("country", array(
      "germany" => "Germany",
      "uk" => "United Kingdom"
    ))?>
  </select>
  <?h::errors("country")?>

  <div class="radio">
    <input <?h::radio("gender", "male")?> />
    <label for="gender-male">Male</label>
    <input <?h::radio("gender", "female")?> />
    <label for="gender-female">Female</label>
  </div>
  <?h::errors("gender")?>

  <div class="checkbox">
    <input <?h::checkbox("public")?> />
    <label for="public">Public</label>
  </div>
  <?h::errors("public")?>

</div>
