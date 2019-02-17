var App = (() => {
    'use strict';

    App.config = {
        AJAXURI_ALERT_DIV: 'api/internal/ajax-alert-div.php',
        AJAXURI_SPARKLINE: 'api/internal/ajax-sparkline-data.php',
        AJAXTYPE_COMMITS_SPARKLINE: 0,
        AJAXTYPE_ALERTS_SPARKLINE: 1
    };


    App.counter = function () {
        //FIXME: Prettify vars init steps.
        $('[data-toggle="counter"]').each(function (_index, _element) {
            var elem = $(this);
            var prefix = '';
            var suffix = '';
            var start = 0;
            var end = 0;
            var decimals = 0;
            var duration = 2.5;

            prefix = !!elem.data('prefix') ? elem.data('prefix') : prefix;
            suffix = !!elem.data('suffix') ? elem.data('suffix') : suffix;
            start = !!elem.data('start') ? elem.data('start') : start;
            end = !!elem.data('end') ? elem.data('end') : end;
            decimals = !!elem.data('decimals') ? elem.data('decimals') : decimals;
            duration = !!elem.data('duration') ? elem.data('duration') : duration;

            var count = new CountUp(elem.get(0), start, end, decimals, duration, {
                suffix: suffix,
                prefix: prefix,
            });
            count.start();
        });
    }

    App.toggleLoadingButton = function () {
        //TODO: Prevent the use of jQuery. Use DOM-based funcs instead.
        $('.toggle-loading').on('click', function () {
            var parent = $(this).parents('.widget, .panel');
            if (parent.length) {
                parent.addClass('be-loading-active');

                //TODO: Add more accurate loader
                setTimeout(function () {
                    parent.removeClass('be-loading-active');
                }, 300);
            }
        });
    }

    App.toggleCloseButton = function () {
        //TODO: Prevent the use of jQuery. Use DOM-based funcs instead.
        $('.toggle-close').on('click', function () {
            var parent = $(this).parents('.card, .widget, .panel');
            if (parent.length) {
                parent.fadeOut();
            }
        });
    }

    App.sparkline = function () {
        //TODO: VERY UGLY ALGORITHM. Do some clean-up.
        var colorPrimary = App.color.primary;
        var colorWarning = App.color.warning;
        var colorSuccess = App.color.success;
        var colorDanger = App.color.danger;

        App.ajaxGetSparkline(
            App.config.AJAXTYPE_COMMITS_SPARKLINE,
            '#all-commits-count-sparkline', {
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

        App.ajaxGetSparkline(
            App.config.AJAXTYPE_ALERTS_SPARKLINE,
            '#all-alerts-count-sparkline', {
                type: 'bar',
                width: '85',
                height: '35',
                barWidth: 4,
                barSpacing: 3,
                chartRangeMin: 0,
                barColor: colorSuccess
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

    App.map = function () {

        //TODO: Make the map into the real map of greenhouse

        var blueLighten2 = tinycolor(App.color.primary).lighten(15).toHexString();
        var blueLighten = tinycolor(App.color.primary).lighten(8).toHexString();
        var blue = tinycolor(App.color.primary).toHexString();

        //Highlight data
        //TODO: Data weights can be shown here using AJAX
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
            color: blueLighten2,
            hoverOpacity: 0.7,
            selectedColor: blueLighten,
            enableZoom: true,
            showTooltip: true,
            values: data,
            scaleColors: [blueLighten2, blueLighten],
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
        //TODO: VERY UGLY ALGORITHM. Do some clean-up.
        var alertDivElem = document.getElementById('alert-div');
        var xhrAlertDiv = new XMLHttpRequest();
        xhrAlertDiv.addEventListener('load', function () {
            alertDivElem.innerHTML += this.responseText;
            this.abort();
        });
        xhrAlertDiv.open('GET', App.config.AJAXURI_ALERT_DIV);
        xhrAlertDiv.send();
    }

    App.ajaxApply = () => {
        //TODO: VERY UGLY ALGORITHM. Do some clean-up.
        var xhrAlertDiv = new XMLHttpRequest();
        var alertDivElem = document.getElementById('alert-div');
        xhrAlertDiv.addEventListener('load', function () {
            alertDivElem.innerHTML = this.responseText;
            this.abort();
        });
        setInterval(() => {
            xhrAlertDiv.open('GET', App.config.AJAXURI_ALERT_DIV);
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

    App.ajaxGetSparkline = (
        data_type,
        elem_selector,
        painting_options,
        on_done =
        (data, txtStatus, $xhr) => {
            var flatten = JSON.parse(data).flat();
            $(elem_selector).sparkline(flatten, painting_options);
        },
        on_fail =
        ($xhr, txtStatus, err) => {
            console.error(err);
        }
    ) => {
        $.post({
                url: App.config.AJAXURI_SPARKLINE,
                data: {
                    data_type: data_type
                },
                //HACK: Use directive text input to stringify it later
                dataType: 'text'
            })
            .done(on_done)
            .fail(on_fail);
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