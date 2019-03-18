<? $navigation = $this->navigation("main"); 
// print_r($navigation); exit;
?>

<?

// use global vars to keep track of path and indenting
global $_nav_indent;
global $_nav_path;

$_nav_indent = 0;
$_nav_path = array();

// node iteration
function recurseNodes($nodes, $_parent_path) {
	global $page;
	global $HTML;
	global $_nav_indent;
	global $_nav_path;
	
	$_ = "";
	$_ .= '<ul class = "navigation">';
	
	// loop through nodes
	foreach($nodes as $node) {
		// print_r($node); exit;

		// reset variables before each loop
		$children = "";
		$content = "";
		$trail_class = "";
		$selected_class = "";


		// path of node
		//$current_path = '/page'.($_nav_path ? "/".implode("/", $_nav_path) : "");
		$current_path = '/'.$_parent_path.($_nav_path ? "/".implode("/", $_nav_path) : "");

		// print_r($nodes); exit;
		// is current path part of requested url
		if(strpos($page->url, $current_path."/".superNormalize($node["name"])) !== false) {
			$trail_class = "path";
		}

		// prioritize links
		if($node["link"]) {
			$content .= '<a href="'.$node["link"].'"'.(strpos($node["link"], "http://") === 0 ? $HTML->attribute("target", "_blank") : "").'>'.$node["name"].'</a>';
		}
		
		// // page reference
		// else if($node["item_id"]) {
			
			// 	// look for url match
			// 	$current_url = $current_path."/".$node["sindex"];
			// 	if($current_url == $page->url) {
				// 		$selected_class = "selected";
				// 	}
				
				// 	$content .= '<a href="'.$current_url.'">'.$node["name"].'</a>';
				// }
				
		// empty folder
		else {
			$content .= '<h'.(4+$_nav_indent).'>'.$node["name"].'</h'.(4+$_nav_indent).'>';
		}
				
		// look for children
		if($node["nodes"]) {
			$_nav_indent++;
			array_push($_nav_path, superNormalize($node["name"]));
			
			$children .= recurseNodes($node["nodes"], $_parent_path);
			
			$_nav_indent--;
			array_pop($_nav_path);
		}
		
		// compile LI HTML string using correct classes
		$att_class = $HTML->attribute("class", "item", $node["classname"], "indent".$_nav_indent, $selected_class, $trail_class, ($node["nodes"] ? "parent" : ""));
		$_ .= '<li'.$att_class.'>';
		$_ .= $content;
		$_ .= $children;
		$_ .= '</li>';
		
	}
	$_ .= '</ul>';
	
	return $_;
}

$nested_navigation = recurseNodes($navigation["nodes"],	 "/");

?>
	</div>

	<div id="navigation">
		<? if($navigation): ?>
			<?= $nested_navigation ?> 
	 	<? endif; ?>
	</div>

	<div id="footer">
		<ul class="servicenavigation">
			<li class="copyright">KBHFF, 2018</li>
			<li class="personaldata"><a href="/persondata">Persondata</a></li>
			<li class="contact"><a href="/kontakt">Kontakt</a></li>
			<li><a href="http://kbhff.dk">kbhff.dk</a></li>
		</ul>
	</div>

</div>

</body>
</html>
