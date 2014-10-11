(function($, window, document) {
  // The $ is now locally scoped

  // The rest of your code goes here!
  function set_equal_height(selectors) {
    var tallest = 0;
    // Reset all tiles to 0 min-height
    selectors.css({
      'height' : 'auto'
    });
    selectors.each(function() {
      if ($(this).height() > tallest) {
        tallest = $(this).height();
      }
    });
    selectors.css({
      'height' : tallest
    });
  }

  $(function() {
    /**
     * Setup external links
     */
    $('.entry a[href^="http://"] , #comments a[href^="http://"] , .entry a[href^="https://"] , #comments a[href^="https://"]')
        .not('.entry a[href$=".jpg"]')
        .each(function() {

          if (this.hostname !== location.hostname) {
            $(this)
                .attr({
                'class' : function(i, val) {
                  val = (val == undefined) ? "" : val;
                  return val + " ui-state-default";
                },
                'target' : "_blank",
                'title' : function(i, val) {
                  val = (val == undefined) ? this.href : val;
                  return val + " (external link, click to open in a new window)";
                },
                'onclick' : function(i, val) {
                  return "javascript:ga('send','event', 'Outgoing Links', '" + this.href + "', '" + document.location.pathname + document.location.search + "',  {'nonInteraction': 1});";
                }
                });

            $(this).append("<span class='ui-icon ui-icon-extlink'></span>");

            $(this).hover(function() {
              $(this).addClass("ui-state-hover");
            }, function() {
              $(this).removeClass("ui-state-hover");
            });
          }
        });

    var rps_IMG_select = '.entry a[href*=".jpg"], .entry area[href*=".jpg"], .entry a[href*=".gif"], .entry area[href*=".gif"], .entry a[href*=".png"], .entry area[href*=".png"]';
    $(rps_IMG_select)
        .each(function() {
          title = $.url('file', this.href);
          $(this)
              .attr({
                'onclick' : function(i, val) {
                  return "javascript:ga('send','event', 'image', '" + title + "', '" + document.location.pathname + document.location.search + "',  {'nonInteraction': 1});";
                }
              });
        });

    var rps_Downloads_select = '.entry a[href*=".pdf"]';
    $(rps_Downloads_select)
        .each(function() {
          title = $.url(file);
          $(this)
              .attr({
                'onclick' : function(i, val) {
                  return "javascript:ga('send','event', 'download', '" + title + "', '" + document.location.pathname + document.location.search + "',  {'nonInteraction': 1});";
                }
              });
        });
  });

  $(window).load(function() {
    // Set the gallery items in the same row to equal heights
    $('.gallery-row-equal').each(function() {
      set_equal_height($(this).find('.gallery-item-content'));
    });
  });
}(window.jQuery, window, document));
// The global jQuery object is passed as a parameter
