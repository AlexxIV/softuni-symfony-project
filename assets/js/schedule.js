exports.default = (() => {
    const schedule = $('#editable-schedule');

    $('#main-wrapper').on('click', '#schedule-edit', function (e) {
        e.preventDefault();

        let scheduleClone = schedule.clone();
        let tableData = scheduleClone.find('.single-subject');
        tableData.each(function (index, element) {
           $(element).attr('contenteditable', 'true').addClass('editing');
        });

        schedule.parent().html(scheduleClone);

        tableData[0].focus();

        $('#schedule-save, #schedule-save-cancel').show();
        $('#schedule-edit').hide();
    });

    $('#main-wrapper').on('click', '#schedule-save-cancel', function (e) {
        e.preventDefault();

        $('#schedule-save, #schedule-save-cancel').hide();

        let container = $('#editable-schedule').parent();

        container.html(schedule);

        $('#schedule-edit').show();
    });


})();