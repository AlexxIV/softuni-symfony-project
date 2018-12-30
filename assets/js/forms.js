exports.default = (() => {

    $('#main-wrapper').on('click', 'button#change_password_button', function () {
        $('#change_password_form').toggleClass('d-none d-block');
        $('#cancel_change_password_button').toggleClass('d-none d-inline');

        $(this).attr('id', 'change_password_submit');
        $(this).toggleClass('btn-link btn-primary')
    });

    $('#main-wrapper').on('click', 'button#change_password_submit', function (e) {
        e.preventDefault();
        $('#change_password_form').submit();
    });

    $('#main-wrapper').on('click', 'button#cancel_change_password_button', function (e) {
        e.preventDefault();
        $('#change_password_form').toggleClass('d-block d-none');

        $(this).toggleClass('d-inline d-none');
        $('#change_password_submit').toggleClass('btn-primary btn-link');
        $('#change_password_submit').attr('id', 'change_password_button');

    });

    $('#main-wrapper').on('click', 'input#user_isTeacher', function (e) {
        if ($(e.target).is(':checked')) {
                $('#user_grade').parent().hide();
        } else {
            $('#user_grade').parent().show();
        }
    })


})();