var App = (() => {
    'use strict';

    App.ajax_apply = function () {
        setInterval(() => {
            $.get('api/internal/ajax-history-data-table.php', (result) => {
                $('#history-data-table-tbody').html = result;
            });
        }, 1000);
    }

    return App;
})(App || {});