var CiiDashboard = {

	endPoint : $("#dashboard-endpoint").attr("value"),
	
	Users : {

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
			})
		},

		loadIndex: function() {
			CiiDashboard.Users.bindUserSearchBehavior();
			CiiDashboard.Users.roundedImage();

			// Bind Invitation Ajax + Effects
			$(".invite-button").click(function(e) {
				$(this).find("#spinner").fadeIn();
				e.preventDefault();

				$.post(CiiDashboard.endPoint + "/users/create", { "email" : $("#Invite_email").val() }, function(data) {
					$("#inviteesListView .items").append(data);
					$("#inviteesListView .items .empty").remove();
				}).fail(function(data) {
					$(".alert-secondary #info").remove();
					$(".alert-secondary").append("<div id=\"info\">" + data.responseText + "</div>").show();
				});

				$(this).find("#spinner").fadeOut();
				return false;
			});
		},

		loadUpdate : function() {
			$(".meta-icon-plus").click(function(e) {
				$(".meta-container").append("<div class=\"pure-control-group\"><label contenteditable=true>Click to Change</label><input type=\"text\" class=\"pure-input-2-3\" value=\"\" /></div>");

				$(".meta-container input").on("keyup change", function() {
					$(this).attr("name", "UserMetadata[" + $(this).prev().text() + "__new]");
				})

			});

			setInterval(function() {
				$(".meta-container label[contenteditable]").each(function() {
					$(this).next().attr("name", "UserMetadata[" + $(this).text() + "__new]");
				});
			}, 1000);
		},

		loadUserList : function() {

		},

		roundedImage : function() {
			$(".rounded-img, .rounded-img2").load(function() {
			    $(this).wrap(function(){
			      return '<span class="' + $(this).attr('class') + '" style="background:url(' + $(this).attr('src') + ') no-repeat center center; width: ' + $(this).width() + 'px; height: ' + $(this).height() + 'px;" />';
			    });
			    $(this).css("opacity","0");
			  });
		},

		bindUserSearchBehavior : function() {
		    $('input#Users_displayName').keyup(function() {
		        CiiDashboard.Users.ajaxRequest = $(this).serialize();
		        clearTimeout(CiiDashboard.Users.ajaxUpdateTimeout);
		        CiiDashboard.Users.ajaxUpdateTimeout = setTimeout(function () {
		            $.fn.yiiListView.update('categoryListView', { 
			       		data: $('input#Users_displayName').serialize(),
			       		url : CiiDashboard.endPoint + '/users/index',
			       	});
		        },
		        300);
		    });
		},
	}
};

/**
 * Handles all the Javacript for User Management
 */
$(document).ready(function() {

	// Bind nanoscrollers
	$("#main.nano").nanoScroller();

	// Load the dashboard
	CiiDashboard.Users.load();
	
});
