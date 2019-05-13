var App = (() => {
    'use strict';

    App.config = {
        AJAXURI_ALERT_DIV: 'api/internal/ajax-alert-div.php',
        AJAXURI_SPARKLINE: 'api/internal/ajax-sparkline-data.php',
        AJAXURI_HISTORY_ACTIONS: 'api/internal/ajax-history-action-list.php',
        AJAXURI_HISTORY_DATA_TABLE_BODY: 'api/internal/ajax-history-data-table-body.php',
        AJAXURI_NUMERIC: 'api/internal/ajax-numeric.php',

        AJAXTYPE_COMMITS_SPARKLINE: 0,
        AJAXTYPE_ALERTS_SPARKLINE: 1,
        AJAXTYPE_TOTAL_COMMITS: 0,
        AJAXTYPE_TOTAL_ALERTS: 1,

        ELEMSELECTOR_COMMITS_SPARKLINE: '#all-commits-count-sparkline',
        ELEMSELECTOR_ALERTS_SPARKLINE: '#all-alerts-count-sparkline',
        ELEMSELECTOR_HISTORY_ACTION: '#history-actions',
        ELEMSELECTOR_HISTORY_DATA_TABLE_BODY: '#history-data-table-body',
        ELEMSELECTOR_TOTAL_COMMITS: '#total-commits',
        ELEMSELECTOR_TOTAL_ALERTS: '#total-alerts',
        ELEMSELECTOR_TOGGLE_LOADING: '.toggle-loading',
        ELEMSELECTOR_TOGGLE_CLOSE: '.toggle-close',
        ELEMSELECTOR_UPDATED_DATETIME: '.updated-datetime',

        SPARKLINECFG_COMMITS: {
            width: '85',
            height: '35',
            lineColor: App.color.primary,
            highlightSpotColor: App.color.primary,
            highlightLineColor: App.color.primary,
            fillColor: false,
            spotColor: false,
            minSpotColor: false,
            maxSpotColor: false,
            lineWidth: 1.15
        },
        SPARKLINECFG_ALERTS: {
            type: 'bar',
            width: '85',
            height: '35',
            barWidth: 4,
            barSpacing: 3,
            chartRangeMin: 0,
            barColor: App.color.success
        }
    };


    App.counter = function () {
        $('[data-toggle="counter"]').each(function (index, $element) {
            let elem = $(this);
            let prefix = '';
            let suffix = '';
            let start = 0;
            let end = 0;
            let decimals = 0;
            let duration = 2.5;

            prefix = !!elem.data('prefix') ? elem.data('prefix') : prefix;
            suffix = !!elem.data('suffix') ? elem.data('suffix') : suffix;
            start = !!elem.data('start') ? elem.data('start') : start;
            end = !!elem.data('end') ? elem.data('end') : end;
            decimals = !!elem.data('decimals') ? elem.data('decimals') : decimals;
            duration = !!elem.data('duration') ? elem.data('duration') : duration;

            let count = new CountUp(elem.get(0), start, end, decimals, duration, {
                suffix: suffix,
                prefix: prefix,
            });
            count.start();
        });
    }

    App.toggleLoadingButton = function () {
        //TODO: Prevent the use of jQuery. Use DOM-based funcs instead.
        $(App.config.ELEMSELECTOR_TOGGLE_LOADING).on('click', function () {
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
        $(App.config.ELEMSELECTOR_TOGGLE_CLOSE).on('click', function () {
            let parent = $(this).parents('.card, .widget, .panel');
            if (parent.length) {
                parent.fadeOut();
            }
        });
    }

    App.numericData = () => {
        $(App.config.ELEMSELECTOR_UPDATED_DATETIME).html(new Date().toLocaleString());

        $.post({
                url: App.config.AJAXURI_NUMERIC,
                data: {
                    data_type: App.config.AJAXTYPE_TOTAL_COMMITS
                },
                //HACK: Use directive text input to stringify it later
                dataType: 'text'
            })
            .done((data, txtStatus, $xhr) => {
                $(App.config.ELEMSELECTOR_TOTAL_COMMITS).html(data);
            })
            .fail(($xhr, txtStatus, err) => {
                console.error(err);
            });

        $.post({
                url: App.config.AJAXURI_NUMERIC,
                data: {
                    data_type: App.config.AJAXTYPE_TOTAL_ALERTS
                },
                //HACK: Use directive text input to stringify it later
                dataType: 'text'
            })
            .done((data, txtStatus, $xhr) => {
                $(App.config.ELEMSELECTOR_TOTAL_ALERTS).html(data);
            })
            .fail(($xhr, txtStatus, err) => {
                console.error(err);
            });
    }

    App.sparkline = () => {
        App.ajaxGetSparkline(
            App.config.AJAXTYPE_COMMITS_SPARKLINE,
            App.config.ELEMSELECTOR_COMMITS_SPARKLINE,
            App.config.SPARKLINECFG_COMMITS);

        App.ajaxGetSparkline(
            App.config.AJAXTYPE_ALERTS_SPARKLINE,
            App.config.ELEMSELECTOR_ALERTS_SPARKLINE,
            App.config.SPARKLINECFG_ALERTS);
    }

    App.historyActions = () => {
        $.get({
                url: App.config.AJAXURI_HISTORY_ACTIONS,
                dataType: 'text'
            })
            .done((data, txtStatus, $xhr) => {
                $(App.config.ELEMSELECTOR_HISTORY_ACTION).html(data);
            })
            .fail(($xhr, txtStatus, err) => {
                console.error(err);
            });
    }

    App.map = function () {

        //TODO: Make the map into the real map of greenhouse

        let blueLighten2 = tinycolor(App.color.primary).lighten(15).toHexString();
        let blueLighten = tinycolor(App.color.primary).lighten(8).toHexString();
        let blue = tinycolor(App.color.primary).toHexString();

        //Highlight data
        //TODO: Data weights can be shown here using AJAX
        let data = {
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

        $.get({
                url: App.config.AJAXURI_HISTORY_DATA_TABLE_BODY,
                dataType: 'text'
            })
            .done((data, txtStatus, $xhr) => {
                $(App.config.ELEMSELECTOR_HISTORY_DATA_TABLE_BODY).html(data);
                $("#history-data-table").dataTable({
                    pageLength: 5,
                    dom: "<'row be-datatable-body'<'col-sm-12'tr>>" +
                        "<'row be-datatable-footer'<'col-sm-5'i><'col-sm-7'p>>"
                });
            })
            .fail(($xhr, txtStatus, err) => {
                console.error(err);
            });
    }

    App.firstTimeLoad = () => {
        //TODO: VERY UGLY ALGORITHM. Do some clean-up.
        App.toggleLoadingButton();
        App.toggleCloseButton();

        App.numericData();
        App.counter();
        App.sparkline();
        App.historyActions();
        App.dataTables();
        App.map();

        var alertDivElem = document.getElementById('alert-div');
        var xhrAlertDiv = new XMLHttpRequest();
        xhrAlertDiv.addEventListener('load', function () {
            alertDivElem.innerHTML += this.responseText;
            this.abort();
        });
        xhrAlertDiv.open('GET', App.config.AJAXURI_ALERT_DIV);
        xhrAlertDiv.send();
    }

    App.refreshAlertDivTimely = () => {
        //TODO: VERY UGLY ALGORITHM. Do some clean-up.
        let xhrAlertDiv = new XMLHttpRequest();
        let alertDivElem = document.getElementById('alert-div');
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
            let widthToolTip = $('.tooltip-chart').width();
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

    //TODO: Simplify this function -> App.doAjax(ajax_type, url, data, dataType, on_done=func, on_fail=func)
    App.ajaxGetSparkline = (
        data_type,
        elem_selector,
        painting_options,
        on_done =
        (data, txtStatus, $xhr) => {
            let flatten = JSON.parse(data).flat();
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
        App.firstTimeLoad();
    }

    return App;
})(App || {});

$(document).ready(() => {
    App.init();
    App.dashboard();
    App.refreshAlertDivTimely();
});