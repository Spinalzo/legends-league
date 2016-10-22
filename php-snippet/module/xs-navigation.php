	<menu class="navigation-xs">
		<span class="menu-trigger"></span>
		<?php
		if (!isset($_SESSION["access"])){
				echo '<a class="login-button" href="index.php" title="Login"></a>';
			}
		?>
	<?php
		if (isset($_SESSION["access"])){
			echo '<a class="logout-button" href="logout.php" title="Logout"></a>';
		}
	?>
	</menu>
