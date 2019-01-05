exports.default = (() => {
    $('.btn-show-grades').on('click', function () {
        let content = $(this).closest('.row').parent().parent().next();
        let arrow = $(this).find('.fa-arrow-right');

        if (!content.hasClass("d-flex")) {
            content.toggleClass("d-none d-flex");
            arrow.addClass("rotated");
        } else if (content.hasClass("d-flex")) {
            content.toggleClass("d-flex d-none");
            arrow.removeClass("rotated");
        }
    })

    $('#student-register').on('click', function () {
        console.log('register as student');

        let regForm = $('#register-form');

    });

    $('#teacher-register').on('click', function () {
        console.log('register as teacher');
    });
})();