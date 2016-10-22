<?php
$verbindung = mysql_connect("localhost", "web128013" , "wednesday")
or die("No conection to database");

mysql_select_db("usr_web128013_2") or die ("Database cant be found");

$username = $_POST["u_username"];
$password = $_POST["u_password"];
$password2 = $_POST["password2"];
$rolecode = $_POST["u_rolecode"];

if($password != $password2 OR $username == "" OR $password == "")
    {
    echo "Error. Please do it better! <a href=\"../edit.php\">back</a>";
    exit;
    }
$password = md5($password);

$result = mysql_query("
SELECT * FROM system_users
WHERE u_username
LIKE '".mysql_real_escape_string ($username)."'
");






if($result === FALSE) {
  echo "Mist";
    die(mysql_error()); // TODO: better error handling
}



$menge = mysql_num_rows($result);

if($menge == 0)
    {
    $eintrag = "INSERT INTO system_users (u_username, u_password, u_rolecode) VALUES ('$username', '$password', '$rolecode')";
    $eintragen = mysql_query($eintrag);

    if($eintragen == true)
        {
        echo '

        Username <b>$username</b> is created. <a href="../index.php">Login</a>
        <script language="JavaScript" type="text/javascript">
        setTimeout("location.href=\'../edit.php\'", 50);
        </script>

        ';
        }
    else
        {
        echo "Error. Something wrong with username. <a href=\"../edit.php\">Back</a>";
        }


    }

else
    {
    echo "Username already exists. Try another one. <a href=\"../edit.php\">Back</a>";
    }
?>
