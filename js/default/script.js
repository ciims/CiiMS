$(document).ready(function(){
	$('.image, .featured-image, .image-alignleft,.image-alignright, .one-column-picture, .two-column-picture, .three-column-picture, .four-column-picture, .gallery-picture').fadeTo("slow", 1.0); // This sets the opacity of the thumbs to fade down to 30% when the page loads
	$('.image, .featued-image, .image-alignleft,.image-alignright, .one-column-picture, .two-column-picture, .three-column-picture, .four-column-picture, .gallery-picture').hover(function(){
	$(this).fadeTo("slow", 0.40); // This should set the opacity to 100% on hover
	
	},function(){
		$(this).fadeTo("slow", 1.0); // This should set the opacity back to 30% on mouseout
	});
	
});

