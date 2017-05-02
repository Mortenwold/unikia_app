<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="favicon.ico">
        <!-- Bootstrap core CSS -->
        <link href="CSS/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="CSS/dashboard.css" rel="stylesheet">

        <link id="size-stylesheet" rel="stylesheet" type="text/css" href="ny_farge.css" />
        <title>Unikia Dashboard</title>
    </head>

    <body id="mainbody">

        <nav class="navbar navbar-toggleable-md navbar-inverse fixed-top bg-inverse">
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <img id="unikiaicon" src="images/unikia-link.png">
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="index.html">Home</a>
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

        <h1>Dashboard</h1>


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
        </script>
        <div id="embed-api-auth-container"></div>
        <div id="main">
            <div id="analyticsPart">

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
            gapi.analytics.ready(function () {

                gapi.analytics.auth.authorize({
                    container: 'embed-api-auth-container',
                    clientid: '704702109256-08uvcbane8mgalecg2b4r2el9qp2a9on.apps.googleusercontent.com'
                });

                var viewSelector3 = new gapi.analytics.ext.ViewSelector2({
                    container: 'view-selector-container',
                })
                        .execute();


                /**
                 * Update the activeUsers component, the Chartjs charts, and the dashboard
                 * title whenever the user changes the view.
                 */
                viewSelector3.on('viewChange', function (data) {
                    var title = document.getElementById('view-name');
                    title.textContent = data.property.name + ' (' + data.view.name + ')';

                    // Render all the of charts for this view.
                    renderMonth(data.ids);
                });

            });

                </script>
                <script>

                    // == NOTE ==
                    // This code uses ES6 promises. If you want to use this code in a browser
                    // that doesn't supporting promises natively, you'll have to include a polyfill.

                    gapi.analytics.ready(function () {

                        /**
                         * Authorize the user immediately if the user has already granted access.
                         * If no access has been created, render an authorize button inside the
                         * element with the ID "embed-api-auth-container".
                         */
                        gapi.analytics.auth.authorize({
                            container: 'embed-api-auth-container',
                            clientid: '704702109256-08uvcbane8mgalecg2b4r2el9qp2a9on.apps.googleusercontent.com'
                        });


                        /**
                         * Create a new ActiveUsers instance to be rendered inside of an
                         * element with the id "active-users-container" and poll for changes every
                         * five seconds.
                         */
                        var activeUsers = new gapi.analytics.ext.ActiveUsers({
                            container: 'active-users-container',
                            pollingInterval: 5
                        });


                        /**
                         * Add CSS animation to visually show the when users come and go.
                         */
                        activeUsers.once('success', function () {
                            var element = this.container.firstChild;
                            var timeout;

                            this.on('change', function (data) {
                                var element = this.container.firstChild;
                                var animationClass = data.delta > 0 ? 'is-increasing' : 'is-decreasing';
                                element.className += (' ' + animationClass);

                                clearTimeout(timeout);
                                timeout = setTimeout(function () {
                                    element.className =
                                            element.className.replace(/ is-(increasing|decreasing)/g, '');
                                }, 3000);
                            });
                        });


                        /**
                         * Create a new ViewSelector2 instance to be rendered inside of an
                         * element with the id "view-selector-container".
                         */
                        var viewSelector = new gapi.analytics.ext.ViewSelector2({
                            container: 'view-selector-container',
                        })
                                .execute();


                        /**
                         * Update the activeUsers component, the Chartjs charts, and the dashboard
                         * title whenever the user changes the view.
                         */
                        viewSelector.on('viewChange', function (data) {
                            var title = document.getElementById('view-name');
                            title.textContent = data.property.name + ' (' + data.view.name + ')';

                            // Start tracking active users for this view.
                            activeUsers.set(data).execute();

                            // Render all the of charts for this view.
                            renderWeekOverWeekChart(data.ids);
                            renderYearOverYearChart(data.ids);
                            renderTopBrowsersChart(data.ids);
                            renderTopCountriesChart(data.ids);
                        });


                        /**
                         * Draw the a chart.js line chart with data from the specified view that
                         * overlays session data for the current week over session data for the
                         * previous week.
                         */


                    });
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
        <script src="javascript/analytics_functions.js"></script>
    </body>
</html>