<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="analyticsdesign.css">
        <link rel="stylesheet" href="/public/css/chartjs-visualizations.css">
        <title>Analytics page</title>
        <script>
                (function(w,d,s,g,js,fs){
                  g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(f){this.q.push(f);}};
                  js=d.createElement(s);fs=d.getElementsByTagName(s)[0];
                  js.src='https://apis.google.com/js/platform.js';
                  fs.parentNode.insertBefore(js,fs);js.onload=function(){g.load('analytics');};
                }(window,document,'script'));
        </script>
    </head>
    <body>
        <div class="sidebar">
            <div>
                <img id="unikiaicon" src="images/unikia-link.png">
            </div>
            <div id="list">
                <a>Google Analytics<br></a>
                <a>Facebook<br></a>
                <a>Twitter<br></a>
                <a>M.D. NAV</a>
            </div>
        </div>
        <div id="main">
            <div id="chart1">
            

            <div id="embed-api-auth-container"></div>
            <div id="view-selector-container"></div>
            <div id="data-chart-1-container"></div>
            <div id="date-range-selector-1-container"></div>
            <div id="data-chart-2-container"></div>
            <div id="date-range-selector-2-container"></div>
            <div id="active-users-container"></div>

            <script src="javascript/view-selector2.js"></script>
            <script src="javascript/active-users.js"></script>
            <script src="javascript/date-range-selector.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>

            <script>

            gapi.analytics.ready(function() {

            gapi.analytics.auth.authorize({
                container: 'embed-api-auth-container',
                clientid: '704702109256-08uvcbane8mgalecg2b4r2el9qp2a9on.apps.googleusercontent.com'
            });

            var activeUsers = new gapi.analytics.ext.ActiveUsers({
            container: 'active-users-container',
            pollingInterval: 5
            });


            /**
             * Add CSS animation to visually show the when users come and go.
             */
            activeUsers.once('success', function() {
            var element = this.container.firstChild;
            var timeout;

            this.on('change', function(data) {
            var element = this.container.firstChild;
            var animationClass = data.delta > 0 ? 'is-increasing' : 'is-decreasing';
            element.className += (' ' + animationClass);

            clearTimeout(timeout);
            timeout = setTimeout(function() {
            element.className =
                element.className.replace(/ is-(increasing|decreasing)/g, '');
            }, 3000);
            });
            });

                          var commonConfig = {
                query: {
                  metrics: 'ga:sessions',
                  dimensions: 'ga:date'
                },
                chart: {
                  type: 'LINE',
                  options: {
                    width: '100%'
                  }
                }
              };


              /**
               * Query params representing the first chart's date range.
               */
              var dateRange1 = {
                'start-date': '14daysAgo',
                'end-date': '8daysAgo'
              };


              /**
               * Query params representing the second chart's date range.
               */
              var dateRange2 = {
                'start-date': '7daysAgo',
                'end-date': 'yesterday'
              };


              /**
               * Create a new ViewSelector2 instance to be rendered inside of an
               * element with the id "view-selector-container".
               */
              var viewSelector = new gapi.analytics.ext.ViewSelector2({
                container: 'view-selector-container',
              }).execute();


              /**
               * Create a new DateRangeSelector instance to be rendered inside of an
               * element with the id "date-range-selector-1-container", set its date range
               * and then render it to the page.
               */
              var dateRangeSelector1 = new gapi.analytics.ext.DateRangeSelector({
                container: 'date-range-selector-1-container'
              })
              .set(dateRange1)
              .execute();

              /**
               * Create a new DataChart instance with the given query parameters
               * and Google chart options. It will be rendered inside an element
               * with the id "data-chart-1-container".
               */
              var dataChart1 = new gapi.analytics.googleCharts.DataChart(commonConfig)
                  .set({query: dateRange1})
                  .set({chart: {container: 'data-chart-1-container'}});

              /**
               * Register a handler to run whenever the user changes the view.
               * The handler will update both dataCharts as well as updating the title
               * of the dashboard.
               */
              viewSelector.on('viewChange', function(data) {
                dataChart1.set({query: {ids: data.ids}}).execute();
                dataChart2.set({query: {ids: data.ids}}).execute();

                var title = document.getElementById('view-name');
                title.textContent = data.property.name + ' (' + data.view.name + ')';
              });


              /**
               * Register a handler to run whenever the user changes the date range from
               * the first datepicker. The handler will update the first dataChart
               * instance as well as change the dashboard subtitle to reflect the range.
               */
              dateRangeSelector1.on('change', function(data) {
                dataChart1.set({query: data}).execute();

                // Update the "from" dates text.
                var datefield = document.getElementById('from-dates');
                datefield.textContent = data['start-date'] + '&mdash;' + data['end-date'];
              });

            });
            </script>
            </div>
        </div>
    </body>
</html>
