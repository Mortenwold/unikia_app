<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="unikiaicon.ico">
        <!-- Bootstrap core CSS -->
        <link href="CSS/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="CSS/admin.css" rel="stylesheet">


        <link href="CSS/index.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

        <title>Admin</title>
    </head>

    <body>
        <?php
        session_start();
        if (!$_SESSION["login"]) {
            Header("location: login.php");
        }
        ?>
        <nav class="navbar navbar-toggleable-md navbar-inverse fixed-top bg-inverse">
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <img id="unikiaicon" src="images/unikia-link.png">
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="analyticsdashboard.html">Google Analytics</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="facebooktwo.php">Facebook</a>
                    </li>
                </ul>
            </div>
        </nav>
        <script>
            var id;
            $(window).resize(function () {
                clearTimeout(id);
                id = setTimeout(doneResizing, 500);

            });


            var windowSize = $(window).width();

            function doneResizing() {
                windowSize = $(window).width();

            }
        </script>
        <div id="main">
            <div id="endre_pw">
                <form action="" method="post">
                    <?php
                    include "db_connect.php";

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
                    Update password: <br/>
                    <input type="text" name="lagreBrukernavn" placeholder="Username"/><br/>
                    <input type="password" name="lagrePassord" placeholder="Password"/><br/>
                    <input type="submit" name="lagre" value="Oppdater passord"/><br/><br/><br/>
                    Create user: <br/>
                    <input type="text" name="lageBrukernavn" placeholder="Username"/><br/>
                    <input type="password" name="lagePassord" placeholder="Password"/><br/>
                    <input type="submit" value="Opprett" name="lage">
                </form>
            </div>

            <div id="skriv_brukere">
                <form action="" method ="post">
                    <table>
                        <col width=""/>
                        <col width=""/>
                        <col width="20"/>
                        <th>Brukernavn</th>
                        <th>Passord</th>
                        <th></th>
                        <?php
                        if (isset($_POST['slett_knapp']) and is_numeric($_POST['slett_knapp'])) {
                            $slett_valg = $_POST['slett_knapp'];
                            $db->query("DELETE FROM login where bruker_id = '$slett_valg'");
                        }
                        
                        $result = $db->query("select * from login"); //skrive ut alle øvelser
                        while ($row = $result->fetch_assoc()) {
                            $navn = $row['username'];
                            $passord = $row['password'];
                            $b_id = $row['bruker_id'];

                            echo "<tr>";
                            echo "<td>" . $navn . "</td>";
                            echo "<td>" . $passord . "</td>";
                            echo "<td><input type='image' id='delete_btn' name='slett_knapp' value='" . $b_id . "' src='images/delete_icon.png'/></td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </form>
            </div>
        </div>
        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
        <script>window.jQuery || document.write('<script src="javascript/jquery.min.js"><\/script>')</script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
        <script src="javascript/bootstrap.min.js"></script>
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="javascript/ie10-viewport-bug-workaround.js"></script>
    </body>
</html>