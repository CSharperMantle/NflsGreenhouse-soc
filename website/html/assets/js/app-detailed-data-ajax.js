var App = (() => {
    'use strict';

    App.ajaxApply = () => {
        setInterval(() => {
            $('#air-temp-card-holder').load('api/internal/ajax-current-data-panel.php', {id: 0});
            $('#air-hum-card-holder').load('api/internal/ajax-current-data-panel.php', {id: 1});
            $('#air-light-card-holder').load('api/internal/ajax-current-data-panel.php', {id: 2});
            $('#ground-hum-card-holder').load('api/internal/ajax-current-data-panel.php', {id: 3});
        }, 5000);
    }

    return App;

})(App || {});