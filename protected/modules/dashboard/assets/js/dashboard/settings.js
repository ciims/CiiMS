var CiiDashboard = {

	endPoint : $("#dashboard-endpoint").attr("value"),

	Settings : {

		load : function() {
			$(".menu li").click(function() { 
				window.location = $(this).find("a").attr("href");
			});
		},

		loadIndex : function() {
			$("#GeneralSettings_subdomain").attr("disabled", "disabled");
		},

		loadAppearance : function() {
			$("select").imagepicker();
		},

		loadAnalytics: function() {
			$("label.checkbox.toggle input").click(function(e) {
				e.stopPropagation();

				var name = $(this).parent().parent().parent().attr("data-name");

				if ($(this).parent().find("[type=checkbox]").is(":checked"))
					window[name] = false;

				return;
			});

			$(".provider").click(function(e) {
				e.stopPropagation();

				// Get the current sidebarname
				var side = $(this).find(".tile").attr("data-name");

				if (typeof(window[side]) == "undefined")
					window[side] = true;

				if (!window[side])
				{
					window[side] = true;
					return;
				}

				// Hide the transparent overflow
				var top = ($(".content").scrollTop() - 6 + "px");

				$(".transparent").css("top", top).show();

				// Remove the active class from all options-panels
				$(".options-panel").removeClass("active");

				// Disable scrolling
				$("#main, .content").css("overflow", "hidden");
				$(".content").css("right", "0px");
				$(".nano").nanoScroller({ stop: true });

				$(".options-panel." + side).css("position", "absolute").css("top", top).animate({
			        right: 0
			    }, 300);
			});

			$(".transparent").click(function() {
				// Hide the transparent overflow
				$(".transparent").hide();

				// Remove the active class from all options-panels
				$(".options-panel").animate({
			        right: "-100%"
			    }, 500);

				// Restore scrolling
				$("#main, .content").css("overflow", "");
				$(".content").css("right", "-17px");
				$(".nano").nanoScroller({ stop: false });
			});
		},

		loadEmail : function() {
			$("#test-email").click(function() {
				var testaddress = $("#EmailSettings_Test").val();
				$.post(CiiDashboard.endPoint + "/settings/emailtest", { email : testaddress }, function(data, textStatus, jqXHR) { 
					console.log(data);
				});
			});
		},

		loadSocial : function() {},

		loadSystem: function() {
			$("#header-button").click(function() {
				$.post("flushcache", function(data, textStatus) {
					if (textStatus == "success")
					{
						// Do something to indicate it was successful
					}
					else
					{
						// Do something to indicate it failed
					}

					// Stop the "in progress" behavior
				});
			
			});

			$.get("getissues", function(data) {
				$(".issues").html(data);
			});

		},

	}
};

/**
 * Handles all the Javacript for the Dashboard.
 */
$(document).ready(function() {

	// Bind nanoscrollers
	$("#main.nano").nanoScroller();

	// Load the dashboard
	CiiDashboard.Settings.load();
	
});