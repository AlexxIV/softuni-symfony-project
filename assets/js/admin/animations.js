exports.default = (() => {
    $(document).ready(function () {

        $('#sidebarCollapse').on('click', function () {
            $('#admin-sidebar').toggleClass('active');
        });

    });
})();