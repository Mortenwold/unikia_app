<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="unikia-link.png">
        <!-- Bootstrap core CSS -->
        <link href="CSS/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="CSS/index.css" rel="stylesheet">

        <script src="javascript/analytics_functions.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

        <title>Unikia Dashboard</title>
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
                        <a class="nav-link dropdown-toggle"  id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown</a>
                        <div class="dropdown-menu" aria-labelledby="dropdown01">
                            <a class="dropdown-item" href="#">Action</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </li>
                    <?php
                    if ($_SESSION["admin"]) {
                        ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php">Admin</a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </nav>
        <script>
            (function (w, d, s, g, js, fs) {
                g = w.gapi || (w.gapi = {});
                g.analytics = {q: [], ready: function (f) {
                        this.q.push(f);
                    }};
                js = d.createElement(s);
                fs = d.getElementsByTagName(s)[0];
                js.src = 'https://apis.google.com/js/platform.js';
                fs.parentNode.insertBefore(js, fs);
                js.onload = function () {
                    g.load('analytics');
                };
            }(window, document, 'script'));

            var id;
            $(window).resize(function () {
                clearTimeout(id);
                id = setTimeout(doneResizing, 500);

            });


            var windowSize = $(window).width();

            function doneResizing() {
                windowSize = $(window).width();

                skriv_graf(windowSize);
            }
        </script>
        <div id="main">
            <div id="analyticsPart">
                <div id="embed-api-auth-container"></div>
                <p class="text-muted">Number of pageviews for www.Unikia.no</p>

                <div id="view-selector-container"></div>
                <div id="data-chart-1-container"></div>
                <div id="date-range-selector-1-container"></div>

                <div id="data-chart-2-container"></div>
                <div id="date-range-selector-2-container"></div>

                <div id="view-name"></div>

                <script src="javascript/view-selector2.js"></script>
                <script src="javascript/active-users.js"></script>
                <script src="javascript/date-range-selector.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
                <link rel="stylesheet" href="chartjs-visualizations.css">
                <script>
            skriv_graf(windowSize);
                </script>
            </div>
        </div>
        <a href="googleanalytics.html"></a>
        <div id="twittersection">
            <!--<div class="container">
            <h3>Total Follower : <strong></strong></h3>
        </div>-->

            <a class="twitter-timeline" data-height="20rem" data-chrome="nofooter, noheader" href="https://twitter.com/unikiadotcom"></a>
            <script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
        </div>

        <a href="analyticsdashboard.html">


            <div class="section" id="section2">


            </div>
        </a>
        <a href="facebooktwo.php">Facebook Link</a>

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