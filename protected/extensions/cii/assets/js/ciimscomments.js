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
		$("#ciims_comments").html("<div class='comment_loader'></div><div class='comment_messages'><div class='clearfix'></div></div><h3>Comments</h3><div class='new_comment'></div><div class='comments_container' style='display:none'></div>");

		var isAuthenticated = localStorage.getItem("isAuthenticated");

		if (isAuthenticated == "true")
		{
			// Create the container and clone it
			var templateContainer = '<div class="comment template_container"><div class="pull-left comment_person"></div><div class="pull-left comment_body"><div class="comment_body_inner"></div></div><div class="clearfix"></div></div>';
			$(".new_comment").append(templateContainer);
			var NewComment = $(".template_container").clone();

			// Remove what we put into the DOM
			$(".template_container").remove();

			// Create the Gravatar URL for the user
			var gravatar = $('<img>').attr({src: 'http://www.gravatar.com/avatar/' + md5(localStorage.getItem("email")) + "?s=30"});
			$(NewComment).find(".comment_person").append($(gravatar));

			// Add the comment box
			var form = $("<form>").addClass("pure-form");
			var box = $("<input>").attr("type", "text").addClass("comment_box pure-u-1").attr("placeholder", "Add a Comment...").attr("name", "comment_box");

			$(form).append($(box));
			$(NewComment).find(".comment_body_inner").append($(form));

			// Add the new comment to the body
			$(".new_comment").html($(NewComment));

			CiiMSComments.behaviors.bind();
		}
		else
		{
			var link = $("<a>").attr("href", endpoint + "login?next=" + window.location.href.replace(endpoint, "")).addClass("alert alert-info").html("You must be logged in to post new comments");
			$("#ciims_comments").find(".comment_messages").show().prepend($(link));
		}

		// Retrieve all the comments
		
		$.ajax({
		    url : endpoint + 'api/comment/comments/id/' + id,
		    type : 'get',
		    headers : {
		        "X-Auth-Email" : localStorage.getItem("email"),
		        "X-Auth-Token" : localStorage.getItem("token")
		    },
		    dataType : 'json',
		    success : function(data) {
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
				$(".comment_loader").fadeOut();
			}
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
	},

	behaviors : {

		bind : function() {

			// On the Focu event
			$(".comment_box").focus(function() {

				// Replace the box with a textarea
				var textArea = $("<textarea>").attr("type", "text").addClass("comment_box pure-u-1 pure-focus").attr("placeholder", "Add a Comment...").attr("name", "comment_box");
				$(this).replaceWith($(textArea));

				// Focus on it
				$(".comment_box").focus();

				// Append the button
				var button = $("<input>").attr("type", "submit").attr("id", "comment_submit_button").addClass("pure-button pull-right pure-button-primary comment_button");
				var cancel = $("<a>").attr("href", "#").addClass("pure-button pull-right cancel_button").text("Cancel");
				$(".comment_box").after($(cancel)).after($(button));

				// Bind the OnBlur event
				$("a.cancel_button").click(function() {
					var box = $("<input>").attr("type", "text").addClass("comment_box pure-u-1 pure-focus").attr("placeholder", "Add a Comment...").attr("name", "comment_box");
					$(".comment_box").replaceWith($(box));

					$(".new_comment").find("input#comment_submit_button").remove();
					$(".new_comment").find("a").remove();

					$(".comment_box").removeClass("pure-focus");

					CiiMSComments.behaviors.bind();
				});

				// Bind the submit button click event
				$("#comment_submit_button").click(function(e) {
					e.preventDefault();

					var text = $(".comment_box").val();
					if (text == "")
					{
						$(".comment_box").addClass("error");
						setTimeout(function() { $(".comment_box").removeClass("error"); }, 2000);
						return false;
					}

					// Send the POST Request
					$.ajax({
					    url : $('#endpoint').attr('data-attr-endpoint') + "/api/comment",
					    type : 'post',
					    data : {
					        "comment" : text,
					        "content_id" : $('.comment-count').attr("data-attr-id"),
					    },
					    headers : {
					        "X-Auth-Email" : localStorage.getItem("email"),
					        "X-Auth-Token" : localStorage.getItem("token")
					    },
					    dataType : 'json',
					    beforeSend : function() {
					    	$("input#comment_submit_button").attr("disabled", "disabled");
					    },
					    error : function() {
					    	$("input#comment_submit_button").removeAttr("disabled");
					    	$(".comment_box").addClass("error");
							setTimeout(function() { $(".comment_box").removeClass("error"); }, 2000);
					    },
					    success : function (data) {

					    	var response = data.response;
					    	var html = $(".comment.template").clone();

							// Get the gravatar URL and append it
							var gravatar = $('<img>').attr({src: 'http://www.gravatar.com/avatar/' + md5(response.user.email) + "?s=30"});
							$(html).removeClass("template").find(".comment_person").append($(gravatar));

							// Append the byline
							var byline = $("<span class='author'><a href='" + endpoint + "/profile/" + response.user_id + "'>" + response.user.displayName+ "</a></span>");
							var date = new Date(response.created * 1000);
							var mydate = date.format('c');
							var timeAgo = $("<span class='timeago' title='" + mydate + "'>" + mydate + "</span>");
							$(html).find(".comment_body_byline").append($(byline)).append(" &#183; ").append($(timeAgo));

							// Append the comment
							$(html).find(".comment_body_inner").html(marked(response.comment));

							// Add the comment to the DOM
							$(".comments_container").prepend(html);
							$(".timeago").timeago();

					    	// Close the comment box
					        $("a.cancel_button").click();
					    }
					});

					return false;

				});
			});

			


		}

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