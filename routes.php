<?php
/*
============================================
        Routes
============================================
*/

use App\Controllers\AnalyticsController;

$analytics = new AnalyticsController();

Flight::route('/', function () {
    echo 'Hello';
});

Flight::route('/analytics', array($analytics, 'analytics'));

