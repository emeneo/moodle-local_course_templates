var can_process = true;

window.onload = function(){
    require(['jquery'], function($) {
        $('#btnProcess').click(function(){
            if(!can_process) {
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
            if($('#location').length >0){
                post_data = post_data + '&location=' +  $('#location').val();
            }
            post_data = post_data + '&course_date=' + $('#course_date').val();
            
            $.ajax({
                type: "post",
                url: $('#process_request_url').val(),
                data: post_data,
                dataType: "json",
                beforeSend: function(){
                    $('#btnProcess').val('Creating...');
                },
                success: function(data, textStatus){
                    if(data.status == 1){
                        // ... window.location.href = $('#process_returnurl').val()+"&status=1&courseid="+data.id;
                        if($('#jump_to').val() == 1){
                            window.location.href = $('#success_returnurl').val() + "/course/view.php?id=" + data.id;
                        }else{
                            window.location.href = $('#success_returnurl').val() + "/course/edit.php?id=" + data.id;
                        }
                    }else{
                        window.location.href = $('#process_returnurl').val() + "&status=" + data.status + "&courseid=" + data.id + "&cateid=" + data.cateid;
                    }
                    $('#btnProcess').val('Continue');
                    can_process = true;
                }
            });
        })
    });
}