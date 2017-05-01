<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link href="CSS/login.css" rel="stylesheet">
    </head>
    <body>
        <img class="bg" src="images/unikialogin.jpg">
        <div id="hei">
            <form action="" method="post">
                Oppdater passord: <br/>
                Brukernavn : <input type="text" name="lagreBrukernavn"/><br/>
                Passord : <input type="password" name="lagrePassord"/><br/>
                Navn : <input type="text" name="lagreNavn"/><br/>
                <input type="submit" name="lagre" value="Oppdater passord"/><br/>
                <?php
                $db = mysqli_connect("localhost", "root", "", "unikia");
                if (!$db) {
                    trigger_error(mysqli_error($db));
                    die("Kunne ikke knytte til server");
                }
                session_start();
                if (isset($_POST["lagre"])) {
                    $lagreBrukernavn = $_POST["lagreBrukernavn"];
                    $lagrePassord = $_POST["lagrePassord"];
                    $lagreNavn = $_REQUEST["lagreNavn"];

                    $sql = "Update login Set password = Password('$lagrePassord') where username='$lagreBrukernavn'";
                    $res = $db->query($sql);
                    if ($db->affected_rows > 0) {
                        echo "Oppdatering OK";
                    } else {
                        echo "Oppdatering ikke OK";
                    }
                }
                if (isset($_POST["sjekk"])) {
                    $sjekkBrukernavn = $db->escape_string($_POST["sjekkBrukernavn"]);
                    $sjekkPassord = $db->escape_string($_POST["sjekkPassord"]);

                    $sql = "Select * from login where username='$sjekkBrukernavn' AND password=Password('$sjekkPassord')";
                    echo "$sql<br/>";
                    $res = $db->query($sql);
                    if ($db->affected_rows > 0) {

                        $_SESSION["login"] = true;
                        Header("location: index.html");
                    } else {
                        echo "Feil passord";
                        $_SESSION["login"] = false;
                    }
                }
                ?>
                <input type="text" name="sjekkBrukernavn" placeholder="Brukernavn"/><br/>
                <input type="password" name="sjekkPassord" placeholder="Passord"/><br/>
                <input class="btn btn-outline-success my-2 my-sm-0" type="submit" value="login" name="sjekk">
            </form>
        </div>
    </body>
</html>
