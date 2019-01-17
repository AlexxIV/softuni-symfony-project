const $ = require('jquery');

import 'bootstrap';

import '@fortawesome/fontawesome-free/js/all';

import './forms';
import './ajax-crud';
import './fe-animations';
import './schedule';

import './admin/animations';

import '../css/main.scss';

$("document").ready(function(){
    let height = window.innerHeight;
    let headerHeight = $('#header').outerHeight(true);
    let footerHeight = $('#footer').outerHeight(true);

    height -= headerHeight + footerHeight;

    $('#main').height(height);

    setTimeout(function(){
        $("div.alert").fadeOut().remove();
    }, 5000 ); // 5 secs
});


