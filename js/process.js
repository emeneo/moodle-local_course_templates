var can_process = true;

window.onload = function () {
    require(
        ['jquery', 'core/notification'], function ($, Notification) {
            $('#btnProcess').click(
                function () {
                    if (!can_process) {
                        return false;
                    }

                    let formerrormessage = $('#course_templates_validation_error_message');
                    let coursename = $('#course_name');
                    let coursenamemessage = $('#course_name_message');
                    let courseshortname = $('#course_short_name');
                    let courseshortnamemessage = $('#course_short_name_message');
                    let formvalidation = true;

                    if (!coursename.val()) {
                        coursename.css('border-color', 'red');

                        if (!coursenamemessage.length) {
                            coursename.before('<div id="course_name_message" class="for-control-feedback invalid-feedback" style="display: block;">   - '
                                + formerrormessage.attr('data-validation-message')
                                + '</div>'
                            );
                        }

                        formvalidation = false;
                    } else {
                        coursename.css('border-color', 'black');

                        if (coursenamemessage.length) {
                            coursenamemessage.remove();
                        }
                    }

                    if (!courseshortname.val()) {
                        courseshortname.css('border-color', 'red');

                        if (!courseshortnamemessage.length) {
                            courseshortname.before('<span id="course_short_name_message" class="for-control-feedback invalid-feedback" style="display: block;">   - '
                                + formerrormessage.attr('data-validation-message')
                                + '</span>'
                            );
                        }

                        formvalidation = false;
                    } else {
                        courseshortname.css('border-color', 'black');

                        if (courseshortnamemessage.length) {
                            courseshortnamemessage.remove();
                        }
                    }

                    if (!formvalidation) {
                        return false;
                    }

                    can_process = false;

                    var post_data = "course_short_name=" + $('#course_short_name').val() + "&course_name=" + $('#course_name').val();
                    var start_datetime = $('#start_datetime_h').val() + ':' + $('#start_datetime_m').val();
                    var end_datetime = $('#end_datetime_h').val() + ':' + $('#end_datetime_m').val();
                    //if($('#start_datetime').length >0){
                    post_data = post_data + '&start_datetime=' + start_datetime;
                    //}
                    //if($('#end_datetime').length >0){
                    post_data = post_data + '&end_datetime=' + end_datetime;
                    //}
                    if($('#location').length >0) {
                        post_data = post_data + '&location=' +  $('#location').val();
                    }
                    post_data = post_data + '&course_date=' + $('#course_date').val();

                    $.ajax(
                        {
                            type: "post",
                            url: $('#process_request_url').val(),
                            data: post_data,
                            dataType: "json",
                            beforeSend: function () {
                                $('#btnProcess').parent().children().hide();
                                $('#btnProcess').after('<div id="local-course-templates-throbber"></div>');
                            },
                            success: function (data, textStatus) {
                                if (
                                    typeof data !== 'undefined'
                                    && data.status == 1
                                ) {
                                    $('#local-course-templates-throbber').hide();
                                    if ($('#jump_to').val() == 1) {
                                        window.location.href = $('#success_returnurl').val() + "/course/view.php?id=" + data.id;
                                    } else {
                                        window.location.href = $('#success_returnurl').val() + "/course/edit.php?id=" + data.id;
                                    }
                                } else {
                                    Notification.fetchNotifications();
                                    $('#local-course-templates-throbber').remove();
                                    $('#btnProcess').parent().children().show();
                                }

                                can_process = true;
                            },
                            error: function (request, status, error) {
                                if (typeof request.responseText !== 'undefined') {
                                    var jsonPos, jsonString, data;

                                    jsonPos = request.responseText.indexOf('{"status":');

                                    if (jsonPos !== -1) {
                                        jsonString = request.responseText.substr(jsonPos);

                                        try {
                                            data = JSON.parse(jsonString);
                                        } catch(e) {
                                            $('#local-course-templates-throbber').remove();

                                            window.location.href = $('#success_returnurl').val()
                                                + "/course/management.php";
                                        }

                                        if (
                                            typeof data !== 'undefined'
                                            && data.status == 1
                                        ) {
                                            $('#local-course-templates-throbber').hide();

                                            if ($('#jump_to').val() == 1) {
                                                window.location.href = $('#success_returnurl').val()
                                                    + "/course/view.php?id=" + data.id;
                                            } else {
                                                window.location.href = $('#success_returnurl').val()
                                                    + "/course/edit.php?id=" + data.id;
                                            }
                                        } else {
                                            $('#local-course-templates-throbber').remove();

                                            window.location.href = $('#success_returnurl').val()
                                                + "/course/management.php";
                                        }
                                    } else {
                                        $('#local-course-templates-throbber').remove();

                                        window.location.href = $('#success_returnurl').val()
                                            + "/course/management.php";
                                    }
                                } else {
                                    $('#local-course-templates-throbber').remove();

                                    window.location.href = $('#success_returnurl').val()
                                        + "/course/management.php";
                                }

                                can_process = true;
                            }
                        }
                    );
                }
            );
        }
    );
};