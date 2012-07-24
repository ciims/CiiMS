<?php
// http://www.acornartwork.com/blog/2010/04/12/tutorial-twitter-rss-feed-parser-in-pure-php/
// Ann Christin Kern
// Modified by Charles R. Portwood II for Yii Class

class JTwitterParser {
	
	public $username;
	public $maxTweets = 5;
	
	public function __construct($options)
	{
		if (!isset($options['username']))
			throw new CException('Username is not defined. Unable to display tweets');	
		else
			$this->username = $options['username'];
		
		if (isset($options['max']))
			$this->maxTweets = $options['max'];
			
		return $this;		
	}
	
	public function fetch_tweets()
	{
		 //Using simplexml to load URL
		$tweets = Yii::app()->cache->get('tweet-listing');
		
		if ($tweets === FALSE)
		{
			$tweets = @simplexml_load_file("http://twitter.com/statuses/user_timeline/" . $this->username . ".rss");
			
			// Cache the response for 10 Minutes. 10*6*24 = 144 Requests/Day < Max of 150 Requests API regulation
			if (!empty($tweets))
			{
				$items = array();
				foreach ($tweets->channel->item as $item)
					$items[] = (array)$item;
				
				$tweets = $items;
				Yii::app()->cache->set('tweet-listing', $tweets, 600);
			}
		}
		
		if(!$tweets)
		{
			$tweet_array = array();
			$tweet_array[] = array(
				'desc'=>'Unable to connect to Twitter API at this time',
				'date'=>gmdate('F jS Y, H:i'),
				'link'=>''
				);
			return $tweet_array;
		}
		
		$tweet_array = array();  //Initialize empty array to store tweets
		
		foreach($tweets as $k=>$tweet)
		{
			$twit = $tweet['description'];  //Fetch the tweet itself
				
			//Remove the preceding 'username: '
			$twit = substr(strstr($twit, ': '), 2, strlen($twit));
			
			// Convert URLs into hyperlinks
			$twit = preg_replace("/(http:\/\/)(.*?)\/([\w\.\/\&\=\?\-\,\:\;\#\_\~\%\+]*)/", "<a href=\"\\0\">\\0</a>", $twit);
			
			// Convert usernames (@) into links 
			$twit = preg_replace("(@([a-zA-Z0-9\_]+))", "<a href=\"http://www.twitter.com/\\1\">\\0</a>", $twit);
			
			// Convert hash tags (#) to links 
			$twit = preg_replace('/(^|\s)#(\w+)/', '\1<a href="http://search.twitter.com/search?q=%23\2">#\2</a>', $twit);
			
			//Specifically for non-English tweets, converts UTF-8 into ISO-8859-1
			$twit = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $twit);
			
			//Get the date it was posted
			$pubdate = strtotime($tweet['pubDate']); 
			$propertime = gmdate('F jS Y @ H:i', $pubdate);  //Customize this to your liking
			
			//Store tweet and time into the array
			$tweet_item = array(
				'desc' => $twit,
				'date' => $propertime,
				'link' => $tweet['link'],
				);
			$tweet_array[] = $tweet_item;
			
			if (sizeof($tweet_array) == $this->maxTweets)
				break;
		}
		

	//Return array
	return $tweet_array;
	}
}
?>