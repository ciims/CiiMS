
/**
 * [CiiDashboard description]
 * @type {Object}
 */
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
			$(".nano").nanoScroller()
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
						$("#md-output").html(marked($("#markdown").val()));
						$("#preview.nano").nanoScroller({ OSNativeScrolling: true});

						CiiDashboard.Content.futurePerspective.delete();
						CiiDashboard.Content.futurePerspective.bindCommentBehavior();
					});
				});
			},

			/**
			 * Binds the ability to view and manage comments for a particular post
			 */
			bindCommentBehavior : function() {
				$(".preview-header .icon-comment").click(function() {
					$(".preview-data").slideToggle(function() {
						$(".preview-image").slideToggle();
						var display = true;
						$("#comments").slideToggle();
						
						if ($("#comments").is(":visible")) {
							$("#comments").remove();
							setTimeout(function() {
									$("#preview.nano").nanoScroller({ destroy: true });
									$("#preview.nano").nanoScroller({ OSNativeScrolling: true}); 
									$("#preview .content").animate({
										scrollTop : 0
									}, 0);
								}, 500);
							return;
						}

						$.post(CiiDashboard.endPoint + "/comment/getComments/id/" + $("#item-id").text(), function(data) {
							$(".preview-data").after("<div id='comments'></div>");
							var mainComments = $("#main-comment").clone();

							$("#main-comment").remove();


							console.log(mainComments);


							CiiDashboard.Content.futurePerspective.loadMainCommentBox();

							$("#comments").html(data).fadeIn(function() {// Show the main comment box
								$("#comments").prepend($(mainComments));
								$("#main-comment").show();
								CiiDashboard.Content.futurePerspective.loadMainCommentBox();

								setTimeout(function() {
									$("#preview.nano").nanoScroller({ destroy: true });
									$("#preview.nano").nanoScroller({ OSNativeScrolling: true}); 
									$("#preview .content").animate({
										scrollTop : 0
									}, 0);


								}, 500);
							});

							// Rounded Image 
							$(".rounded-img").load(function() {
							    $(this).wrap(function(){
							      return '<span class="' + $(this).attr('class') + '" style="background:url(' + $(this).attr('src') + ') no-repeat center center; width: ' + $(this).width() + 'px; height: ' + $(this).height() + 'px;" />';
							    });
							    $(this).css("opacity","0");
							});
						});
					});
				})
			},

			/**
			 * Loads a main comment box that isn't bound to any particular comment
			 * @return {[type]} [description]
			 */
			loadMainCommentBox : function() {
				$("#b").click( function () {
			        $(this).html("");
			        $("#a").slideDown("fast");
			        $("#submit-comment").show();
			        setTimeout(function() {
			            $("#textbox").focus();
			        }, 100);
			    });

			    $("#textbox").keydown( function() {
			        if($(this).text() != "")
			            $("#submit-comment").css("background","#3b9000");
			        else
			            $("#submit-comment").css("background","#9eca80");
			        });
			    $("#close").click( function () {
			        $("#b").html("Comment on this post");
			        $("#textbox").html("");
			        $("#a").slideUp("fast");
			        $("#submit-comment").hide();
			    });
			    
			    $("#submit-comment").click(function(e) {
			        e.preventDefault();
			        if ($("#textbox").text() == "")
			            return;
			        $.post(CiiDashboard.endPoint + "/comment/comment", 
			        	{ 
			        		"Comments" : 
			        		{ 
			        			"comment" : $("#textbox").html(), 
			        			"content_id" : $("#item-id").text()
			        		}
			        	}, 
			        	function(data) { 
			        		$("#textbox").text("");
			        		$("#close").click();
			        		$("#main-comment").after(data);
			        		var count = (parseInt($(".post.active").find(".comments strong").text()) + 1);
			        		$(".post.active").find(".comments strong").text(count);
			        	}
			        );
			    });
			},

			/**
			 * Loads appropriate comment data
			 **/
			loadComment : function(id) {
				// Reply
				$(".reply-" + id).click(function() {
					$(".comment-form-" + id).slideToggle(200);
				});

				// Delete
				$(".delete-" + id).click(function() {
					$.post(CiiDashboard.endPoint + "/comment/delete/id/" + id, function() {
						$(".comment-" + id).fadeOut(function() {
							$(this).remove();
							var count = (parseInt($(".post.active").find(".comments strong").text()) + -1);
			        		$(".post.active").find(".comments strong").text(count);
						});
					})
				});

				// Comment form
				$("#b-" + id).click( function () {
			        $(this).html("");
			        $("#a-" + id).slideDown("fast");
			        $("#submit-comment-" + id).show();
			        setTimeout(function() {
			            $("#textbox-" + id).focus();
			        }, 100);
			    });

			    $("#textbox-" + id).keydown( function() {
			        if($(this).text() != "")
			            $("#submit-comment-" + id).css("background","#3b9000");
			        else
			            $("#submit-comment-" + id).css("background","#9eca80");
			        });
			    $("#close-" + id).click( function () {
			        $("#b-" + id).html("Comment on this post");
			        $("#textbox-" + id).html("");
			        $("#a-" + id).slideUp("fast");
			        $("#submit-comment-" + id).hide();
			    });

			    // Submit
			    $("#submit-comment-" + id).click(function(e) {
			    	var elementId = $(this).attr('id').replace('submit-comment-', '');
		        	e.preventDefault();
			        if ($("#textbox-" + id).text() == "")
			            return;

			        $.post(CiiDashboard.endPoint + "/comment/comment", 
			        	{ 
			        		"Comments" : 
			        		{ 
			        			"comment" : $("#textbox-" + id).html(), 
			        			"content_id" : $("#item-id").text(),
			        			"parent_id" : elementId
			        		}
			        	}, 
			        	function(data, textStatus, jqXHR) { 
			        		$("#textbox-" + id).text("");  
			        		// PREPEND DATA
			        		var newElementId = jqXHR.getResponseHeader("X-Attribute-Id");
			        		$(".comment-" + elementId).append(data);
			        		$(".comment-" + newElementId).fadeIn();

			        		$("#close-" + id).click();
			        		$(".reply-" + id).click();
			        		var count = (parseInt($(".post.active").find(".comments strong").text()) + 1);
			        		$(".post.active").find(".comments strong").text(count);
			        	}
			        );
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
					confirm = confirm("Are you sure you want to delete this item?");
					if (confirm==true)
					{
						$.post($(this).attr("href"), function() {
							CiiDashboard.Content.futurePerspective.contentPane = null;
							$(".preview").html("<div class=\"content\"></div>");
							$.fn.yiiListView.update('ajaxListView');
						});
					}
					delete confirm;
					return false;
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

			/**
			 * Binds promoted Dropzone functionality for promoted images
			 */
			bindPromotedDz : function() {
				$(".icon-camera").click(function() {
					$("#promotedDz").toggle();
				});

				var dz = new Dropzone("#promotedDz", {
						url : CiiDashboard.endPoint + "/content/upload/id/" + $("#Content_id").val() + "/promote/1",
						dictDefaultMessage : "Drop files here to upload promoted image - or click",
						success : function(data) {
							var json = $.parseJSON(data.xhr.response);
							$(".preview-image").attr("src", CiiDashboard.endPoint.replace("/dashboard", "") + json.filepath);
							$(".icon-camera").click();
							$("#promotedDz").remove();
							$(".editor .content").prepend($("<div id='promotedDz' class='dropzone'></div>").hide());
							CiiDashboard.Content.Save.bindPromotedDz();

							$(".icon-camera").unbind("click").click(function() {
								$("#promotedDz").toggle();
							});
						}
					});
			},

			/**
			 * Provides a delete confirmation box for content
			 */
			bindDelete : function() {
				$(".icon-trash").click(function() {
					confirm = confirm("Are you sure you want to delete this item?");
					if (confirm==true)
						window.location = CiiDashboard.endPoint + "/content/delete/id/" + $("#Content_id").val();

					delete confirm;
				})
			},

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

				CiiDashboard.Content.Save.bindPromotedDz();
				CiiDashboard.Content.Save.bindDelete();

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
										if (index == '-1')
											index = 0;

										// Remove the uploader
										$("." + classEl).remove();

										// Append the text to the item at that index
										var md = $("#Content_content").val();

										// Insert either Markdown or an image tag depending upon the user preference
										if ($(".preferMarkdown").val() == 1)
											md = CiiDashboard.Content.Save.splice(md, index, 7, "![" + response.filename + "](" + CiiDashboard.endPoint.replace("/dashboard", "") + response.filepath +")");
										else
										{
											var text = $(".redactor_editor").html();
											md = CiiDashboard.Content.Save.splice(md, index, 7, "<img src=\"" + CiiDashboard.endPoint.replace("/dashboard", "") + response.filepath +"\" />");
											var index2 = CiiDashboard.Content.Save.GetSubstringIndex($(".redactor_editor").html(), "{image}", i + 1);
											if (index2 == -1)
												index2 = CiiDashboard.Content.Save.GetSubstringIndex($(".redactor_editor").html(), "{image}", i);
											text = CiiDashboard.Content.Save.splice(text, index2, 7, "<img src=\"" + CiiDashboard.endPoint.replace("/dashboard", "") + response.filepath +"\" />");
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
 * Handles all the Javacript for the Dashboard.
 */
$(document).ready(function() {

	CiiDashboard.Content.load();
	
});