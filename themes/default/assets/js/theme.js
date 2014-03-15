var Theme = {

	// The sites functional endpoint, used so we don't have to rely on PHP
	endPoint : $("#endpoint").attr("data-attr-endpoint") + "/",

	/**
	 * All functionality related to blog posts is wrapped up in here
	 */
	Blog : {

		/**
		 * Adds functionalityt o conitrol the likebox
		 */
		likeBox : function(id) {

            $(".comment-container").click(function(e) {
                e.preventDefault();
                window.location = "#comments";
                return false;
            });

            $(".likes-container").click(function(e) {
                e.preventDefault();
                $(this).find("a").click();
            });

			$("[id ^='upvote']").click(function(e) {
				e.preventDefault();

				$.post(Theme.endPoint + "/content/like/id/" + id, function(data, textStatus, jqXHR) {
					if (data.status == undefined)
						window.location = Theme.endPoint + "/login";

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
		Theme.endPoint = $("#endpoint").attr("data-attr-endpoint");

        $(".comment-container").click(function(e) {
            e.preventDefault();
            window.location = $(this).find("a").attr("href");
            return false;
        });

        $(".nav-item").click(function() {
        	$(".top-navigation").slideToggle();
        });
	},

	/**
	 * Disables automatic infinite scrollings on the appropriate page
	 */
	loadAll : function() {
		Theme.endPoint = $("#endpoint").attr("data-attr-endpoint");
		$(window).unbind('.infscr');
	},

	/**
	 * Binds necessary behaviors for the blog view
	 * @param  int id      The id of the blog
	 */
	loadBlog : function(id) {
		Theme.endPoint = $("#endpoint").attr("data-attr-endpoint");
		Theme.Blog.marked();
		Theme.Blog.likeBox(id);
	},

	/**
	 * Binds registreation behaviors on the registration page
	 */
	loadRegister : function() {
		Theme.endPoint = $("#endpoint").attr("data-attr-endpoint");
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
};
