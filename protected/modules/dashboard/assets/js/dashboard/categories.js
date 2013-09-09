var CiiDashboard = {
	
	endPoint : $("#dashboard-endpoint").attr("value"),

	Categories : {

		load : function() {
			$(".menu li").click(function() {
				window.location = $(this).find("a").attr("href");
			});

			if ($(".alert").is(":visible")) {
				$("main .settings-container #main .content").css("height", "91%").css("min-height", "91%");
			}

			$("a.close").click(function() {
				$("main .settings-container #main .content").css("height", "98%").css("min-height", "98%")
			})
		},

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

$(document).ready(function() {

	// Bind nanoscrollers
	$("#main.nano").nanoScroller();

	// Load the dashboard
	CiiDashboard.Categories.load();
});