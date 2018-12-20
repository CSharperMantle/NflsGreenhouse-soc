var App = (() => {
    'use strict';

    App.ajaxApply = () => {
        setInterval(() => {
            $('#alert-div').load('api/internal/ajax-alert-div.php');
        }, 5000);
    }

    return App;
})(App || {});