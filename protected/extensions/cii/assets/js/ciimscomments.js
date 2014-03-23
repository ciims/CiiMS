var CiiMSComments = {

	load : function() {
		var endpoint = $('#endpoint').attr('data-attr-endpoint') + '/';

		// Retrieve all the comments
		$.get(endpoint + '/api/comment/comments/id/' + $('.comment-count').attr('data-attr-id'), function(data) { 
			console.log(data.response); 
		});
	},

	commentCount : function() {
		var endpoint = $('#endpoint').attr('data-attr-endpoint');

		var elements = [];
		$('.comment-count').each(function() {
			// Retrieve the ID
			var id = $(this).attr('data-attr-id');
			elements.push(id);

			// Bind a 0 comment count to items that have not already been registered
			if ($(this).hasClass("registered"))
				return;

			$(this).addClass("registered").append("<a href=\"" + endpoint + $(this).attr("data-attr-slug") + "#comment\" data-ciimscomments-identifier=\"" + id + "\">0</a>");
		});

		CiiMSComments.more();
		
		$.post(endpoint + "/api/comment/count", { "ids" : elements }, function(data) {
			$.each(data.response, function(k, v) { 
				$("[data-ciimscomments-identifier=" + k + "]").text(v);
			});
		});
	},

	more : function() {
		$("a#more").click(function() {
			CiiMSComments.commentCount();
		})
	}
};