exports.default = (() => {

    // Delete selected grade
    $('#main-wrapper').on('click', '.grade-delete', function (e) {
        e.preventDefault();
        let url = $(this).attr('href');
        console.log();
        $.ajax(url)
            .done(function (data) {
                $(e.target).parent().parent().remove();
                $('#main').prepend("<div class='ajax-message text-center alert alert-success'>" + data.message + "</div>");
                setTimeout(function () {
                    $('.ajax-message').fadeOut().remove();
                }, 3000);
            })
            .fail(function () {
                $('#main').prepend("<div class='ajax-message text-center alert alert-danger'>An error occurred</div>");
                setTimeout(function () {
                    $('.ajax-message').fadeOut().remove();
                }, 3000);
            });
    });

    // Initial edit click
    $('#main-wrapper').on('click', '.grade-edit', function (e) {
        e.preventDefault();

        let backupRow = $(e.target).parent().parent().clone();
        backupRow.addClass('backupRow');

        let editUrl = $(e.target).attr('href');
        let gradeRow = $(e.target).parent().parent();
        gradeRow.after(backupRow.hide());
        let newBtns = '<a class="btn-success btn btn-edit-grade temp-btn" href="' + editUrl + '" >Save</a>' +
                      '<button class="btn-danger btn grade-edit-cancel ml-1 temp-btn">Cancel</button>';
        let editFields = gradeRow
            .find('td:not(.text-center)')
            .each(function (index, element) {
                $(element)
                    .attr('contenteditable', 'true')
                    .addClass('editing');
            });
        editFields[0].focus();

        let actionBtns = gradeRow.find('td.text-center');
        actionBtns.children().hide();
        actionBtns.append(newBtns);
    });

    // Submit edit
    $('#main-wrapper').on('click', '.btn-edit-grade', function (e) {
        e.preventDefault();
        let editUrl = $(e.target).attr('href');
        let gradeRow = $(e.target).parent().parent();
        let editFields = gradeRow.find('td:not(.text-center)');
        let form = createForm([$(editFields[0]).text(), $(editFields[1]).text(), $(editFields[2]).text()]);

        $.ajax({
            type: "POST",
            url: editUrl,
            data: $(form).serialize(), // serializes the form's elements.
            success: function (data) {
                form.remove();
                $(editFields[0]).text(data.newName).attr('contenteditable', 'false').removeClass('editing');
                $(editFields[1]).text(data.newValue).attr('contenteditable', 'false').removeClass('editing');
                $(editFields[2]).text(data.newNotes).attr('contenteditable', 'false').removeClass('editing');

                $(editFields).each(function (index, element) {
                    $(element)
                        .attr('contenteditable', 'false')
                        .removeClass('editing');
                });

                let btns = gradeRow.find('td.text-center .temp-btn');
                btns.remove();

                gradeRow.find('td.text-center').children().show();

                $('#main').prepend("<div class='ajax-message text-center alert alert-success'>" + data.message + "</div>");
                setTimeout(function () {
                    $('.ajax-message').fadeOut().remove();
                }, 4000);

            },
            error: function (data) {
                form.remove();
                $(editFields[1]).focus();
                let errorMessage = (data.responseJSON.message);
                $('#main').prepend("<div class='ajax-message text-center alert alert-danger'>" + errorMessage + "</div>");
                setTimeout(function () {
                    $('.ajax-message').fadeOut().remove();
                }, 5000);
            }
        });
    });

    // Cancel edit
    $('#main-wrapper').on('click', 'button.grade-edit-cancel', function (e) {
        let activeRow = $(e.target).parent().parent();
        if ($(this).hasClass('cancel-adding')) {
            let editFields = activeRow
                .find('td:not(.text-center)')
                .each(function (index, element) {
                    $(element)
                        .attr('contenteditable', 'false')
                        .removeClass('editing')
                        .text('');
                });
        } else {
            activeRow.next().show();
            activeRow.remove();
            let editFields = activeRow
                .find('td:not(.text-center)')
                .each(function (index, element) {
                    $(element)
                        .attr('contenteditable', 'false')
                        .removeClass('editing');
                });
        }

        let btns = activeRow.find('td.text-center .temp-btn');
        btns.remove();

        activeRow.find('td.text-center').children().show();
    });

    // Add grade
    $('#main-wrapper').on('click', 'a#grade-add', function (e) {
        e.preventDefault();
        let addUrl = $(e.target).attr('href');
        let currentRow = $(e.target).parent().parent();
        if (currentRow.parent().find('.empty-row').length > 0) {
            currentRow.parent().find('.empty-row').remove();
        }
        let emptyRow = currentRow.clone();
        emptyRow.addClass('empty-row d-none');
        currentRow.parent().append(emptyRow);

        let newBtns = '<a class="btn-success btn btn-add-grade temp-btn" href="' + addUrl + '" >Save</a>' +
                      '<button class="btn-danger btn grade-edit-cancel cancel-adding ml-1 temp-btn">Cancel</button>';

        let addFields = currentRow
            .find('td:not(.text-center)')
            .each(function (index, element) {
                $(element)
                    .attr('contenteditable', 'true')
                    .addClass('adding');
            });

        addFields[0].focus();

        let actionBtns = currentRow.find('td.text-center');
        actionBtns.children().hide();
        actionBtns.append(newBtns);
    });

    // Submit add
    $('#main-wrapper').on('click', '.btn-add-grade', function (e) {
        e.preventDefault();
        let addUrl = $(e.target).attr('href');
        let gradeRow = $(e.target).parent().parent();
        let addFields = gradeRow.find('td:not(.text-center)');
        let form = createForm([$(addFields[0]).text(), $(addFields[1]).text(), $(addFields[2]).text()]);

        $.ajax({
            type: "POST",
            url: addUrl,
            data: $(form).serialize(),
            success: function (data) {
                form.remove();
                $(addFields[0]).text(data.newName).attr('contenteditable', 'false').removeClass('editing');
                $(addFields[1]).text(data.newValue).attr('contenteditable', 'false').removeClass('editing');
                $(addFields[2]).text(data.newNotes).attr('contenteditable', 'false').removeClass('editing');

                $(addFields).each(function (index, element) {
                    $(element)
                        .attr('contenteditable', 'false')
                        .removeClass('adding');
                });

                let tempBtns = gradeRow.find('td.text-center .temp-btn');
                let newBtns = '<a class="btn-success btn grade-edit" id="btn-edit-' + data.newId + '" href="/teacher/student/grades/edit/' + data.newId + '" >Edit</a>' +
                              '<a class="btn-danger btn grade-delete ml-1" id="btn-delete-' + data.newId + '"href="/teacher/student/grades/delete/' + data.newId + '">Delete</a>';
                tempBtns.remove();

                gradeRow.parent().find('.empty-row').removeClass('d-none').removeClass('empty-row');
                // let addBtn = gradeRow.find('#grade-add');
                // let newRow = gradeRow.clone();
                // console.log(addBtn);
                // console.log(newRow);
                // console.log(emptyGradeRow);
                //let newRow = '<tr><td></td><td></td><td></td><td class="text-center"></td></tr>';
                // gradeRow.parent().append(newRow);
                gradeRow.find('td.text-center').append(newBtns);

                $('#main').prepend("<div class='ajax-message text-center alert alert-success'>" + data.message + "</div>");
                setTimeout(function () {
                    $('.ajax-message').fadeOut().remove();
                }, 4000);

            },
            error: function (data) {
                form.remove();
                $(addFields[1]).focus();
                let errorMessage = (data.responseJSON.message);
                $('#main').prepend("<div class='ajax-message text-center alert alert-danger'>" + errorMessage + "</div>");
                setTimeout(function () {
                    $('.ajax-message').fadeOut().remove();
                }, 5000);
            }
        })
    });

    function createForm([name, value, notes]) {
        let form = document.createElement("form");
        let element1 = document.createElement("input");
        element1.name = "gradeName";
        element1.value = name;
        element1.type = 'text';
        form.appendChild(element1);
        let element2 = document.createElement("input");
        element2.name = "gradeValue";
        element2.value = value;
        element2.type = 'text';
        form.appendChild(element2);
        let element3 = document.createElement("input");
        element3.name = "gradeNotes";
        element3.value = notes;
        element3.type = 'text';
        form.appendChild(element3);

        return form;
    }


})();


