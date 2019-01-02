exports.default = (() => {

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

    $('#main-wrapper').on('click', '.grade-edit', function (e) {
        e.preventDefault();

        let editUrl = $(e.target).attr('href');
        let gradeRow = $(e.target).parent().parent();
        let gradeName = gradeRow.find('.grade-name').text().trim();
        let gradeValue = gradeRow.find('.grade-value').text().trim();
        let gradeNotes = gradeRow.find('.grade-notes').text().trim();

        let editForm =
            '<div class="row m-3">' +
            '<div class="col-12">' +
            '<form class="form-inline" id="grade-edit-form">' +
            '<div class="form-group col-3"><input class="form-control" type="text" name="gradeName" value="' + gradeName + '"/></div>' +
            '<div class="form-group col-3"><input class="form-control" type="text" name="gradeValue" value="' + gradeValue + '"/></div>' +
            '<div class="form-group col-3"><input class="form-control" type="text" name="gradeNotes" value="' + gradeNotes + '"/></div>' +
            '<div class="form-group col-3">' +
            '<button class="btn-success btn" type="submit">Save</button>' +
            '<button class="btn-light btn grade-edit-cancel ml-1">Cancel</button>' +
            '</div>' +
            '<input type="hidden" id="edit-url" value="' + editUrl + '">' +
        '</form>' +
        '</div>' +
        '</div>';

        gradeRow.after(editForm);
        gradeRow.hide();
    });

    $('#main-wrapper').on('submit', '#grade-edit-form', function (e) {
        e.preventDefault();
        let url = $(this).find('#edit-url').val();

        let form = $(this);

        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(), // serializes the form's elements.
            success: function (data) {
                let activeRow = ($(e.target).closest('.row'));
                let editedRow = activeRow.prev();

                editedRow.find('.grade-name').text(data.newName);
                editedRow.find('.grade-value').text(data.newValue);
                editedRow.find('.grade-notes').text(data.newNotes);

                activeRow.remove();
                editedRow.show();

                $('#main').prepend("<div class='ajax-message text-center alert alert-success'>" + data.message + "</div>");
                setTimeout(function () {
                    $('.ajax-message').fadeOut().remove();
                }, 3000);
            },
            error: function (data) {
                let errorMessage = (data.responseJSON.message);
                $('#main').prepend("<div class='ajax-message text-center alert alert-danger'>" + errorMessage + "</div>");
                setTimeout(function () {
                    $('.ajax-message').fadeOut().remove();
                }, 3000);
            }
        });
    });

    $('#main-wrapper').on('click', 'button.grade-edit-cancel', function (e) {
        let activeRow = ($(e.target).closest('.row'));
        activeRow.prev().show();
        activeRow.remove();
        $('#grade-add').show();
    });

    $('#main-wrapper').on('click', 'a#grade-add', function (e) {
        e.preventDefault();
        $(e.target).hide();
        let addUrl = $(e.target).attr('href');
        let gradeRow = $(e.target).parent();

        let editForm =
            '<div class="row m-3">' +
            '<div class="col-12">' +
            '<form class="form-inline" id="grade-add-form">' +
            '<div class="form-group col-3">' +
            '<label for="grade-name">Course*</label>' +
            '<input id="grade-name" class="form-control" type="text" name="gradeName"/>' +
            '</div>' +
            '<div class="form-group col-3">' +
            '<label for="grade-value">Value*</label>' +
            '<input id="grade-value" class="form-control" type="text" name="gradeValue"/>' +
            '</div>' +
            '<div class="form-group col-3">' +
            '<label for="grade-notes">Notes</label>' +
            '<input id="grade-notes" class="form-control" type="text" name="gradeNotes"/>' +
            '</div>' +
            '<div class="form-group col-3">' +
            '<button class="btn-primary btn" type="submit">Add</button>' +
            '<button class="btn-light btn grade-edit-cancel ml-1">Cancel</button>' +
            '</div>' +
            '<input type="hidden" id="add-url" value="' + addUrl + '">' +
        '</form>' +
        '</div>' +
        '</div>';

        gradeRow.prepend(editForm);
    });

    $('#main-wrapper').on('submit', '#grade-add-form', function (e) {
        e.preventDefault();
        let url = $(this).find('#add-url').val();
        console.log(url);
        let form = $(this);

        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(), // serializes the form's elements.
            success: function (data) {
                let activeRow = ($(e.target).closest('.row'));
                let newRowParent = activeRow.parent().prev();

                let newRow =
                    '<div class="row m-3">' +
                        '<div class="col-3 grade-name">' +
                            data.newName +
                        '</div>' +
                        '<div class="col-3 grade-value">' +
                            data.newGradeValue +
                        '</div>' +
                        '<div class="col-3 grade-notes">' +
                            data.newNotes +
                        '</div>' +
                        '<div class="col-3 grade-actions">' +
                            '<a href="/teacher/student/grades/edit/' + data.newId + '" class="btn-success btn grade-edit" id="btn-edit-' + data.newId + '">Edit</a>' +
                            '<a href="/teacher/student/grades/delete/' + data.newId + '" class="btn-danger btn grade-delete ml-1" id="btn-delete-' + data.newId + '">Delete</a>' +
                        '</div>' +
                    '</div>';

                newRowParent.append(newRow);
                activeRow.hide();


                $('#main').prepend("<div class='ajax-message text-center alert alert-success'>" + data.message + "</div>");
                setTimeout(function () {
                    $('.ajax-message').fadeOut().remove();
                }, 3000);

                $('#grade-add').show();
            },
            error: function (data) {
                let errorMessage = (data.responseJSON.message);
                $('#main').prepend("<div class='ajax-message text-center alert alert-danger'>" + errorMessage + "</div>");
                setTimeout(function () {
                    $('.ajax-message').fadeOut().remove();
                }, 3000);
            }
        });
    });

})();
