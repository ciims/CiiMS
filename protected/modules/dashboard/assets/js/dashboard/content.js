
/**
 * [CiiDashboard description]
 * @type {Object}
 */
var CiiDashboard = {

	// The javascript endpoint
	endPoint : $("#dashboard-endpoint").attr("value"),

	/**
	 * Neat little function that lets us know if a given element is on screen or not
	 * @see https://github.com/benpickles/onScreen
	 * @see http://stackoverflow.com/questions/14986738/when-a-div-comes-into-view-add-class
	 * @author Ben Pickles <https://github.com/benpickles> 
	 * @license MIT License <https://github.com/benpickles/onScreen/blob/master/LICENCE>
	 */
	isOnScreen : function(elem) {
		var $window = $(window);
		var viewport_top = $window.scrollTop();
		var viewport_height = $window.height();
		var viewport_bottom = viewport_top + viewport_height;
		var $elem = $(elem);
		var top = $elem.offset().top;
		var height = $elem.height();
		var bottom = top + height;

		return (top >= viewport_top && top < viewport_bottom) ||
			   (bottom > viewport_top && bottom <= viewport_bottom) ||
			   (height > viewport_height && top <= viewport_top && bottom >= viewport_bottom);
	},

	Content : {

		ajaxUpdateTimeout : null,

		ajaxRequest : null,

		// Loaded on /content/index
		loadIndex: function() {
			CiiDashboard.Content.Preview.load();
			CiiDashboard.Content.bindSearch();
		},

		// Adds functionality for Ajax powered searching
		bindSearch : function() {

			$("form").submit(function(e) { 
				e.preventDefault();
		        $('input#Content_title').keyup();
			});

		    $('input#Content_title').keyup(function(){
		        CiiDashboard.Content.ajaxRequest = $(this).serialize();
		        clearTimeout(CiiDashboard.Content.ajaxUpdateTimeout);
		        CiiDashboard.Content.ajaxUpdateTimeout = setTimeout(function () {
		            $.fn.yiiListView.update('ajaxListView', { 
			       		data: $('input#Content_title').serialize(),
			       		url : CiiDashboard.endPoint + '/content/index',
			       	});
		        },
		        300);
		    });
		},

		/**
		 * All functionallity related to the _future_ perspective is held here
		 * All methods and objects
		 */
		Preview : {
			
			// The content pane variable stores data about the preview window so Ajax events aren't visually disturbing to the user
			// eg, you can change to a new page without screwing up the preview window
			contentPane : null,

			// The current page of infScroll
			currentPage : 1,

			// Whether or not the last page has been loaded or not
			isLastPageLoaded : false,

			// Allows pagination to occur
			allowPagination : true,

			// Stores the last md5 hash from inf pagination
			lastMD5 : null,

			// ScrollTop
			scrollTop : null,

			// IE11 Computed Height fix
			computedHeight : 0,

			// Consolodating Preview base functions that should be loaded
			load : function() {
				CiiDashboard.Content.Preview.marked();
				CiiDashboard.Content.Preview.bindPostClick();
				CiiDashboard.Content.Preview.bindScrollEvent();
				$(".nano").nanoScroller();
			},

			// Binds infinite scrolling behavior to the content page. This is preferable to Ajax Pagination
			bindScrollEvent : function() {

				// This event should only fire if we haven't loaded the last page	
				if (!CiiDashboard.Content.Preview.isLastPageLoaded) {
					// When the user is scrolling the list of articles
					$(".posts .content").scroll(function() {
						if (CiiDashboard.Content.Preview.isLastPageLoaded || !CiiDashboard.Content.Preview.allowPagination)
							return;

						if(CiiDashboard.isOnScreen($(".post").last())) {

							// Disable pagination updates while we load the next page
							CiiDashboard.Content.Preview.allowPagination = false;

							CiiDashboard.Content.Preview.getPages(CiiDashboard.Content.Preview.currentPage + 1);
						}
					});
				}
			},

			getPages : function(page) {
				$.get(CiiDashboard.endPoint + "/content/index/Content_page/" + (page), function(data) { 
					var response;
					try {
						response = $(data);
					} catch (e) {
						response = $.parseHTML(data);
					}

					var hash = md5($(response).find(".posts .content").html())
					if (hash == CiiDashboard.Content.Preview.lastMD5) {
						CiiDashboard.Content.Preview.isLastPageLoaded = true;
						CiiDashboard.Content.Preview.allowPagination = false;
						return;
					}

					CiiDashboard.Content.Preview.lastMD5 = hash;

					$(".posts.nano").nanoScroller({ destroy: true });
					var posts = $(response).find(".posts .content");
					$(posts).find(".post-header").remove();
					$(".posts .content").append($(posts).html());
					$(".posts.nano").nanoScroller({ iOSNativeScrolling: true }); 
					CiiDashboard.Content.Preview.currentPage++;

					CiiDashboard.Content.Preview.beforeAjaxUpdate();
					CiiDashboard.Content.Preview.afterAjaxUpdate();

					CiiDashboard.Content.Preview.allowPagination = true;
				});

			},

			// AfterAjaxUpdate for ContentL:istview::afterAjaxUpdate
			afterAjaxUpdate : function() {

				CiiDashboard.Content.bindSearch();

				CiiDashboard.Content.Preview.bindComment();

				CiiDashboard.Content.Preview.bindScrollEvent();
				$("input").focus();

				// NanoScroller for main div
		    	$(".posts.nano").nanoScroller({ iOSNativeScrolling: true, flash : true }); 

		    	// Timeago
		    	$(".timeago").timeago(); 

		    	// Post Click Behavior
		    	CiiDashboard.Content.Preview.bindPostClick(); 

		    	// Reset Preview Pane
		    	$(".preview").remove();
				$(".sorter").after("<div class=\"preview nano\" id=\"preview\"></div>");
				$(".preview").html(CiiDashboard.Content.Preview.contentPane).removeClass("has-scrollbar");
				$("#preview.nano").nanoScroller({ OSNativeScrolling: true, flash : true}); 
				$("#preview .content").animate({
					scrollTop : CiiDashboard.Content.Preview.scrollTop
				}, 0);

				CiiDashboard.Content.Preview.delete();
			},

			// BeforeAjaxUpdate for ContentListView::beforeAjaxUpdate
			beforeAjaxUpdate : function() {
				previewPane = $("#preview .content");
	    		CiiDashboard.Content.Preview.scrollTop = $("#preview .content").scrollTop();
	    		CiiDashboard.Content.Preview.contentPane = $(".preview").html();
			},

			/**
			 * When a given post is clicked, the active class from all other items should be removed
			 * and that post should be loaded in the preview pane
			 */
			bindPostClick : function() {

				CiiDashboard.Content.Preview.computedHeight = $("#preview").css("height").replace("px", "");

				$(".post").unbind("click");
				$(".post").click(function() { 
					if ($(this).hasClass("post-header"))
						return;
					
					$(".post").removeClass("active");
					$(this).addClass("active"); 
					var id = $(this).attr("data-attr-id");

					var url = CiiDashboard.endPoint + "/content/index/id/" + id;
					
					$.get(url, function(data, textStatus, jqXHR) {

						// Sometimes one of these works, sometimes the other does. Possible OS/Browser issue
						try {
							CiiDashboard.Content.Preview.contentPane = $($.parseHTML(data)).find(".preview").html();
						} catch (Exception) {
							CiiDashboard.Content.Preview.contentPane = $(data).find(".preview").html();
						}

						$(".preview").remove();
						$(".sorter").after("<div class=\"preview nano\" id=\"preview\"></div>");
						$("#preview").html(CiiDashboard.Content.Preview.contentPane).removeClass("has-scrollbar");

						$("#md-output").html(marked($("#markdown").val()));
						$("#preview.nano").nanoScroller({ OSNativeScrolling: true});

						CiiDashboard.Content.Preview.delete();
						CiiDashboard.Content.Preview.bindComment();

						if ($("#disqus_shortname").text() != "") {
							CiiDashboard.Content.Preview.loadDisqus();
							$(".comment-box-main").show();
						}

						if ($("#preview").css("height") == "0px")
						{
							$("#preview").css("height", CiiDashboard.Content.Preview.computedHeight +"px");
							$(".content-sidebar").css("height", CiiDashboard.Content.Preview.computedHeight +"px");
						}
						
					});
				});
			},

			// Allows comments to be displayed
			bindComment : function() {
				$(".content-sidebar").find(".comment-container").html(null);
				if ($(".content-sidebar").is(":visible")) {
					var id = $(".preview-container").find("#item-id").text();
					Comments.reload(id);
					CiiDashboard.Content.Preview.bindNano();
				}

				$(".icon-comment").click(function() {
					$(".preview-container").toggleClass("active", function() {
						if ($(this).hasClass("active")) {
							var id = $(".preview-container").find("#item-id").text();
							Comments.reload(id);
							CiiDashboard.Content.Preview.bindNano();
						}
					});
				})
			},

			bindNano : function() {
					setTimeout(function() {
						$("div.comments").addClass("nano");
						$("div.comments").children().first().addClass("content");
						$("div.comments").nanoScroller({ OSNativeScrolling: true, flash : true}); 
						$("div.comments").animate({
							scrollTop : CiiDashboard.Content.Preview.scrollTop
						}, 0);
					}, 200);
			},

			// Allows content to be deleted without page refresh
			delete : function() {
				$(".icon-trash").click(function(e) {
					e.preventDefault();
					confirm = confirm("Are you sure you want to delete this item?");
					if (confirm==true)
					{
						var self = this;
						$.post($(this).attr("href"), function() {
							CiiDashboard.Content.Preview.contentPane = null;
							$(".preview").html("<div class=\"content\"></div>");

							var scrollPosition = $(".posts.nano .content").scrollTop();
							
							$.fn.yiiListView.update('ajaxListView', { 
					       		data: null,
					       		url : CiiDashboard.endPoint + '/content/index'
					       	});

					       

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

		// The scripts that are loaded on /content/save
		loadSave : function() {
			CiiDashboard.Content.Save.tags();
			CiiDashboard.Content.Save.datePicker();
			CiiDashboard.Content.Save.bindFlipEvent();
			CiiDashboard.Content.Save.bindPreviewEditor();

			setTimeout(function() { 
				$(".redactor_box").height($(window).height() - 250); 
				$(".redactor_editor").height($(".editor").height()-80);
				var isChrome = /chrom(e|ium)/.test(navigator.userAgent.toLowerCase()); 

				if(!isChrome) {
					$(".redactor_editor").height($(".editor").height()+30);
					$(".editor").height($(".content-container").height()-80);
					$(".body-content").height($(".content-container").height()-80);
				}

			}, 200);
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
					    	$(".settings").hide();
					    },
					    onReverseFinish : function() {
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

				var autosaveTimeout;
				$("#Content_content").bind("input propertychange change", function(event) {

					//if(typeof(Storage)!=="undefined")
					//	localStorage.setItem("content-" + $("#Content_id").val(), $(this).val());

					// Attempt to save the document via Ajax
					clearTimeout(autosaveTimeout);
					autosaveTimeout = setTimeout(function() {
						$.post('', $("form").serialize(), function(data) {
							$("#Content_vid").val(data.vid);
						})
					}, 3000);

					
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
										

										//if(typeof(Storage)!=="undefined")
										//	localStorage.setItem("content-" + $("#Content_id").val(), md);
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
				var dateStr = $("#Content_published").val();

				var a=dateStr.split(" ");
				var d=a[0].split("-");
				var t=a[1].split(":");
				var date = new Date(Date.UTC(d[0],(d[1]-1),d[2],t[0],t[1],t[2]));
				var format = date.format('Y-m-d H:i:s');
				
				$("#Content_published").val(format);

				var tz = jstz.determine(); // Determines the time zone of the browser client
    	
				$("#timezone").val(tz.name());
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
