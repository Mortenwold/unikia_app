function renderWeekOverWeekChart(ids) {

    // Adjust `now` to experiment with different days, for testing only...
    var now = moment(); // .subtract(3, 'day');

    var thisWeek = query({
        'ids': ids,
        'dimensions': 'ga:date,ga:nthDay',
        'metrics': 'ga:sessions',
        'start-date': moment(now).subtract(1, 'day').day(0).format('YYYY-MM-DD'),
        'end-date': moment(now).format('YYYY-MM-DD')
    });

    var lastWeek = query({
        'ids': ids,
        'dimensions': 'ga:date,ga:nthDay',
        'metrics': 'ga:sessions',
        'start-date': moment(now).subtract(1, 'day').day(0).subtract(1, 'week')
                .format('YYYY-MM-DD'),
        'end-date': moment(now).subtract(1, 'day').day(6).subtract(1, 'week')
                .format('YYYY-MM-DD')
    });

    Promise.all([thisWeek, lastWeek]).then(function (results) {

        var data1 = results[0].rows.map(function (row) {
            return +row[2];
        });
        var data2 = results[1].rows.map(function (row) {
            return +row[2];
        });
        var labels = results[1].rows.map(function (row) {
            return +row[0];
        });

        labels = labels.map(function (label) {
            return moment(label, 'YYYYMMDD').format('ddd');
        });

        var data = {
            labels: labels,
            datasets: [
                {
                    label: 'Last Week',
                    fillColor: 'rgba(220,220,220,0.5)',
                    strokeColor: 'rgba(220,220,220,1)',
                    pointColor: 'rgba(220,220,220,1)',
                    pointStrokeColor: '#fff',
                    data: data2
                },
                {
                    label: 'This Week',
                    fillColor: 'rgba(151,187,205,0.5)',
                    strokeColor: 'rgba(151,187,205,1)',
                    pointColor: 'rgba(151,187,205,1)',
                    pointStrokeColor: '#fff',
                    data: data1
                }
            ]
        };

        new Chart(makeCanvas('chart-1-container')).Line(data);
        generateLegend('legend-1-container', data.datasets);
    });
}


/**
 * Draw the a chart.js bar chart with data from the specified view that
 * overlays session data for the current year over session data for the
 * previous year, grouped by month.
 */
function renderYearOverYearChart(ids) {

    // Adjust `now` to experiment with different days, for testing only...
    var now = moment(); // .subtract(3, 'day');

    var thisYear = query({
        'ids': ids,
        'dimensions': 'ga:month,ga:nthMonth',
        'metrics': 'ga:users',
        'start-date': moment(now).date(1).month(0).format('YYYY-MM-DD'),
        'end-date': moment(now).format('YYYY-MM-DD')
    });

    var lastYear = query({
        'ids': ids,
        'dimensions': 'ga:month,ga:nthMonth',
        'metrics': 'ga:users',
        'start-date': moment(now).subtract(1, 'year').date(1).month(0)
                .format('YYYY-MM-DD'),
        'end-date': moment(now).date(1).month(0).subtract(1, 'day')
                .format('YYYY-MM-DD')
    });

    Promise.all([thisYear, lastYear]).then(function (results) {
        var data1 = results[0].rows.map(function (row) {
            return +row[2];
        });
        var data2 = results[1].rows.map(function (row) {
            return +row[2];
        });
        var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Ensure the data arrays are at least as long as the labels array.
        // Chart.js bar charts don't (yet) accept sparse datasets.
        for (var i = 0, len = labels.length; i < len; i++) {
            if (data1[i] === undefined)
                data1[i] = null;
            if (data2[i] === undefined)
                data2[i] = null;
        }

        var data = {
            labels: labels,
            datasets: [
                {
                    label: 'Last Year',
                    fillColor: 'rgba(220,220,220,0.5)',
                    strokeColor: 'rgba(220,220,220,1)',
                    data: data2
                },
                {
                    label: 'This Year',
                    fillColor: 'rgba(151,187,205,0.5)',
                    strokeColor: 'rgba(151,187,205,1)',
                    data: data1
                }
            ]
        };

        new Chart(makeCanvas('chart-2-container')).Bar(data);
        generateLegend('legend-2-container', data.datasets);
    })
            .catch(function (err) {
                console.error(err.stack);
            });
}


