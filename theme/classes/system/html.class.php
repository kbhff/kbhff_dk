<?php
/**
* This file contains customized HTML-element output functions
*/
class HTML extends HTMLCore {

/** 
* The function takes item and add_path as parameters, and creates a div with the specific item-id as well as meta-data consisting of the validated action path and the crsf-token. 
* It checks if item has comments. If so, it loops through the comments and prints them together with their corresponding information (published-at and author).
* Note: Is not yet used in the project. 
*/
	function frontendComments($item, $add_path) {
		global $page;

		$_ = '';

		$_ .= '<div class="comments i:comments item_id:'.$item["item_id"].'"';
		$_ .= '	data-comment-add="'.$page->validPath($add_path).'"';
		$_ .= '	data-csrf-token="'.session()->value("csrf").'"';
		$_ .= '	>';
		$_ .= '	<h2 class="comments">Comments</h2>';
		if($item["comments"]):
			$_ .= '<ul class="comments">';
			foreach($item["comments"] as $comment):
			$_ .= '<li class="comment comment_id:'.$comment["id"].'" itemprop="comment" itemscope itemtype="https://schema.org/Comment">';
				$_ .= '<ul class="info">';
					$_ .= '<li class="published_at" itemprop="datePublished" content="'.date("Y-m-d", strtotime($comment["created_at"])).'">'.date("Y-m-d, H:i", strtotime($comment["created_at"])).'</li>';
					$_ .= '<li class="author" itemprop="author">'.$comment["nickname"].'</li>';
				$_ .= '</ul>';
				$_ .= '<p class="comment" itemprop="text">'. $comment["comment"].'</p>';
			$_ .= '</li>';
			endforeach;
		$_ .= '</ul>';
		else:
		$_ .= '<p>No comments yet</p>';
		endif;
		$_ .= '</div>';

		return $_;

	}


/** 
* The function checks if item has prices.
* If the item has an offer, the function prints the formatted price of the offer and the default-price. It also checks for a subscibtion-method and prints it.
* If the item does not have an offer but instead has a default-price, the default-price is printed together with a subscribtion-method. 
* If the item does not have either an offer or a default-price the function prints 'Free'. 
* If the item has a description, it is printed. 
*/
	function frontendOffer($item, $url, $description = false) {
	
		$_ = '';

		if($item["prices"]) {

			global $page;

			$offer_key = arrayKeyValue($item["prices"], "type", "offer");
			$default_key = arrayKeyValue($item["prices"], "type", "default");

			$_ .= '<ul class="offer" itemscope itemtype="http://schema.org/Offer">';
				$_ .= '<li class="name" itemprop="name" content="'.$item["name"].'"></li>';
				$_ .= '<li class="currency" itemprop="priceCurrency" content="'.$page->currency().'"></li>';

				if($offer_key !== false) {
					$_ .= '<li class="price default">'.formatPrice($item["prices"][$default_key]).(isset($item["subscription_method"]) && $item["subscription_method"] && $item["prices"][$default_key]["price"] ? ' / '.$item["subscription_method"]["name"] : '').'</li>';
					$_ .= '<li class="price offer" itemprop="price" content="'.$item["prices"][$offer_key]["price"].'">'.formatPrice($item["prices"][$offer_key]).(isset($item["subscription_method"]) && $item["subscription_method"] && $item["prices"][$default_key]["price"] ? ' / '.$item["subscription_method"]["name"] : '').'</li>';
				}
				else if($item["prices"][$default_key]["price"]) {
					$_ .= '<li class="price" itemprop="price" content="'.$item["prices"][$default_key]["price"].'">'.formatPrice($item["prices"][$default_key]).(isset($item["subscription_method"]) && $item["subscription_method"] && $item["prices"][$default_key]["price"] ? ' / '.$item["subscription_method"]["name"] : '').'</li>';
				}
				else {
					$_ .= '<li class="price" itemprop="price" content="'.$item["prices"][$default_key]["price"].'">Free</li>';
				}

				$_ .= '<li class="url" itemprop="url" content="'.$url.'"></li>';
				if($description) {
					$_ .= '<li class="description" itemprop="description">'.$description.'</li>';
				}

			$_ .= '</ul>';

		}

		return $_;
	}

/**
* The function takes three parameters: item, url and an optional parameter with the two options 'media' and 'sharing'. 
* The function prints a list with meta-data and article-info such as published-at, modified-at, author, main-entity, publisher-info (name, logo) and image. 
* If the function gets media as a parameter, it prints meta-data of the media for SEO. If not, it prints meta-data of the logo for SEO. 
* If the item has a location with longitude- and latitudedata it is printed. 

* Note: Errors in publisher-info.  The function prints think.dk as name and uses logo-large instead of the correct KBHFF-logo. 
* Note: Errors in meta-data of media. Wrong logo path as in publisher-info. 
*/ 
	function articleInfo($item, $url, $_options) {

		$media = false;
		$sharing = false;

		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					case "media"            : $media              = $_value; break;
					case "sharing"          : $sharing            = $_value; break;
				}
			}
		}


		$_ = '';

		$_ .= '<ul class="info">';
		$_ .= '	<li class="published_at" itemprop="datePublished" content="'. date("Y-m-d", strtotime($item["published_at"])) .'">'. date("Y-m-d, H:i", strtotime($item["published_at"])) .'</li>';
		$_ .= '	<li class="modified_at" itemprop="dateModified" content="'. date("Y-m-d", strtotime($item["modified_at"])) .'"></li>';
		$_ .= '	<li class="author" itemprop="author">'. (isset($item["user_nickname"]) ? $item["user_nickname"] : SITE_NAME) .'</li>';
		$_ .= '	<li class="main_entity'. ($sharing ? ' share' : '') .'" itemprop="mainEntityOfPage" content="'. SITE_URL.$url .'"></li>';
		$_ .= '	<li class="publisher" itemprop="publisher" itemscope itemtype="https://schema.org/Organization">';
		$_ .= '		<ul class="publisher_info">';
		$_ .= '			<li class="name" itemprop="name">think.dk</li>';
		$_ .= '			<li class="logo" itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">';
		$_ .= '				<span class="image_url" itemprop="url" content="'. SITE_URL .'/img/logo-large.png"></span>';
		$_ .= '				<span class="image_width" itemprop="width" content="720"></span>';
		$_ .= '				<span class="image_height" itemprop="height" content="405"></span>';
		$_ .= '			</li>';
		$_ .= '		</ul>';
		$_ .= '	</li>';
		$_ .= '	<li class="image_info" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">';

		if($media):
			$_ .= '		<span class="image_url" itemprop="url" content="'. SITE_URL .'/images/'. $item["item_id"] .'/'. $media["variant"] .'/720x.'. $media["format"] .'"></span>';
			$_ .= '		<span class="image_width" itemprop="width" content="720"></span>';
			$_ .= '		<span class="image_height" itemprop="height" content="'. floor(720 / ($media["width"] / $media["height"])) .'"></span>';
		else:
			$_ .= '		<span class="image_url" itemprop="url" content="'. SITE_URL .'/img/logo-large.png"></span>';
			$_ .= '		<span class="image_width" itemprop="width" content="720"></span>';
			$_ .= '		<span class="image_height" itemprop="height" content="405"></span>';
		endif;

		$_ .= '	</li>';

		if(isset($item["location"]) && $item["location"] && $item["latitude"] && $item["longitude"]):
			$_ .= '	<li class="place" itemprop="contentLocation" itemscope itemtype="http://schema.org/Place">';
			$_ .= '		<ul class="geo" itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">';
			$_ .= '			<li class="location" itemprop="name">'.$item["location"].'</li>';
			$_ .= '			<li class="latitude" itemprop="latitude" content="'.round($item["latitude"], 5).'"></li>';
			$_ .= '			<li class="longitude" itemprop="longitude" content="'.round($item["longitude"], 5).'"></li>';
			$_ .= '		</ul>';
			$_ .= '	</li>';
		endif;

		$_ .= '</ul>';

		return $_;

	}


	// $context should be array of allowed contexts
	// - if $context is false, no tags are shown (except editing and default tag)
	// $default should be array with url and text
	// $url should be url to prefix tag links
	// $editing defines if editing link is shown
	
	/**
	* The function takes two parameters: item and an optional with the options context, default, url, editing and schema.
	* If the item has tags and an editing link is shown, the function checks if context and editing exist as a keyvalue pair and returns the index as an editing-tag.
	* If the editing-tag is returned and it has a value of true, the function prints 'still editing'.
	* If the editing-tag does not have a value of true, it prints the value.
	* If a default-array is given as parameter, the function prints the url and the text.
	* If the item has tags and context is defined, the function loops through the item-tags and creates a list of each item-tag containing its url and text and if relevant its schema-properties for SEO.
	* The function prints a tag ul with the content. 
	*/
	function articleTags($item, $_options = false) {

		$context = false;
		$default = false;
		$url = false;
		$editing = true;
		$schema = "articleSection";


		if($_options !== false) {
			foreach($_options as $_option => $_value) {
				switch($_option) {
					case "context"           : $context             = $_value; break;
					case "default"           : $default             = $_value; break;

					case "url"               : $url                 = $_value; break;

					case "editing"           : $editing             = $_value; break;
					case "schema"            : $schema              = $_value; break;

				}
			}
		}



		$_ = '';


		// editing tag
		if($item["tags"] && $editing):
			$editing_tag = arrayKeyValue($item["tags"], "context", "editing");
			if($editing_tag !== false):
				$_ .= '	<li class="editing" title="This post is work in progress">'.($item["tags"][$editing_tag]["value"] == "true" ? "Still editing" : $item["tags"][$editing_tag]["value"]).'</li>';
			endif;
		endif;

		// default tag
		if(is_array($default)):
			$_ .= '	<li><a href="'.$default[0].'">'.$default[1].'</a></li>';
		endif;

		// item tag list
		if($item["tags"] && $context):
			foreach($item["tags"] as $item_tag):
				if(array_search($item_tag["context"], $context) !== false):
					$_ .= '	<li'.($schema ? ' itemprop="'.$schema.'"' : '').'>';
					if($url):
						$_ .= '<a href="'.$url."/".urlencode($item_tag["value"]).'">';
					endif;
					$_ .= $item_tag["value"];
					if($url):
						$_ .= '</a>';
					endif;
					$_ .= '</li>';
				endif;
			endforeach;
		endif;


		// only print tags ul if it has content
		if($_) {
			$_ = '<ul class="tags">'.$_.'</ul>';
		}


		return $_;
	}


	/**
	* The function checks if there are any messages, gets all stored messages and creates an array of these.
	* It then loops through all the stored messages to get the type and message and then prints this information in a div. 
	*/

	function serverMessages($type = []) {

		$_ = '';

		if(message()->hasMessages($type)) {
			$_ .= '<div class="messages">';

			$all_messages = message()->getMessages($type);
			message()->resetMessages();
			foreach($all_messages as $type => $messages) {
				foreach($messages as $message) {
					$_ .= '<p class="'.$type.'">'.$message.'</p>';
				}
			}
			$_ .= '</div>';
		}

		return $_;
	}
}

// create standalone instance to make HTML available without model
$HTML = new HTML();

?>
