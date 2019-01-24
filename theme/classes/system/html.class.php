<?php
/**
* This file contains customized HTML-element output functions
*/
class HTML extends HTMLCore {

	/** 
	 * Compile a html list with comments and information. 
	 * 
	 * @param Array $item array containing data of a single item.
	 * @param String $add_path string containing action / controller path
	 * @return String compiled html list 
	*/
	function frontendComments($item, $add_path) {
		global $page;

		$_ = '';

		$_ .= '<div class="comments i:comments item_id:'.$item["item_id"].'"';
		$_ .= '	data-comment-add="'.$page->validPath($add_path).'"';
		$_ .= '	data-csrf-token="'.session()->value("csrf").'"';
		$_ .= '	>';
		$_ .= '	<h2 class="comments">Comments</h2>';
		// Check if item has comments.
		if($item["comments"]):
			//  Loop through the comments and concatenate them, along with their corresponding information (published_at and author),  into an html-string. 
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
	* Compile a html list with price and description of item
	* 
	* @param Array $item array containing data of a single item.
	* @param String $_url is the url associated to the item.
	* @param String $description contains an optional description of the item. 
	* @return String compiled html list.
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
				// Generate offer price, if relevant 
				if($offer_key !== false) {
				$_ .= '<li class="price default">'.formatPrice($item["prices"][$default_key]).(isset($item["subscription_method"]) && $item["subscription_method"] && $item["prices"][$default_key]["price"] ? ' / '.$item["subscription_method"]["name"] : '').'</li>';
				$_ .= '<li class="price offer" itemprop="price" content="'.$item["prices"][$offer_key]["price"].'">'.formatPrice($item["prices"][$offer_key]).(isset($item["subscription_method"]) && $item["subscription_method"] && $item["prices"][$default_key]["price"] ? ' / '.$item["subscription_method"]["name"] : '').'</li>';
				}
				// Generate default price 
				else if($item["prices"][$default_key]["price"]) {
				$_ .= '<li class="price" itemprop="price" content="'.$item["prices"][$default_key]["price"].'">'.formatPrice($item["prices"][$default_key]).(isset($item["subscription_method"]) && $item["subscription_method"] && $item["prices"][$default_key]["price"] ? ' / '.$item["subscription_method"]["name"] : '').'</li>';
				}
				// No price, so item is free 
				else {
				$_ .= '<li class="price" itemprop="price" content="'.$item["prices"][$default_key]["price"].'">Free</li>';
				}
				// Does the item have a description?
				$_ .= '<li class="url" itemprop="url" content="'.$url.'"></li>';
				if($description) {
				$_ .= '<li class="description" itemprop="description">'.$description.'</li>';
				}

			$_ .= '</ul>';

		}

		return $_;
	}

	/**
	* 
	* Compile a html list with meta-data and article-info suitable for SEO
	* 
	* @param Array $item array containing data of a single item.
	* @param String $_url is the url associated to the item.
	* @param ARRAY $options is an associative array containing the option to extend the item. 
	* @param Bollean $media is the option to extend the item with a media property.
	* @param Boolean $sharing contain the option to extend the item with a sharing property.
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
		$_ .= '			<li class="name" itemprop="name">Københavns Fødevarefællesskab</li>';
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

	
	/**
	* Compile a html list containing tags. 
	* 
	* @param Array $item array containing data of a single item.
	* @param Array $options is an associative array containing the option to extend the item. 
	* @param Array $context Array is the option of allowed contexts - if $context is false, no tags are shown (except editing and default tag)
	* @param Array $default Array containing url and text
	* @param String $url url to prefix tag links
	* @param Boolean $editing defines if editing link is shown
	* @param String $schema
	* @return STRING compiled html list 
	*
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


		// Generate editing tag if relevant
		if($item["tags"] && $editing):
			$editing_tag = arrayKeyValue($item["tags"], "context", "editing");
			if($editing_tag !== false):
				$_ .= '	<li class="editing" title="This post is work in progress">'.($item["tags"][$editing_tag]["value"] == "true" ? "Still editing" : $item["tags"][$editing_tag]["value"]).'</li>';
			endif;
		endif;

		// Generate default tag if relevant
		if(is_array($default)):
			$_ .= '	<li><a href="'.$default[0].'">'.$default[1].'</a></li>';
		endif;

		// Generate item tag list
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
	* Generate a contaginated string with all stored messages. 
	*
	* @param String $type message delivery type
	* @return String messages
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
