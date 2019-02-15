var App = (() => {
    'use strict';

    App.config = {
        AJAX_ALERT_DIV_PATH : 'api/internal/ajax-alert-div.php'
    };

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
        $('#alert-div').load(App.config.AJAX_ALERT_DIV_PATH);
    }

    App.ajaxApply = () => {
        setInterval(() => {
            $('#alert-div').load(App.config.AJAX_ALERT_DIV_PATH);
        }, 10000);
    }

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

    App.toggleLoader = function () {
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
    };

    return App;
})(App || {});

$(document).ready(() => {
    App.init();
    App.dashboard();
    App.firstTimeLoad();
    App.dataTables();
    App.ajaxApply();
});