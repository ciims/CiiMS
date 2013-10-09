var CiiDashboard = {
	
	endPoint : $("#dashboard-endpoint").attr("value"),

	Categories : {
		
		ajaxUpdateTimeout : null,

		ajaxRequest : null,

		load : function() {
			$(".menu li").click(function() {
				window.location = $(this).find("a").attr("href");
			});

			if ($(".alert").is(":visible")) {
				$("main .settings-container #main .content").css("height", "91%").css("min-height", "91%");
			}

			$("a.close").click(function() {
				$("main .settings-container #main .content").css("height", "98%").css("min-height", "98%")
			});

		    $('input#Categories_name').keyup(function() {
		        CiiDashboard.Categories.ajaxRequest = $(this).serialize();
		        clearTimeout(CiiDashboard.Categories.ajaxUpdateTimeout);
		        CiiDashboard.Categories.ajaxUpdateTimeout = setTimeout(function () {
		            $.fn.yiiListView.update('categoryListView', { 
			       		data: $('input#Categories_name').serialize(),
			       		url : CiiDashboard.endPoint + '/categories/index',
			       	});
		        },
		        300);
		    });
		},

		loadIndex : function() {
			
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