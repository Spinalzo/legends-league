<?php
require_once("config.php");
if (!isset($_SESSION["user_id"]) || $_SESSION["user_id"] == "") {
    // not logged in send to login page
    redirect("logout.php");
}

// set page title
$title = "Index";

// if the rights are not set then add them in the current session
if (!isset($_SESSION["access"])) {

    try {

        $sql = "SELECT mod_modulegroupcode, mod_modulegroupname FROM module "
                . " WHERE 1 GROUP BY `mod_modulegroupcode` "
                . " ORDER BY `mod_modulegrouporder` ASC, `mod_moduleorder` ASC  ";


        $stmt = $DB->prepare($sql);
        $stmt->execute();
        $commonModules = $stmt->fetchAll();

        $sql = "SELECT mod_modulegroupcode, mod_modulegroupname, mod_modulepagename,  mod_modulecode, mod_modulename FROM module "
                . " WHERE 1 "
                . " ORDER BY `mod_modulegrouporder` ASC, `mod_moduleorder` ASC  ";

        $stmt = $DB->prepare($sql);
        $stmt->execute();
        $allModules = $stmt->fetchAll();

        $sql = "SELECT rr_modulecode, rr_create,  rr_edit, rr_delete, rr_view FROM role_rights "
                . " WHERE  rr_rolecode = :rc "
                . " ORDER BY `rr_modulecode` ASC  ";

        $stmt = $DB->prepare($sql);
        $stmt->bindValue(":rc", $_SESSION["rolecode"]);


        $stmt->execute();
        $userRights = $stmt->fetchAll();

        $_SESSION["access"] = set_rights($allModules, $userRights, $commonModules);

    } catch (Exception $ex) {

        echo $ex->getMessage();
    }
}
include 'php-snippet/module/htmlhead.php';
?>

<body>
<?php
include 'php-snippet/module/header.php';
include 'php-snippet/module/main-navigation.php';
include 'php-snippet/module/xs-navigation.php';
?>





<main data-gurk="main">


<!-- Content home -->

		<section class="layout top">

			<h1>Dashboard</h1>

			<ul class="dashboard-board">
					<?php foreach ($_SESSION["access"] as $key => $access) { ?>
							<li>
									<?php echo $access["top_menu_name"]; ?>
									<?php
									echo '<ul>';
									foreach ($access as $k => $val) {
											if ($k != "top_menu_name") {
													echo '<li><a href="' . ($val["page_name"]) . '">' . $val["menu_name"] . '</a></li>';
													?>
													<?php
											}
									}
									echo '</ul>';
									?>
							</li>
							<?php
					}
					?>

			</ul>

		</section>


<!-- /Content home -->



</main>
<?php
include 'php-snippet/module/footer.php';
?>


<script src="js/global.js"></script>
<script src="js/frontend.js"></script>
<script src="js/app.js"></script>

</body>

</html>
