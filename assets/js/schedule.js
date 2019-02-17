exports.default = (() => {
    let schedule = $('#editable-schedule');

    $('#main-wrapper').on('click', '#schedule-edit', function (e) {
        e.preventDefault();

        let scheduleClone = schedule.clone();
        let tableData = scheduleClone.find('.single-subject .row-value');

        tableData.each(function (index, element) {
           $(element).parent().addClass('editing');
           $(element).attr('contenteditable', 'true');
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

    $('#main-wrapper').on('click', '#schedule-save', function (e) {
        e.preventDefault();
        let url = $(this).attr('href');
        let readyDays = [];

        let days = $('#editable-schedule').find('.single-day');

        days.each(function (index, element) {
            let subjectsArray = [];
            let subjects = $(element).find('.single-subject');

            subjects.each(function(){

                let recordIdentifier = $(this).find('.row-index').text();
                recordIdentifier = recordIdentifier.split('.')[0];

                let recordValue = $(this).find('.row-value').text();

                let singleRecord = {[recordIdentifier]: recordValue};
                subjectsArray.push(singleRecord);
            });

            let readyDay = {
                name: $(element).attr('class').split(' ')[1].trim(),
                id: $(element).attr('id'),
                subjects: subjectsArray
            };

            readyDays.push(readyDay);
        });

        console.log(JSON.stringify(readyDays));

        $.ajax({
            type: "POST",
            url: url,
            // The key needs to match your method's input parameter (case-sensitive).
            data: JSON.stringify(readyDays),
            contentType: "application/json",
            dataType: "json",
            success: function(data){
                e.preventDefault();

                $('#schedule-save, #schedule-save-cancel').hide();

                let container = $('#editable-schedule').parent();

                let tableData = $('#editable-schedule').find('.single-subject .row-value');
                tableData.each(function (index, element) {
                    $(element).parent().removeClass('editing');
                    $(element).attr('contenteditable', 'false');
                });
                $('#schedule-edit').show();
                schedule = $('#editable-schedule');
                $('#main').prepend("<div class='ajax-message text-center alert alert-success'>" + data.message + "</div>");
                setTimeout(function () {
                    $('.ajax-message').fadeOut().remove();
                }, 4000);
                },
            error: function(errorMessage) {
                $('#main').prepend("<div class='ajax-message text-center alert alert-danger'>" + errorMessage + "</div>");
                setTimeout(function () {
                    $('.ajax-message').fadeOut().remove();
                }, 5000);
                console.log(errorMessage);
            }
        });
    });

})();