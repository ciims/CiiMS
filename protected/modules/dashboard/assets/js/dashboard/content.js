var CiiDashboard = {

	// The javascript endpoint
	endPoint : $("#dashboard-endpoint").attr("value"),

	Content : {

		// Function for handling perspective changes
		bindPerspectiveChange : function() {
			$("#perspective").click(function(e) {
	            $.post(CiiDashboard.endPoint + "/content/index/perspective/" + CiiDashboard.Content.nextPerspective(), function() { 
	                window.location.reload();
	            });
	        });
		},

		// The current perspective
		currentPerspective : $("#currentPerspective").attr("value"),

		// What the next perspective should be
		nextPerspective : function() {
			return CiiDashboard.Content.currentPerspective == 1 ? 2 : 1
		},

		load : function() {},

		// Loaded on /content/index
		loadIndex: function() {

			// Bind the perspective
			CiiDashboard.Content.bindPerspectiveChange();

			// And apply the appropriate data for that perspective
			if (CiiDashboard.Content.currentPerspective == 1)
				CiiDashboard.Content.loadFuturePerspetive();
			else
				CiiDashboard.Content.loadOldPerspective();
		},

		// Loads content necessary for the Future Dashboard Perspective
		loadFuturePerspetive : function() {
			CiiDashboard.Content.futurePerspective.marked();
			CiiDashboard.Content.futurePerspective.bindPostClick();
		},

		/**
		 * All functionallity related to the _future_ perspective is held here
		 * All methods and objects
		 */
		futurePerspective : {

			contentPane : null,

			// BeforeAjaxUpdate for ContentListView::beforeAjaxUpdate
			beforeAjaxUpdate : function() {
				previewPane = $("#preview .content");
	    		scrollTop = $("#preview .content").scrollTop();
	    		CiiDashboard.Content.futurePerspective.contentPane = $(".preview").html();
			},

			/**
			 * When a given post is clicked, the active class from all other items should be removed
			 * and that post should be loaded in the preview pane
			 */
			bindPostClick : function() {
				$(".post").click(function() { 
					if ($(this).hasClass("post-header"))
						return;
					
					$(".post").removeClass("active");
					$(this).addClass("active"); 
					var id = $(this).attr("data-attr-id");

					var url = CiiDashboard.endPoint + "/content/index/id/" + id;

					$.get(url, function(data, textStatus, jqXHR) {
						CiiDashboard.Content.futurePerspective.contentPane = $($.parseHTML(data)).find(".preview").html();
						$(".preview").remove();
						$(".posts").after("<div class=\"preview nano\" id=\"preview\"></div>");
						$(".preview").html(CiiDashboard.Content.futurePerspective.contentPane).removeClass("has-scrollbar");
						$("#md-output").html(marked($("#markdown").text()));
						$("#preview.nano").nanoScroller({ OSNativeScrolling: true});

						CiiDashboard.Content.futurePerspective.delete();
					});
				});
			},

			// AfterAjaxUpdate for ContentL:istview::afterAjaxUpdate
			afterAjaxUpdate : function() {

				// Change perspective
				CiiDashboard.Content.bindPerspectiveChange();

				// NanoScroller for main div
		    	$("#posts.nano").nanoScroller({ iOSNativeScrolling: true }); 

		    	// Timeago
		    	$(".timeago").timeago(); 

		    	// Post Click Behavior
		    	CiiDashboard.Content.futurePerspective.bindPostClick(); 

		    	// Reset Preview Pane
		    	$(".preview").remove();
				$(".posts").after("<div class=\"preview nano\" id=\"preview\"></div>");
				$(".preview").html(CiiDashboard.Content.futurePerspective.contentPane).removeClass("has-scrollbar");
				$("#preview.nano").nanoScroller({ OSNativeScrolling: true}); 
				$("#preview .content").animate({
					scrollTop : scrollTop
				}, 0);

				CiiDashboard.Content.futurePerspective.delete();
			},

			// Allows content to be deleted without page refresh
			delete : function() {
				$(".icon-trash").click(function(e) {
					e.preventDefault();
					$.post($(this).attr("href"), function() {
						CiiDashboard.Content.futurePerspective.contentPane = null;
						$(".preview").html("<div class=\"content\"></div>");
						$.fn.yiiListView.update('ajaxListView');
					});
				});
			},

			// Bind the marked.js behavior. This should be universal across the board, so see CiiDashboard.Content.Save.marked()
			marked : function() {
				return CiiDashboard.Content.Save.marked();
			}

		},

		// Loads the necessary scripts for the old perspective
		loadOldPerspective : function() {},

		/**
		 * All functionality related to the older perspective is held here
		 * all objects and methods
		 */
		oldPerspective : {

			// Bulk action control binding
			bulkActionClick : function() {
				 $.post(CiiDashboard.endPoint + "/content/deleteMany", values, function(data) {
                    values.each(function() {
                        $(this).parent().parent().remove();
                    });
                });
			}

		},

		// The scripts that are loaded on /content/save
		loadSave : function() {
			CiiDashboard.Content.Save.tags();
			CiiDashboard.Content.Save.datePicker();
			CiiDashboard.Content.Save.bindFlipEvent();
			CiiDashboard.Content.Save.bindPreviewEditor();
		},

		/**
		 * All functionality related to saving and preview data is stored here
		 */
		Save : {

			// Binds the flip event for the preview => settings pane
			bindFlipEvent : function() {
			 	$(".show-settings").click(function() {
				 	$(".flipbox").flippy({
				 		color_target : "#FFF",
					    duration: "500",
					    verso: $(".settings"),
					    onStart : function() {
					    	$(".nano").nanoScroller({ destroy: true });
					    	$(".nano").removeClass("has-scrollbar");
					    },
					    onFinish : function() {
					    	$(".show-settings").hide();
					    	$(".show-preview").show();
					    	$(".settings").show();
					    	$(".nano").nanoScroller({ flash : true});
					    	CiiDashboard.Content.Save.bindFlipEvent();
					    },
					    onReverseStart : function() {
					    	$(".body-content").after($(".settings"));
					    	$(".nano").nanoScroller({ destroy: true });
					    	$(".nano").removeClass("has-scrollbar");
					    },
					    onReverseFinish : function() {
					    	$(".settings").hide();
					    	$(".show-preview").hide();
					    	$(".show-settings").show();
					    	$(".nano").nanoScroller({ flash : true});
					    	CiiDashboard.Content.Save.bindFlipEvent();
					    }
					 });
				 });

		 		$(".show-preview").click(function() {
			 		$(".flipbox").flippyReverse();
			 	});
			},

			/**
			 * This is the majority of the preview functionality.
			 *
			 * The Preview editor ties into serveral components, namely marked.js and dropzone.js.
			 *
			 * marked.js allows the content to be rendered in near real time with syntax highlighting from highlight.js. 
			 * The use of the "{image}" keyword will instantiate a unique and persistent dropzsone.js instance which will 
			 * be held until an image is dropped in the dropzone.
			 * 
			 * Once the image is uplaoded, it will replace the corresponding "{image}" tage in the markdown editor with
			 * either a markdown syntax image tag ![]() or a <img> tag, depending upon the editor preferences set by the administrator
			 *
			 * There's a WHOLE bunch of complex logix nested into this, that probably could be optimized. It's pretty ugly, but it works magnificently
			 */
			bindPreviewEditor : function() {
				$("#Content_content").bind("input propertychange change", function(event) {

					if(typeof(Storage)!=="undefined")
						localStorage.setItem("content-" + $("#Content_id").val(), $(this).val());
					
					CiiDashboard.Content.Save.marked();

					var markdown = $("<div class=\"md-preview\">" + marked($(this).val()).replace(/{image}/g, "<div class=\"dropzone\"></div>") + "</div>");

					var i = 0;

					$(".preview div.dropzone").each(function() {
						$(markdown).find("div.dropzone:eq(" + i + ")").replaceWith($(this));
						i++;
					});	

					$(".preview").html(markdown);
					$(".nano").nanoScroller();

					$("div.dropzone").each(function() {
						if (!$(this).hasClass("dz-clickable"))
		 				{
		 					// Make sure we do not have a hash collision
		 					var hash = Math.random().toString(36).substring(7);

		 					while ($(".dropzone-" + hash).length > 0)
		 						hash = Math.random().toString(36).substring(15);

							$(this).addClass("dropzone-" + hash);
							var dz = new Dropzone(".preview div.dropzone-" + hash, {
								url : CiiDashboard.endPoint + "/content/upload/id/" + $("#Content_id").val(),
								dictDefaultMessage : "Drop files here to upload - or click",
								success : function(data) {
									var response = $.parseJSON(data.xhr.response);
									if (response.success == true)
									{
										var instance = 0;

										var self = $(this);
										var classEl = "";

										var classes = $(this)[0].element.className.split(" ");
										$(classes).each(function() { 
									        var classElement = this + "";
									        if (classElement != "dropzone" && classElement != "dz-clickable" && classElement != "dz-started")
									        	classEl = classElement
									    });

										// Iterate through all the dropzone objects on the page until this one is reached
										var i = 0;
										$(".preview div.dropzone").each(function() {
											if ($(this).hasClass(classEl))
												return false;
											i++;
										});

										var index = CiiDashboard.Content.Save.GetSubstringIndex($("#Content_content").val(), "{image}", i + 1);

										// Remove the uploader
										$("." + classEl).remove();

										// Append the text to the item at that index
										var md = $("#Content_content").val();

										// Insert either Markdown or an image tag depending upon the user preference
										if ($(".preferMarkdown").val() == 1)
											md = CiiDashboard.Content.Save.splice(md, index, 7, "![" + response.filename + "](" + response.filepath +")");
										else
										{
											var text = $(".redactor_editor").html();
											md = CiiDashboard.Content.Save.splice(md, index, 7, "<img src=\"" + response.filepath +"\" />");
											var index2 = CiiDashboard.Content.Save.GetSubstringIndex($(".redactor_editor").html(), "{image}", i + 1);
											text = CiiDashboard.Content.Save.splice(text, index2, 7, "<img src=\"" + response.filepath +"\" />");
											$(".redactor_editor").html(text);
										}

										// Then modify the markdown
										$("#Content_content").val(md).change();

										// Then change the redactor view if it exists
										

										if(typeof(Storage)!=="undefined")
											localStorage.setItem("content-" + $("#Content_id").val(), md);
									}
								}
							});
		 				}
		 			});
				});

		 		$("#Content_content").change();
			},

			// Binds the datePicker effect
			datePicker : function() {
				$("#Content_published").datetimepicker({
				    format: "yyyy-mm-dd hh:ii:ss",
				    showMeridian: true,
				});
			},

			// Utility method for getting a substring index. This finds the unique instance of {image}
			GetSubstringIndex : function(str, substring, n) {
			    var times = 0, index = null;

			    while (times < n && index !== -1) {
			        index = str.indexOf(substring, index+1);
			        times++;
			    }

			    return index;
			},

			// Binds the marked.js behavior
			marked : function() {
				marked.setOptions({
				    gfm: true,
				    highlight: function (lang, code) {
				      return hljs.highlightAuto(lang, code).value;
				    },
				    tables: true,
				    breaks: true,
				    pedantic: false,
				    sanitize: false,
				    smartLists: true,
				    smartypants: true,
				    langPrefix: "lang-"
				});
			},

			// String splcing madness
			splice : function(str, idx, rem, s) {
			    return (str.slice(0,idx) + s + str.slice(idx + Math.abs(rem)));
			},

			// Binds the tagging behavior
			tags : function() {
				$("#tags").tagsInput({
					defaultText : "Add a Tag",
				    width: "99%",
				    height : "40px",
				    onRemoveTag : function(e) {
				    	$.post(CiiDashboard.endPoint + "/content/removeTag", { id : $("#Content_id").val(), keyword : e });
				    },
				    onAddTag : function(e) {
				    	$.post(CiiDashboard.endPoint + "/content/addTag", { id : $("#Content_id").val(), keyword : e });
				    }
				});
			},

		}
	}
};

/**
 * Handles all the Javacript for User Management
 */
$(document).ready(function() {

	// Bind nanoscrollers
	$("#main.nano").nanoScroller();

	// Load the dashboard
	CiiDashboard.Content.load();
	
});