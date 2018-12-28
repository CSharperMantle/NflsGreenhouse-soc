var App = (() => {
    'use strict';

    App.loadContent = () => {
        $('#air-temp-card-holder').load('api/internal/ajax-current-data-panel.php', {id: 0});
        $('#air-hum-card-holder').load('api/internal/ajax-current-data-panel.php', {id: 1});
        $('#air-light-card-holder').load('api/internal/ajax-current-data-panel.php', {id: 2});
        $('#ground-hum-card-holder').load('api/internal/ajax-current-data-panel.php', {id: 3});
    }

    App.ajaxApply = () => {
        setInterval(() => {
            loadContent();
        }, 10000);
    }

    return App;

})(App || {});