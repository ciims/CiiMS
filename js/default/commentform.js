/* contact form */
$(document).ready(function(){
	var message = $("#comment");
	var form = $("#reply");
	
	message.blur(validateMessage);
	
	var inputs = form.find(":input").filter(":not(:submit)").filter(":not(:checkbox)").filter(":not([type=hidden])").filter(":not([validate=false])");

	form.submit(function()
	{
		if(validateMessage())
		{
			$.ajax({
				type: 'POST',
				url: 'comment/comment/',
				data: $("#reply").serialize(),
				success: function (data, textStatus, jqXHR)
				{
					$("#new-comment").replaceWith(data).slideDown(500);
					$("#comment").val("");
				}
			});
		}
		else
		{
			return false;
		}
		return false;
	});

	
	function validateMessage()
	{
		if(!message.val())
		{
			return false;
		}
		
		return true;
	}
	
	$("a#delete").click(function() { 
		$.ajax({
			type: 'POST',
			url: 'admin/comments/delete/id/' + $(this).attr('value')
		});
		
		$(this).parent().parent().parent().slideUp();
		return false;
	});
	
	$("a#flag").click(function() { 
		$.ajax({
			type: 'POST',
			url: 'comment/flag/id/' + $(this).attr('value'),
			success : function() {
				$.gritter.add({
				title: 'Comment has been flagged',
				text: 'This comment has been flagged for review, and a moderator will review it shortly. Abuse of this feature may result in account suspension',
				sticky: false,
				time: ''
			});
			}
		});
		return false;
	});	
});
/* end contact form */
