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

    // $('#main-wrapper').on('click', 'input#user_registerTeacher', function (e) {
    //     if ($(e.target).is(':checked')) {
    //             $('#user_grade').parent().hide();
    //     } else {
    //         $('#user_grade').parent().show();
    //     }
    // });

    $(document).ready(function () {
        let regForm = $('#register-form');
        let errors = regForm.find('.is-invalid');

        if(errors.length > 0) {
            let teacher = regForm.find('#user_registerTeacher').val();
            switch (teacher) {
                case '1':
                    $('#main-wrapper').find('#teacher-register').addClass('out').hide();
                    regForm.find('#user_studentClass').val('').parent().hide();
                    regForm.show();
                    break;
                case '0':
                    $('#main-wrapper').find('#student-register').addClass('out').hide();
                    regForm.fadeIn();
                    break;
                default: return;
            }
        }

    })
})();