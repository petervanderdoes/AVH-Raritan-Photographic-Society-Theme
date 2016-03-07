(function ($) {
  // The $ is now locally scoped
  $(function () {
    /**
     * Setup external links
     */
    $('.entry a[href^="http://"] , #comments a[href^="http://"] , .entry a[href^="https://"] , #comments a[href^="https://"]')
      .not('.entry a[href$=".jpg"]')
      .each(function () {
        if (this.hostname !== location.hostname) {
          $(this)
            .attr({
              'class': function (i, val) {
                val = (val === undefined) ? '' : val + ' ';
                return val + 'ui-state-default';
              },
              'target': '_blank',
              'title': function (i, val) {
                val = (val === undefined) ? this.innerHTML : val;
                return val + ' (external link, click to open in a new window)';
              },
              'onclick': function () {
                return TrackClick('Outgoing Links', this.href, this.innerHTML);
              }
            });
          $(this).append('<span class=\'ui-icon ui-icon-extlink\'></span>');
          $(this).hover(function () {
            $(this).addClass('ui-state-hover');
          }, function () {
            $(this).removeClass('ui-state-hover');
          });
        }
      });
    var rps_IMG_select = '.entry a[href*=".jpg"], .entry area[href*=".jpg"], .entry a[href*=".gif"], .entry area[href*=".gif"], .entry a[href*=".png"], .entry area[href*=".png"]';
    $(rps_IMG_select)
      .each(function () {
        $(this)
          .attr({
            'onclick': function () {
              return TrackClick('Images', this.href, this.title);
            }
          });
      });
    var rps_Downloads_select = '.entry a[href*=".pdf"]';
    $(rps_Downloads_select)
      .each(function () {
        $(this)
          .attr({
            'onclick': function () {
              return TrackClick('Downloads', this.href, this.innerHTML);
            }
          });
      });
  });
}(window.jQuery));
// The global jQuery object is passed as a parameter
/**
 * @return {string}
 */
function TrackClick
(eventCategory, eventAction, eventLabel) {
  var n = eventAction.indexOf('?');
  eventAction = eventAction.substring(0, n !== -1 ? n : eventAction.length);
  return '__gaTracker(\'send\',\'event\', \'' + eventCategory + '\', \'' + decodeURIComponent(eventAction) + '\', \'' + decodeURIComponent(eventLabel) + '\';, {\'nonInteraction\': 1}';
}
