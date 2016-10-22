<?php
require_once("config.php");
include 'php-snippet/module/htmlhead.php';
?>
<body>
<?php
include 'php-snippet/module/header.php';
include 'php-snippet/module/main-navigation.php';
include 'php-snippet/module/xs-navigation.php';
?>
<main data-gurk="main" class="result">

	<section class="layout top">

		<h1>Results</h1>
		<form action="reg/register.php" method="post">
		<div class="form-row">
		Dein Username:
		</div>
		<div class="form-row">
				<input type="text" size="24" maxlength="50" name="u_username">
		</div>

		<div class="form-row">
		Dein Passwort:
		</div>

		<div class="form-row">
				<input type="password" size="24" maxlength="50" name="u_password">
		</div>

		<div class="form-row">
		Passwort wiederholen:
		</div>

		<div class="form-row">
				<input type="password" size="24" maxlength="50" name="password2">
		</div>



		<div class="form-row">
			<input id="inputrole" type="hidden" size="24" maxlength="50" name="u_rolecode" value="ADMIN">
			<select id="role">
				<option value="ADMIN">Admin</option>
				<option value="SUPERADMIN">Super Admin</option>
			</select>
		</div>


		<div class="form-row"><input type="submit" value="Abschicken" class="button pull-left"></div>

		</form>


	</section>
</main>

<script src="js/global.js"></script>
<script src="js/frontend.js"></script>
<script src="js/app.js"></script>

<script>
$('#role').on('change', function() {
		var thisValue = $( this ).val();
		$(  '#inputrole' ).val(thisValue);
});
</script>

</body>

</html>
