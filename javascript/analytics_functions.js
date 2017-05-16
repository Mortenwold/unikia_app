function renderWeekOverWeekChart(ids) {
    var now = moment();

    var thisWeek = query({
        'ids': ids,
        'dimensions': 'ga:date,ga:nthDay',
        'metrics': 'ga:transactionRevenue',
        'start-date': moment(now).subtract(1, 'day')
                .day(0).format('YYYY-MM-DD'),
        'end-date': moment(now).format('YYYY-MM-DD')
    });

    var lastWeek = query({
        'ids': ids,
        'dimensions': 'ga:date,ga:nthDay',
        'metrics': 'ga:transactionRevenue',
        'start-date': moment(now).subtract(1, 'day')
                .day(0).subtract(1, 'week')
                .format('YYYY-MM-DD'),
        'end-date': moment(now).subtract(1, 'day')
                .day(6).subtract(1, 'week')
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
                    fillColor: 'rgba(82,10,118,0.5)',
                    strokeColor: 'rgba(82,10,118,1)',
                    pointColor: 'rgba(82,10,118,1)',
                    pointStrokeColor: '#fff',
                    data: data1
                }
            ]
        };

        new Chart(makeCanvas('chart-1-container')).Line(data);
        generateLegend('legend-1-container', data.datasets);
    });
}

function renderYearOverYearChart(ids) {
    var now = moment();

    var thisYear = query({
        'ids': ids,
        'dimensions': 'ga:month,ga:nthMonth',
        'metrics': 'ga:transactionRevenue',
        'start-date': moment(now).date(1).month(0).format('YYYY-MM-DD'),
        'end-date': moment(now).format('YYYY-MM-DD')
    });

    var lastYear = query({
        'ids': ids,
        'dimensions': 'ga:month,ga:nthMonth',
        'metrics': 'ga:transactionRevenue',
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
                    fillColor: 'rgba(82,10,118,0.5)',
                    strokeColor: 'rgba(82,10,118,1)',
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

function renderTopBrowsersChart(ids) {

    query({
        'ids': ids,
        'dimensions': 'ga:browser',
        'metrics': 'ga:transactionRevenue',
        'sort': '-ga:transactionRevenue',
        'max-results': 5
    })
            .then(function (response) {

                var data = [];
                var colors = ['#520A76', '#949FB1', '#D4CCC5', '#E2EAE9', '#F7464A'];

                response.rows.forEach(function (row, i) {
                    data.push({value: +row[1], color: colors[i], label: row[0]});
                });

                new Chart(makeCanvas('chart-3-container')).Doughnut(data);
                generateLegend('legend-3-container', data);
            });
}


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
                var colors = ['#520A76', '#949FB1', '#D4CCC5', '#E2EAE9', '#F7464A'];

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

function generateLegend(id, items) {
    var legend = document.getElementById(id);
    legend.innerHTML = items.map(function (item) {
        var color = item.color || item.fillColor;
        var label = item.label;
        return '<li><div class="foo" style="background-color:' + color + '"></div>' +
                escapeHtml(label) + '</li>';
    }).join('');
}

Chart.defaults.global.animationSteps = 60;
Chart.defaults.global.animationEasing = 'easeInOutQuart';
Chart.defaults.global.responsive = true;
Chart.defaults.global.maintainAspectRatio = false;

function escapeHtml(str) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

function renderTransactionRevenue(ids, windowsizing) {
    var now = moment();

    var thisWeek;

    if (windowsizing < 350) {
        thisWeek = query({
            'ids': ids,
            'dimensions': 'ga:date,ga:nthDay',
            'metrics': 'ga:transactionRevenue',
            'start-date': moment(now).subtract(1, 'day')
                    .day(0).format('YYYY-MM-DD'),
            'end-date': moment(now).format('YYYY-MM-DD')
        });
    } else if (windowsizing < 768) {
        thisWeek = query({
            'ids': ids,
            'dimensions': 'ga:date,ga:nthDay',
            'metrics': 'ga:transactionRevenue',
            'start-date': moment(now).subtract(10, 'day').day(0).format('YYYY-MM-DD'),
            'end-date': moment(now).format('YYYY-MM-DD')
        });
    } else {
        thisWeek = query({
            'ids': ids,
            'dimensions': 'ga:date,ga:nthDay',
            'metrics': 'ga:transactionRevenue',
            'start-date': moment(now).subtract(23, 'day').day(0).format('YYYY-MM-DD'),
            'end-date': moment(now).format('YYYY-MM-DD')
        });
    }

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
                    fillColor: 'rgba(82,10,118,0.5)',
                    strokeColor: 'rgba(82,10,118,1)',
                    pointColor: 'rgba(82,10,118,1)',
                    pointStrokeColor: '#fff',
                    data: data1
                }
            ]
        };

        new Chart(makeCanvas('data-chart-1-container')).Line(data);
    });


}


