var App = (() => {
    'use strict';

    App.ajax_apply = function () {
        setInterval(() => {
            $('#alert-div').load('api/internal/ajax-alert-div.php');
        }, 5000);
    }

    return App;
})(App || {});