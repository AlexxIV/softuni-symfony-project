const $ = require('jquery');

import 'bootstrap';

import '@fortawesome/fontawesome-free/js/all';

import './forms';
import './ajax-crud';
import './fe-animations';

import '../css/main.scss';

$("document").ready(function(){
    setTimeout(function(){
        $("div.alert").fadeOut().remove();
    }, 5000 ); // 5 secs
});