function renderTransactionsPerUser(ids, windowsizing) {
    var now = moment();

    var thisWeek;

    if (windowsizing < 350) {
        thisWeek = query({
            'ids': ids,
            'dimensions': 'ga:date,ga:nthDay',
            'metrics': 'ga:transactionsPerUser',
            'start-date': moment(now).subtract(1, 'day').day(0).format('YYYY-MM-DD'),
            'end-date': moment(now).format('YYYY-MM-DD')
        });
    } else if (windowsizing < 768) {
        thisWeek = query({
            'ids': ids,
            'dimensions': 'ga:date,ga:nthDay',
            'metrics': 'ga:transactionsPerUser',
            'start-date': moment(now).subtract(10, 'day').day(0).format('YYYY-MM-DD'),
            'end-date': moment(now).format('YYYY-MM-DD')
        });
    } else {
        thisWeek = query({
            'ids': ids,
            'dimensions': 'ga:date,ga:nthDay',
            'metrics': 'ga:transactionsPerUser',
            'start-date': moment(now).subtract(23, 'day').day(0).format('YYYY-MM-DD'),
            'end-date': moment(now).format('YYYY-MM-DD')
        });
    }

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
                    fillColor: 'rgba(82,10,118,0.5)',
                    strokeColor: 'rgba(82,10,118,1)',
                    pointColor: 'rgba(82,10,118,1)',
                    pointStrokeColor: '#fff',
                    data: data1
                }
            ]
        };

        new Chart(makeCanvas('data-chart-1-container')).Line(data);
    });


}

function renderItemQuantity(ids, windowsizing) {
    var now = moment();

    var thisWeek;

    if (windowsizing < 350) {
        thisWeek = query({
            'ids': ids,
            'dimensions': 'ga:date,ga:nthDay',
            'metrics': 'ga:itemQuantity',
            'start-date': moment(now).subtract(1, 'day').day(0).format('YYYY-MM-DD'),
            'end-date': moment(now).format('YYYY-MM-DD')
        });
    } else if (windowsizing < 768) {
        thisWeek = query({
            'ids': ids,
            'dimensions': 'ga:date,ga:nthDay',
            'metrics': 'ga:itemQuantity',
            'start-date': moment(now).subtract(10, 'day').day(0).format('YYYY-MM-DD'),
            'end-date': moment(now).format('YYYY-MM-DD')
        });
    } else {
        thisWeek = query({
            'ids': ids,
            'dimensions': 'ga:date,ga:nthDay',
            'metrics': 'ga:itemQuantity',
            'start-date': moment(now).subtract(23, 'day').day(0).format('YYYY-MM-DD'),
            'end-date': moment(now).format('YYYY-MM-DD')
        });
    }

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
                    fillColor: 'rgba(82,10,118,0.5)',
                    strokeColor: 'rgba(82,10,118,1)',
                    pointColor: 'rgba(82,10,118,1)',
                    pointStrokeColor: '#fff',
                    data: data1
                }
            ]
        };

        new Chart(makeCanvas('data-chart-1-container')).Line(data);
    });

}

