/**
 * CiiDashboard
 * A utility class for managing all the behaviors and attributes for managing the dashboard.
 * This has been broken out into a separate file since it no longer requires PHP to operate.
 * @type {Object}
 */
var CiiDashboard = {

	// The dashboard endpoint. We an now access the /dashboard's full path anywhere in js
	endPoint : $("#dashboard-endpoint").attr("value"),

	Default : {

		/**
		 * Because there is a time and a place for silliness
		 * Sets a cheeky greeting for the user
		 */
		loadGreeting : function() {
			var now = new Date();
			var hrs = now.getHours();
			
			if (hrs >=  0 && hrs < 4)
				$(".welcome").html($("#midnight-greeting").html());

			if (hrs >= 4 && hrs < 6)
				$(".welcome").html($("#early-greeting").html());

			if (hrs >= 6)
				$(".welcome").html($("#morning-greeting").html());

			if (hrs >= 12)
				$(".welcome").html($("#afternoon-greeting").html());

			if (hrs >= 17)
				$(".welcome").html($("#evening-greeting").html());

			if (hrs >= 22)
				$(".welcome").html($("#late-greeting").html());
		},

		/**
		 * Loads everything necessary to bootstrap ShapeShift
		 */
		loadIndex : function() {
			// Sets the cheeky greeting
			CiiDashboard.Default.loadGreeting();

			// Init ShapeShift
			$.get(CiiDashboard.endPoint + "/card/getCards", function(data) {
				var newData = $.parseHTML(data);

		 		$(".widget-container").html(data).shapeshift({
					minColumns: 3,
					gutterX: 20,
					gutterY: 20,
					paddingX: 0,
					paddingY: 0
				});

				CiiDashboard.Default.loadScripts(newData);
				CiiDashboard.Default.enableDragBehavior();
				CiiDashboard.Default.bindDeleteBehavior();
				CiiDashboard.Default.bindSettingsBehavior();
				CiiDashboard.Default.bindFlipEvent();
		 	});


			// Bind Add Card click functionality
			$("#add-card").click(function(e) {
				e.preventDefault();
				$(".widget-selector").toggleClass("hidden");

				$(".menu li:first-child").addClass("active");

				$.post(CiiDashboard.endPoint + "/default/getCardsByCategory/id/" + $(".menu li:first-child").find("a").attr("href").replace("#", ""), function(data) {
					$(".body-content #main .content").html(data);
					$("select").imagepicker();
					CiiDashboard.Default.bindAddCardsButton();
				});

				return false;
			});

			// Allow menu clicks of add card menu to also trigger the click event
			$(".menu li").click(function() { 
				window.location = $(this).find("a").attr("href");

				$(".menu li").each(function() {
					$(this).removeClass("active");
				});

				$(this).addClass("active");

				$.post(CiiDashboard.endPoint + "/default/getCardsByCategory/id/" + $(this).find("a").attr("href").replace("#", ""), function(data) {
                                        $(".body-content #main .content").html(data);
                                        $("select").imagepicker();
                                        CiiDashboard.Default.bindAddCardsButton();
                                });
			});
		},
		
		/**
		 * Stores scripts that we have already loaded. Using this prevents unecessary HTTP Requests
		 * to the server to pick up the script
		 * @type {Array}
		 */
		loadedScripts : [],

		/**
		 * Loads the scripts for a loaded cards
		 * @param  DOM Html data Data to iterate through to find scripts to load
		 */
		loadScripts : function(data) {
			$(data).each(function() {
				if ($(this).hasClass("base-card"))
					CiiDashboard.Default.loadScript($(this).attr("data-attr-js"), $(this).attr("data-attr-js-name"), $(this).attr("id"));
			});
		},

		/**
		 * Loads a particular script for a given card.
		 *
		 * It's VERY important that this script have no syntax errors, as this will silently fail otherwise
		 * @param  string script    The path to the script
		 * @param  string name      The name of the method to be called. The Card should know what it's calling it's js file
		 * @param  string id        The id of the card.
		 */
		loadScript : function(script, name, id) {

			if (CiiDashboard.Default.loadedScripts.indexOf(script) == -1)
			{
				CiiDashboard.Default.loadedScripts.push(script);
				$.getScript(script, function(scrpt, textStatus, jqXHR) {
					window[name].load(id, name);
				});
			}
			else
			{
				var timerInterval = setInterval(function() {
					try { 
						window[name].load(id, name);
						clearInterval(timerInterval);
					} catch (e) {}
				}, 500)
			}
		},

		load : function() {},

		/**
		 * Rebuilds the dashboard
		 * @param  boolean   draggable  Whether or not the dashboard should have dragging enabled
		 */
	 	rebuild : function(draggable)
	 	{
	 		$(".widget-container").trigger("ss-destroy").trigger("ss-rearrange").shapeshift({
		        minColumns: 3,
		        gutterX: 20,
		        gutterY: 20,
		        paddingX: 0,
		        paddingY: 0,
		        enableDrag : draggable
	        });

	        $(".widget-container").on("ss-rearranged", function() {
	        	var cards = [];
	        	$(".widget-container > div.base-card").each(function() {
	        		cards.push($(this).attr("id"));
	        	});

	        	$.post(CiiDashboard.endPoint + "/card/rearrange", { "cards" : cards }, function(data) {
	        		console.log(data);
	        	});
	        })
	 	},

	 	/**
	 	 * Binds add card button behavior
	 	 */
		bindAddCardsButton : function()
	 	{
	 		$("#add-cards-button").click(function(e) {
	 			e.preventDefault();

	 			var items = $("select").val();
	 			if (items === null)
	 				return false;

 				$.post(CiiDashboard.endPoint + "/card/add/id/" + items, function(data) {
 					$(".widget-container").append(data);
 					CiiDashboard.Default.enableDragBehavior();
 					CiiDashboard.Default.bindResizeBehavior();
					CiiDashboard.Default.bindDeleteBehavior();
					CiiDashboard.Default.bindSettingsBehavior();
					CiiDashboard.Default.bindFlipEvent();
					CiiDashboard.Default.rebuild(true);
 					$("#add-card").click();
 				});
	 		});
	 	},

	 	/**
	 	 * Bind the setting functionality on .icon-gear for every card
	 	 * This allows the settings-modal to appear and be managed
	 	 */
	 	bindSettingsBehavior : function()
	 	{
	 		// Shader management
	 		$(".shader").click(function() {
	 			$(this).fadeOut();
	 			$(".modal").fadeOut();
	 		});

	 		// When the gear icon is pressed, show the modal
	 		$(".icon-gear").click(function() {

	 			// Stupid dragging behavior screws up stuff at odd z-indexes
	 			CiiDashboard.Default.rebuild(false);

	 			var parent = $(this).parent().parent();

	 			var id = $(parent).attr("id");
	 			var modal = $("." + id + "-modal");

	 			$(".widget-container").after(modal);

	 			$(".shader").fadeIn();
				$(modal).fadeIn();
	 		});

	 		// Overloads the form behavior so that cards submit data over Ajax instead of sending them via regular POST
	 		// The endpoint doesn't support regular POSTing
	 		$("form").submit(function(e) {
	 			e.preventDefault();

	 			var data = $(this).serialize();
	 			var parent = $(this).parent();
	 			var id = $(parent).attr("data-attr-id");

	 			$.post($(this).attr("action"), data, function(data, textStatus) {
	 				if (textStatus == "success")
	 				{
	 					// Hide the shader
	 					$(".shader").click();

	 					// Reload the card...?
	 					var card = null;

	 					$.get(CiiDashboard.endPoint + "/card/card/id/" + id, function(data) {
	 						card = $.parseHTML(data);

	 						// Determine a way to load scripts
	 						CiiDashboard.Default.loadScripts(card);

	 						$("." + id + "-settings").remove();
	 						$("." + id + "-modal").remove();
	 						$("#" + id).replaceWith(data);

	 						CiiDashboard.Default.rebuild();
	 						CiiDashboard.Default.bindResizeBehavior();
							CiiDashboard.Default.bindDeleteBehavior();
							CiiDashboard.Default.bindSettingsBehavior();
							CiiDashboard.Default.bindFlipEvent();
	 					});
	 				}
	 				else
	 				{
	 					// TODO: Handle errors better
	 					console.log("Something has gone horribly wrong...");
	 				}

	 				// Always. Relase. The Modal
	 				CiiDashboard.Default.rebuild(false);

	 			});
	 		});
	 	},

	 	/**
	 	 * Binds the resizing behavior for a given card
	 	 * Each card has a given set of sizes that it can function at, this allows us to toggle between the various acceptable sizes
	 	 */
	 	bindResizeBehavior : function()
	 	{
			$(".icon-resize-full").click(function() {
				var parent = $(this).parent().parent();
				var sizes = $(parent).attr("data-attr-sizes").split(",");
				var classEl = $(parent).attr("class").replace("card-", "").replace("ss-active-child", "").replace(/\s+/g, "");
				
				var i = sizes.indexOf(classEl);

				if (sizes.length - 1 == i)
					var newClass = "card-" + sizes[0];
				else
					var newClass = "card-" + sizes[i+1];

				if (newClass == "card-normal")
					$(parent).attr("data-ss-colspan", "1");
				else
					$(parent).attr("data-ss-colspan", "2");

				$(parent).removeClass("card-" + classEl).addClass(newClass);

				$(".widget-container").trigger("ss-rearrange");

				$.post(CiiDashboard.endPoint + "/card/resize/id/" + $(parent).attr("id"), { activeSize : newClass});
			});
	 	},

	 	/**
	 	 * Binds delete behavior to every card
	 	 */
	 	bindDeleteBehavior :function()
	 	{
	 		$(".icon-trash").click(function()
	 		{
	 			var parent = $(this).parent().parent();

	 			$.post(CiiDashboard.endPoint + "/card/delete/id/" + $(parent).attr("data-attr-id"), function(data, textStatus) {
	 				if (textStatus == "success")
	 				{
	 					$(parent).fadeOut();
	 					setTimeout(function() { 
	 						$(parent).remove(); 
	 						$("#" + $(parent).attr("data-attr-id")).remove(); 
	     					CiiDashboard.Default.enableDragBehavior();
	     					$(".widget-container").trigger("ss-rearrange");
	 					}, 500);

	 				}
	 			});
	 		});
	 	},

	 	/**
	 	 * Verifies that the dashboard can be in a draggable state. This it to alleviate a weird bug where the dashboard will clone a card if
	 	 * it is in a flipped state
	 	 */
	 	enableDragBehavior : function()
		{
			// Prevent dragging until all settings menus are hidden
			var visible = false; 
			$(".settings").each(function() { 
				if (!visible && $(this).is(":visible"))
					visible = true; 
			});

			if (!visible)
				CiiDashboard.Default.rebuild(true);
		},

		/**
		 * Binds the Flip Behavior for every card
		 * Cards have the ability to do a cool flip effect to view some _basic_ settings about it
		 */
		bindFlipEvent : function()
		{
	    	$(".icon-flip").click(function() {
	    		var parent = $(this).parent().parent();
	    		
	    		var settings = $("." + $(parent).attr("id") + "-settings");

	    		$(parent).flippy({
			 		color_target : "#FFF",
				    duration: "500",
				    verso: $(settings),
				    onFinish : function() {
				    	CiiDashboard.Default.bindResizeBehavior();
						CiiDashboard.Default.bindDeleteBehavior();
						CiiDashboard.Default.bindSettingsBehavior();
						CiiDashboard.Default.bindFlipEvent();
				    	$(settings).show();

				    	CiiDashboard.Default.rebuild(false)

				    },
				    onReverseStart : function() {
				    	$(parent).after($(settings));
				    	$(settings).hide();

				    },
				    onReverseFinish : function() {
				    	CiiDashboard.Default.bindResizeBehavior();
						CiiDashboard.Default.bindDeleteBehavior();
						CiiDashboard.Default.bindSettingsBehavior();
						CiiDashboard.Default.bindFlipEvent();
						CiiDashboard.Default.enableDragBehavior();
				    }
				 });
	    	});

	        $(".icon-reverse-flip").click(function() {
	        	var parent = $(this).parent().parent().parent();
	        	$(parent).flippyReverse();
	        });
		}
	}
};

/**
 * Handles all the Javacript for the Dashboard.
 */
$(document).ready(function() {
	// Bind nanoscrollers
	$("#main.nano").nanoScroller();
});
