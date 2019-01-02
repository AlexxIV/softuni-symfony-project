const $ = require('jquery');

import '../css/main.scss';

import 'bootstrap';

import './forms';
import './ajax-crud';

$("document").ready(function(){
    setTimeout(function(){
        $("div.alert").fadeOut().remove();
    }, 5000 ); // 5 secs
});