function renderProductRevenuePerPurchase(ids, windowSize) {
    var now = moment();

    var thisWeek;

    if (windowSize <= 400) {
        thisWeek = query({
            'ids': ids,
            'dimensions': 'ga:date,ga:nthDay',
            'metrics': 'ga:productRevenuePerPurchase',
            'start-date': moment(now).subtract(1, 'day').day(0).format('YYYY-MM-DD'),
            'end-date': moment(now).format('YYYY-MM-DD')
        });
    } else if (windowSize <= 768) {
        thisWeek = query({
            'ids': ids,
            'dimensions': 'ga:date,ga:nthDay',
            'metrics': 'ga:productRevenuePerPurchase',
            'start-date': moment(now).subtract(6, 'day').day(0).format('YYYY-MM-DD'),
            'end-date': moment(now).format('YYYY-MM-DD')
        });
    } else {
        thisWeek = query({
            'ids': ids,
            'dimensions': 'ga:date,ga:nthDay',
            'metrics': 'ga:productRevenuePerPurchase',
            'start-date': moment(now).subtract(23, 'day').day(0).format('YYYY-MM-DD'),
            'end-date': moment(now).format('YYYY-MM-DD')
        });
    }

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
                    fillColor: 'rgba(82,10,118,0.5)',
                    strokeColor: 'rgba(82,10,118,1)',
                    pointColor: 'rgba(82,10,118,1)',
                    pointStrokeColor: '#fff',
                    data: data1
                }
            ]
        };

        new Chart(makeCanvas('data-chart-1-container')).Line(data);
    });
}

function renderItemsPerPurchase(ids, windowSize) {
    var now = moment();

    var thisWeek;

    if (windowSize <= 400) {
        thisWeek = query({
            'ids': ids,
            'dimensions': 'ga:date,ga:nthDay',
            'metrics': 'ga:itemsPerPurchase',
            'start-date': moment(now).subtract(1, 'day').day(0).format('YYYY-MM-DD'),
            'end-date': moment(now).format('YYYY-MM-DD')
        });
    } else if (windowSize <= 768) {
        thisWeek = query({
            'ids': ids,
            'dimensions': 'ga:date,ga:nthDay',
            'metrics': 'ga:itemsPerPurchase',
            'start-date': moment(now).subtract(6, 'day').day(0).format('YYYY-MM-DD'),
            'end-date': moment(now).format('YYYY-MM-DD')
        });
    } else {
        thisWeek = query({
            'ids': ids,
            'dimensions': 'ga:date,ga:nthDay',
            'metrics': 'ga:itemsPerPurchase',
            'start-date': moment(now).subtract(23, 'day').day(0).format('YYYY-MM-DD'),
            'end-date': moment(now).format('YYYY-MM-DD')
        });
    }

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
                    fillColor: 'rgba(82,10,118,0.5)',
                    strokeColor: 'rgba(82,10,118,1)',
                    pointColor: 'rgba(82,10,118,1)',
                    pointStrokeColor: '#fff',
                    data: data1
                }
            ]
        };

        new Chart(makeCanvas('data-chart-1-container')).Line(data);
    });
}

function renderRevenuePerUser(ids, windowSize) {
    var now = moment();

    var thisWeek;

    if (windowSize <= 400) {
        thisWeek = query({
            'ids': ids,
            'dimensions': 'ga:date,ga:nthDay',
            'metrics': 'ga:revenuePerUser',
            'start-date': moment(now).subtract(1, 'day').day(0).format('YYYY-MM-DD'),
            'end-date': moment(now).format('YYYY-MM-DD')
        });
    } else if (windowSize <= 768) {
        thisWeek = query({
            'ids': ids,
            'dimensions': 'ga:date,ga:nthDay',
            'metrics': 'ga:revenuePerUser',
            'start-date': moment(now).subtract(6, 'day').day(0).format('YYYY-MM-DD'),
            'end-date': moment(now).format('YYYY-MM-DD')
        });
    } else {
        thisWeek = query({
            'ids': ids,
            'dimensions': 'ga:date,ga:nthDay',
            'metrics': 'ga:revenuePerUser',
            'start-date': moment(now).subtract(23, 'day').day(0).format('YYYY-MM-DD'),
            'end-date': moment(now).format('YYYY-MM-DD')
        });
    }

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
                    fillColor: 'rgba(82,10,118,0.5)',
                    strokeColor: 'rgba(82,10,118,1)',
                    pointColor: 'rgba(82,10,118,1)',
                    pointStrokeColor: '#fff',
                    data: data1
                }
            ]
        };

        new Chart(makeCanvas('data-chart-1-container')).Line(data);
    });
}

