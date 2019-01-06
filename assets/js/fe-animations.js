exports.default = (() => {
    $('#btn-show-grades').on('click', function () {
        let content = $('.table-grades');
        let arrow = $(this).find('.fa-arrow-right');

        if (!content.hasClass("d-table")) {
            content.toggleClass("d-none d-table");
            arrow.addClass("rotated");
        } else if (content.hasClass("d-table")) {
            content.toggleClass("d-table d-none");
            arrow.removeClass("rotated");
        }
    });

    $('#student-register').on('click', function () {

        let regForm = $('#register-form');
        regForm.find('#user_grade').parent().show();
        regForm.find('#user_isTeacher').val(0);
        regForm.fadeIn();

        let teacherBtn = $('#teacher-register');

        if (teacherBtn.hasClass('out')) {
            teacherBtn.removeClass('out').fadeIn();
        }

        $(this).addClass('out').fadeOut();

    });

    $('#teacher-register').on('click', function () {
        let regForm = $('#register-form');
        regForm.find('#user_grade').parent().hide();
        regForm.find('#user_isTeacher').val(1);
        regForm.fadeIn();

        let studentBtn = $('#student-register');

        if (studentBtn.hasClass('out')) {
            studentBtn.removeClass('out').fadeIn();
        }

        $(this).addClass('out').fadeOut();
    });

    (function () {
            $('label[for=user_image]').parent().css({
                'padding-top': '32px'
            })
    })();
})();