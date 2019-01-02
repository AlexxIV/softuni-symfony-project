exports.default = (() => {
    $('.btn-show-grades').on('click', function () {
        let content = $(this).closest('.row').next();
        let arrow = $(this).find('.fa-arrow-right');

        if (!content.hasClass("d-flex")) {
            content.toggleClass("d-none d-flex");
            arrow.addClass("rotated");
        } else if (content.hasClass("d-flex")) {
            content.toggleClass("d-flex d-none");
            arrow.removeClass("rotated");
        }
    })
})();