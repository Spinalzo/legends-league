	<nav class="main">

		<ul>
			<li><a href="dashboard.php" title="Home">Home</a></li>



			<?php
			if(!isset($_SESSION["user_id"])){
					echo '
					<li><a href="results-free.php" title="Results">Results</a></li>
					<li><a href="tables-free.php" title="Tables">Tables</a></li>
					';
			 }

			 if(isset($_SESSION["user_id"])){
					 echo '<li><a href="results.php" title="Results">Results</a></li>';
					 echo '<li><a href="tables.php" title="Results">Tables</a></li>';
					 echo '<li><a href="edit.php">Edit-Center</a></li>';
					 echo '<li><a href="logout.php">Logout</a></li>';
				}

		?>
		</ul>

	</nav>
