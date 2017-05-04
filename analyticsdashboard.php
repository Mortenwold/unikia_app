<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="images/unikiaicon.ico">

        <title>Google Analytics</title>

        <!-- Bootstrap core CSS -->
        <link href="CSS/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="CSS/analyticsdesign.css" rel="stylesheet">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="javascript/analytics_functions.js"></script>
    </head>
    <nav class="navbar navbar-toggleable-md navbar-inverse fixed-top bg-inverse">
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <img id="unikiaicon" src="images/unikia-link.png">
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="analyticsdashboard.php">Google Analytics</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="facebooktwo.php">Facebook</a>
                </li>
            </ul>
        </div>
    </nav>
    <body>
        <?php
        session_start();
        if (!$_SESSION["login"]) {
            Header("location: login.php");
        }
        ?>


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
            var id = "graph1";
            function doneResizing() {
                id = $("#graph").val();
                windowSize = $(window).width();
                analyticsdashboard(id, windowSize);

            }
        </script>

        <div id="main">
            <div id="chart1">
                <div id="top">
                    <h1>Dashboard</h1>

                    <div id="active-users-container"></div>
                </div>
                <div id="topchart">
                    <div id="embed-api-auth-container"></div>
                    <header>  
                        <form action="" method ="post">
                            <select id="graph" onchange="myFunction1(windowSize)">     
                                <option value="graph1" selected="selected">New users</option>
                                <option value="graph2">Pageviews</option>
                                <option value="graph3">Time on page</option>
                                <option value="graph4">% new sessions</option>
                            </select>
                        </form>

                        <div id="view-selector-container"></div>
                        <div id="data-chart-1-container"></div>
                        <div id="date-range-selector-1-container"></div>
                </div>


                <div id="view-name"></div>
                <div id="charts">      
                    <div class="position" id="c1">  
                        <h3>This Week vs Last Week (by sessions)</h3>
                        <figure class="Chartjs-figure" id="chart-1-container"></figure>
                        <ol class="Chartjs-legend" id="legend-1-container"></ol>
                    </div>    
                    <div class="position" id="c2">  
                        <div class="Chartjs">
                            <h3>This Year vs Last Year (by users)</h3>
                            <figure class="Chartjs-figure" id="chart-2-container"></figure>
                            <ol class="Chartjs-legend" id="legend-2-container"></ol>
                        </div>
                    </div> 
                    <div class="position" id="c3">  
                        <h3>Top Browsers (by pageview)</h3>
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

        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
        <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
        <script src="javascript/bootstrap.min.js"></script>
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="javascript/ie10-viewport-bug-workaround.js"></script>

    </body>
</html>