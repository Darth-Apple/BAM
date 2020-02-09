<script>
jQuery(document).ready(function($) {
	$("#announcement-{$announcement_id}.close").click(function() {
        // old: add space back before .close
		$.cookie("announcement{$announcement_id}", "dismissed");
		var a{$announcement_id} = $.cookie("announcement{$announcement_id}");
		$("#announcement-{$announcement_id}").fadeOut();
	});
	var a{$announcement_id} = $.cookie("announcement{$announcement_id}");
	if (a{$announcement_id} == 'dismissed') {
		$("#announcement-{$announcement_id}").hide();
	}
});
</script>






<script>
jQuery(document).ready(function($) {
	// onclick="removeFadeOut(this.parentNode, 750, {$announcement_id});"
	$("#announcement-{$announcement_id}.close").click(function() {
        // old: add space back before .close
		$.cookie("announcement{$announcement_id}", "dismissed");
		var a{$announcement_id} = $.cookie("announcement{$announcement_id}");
		$("#announcement-{$announcement_id}").fadeOut();
	});
	var a{$announcement_id} = $.cookie("announcement{$announcement_id}");
	if (a{$announcement_id} == 'dismissed') {
		$("#announcement-{$announcement_id}").hide();
	}
});
</script>




<script>
	// https://stackoverflow.com/a/33424474
	function bam_dismiss_announcement(el, speed, announcementID) {
    	var seconds = speed/1000;
    	el.style.transition = "opacity "+seconds+"s ease";
    	el.style.opacity = 0;
    	setTimeout(function() {
        	el.parentNode.removeChild(el);
    	}, speed);
		
		// set the cookie. 
		var date = new Date();
    	date.setTime(date.getTime() + ({$bam_cookie_expire_days} * 24 * 60 * 60 * 1000));
    	expires = "; expires=" + date.toUTCString();
		document.cookie = "announcement_id_" + announcementID + "=" + ({$announcement_id} || "") + expires + "; path=" + {$bam_cookie_path};
	}
	
	function getCookie(name) {
  		var nameEQ = name + "=";
  		var ca = document.cookie.split(';');
  		for (var i = 0; i < ca.length; i++) {
    		var c = ca[i];
    		while (c.charAt(0) == ' ') c = c.substring(1, c.length);
    		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
  		}
  		return null;
	}
</script>






<script>
	// https://stackoverflow.com/a/33424474
	function bam_dismiss_announcement(el, speed, announcementID) {
    	var seconds = speed/1000;
    	el.style.transition = "opacity "+seconds+"s ease";
    	el.style.opacity = 0;
    	setTimeout(function() {
        	el.parentNode.removeChild(el);
    	}, speed);
		
		// set the cookie. 
		var date = new Date();
    	date.setTime(date.getTime() + ({$bam_cookie_expire_days} * 24 * 60 * 60 * 1000));
    	expires = "; expires=" + date.toUTCString();
		document.cookie = "announcement_id_" + announcementID + "=" + ({$announcement_id} || "") + expires + "; path=" + {$bam_cookie_path};
	}
	
	function getCookie(name) {
  		var nameEQ = name + "=";
  		var ca = document.cookie.split(';');
  		for (var i = 0; i < ca.length; i++) {
    		var c = ca[i];
    		while (c.charAt(0) == ' ') c = c.substring(1, c.length);
    		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
  		}
  		return null;
	}
</script>

nclick="bam_dismiss_announcement(this.parentNode, 750, {$announcement_id});"