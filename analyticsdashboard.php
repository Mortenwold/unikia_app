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

        <title>Google Analytics</title>
        <link href="CSS/bootstrap.min.css" rel="stylesheet">
        <link href="CSS/analyticsdesign.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="javascript/analytics_functions.js"></script>
    </head>

    <body>
        <?php
        /*session_start();
        if (!$_SESSION["login"]) {
            Header("location: login.php");
        }*/
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
                    <li class="nav-item active">
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
                    if ($_SESSION["admin"]) {
                        ?>
                        <li class="nav-item">
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
            

            
           var windowSize = $(window).width(), height = $(window).height();
            var id = "graph1";
            function doneResizing() {
                if ($(window).width() != windowSize || $(window).height() != height) {
                    windowSize = $(window).width();
                id = $("#graph").val();
                analyticsdashboard(id, windowSize);
            }

            }
            dropdownAnalytics(windowSize);
        </script>

        <div id="main">
            <div id="chart1">
                <div id="top">
                    <h1>Analytics</h1>
                    <div id="align">
                        <div id="active-users-container"class="btn btn-secondary"></div>
                    </div>
                    
                </div>
                <div id="topchart">
                    <div id="embed-api-auth-container"></div>
                    <header>  
                        <form action="analyticsdashboard.php" method ="post">
                            <select id="graph" onchange="dropdownAnalytics(windowSize)">     
                                <option value="graph1" selected="selected">Transaction Revenue</option>
                                <option value="graph2">Transactions per user</option>
                                <option value="graph3">Item quantity</option>
                                <option value="graph4">Product revenue per purchase</option>
                                <option value="graph5">Revenue per user</option>
                                <option value="graph6">Items per purchase</option>
                            </select>
                        </form>

                        <div id="view-selector-container"></div>
                        <div id="data-chart-1-container"></div>
                        <div id="date-range-selector-1-container"></div>
                        <a href="https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue=http://www.unikiadashboard.com/analyticsdashboard.php">
                            <button class="btn btn-secondary" id="glogout">Logout from Google</button></a>
                </div>


                <div id="view-name"></div>

                <div id="charts">      
                    <div class="position" id="c1">  
                        <h3>This Week vs Last Week (by Revenue)</h3>
                        <figure class="Chartjs-figure" id="chart-1-container"></figure>
                        <ol class="Chartjs-legend" id="legend-1-container"></ol>
                    </div>    
                    <div class="position" id="c2">  
                        <div class="Chartjs">
                            <h3>This Year vs Last Year (by Revenue)</h3>
                            <figure class="Chartjs-figure" id="chart-2-container"></figure>
                            <ol class="Chartjs-legend" id="legend-2-container"></ol>
                        </div>
                    </div> 
                    <div class="position" id="c3">  
                        <h3>Top Browsers (by Revenue)</h3>
                        <figure class="Chartjs-figure" id="chart-3-container"></figure>
                        <ol class="Chartjs-legend" id="legend-3-container"></ol>
                    </div>
                    <div class="position" id="c4">   
                        <h3>Top Countries (by sessions)</h3>
                        <figure class="Chartjs-figure" id="chart-4-container"></figure>
                        <ol class="Chartjs-legend" id="legend-4-container"></ol>
                    </div>
                </div>
                <div id="datechart">
                    <div id="data-chart-2-container"></div>
                    <div id="date-range-selector-2-container"></div>
                </div>

                <script src="javascript/view-selector2.js"></script>
                <script src="javascript/active-users.js"></script>
                <script src="javascript/date-range-selector.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
                <link rel="stylesheet" href="chartjs-visualizations.css">
                <script>
                                analyticsdashboard(id, windowSize);
                </script>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
        <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
        <script src="javascript/bootstrap.min.js"></script>
        <script src="javascript/ie10-viewport-bug-workaround.js"></script>
    </body>
</html>