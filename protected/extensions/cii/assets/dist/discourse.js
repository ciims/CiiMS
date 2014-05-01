/**
 * Discourse Javascript handler
 * Automatically loads and registers Discourse comments to a site
 * @type {Object}
 */
var Discourse = {

	/**
	 * Binds the Discourse comment box to the post
	 * @param  optional int id
	 * @return void
	 */
	load : function(id) {
		if (id != undefined)
			 discourseEmbedUrl = id;

		discourseEmbedUrl = window.location.href;

	  	(function() {
	    	var d = document.createElement('script'); d.type = 'text/javascript'; d.async = true;
	      	d.src = discourseUrl + 'javascripts/embed.js';
	    	(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(d);
	  	})();
	},

	/**
	 * Binds the Discourse comment counts to the post and to the paginated items
	 * @return void
	 */
	commentCount : function () {
		var endpoint = $('#endpoint').attr('data-attr-endpoint');

		$('.comment-count').each(function() {
			// Bind a 0 comment count to items that have not already been registered
			if ($(this).hasClass("registered"))
				return;

			var id = $(this).attr('data-attr-id');

			// Then register the comment
			$(this).addClass("registered").append("<a href=\"" + endpoint + $(this).attr("data-attr-slug") + "#Discourse_thread\" data-Discourse-identifier=\"" + id + "\">0</a>");
		});

		// Bind the load more behavior
		Discourse.more();
	},

	/**
	 * Binds to the load more click event
	 * @return void
	 */
	more : function(force) {
		$("a#more").click(function() {
			setTimeout(function() { Discourse.commentCount(); }, 500);
		});
		
		if (force == true)
			setTimeout(function() { Discourse.commentCount(); }, 500);
	},
};

/**
 * Overload this object depending upon the commenting system you are using
 * @type Comments
 */
var Comments = {
	reload : function(id) {
		Discourse.load(id);
	},

	more : function() {
		Discourse.commentCount(true);
	}
};