/**
 * Draw the a chart.js doughnut chart with data from the specified view that
 * show the top 5 browsers over the past seven days.
 */
function renderTopBrowsersChart(ids) {

    query({
        'ids': ids,
        'dimensions': 'ga:browser',
        'metrics': 'ga:pageviews',
        'sort': '-ga:pageviews',
        'max-results': 5
    })
            .then(function (response) {

                var data = [];
                var colors = ['#4D5360', '#949FB1', '#D4CCC5', '#E2EAE9', '#F7464A'];

                response.rows.forEach(function (row, i) {
                    data.push({value: +row[1], color: colors[i], label: row[0]});
                });

                new Chart(makeCanvas('chart-3-container')).Doughnut(data);
                generateLegend('legend-3-container', data);
            });
}


/**
 * Draw the a chart.js doughnut chart with data from the specified view that
 * compares sessions from mobile, desktop, and tablet over the past seven
 * days.
 */
function renderTopCountriesChart(ids) {
    query({
        'ids': ids,
        'dimensions': 'ga:country',
        'metrics': 'ga:sessions',
        'sort': '-ga:sessions',
        'max-results': 5
    })
            .then(function (response) {

                var data = [];
                var colors = ['#4D5360', '#949FB1', '#D4CCC5', '#E2EAE9', '#F7464A'];

                response.rows.forEach(function (row, i) {
                    data.push({
                        label: row[0],
                        value: +row[1],
                        color: colors[i]
                    });
                });

                new Chart(makeCanvas('chart-4-container')).Doughnut(data);
                generateLegend('legend-4-container', data);
            });
}


/**
 * Extend the Embed APIs `gapi.analytics.report.Data` component to
 * return a promise the is fulfilled with the value returned by the API.
 * @param {Object} params The request parameters.
 * @return {Promise} A promise.
 */
function query(params) {
    return new Promise(function (resolve, reject) {
        var data = new gapi.analytics.report.Data({query: params});
        data.once('success', function (response) {
            resolve(response);
        })
                .once('error', function (response) {
                    reject(response);
                })
                .execute();
    });
}


/**
 * Create a new canvas inside the specified element. Set it to be the width
 * and height of its container.
 * @param {string} id The id attribute of the element to host the canvas.
 * @return {RenderingContext} The 2D canvas context.
 */
function makeCanvas(id) {
    var container = document.getElementById(id);
    var canvas = document.createElement('canvas');
    var ctx = canvas.getContext('2d');

    container.innerHTML = '';
    canvas.width = container.offsetWidth;
    canvas.height = container.offsetHeight;
    container.appendChild(canvas);

    return ctx;
}


/**
 * Create a visual legend inside the specified element based off of a
 * Chart.js dataset.
 * @param {string} id The id attribute of the element to host the legend.
 * @param {Array.<Object>} items A list of labels and colors for the legend.
 */
function generateLegend(id, items) {
    var legend = document.getElementById(id);
    legend.innerHTML = items.map(function (item) {
        var color = item.color || item.fillColor;
        var label = item.label;
        return '<li><i style="background:' + color + '"></i>' +
                escapeHtml(label) + '</li>';
    }).join('');
}


// Set some global Chart.js defaults.
Chart.defaults.global.animationSteps = 60;
Chart.defaults.global.animationEasing = 'easeInOutQuart';
Chart.defaults.global.responsive = true;
Chart.defaults.global.maintainAspectRatio = false;


/**
 * Escapes a potentially unsafe HTML string.
 * @param {string} str An string that may contain HTML entities.
 * @return {string} The HTML-escaped string.
 */
