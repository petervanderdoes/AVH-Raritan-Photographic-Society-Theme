(function($, window, document) {
    // The $ is now locally scoped
    $(document).ready(function() {
	$('.gallery-masonry').masonry({
	    itemSelector : '.gallery-item-masonry',
	    columnWidth : '.grid-sizer',
	    isFitWidth : true
	}).imagesLoaded(function() {
	    $('.gallery-masonry').masonry('reloadItems');
        $('.gallery-masonry').masonry();
	});
    });
}(window.jQuery, window, document));
