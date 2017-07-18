var can_process = true;

$(document).ready(function(){
	$('#btnProcess').click(function(){
		if(!can_process){
			return false;
		}

		can_process = false;
		$.ajax({
	        type: "post",
	        url: $('#process_request_url').val(),
	        data: "course_short_name="+$('#course_short_name').val(),
	        dataType: "json",
	        beforeSend: function(){
	        	$('#btnProcess').val('Creating...');
	        },
	        success: function(data, textStatus){
	        	if(data.status == 1){
	        		//window.location.href = $('#process_return_url').val()+"&status=1&courseid="+data.id;
	        		window.location.href = $('#success_return_url').val()+"/course/view.php?id="+data.id;
	        	}else{
	        		window.location.href = $('#process_return_url').val()+"&status="+data.status+"&courseid="+data.id+"&cateid="+data.cateid;
	        	}
	        	$('#btnProcess').val('Continue');
	        	can_process = true;
	        }
    	});
	})
})