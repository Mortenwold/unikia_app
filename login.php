<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
mobilvennlig side/admin side

-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link href="CSS/login.css" rel="stylesheet">
        <link rel="stylesheet" href="CSS/font-awesome.min.css">
        <link href='http://fonts.googleapis.com/css?family=Varela+Round' rel='stylesheet' type='text/css'>
        <script src="javascript/validate.min.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    </head>
    <body>
        <img class="bg" src="images/unikialogin.jpg">
        <div id="login">
            <img id="icon" src="images/unikia-logo2.png">
            <form action="" method="post">
                <?php
                session_start();
                $_SESSION["login"] = false;
                $_SESSION["admin"] = false;
                $db = mysqli_connect("localhost", "root", "", "unikia");
                if (!$db) {
                    trigger_error(mysqli_error($db));
                    die("Kunne ikke knytte til server");
                }
                if (isset($_POST["sjekk"])) {
                    $sjekkBrukernavn = $db->escape_string($_POST["lg_username"]);
                    $sjekkPassord = $db->escape_string($_POST["lg_password"]);

                    $sql = "Select * from login where username='$sjekkBrukernavn' AND password=Password('$sjekkPassord')";
                    $res = $db->query($sql);
                    if ($db->affected_rows > 0) {

                        $_SESSION["login"] = true;
                        if ($sjekkBrukernavn == "admin") {
                            $_SESSION["admin"] = true;
                        } 
                        Header("location: index.php");
                    } else {
                        echo "Incorrect password!";
                        $_SESSION["login"] = false;
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
                                    <input type="text" class="form-control" id="lg_username" name="lg_username" placeholder="username">
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
