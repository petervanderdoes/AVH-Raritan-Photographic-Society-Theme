/**
 *
 */
jQuery(document)
		.ready(function($) {
			/**
			 * Setup external links
			 */
			// here goes your javascript code where you access
			// jquery object will dollar sign
			$('.entry a[href^="http://"] , #comments a[href^="http://"] , .entry a[href^="https://"] , #comments a[href^="https://"]')
					.each(function() {
						if (this.hostname !== location.hostname) {
							// var title = (this.title ==
							// "") ? this.href : this.title;
							$(this).attr({
							'class' : function(i, val) {
								val = (val == undefined) ? "" : val;
								return val + " ui-state-default";
							},
							'target' : "_blank",
							'title' : function(i, val) {
								val = (val == undefined) ? this.href : val;
								return val + " (external link, click to open in a new window)";
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
		});
