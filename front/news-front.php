<?php
/**
* ajax for news
*/

//ajax loader
echo '<div id="news-ajax-loader"><img src="http://localhost/michal/budovanistatu.cz/front/ajax-loader.gif" /></div>';
echo '<div id="news-wrapper"></div>';
drupal_add_js('
//jQuery.noConflict(); // Tell jQuery you are going with noConflict mode.
alert("ja");
jQuery(function(){ 
  url = "http://localhost/michal/budovanistatu.cz/front/news.php";
  jQuery.ajax({
			url: url,
			dataType: "html",
			success: function(data) {
alert("je");
				jQuery("#news-ajax-loader").html("");
				jQuery("#news-wrapper").html(data);
			}
  });
}); 
','inline');
?>
