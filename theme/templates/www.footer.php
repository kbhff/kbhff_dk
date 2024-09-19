<?
$navigation = $this->navigation("main-public");


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
	
	// loop through nodes
	foreach($nodes as $node) {
		// print_r($node); exit;

		// reset variables before each loop
		$children = "";
		$content = "";
		$placeholder = "";

		$trail_class = "";
		$selected_class = "";


		// path of node
		$current_path = '/'.$_parent_path.($_nav_path ? "/".implode("/", $_nav_path) : "");


		if($page->url == $node["link"]) {
			$selected_class = "selected";
		}

		// prioritize links
		if($node["link"]) {

			// Does user have access
			if(security()->validatePath($node["link"])) {
				$content .= '<a href="'.$node["link"].'"' . ($node["target"] ? $HTML->attribute("target", "_blank") : "").'>'.$node["name"].'</a>';
			}
			// Does node have fallback url?
			else if($node["fallback"] && security()->validatePath($node["fallback"])) {
				$content .= '<a href="'.$node["fallback"].'"' . ($node["target"] ? $HTML->attribute("target", "_blank") : "").'>'.$node["name"].'</a>';
			}

		}

		// empty folder
		else {
			$placeholder .= '<h'.(4+$_nav_indent).'>'.$node["name"].'</h'.(4+$_nav_indent).'>';
		}

		// look for children
		if($node["nodes"]) {
			$_nav_indent++;
			array_push($_nav_path, superNormalize($node["name"]));
			
			$children .= recurseNodes($node["nodes"], $_parent_path);
			
			$_nav_indent--;
			array_pop($_nav_path);
		}


		// Do we have valid navigation links?
		if($content || $children || ($placeholder && $children)) {

			if($children && preg_match("/class\=\"[^\"]*selected[^\"]*\"/", $children)) {
				$trail_class = "path";
			}

			// compile LI HTML string using correct classes
			$att_class = $HTML->attribute("class", "item", $node["classname"], "indent".$_nav_indent, $selected_class, $trail_class, ($node["nodes"] ? "parent" : ""));
			$_ .= '<li'.$att_class.'>';
			$_ .= $content;
			$_ .= $placeholder;
			$_ .= $children;
			$_ .= '</li>';

		}

	}

	// if this results in any elements, then wrap stuff in UL
	if($_) {
		$_ = '<ul class="' . ($_nav_indent ? "sub" : "navigation") . '">' . $_ . '</ul>';
	}


	return $_;
}

$nested_navigation = recurseNodes($navigation["nodes"], "/");

?>
	</div>

	<div id="navigation" class="public">
		<? if($navigation): ?>
			<?= $nested_navigation ?> 
	 	<? endif; ?>
	</div>

	<div id="footer">
		<div class="row">
			<div class="column">
				<h3>KBHFF</h3>
			</div>
			<div class="column">
				<h3>Genveje</h3>
				<ul>
					<li><a href="/faq">FAQ</a></li>
					<li><a href="/ugens-pose">Ugens pose</a></li>
					<li><a href="/butik">Grøntshoppen</a></li>
					<li><a href="/afdelinger">Afdelinger</a></li>
					<li><a href="/om">Om KBHFF</a></li>
				</ul>
			</div>
			<div class="column newsletter">
				<h3>Tilmeld Nyhedsbrev</h3>
				<form action="//kbhff.us15.list-manage.com/subscribe/post?u=d2a926649ebcf316af87a05bb&amp;id=141ae6f59f" method="post" target="_blank">
					<input type="hidden" name="b_d2a926649ebcf316af87a05bb_141ae6f59f" value="">
					<div class="field email required">
						<label for="input_email">E-mailadresse</label>
						<input type="email" value="" name="EMAIL" id="input_email" />
					</div>

					<ul class="actions">
						<li class="submit"><input type="submit" value="Tilmeld" name="subscribe" class="button" /></li>
					</ul>
				</form>
			</div>
		</div>
		<ul class="servicenavigation">
			<li class="copyright">Københavns Fødevarefællesskab, 2024</li>
			<li class="businessterms"><a href="/handelsbetingelser">Handelsbetingelser</a></li>
			<li class="personaldata"><a href="/persondata">Persondata</a></li>
			<li class="contact"><a href="/kontakt">Kontakt</a></li>
			<li><a href="http://kbhff.dk">kbhff.dk</a></li>
		</ul>
	</div>

</div>

</body>
</html>
