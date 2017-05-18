<!DOCTYPE html>
<?php
error_reporting(0);
?>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="images/unikiaicon.ico">
        <link href="CSS/bootstrap.min.css" rel="stylesheet">
        <link href="CSS/admin.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="javascript/adminRegex.js"></script>
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
                        <a class="nav-link" href="analyticsdashboard.php">Google Analytics</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Facebook</a>
                        <div class="dropdown-menu" aria-labelledby="dropdown01">
                            <a class="dropdown-item" id ="menuLinks" href="facebookone.php">UnikiaNorge</a>
                            <a class="dropdown-item" id="menuLinks" href="facebooktwo.php">UnikiaInnovation</a>
                            <a class="dropdown-item" id="menuLinks" href="facebookthree.php">Barnas Designlab</a>
                            <a class="dropdown-item" id="menuLinks" href="facebook.php">Facebook Archive</a>
                        </div>
                    </li>
                    <?php
                    if ($_SESSION["login"]) {
                        ?>
                        <li class="nav-item active">
                            <a class="nav-link" href="admin.php">Admin</a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
                <button id="logout_btn" onclick="location.href = 'login.php';">
                    <img src="images/logout_btn.png" id="logout">
                </button>
            </div>
        </nav>
        <div id="main">
            <div id="endre_pw">
                <form action="" method="post" name="reg_login">
                    <?php
                    include "db_connect.php";

                    if (isset($_POST["lagre"])) {
                        $lagreBrukernavn = $db->escape_string($_POST["lagreBrukernavn"]);
                        $lagrePassord = $db->escape_string($_POST["lagrePassord"]);
                        if (empty($lagreBrukernavn)) {
                            echo 'Username is required!<br>';
                        } else if (empty($lagrePassord)) {
                            echo 'Password is required!<br>';
                        } else {
                            $sql = "Update login Set password = Password('$lagrePassord') where username='$lagreBrukernavn'";
                            $res = $db->query($sql);
                            if ($db->affected_rows > 0) {
                                echo "<p style='color: green'>Update successful</p>";
                            } else {
                                echo "<p style='color: red'>Username does not exist</p>";
                            }
                        }
                    }
                    ?>
                    Update password: <br/>
                    <input type="text" name="lagreBrukernavn" placeholder="Username"/><br/>
                    <input type="password" name="lagrePassord" placeholder="Password"/><br/>
                    <input type="submit" name="lagre" value="Update Password"/><br/><br/><br/>
                    <?php
                    if (isset($_POST["lage"])) {
                        $lageBrukernavn = $db->escape_string($_POST["lageBrukernavn"]);
                        $lagePassord = $db->escape_string($_POST["lagePassord"]);
                        $res = $db->query("select * from login where username='$lageBrukernavn'");
                        if (mysqli_num_rows($res) > 0) {
                            echo "<p style='color: red'>Username already exists</p>";
                        } else {
                            if (empty($lageBrukernavn)) {
                                echo 'Username is required!<br>';
                            } else if (empty($lagePassord)) {
                                echo 'Password is required!<br>';
                            } else {
                                $sql1 = "INSERT INTO login (username, password)
                                VALUES ('$lageBrukernavn', 'Password($lagePassord)') ";
                                $sql2 = "Update login Set password = Password('$lagePassord') where username='$lageBrukernavn'";
                                $res1 = $db->query($sql1);
                                $res2 = $db->query($sql2);
                                if ($db->affected_rows > 0) {
                                    echo "<p style='color: green'>User created successfully</p>";
                                } else {
                                    echo "<p style='color: red'>Something went wrong!</p>";
                                }
                            }
                        }
                    }
                    ?>
                    Create user: <br/>
                    <div id="error_create"></div>
                    <input type="text" name="lageBrukernavn" placeholder="Username" onchange="sjekkAdmin()"/><br/>
                    <input type="password" name="lagePassord" placeholder="Password"/><br/>
                    <input type="submit" value="Create User" name="lage">
                </form>
            </div>

            <div id="skriv_brukere">
                <form action="" method ="post">
                    <table>
                        <col width="20"/>
                        <col width=""/>
                        <col width=""/>
                        <col width="20"/>
                        <th>ID</th>
                        <th>Brukernavn</th>
                        <th>Passord</th>
                        <th></th>
                        <?php
                        if (isset($_POST['slett_knapp'])) {
                            $slett_valg = $_POST['slett_knapp'];
                            $db->query("DELETE FROM login where bruker_id = '$slett_valg'");
                        }

                        $result = $db->query("select * from login");
                        while ($row = $result->fetch_assoc()) {
                            $navn = $row['username'];
                            $passord = $row['password'];
                            $b_id = $row['bruker_id'];

                            echo "<tr>";
                            echo "<td>" . $b_id . "</td>";
                            echo "<td>" . $navn . "</td>";
                            echo "<td>" . $passord . "</td>";
                            if ($navn != "admin") {
                                echo "<td><input type='image' id='delete_btn' name='slett_knapp' value='" . $b_id . "' src='images/delete_icon.png'/></td>";
                            }
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </form>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
        <script>window.jQuery || document.write('<script src="javascript/jquery.min.js"><\/script>')</script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
        <script src="javascript/bootstrap.min.js"></script>
        <script src="javascript/ie10-viewport-bug-workaround.js"></script>
    </body>
</html>