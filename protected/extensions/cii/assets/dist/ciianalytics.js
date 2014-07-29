var ciianalytics = {
	
	endpoint : null,

	init : function() {
		this.endpoint = $("#endpoint").attr("data-attr-endpoint") + "/api/event"
		this.page();
	},

	page : function() {
		var self = this;
		this.ajax('_trackPageView', {
			'content_id' : self.getContentId(),
			'uri' : window.location.pathname,
			'page_title' : document.title
		});
	},

	track : function(aEvent, params) {
		var self = this;
		params['content_id'] = self.getContentId();
		params['uri'] = params['uri'] || window.location.pathname;
		params['page_title'] = params['page_title'] || document.title;
		this.ajax(aEvent, params);
	},

	getContentId : function() {
		return $("#content").attr("data-attr-id") || false;
	},

	ajax : function(aEvent, params) {
		var self = this;
		params['event'] = aEvent;
		return $.post(self.endpoint, { 'Event' : params });
	}
};