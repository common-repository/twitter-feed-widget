<?php
/*
Plugin Name: Twitter Feed Widget
Plugin URI: http://thesquaremedia.com/blog/plugins/twitter-feed-widget/
Description: Twitter Feed Widget will display your tweets using ajax and jquery. It shows one tweet at a time and loops through an specified number of tweets and time interval. *IMPORTANT* the twitter feed currently uses the new Twitter 1.1 API you will need to get your Access Token,Access Token Secret, Consumer Key and Consumer Secret from [dev.twitter.com](https://dev.twitter.com/docs/auth/tokens-devtwittercom). 
Version: 2.0
Stable tag: 2.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Author: Xavier Serrano
Author URI: http://thesquaremedia.com/blog/
*/
if(!class_exists('TSMTwitterFeedWidget')) {
class TSMTwitterFeedWidget extends WP_Widget {

	function TSMTwitterFeedWidget() {
		$widget_ops  = array( 'classname' => 'tsm_twitter_feed_widget', 'description' => __( 'Twitter Feed that Allows you to add more than one instance of it.', 'tsm-twitter-feed-widget' ) );
		$control_ops = array( 'width' => 300, 'height' => 300 );

		$this->WP_Widget( 'tsm-twitter-feed-widget', __( 'Twitter feed Widget', 'tsm-twitter-feed-widget' ), $widget_ops, $control_ops );

		wp_enqueue_script('jquery');
		wp_register_style( 'tsm_twitter_feed_widget', plugins_url('style.css', __FILE__) );
        wp_enqueue_style( 'tsm_twitter_feed_widget' );
	}	
	/**
	 * Display the widget
	 *
	 * @param string $args Widget arguments
	 * @param string $instance Widget instance
	 * @return void
	 **/
	function widget( $args, $instance ) {
		extract( $args );
		$search=array("-","_"); 
		$replace=array("",""); 
		global $variable,$jVariable,$widget_id;
		$widget_id=$args['widget_id'];
		$variable=str_replace($search,$replace,$args['widget_id']);
		$jVariable=$args['widget_id'];
		$title        = apply_filters( 'widget_title', $instance['title'] );
		$twitter = stripslashes($instance['twitter']);
		$howManyTweets = stripslashes($instance['howManyTweets']);
		$timeInterval = stripslashes($instance['timeInterval']);
		$twitterAccessToken = stripslashes($instance['twitterAccessToken']);
		$twitterAccessTokenSecret = stripslashes($instance['twitterAccessTokenSecret']);
		$twitterConsumerKey = stripslashes($instance['twitterConsumerKey']);
		$twitterConsumerSecret = stripslashes($instance['twitterConsumerSecret']);
		$access_token			= $twitterAccessToken;
		$access_token_secret	= $twitterAccessTokenSecret;
		$consumer_key			= $twitterConsumerKey;
		$consumer_secret		= $twitterConsumerSecret;
		
		require_once('twitter-api-exchange.php');
		
		$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
$getfield = '?screen_name='.$twitter.'&count=';
if($howManyTweets!=''){$getfield .= $howManyTweets; }else{ $getfield .= '3'; }
$requestMethod = 'GET';
$settings = array(
	'oauth_access_token' => $access_token,
	'oauth_access_token_secret' => $access_token_secret,
	'consumer_key' => $consumer_key,
	'consumer_secret' => $consumer_secret
);
		
		echo $before_widget;
	
		if ( $title )
			echo $before_title . stripslashes( $title ) . $after_title;
?>
<ul class="twitter-box <?php echo $jVariable; ?>" >
	</ul>
<script>
jQuery(document).ready(function () {
	///////loading tweets ajax func
	tweetBody<?php  echo $variable;  ?>='';
	var twitterJSON<?php  echo $variable;  ?> =  <?php $twitterAPI = new TwitterAPIExchange($settings);
	$twitter_stream = $twitterAPI->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest(); echo $twitter_stream; ?>;
		tweetBody<?php  echo $variable;  ?>='';
		jQuery.each(twitterJSON<?php  echo $variable;  ?>, function(i,item){
			tweetTxt<?php  echo $variable;  ?>=item.text;
			tweetBody<?php  echo $variable;  ?>+="<li><a href=\"http://twitter.com/"+item.user.screen_name+"\" >@"+item.user.name+"</a> ";
			for(var i=0;i< item.entities.urls.length;i++){
				tweetURL<?php  echo $variable;  ?>=" <a href=\""+item.entities.urls[i].url+"\" >"+item.entities.urls[i].url+"</a>";
				pattern<?php  echo $variable;  ?>=item.entities.urls[i].url;
				var patt<?php  echo $variable;  ?>=new RegExp(pattern<?php  echo $variable;  ?>);
				tweetTxt<?php  echo $variable;  ?>=tweetTxt<?php  echo $variable;  ?>.replace(patt<?php  echo $variable;  ?>, tweetURL<?php  echo $variable;  ?>);
			}
			tweetBody<?php  echo $variable;  ?>+=tweetTxt<?php  echo $variable;  ?>;
			myDate<?php  echo $variable;  ?>= new Date(item.created_at);
			source<?php  echo $variable;  ?>=item.source.replace(/&lt;/gm,"<");
			source<?php  echo $variable;  ?>=source<?php  echo $variable;  ?>.replace(/&gt;/gm,">");
			source<?php  echo $variable;  ?>=source<?php  echo $variable;  ?>.replace(/&quot;/gm,"\"");
			tweetBody<?php  echo $variable;  ?>+="<br /><span class=\"tweet-meta\">"+myDate<?php  echo $variable;  ?>.format("D M d Y")+" - via "+source<?php  echo $variable;  ?>+"</span>";
			tweetBody<?php  echo $variable;  ?>+="</li>";
			  if ( i == 3 ) return false;
		});
		jQuery('.twitter-box.<?php  echo $jVariable;  ?>').html(tweetBody<?php  echo $variable;  ?>);
		
	
//end of loading tweets ajax
///after loading tweets create twitter widget and display
	jQuery.fn.startTweet<?php  echo $variable;  ?>=function(){
		tweets<?php  echo $variable;  ?>=new Array();
		howManytweets<?php  echo $variable;  ?>=0;
		whichOne<?php  echo $variable;  ?>=0;
		jQuery(' .twitter-box.<?php  echo $jVariable;  ?> > li').each(function(){
			tweets<?php  echo $variable;  ?>.push(this);	
			if(tweets<?php  echo $variable;  ?>.length==1){
				jQuery(this).css({'display':'list-item'})
			}else{
				jQuery(this).css({'display':'none'})	
			}
		});
		howManyTweets<?php  echo $variable;  ?>=tweets<?php  echo $variable;  ?>.length;
		timeInterval<?php  echo $variable;  ?>=<?php if($timeInterval!='' && $timeInterval>=2){ echo $timeInterval;}else{echo '2';} ?>;
		t<?php  echo $variable;  ?>=setInterval("jQuery(this).nextTweet<?php  echo $variable;  ?>()",timeInterval<?php  echo $variable;  ?>);
		jQuery.fn.nextTweet<?php  echo $variable;  ?>=function(){
			 if(whichOne<?php  echo $variable;  ?>==0){
				jQuery(tweets<?php  echo $variable;  ?>[whichOne<?php  echo $variable;  ?>]).fadeOut(500,function(){
					jQuery(tweets<?php  echo $variable;  ?>[whichOne<?php  echo $variable;  ?>+1]).fadeIn(500);
					whichOne<?php  echo $variable;  ?>++;
					//alert(whichOne);
				});
			 }else if(whichOne<?php  echo $variable;  ?>>0 && whichOne<?php  echo $variable;  ?><howManyTweets<?php  echo $variable;  ?>-1){
				jQuery(tweets<?php  echo $variable;  ?>[whichOne<?php  echo $variable;  ?>]).fadeOut(500,function(){
					jQuery(tweets<?php  echo $variable;  ?>[whichOne<?php  echo $variable;  ?>+1]).fadeIn(500);
					whichOne<?php  echo $variable;  ?>++;
					//alert(whichOne);
				});
			}else if(whichOne<?php  echo $variable;  ?>==howManyTweets<?php  echo $variable;  ?>-1){
				jQuery(tweets<?php  echo $variable;  ?>[whichOne<?php  echo $variable;  ?>]).fadeOut(500,function(){
					jQuery(tweets<?php  echo $variable;  ?>[0]).fadeIn(500);
					whichOne<?php  echo $variable;  ?>=0;
					//alert(whichOne);
				});
			}
		}
	}
	jQuery(this).startTweet<?php  echo $variable;  ?>();
});
////end of creating and display twitter widget
</script>
<?php
		// After
		echo $after_widget;
	}
	/**
	 * Display config interface
	 *
	 * @param string $instance Widget instance
	 * @return void
	 **/
	function form( $instance ) {
		$title = stripslashes($instance['title']);
		$title_id = $this->get_field_id('title');
		$title_name = $this->get_field_name('title');
		$twitter = stripslashes($instance['twitter']);
		$twitter_id = $this->get_field_id('twitter');
		$twitter_name = $this->get_field_name('twitter');
		$howManyTweets = stripslashes($instance['howManyTweets']);
		$howManyTweets_id = $this->get_field_id('howManyTweets');
		$howManyTweets_name = $this->get_field_name('howManyTweets');
		$timeInterval = stripslashes($instance['timeInterval']);
		$timeInterval_id = $this->get_field_id('timeInterval');
		$timeInterval_name = $this->get_field_name('timeInterval');
		$twitterAccessToken = stripslashes($instance['twitterAccessToken']);
		$twitterAccessToken_id = $this->get_field_id('twitterAccessToken');
		$twitterAccessToken_name = $this->get_field_name('twitterAccessToken');
		$twitterAccessTokenSecret = stripslashes($instance['twitterAccessTokenSecret']);
		$twitterAccessTokenSecret_id = $this->get_field_id('twitterAccessTokenSecret');
		$twitterAccessTokenSecret_name = $this->get_field_name('twitterAccessTokenSecret');
		$twitterConsumerKey = stripslashes($instance['twitterConsumerKey']);
		$twitterConsumerKey_id = $this->get_field_id('twitterConsumerKey');
		$twitterConsumerKey_name = $this->get_field_name('twitterConsumerKey');
		$twitterConsumerSecret = stripslashes($instance['twitterConsumerSecret']);
		$twitterConsumerSecret_id = $this->get_field_id('twitterConsumerSecret');
		$twitterConsumerSecret_name = $this->get_field_name('twitterConsumerSecret');
	?>
	<p> <br/>
		<label for="<?php echo $title_id; ?>"><strong><?php _e('Title:','tsm-twitter-feed-widget'); ?></strong></label>
		<input class="widefat" id="<?php echo $title_id; ?>" name="<?php echo $title_name; ?>"
					   type="text" value="<?php echo esc_attr($title); ?>"/>
	</p>
	<p> <br/>
		<label for="<?php echo $twitter_id; ?>"><strong><?php _e('Twitter Account:','tsm-twitter-feed-widget'); ?></strong></label>
		<input class="widefat" id="<?php echo $twitter_id; ?>" name="<?php echo $twitter_name; ?>"
					   type="text" value="<?php echo esc_attr($twitter); ?>"/>
	</p>
	<p> <br/>
		<label for="<?php echo $howManyTweets_id; ?>"><strong><?php _e('How Many Tweets:','tsm-twitter-feed-widget'); ?></strong></label>
		<input class="widefat" id="<?php echo $howManyTweets_id; ?>" name="<?php echo $howManyTweets_name; ?>"
					   type="text" value="<?php echo esc_attr($howManyTweets); ?>"/><br /><span>number of tweets to show if empty will show 3</span>
	</p>
	<p> <br/>
		<label for="<?php echo $timeInterval_id; ?>"><strong><?php _e('Time Interval:','tsm-twitter-feed-widget'); ?></strong></label>
		<input class="widefat" id="<?php echo $timeInterval_id; ?>" name="<?php echo $timeInterval_name; ?>"
					   type="text" value="<?php echo esc_attr($timeInterval); ?>"/><br /><span>time in miliseconds minimum interval can be 2 seconds(2000)</span>
	</p>
	<p> <br/>
		<label for="<?php echo $twitterAccessToken_id; ?>"><strong><?php _e('Twitter Access Token:','tsm-twitter-feed-widget'); ?></strong></label>
		<input class="widefat" id="<?php echo $twitterAccessToken_id; ?>" name="<?php echo $twitterAccessToken_name; ?>"
					   type="text" value="<?php echo esc_attr($twitterAccessToken); ?>"/>
	</p>
	<p> <br/>
		<label for="<?php echo $twitterAccessTokenSecret_id; ?>"><strong><?php _e('Twitter Access Token Secret:','tsm-twitter-feed-widget'); ?></strong></label>
		<input class="widefat" id="<?php echo $twitterAccessTokenSecret_id; ?>" name="<?php echo $twitterAccessTokenSecret_name; ?>"
					   type="text" value="<?php echo esc_attr($twitterAccessTokenSecret); ?>"/>
	</p>
	<p> <br/>
		<label for="<?php echo $twitterConsumerKey_id; ?>"><strong><?php _e('Twitter Consumer Key:','tsm-twitter-feed-widget'); ?></strong></label>
		<input class="widefat" id="<?php echo $twitterConsumerKey_id; ?>" name="<?php echo $twitterConsumerKey_name; ?>"
					   type="text" value="<?php echo esc_attr($twitterConsumerKey); ?>"/>
	</p>
	<p> <br/>
		<label for="<?php echo $twitterConsumerSecret_id; ?>"><strong><?php _e('Twitter Consumer Secret:','tsm-twitter-feed-widget'); ?></strong></label>
		<input class="widefat" id="<?php echo $twitterConsumerSecret_id; ?>" name="<?php echo $twitterConsumerSecret_name; ?>"
					   type="text" value="<?php echo esc_attr($twitterConsumerSecret); ?>"/>
	</p>
	<?php 
	}
		
	/**
	 * Save widget data
	 *
	 * @param string $new_instance
	 * @param string $old_instance
	 * @return void
	 **/
	function update($new_instance, $old_instance) {             
			return $new_instance;
	}
}

function register_tsm_twitter_feed_widget() {
	register_widget( 'TSMTwitterFeedWidget' );
}

add_action( 'widgets_init', 'register_tsm_twitter_feed_widget' );
}

