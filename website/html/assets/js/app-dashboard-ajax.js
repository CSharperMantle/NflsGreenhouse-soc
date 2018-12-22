var App = (() => {
    'use strict';

    App.firstTimeLoad = () => {
        $('#alert-div').load('api/internal/ajax-alert-div.php');
    }

    App.ajaxApply = () => {
        setInterval(() => {
            $('#alert-div').load('api/internal/ajax-alert-div.php');
        }, 10000);
    }

    return App;
})(App || {});