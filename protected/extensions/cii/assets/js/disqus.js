/**
 * Disqus Javascript handler
 * Automatically loads and registers Disqus comments to a site
 * @type {Object}
 */
var Disqus = {

	/**
	 * Binds the Disqus comment box to the post
	 * @param  optional int id
	 * @return void
	 */
	load : function(id) {
		if (id != undefined)
			 disqus_identifier = id;

		(function() {
            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
            dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        })();
	},

	/**
	 * Binds the Disqus comment counts to the post and to the paginated items
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
			$(this).addClass("registered").append("<a href=\"" + endpoint + $(this).attr("data-attr-slug") + "#disqus_thread\" data-disqus-identifier=\"" + id + "\">0</a>");
		});

		// Bind the load more behavior
		Disqus.more();

		(function () {
	        var s = document.createElement('script'); s.async = true;
	        s.type = 'text/javascript';
	        s.src = '//' + disqus_shortname + '.disqus.com/count.js';
	        (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
	    }());
	},

	/**
	 * Binds to the load more click event
	 * @return void
	 */
	more : function(force) {
		$("a#more").click(function() {
			setTimeout(function() { Disqus.commentCount(); }, 500);
		});
		
		if (force == true)
			setTimeout(function() { Disqus.commentCount(); }, 500);
	},
};

/**
 * Overload this object depending upon the commenting system you are using
 * @type Comments
 */
var Comments = {
	reload : function(id) {
		Disqus.load(id);
	},

	more : function() {
		Disqus.commentCount(true);
	}
};