function dateGraph(ids, windowsizing) {

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

        var dateRange1;
        if (windowsizing < 350) {
            var dateRange1 = {
                'start-date': '3daysAgo',
                'end-date': 'yesterday'
            };
        } else if (windowsizing < 768) {
            var dateRange1 = {
                'start-date': '10daysAgo',
                'end-date': 'yesterday'
            };
        } else {
            var dateRange1 = {
                'start-date': '30daysAgo',
                'end-date': 'yesterday'
            };
        }


        var viewSelector = new gapi.analytics.ext.ViewSelector2({
            container: 'view-selector-container',
        }).execute();




        var dateRangeSelector1 = new gapi.analytics.ext.DateRangeSelector({
            container: 'date-range-selector-2-container'
        })
                .set(dateRange1)
                .execute();

        var dataChart1 = new gapi.analytics.googleCharts.DataChart(commonConfig)
                .set({query: dateRange1})
                .set({chart: {container: 'data-chart-2-container'}});


        viewSelector.on('viewChange', function (data) {
            dataChart1.set({query: {ids: data.ids}}).execute();

            var title = document.getElementById('view-name');
            title.textContent = data.property.name + ' (' + data.view.name + ')';
        });

        dateRangeSelector1.on('change', function (data) {
            dataChart1.set({query: data}).execute();

            var datefield = document.getElementById('from-dates');
            datefield.textContent = data['start-date'] + '&mdash;' + data['end-date'];
        });

    });
}

function dropdownAnalytics(windowSize) {
    var x = document.getElementById("graph").value;
    if (x === "graph1") {
        gapi.analytics.ready(function () {

            gapi.analytics.auth.authorize({
                container: 'embed-api-auth-container',
                clientid: '704702109256-08uvcbane8mgalecg2b4r2el9qp2a9on.apps.googleusercontent.com'
            });

            var viewSelector3 = new gapi.analytics.ext.ViewSelector2({
                container: 'view-selector-container',
            })
                    .execute();

            viewSelector3.on('viewChange', function (data) {
                var title = document.getElementById('view-name');
                title.textContent = data.property.name + ' (' + data.view.name + ')';

                renderTransactionRevenue(data.ids, windowSize);
            });

        });
    } else if (x === "graph2") {

        gapi.analytics.ready(function () {

            gapi.analytics.auth.authorize({
                container: 'embed-api-auth-container',
                clientid: '704702109256-08uvcbane8mgalecg2b4r2el9qp2a9on.apps.googleusercontent.com'
            });

            var viewSelector3 = new gapi.analytics.ext.ViewSelector2({
                container: 'view-selector-container',
            })
                    .execute();


            viewSelector3.on('viewChange', function (data) {
                var title = document.getElementById('view-name');
                title.textContent = data.property.name + ' (' + data.view.name + ')';

                renderTransactionsPerUser(data.ids, windowSize);
            });

        });
    } else if (x === "graph3") {
        gapi.analytics.ready(function () {

            gapi.analytics.auth.authorize({
                container: 'embed-api-auth-container',
                clientid: '704702109256-08uvcbane8mgalecg2b4r2el9qp2a9on.apps.googleusercontent.com'
            });


            var viewSelector3 = new gapi.analytics.ext.ViewSelector2({
                container: 'view-selector-container',
            })
                    .execute();

            viewSelector3.on('viewChange', function (data) {
                var title = document.getElementById('view-name');
                title.textContent = data.property.name + ' (' + data.view.name + ')';

                renderItemQuantity(data.ids, windowSize);
            });

        });
    } else if (x === "graph4") {
        gapi.analytics.ready(function () {

            gapi.analytics.auth.authorize({
                container: 'embed-api-auth-container',
                clientid: '704702109256-08uvcbane8mgalecg2b4r2el9qp2a9on.apps.googleusercontent.com'
            });


            var viewSelector3 = new gapi.analytics.ext.ViewSelector2({
                container: 'view-selector-container',
            })
                    .execute();

            viewSelector3.on('viewChange', function (data) {
                var title = document.getElementById('view-name');
                title.textContent = data.property.name + ' (' + data.view.name + ')';

                renderProductRevenuePerPurchase(data.ids, windowSize);
            });

        });
    }
    else if (x === "graph5") {
        gapi.analytics.ready(function () {

            gapi.analytics.auth.authorize({
                container: 'embed-api-auth-container',
                clientid: '704702109256-08uvcbane8mgalecg2b4r2el9qp2a9on.apps.googleusercontent.com'
            });


            var viewSelector3 = new gapi.analytics.ext.ViewSelector2({
                container: 'view-selector-container',
            })
                    .execute();

            viewSelector3.on('viewChange', function (data) {
                var title = document.getElementById('view-name');
                title.textContent = data.property.name + ' (' + data.view.name + ')';

                renderRevenuePerUser(data.ids, windowSize);
            });

        });
    }
    else if (x === "graph6") {
        gapi.analytics.ready(function () {

            gapi.analytics.auth.authorize({
                container: 'embed-api-auth-container',
                clientid: '704702109256-08uvcbane8mgalecg2b4r2el9qp2a9on.apps.googleusercontent.com'
            });


            var viewSelector3 = new gapi.analytics.ext.ViewSelector2({
                container: 'view-selector-container',
            })
                    .execute();

            viewSelector3.on('viewChange', function (data) {
                var title = document.getElementById('view-name');
                title.textContent = data.property.name + ' (' + data.view.name + ')';

                renderItemsPerPurchase(data.ids, windowSize);
            });

        });
    }
}

