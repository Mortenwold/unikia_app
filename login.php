<!DOCTYPE html>
<?php
error_reporting(0);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Unikia</title>
        <link href="CSS/login.css" rel="stylesheet">
        <link rel="stylesheet" href="CSS/font-awesome.min.css">
        <link href='http://fonts.googleapis.com/css?family=Varela+Round' rel='stylesheet' type='text/css'>
        <script src="javascript/validate.min.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <link rel="icon" href="images/unikiaicon.ico">  
    </head>
    <body>
        <script>
            function lgName()
            {
                var regEx = /^[a-zÃ¦Ã¸Ã¥A-ZÃ†Ã˜Ã… ]{2,20}$/;
                ok = regEx.test(document.login.lg_username.value);
                if (!ok)
                {
                    document.getElementById("blackFont").innerHTML = "Username is invalid";
                    return false;
                } else
                {
                    document.getElementById("blackFont").innerHTML = "";
                    return true;
                }
            }
        </script>
        <div id="login">
            <img id="icon" src="images/unikia-logo.png" alt="The logo with the text of Unikia">
            <div id="blackFont"></div>
            <form action="login.php" method="post" name="login">
                <?php
                session_start();
                $_SESSION["login"] = false;
                $_SESSION["admin"] = false;

                include 'db_connect.php';
                if (!$db) {
                    trigger_error(mysqli_error($db));
                    die("Kunne ikke knytte til server");
                }
                if (isset($_POST["sjekk"])) {
                    $navn = $_POST["lg_username"];
                    if (!preg_match("/^[a-zæøåA-ZÆØÅ ]{2,20}$/", $navn)) {
                        echo "<div id='blackFont'>Username is invalid!</div>";
                    } else {
                        $sjekkBrukernavn = $db->escape_string($_POST["lg_username"]);
                        $sjekkPassord = $db->escape_string($_POST["lg_password"]);

                        $sql = "Select * from login where username='$sjekkBrukernavn' "
                                . "AND password=Password('$sjekkPassord')";
                        $res = $db->query($sql);
                        if ($db->affected_rows > 0) {
                            $_SESSION["login"] = true;
                            if (strtolower($sjekkBrukernavn) == "admin") {
                                $_SESSION["admin"] = true;
                            }
                            Header("location: index.php");
                        } else {
                            echo "<div id='blackFont'>Incorrect username or password!</div>";
                            $_SESSION["login"] = false;
                        }
                    }
                }
                ?>
                <div class="text-center" style="padding:50px 0">
                    <!-- Main Form -->
                    <div class="login-form-1">
                        <div class="login-form-main-message"></div>
                        <div class="main-login-form">
                            <div class="login-group">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="lg_username" name="lg_username" placeholder="username" onchange ="lgName()">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" id="lg_password" name="lg_password" placeholder="password">
                                </div>
                            </div>
                            <button name="sjekk" type="submit" class="login-button"><i class="fa fa-chevron-right">></i></button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
        
    </body>
</html>
