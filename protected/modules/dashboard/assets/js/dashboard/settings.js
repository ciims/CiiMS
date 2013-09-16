var CiiDashboard = {

	endPoint : $("#dashboard-endpoint").attr("value"),

	Settings : {

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

		loadCards : function() {
			$("#submit-form").click(function(e) {
				$("#spinner").fadeIn();
				e.preventDefault();

				$.post(CiiDashboard.endPoint + '/settings/addCard', $("form").serialize(), function(data) {
					$(".meta-container").append('<div class="pure-control-group"><label class="inline">' +  data.class + '</label><p class="text-small inline" style="top: -8px;">' + data.name + '</p><span class="pure-button pure-button-warning pure-button-small pure-button-link pull-right" style="top: -13px;">0</span><span class="icon-remove inline pull-right" id="' + data.folderName + '"></span></div>');

					$("#spinner").fadeOut();add()
				})
			});

			$(".icon-remove").click(function() {
				var parent = $(this).parent();
				$.post(CiiDashboard.endPoint + "/settings/deleteCard/id/" + $(this).attr("id"), function() {
					$(parent).fadeOut();
				})
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

		loadPlugins : function() {},
		
		loadTheme : function() {},
		loadMobileTheme : function() {},
		loadTabletTheme : function() {},
		
		loadSocial : function() {},

		loadSystem: function() {
			$("#header-button").click(function() {
				$("#spinner").fadeIn();
				$.post("flushcache", function(data, textStatus) {
					$("#spinner").fadeOut();
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