function skriv_graf(windowSize) {
    gapi.analytics.ready(function () {
        gapi.analytics.auth.authorize({
            container: 'embed-api-auth-container',
            clientid: '704702109256-08uvcbane8mgalecg2b4r2el9qp2a9on.apps.googleusercontent.com'
        });

        var viewSelector3 = new gapi.analytics.ext.ViewSelector2({
            container: 'view-selector-container'
        })
                .execute();

        viewSelector3.on('viewChange', function (data) {
            var title = document.getElementById('view-name');
            title.textContent = data.property.name + ' (' + data.view.name + ')';

            renderTransactionRevenue(data.ids, windowSize);
        });
    });
}

function analyticsdashboard(id, windowSize) {
    gapi.analytics.ready(function () {

        gapi.analytics.auth.authorize({
            container: 'embed-api-auth-container',
            clientid: '704702109256-08uvcbane8mgalecg2b4r2el9qp2a9on.apps.googleusercontent.com'
        });

        var activeUsers = new gapi.analytics.ext.ActiveUsers({
            container: 'active-users-container',
            pollingInterval: 5
        });

        activeUsers.once('success', function () {
            var element = this.container.firstChild;
            var timeout;

            this.on('change', function (data) {
                element = this.container.firstChild;
                var animationClass = data.delta > 0 ? 'is-increasing' : 'is-decreasing';
                element.className += (' ' + animationClass);

                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    element.className =
                            element.className.replace(/ is-(increasing|decreasing)/g, '');
                }, 3000);
            });
        });

        var viewSelector = new gapi.analytics.ext.ViewSelector2({
            container: 'view-selector-container',
        })
                .execute();

        viewSelector.on('viewChange', function (data) {
            var title = document.getElementById('view-name');
            title.textContent = data.property.name + ' (' + data.view.name + ')';

            activeUsers.set(data).execute();
            renderWeekOverWeekChart(data.ids);
            renderYearOverYearChart(data.ids);
            renderTopBrowsersChart(data.ids);
            renderTopCountriesChart(data.ids);

            if (id === "graph1") {
                renderTransactionRevenue(data.ids, windowSize);
            } else if (id === "graph2") {
                renderTransactionPerUser(data.ids, windowSize);
            } else if (id === "graph3") {
                renderItemQuantity(data.ids, windowSize);
            } else if (id === "graph4") {
                renderProductRevenuePerPurchase(data.ids, windowSize);
            } else if (id === "graph5") {
                renderTransactionsPerSession(data.ids, windowSize);
            } else if (id === "graph6") {
                renderItemPerPurchase(data.ids, windowSize);
            }           
            dateGraph(data.ids, windowSize);
        });
    });
}
