var App = (() => {
    'use strict';

    App.ajax_apply = function () {
        setInterval(() => {
            $.get('api/internal/ajax-history-data-table.php', (result) => {
                $('#history-data-table-tbody').html = result;
                console.log("ajax applied");
            });
        }, 1000);
    }

    return App;
})(App || {});