function escapeHtml(str) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}
function renderMonth(ids) {

    // Adjust `now` to experiment with different days, for testing only...
    var now = moment(); // .subtract(3, 'day');

    var thisWeek = query({
        'ids': ids,
        'dimensions': 'ga:date,ga:nthDay',
        'metrics': 'ga:sessions',
        'start-date': moment(now).subtract(30, 'day').day(0).format('YYYY-MM-DD'),
        'end-date': moment(now).format('YYYY-MM-DD')
    });

    Promise.all([thisWeek]).then(function (results) {

        var data1 = results[0].rows.map(function (row) {
            return +row[2];
        });
        var labels = results[0].rows.map(function (row) {
            return +row[0];
        });

        labels = labels.map(function (label) {
            return moment(label, 'YYYYMMDD').format('MMM - DD');
        });

        var data = {
            labels: labels,
            datasets: [
                {
                    label: 'This Week',
                    fillColor: 'rgba(151,187,205,0.5)',
                    strokeColor: 'rgba(151,187,205,1)',
                    pointColor: 'rgba(151,187,205,1)',
                    pointStrokeColor: '#fff',
                    data: data1
                }
            ]
        };

        new Chart(makeCanvas('data-chart-1-container')).Line(data);
        generateLegend('legend-1-container', data.datasets);
    });


}
function myFunction1() {
    var x = document.getElementById("graph").value;
    if (x == "graph1") {
        gapi.analytics.ready(function () {

            gapi.analytics.auth.authorize({
                container: 'embed-api-auth-container',
                clientid: '704702109256-08uvcbane8mgalecg2b4r2el9qp2a9on.apps.googleusercontent.com'
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
                'start-date': '7daysAgo',
                'end-date': '1daysAgo'
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
            viewSelector.on('viewChange', function (data) {
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
            dateRangeSelector1.on('change', function (data) {
                dataChart1.set({query: data}).execute();

                // Update the "from" dates text.
                var datefield = document.getElementById('from-dates');
                datefield.textContent = data['start-date'] + '&mdash;' + data['end-date'];
            });

        });
    } else if (x == "graph2") {

        gapi.analytics.ready(function () {

            gapi.analytics.auth.authorize({
                container: 'embed-api-auth-container',
                clientid: '704702109256-08uvcbane8mgalecg2b4r2el9qp2a9on.apps.googleusercontent.com'
            });

            var commonConfig = {
                query: {
                    metrics: 'ga:pageviews',
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
            viewSelector.on('viewChange', function (data) {
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
            dateRangeSelector1.on('change', function (data) {
                dataChart1.set({query: data}).execute();

                // Update the "from" dates text.
                var datefield = document.getElementById('from-dates');
                datefield.textContent = data['start-date'] + '&mdash;' + data['end-date'];
            });

        });
    } else if (x == "graph3") {
        gapi.analytics.ready(function () {

            gapi.analytics.auth.authorize({
                container: 'embed-api-auth-container',
                clientid: '704702109256-08uvcbane8mgalecg2b4r2el9qp2a9on.apps.googleusercontent.com'
            });

            var commonConfig = {
                query: {
                    metrics: 'ga:timeOnPage',
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
            viewSelector.on('viewChange', function (data) {
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
            dateRangeSelector1.on('change', function (data) {
                dataChart1.set({query: data}).execute();

                // Update the "from" dates text.
                var datefield = document.getElementById('from-dates');
                datefield.textContent = data['start-date'] + '&mdash;' + data['end-date'];
            });

        });
    } else if (x == "graph4") {
        gapi.analytics.ready(function () {

            gapi.analytics.auth.authorize({
                container: 'embed-api-auth-container',
                clientid: '704702109256-08uvcbane8mgalecg2b4r2el9qp2a9on.apps.googleusercontent.com'
            });

            var commonConfig = {
                query: {
                    metrics: 'ga:percentNewSessions',
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
            viewSelector.on('viewChange', function (data) {
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
            dateRangeSelector1.on('change', function (data) {
                dataChart1.set({query: data}).execute();

                // Update the "from" dates text.
                var datefield = document.getElementById('from-dates');
                datefield.textContent = data['start-date'] + '&mdash;' + data['end-date'];
            });

        });
    }
}

function adjustStyle(width) {
  width = parseInt(width);
  if (width < 701) {
    $("#size-stylesheet").attr("href", "css/ny_farge.css");
  } else if (width < 900) {
    $("#size-stylesheet").attr("href", "css/medium.css");
  } else {
     $("#size-stylesheet").attr("href", "css/wide.css"); 
  }
}

$(function() {
  adjustStyle($(this).width());
  $(window).resize(function() {
    adjustStyle($(this).width());
  });
});
