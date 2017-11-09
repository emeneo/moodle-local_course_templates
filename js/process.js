var can_process = true;

window.onload = function(){
    require(['jquery'], function($) {
        $('#btnProcess').click(function(){
            if(!can_process) {
                return false;
            }
            can_process = false;
            $.ajax({
                type: "post",
                url: $('#process_request_url').val(),
                data: "course_short_name=" + $('#course_short_name').val() + "&course_name=" + $('#course_name').val(),
                dataType: "json",
                beforeSend: function(){
                    $('#btnProcess').val('Creating...');
                },
                success: function(data, textStatus){
                    if(data.status == 1){
                        // ... window.location.href = $('#process_returnurl').val()+"&status=1&courseid="+data.id;
                        window.location.href = $('#success_returnurl').val() + "/course/view.php?id=" + data.id;
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