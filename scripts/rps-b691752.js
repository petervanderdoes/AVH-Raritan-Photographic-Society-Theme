function TrackClick(eventCategory,eventAction,eventLabel){return"__gaTracker('send','event', '"+eventCategory+"', '"+decodeURIComponent(eventAction)+"', '"+decodeURIComponent(eventLabel)+"';, {'nonInteraction': 1}"}function ExtractDomain(url){var domain;return domain=url.indexOf("://")>-1?url.split("/")[2]:url.split("/")[0],domain=domain.split(":")[0]}!function($,window){$(function(){$('.entry a[href^="http://"] , #comments a[href^="http://"] , .entry a[href^="https://"] , #comments a[href^="https://"]').not('.entry a[href$=".jpg"]').each(function(){this.hostname!==location.hostname&&($(this).attr({"class":function(i,val){return val=void 0===val?"":val+" ",val+"ui-state-default"},rel:"external nofollow",target:"_blank",title:function(i,val){return val=val?val:this.innerHTML,val+" (external link, click to open in a new window)"},onclick:function(){var eventAction=ExtractDomain(this.href);return TrackClick("Outgoing Links",eventAction,this.innerHTML)}}),$(this).append("<span class='ui-icon ui-icon-extlink'></span>"),$(this).hover(function(){$(this).addClass("ui-state-hover")},function(){$(this).removeClass("ui-state-hover")}))});var rps_IMG_select='.rps-showcases a[href*=".jpg"], .entry a[href*=".jpg"], .entry area[href*=".jpg"], .entry a[href*=".gif"], .entry area[href*=".gif"], .entry a[href*=".png"], .entry area[href*=".png"]';$(rps_IMG_select).each(function(){$(this).attr({onclick:function(){return TrackClick("Images",window.location.pathname,this.title)},title:function(i,val){if(!val){var image=$(this).find("img").first();val=image[0].alt}return val}})});var rps_Downloads_select='.entry a[href*=".pdf"]';$(rps_Downloads_select).each(function(){$(this).attr({onclick:function(){return TrackClick("Downloads",window.location.pathname,this.innerHTML)}})})})}(window.jQuery,window);