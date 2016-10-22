<?php
require_once("config.php");

if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] != "") {
    // if logged in send to dashboard page
    redirect("dashboard.php");
}

$title = "Login";
$mode = $_REQUEST["mode"];
if ($mode == "login") {
    $username = trim($_POST['username']);
    $pass = trim($_POST['user_password']);
    $pass = md5($_POST['user_password']);


    if ($username == "" || $pass == "") {

        $_SESSION["errorType"] = "danger";
        $_SESSION["errorMsg"] = "Enter manadatory fields";
    } else {
        $sql = "SELECT * FROM system_users WHERE u_username = :uname AND u_password = :upass ";

        try {
            $stmt = $DB->prepare($sql);

            // bind the values
            $stmt->bindValue(":uname", $username);
            $stmt->bindValue(":upass", $pass);

            // execute Query
            $stmt->execute();
            $results = $stmt->fetchAll();

            if (count($results) > 0) {
                $_SESSION["errorType"] = "success";
                $_SESSION["errorMsg"] = "You have successfully logged in.";

                $_SESSION["user_id"] = $results[0]["u_userid"];
                $_SESSION["rolecode"] = $results[0]["u_rolecode"];
                $_SESSION["username"] = $results[0]["u_username"];

                redirect("dashboard.php");
                exit;
            } else {
                $_SESSION["errorType"] = "info";
                $_SESSION["errorMsg"] = "username or password does not exist.";
            }
        } catch (Exception $ex) {

            $_SESSION["errorType"] = "danger";
            $_SESSION["errorMsg"] = $ex->getMessage();
        }
    }
    redirect("index.php");
}

include 'php-snippet/module/htmlhead.php';
?>

<body>
<?php
include 'php-snippet/module/header.php';
include 'php-snippet/module/main-navigation.php';
include 'php-snippet/module/xs-navigation.php';
?>

<?php if ($ERROR_MSG <> "") { ?>
    <div class="col-lg-12">
        <div class="alert alert-dismissable alert-<?php echo $ERROR_TYPE ?>">
            <button data-dismiss="alert" class="close" type="button">x</button>
            <p><?php echo $ERROR_MSG; ?></p>
        </div>
        <div style="height: 10px;">&nbsp;</div>
    </div>
<?php } ?>


<main data-gurk="main">


<!-- Content home -->

		<section class="layout top">

			<h1>Legends never die</h1>

      <?php
    		include 'loginUser.php';
    	?>


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
