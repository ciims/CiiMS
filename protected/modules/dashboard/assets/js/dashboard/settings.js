var CiiDashboard = {

	endPoint : $("#dashboard-endpoint").attr("value"),

	Settings : {

		// Settings for Addons that apply to both themes and cards
		Addon : {

			ucwords : function(str) {
			  	return (str + '').replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function($1) {
			    	return $1.toUpperCase();
			    });
			},

			manager : function(type) {
				$(".remove-button").click(function() {
					var parent = $(this).parent();
					$.post(CiiDashboard.endPoint + "/" + type + "/uninstall/id/" + $(this).attr("id"), function() {
						$(parent).fadeOut();
					})
				});

				// Check for card updates
				$("span[id^=updater]").each(function() {
					var id = $(this).attr("data-attr-id");

					if (id == "")
						return;

					var self = this;
					$.get(CiiDashboard.endPoint + '/' + type + '/isUpdateAvailable/id/' + id, function(data) {

						$(self).find(".icon-spinner").hide();
						$(self).find(".checking").hide();

						if (data.response.update == false) {						
							$(self).find(".uptodate").show();
							$(self).removeClass("pure-button-primary").addClass("pure-button-success");
						} else {
							$(self).find(".available").show();
							$(self).removeClass("pure-button-primary").addClass("pure-button-warning-pulse");
						}
					}).fail(function() {
						$(self).parent().find(".icon-spinner").hide();
						$(self).parent().find(".checking").hide();
						$(self).removeClass("pure-button-primary").addClass("pure-button-error-pulse");
						$(self).parent().find(".updating-error").show();
					});
				});

				// Action to perform the update
				$(".available").click(function() {
					$(this).parent().removeClass("pure-button-warning-pulse").addClass("pure-button-primary-pulse");
					$(this).parent().find(".icon-spinner").show();
					$(this).parent().find(".updating").show();
					$(this).hide();

					var id = $(this).parent().attr("data-attr-id");
					var self = this;
					$.get(CiiDashboard.endPoint + '/' + type + '/upgrade/id/' + id, function(data) {

						$(self).parent().find(".icon-spinner").hide();
						$(self).parent().find(".updating").hide();

						if (data.status == 200) {
							$(self).parent().removeClass("pure-button-primary-pulse").addClass("pure-button-success");
							$(self).parent().find(".uptodate").show();
						} else {
							$(self).parent().removeClass("pure-button-primary-pulse").addClass("pure-button-error-pulse");
							$(self).parent().find(".updating-error").show();
						}
					}).fail(function() {
						$(self).parent().find(".icon-spinner").hide();
						$(self).parent().find(".updating").hide();
						$(self).parent().removeClass("pure-button-primary-pulse").addClass("pure-button-error-pulse");
						$(self).parent().find(".updating-error").show();
					})
				});
			},

			uninstall : function(type) {
				// Generate a list of uninstalled cards and display them        
	            $.getJSON(window.location.origin + CiiDashboard.endPoint + "/" + type +"/uninstalled", function(data) {
	                if (data.response.length == 0)
	                    $("#uninstalled-notifier").show();
	                else
	                {
	                    var html = "";
	                    var installDiv = $(".install").html();
	                    var installingDiv = $(".installing").html();
	                    var unregisterDiv = $(".unregister").html();
	                    $(data.response).each(function() {
	                        html += '<div class="pure-control-group"><p class="text-small text-small-inline inline">' + this.name + '</p><span class="pure-button pure-button-warning-pulse pure-button-xsmall pure-button-link-xs pull-right" id="updater" data-attr-id="' + this.uuid + '"><span class="icon-spinner icon-spin" style="display:none;"></span><span class="install">' + installDiv + '</span></span><span class="pure-button pure-button-error pure-button-xsmall pure-button-link-xs pull-right"><span class="unregister">' + unregisterDiv + '</span></span><div class="clearfix"></div></div>';
	                    });

	                    $("#uninstalled-notifier").before(html);

	                    $(".unregister").click(function() {
	                        var id = $(this).parent().parent().find("#updater").attr("data-attr-id");
	                        var self = this;
	                        $.getJSON(CiiDashboard.endPoint + '/' + type + '/unregister/id/' + id, function(data) {
	                            if (data.status == 200)
	                            {
	                                $(self).parent().parent().remove();
	                            }
	                            else
	                                $(self).parent().removeClass("pure-button-primary-pulse").addClass("pure-button-error-pulse");
	                        });
	                    });

	                    // Bind an install click event for each of these buttons
	                    $(".install").click(function() {
	                        $(this).parent().removeClass("pure-button-warning-pulse").addClass("pure-button-primary-pulse");
	                        $(this).parent().find(".icon-spinner").show();
	                        $(this).addClass("installing").removeClass("install").html(installingDiv);

	                        var id = $(this).parent().attr("data-attr-id");
	                        var self = this;

	                        // Actually install the card
	                        $.getJSON(CiiDashboard.endPoint + '/' + type + '/install/id/' + id, function(data) {
	                            if (data.status == 200)
	                            {
	                                $(self).parent().parent().remove();
	                                $("#reload-notifier").show();
	                                $("#installed-notifier").hide();
	                            }
	                            else
	                                $(self).parent().removeClass("pure-button-primary-pulse").addClass("pure-button-error-pulse");

	                            // Show the uninstalled notifier
	                            if ($("#uninstalled-container").find(".pure-control-group").size() == 0)
	                                $("#uninstalled-notifier").show();
	                        });
	                    });
	                }
	            });
			}
		},

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

            CiiDashboard.Settings.bindCarousel('theme', null);

			$("select").imagepicker();

			$("#ThemeSettings_theme, #ThemeSettings_mobileTheme, #ThemeSettings_tabletTheme").change(function(e) { 
				e.preventDefault();
                var form = $(this).parent().parent();

                // TODO: Pretty this up some
                $.post($(form).attr("action"), $(form).serialize());
			});

            // Bind the search event on the carousel
            $("input#text").submit(function(e) {
                e.preventDefault();
                CiiDashboard.Settings.bindCarousel('theme', $(this).val());
                return false;
            });

            $('form').keypress(function(e) {
                if ( e.which == 13 ) {
                    $("input#text").submit();
                    return false;
                }
            });

            // Handle uninstallation of themnes
            CiiDashboard.Settings.Addon.uninstall('theme');

            // Retrieve all the managed & installed items
            $.getJSON(window.location.origin + CiiDashboard.endPoint + "/theme/installed", function(data) {
				// Iterate through all of the existing items, and templatize them
            	$.each(data.response, function(key, value) {
            		// Fetch the template
            		if (value.uuid == false)
            			return;

            		var template = $(".template").clone();
            		$(template).removeClass(".template");
            		$(template).find(".text-small").text(CiiDashboard.Settings.Addon.ucwords(value.name));
            		$(template).find(".remove-button").attr("id", value.uuid);
            		$(template).find("#updater").attr("data-attr-id", value.uuid);
            		$(template).show();

            		$("#installed-container").append("<div class='pure-control-group'>" + $(template).html() + "</div>");
            	});

            	CiiDashboard.Settings.Addon.manager('theme');
            }, "json");

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

		Carousel : {
			recentlySearched : []
		},

		loadCards : function() {

			// Bind the carousel
			CiiDashboard.Settings.bindCarousel('card', null);

			// Bind the search event on the carousel
			$("input#text").submit(function(e) {
				e.preventDefault();
				CiiDashboard.Settings.bindCarousel('card', $(this).val());
				return false;
			});

			$('form').keypress(function(e){
			    if ( e.which == 13 ) // Enter key = keycode 13
			    {
			        $("input#text").submit();
			        return false;
			    }
			});

			// Generate a list of uninstalled cards and display them		
			CiiDashboard.Settings.Addon.uninstall('card');

			// Handle management of existing items
			CiiDashboard.Settings.Addon.manager('card');
		},

		loadEmail : function() {
			$("#test-email").click(function() {
				var self = this;
				$(this).find("#spinner").fadeIn();
				var testaddress = $("#EmailSettings_Test").val();
				$(".alert-secondary").hide().html("<a class='close' data-dismiss='alert'></a>");
				$.post(CiiDashboard.endPoint + "/settings/emailtest", { email : testaddress }, function(data, textStatus, jqXHR) { 
					$(".alert-secondary").removeClass("alert-error").addClass("alert-success").find("a").after("Email Sent!");
					$(".alert-secondary").fadeIn(200);
					$(self).find("#spinner").fadeOut();
				}).fail(function(data) {
					$(".alert-secondary").removeClass("alert-success").addClass("alert-error").find("a").after(data.responseText);
					$(".alert-secondary").fadeIn(200);
					$(self).find("#spinner").fadeOut();
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

		bindReload : function(type) {
        	$(".jcarousel ul li img").click(function() {
        		// Get the clicked ID
        		var id = $(this).parent().attr("data-attr-id");

        		// Retrieve the view file, and display it.
        		$.get(window.location.origin + CiiDashboard.endPoint + "/" + type + "/detailsview/id/" + id, function(data) {
        			$('div.modal').html(data).omniWindow().trigger('show');
        		});
        	});
		},

		bindCarousel : function(type, text) {

			var jcarousel = $('.jcarousel').jcarousel();

			$.post(window.location.origin + CiiDashboard.endPoint + "/" + type + "/search", { 'type' : type, 'text' : text}, function(data) {
				CiiDashboard.Settings.Carousel.recentlySearched = data.response;

            	var html = "<ul>";
            	$(data.response).each(function() {
            		html += '<li data-attr-id="' + this.uuid +'"><img src="' + this.screen_shot + '"></li>';
            	});
            	html += "</ul>";
            	jcarousel.html(html);
            	jcarousel.jcarousel('reload');

            	CiiDashboard.Settings.bindReload(type);

            }, 'json');

            $('.jcarousel-control-prev').on('jcarouselcontrol:active', function() {
                $(this).removeClass('inactive');
            }).on('jcarouselcontrol:inactive', function() {
                $(this).addClass('inactive');
            }).jcarouselControl({
                target: '-=1'
            });

	        $('.jcarousel-control-next').on('jcarouselcontrol:active', function() {
                $(this).removeClass('inactive');
            }).on('jcarouselcontrol:inactive', function() {
                $(this).addClass('inactive');
            }).jcarouselControl({
                target: '+=1'
            });
		},
	}
};

/**
 * Handles all the Javacript for the Dashboard.
 */
$(document).ready(function() {
	$("#main.nano").nanoScroller();
	CiiDashboard.Settings.load();
});
