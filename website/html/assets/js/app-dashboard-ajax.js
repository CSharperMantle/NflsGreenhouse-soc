var App = (() => {
    'use strict';

    App.ajax_apply = function () {
        setInterval(() => {
            /*$.get('api/internal/ajax-history-data-table.php', (result) => {
                $('#history-data-table-tbody').html = result;
                console.log(result);
            });*/
            //$('#history-data-table-tbody').load('api/internal/ajax-history-data-table.php');
            $('#alert-div').load('api/internal/ajax-alert-div.php');
            console.log('ajaxed');
        }, 1000);
    }

    return App;
})(App || {});