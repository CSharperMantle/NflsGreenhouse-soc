var App = (() => {
    'use strict';

    App.config = {
        AJAX_ALERT_DIV_URI: 'api/internal/ajax-alert-div.php',
        AJAX_SPARKLINE_URI: 'api/internal/ajax-sparkline-data.php',
        AJAXTYPE_COMMITS_SPARKLINE: 0,
        AJAXTYPE_ALERTS_SPARKLINE: 1
    };



    App.counter = function () {

        $('[data-toggle="counter"]').each(function (_index, _element) {
            var elem = $(this);
            var prefix = '';
            var suffix = '';
            var start = 0;
            var end = 0;
            var decimals = 0;
            var duration = 2.5;

            if (elem.data('prefix')) {
                prefix = elem.data('prefix');
            }
            if (elem.data('suffix')) {
                suffix = elem.data('suffix');
            }
            if (elem.data('start')) {
                start = elem.data('start');
            }
            if (elem.data('end')) {
                end = elem.data('end');
            }
            if (elem.data('decimals')) {
                decimals = elem.data('decimals');
            }
            if (elem.data('duration')) {
                duration = elem.data('duration');
            }
            var count = new CountUp(elem.get(0), start, end, decimals, duration, {
                suffix: suffix,
                prefix: prefix,
            });
            count.start();
        });
    }

    App.toggleLoadingButton = function () {
        $('.toggle-loading').on('click', function () {
            var parent = $(this).parents('.widget, .panel');
            if (parent.length) {
                parent.addClass('be-loading-active');

                setTimeout(function () {
                    this.document.location.reload();
                    parent.removeClass('be-loading-active');
                }, 300);
            }
        });
    }

    App.toggleCloseButton = function () {
        $('.toggle-close').on('click', function () {
            var parent = $(this).parents('.card, .widget, .panel');
            if (parent.length) {
                parent.fadeOut();
            }
        });
    }

    App.sparkline = function () {

        var colorPrimary = App.color.primary;
        var colorWarning = App.color.warning;
        var colorSuccess = App.color.success;
        var colorDanger = App.color.danger;

        var xhrCommitSpark = new XMLHttpRequest();
        var xhrAlertSpark = new XMLHttpRequest();

        xhrCommitSpark.addEventListener('load', function () {
            var commitsSparkData = JSON.parse(this.responseText);
            var commitsSparkDataFlatten = commitsSparkData.flat();
            $('#all-commits-count-sparkline').sparkline(commitsSparkDataFlatten, {
                width: '85',
                height: '35',
                lineColor: colorPrimary,
                highlightSpotColor: colorPrimary,
                highlightLineColor: colorPrimary,
                fillColor: false,
                spotColor: false,
                minSpotColor: false,
                maxSpotColor: false,
                lineWidth: 1.15
            });
        });
        xhrCommitSpark.open('POST', App.config.AJAX_SPARKLINE_URI);
        xhrCommitSpark.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhrCommitSpark.send("data_type=" + App.config.AJAXTYPE_COMMITS_SPARKLINE);

        xhrAlertSpark.addEventListener('load', function () {
            var alertsSparkData = JSON.parse(this.responseText);
            var alertsSparkDataFlatten = alertsSparkData.flat();
            $('#all-alerts-count-sparkline').sparkline(alertsSparkDataFlatten, {
                type: 'bar',
                width: '85',
                height: '35',
                barWidth: 4,
                barSpacing: 3,
                chartRangeMin: 0,
                barColor: colorSuccess
            });
        });
        xhrAlertSpark.open('POST', App.config.AJAX_SPARKLINE_URI);
        xhrAlertSpark.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhrAlertSpark.send("data_type=" + App.config.AJAXTYPE_ALERTS_SPARKLINE);

    }

    App.dataTables = () => {

        //We use this to apply style to certain elements
        $.extend(true, $.fn.dataTable.defaults, {
            dom: "<'row be-datatable-header'<'col-sm-6'l><'col-sm-6'f>>" +
                "<'row be-datatable-body'<'col-sm-12'tr>>" +
                "<'row be-datatable-footer'<'col-sm-5'i><'col-sm-7'p>>"
        });

        //Remove search & paging dropdown
        $("#history-data-table").dataTable({
            pageLength: 5,
            dom: "<'row be-datatable-body'<'col-sm-12'tr>>" +
                "<'row be-datatable-footer'<'col-sm-5'i><'col-sm-7'p>>"
        });
    }

    App.map = function () {

        var color1 = tinycolor(App.color.primary).lighten(15).toHexString();
        var color2 = tinycolor(App.color.primary).lighten(8).toHexString();
        var color3 = tinycolor(App.color.primary).toHexString();

        //Highlight data
        var data = {
            "ru": "14",
            "us": "14",
            "ca": "10",
            "br": "10",
            "au": "11",
            "uk": "3",
            "cn": "12"
        };

        $('#map-widget').vectorMap({
            map: 'world_en',
            backgroundColor: null,
            color: color1,
            hoverOpacity: 0.7,
            selectedColor: color2,
            enableZoom: true,
            showTooltip: true,
            values: data,
            scaleColors: [color1, color2],
            normalizeFunction: 'polynomial'
        });
    }

    App.dataTables = () => {

        //We use this to apply style to certain elements
        $.extend(true, $.fn.dataTable.defaults, {
            dom: "<'row be-datatable-header'<'col-sm-6'l><'col-sm-6'f>>" +
                "<'row be-datatable-body'<'col-sm-12'tr>>" +
                "<'row be-datatable-footer'<'col-sm-5'i><'col-sm-7'p>>"
        });

        //Remove search & paging dropdown
        $("#history-data-table").dataTable({
            pageLength: 5,
            dom: "<'row be-datatable-body'<'col-sm-12'tr>>" +
                "<'row be-datatable-footer'<'col-sm-5'i><'col-sm-7'p>>"
        });

    }

    App.firstTimeLoad = () => {
        var alertDivElem = document.getElementById('alert-div');
        var xhrAlertDiv = new XMLHttpRequest();
        xhrAlertDiv.addEventListener('load', function () {
            alertDivElem.innerHTML += this.responseText;
            this.abort();
        });
        xhrAlertDiv.open('GET', App.config.AJAX_ALERT_DIV_URI);
        xhrAlertDiv.send();
    }

    App.ajaxApply = () => {
        var xhrAlertDiv = new XMLHttpRequest();
        var alertDivElem = document.getElementById('alert-div');
        xhrAlertDiv.addEventListener('load', function () {
            alertDivElem.innerHTML += this.responseText;
            this.abort();
        });
        setInterval(() => {
            xhrAlertDiv.open('GET', App.config.AJAX_ALERT_DIV_URI);
            xhrAlertDiv.send();
        }, 10000);
    }

    App.widgetTooltipPosition = function (id, top) {
        $('#' + id).bind("plothover", function (event, pos, item) {
            var widthToolTip = $('.tooltip-chart').width();
            if (item) {
                $(".tooltip-chart")
                    .css({
                        top: item.pageY - top,
                        left: item.pageX - (widthToolTip / 2)
                    })
                    .fadeIn(200);
            } else {
                $(".tooltip-chart").hide();
            }
        });
    }

    App.dashboard = () => {
        App.toggleLoadingButton();
        App.toggleCloseButton();

        App.counter();
        App.sparkline();
        App.dataTables();
        App.map();

        App.firstTimeLoad();
    }

    return App;
})(App || {});

$(document).ready(() => {
    App.init();
    App.dashboard();
    App.ajaxApply();
});