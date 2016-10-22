<?php
require_once("config.php");
if (!isset($_SESSION["user_id"]) || $_SESSION["user_id"] == "") {
    // not logged in send to login page
    redirect("index.php");
}

if ($status === FALSE) {
die("You dont have the permission to access this page");
}

// set page title
$title = "Table";

?>
<?php
include 'php-snippet/module/htmlhead.php';
?>

<body>
<?php
include 'php-snippet/module/header.php';
include 'php-snippet/module/main-navigation.php';
include 'php-snippet/module/xs-navigation.php';
?>
<main data-gurk="main" class="table">



<!-- Content results -->

		<section class="layout top">

			<h1>Tables</h1>




<?php
			include 'php-snippet/organism/table.php';
?>

		</section>


<!-- /Content results -->

</main>
<?php
include 'php-snippet/module/footer.php';
?>

<script src="js/global.js"></script>
<script src="js/frontend.js"></script>
<script src="js/app.js"></script>


</body>

</html>
