jQuery(document).ready(function ($) {
  /**
   * Setup external links
   */
  $('.entry a[href^="http://"] , #comments a[href^="http://"] , .entry a[href^="https://"] , #comments a[href^="https://"]').each(function () {

  	if (this.hostname !== location.hostname) {
      $(this).attr({
        'class': function (i, val) {
          val = (val == undefined) ? "" : val;
          return val + " ui-state-default";
        },
        'target': "_blank",
        'title': function (i, val) {
          val = (val == undefined) ? this.href : val;
          return val + " (external link, click to open in a new window)";
        },
        'onclick': function (i, val) {
          return "javascript:ga('send','event', 'Outgoing Links', '" + this.href + "', '" + document.location.pathname + document.location.search + "',  {'nonInteraction': 1});";
        }
      });

      $(this).append("<span class='ui-icon ui-icon-extlink'></span>");

      $(this).hover(
        function () { $(this).addClass("ui-state-hover");}, function () { $(this).removeClass("ui-state-hover");}
      );
    }
  });

  var rps_IMG_select = '.entry a[href*=".jpg"], .entry area[href*=".jpg"], .entry a[href*=".gif"], .entry area[href*=".gif"], .entry a[href*=".png"], .entry area[href*=".png"]';
  $(rps_IMG_select).each(function () {
    $(this).attr({
      'onclick': function (i, val) {
        return "javascript:ga('send','event', 'image', '" + this.title + "', '" + document.location.pathname + document.location.search + "',  {'nonInteraction': 1});";
      }
    });
  });

  var rps_Downloads_select = '.entry a[href*=".pdf"]';
  $(rps_Downloads_select).each(function () {
    $(this).attr({
      'onclick': function (i, val) {
        return "javascript:ga('send','event', 'download', '" + this.title + "', '" + document.location.pathname + document.location.search + "',  {'nonInteraction': 1});";
      }
    });
  });
});
