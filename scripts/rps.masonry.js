var galleries = document.querySelectorAll('.gallery-masonry');
for ( var i=0, len = galleries.length; i < len; i++ ) {
  var gallery = galleries[i];
  initMasonry( gallery );
}
function initMasonry( container ) {
  var imgLoad = imagesLoaded( container, function() {
    new Masonry( container, {
      itemSelector: '.gallery-item-masonry',
      columnWidth: '.gallery-item-masonry',
      gutter: 5,
      isFitWidth: true
    });
  });
}
