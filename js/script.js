$(document).ready(function() {

	var uri = window.location.pathname.replace('html', 'md'),
		file = "https://rawgit.com/charlesportwoodii/CiiMS/master/README.md";

	$.get("/partials/nav.html", function(html) {
		$("ul.main-nav").replaceWith(html);
	});	

	if (uri != "/")
		file = "/md"+uri;

	Flatdoc.run({
	  fetcher: Flatdoc.file(file)
	});
});
