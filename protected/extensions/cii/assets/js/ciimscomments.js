/**
 * CiiMSComments Javascript handler
 * Automatically loads and registers CiiMS comments to a site
 * @type {Object}
 */
var CiiMSComments = {

	/**
	 * Binds the CiiMS comments to the page
	 * @param  optional int id
	 */
	load : function(id) {

		if (id == undefined)
			id = $('.comment-count').attr('data-attr-id');

		var endpoint = $('#endpoint').attr('data-attr-endpoint') + '/';

		// Update the DOM
		$("#ciims_comments").html("<div class='comment_loader'></div><div class='new_comment'></div><div class='comments_container' style='display:none'></div>");

		// Retrieve all the comments
		$.get(endpoint + '/api/comment/comments/id/' + id, function(data) {
			// Create and append the template
			var template = '<div class="comment template"><div class="pull-left comment_person"></div><div class="pull-left comment_body"><div class="comment_body_byline"></div><div class="comment_body_inner"></div></div><div class="clearfix"></div></div>';
			$(".comments_container").append(template);

			// Iterate through the objects to add them to the dom
			$.each(data.response, function() {
				// Clone the template
				var html = $(".comment.template").clone();

				// Get the gravatar URL and append it
				var gravatar = $('<img>').attr({src: 'http://www.gravatar.com/avatar/' + md5(this.user.email) + "?s=30"});
				$(html).removeClass("template").find(".comment_person").append($(gravatar));

				// Append the byline
				var byline = $("<span class='author'><a href='" + endpoint + "/profile/" + this.user_id + "'>" + this.user.displayName+ "</a></span>");
				var date = new Date(this.created * 1000);
				var mydate = date.format('c');
				var timeAgo = $("<span class='timeago' title='" + mydate + "'>" + mydate + "</span>");
				$(html).find(".comment_body_byline").append($(byline)).append(" &#183; ").append($(timeAgo));

				// Append the comment
				$(html).find(".comment_body_inner").html(marked(this.comment));

				// Add the comment to the DOM
				$(".comments_container").append(html);
			});

			// Timeago
			$(".timeago").timeago();

			// Show the contianer
			$(".comments_container").show();

			// Show the submission container
		});
	},

	/**
	 * Binds the CiiMSComments comment count
	 */
	commentCount : function() {
		var endpoint = $('#endpoint').attr('data-attr-endpoint') + "/";

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
		
		if (elements.length == 0)
			return;

		$.post(endpoint + "/api/comment/count", { "ids" : elements }, function(data) {
			$.each(data.response, function(k, v) { 
				$("[data-ciimscomments-identifier=" + k + "]").text(v);
			});
		});
	},

	/**
	 * Binds to the load more click event
	 * @return void
	 */
	more : function() {
		$("a#more").click(function() {
			CiiMSComments.commentCount();
		})
	}
};

/**
 * Overload this object depending upon the commenting system you are using
 * @type Comments
 */
var Comments = {
	reload : function(id) {
		CiiMSComments.load(id);
	}
}