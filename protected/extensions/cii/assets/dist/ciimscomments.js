/**
 * CiiMSComments Javascript handler
 * Automatically loads and registers CiiMS comments to a site
 * @type {Object}
 */
var Comments = {

	isModuleLoaded : false,

	isLoaded : false,

	init : function(id, title, url) {
		ciims_identifier = id;
		ciims_title = title;
	},

	reload : function(id) {
		ciims_identifier = id;
		this.load(id);
	},

	storage : null,

	getStoredInfoBy : function(name) {
		if (Comments.storage == null)
			Comments.storage = jQuery.parseJSON(localStorage.getItem('ciims'));

		return Comments.storage[name];
	},

	/**
	 * Binds the CiiMS comments to the page
	 * @param  optional int id
	 */
	load : function(id) {

		if (id == undefined)
			id = $('.comment-count').attr('data-attr-id');

		ciims_identifier = id;

		var endpoint = $('#endpoint').attr('data-attr-endpoint') + '/';

		// Update the DOM
		$("#ciims_comments").html("<div class='comment_loader'></div><div class='comment_messages'><div class='clearfix'></div></div><h3>Comments</h3><div class='new_comment'></div><div class='comments_container' style='display:none'></div>");

		var isAuthenticated = Comments.getStoredInfoBy("isAuthenticated");

		if (isAuthenticated == true)
		{
			// Create the container and clone it
			var templateContainer = '<div class="comment template_container"><div class="pull-left comment_person"></div><div class="pull-left comment_body"><div class="comment_body_inner"></div></div><div class="clearfix"></div></div>';
			$(".new_comment").append(templateContainer);
			var NewComment = $(".template_container").clone();

			// Remove what we put into the DOM
			$(".template_container").remove();

			// Create the Gravatar URL for the user
			var gravatar = $('<img>').attr({src: 'https://www.gravatar.com/avatar/' + md5(Comments.getStoredInfoBy("email")) + "?s=30"});
			$(NewComment).find(".comment_person").append($(gravatar));

			// Add the comment box
			var form = $("<form>").addClass("pure-form");
			var box = $("<input>").attr("type", "text").addClass("comment_box pure-u-1").attr("placeholder", "Add a Comment...").attr("name", "comment_box");

			$(form).append($(box));
			$(NewComment).find(".comment_body_inner").append($(form));

			// Add the new comment to the body
			$(".new_comment").html($(NewComment));

			Comments.behaviors.bind();
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
		        "X-Auth-Email" : Comments.getStoredInfoBy("email"),
		        "X-Auth-Token" : Comments.getStoredInfoBy("token")
		    },
		    dataType : 'json',
		    success : function(data) {
				// Create and append the template
				var template = '<div class="comment template"><div class="pull-left comment_person"></div><div class="pull-left comment_body"><div class="comment_body_byline"></div><div class="comment_body_inner"></div></div><div class="clearfix"></div></div>';
				$(".comments_container").append(template);

				// Iterate through the objects to add them to the dom
				$.each(data.response, function() {
                    Comments.behaviors.showComment(this);
				});

                Comments.behaviors.bindMod();
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

		Comments.more();
		
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
	more : function(force) {
		$("a#more").click(function() {
			setTimeout(function() { Comments.commentCount(); }, 500);
		});
		
		if (force == true)
			setTimeout(function() { Comments.commentCount(); }, 500);
	},

	behaviors : {

        /**
         * Shows a comment with a response
         * @return void
         */
        showComment : function(response, showFirst) {

        	if (showFirst == undefined)
        		showFirst = false;

            var role = Comments.getStoredInfoBy("role");
            var isAuthenticated = Comments.getStoredInfoBy("isAuthenticated");

            var html = $(".comment.template").clone();

            // Get the gravatar URL and append it
            var gravatar = $('<img>').attr({src: 'http://www.gravatar.com/avatar/' + md5(response.user.email) + "?s=30"});
            $(html).removeClass("template").find(".comment_person").append($(gravatar));

            // Append the byline
            var byline = $("<span class='author'><a href='" + endpoint + "/profile/" + response.user_id + "'>" + response.user.displayName+ "</a></span>");
            var date = new Date(response.created * 1000);
            var mydate = date.format('c');
            var timeAgo = $("<span class='timeago' title='" + mydate + "'>" + mydate + "</span>");

            // Flag/Delete functionality
            var modContainer = $("<div>").addClass("mod_container pull-right");
            var flagBtn = $("<a>").addClass("flag_comment fa fa-flag").attr("href", "#").attr("data-attr-id", response.id);
            var deleteBtn = $("<a>").addClass("delete_comment fa fa-times").attr("href", "#").attr("data-attr-id", response.id);
        
            // If the user is an admin or mod Show some extra details
            if (role == 7||role == 9)
            {
                // Indicate hellbanned comments to admins
                if (response.banned_comment == true)
                {
                    var banned = $("<span>").addClass("orange").text("shadowbanned");
                    $(modContainer).append($(banned)).append(" &#183; ");
                }
                $(modContainer).append($(flagBtn)).append(" &#183; ").append($(deleteBtn));
            }
            else if (isAuthenticated == "true")
                $(modContainer).append($(flagBtn));
            else
                $(modContainer);

            $(html).find(".comment_body_byline").append($(byline)).append(" &#183; ").append($(timeAgo)).append($(modContainer));

            // Append the comment
            $(html).find(".comment_body_inner").html(marked(response.comment));

            // Add the comment to the DOM
            if (showFirst)
            	$(".comments_container").prepend(html);
            else
            	$(".comments_container").append(html);
        },

        bindMod : function() {
            // Bind flagging behavior
            $(".flag_comment").click(function(e) {
                e.preventDefault();
                var id = $(this).attr("data-attr-id");
                var self = $(this);
                $.ajax({
                    url : $('#endpoint').attr('data-attr-endpoint') + "/api/comment/flag/id/" + id,
                    type : 'post',
                    headers : {
                        "X-Auth-Email" : Comments.getStoredInfoBy("email"),
                        "X-Auth-Token" : Comments.getStoredInfoBy("token")
                    },
                    dataType : 'json',
                    beforeSend : function() {
                        $(self).addClass("orange");
                    },
                    error : function () {
                        setTimeout(function() { $(self).removeClass("orange"); }, 1000);
                    }
                });

                // Return false to prevent binding
                return false;
            });
        
            // Bind delete behavior
            $(".delete_comment").click(function(e) {
                e.preventDefault();

                var id = $(this).attr("data-attr-id");
                var self = $(this);
                $.ajax({
                    url : $('#endpoint').attr('data-attr-endpoint') + "/api/comment/index/id/" + id,
                    type : 'delete',
                    headers : {
                        "X-Auth-Email" : Comments.getStoredInfoBy("email"),
                        "X-Auth-Token" : Comments.getStoredInfoBy("token")
                    },
                    dataType : 'json',
                    beforeSend : function() {
                        $(self).addClass("orange");
                    },
                    error : function () {
                        setTimeout(function() { $(self).removeClass("orange"); }, 1000);
                    },
                    success : function() {
                        var par = $(self).parent().parent().parent().parent().fadeOut().remove();
                    }
                });
                // Return false to prevent link click
                return false;
            });
        },

        /**
         * Binds the new comment box to the DOM
         * @return void
         */
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

					Comments.behaviors.bind();
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
					        "content_id" : ciims_identifier,
					    },
					    headers : {
					        "X-Auth-Email" : Comments.getStoredInfoBy("email"),
					        "X-Auth-Token" : Comments.getStoredInfoBy("token")
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

                            // Show the comment
                            Comments.behaviors.showComment(data.response, true);
                            Comments.behaviors.bindMod();

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