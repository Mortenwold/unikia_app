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
    </head>
    <body>
        <?php
        session_start();
        if (!$_SESSION["login"]) {
            Header("location: login.php");
        }
        ?>
        <form action="" method="post">
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
            if (isset($_POST["lage"])) {
                $lageBrukernavn = $db->escape_string($_POST["lageBrukernavn"]);
                $lagePassord = $db->escape_string($_POST["lagePassord"]);

                $sql1 = "INSERT INTO login (username, password)
                                VALUES ('$lageBrukernavn', 'Password($lagePassord)') ";
                $sql2 = "Update login Set password = Password('$lagePassord') where username='$lageBrukernavn'";
                echo "$sql1<br/>";
                $res1 = $db->query($sql1);
                $res2 = $db->query($sql2);
                if ($db->affected_rows > 0) {
                    echo "Oppretting OK";
                } else {
                    echo "Oppretting ikke OK";
                }
            }
            ?>
            Oppdater passord: <br/>
            Brukernavn : <input type="text" name="lagreBrukernavn"/><br/>
            Passord : <input type="password" name="lagrePassord"/><br/>
            <input type="submit" name="lagre" value="Oppdater passord"/><br/>
            lage bruker: <br/>
            <input type="text" name="lageBrukernavn" placeholder="Brukernavn"/><br/>
            <input type="password" name="lagePassord" placeholder="Passord"/><br/>
            <input class="btn btn-outline-success my-2 my-sm-0" type="submit" value="Opprett" name="lage">
        </form>
    </body>
</html>
