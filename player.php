<?php require_once 'inc/player_init.php'; ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Players</title>
		

	</head>
	<body>
	
		<fieldset>
			<legend>Players</legend>
			<?=$player_list_html?>
		</fieldset>
		
		<fieldset>
			<legend>Add New Player</legend>
			<?=$player_create_form?>
		</fieldset>
		
		<fieldset>
			<legend>Delete Player</legend>
			<?=$player_delete_form?>
		</fieldset>
		
		<fieldset>
			<legend>Rename Player</legend>
			<?=$player_update_form?>
		</fieldset>
		
		

		
	</body>
</html>