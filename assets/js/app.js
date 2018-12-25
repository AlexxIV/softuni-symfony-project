import '../css/main.scss';

const $ = require('jquery');

import 'bootstrap';

$(document).ready(function () {
    $('[data-toggle="popover"]').popover();
});