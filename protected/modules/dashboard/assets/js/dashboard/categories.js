$(document).ready(function() {

	// Bind nanoscrollers
	$("#main.nano").nanoScroller();

	// Load the dashboard
	CiiDashboard.Categories.load();
});

var CiiDashboard = {
	
	endPoint : $("#dashboard-endpoint").attr("value"),

	Categories : {

		load : function() {},

		loadIndex : function() {
			var ajaxUpdateTimeout;
		    var ajaxRequest;
		    $('input#Categories_name').keyup(function(){
		        ajaxRequest = $(this).serialize();
		        clearTimeout(ajaxUpdateTimeout);
		        ajaxUpdateTimeout = setTimeout(function () {
		            $.fn.yiiListView.update(
		                'ajaxListView',
		                {data: ajaxRequest}
		            )
		        },
		        300);
		    });
		},

		loadSave : function() {}
	}
};