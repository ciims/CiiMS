var DefaultTheme = {

	// The sites functional endpoint, used so we don't have to rely on PHP
	endPoint : $("#endpoint").attr("data-attr-endpoint") + "/",

	/**
	 * All functionality related to blog posts is wrapped up in here
	 * @type {Object}
	 */
	Blog : {

		loadDisqus : function(shortname, id, title, slug) {
			var disqus_shortname = shortname;
                        var disqus_identifier = id;
                        var disqus_title = title;
                        var disqus_url = slug;

                        (function() {
                                var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                                dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
                                (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
                            })();
		},
		
		/**
		 * Loads functionality to allow the comment box to work
		 */
		commentBox : function() {
			DefaultTheme.endPoint = $("#endpoint").attr("data-attr-endpoint") + "/";
			$("#b").click( function () {
		        $(this).html("");
		        $("#a").slideDown("fast");
		        $("#submit-comment").show();
		        setTimeout(function() {
		            $("#textbox").focus();
		        }, 100);
		    });
		    $("#textbox").keydown( function() {
		        if($(this).text() != "")
		            $("#submit-comment").css("background","#3b9000");
		        else
		            $("#submit-comment").css("background","#9eca80");
		        });
		    $("#close").click( function () {
		        $("#b").html("Comment on this post");
		        $("#textbox").html("");
		        $("#a").slideUp("fast");
		        $("#submit-comment").hide();
		    });
		    
		    $("#submit-comment").click(function(e) {
		        e.preventDefault();
		        if ($("#textbox").text() == "")
		            return;

		        // Disable the button to prevent double submits
		        $("#submit-comment").attr("disabled", "disabled");
		        $("#submit-comment i").show();

		        $.post(DefaultTheme.endPoint + "/comment/comment", 
		        	{ 
		        		"Comments" : 
		        		{ 
		        			"comment" : $("#textbox").html(), 
		        			"content_id" : $(".content").attr("data-attr-id") 
		        		}
		        	}, 
		        	function(data) { 
		        		$("#submit-comment i").hide();
		        		$("#textbox").text("");  
		        		$("#comment-container").prepend(data);
		        		$("div#comment-container").children(":first").fadeIn();
		        		$("#close").click();

				        // Disable the button to prevent double submits
				        $("#submit-comment").removeAttr("disabled");
		        		$(".comment-count").text((parseInt($(".comment-count").text().replace(" Comment", "").replace(" Comments", "")) + 1) + " Comments");
		        	}
		        );
		    });
		},

		/**
		 * Retrieves comments for a given blog
		 * @param  int id    The id of the blog
		 */
		getComments : function(id) {
		
			$.post(DefaultTheme.endPoint + "/comment/getComments/id/" + id, function(data) {

				$("#comment-container").html(data);
				$(".comment").show();
				$("#comment-container").fadeIn();
				$(".rounded-img").load(function() {
				    $(this).wrap(function(){
				      return '<span class="' + $(this).attr('class') + '" style="background:url(' + $(this).attr('src') + ') no-repeat center center; width: ' + $(this).width() + 'px; height: ' + $(this).height() + 'px;" />';
				    });
				    $(this).css("opacity","0");
				});

				// Flag option
				$("[class ^='flag']").click(function() {
					if ($(this).hasClass("flagged"))
						return;

					var element = $(this);
					$.post("comment/flag/id/" + $(this).attr("data-attr-id"), function() {
						$(element).addClass("flagged").text("flagged");
					});
				});

				// Reply button
				$("[class ^='reply']").click(function() { 
					$(this).parent().parent().parent().find("#comment-form").slideToggle(200); 
				});
				
			});
		},

		/**
		 * Adds functionalityt o conitrol the likebox
		 * @return {[type]} [description]
		 */
		likeBox : function(id) {
			$("[id ^='upvote']").click(function(e) {
				e.preventDefault();

				$.post(DefaultTheme.endPoint + "/content/like/id/" + id, function(data, textStatus, jqXHR) {
					if (data.status == undefined)
						window.location = "' . $this->createUrl('/login') . '"

					if (data.status == "success")
					{
						var count = parseInt($("#like-count").text());
						if (data.type == "inc")
							$("[id ^='like-count']").text(count + 1).parent().parent().parent().addClass("liked");
						else
							$("[id ^='like-count']").text(count - 1).parent().parent().parent().removeClass("liked");
					}
				});
				return false;
			});
		},

		/**
		 * Binds Markdown behavior
		 */
		marked : function() {
			marked.setOptions({
			    gfm: true,
			    highlight: function (lang, code) {
			        return hljs.highlightAuto(lang, code).value;
			    },
			    tables: true,
			    breaks: true,
			    pedantic: false,
			    sanitize: false,
			    smartLists: true,
			    smartypants: true,
			    langPrefix: "lang-"
			});

			var output = marked($("#markdown").val());
			$("#md-output").html(output);
			$("#md-output a").attr("rel", "nofollow").attr("target", "_blank")
		}


	},

	/**
	 * Retrieves twitter tweets from defaultTheme callback method getTweets and displays them
	 */
	getTweets : function() {
		DefaultTheme.endPoint = $("#endpoint").attr("data-attr-endpoint");

		$.get(DefaultTheme.endPoint + "/site/themeCallback/method/getTweets", function(data) {
			

			var dom = $("<ul id=\"tweet-list\"></ul>");
			if (!data.errors)
			{
				$(data).each(function() {
					var tweet = $("<li></li>"),
						date = new Date($(this)[0].created_at),
						message = $(this)[0].text;

					$(tweet).append($('<p></p>').addClass("date").html(date.toDateString()));
	                $(tweet).append($('<p></p>').html(message));
					$(dom).append(tweet);
				});
			}
			else {
				$(dom).append($("<li></li>").html(data.errors[0].message));
			}

			$("#twitterFeed").append(dom);
		});
	},

	/**
	 * Inifinite Scrolling Behavior callback, binds js Analytics
	 * @param  response 
	 * @param  data
	 */
	infScroll : function(response, data) {
    	var url = response.options.path.join(response.options.state.currPage);
		analytics.pageview(url);
	},

	/**
	 * Site Load function
	 *
	 * These tasks will be automatically performed when the site is loaded. Current tasks are
	 * 1) Retrieve Twitter Tweets
	 * @return {[type]} [description]
	 */
	load : function() {
		DefaultTheme.endPoint = $("#endpoint").attr("data-attr-endpoint");
		DefaultTheme.getTweets();
	},

	/**
	 * Disables automatic infinite scrollings on the appropriate page
	 */
	loadAll : function() {
		DefaultTheme.endPoint = $("#endpoint").attr("data-attr-endpoint");
		$(window).unbind('.infscr');
	},

	/**
	 * Binds necessary behaviors for the blog view
	 * @param  int id      The id of the blog
	 */
	loadBlog : function(id) {
		DefaultTheme.endPoint = $("#endpoint").attr("data-attr-endpoint");
	
		if (!$(".comments").hasClass("disqus"))	
			DefaultTheme.Blog.getComments(id);
		DefaultTheme.Blog.marked();
		DefaultTheme.Blog.commentBox();
		DefaultTheme.Blog.likeBox(id);
	},

	/**
	 * Binds certain behaviors to a comment when it is laoded in via Ajax
	 * @param  int id   The id of the comment. This must be bound to each comment set
	 */
	loadComment : function(id) {
		DefaultTheme.endPoint = $("#endpoint").attr("data-attr-endpoint");
		$(".timeago").timeago();

		// Comment Form
		$("#b-" + id).click( function () {
	        $(this).html("");
	        $("#a-" + id).slideDown("fast");
	        $("#submit-comment-" + id).show();
	        setTimeout(function() {
	            $("#textbox-" + id).focus();
	        }, 100);
	    });
	    $("#textbox-" + id).keydown( function() {
	        if($(this).text() != "")
	            $("#submit-comment-" + id).css("background","#3b9000");
	        else
	            $("#submit-comment-" + id).css("background","#9eca80");
	        });
	    $("#close-" + id).click( function () {
	        $("#b-" + id).html("Comment on this post");
	        $("#textbox-" + id).html("");
	        $("#a-" + id).slideUp("fast");
	        $("#submit-comment-" + id).hide();
	    });

	    // Submit
	    $("#submit-comment-" + id).click(function(e) {
	    	var elementId = $(this).attr('id').replace('submit-comment-', '');
        	e.preventDefault();
	        if ($("#textbox-" + id).text() == "")
	            return;

	        $("#submit-comment-" + id).attr("disabled", "disabled");
	        $("#submit-comment-" + id + " i").show();

	        $.post(DefaultTheme.endPoint + "/comment/comment", 
	        	{ 
	        		"Comments" : 
	        		{ 
	        			"comment" : $("#textbox-" + id).html(), 
	        			"content_id" : $(".content").attr("data-attr-id"),
	        			"parent_id" : elementId
	        		}
	        	}, 
	        	function(data, textStatus, jqXHR) { 
	        		$("#submit-comment-" + id + " i").hide();
	        		$("#textbox-" + id).text("");  
	        		// PREPEND DATA
	        		var newElementId = jqXHR.getResponseHeader("X-Attribute-Id");
	        		$(".comment-" + elementId).append(data);
	        		$(".comment-" + newElementId).fadeIn();

	        		$("#close-" + id).click();

	        		$("#submit-comment-" + id).removeAttr("disabled");
	        		$(".comment-count").text((parseInt($(".comment-count").text().replace(" Comment", "").replace(" Comments", "")) + 1) + " Comments");
	        	}
	        );
	    });
	},

	/**
	 * Binds registreation behaviors on the registration page
	 */
	loadRegister : function() {
		DefaultTheme.endPoint = $("#endpoint").attr("data-attr-endpoint");
		if ($("#password").val() != undefined && $("#password").val().length > 0)
			setTimeout(function() { $("#password, #password2").keyup(); }, 200);

		$("#password, #password2").keyup(function() { 
		    var element = $(this).attr("id") == "password" ? "password_strength_1" : "password_strength_2";
		    var score = zxcvbn($(this).val()).score;

		    if (score <= 1 || $(this).val().length <= 8)
		    	$("#" + element).find(".password_strength").removeClass("great").removeClass("good").removeClass("poor").css("width", "25%");
		    if (score == 2)
		    	$("#" + element).find(".password_strength").removeClass("great").removeClass("good").removeClass("poor").addClass("poor").css("width", "50%");
		    else if (score == 3)
		    	$("#" + element).find(".password_strength").removeClass("great").removeClass("good").removeClass("poor").addClass("good").css("width", "75%");
		    else if (score == 4)
		    	$("#" + element).find(".password_strength").removeClass("great").removeClass("good").removeClass("poor").addClass("great").css("width", "100%");
		    else
		    	$("#" + element).find(".password_strength").removeClass("great").removeClass("good").removeClass("poor").css("width", "25%");
		});

		// Override the submit form to display password issues
		$("form").submit(function(e) { 
			$("#jsAlert").hide();

			if ($("#password").val().length < 8)
			{
				$("#jsAlertContent").text("Your password must be at least 8 characters.").parent().slideDown();
				e.preventDefault();
				return false;
			}

			if ($("#password2").val() != $("#password").val())
			{
				$("#jsAlertContent").text("Your passwords do not match!").parent().slideDown();
				e.preventDefault();
				return false;
			}

			return true;
		});
	}
}
