/*
asset-builder @ 2021-03-09 20:35:45
*/

/*seg_desktop_include.js*/

/*seg_desktop_include.js*/

/*seg_desktop.js*/
if(!u || !Util) {
	var u, Util = u = new function() {};
	u.version = "0.9.3";
	u.bug = u.nodeId = u.exception = function() {};
	u.stats = new function() {this.pageView = function(){};this.event = function(){};}
	u.txt = function(index) {return index;}
}
function fun(v) {return (typeof(v) === "function")}
function obj(v) {return (typeof(v) === "object")}
function str(v) {return (typeof(v) === "string")}
u.bug_console_only = true;
Util.debugURL = function(url) {
	if(u.bug_force) {
		return true;
	}
	return document.domain.match(/(\.local|\.proxy)$/);
}
Util.nodeId = function(node, include_path) {
	console.log("Util.nodeId IS DEPRECATED. Use commas in u.bug in stead.");
	console.log(arguments.callee.caller);
	try {
		if(!include_path) {
			return node.id ? node.nodeName+"#"+node.id : (node.className ? node.nodeName+"."+node.className : (node.name ? node.nodeName + "["+node.name+"]" : node.nodeName));
		}
		else {
			if(node.parentNode && node.parentNode.nodeName != "HTML") {
				return u.nodeId(node.parentNode, include_path) + "->" + u.nodeId(node);
			}
			else {
				return u.nodeId(node);
			}
		}
	}
	catch(exception) {
		u.exception("u.nodeId", arguments, exception);
	}
	return "Unindentifiable node!";
}
Util.exception = function(name, _arguments, _exception) {
	u.bug("Exception in: " + name + " (" + _exception + ")");
	console.error(_exception);
	u.bug("Invoked with arguments:");
	console.log(_arguments);
}
Util.bug = function() {
	if(u.debugURL()) {
		if(!u.bug_console_only) {
			var i, message;
			if(obj(console)) {
				for(i = 0; i < arguments.length; i++) {
					if(arguments[i] || typeof(arguments[i]) == "undefined") {
						console.log(arguments[i]);
					}
				}
			}
			var option, options = new Array([0, "auto", "auto", 0], [0, 0, "auto", "auto"], ["auto", 0, 0, "auto"], ["auto", "auto", 0, 0]);
			var corner = u.bug_corner ? u.bug_corner : 0;
			var color = u.bug_color ? u.bug_color : "black";
			option = options[corner];
			if(!document.getElementById("debug_id_"+corner)) {
				var d_target = u.ae(document.body, "div", {"class":"debug_"+corner, "id":"debug_id_"+corner});
				d_target.style.position = u.bug_position ? u.bug_position : "absolute";
				d_target.style.zIndex = 16000;
				d_target.style.top = option[0];
				d_target.style.right = option[1];
				d_target.style.bottom = option[2];
				d_target.style.left = option[3];
				d_target.style.backgroundColor = u.bug_bg ? u.bug_bg : "#ffffff";
				d_target.style.color = "#000000";
				d_target.style.fontSize = "11px";
				d_target.style.lineHeight = "11px";
				d_target.style.textAlign = "left";
				if(d_target.style.maxWidth) {
					d_target.style.maxWidth = u.bug_max_width ? u.bug_max_width+"px" : "auto";
				}
				d_target.style.padding = "2px 3px";
			}
			for(i = 0; i < arguments.length; i++) {
				if(arguments[i] === undefined) {
					message = "undefined";
				}
				else if(!str(arguments[i]) && fun(arguments[i].toString)) {
					message = arguments[i].toString();
				}
				else {
					message = arguments[i];
				}
				var debug_div = document.getElementById("debug_id_"+corner);
				message = message ? message.replace(/\>/g, "&gt;").replace(/\</g, "&lt;").replace(/&lt;br&gt;/g, "<br>") : "Util.bug with no message?";
				u.ae(debug_div, "div", {"style":"color: " + color, "html": message});
			}
		}
		else if(typeof(console) !== "undefined" && obj(console)) {
			var i;
			for(i = 0; i < arguments.length; i++) {
				console.log(arguments[i]);
			}
		}
	}
}
Util.xInObject = function(object, _options) {
	if(u.debugURL()) {
		var return_string = false;
		var explore_objects = false;
		if(obj(_options)) {
			var _argument;
			for(_argument in _options) {
				switch(_argument) {
					case "return"     : return_string               = _options[_argument]; break;
					case "objects"    : explore_objects             = _options[_argument]; break;
				}
			}
		}
		var x, s = "--- start object ---\n";
		for(x in object) {
			if(explore_objects && object[x] && obj(object[x]) && !str(object[x].nodeName)) {
				s += x + "=" + object[x]+" => \n";
				s += u.xInObject(object[x], true);
			}
			else if(object[x] && obj(object[x]) && str(object[x].nodeName)) {
				s += x + "=" + object[x]+" -> " + u.nodeId(object[x], 1) + "\n";
			}
			else if(object[x] && fun(object[x])) {
				s += x + "=function\n";
			}
			else {
				s += x + "=" + object[x]+"\n";
			}
		}
		s += "--- end object ---\n";
		if(return_string) {
			return s;
		}
		else {
			u.bug(s);
		}
	}
}
Util.Animation = u.a = new function() {
	this.support3d = function() {
		if(this._support3d === undefined) {
			var node = u.ae(document.body, "div");
			try {
				u.as(node, "transform", "translate3d(10px, 10px, 10px)");
				if(u.gcs(node, "transform").match(/matrix3d\(1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 10, 10, 10, 1\)/)) {
					this._support3d = true;
				}
	 			else {
					this._support3d = false;
				}
			}
			catch(exception) {
				this._support3d = false;
			}
			document.body.removeChild(node);
		}
		return this._support3d;
	}
	this.transition = function(node, transition, callback) {
		try {
			var duration = transition.match(/[0-9.]+[ms]+/g);
			if(duration) {
				node.duration = duration[0].match("ms") ? parseFloat(duration[0]) : (parseFloat(duration[0]) * 1000);
				if(callback) {
					var transitioned;
					transitioned = (function(event) {
						u.e.removeEvent(event.target, u.a.transitionEndEventName(), transitioned);
						if(event.target == this) {
							u.a.transition(this, "none");
							if(fun(callback)) {
								var key = u.randomString(4);
								node[key] = callback;
								node[key](event);
								delete node[key];
								callback = null;
							}
							else if(fun(this[callback])) {
								this[callback](event);
							}
						}
						else {
						}
					});
					u.e.addEvent(node, u.a.transitionEndEventName(), transitioned);
				}
				else {
					u.e.addEvent(node, u.a.transitionEndEventName(), this._transitioned);
				}
			}
			else {
				node.duration = false;
			}
			u.as(node, "transition", transition);
		}
		catch(exception) {
			u.exception("u.a.transition", arguments, exception);
		}
	}
	this.transitionEndEventName = function() {
		if(!this._transition_end_event_name) {
			this._transition_end_event_name = "transitionend";
			var transitions = {
				"transition": "transitionend",
				"MozTransition": "transitionend",
				"msTransition": "transitionend",
				"webkitTransition": "webkitTransitionEnd",
				"OTransition": "otransitionend"
			};
			var x, div = document.createElement("div");
			for(x in transitions){
				if(typeof(div.style[x]) !== "undefined") {
					this._transition_end_event_name = transitions[x];
					break;
				}
			}
		}
		return this._transition_end_event_name;
	}
	this._transitioned = function(event) {
		if(event.target == this) {
			u.e.removeEvent(event.target, u.a.transitionEndEventName(), u.a._transitioned);
			u.a.transition(event.target, "none");
			if(fun(this.transitioned)) {
				this.transitioned_before = this.transitioned;
				this.transitioned(event);
				if(this.transitioned === this.transitioned_before) {
					delete this.transitioned;
				}
			}
		}
	}
	this.translate = function(node, x, y) {
		if(this.support3d()) {
			u.as(node, "transform", "translate3d("+x+"px, "+y+"px, 0)");
		}
		else {
			u.as(node, "transform", "translate("+x+"px, "+y+"px)");
		}
		node._x = x;
		node._y = y;
		node.offsetHeight;
	}
	this.rotate = function(node, deg) {
		u.as(node, "transform", "rotate("+deg+"deg)");
		node._rotation = deg;
		node.offsetHeight;
	}
	this.scale = function(node, scale) {
		u.as(node, "transform", "scale("+scale+")");
		node._scale = scale;
		node.offsetHeight;
	}
	this.setOpacity = this.opacity = function(node, opacity) {
		u.as(node, "opacity", opacity);
		node._opacity = opacity;
		node.offsetHeight;
	}
	this.setWidth = this.width = function(node, width) {
		width = width.toString().match(/\%|auto|px/) ? width : (width + "px");
		node.style.width = width;
		node._width = width;
		node.offsetHeight;
	}
	this.setHeight = this.height = function(node, height) {
		height = height.toString().match(/\%|auto|px/) ? height : (height + "px");
		node.style.height = height;
		node._height = height;
		node.offsetHeight;
	}
	this.setBgPos = this.bgPos = function(node, x, y) {
		x = x.toString().match(/\%|auto|px|center|top|left|bottom|right/) ? x : (x + "px");
		y = y.toString().match(/\%|auto|px|center|top|left|bottom|right/) ? y : (y + "px");
		node.style.backgroundPosition = x + " " + y;
		node._bg_x = x;
		node._bg_y = y;
		node.offsetHeight;
	}
	this.setBgColor = this.bgColor = function(node, color) {
		node.style.backgroundColor = color;
		node._bg_color = color;
		node.offsetHeight;
	}
	this._animationqueue = {};
	this.requestAnimationFrame = function(node, callback, duration) {
		if(!u.a.__animation_frame_start) {
			u.a.__animation_frame_start = Date.now();
		}
		var id = u.randomString();
		u.a._animationqueue[id] = {};
		u.a._animationqueue[id].id = id;
		u.a._animationqueue[id].node = node;
		u.a._animationqueue[id].callback = callback;
		u.a._animationqueue[id].duration = duration;
		u.t.setTimer(u.a, function() {u.a.finalAnimationFrame(id)}, duration);
		if(!u.a._animationframe) {
			window._requestAnimationFrame = eval(u.vendorProperty("requestAnimationFrame"));
			window._cancelAnimationFrame = eval(u.vendorProperty("cancelAnimationFrame"));
			u.a._animationframe = function(timestamp) {
				var id, animation;
				for(id in u.a._animationqueue) {
					animation = u.a._animationqueue[id];
					if(!animation["__animation_frame_start_"+id]) {
						animation["__animation_frame_start_"+id] = timestamp;
					}
					if(fun(animation.node[animation.callback])) {
						animation.node[animation.callback]((timestamp-animation["__animation_frame_start_"+id]) / animation.duration);
					}
				}
				if(Object.keys(u.a._animationqueue).length) {
					u.a._requestAnimationId = window._requestAnimationFrame(u.a._animationframe);
				}
			}
		}
		if(!u.a._requestAnimationId) {
			u.a._requestAnimationId = window._requestAnimationFrame(u.a._animationframe);
		}
		return id;
	}
	this.finalAnimationFrame = function(id) {
		var animation = u.a._animationqueue[id];
		animation["__animation_frame_start_"+id] = false;
		if(fun(animation.node[animation.callback])) {
			animation.node[animation.callback](1);
		}
		if(fun(animation.node.transitioned)) {
			animation.node.transitioned({});
		}
		delete u.a._animationqueue[id];
		if(!Object.keys(u.a._animationqueue).length) {
			this.cancelAnimationFrame(id);
		}
	}
	this.cancelAnimationFrame = function(id) {
		if(id && u.a._animationqueue[id]) {
			delete u.a._animationqueue[id];
		}
		if(u.a._requestAnimationId) {
			window._cancelAnimationFrame(u.a._requestAnimationId);
			u.a.__animation_frame_start = false;
			u.a._requestAnimationId = false;
		}
	}
}
Util.saveCookie = function(name, value, _options) {
	var expires = true;
	var path = false;
	var force = false;
	if(obj(_options)) {
		var _argument;
		for(_argument in _options) {
			switch(_argument) {
				case "expires"	: expires	= _options[_argument]; break;
				case "path"		: path		= _options[_argument]; break;
				case "force"	: force		= _options[_argument]; break;
			}
		}
	}
	if(!force && obj(window.localStorage) && obj(window.sessionStorage)) {
		if(expires === true) {
			window.sessionStorage.setItem(name, value);
		}
		else {
			window.localStorage.setItem(name, value);
		}
		return;
	}
	if(expires === false) {
		expires = ";expires=Mon, 04-Apr-2020 05:00:00 GMT";
	}
	else if(str(expires)) {
		expires = ";expires="+expires;
	}
	else {
		expires = "";
	}
	if(str(path)) {
		path = ";path="+path;
	}
	else {
		path = "";
	}
	document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + path + expires;
}
Util.getCookie = function(name) {
	var matches;
	if(obj(window.sessionStorage) && window.sessionStorage.getItem(name)) {
		return window.sessionStorage.getItem(name)
	}
	else if(obj(window.localStorage) && window.localStorage.getItem(name)) {
		return window.localStorage.getItem(name)
	}
	return (matches = document.cookie.match(encodeURIComponent(name) + "=([^;]+)")) ? decodeURIComponent(matches[1]) : false;
}
Util.deleteCookie = function(name, _options) {
	var path = false;
	if(obj(_options)) {
		var _argument;
		for(_argument in _options) {
			switch(_argument) {
				case "path"	: path	= _options[_argument]; break;
			}
		}
	}
	if(obj(window.sessionStorage)) {
		window.sessionStorage.removeItem(name);
	}
	if(obj(window.localStorage)) {
		window.localStorage.removeItem(name);
	}
	if(str(path)) {
		path = ";path="+path;
	}
	else {
		path = "";
	}
	document.cookie = encodeURIComponent(name) + "=" + path + ";expires=Thu, 01-Jan-70 00:00:01 GMT";
}
Util.saveNodeCookie = function(node, name, value, _options) {
	var ref = u.cookieReference(node, _options);
	var mem = JSON.parse(u.getCookie("man_mem"));
	if(!mem) {
		mem = {};
	}
	if(!mem[ref]) {
		mem[ref] = {};
	}
	mem[ref][name] = (value !== false && value !== undefined) ? value : "";
	u.saveCookie("man_mem", JSON.stringify(mem), {"path":"/"});
}
Util.getNodeCookie = function(node, name, _options) {
	var ref = u.cookieReference(node, _options);
	var mem = JSON.parse(u.getCookie("man_mem"));
	if(mem && mem[ref]) {
		if(name) {
			return (typeof(mem[ref][name]) != "undefined") ? mem[ref][name] : false;
		}
		else {
			return mem[ref];
		}
	}
	return false;
}
Util.deleteNodeCookie = function(node, name, _options) {
	var ref = u.cookieReference(node, _options);
	var mem = JSON.parse(u.getCookie("man_mem"));
	if(mem && mem[ref]) {
		if(name) {
			delete mem[ref][name];
		}
		else {
			delete mem[ref];
		}
	}
	u.saveCookie("man_mem", JSON.stringify(mem), {"path":"/"});
}
Util.cookieReference = function(node, _options) {
	var ref;
	var ignore_classnames = false;
	var ignore_classvars = false;
	if(obj(_options)) {
		var _argument;
		for(_argument in _options) {
			switch(_argument) {
				case "ignore_classnames"	: ignore_classnames	= _options[_argument]; break;
				case "ignore_classvars" 	: ignore_classvars	= _options[_argument]; break;
			}
		}
	}
	if(node.id) {
		ref = node.nodeName + "#" + node.id;
	}
	else {
		var node_identifier = "";
		if(node.name) {
			node_identifier = node.nodeName + "["+node.name+"]";
		}
		else if(node.className) {
			var classname = node.className;
			if(ignore_classnames) {
				var regex = new RegExp("(^| )("+ignore_classnames.split(",").join("|")+")($| )", "g");
				classname = classname.replace(regex, " ").replace(/[ ]{2,4}/, " ");
			}
			if(ignore_classvars) {
				classname = classname.replace(/\b[a-zA-Z_]+\:[\?\=\w\/\\#~\:\.\,\+\&\%\@\!\-]+\b/g, "").replace(/[ ]{2,4}/g, " ");
			}
			node_identifier = node.nodeName+"."+classname.trim().replace(/ /g, ".");
		}
		else {
			node_identifier = node.nodeName
		}
		var id_node = node;
		while(!id_node.id) {
			id_node = id_node.parentNode;
		}
		if(id_node.id) {
			ref = id_node.nodeName + "#" + id_node.id + " " + node_identifier;
		}
		else {
			ref = node_identifier;
		}
	}
	return ref;
}
Util.querySelector = u.qs = function(query, scope) {
	scope = scope ? scope : document;
	return scope.querySelector(query);
}
Util.querySelectorAll = u.qsa = function(query, scope) {
	try {
		scope = scope ? scope : document;
		return scope.querySelectorAll(query);
	}
	catch(exception) {
		u.exception("u.qsa", arguments, exception);
	}
	return [];
}
Util.getElement = u.ge = function(identifier, scope) {
	var node, nodes, i, regexp;
	if(document.getElementById(identifier)) {
		return document.getElementById(identifier);
	}
	scope = scope ? scope : document;
	regexp = new RegExp("(^|\\s)" + identifier + "(\\s|$|\:)");
	nodes = scope.getElementsByTagName("*");
	for(i = 0; i < nodes.length; i++) {
		node = nodes[i];
		if(regexp.test(node.className)) {
			return node;
		}
	}
	return scope.getElementsByTagName(identifier).length ? scope.getElementsByTagName(identifier)[0] : false;
}
Util.getElements = u.ges = function(identifier, scope) {
	var node, nodes, i, regexp;
	var return_nodes = new Array();
	scope = scope ? scope : document;
	regexp = new RegExp("(^|\\s)" + identifier + "(\\s|$|\:)");
	nodes = scope.getElementsByTagName("*");
	for(i = 0; i < nodes.length; i++) {
		node = nodes[i];
		if(regexp.test(node.className)) {
			return_nodes.push(node);
		}
	}
	return return_nodes.length ? return_nodes : scope.getElementsByTagName(identifier);
}
Util.parentNode = u.pn = function(node, _options) {
	var exclude = "";
	var include = "";
	if(obj(_options)) {
		var _argument;
		for(_argument in _options) {
			switch(_argument) {
				case "include"      : include       = _options[_argument]; break;
				case "exclude"      : exclude       = _options[_argument]; break;
			}
		}
	}
	var exclude_nodes = exclude ? u.qsa(exclude) : [];
	var include_nodes = include ? u.qsa(include) : [];
	node = node.parentNode;
	while(node && (node.nodeType == 3 || node.nodeType == 8 || (exclude && (u.inNodeList(node, exclude_nodes))) || (include && (!u.inNodeList(node, include_nodes))))) {
		node = node.parentNode;
	}
	return node;
}
Util.previousSibling = u.ps = function(node, _options) {
	var exclude = "";
	var include = "";
	if(obj(_options)) {
		var _argument;
		for(_argument in _options) {
			switch(_argument) {
				case "include"      : include       = _options[_argument]; break;
				case "exclude"      : exclude       = _options[_argument]; break;
			}
		}
	}
	var exclude_nodes = exclude ? u.qsa(exclude, node.parentNode) : [];
	var include_nodes = include ? u.qsa(include, node.parentNode) : [];
	node = node.previousSibling;
	while(node && (node.nodeType == 3 || node.nodeType == 8 || (exclude && (u.inNodeList(node, exclude_nodes))) || (include && (!u.inNodeList(node, include_nodes))))) {
		node = node.previousSibling;
	}
	return node;
}
Util.nextSibling = u.ns = function(node, _options) {
	var exclude = "";
	var include = "";
	if(obj(_options)) {
		var _argument;
		for(_argument in _options) {
			switch(_argument) {
				case "include"      : include       = _options[_argument]; break;
				case "exclude"      : exclude       = _options[_argument]; break;
			}
		}
	}
	var exclude_nodes = exclude ? u.qsa(exclude, node.parentNode) : [];
	var include_nodes = include ? u.qsa(include, node.parentNode) : [];
	node = node.nextSibling;
	while(node && (node.nodeType == 3 || node.nodeType == 8 || (exclude && (u.inNodeList(node, exclude_nodes))) || (include && (!u.inNodeList(node, include_nodes))))) {
		node = node.nextSibling;
	}
	return node;
}
Util.childNodes = u.cn = function(node, _options) {
	var exclude = "";
	var include = "";
	if(obj(_options)) {
		var _argument;
		for(_argument in _options) {
			switch(_argument) {
				case "include"      : include       = _options[_argument]; break;
				case "exclude"      : exclude       = _options[_argument]; break;
			}
		}
	}
	var exclude_nodes = exclude ? u.qsa(exclude, node) : [];
	var include_nodes = include ? u.qsa(include, node) : [];
	var i, child;
	var children = new Array();
	for(i = 0; i < node.childNodes.length; i++) {
		child = node.childNodes[i]
		if(child && child.nodeType != 3 && child.nodeType != 8 && (!exclude || (!u.inNodeList(child, exclude_nodes))) && (!include || (u.inNodeList(child, include_nodes)))) {
			children.push(child);
		}
	}
	return children;
}
Util.appendElement = u.ae = function(_parent, node_type, attributes) {
	try {
		var node = (obj(node_type)) ? node_type : (node_type == "svg" ? document.createElementNS("http://www.w3.org/2000/svg", node_type) : document.createElement(node_type));
		node = _parent.appendChild(node);
		if(attributes) {
			var attribute;
			for(attribute in attributes) {
				if(attribute == "html") {
					node.innerHTML = attributes[attribute];
				}
				else {
					node.setAttribute(attribute, attributes[attribute]);
				}
			}
		}
		return node;
	}
	catch(exception) {
		u.exception("u.ae", arguments, exception);
	}
	return false;
}
Util.insertElement = u.ie = function(_parent, node_type, attributes) {
	try {
		var node = (obj(node_type)) ? node_type : (node_type == "svg" ? document.createElementNS("http://www.w3.org/2000/svg", node_type) : document.createElement(node_type));
		node = _parent.insertBefore(node, _parent.firstChild);
		if(attributes) {
			var attribute;
			for(attribute in attributes) {
				if(attribute == "html") {
					node.innerHTML = attributes[attribute];
				}
				else {
					node.setAttribute(attribute, attributes[attribute]);
				}
			}
		}
		return node;
	}
	catch(exception) {
		u.exception("u.ie", arguments, exception);
	}
	return false;
}
Util.wrapElement = u.we = function(node, node_type, attributes) {
	try {
		var wrapper_node = node.parentNode.insertBefore(document.createElement(node_type), node);
		if(attributes) {
			var attribute;
			for(attribute in attributes) {
				wrapper_node.setAttribute(attribute, attributes[attribute]);
			}
		}	
		wrapper_node.appendChild(node);
		return wrapper_node;
	}
	catch(exception) {
		u.exception("u.we", arguments, exception);
	}
	return false;
}
Util.wrapContent = u.wc = function(node, node_type, attributes) {
	try {
		var wrapper_node = document.createElement(node_type);
		if(attributes) {
			var attribute;
			for(attribute in attributes) {
				wrapper_node.setAttribute(attribute, attributes[attribute]);
			}
		}	
		while(node.childNodes.length) {
			wrapper_node.appendChild(node.childNodes[0]);
		}
		node.appendChild(wrapper_node);
		return wrapper_node;
	}
	catch(exception) {
		u.exception("u.wc", arguments, exception);
	}
	return false;
}
Util.textContent = u.text = function(node) {
	try {
		return node.textContent;
	}
	catch(exception) {
		u.exception("u.text", arguments, exception);
	}
	return "";
}
Util.clickableElement = u.ce = function(node, _options) {
	node._use_link = "a";
	node._click_type = "manual";
	if(obj(_options)) {
		var _argument;
		for(_argument in _options) {
			switch(_argument) {
				case "use"			: node._use_link		= _options[_argument]; break;
				case "type"			: node._click_type		= _options[_argument]; break;
			}
		}
	}
	var a = (node.nodeName.toLowerCase() == "a" ? node : u.qs(node._use_link, node));
	if(a) {
		u.ac(node, "link");
		if(a.getAttribute("href") !== null) {
			node.url = a.href;
			a.removeAttribute("href");
			node._a = a;
		}
	}
	else {
		u.ac(node, "clickable");
	}
	if(obj(u.e) && fun(u.e.click)) {
		u.e.click(node, _options);
		if(node._click_type == "link") {
			node.clicked = function(event) {
				if(fun(node.preClicked)) {
					node.preClicked();
				}
				if(event && (event.metaKey || event.ctrlKey || (this._a && this._a.target))) {
					window.open(this.url);
				}
				else {
					if(obj(u.h) && u.h.is_listening) {
						u.h.navigate(this.url, this);
					}
					else {
						location.href = this.url;
					}
				}
			}
		}
	}
	return node;
}
Util.classVar = u.cv = function(node, var_name) {
	try {
		var regexp = new RegExp("(\^| )" + var_name + ":[?=\\w/\\#~:.,?+=?&%@!\\-]*");
		var match = node.className.match(regexp);
		if(match) {
			return match[0].replace(var_name + ":", "").trim();
		}
	}
	catch(exception) {
		u.exception("u.cv", arguments, exception);
	}
	return false;
}
Util.setClass = u.sc = function(node, classname, dom_update) {
	var old_class;
	if(node instanceof SVGElement) {
		old_class = node.className.baseVal;
		node.setAttribute("class", classname);
	}
	else {
		old_class = node.className;
		node.className = classname;
	}
	dom_update = (dom_update === false) || (node.offsetTop);
	return old_class;
}
Util.hasClass = u.hc = function(node, classname) {
	if(node.classList.contains(classname)) {
		return true;
	}
	else {
		var regexp = new RegExp("(^|\\s)(" + classname + ")(\\s|$)");
		if(node instanceof SVGElement) {
			if(regexp.test(node.className.baseVal)) {
				return true;
			}
		}
		else {
			if(regexp.test(node.className)) {
				return true;
			}
		}
	}
	return false;
}
Util.addClass = u.ac = function(node, classname, dom_update) {
	var classnames = classname.split(" ");
	while(classnames.length) {
		node.classList.add(classnames.shift());
	}
	dom_update = (dom_update === false) || (node.offsetTop);
	return node.className;
}
Util.removeClass = u.rc = function(node, classname, dom_update) {
	if(node.classList.contains(classname)) {
		node.classList.remove(classname);
	}
	else {
		var regexp = new RegExp("(^|\\s)(" + classname + ")(?=[\\s]|$)", "g");
		if(node instanceof SVGElement) {
			node.setAttribute("class", node.className.baseVal.replace(regexp, " ").trim().replace(/[\s]{2}/g, " "));
		}
		else {
			node.className = node.className.replace(regexp, " ").trim().replace(/[\s]{2}/g, " ");
		}
	}
	dom_update = (dom_update === false) || (node.offsetTop);
	return node.className;
}
Util.toggleClass = u.tc = function(node, classname, _classname, dom_update) {
	if(u.hc(node, classname)) {
		u.rc(node, classname, dom_update);
		if(_classname) {
			u.ac(node, _classname, dom_update);
		}
	}
	else {
		u.ac(node, classname);
		if(_classname) {
			u.rc(node, _classname, dom_update);
		}
	}
	dom_update = (dom_update === false) || (node.offsetTop);
	return node.className;
}
Util.applyStyle = u.as = function(node, property, value, dom_update) {
	node.style[u.vendorProperty(property)] = value;
	dom_update = (dom_update === false) || (node.offsetTop);
}
Util.applyStyles = u.ass = function(node, styles, dom_update) {
	if(styles) {
		var style;
		for(style in styles) {
			if(obj(u.a) && style == "transition") {
				u.a.transition(node, styles[style]);
			}
			else {
				node.style[u.vendorProperty(style)] = styles[style];
			}
		}
	}
	dom_update = (dom_update === false) || (node.offsetTop);
}
Util.getComputedStyle = u.gcs = function(node, property) {
	var dom_update = node.offsetHeight;
	property = (u.vendorProperty(property).replace(/([A-Z]{1})/g, "-$1")).toLowerCase().replace(/^(webkit|ms)/, "-$1");
	return window.getComputedStyle(node, null).getPropertyValue(property);
}
Util.hasFixedParent = u.hfp = function(node) {
	while(node.nodeName.toLowerCase() != "body") {
		if(u.gcs(node.parentNode, "position").match("fixed")) {
			return true;
		}
		node = node.parentNode;
	}
	return false;
}
u.contains = function(scope, node) {
	if(scope != node) {
		if(scope.contains(node)) {
			return true
		}
	}
	return false;
}
u.containsOrIs = function(scope, node) {
	if(scope == node || u.contains(scope, node)) {
		return true
	}
	return false;
}
u.elementMatches = u.em = function(node, selector) {
	return node.matches(selector);
}
Util.insertAfter = u.ia = function(after_node, insert_node) {
	var next_node = u.ns(after_node);
	if(next_node) {
		after_node.parentNode.insertBefore(next_node, insert_node);
	}
	else {
		after_node.parentNode.appendChild(insert_node);
	}
}
Util.selectText = function(node) {
	var selection = window.getSelection();
	var range = document.createRange();
	range.selectNodeContents(node);
	selection.removeAllRanges();
	selection.addRange(range);
}
Util.inNodeList = function(node, list) {
	var i, list_node;
	for(i = 0; i < list.length; i++) {
		list_node = list[i]
		if(list_node === node) {
			return true;
		}
	}
	return false;
}
u.easings = new function() {
	this["ease-in"] = function(progress) {
		return Math.pow((progress), 3);
	}
	this["linear"] = function(progress) {
		return progress;
	}
	this["ease-out"] = function(progress) {
		return 1 - Math.pow(1 - ((progress)), 3);
	}
	this["linear"] = function(progress) {
		return (progress);
	}
	this["ease-in-out-veryslow"] = function(progress) {
		if(progress > 0.5) {
			return 4*Math.pow((progress-1),3)+1;
		}
		return 4*Math.pow(progress,3);  
	}
	this["ease-in-out"] = function(progress) {
		if(progress > 0.5) {
			return 1 - Math.pow(1 - ((progress)), 2);
		}
		return Math.pow((progress), 2);
	}
	this["ease-out-slow"] = function(progress) {
		return 1 - Math.pow(1 - ((progress)), 2);
	}
	this["ease-in-slow"] = function(progress) {
		return Math.pow((progress), 2);
	}
	this["ease-in-veryslow"] = function(progress) {
		return Math.pow((progress), 1.5);
	}
	this["ease-in-fast"] = function(progress) {
		return Math.pow((progress), 4);
	}
	this["easeOutQuad"] = function (progress) {
		d = 1;
		b = 0;
		c = progress;
		t = progress;
		t /= d;
		return -c * t*(t-2) + b;
	};
	this["easeOutCubic"] = function (progress) {
		d = 1;
		b = 0;
		c = progress;
		t = progress;
		t /= d;
		t--;
		return c*(t*t*t + 1) + b;
	};
	this["easeOutQuint"] = function (progress) {
		d = 1;
		b = 0;
		c = progress;
		t = progress;
		t /= d;
		t--;
		return c*(t*t*t*t*t + 1) + b;
	};
	this["easeInOutSine"] = function (progress) {
		d = 1;
		b = 0;
		c = progress;
		t = progress;
		return -c/2 * (Math.cos(Math.PI*t/d) - 1) + b;
	};
	this["easeInOutElastic"] = function (progress) {
		d = 1;
		b = 0;
		c = progress;
		t = progress;
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d/2)==2) return b+c;  if (!p) p=d*(.3*1.5);
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		if (t < 1) return -.5*(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
		return a*Math.pow(2,-10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )*.5 + c + b;
	}
	this["easeOutBounce"] = function (progress) {
		d = 1;
		b = 0;
		c = progress;
		t = progress;
			if ((t/=d) < (1/2.75)) {
				return c*(7.5625*t*t) + b;
			} else if (t < (2/2.75)) {
				return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
			} else if (t < (2.5/2.75)) {
				return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
			} else {
				return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
			}
	}
	this["easeInBack"] = function (progress) {
		var s = 1.70158;
		d = 1;
		b = 0;
		c = progress;
		t = progress;
			return c*(t/=d)*t*((s+1)*t - s) + b;
	}
}
Util.Events = u.e = new function() {
	this.event_pref = typeof(document.ontouchmove) == "undefined" || (navigator.maxTouchPoints > 1 && navigator.userAgent.match(/Windows/i)) ? "mouse" : "touch";
	if (navigator.userAgent.match(/Windows/i) && ((obj(document.ontouchmove) && obj(document.onmousemove)) || (fun(document.ontouchmove) && fun(document.onmousemove)))) {
		this.event_support = "multi";
	}
	else if (obj(document.ontouchmove) || fun(document.ontouchmove)) {
		this.event_support = "touch";
	}
	else {
		this.event_support = "mouse";
	}
	this.events = {
		"mouse": {
			"start":"mousedown",
			"move":"mousemove",
			"end":"mouseup",
			"over":"mouseover",
			"out":"mouseout"
		},
		"touch": {
			"start":"touchstart",
			"move":"touchmove",
			"end":"touchend",
			"over":"touchstart",
			"out":"touchend"
		}
	}
	this.kill = function(event) {
		if(event) {
			event.preventDefault();
			event.stopPropagation();
		}
	}
	this.addEvent = function(node, type, action) {
		try {
			node.addEventListener(type, action, false);
		}
		catch(exception) {
			u.exception("u.e.addEvent", arguments, exception);
		}
	}
	this.removeEvent = function(node, type, action) {
		try {
			node.removeEventListener(type, action, false);
		}
		catch(exception) {
			u.exception("u.e.removeEvent", arguments, exception);
		}
	}
	this.addStartEvent = this.addDownEvent = function(node, action) {
		if(this.event_support == "multi") {
			u.e.addEvent(node, this.events.mouse.start, action);
			u.e.addEvent(node, this.events.touch.start, action);
		}
		else {
			u.e.addEvent(node, this.events[this.event_support].start, action);
		}
	}
	this.removeStartEvent = this.removeDownEvent = function(node, action) {
		if(this.event_support == "multi") {
			u.e.removeEvent(node, this.events.mouse.start, action);
			u.e.removeEvent(node, this.events.touch.start, action);
		}
		else {
			u.e.removeEvent(node, this.events[this.event_support].start, action);
		}
	}
	this.addMoveEvent = function(node, action) {
		if(this.event_support == "multi") {
			u.e.addEvent(node, this.events.mouse.move, action);
			u.e.addEvent(node, this.events.touch.move, action);
		}
		else {
			u.e.addEvent(node, this.events[this.event_support].move, action);
		}
	}
	this.removeMoveEvent = function(node, action) {
		if(this.event_support == "multi") {
			u.e.removeEvent(node, this.events.mouse.move, action);
			u.e.removeEvent(node, this.events.touch.move, action);
		}
		else {
			u.e.removeEvent(node, this.events[this.event_support].move, action);
		}
	}
	this.addEndEvent = this.addUpEvent = function(node, action) {
		if(this.event_support == "multi") {
			u.e.addEvent(node, this.events.mouse.end, action);
			u.e.addEvent(node, this.events.touch.end, action);
		}
		else {
			u.e.addEvent(node, this.events[this.event_support].end, action);
		}
	}
	this.removeEndEvent = this.removeUpEvent = function(node, action) {
		if(this.event_support == "multi") {
			u.e.removeEvent(node, this.events.mouse.end, action);
			u.e.removeEvent(node, this.events.touch.end, action);
		}
		else {
			u.e.removeEvent(node, this.events[this.event_support].end, action);
		}
	}
	this.addOverEvent = function(node, action) {
		if(this.event_support == "multi") {
			u.e.addEvent(node, this.events.mouse.over, action);
			u.e.addEvent(node, this.events.touch.over, action);
		}
		else {
			u.e.addEvent(node, this.events[this.event_support].over, action);
		}
	}
	this.removeOverEvent = function(node, action) {
		if(this.event_support == "multi") {
			u.e.removeEvent(node, this.events.mouse.over, action);
			u.e.removeEvent(node, this.events.touch.over, action);
		}
		else {
			u.e.removeEvent(node, this.events[this.event_support].over, action);
		}
	}
	this.addOutEvent = function(node, action) {
		if(this.event_support == "multi") {
			u.e.addEvent(node, this.events.mouse.out, action);
			u.e.addEvent(node, this.events.touch.out, action);
		}
		else {
			u.e.addEvent(node, this.events[this.event_support].out, action);
		}
	}
	this.removeOutEvent = function(node, action) {
		if(this.event_support == "multi") {
			u.e.removeEvent(node, this.events.mouse.out, action);
			u.e.removeEvent(node, this.events.touch.out, action);
		}
		else {
			u.e.removeEvent(node, this.events[this.event_support].out, action);
		}
	}
	this.resetClickEvents = function(node) {
		u.t.resetTimer(node.t_held);
		u.t.resetTimer(node.t_clicked);
		this.removeEvent(node, "mouseup", this._dblclicked);
		this.removeEvent(node, "touchend", this._dblclicked);
		this.removeEvent(node, "mouseup", this._rightclicked);
		this.removeEvent(node, "touchend", this._rightclicked);
		this.removeEvent(node, "mousemove", this._cancelClick);
		this.removeEvent(node, "touchmove", this._cancelClick);
		this.removeEvent(node, "mouseout", this._cancelClick);
		this.removeEvent(node, "mousemove", this._move);
		this.removeEvent(node, "touchmove", this._move);
	}
	this.resetEvents = function(node) {
		this.resetClickEvents(node);
		if(fun(this.resetDragEvents)) {
			this.resetDragEvents(node);
		}
	}
	this.resetNestedEvents = function(node) {
		while(node && node.nodeName != "HTML") {
			this.resetEvents(node);
			node = node.parentNode;
		}
	}
	this._inputStart = function(event) {
		this.event_var = event;
		this.input_timestamp = event.timeStamp;
		this.start_event_x = u.eventX(event);
		this.start_event_y = u.eventY(event);
		this.current_xps = 0;
		this.current_yps = 0;
		this.move_timestamp = event.timeStamp;
		this.move_last_x = 0;
		this.move_last_y = 0;
		this.swiped = false;
		if(!event.button) {
			if(this.e_click || this.e_dblclick || this.e_hold) {
				if(event.type.match(/mouse/)) {
					var node = this;
					while(node) {
						if(node.e_drag || node.e_swipe) {
							u.e.addMoveEvent(this, u.e._cancelClick);
							break;
						}
						else {
							node = node.parentNode;
						}
					}
					u.e.addEvent(this, "mouseout", u.e._cancelClick);
				}
				else {
					u.e.addMoveEvent(this, u.e._cancelClick);
				}
				u.e.addMoveEvent(this, u.e._move);
				u.e.addEndEvent(this, u.e._dblclicked);
				if(this.e_hold) {
					this.t_held = u.t.setTimer(this, u.e._held, 750);
				}
			}
			if(this.e_drag || this.e_swipe) {
				u.e.addMoveEvent(this, u.e._pick);
				u.e.addEndEvent(this, u.e._cancelPick);
			}
			if(this.e_scroll) {
				u.e.addMoveEvent(this, u.e._scrollStart);
				u.e.addEndEvent(this, u.e._scrollEnd);
			}
		}
		else if(event.button === 2) {
			if(this.e_rightclick) {
				if(event.type.match(/mouse/)) {
					u.e.addEvent(this, "mouseout", u.e._cancelClick);
				}
				else {
					u.e.addMoveEvent(this, u.e._cancelClick);
				}
				u.e.addMoveEvent(this, u.e._move);
				u.e.addEndEvent(this, u.e._rightclicked);
			}
		}
		if(fun(this.inputStarted)) {
			this.inputStarted(event);
		}
	}
	this._cancelClick = function(event) {
		var offset_x = u.eventX(event) - this.start_event_x;
		var offset_y = u.eventY(event) - this.start_event_y;
		if(event.type.match(/mouseout/) || (event.type.match(/move/) && (Math.abs(offset_x) > 15 || Math.abs(offset_y) > 15))) {
			u.e.resetClickEvents(this);
			if(fun(this.clickCancelled)) {
				this.clickCancelled(event);
			}
		}
	}
	this._move = function(event) {
		if(fun(this.moved)) {
			this.current_x = u.eventX(event) - this.start_event_x;
			this.current_y = u.eventY(event) - this.start_event_y;
			this.current_xps = Math.round(((this.current_x - this.move_last_x) / (event.timeStamp - this.move_timestamp)) * 1000);
			this.current_yps = Math.round(((this.current_y - this.move_last_y) / (event.timeStamp - this.move_timestamp)) * 1000);
			this.move_timestamp = event.timeStamp;
			this.move_last_x = this.current_x;
			this.move_last_y = this.current_y;
			this.moved(event);
		}
	}
	this.hold = function(node, _options) {
		node.e_hold_options = _options ? _options : {};
		node.e_hold_options.eventAction = u.stringOr(node.e_hold_options.eventAction, "Held");
		node.e_hold = true;
		u.e.addStartEvent(node, this._inputStart);
	}
	this._held = function(event) {
		this.e_hold_options.event = event;
		u.stats.event(this, this.e_hold_options);
		u.e.resetNestedEvents(this);
		if(fun(this.held)) {
			this.held(event);
		}
	}
	this.click = this.tap = function(node, _options) {
		node.e_click_options = _options ? _options : {};
		node.e_click_options.eventAction = u.stringOr(node.e_click_options.eventAction, "Clicked");
		node.e_click = true;
		u.e.addStartEvent(node, this._inputStart);
	}
	this._clicked = function(event) {
		if(this.e_click_options) {
			this.e_click_options.event = event;
			u.stats.event(this, this.e_click_options);
		}
		u.e.resetNestedEvents(this);
		if(fun(this.clicked)) {
			this.clicked(event);
		}
	}
	this.rightclick = function(node, _options) {
		node.e_rightclick_options = _options ? _options : {};
		node.e_rightclick_options.eventAction = u.stringOr(node.e_rightclick_options.eventAction, "RightClicked");
		node.e_rightclick = true;
		u.e.addStartEvent(node, this._inputStart);
		u.e.addEvent(node, "contextmenu", function(event){u.e.kill(event);});
	}
	this._rightclicked = function(event) {
		u.bug("_rightclicked:", this);
		if(this.e_rightclick_options) {
			this.e_rightclick_options.event = event;
			u.stats.event(this, this.e_rightclick_options);
		}
		u.e.resetNestedEvents(this);
		if(fun(this.rightclicked)) {
			this.rightclicked(event);
		}
	}
	this.dblclick = this.doubleclick = this.doubletap = this.dbltap = function(node, _options) {
		node.e_dblclick_options = _options ? _options : {};
		node.e_dblclick_options.eventAction = u.stringOr(node.e_dblclick_options.eventAction, "DblClicked");
		node.e_dblclick = true;
		u.e.addStartEvent(node, this._inputStart);
	}
	this._dblclicked = function(event) {
		if(u.t.valid(this.t_clicked) && event) {
			this.e_dblclick_options.event = event;
			u.stats.event(this, this.e_dblclick_options);
			u.e.resetNestedEvents(this);
			if(fun(this.dblclicked)) {
				this.dblclicked(event);
			}
			return;
		}
		else if(!this.e_dblclick) {
			this._clicked = u.e._clicked;
			this._clicked(event);
		}
		else if(event.type == "timeout") {
			this._clicked = u.e._clicked;
			this._clicked(this.event_var);
		}
		else {
			u.e.resetNestedEvents(this);
			this.t_clicked = u.t.setTimer(this, u.e._dblclicked, 400);
		}
	}
	this.hover = function(node, _options) {
		node._hover_out_delay = 100;
		node._hover_over_delay = 0;
		node._callback_out = "out";
		node._callback_over = "over";
		if(obj(_options)) {
			var argument;
			for(argument in _options) {
				switch(argument) {
					case "over"				: node._callback_over		= _options[argument]; break;
					case "out"				: node._callback_out		= _options[argument]; break;
					case "delay_over"		: node._hover_over_delay	= _options[argument]; break;
					case "delay"			: node._hover_out_delay		= _options[argument]; break;
				}
			}
		}
		node.e_hover = true;
		u.e.addOverEvent(node, this._over);
		u.e.addOutEvent(node, this._out);
	}
	this._over = function(event) {
		u.t.resetTimer(this.t_out);
		if(!this._hover_over_delay) {
			u.e.__over.call(this, event);
		}
		else if(!u.t.valid(this.t_over)) {
			this.t_over = u.t.setTimer(this, u.e.__over, this._hover_over_delay, event);
		}
	}
	this.__over = function(event) {
		u.t.resetTimer(this.t_out);
		if(!this.is_hovered) {
			this.is_hovered = true;
			u.e.removeOverEvent(this, u.e._over);
			u.e.addOverEvent(this, u.e.__over);
			if(fun(this[this._callback_over])) {
				this[this._callback_over](event);
			}
		}
	}
	this._out = function(event) {
		u.t.resetTimer(this.t_over);
		u.t.resetTimer(this.t_out);
		this.t_out = u.t.setTimer(this, u.e.__out, this._hover_out_delay, event);
	}
	this.__out = function(event) {
		this.is_hovered = false;
		u.e.removeOverEvent(this, u.e.__over);
		u.e.addOverEvent(this, u.e._over);
		if(fun(this[this._callback_out])) {
			this[this._callback_out](event);
		}
	}
}
u.e.addDOMReadyEvent = function(action) {
	if(document.readyState && document.addEventListener) {
		if((document.readyState == "interactive" && !u.browser("ie")) || document.readyState == "complete" || document.readyState == "loaded") {
			action();
		}
		else {
			var id = u.randomString();
			window["DOMReady_" + id] = action;
			eval('window["_DOMReady_' + id + '"] = function() {window["DOMReady_'+id+'"](); u.e.removeEvent(document, "DOMContentLoaded", window["_DOMReady_' + id + '"])}');
			u.e.addEvent(document, "DOMContentLoaded", window["_DOMReady_" + id]);
		}
	}
	else {
		u.e.addOnloadEvent(action);
	}
}
u.e.addOnloadEvent = function(action) {
	if(document.readyState && (document.readyState == "complete" || document.readyState == "loaded")) {
		action();
	}
	else {
		var id = u.randomString();
		window["Onload_" + id] = action;
		eval('window["_Onload_' + id + '"] = function() {window["Onload_'+id+'"](); u.e.removeEvent(window, "load", window["_Onload_' + id + '"])}');
		u.e.addEvent(window, "load", window["_Onload_" + id]);
	}
}
u.e.addWindowEvent = function(node, type, action) {
	var id = u.randomString();
	window["_OnWindowEvent_node_"+ id] = node;
	if(fun(action)) {
		eval('window["_OnWindowEvent_callback_' + id + '"] = function(event) {window["_OnWindowEvent_node_'+ id + '"]._OnWindowEvent_callback_'+id+' = '+action+'; window["_OnWindowEvent_node_'+ id + '"]._OnWindowEvent_callback_'+id+'(event);};');
	} 
	else {
		eval('window["_OnWindowEvent_callback_' + id + '"] = function(event) {if(fun(window["_OnWindowEvent_node_'+ id + '"]["'+action+'"])) {window["_OnWindowEvent_node_'+id+'"]["'+action+'"](event);}};');
	}
	u.e.addEvent(window, type, window["_OnWindowEvent_callback_" + id]);
	return id;
}
u.e.removeWindowEvent = function(node, type, id) {
	u.e.removeEvent(window, type, window["_OnWindowEvent_callback_"+id]);
	delete window["_OnWindowEvent_node_"+id];
	delete window["_OnWindowEvent_callback_"+id];
}
u.e.addWindowStartEvent = function(node, action) {
	var id = u.randomString();
	window["_Onstart_node_"+ id] = node;
	if(fun(action)) {
		eval('window["_Onstart_callback_' + id + '"] = function(event) {window["_Onstart_node_'+ id + '"]._Onstart_callback_'+id+' = '+action+'; window["_Onstart_node_'+ id + '"]._Onstart_callback_'+id+'(event);};');
	} 
	else {
		eval('window["_Onstart_callback_' + id + '"] = function(event) {if(fun(window["_Onstart_node_'+ id + '"]["'+action+'"])) {window["_Onstart_node_'+id+'"]["'+action+'"](event);}};');
	}
	u.e.addStartEvent(window, window["_Onstart_callback_" + id]);
	return id;
}
u.e.removeWindowStartEvent = function(node, id) {
	u.e.removeStartEvent(window, window["_Onstart_callback_"+id]);
	delete window["_Onstart_node_"+id]["_Onstart_callback_"+id];
	delete window["_Onstart_node_"+id];
	delete window["_Onstart_callback_"+id];
}
u.e.addWindowMoveEvent = function(node, action) {
	var id = u.randomString();
	window["_Onmove_node_"+ id] = node;
	if(fun(action)) {
		eval('window["_Onmove_callback_' + id + '"] = function(event) {window["_Onmove_node_'+ id + '"]._Onmove_callback_'+id+' = '+action+'; window["_Onmove_node_'+ id + '"]._Onmove_callback_'+id+'(event);};');
	} 
	else {
		eval('window["_Onmove_callback_' + id + '"] = function(event) {if(fun(window["_Onmove_node_'+ id + '"]["'+action+'"])) {window["_Onmove_node_'+id+'"]["'+action+'"](event);}};');
	}
	u.e.addMoveEvent(window, window["_Onmove_callback_" + id]);
	return id;
}
u.e.removeWindowMoveEvent = function(node, id) {
	u.e.removeMoveEvent(window, window["_Onmove_callback_" + id]);
	delete window["_Onmove_node_"+ id]["_Onmove_callback_"+id];
	delete window["_Onmove_node_"+ id];
	delete window["_Onmove_callback_"+ id];
}
u.e.addWindowEndEvent = function(node, action) {
	var id = u.randomString();
	window["_Onend_node_"+ id] = node;
	if(fun(action)) {
		eval('window["_Onend_callback_' + id + '"] = function(event) {window["_Onend_node_'+ id + '"]._Onend_callback_'+id+' = '+action+'; window["_Onend_node_'+ id + '"]._Onend_callback_'+id+'(event);};');
	} 
	else {
		eval('window["_Onend_callback_' + id + '"] = function(event) {if(fun(window["_Onend_node_'+ id + '"]["'+action+'"])) {window["_Onend_node_'+id+'"]["'+action+'"](event);}};');
	}
	u.e.addEndEvent(window, window["_Onend_callback_" + id]);
	return id;
}
u.e.removeWindowEndEvent = function(node, id) {
	u.e.removeEndEvent(window, window["_Onend_callback_" + id]);
	delete window["_Onend_node_"+ id]["_Onend_callback_"+id];
	delete window["_Onend_node_"+ id];
	delete window["_Onend_callback_"+ id];
}
u.e.resetDragEvents = function(node) {
	node._moves_pick = 0;
	this.removeEvent(node, "mousemove", this._pick);
	this.removeEvent(node, "touchmove", this._pick);
	this.removeEvent(node, "mousemove", this._drag);
	this.removeEvent(node, "touchmove", this._drag);
	this.removeEvent(node, "mouseup", this._drop);
	this.removeEvent(node, "touchend", this._drop);
	this.removeEvent(node, "mouseup", this._cancelPick);
	this.removeEvent(node, "touchend", this._cancelPick);
	this.removeEvent(node, "mouseout", this._dropOut);
	this.removeEvent(node, "mousemove", this._scrollStart);
	this.removeEvent(node, "touchmove", this._scrollStart);
	this.removeEvent(node, "mousemove", this._scrolling);
	this.removeEvent(node, "touchmove", this._scrolling);
	this.removeEvent(node, "mouseup", this._scrollEnd);
	this.removeEvent(node, "touchend", this._scrollEnd);
}
u.e.overlap = function(node, boundaries, strict) {
	if(boundaries.constructor.toString().match("Array")) {
		var boundaries_start_x = Number(boundaries[0]);
		var boundaries_start_y = Number(boundaries[1]);
		var boundaries_end_x = Number(boundaries[2]);
		var boundaries_end_y = Number(boundaries[3]);
	}
	else if(boundaries.constructor.toString().match("HTML")) {
		var boundaries_start_x = u.absX(boundaries) - u.absX(node);
		var boundaries_start_y =  u.absY(boundaries) - u.absY(node);
		var boundaries_end_x = Number(boundaries_start_x + boundaries.offsetWidth);
		var boundaries_end_y = Number(boundaries_start_y + boundaries.offsetHeight);
	}
	var node_start_x = Number(node._x);
	var node_start_y = Number(node._y);
	var node_end_x = Number(node_start_x + node.offsetWidth);
	var node_end_y = Number(node_start_y + node.offsetHeight);
	if(strict) {
		if(node_start_x >= boundaries_start_x && node_start_y >= boundaries_start_y && node_end_x <= boundaries_end_x && node_end_y <= boundaries_end_y) {
			return true;
		}
		else {
			return false;
		}
	} 
	else if(node_end_x < boundaries_start_x || node_start_x > boundaries_end_x || node_end_y < boundaries_start_y || node_start_y > boundaries_end_y) {
		return false;
	}
	return true;
}
u.e.drag = function(node, boundaries, _options) {
	node.e_drag_options = _options ? _options : {};
	node.e_drag = true;
	if(node.childNodes.length < 2 && node.innerHTML.trim() == "") {
		node.innerHTML = "&nbsp;";
	}
	node.distance_to_pick = 2;
	node.drag_strict = true;
	node.drag_overflow = false;
	node.drag_elastica = 0;
	node.drag_dropout = true;
	node.show_bounds = false;
	node.callback_ready = "ready";
	node.callback_picked = "picked";
	node.callback_moved = "moved";
	node.callback_dropped = "dropped";
	if(obj(_options)) {
		var _argument;
		for(_argument in _options) {
			switch(_argument) {
				case "strict"			: node.drag_strict			= _options[_argument]; break;
				case "overflow"			: node.drag_overflow		= _options[_argument]; break;
				case "elastica"			: node.drag_elastica		= Number(_options[_argument]); break;
				case "dropout"			: node.drag_dropout			= _options[_argument]; break;
				case "show_bounds"		: node.show_bounds			= _options[_argument]; break; 
				case "vertical_lock"	: node.vertical_lock		= _options[_argument]; break;
				case "horizontal_lock"	: node.horizontal_lock		= _options[_argument]; break;
				case "callback_picked"	: node.callback_picked		= _options[_argument]; break;
				case "callback_moved"	: node.callback_moved		= _options[_argument]; break;
				case "callback_dropped"	: node.callback_dropped		= _options[_argument]; break;
			}
		}
	}
	u.e.setDragBoundaries(node, boundaries);
	u.e.addStartEvent(node, this._inputStart);
	if(fun(node[node.callback_ready])) {
		node[node.callback_ready]();
	}
}
u.e._pick = function(event) {
	var init_speed_x = Math.abs(this.start_event_x - u.eventX(event));
	var init_speed_y = Math.abs(this.start_event_y - u.eventY(event));
	if(
		(init_speed_x > init_speed_y && this.only_horizontal) || 
		(init_speed_x < init_speed_y && this.only_vertical) ||
		(!this.only_vertical && !this.only_horizontal)) {
		if((init_speed_x > this.distance_to_pick || init_speed_y > this.distance_to_pick)) {
			u.e.resetNestedEvents(this);
			u.e.kill(event);
			if(u.hasFixedParent(this)) {
				this.has_fixed_parent = true;
			}
			else {
				this.has_fixed_parent = false;
			}
			this.move_timestamp = event.timeStamp;
			this.move_last_x = this._x;
			this.move_last_y = this._y;
			if(u.hasFixedParent(this)) {
				this.start_input_x = u.eventX(event) - this._x - u.scrollX(); 
				this.start_input_y = u.eventY(event) - this._y - u.scrollY();
			}
			else {
				this.start_input_x = u.eventX(event) - this._x; 
				this.start_input_y = u.eventY(event) - this._y;
			}
			this.current_xps = 0;
			this.current_yps = 0;
			u.a.transition(this, "none");
			u.e.addMoveEvent(this, u.e._drag);
			u.e.addEndEvent(this, u.e._drop);
			if(fun(this[this.callback_picked])) {
				this[this.callback_picked](event);
			}
			if(this.drag_dropout && event.type.match(/mouse/)) {
				this._dropOutDrag = u.e._drag;
				this._dropOutDrop = u.e._drop;
				u.e.addOutEvent(this, u.e._dropOut);
			}
		}
	}
}
u.e._drag = function(event) {
	if(this.has_fixed_parent) {
		this.current_x = u.eventX(event) - this.start_input_x - u.scrollX();
		this.current_y = u.eventY(event) - this.start_input_y - u.scrollY();
	}
	else {
		this.current_x = u.eventX(event) - this.start_input_x;
		this.current_y = u.eventY(event) - this.start_input_y;
	}
	this.current_xps = Math.round(((this.current_x - this.move_last_x) / (event.timeStamp - this.move_timestamp)) * 1000);
	this.current_yps = Math.round(((this.current_y - this.move_last_y) / (event.timeStamp - this.move_timestamp)) * 1000);
	this.last_x_distance_travelled = (this.current_xps) ? this.current_x - this.move_last_x : this.last_x_distance_travelled;
	this.last_y_distance_travelled = (this.current_yps) ? this.current_y - this.move_last_y : this.last_y_distance_travelled;
	this.move_timestamp = event.timeStamp;
	this.move_last_x = this.current_x;
	this.move_last_y = this.current_y;
	if(!this.locked && this.only_vertical) {
		this._y = this.current_y;
	}
	else if(!this.locked && this.only_horizontal) {
		this._x = this.current_x;
	}
	else if(!this.locked) {
		this._x = this.current_x;
		this._y = this.current_y;
	}
	if(this.e_swipe) {
		if(this.only_horizontal) {
			if(this.current_xps < 0 || this.current_xps === 0 && this.last_x_distance_travelled < 0) {
				this.swiped = "left";
			}
			else {
				this.swiped = "right";
			}
		}
		else if(this.only_vertical) {
			if(this.current_yps < 0 || this.current_yps === 0 && this.last_y_distance_travelled < 0) {
				this.swiped = "up";
			}
			else {
				this.swiped = "down";
			}
		}
		else {
			if(Math.abs(this.current_xps) > Math.abs(this.current_yps)) {
				if(this.current_xps < 0) {
					this.swiped = "left";
				}
				else {
					this.swiped = "right";
				}
			}
			else if(Math.abs(this.current_xps) < Math.abs(this.current_yps)) {
				if(this.current_yps < 0) {
					this.swiped = "up";
				}
				else {
					this.swiped = "down";
				}
			}
		}
	}
	if(!this.locked) {
		if(u.e.overlap(this, [this.start_drag_x, this.start_drag_y, this.end_drag_x, this.end_drag_y], true)) {
			u.a.translate(this, this._x, this._y);
		}
		else if(this.drag_elastica) {
			this.swiped = false;
			this.current_xps = 0;
			this.current_yps = 0;
			var offset = false;
			if(!this.only_vertical && this._x < this.start_drag_x) {
				offset = this._x < this.start_drag_x - this.drag_elastica ? - this.drag_elastica : this._x - this.start_drag_x;
				this._x = this.start_drag_x;
				this.current_x = this._x + offset + (Math.round(Math.pow(offset, 2)/this.drag_elastica));
			}
			else if(!this.only_vertical && this._x + this.offsetWidth > this.end_drag_x) {
				offset = this._x + this.offsetWidth > this.end_drag_x + this.drag_elastica ? this.drag_elastica : this._x + this.offsetWidth - this.end_drag_x;
				this._x = this.end_drag_x - this.offsetWidth;
				this.current_x = this._x + offset - (Math.round(Math.pow(offset, 2)/this.drag_elastica));
			}
			else {
				this.current_x = this._x;
			}
			if(!this.only_horizontal && this._y < this.start_drag_y) {
				offset = this._y < this.start_drag_y - this.drag_elastica ? - this.drag_elastica : this._y - this.start_drag_y;
				this._y = this.start_drag_y;
				this.current_y = this._y + offset + (Math.round(Math.pow(offset, 2)/this.drag_elastica));
			}
			else if(!this.horizontal && this._y + this.offsetHeight > this.end_drag_y) {
				offset = (this._y + this.offsetHeight > this.end_drag_y + this.drag_elastica) ? this.drag_elastica : (this._y + this.offsetHeight - this.end_drag_y);
				this._y = this.end_drag_y - this.offsetHeight;
				this.current_y = this._y + offset - (Math.round(Math.pow(offset, 2)/this.drag_elastica));
			}
			else {
				this.current_y = this._y;
			}
			if(offset) {
				u.a.translate(this, this.current_x, this.current_y);
			}
		}
		else {
			this.swiped = false;
			this.current_xps = 0;
			this.current_yps = 0;
			if(this._x < this.start_drag_x) {
				this._x = this.start_drag_x;
			}
			else if(this._x + this.offsetWidth > this.end_drag_x) {
				this._x = this.end_drag_x - this.offsetWidth;
			}
			if(this._y < this.start_drag_y) {
				this._y = this.start_drag_y;
			}
			else if(this._y + this.offsetHeight > this.end_drag_y) { 
				this._y = this.end_drag_y - this.offsetHeight;
			}
			u.a.translate(this, this._x, this._y);
		}
	}
	if(fun(this[this.callback_moved])) {
		this[this.callback_moved](event);
	}
}
u.e._drop = function(event) {
	u.e.resetEvents(this);
	if(this.e_swipe && this.swiped) {
		this.e_swipe_options.eventAction = "Swiped "+ this.swiped;
		u.stats.event(this, this.e_swipe_options);
		if(this.swiped == "left" && fun(this.swipedLeft)) {
			this.swipedLeft(event);
		}
		else if(this.swiped == "right" && fun(this.swipedRight)) {
			this.swipedRight(event);
		}
		else if(this.swiped == "down" && fun(this.swipedDown)) {
			this.swipedDown(event);
		}
		else if(this.swiped == "up" && fun(this.swipedUp)) {
			this.swipedUp(event);
		}
	}
	else if(!this.drag_strict && !this.locked) {
		this.current_x = Math.round(this._x + (this.current_xps/2));
		this.current_y = Math.round(this._y + (this.current_yps/2));
		if(this.only_vertical || this.current_x < this.start_drag_x) {
			this.current_x = this.start_drag_x;
		}
		else if(this.current_x + this.offsetWidth > this.end_drag_x) {
			this.current_x = this.end_drag_x - this.offsetWidth;
		}
		if(this.only_horizontal || this.current_y < this.start_drag_y) {
			this.current_y = this.start_drag_y;
		}
		else if(this.current_y + this.offsetHeight > this.end_drag_y) {
			this.current_y = this.end_drag_y - this.offsetHeight;
		}
		this.transitioned = function() {
			if(fun(this.projected)) {
				this.projected(event);
			}
		}
		if(this.current_xps || this.current_yps) {
			u.a.transition(this, "all 1s cubic-bezier(0,0,0.25,1)");
		}
		else {
			u.a.transition(this, "none");
		}
		u.a.translate(this, this.current_x, this.current_y);
	}
	if(this.e_drag && !this.e_swipe) {
		this.e_drag_options.eventAction = u.stringOr(this.e_drag_options.eventAction, "Dropped");
		u.stats.event(this, this.e_drag_options);
	}
	if(fun(this[this.callback_dropped])) {
		this[this.callback_dropped](event);
	}
}
u.e._dropOut = function(event) {
	this._drop_out_id = u.randomString();
	document["_DroppedOutNode" + this._drop_out_id] = this;
	eval('document["_DroppedOutMove' + this._drop_out_id + '"] = function(event) {document["_DroppedOutNode' + this._drop_out_id + '"]._dropOutDrag(event);}');
	eval('document["_DroppedOutOver' + this._drop_out_id + '"] = function(event) {u.e.removeEvent(document, "mousemove", document["_DroppedOutMove' + this._drop_out_id + '"]);u.e.removeEvent(document, "mouseup", document["_DroppedOutEnd' + this._drop_out_id + '"]);u.e.removeEvent(document["_DroppedOutNode' + this._drop_out_id + '"], "mouseover", document["_DroppedOutOver' + this._drop_out_id + '"]);}');
	eval('document["_DroppedOutEnd' + this._drop_out_id + '"] = function(event) {u.e.removeEvent(document, "mousemove", document["_DroppedOutMove' + this._drop_out_id + '"]);u.e.removeEvent(document, "mouseup", document["_DroppedOutEnd' + this._drop_out_id + '"]);u.e.removeEvent(document["_DroppedOutNode' + this._drop_out_id + '"], "mouseover", document["_DroppedOutOver' + this._drop_out_id + '"]);document["_DroppedOutNode' + this._drop_out_id + '"]._dropOutDrop(event);}');
	u.e.addEvent(document, "mousemove", document["_DroppedOutMove" + this._drop_out_id]);
	u.e.addEvent(this, "mouseover", document["_DroppedOutOver" + this._drop_out_id]);
	u.e.addEvent(document, "mouseup", document["_DroppedOutEnd" + this._drop_out_id]);
}
u.e._cancelPick = function(event) {
	u.e.resetDragEvents(this);
	if(fun(this.pickCancelled)) {
		this.pickCancelled(event);
	}
}
u.e.setDragBoundaries = function(node, boundaries) {
	if((boundaries.constructor && boundaries.constructor.toString().match("Array")) || (boundaries.scopeName && boundaries.scopeName != "HTML")) {
		node.start_drag_x = Number(boundaries[0]);
		node.start_drag_y = Number(boundaries[1]);
		node.end_drag_x = Number(boundaries[2]);
		node.end_drag_y = Number(boundaries[3]);
	}
	else if((boundaries.constructor && boundaries.constructor.toString().match("HTML")) || (boundaries.scopeName && boundaries.scopeName == "HTML")) {
		if(node.drag_overflow == "scroll") {
			node.start_drag_x = node.offsetWidth > boundaries.offsetWidth ? boundaries.offsetWidth - node.offsetWidth : 0;
			node.start_drag_y = node.offsetHeight > boundaries.offsetHeight ? boundaries.offsetHeight - node.offsetHeight : 0;
			node.end_drag_x = node.offsetWidth > boundaries.offsetWidth ? node.offsetWidth : boundaries.offsetWidth;
			node.end_drag_y = node.offsetHeight > boundaries.offsetHeight ? node.offsetHeight : boundaries.offsetHeight;
		}
		else {
			node.start_drag_x = u.absX(boundaries) - u.absX(node);
			node.start_drag_y = u.absY(boundaries) - u.absY(node);
			node.end_drag_x = node.start_drag_x + boundaries.offsetWidth;
			node.end_drag_y = node.start_drag_y + boundaries.offsetHeight;
		}
	}
	if(node.show_bounds) {
		var debug_bounds = u.ae(document.body, "div", {"class":"debug_bounds"})
		debug_bounds.style.position = "absolute";
		debug_bounds.style.background = "red"
		debug_bounds.style.left = (u.absX(node) + node.start_drag_x - 1) + "px";
		debug_bounds.style.top = (u.absY(node) + node.start_drag_y - 1) + "px";
		debug_bounds.style.width = (node.end_drag_x - node.start_drag_x) + "px";
		debug_bounds.style.height = (node.end_drag_y - node.start_drag_y) + "px";
		debug_bounds.style.border = "1px solid white";
		debug_bounds.style.zIndex = 9999;
		debug_bounds.style.opacity = .5;
		if(document.readyState && document.readyState == "interactive") {
			debug_bounds.innerHTML = "WARNING - injected on DOMLoaded"; 
		}
		u.bug("node: ", node, " in (" + u.absX(node) + "," + u.absY(node) + "), (" + (u.absX(node)+node.offsetWidth) + "," + (u.absY(node)+node.offsetHeight) +")");
		u.bug("boundaries: (" + node.start_drag_x + "," + node.start_drag_y + "), (" + node.end_drag_x + ", " + node.end_drag_y + ")");
	}
	node._x = node._x ? node._x : 0;
	node._y = node._y ? node._y : 0;
	if(node.drag_overflow == "scroll" && (boundaries.constructor && boundaries.constructor.toString().match("HTML")) || (boundaries.scopeName && boundaries.scopeName == "HTML")) {
		node.locked = ((node.end_drag_x - node.start_drag_x <= boundaries.offsetWidth) && (node.end_drag_y - node.start_drag_y <= boundaries.offsetHeight));
		node.only_vertical = (node.vertical_lock || (!node.locked && node.end_drag_x - node.start_drag_x <= boundaries.offsetWidth));
		node.only_horizontal = (node.horizontal_lock || (!node.locked && node.end_drag_y - node.start_drag_y <= boundaries.offsetHeight));
	}
	else {
		node.locked = ((node.end_drag_x - node.start_drag_x == node.offsetWidth) && (node.end_drag_y - node.start_drag_y == node.offsetHeight));
		node.only_vertical = (node.vertical_lock || (!node.locked && node.end_drag_x - node.start_drag_x == node.offsetWidth));
		node.only_horizontal = (node.horizontal_lock || (!node.locked && node.end_drag_y - node.start_drag_y == node.offsetHeight));
	}
}
u.e.setDragPosition = function(node, x, y) {
	node.current_xps = 0;
	node.current_yps = 0;
	node._x = x;
	node._y = y;
	u.a.translate(node, node._x, node._y);
	if(fun(node[node.callback_moved])) {
		node[node.callback_moved](event);
	}
}
u.e.swipe = function(node, boundaries, _options) {
	node.e_swipe_options = _options ? _options : {};
	node.e_swipe = true;
	u.e.drag(node, boundaries, _options);
}
Util.Form = u.f = new function() {
	this.customInit = {};
	this.customValidate = {};
	this.customDataFormat = {};
	this.customHintPosition = {};
	this.customLabelStyle = {};
	this.init = function(_form, _options) {
		var i, j, field, action, input, hidden_input;
		_form._bulk_operation = true;
		if(_form.nodeName.toLowerCase() != "form") {
			_form.native_form = u.pn(_form, {"include":"form"});
			if(!_form.native_form) {
				u.bug("there is no form in this document??");
				return;
			}
		}
		else {
			_form.native_form = _form;
		}
		_form._focus_z_index = 50;
		_form._validation = true;
		_form._debug = false;
		_form._label_style = u.cv(_form, "labelstyle");
		_form._callback_ready = "ready";
		_form._callback_submitted = "submitted";
		_form._callback_submit_failed = "submitFailed";
		_form._callback_pre_submitted = "preSubmitted";
		_form._callback_resat = "resat";
		_form._callback_updated = "updated";
		_form._callback_changed = "changed";
		_form._callback_blurred = "blurred";
		_form._callback_focused = "focused";
		_form._callback_validation_failed = "validationFailed";
		_form._callback_validation_passed = "validationPassed";
		if(obj(_options)) {
			var _argument;
			for(_argument in _options) {
				switch(_argument) {
					case "validation"               : _form._validation                = _options[_argument]; break;
					case "debug"                    : _form._debug                     = _options[_argument]; break;
					case "focus_z"                  : _form._focus_z_index             = _options[_argument]; break;
					case "label_style"              : _form._label_style               = _options[_argument]; break;
					case "callback_ready"           : _form._callback_ready            = _options[_argument]; break;
					case "callback_submitted"       : _form._callback_submitted        = _options[_argument]; break;
					case "callback_submit_failed"   : _form._callback_submit_failed    = _options[_argument]; break;
					case "callback_pre_submitted"   : _form._callback_pre_submitted    = _options[_argument]; break;
					case "callback_resat"           : _form._callback_resat            = _options[_argument]; break;
					case "callback_updated"         : _form._callback_updated          = _options[_argument]; break;
					case "callback_changed"         : _form._callback_changed          = _options[_argument]; break;
					case "callback_blurred"         : _form._callback_blurred          = _options[_argument]; break;
					case "callback_focused"         : _form._callback_focused          = _options[_argument]; break;
					case "callback_validation_failed"         : _form._callback_validation_failed          = _options[_argument]; break;
					case "callback_validation_passed"         : _form._callback_validation_passed          = _options[_argument]; break;
				}
			}
		}
		_form._hover_z_index = _form._focus_z_index - 1;
		_form.native_form.onsubmit = function(event) {
			if(event.target._form) {
				return false;
			}
		}
		_form.native_form.setAttribute("novalidate", "novalidate");
		_form.DOMsubmit = _form.native_form.submit;
		_form.submit = this._submit;
		_form.DOMreset = _form.native_form.reset;
		_form.reset = this._reset;
		_form.getData = function(_options) {
			return u.f.getFormData(this, _options);
		}
		_form.inputs = {};
		_form.actions = {};
		_form._error_inputs = {};
		var fields = u.qsa(".field", _form);
		for(i = 0; i < fields.length; i++) {
			field = fields[i];
			u.f.initField(_form, field);
		}
		var hidden_inputs = u.qsa("input[type=hidden]", _form);
		for(i = 0; i < hidden_inputs.length; i++) {
			hidden_input = hidden_inputs[i];
			if(!_form.inputs[hidden_input.name]) {
				_form.inputs[hidden_input.name] = hidden_input;
				hidden_input._form = _form;
				hidden_input.val = this._value;
			}
		}
		var actions = u.qsa(".actions li input[type=button],.actions li input[type=submit],.actions li input[type=reset],.actions li a.button", _form);
		for(i = 0; i < actions.length; i++) {
			action = actions[i];
			this.initButton(_form, action);
		}
		u.t.setTimer(_form, function() {
			var validate_inputs = [];
			for(input in this.inputs) {
				if(this.inputs[input].field) {
					validate_inputs.push(this.inputs[input]);
				}
			}
			u.f.bulkValidate(validate_inputs);
			if(_form._debug) {
				u.bug(_form, "inputs:", _form.inputs, "actions:", _form.actions);
			}
			if(fun(this[this._callback_ready])) {
				this[this._callback_ready]();
			}
		}, 100);
	}
	this.initField = function(_form, field) {
		field._form = _form;
		field._base_z_index = u.gcs(field, "z-index");
		field.help = u.qs(".help", field);
		field.hint = u.qs(".hint", field);
		field.error = u.qs(".error", field);
		field.label = u.qs("label", field);
		field.indicator = u.ae(field, "div", {"class":"indicator"});
		if(fun(u.f.fixFieldHTML)) {
			u.f.fixFieldHTML(field);
		}
		field._custom_initialized = false;
		var custom_init;
		for(custom_init in this.customInit) {
			if(u.hc(field, custom_init)) {
				this.customInit[custom_init](field);
				field._custom_initialized = true;
				break;
			}
		}
		if(!field._custom_initialized) {
			if(u.hc(field, "string|email|tel|number|integer|password|date|datetime")) {
				field.type = field.className.match(/(?:^|\b)(string|email|tel|number|integer|password|date|datetime)(?:\b|$)/)[0];
				field.input = u.qs("input", field);
				field.input._form = _form;
				field.input.label = u.qs("label[for='"+field.input.id+"']", field);
				field.input.field = field;
				field.input.val = this._value;
				u.e.addEvent(field.input, "keyup", this._updated);
				u.e.addEvent(field.input, "change", this._changed);
				this.inputOnEnter(field.input);
				this.activateInput(field.input);
			}
			else if(u.hc(field, "text")) {
				field.type = "text";
				field.input = u.qs("textarea", field);
				field.input._form = _form;
				field.input.label = u.qs("label[for='"+field.input.id+"']", field);
				field.input.field = field;
				field.input.val = this._value;
				if(u.hc(field, "autoexpand")) {
					u.ass(field.input, {
						"overflow": "hidden"
					});
					field.input.setHeight = function() {
						u.ass(this, {
							height: "auto"
						});
						u.ass(this, {
							height: (this.scrollHeight) + "px"
						});
					}
					u.e.addEvent(field.input, "input", field.input.setHeight);
					field.input.setHeight();
				}
				u.e.addEvent(field.input, "keyup", this._updated);
				u.e.addEvent(field.input, "change", this._changed);
				this.activateInput(field.input);
			}
			else if(u.hc(field, "select")) {
				field.type = "select";
				field.input = u.qs("select", field);
				field.input._form = _form;
				field.input.label = u.qs("label[for='"+field.input.id+"']", field);
				field.input.field = field;
				field.input.val = this._value_select;
				u.e.addEvent(field.input, "change", this._updated);
				u.e.addEvent(field.input, "keyup", this._updated);
				u.e.addEvent(field.input, "change", this._changed);
				this.activateInput(field.input);
			}
			else if(u.hc(field, "checkbox|boolean")) {
				field.type = field.className.match(/(?:^|\b)(checkbox|boolean)(?:\b|$)/)[0];
				field.input = u.qs("input[type=checkbox]", field);
				field.input._form = _form;
				field.input.label = u.qs("label[for='"+field.input.id+"']", field);
				field.input.field = field;
				field.input.val = this._value_checkbox;
				u.f._update_checkbox_field.bind(field.input)();
				u.e.addEvent(field.input, "change", this._changed);
				u.e.addEvent(field.input, "change", this._updated);
				u.e.addEvent(field.input, "change", this._update_checkbox_field);
				this.inputOnEnter(field.input);
				this.activateInput(field.input);
			}
			else if(u.hc(field, "radiobuttons")) {
				field.type = "radiobuttons";
				field.inputs = u.qsa("input", field);
				field.input = field.inputs[0];
				for(j = 0; j < field.inputs.length; j++) {
					input = field.inputs[j];
					input._form = _form;
					input.label = u.qs("label[for='"+input.id+"']", field);
					input.field = field;
					input.val = this._value_radiobutton;
					u.e.addEvent(input, "change", this._changed);
					u.e.addEvent(input, "change", this._updated);
					this.inputOnEnter(input);
					this.activateInput(input);
				}
			}
			else if(u.hc(field, "files")) {
				field.type = "files";
				field.input = u.qs("input", field);
				field.input._form = _form;
				field.input.label = u.qs("label[for='"+field.input.id+"']", field);
				field.input.field = field;
				field.input.val = this._value_file;
				field.filelist = u.qs("ul.filelist", field);
				if(!field.filelist) {
					field.filelist = u.ae(field, "ul", {"class":"filelist"});
					field.insertBefore(field.help, field.filelist);
				}
				field.filelist.field = field;
				field.uploaded_files = u.qsa("li.uploaded", field.filelist);
				this._update_filelist.bind(field.input)();
				u.e.addEvent(field.input, "change", this._update_filelist);
				u.e.addEvent(field.input, "change", this._updated);
				u.e.addEvent(field.input, "change", this._changed);
				if(u.e.event_support != "touch") {
					u.e.addEvent(field.input, "dragenter", this._focus);
					u.e.addEvent(field.input, "dragleave", this._blur);
					u.e.addEvent(field.input, "drop", this._blur);
				}
				this.activateInput(field.input);
			}
			else {
				u.bug("UNKNOWN FIELD IN FORM INITIALIZATION:", field);
			}
		}
		if(field.input) {
			_form.inputs[field.input.name] = field.input;
			if(!_form._bulk_operation) {
				this.validate(field.input);
			}
		}
	}
	this.initButton = function(_form, action) {
		action._form = _form;
		this.buttonOnEnter(action);
		this.activateButton(action);
	}
	this._reset = function(event, iN) {
		for (name in this.inputs) {
			if (this.inputs[name] && this.inputs[name].field && this.inputs[name].type != "hidden" && !this.inputs[name].getAttribute("readonly")) {
				this.inputs[name]._used = false;
				this.inputs[name].val("");
				if(fun(u.f.updateDefaultState)) {
					u.f.updateDefaultState(this.inputs[name]);
				}
			}
		}
		if(fun(this[this._callback_resat])) {
			this[this._callback_resat](iN);
		}
	}
	this._submit = function(event, iN) {
		var validate_inputs = [];
		for(name in this.inputs) {
			if(this.inputs[name] && this.inputs[name].field && fun(this.inputs[name].val)) {
				this.inputs[name]._used = true;
				validate_inputs.push(this.inputs[name]);
			}
		}
		u.f.bulkValidate(validate_inputs);
		if(!Object.keys(this._error_inputs).length) {
			if(fun(this[this._callback_pre_submitted])) {
				this[this._callback_pre_submitted](iN);
			}
			if(fun(this[this._callback_submitted])) {
				this[this._callback_submitted](iN);
			}
			else {
				for(name in this.inputs) {
					if(this.inputs[name] && this.inputs[name].default_value && this.inputs[name].nodeName.match(/^(input|textarea)$/i)) {
						if(fun(this.inputs[name].val) && !this.inputs[name].val()) {
							this.inputs[name].value = "";
						}
					}
				}
				this.DOMsubmit();
			}
		}
		else {
			if(fun(this[this._callback_submit_failed])) {
				this[this._callback_submit_failed](iN);
			}
		}
	}
	this._value = function(value) {
		if(value !== undefined) {
			this.value = value;
			if(value !== this.default_value) {
				u.rc(this, "default");
			}
			u.f.validate(this);
		}
		return (this.value != this.default_value) ? this.value : "";
	}
	this._value_radiobutton = function(value) {
		var i, option;
		if(value !== undefined) {
			for(i = 0; i < this.field.inputs.length; i++) {
				option = this.field.inputs[i];
				if(option.value == value || (option.value == "true" && value) || (option.value == "false" && value === false)) {
					option.checked = true;
					u.f.validate(this);
				}
				else {
					option.checked = false;
				}
			}
		}
		for(i = 0; i < this.field.inputs.length; i++) {
			option = this.field.inputs[i];
			if(option.checked) {
				return option.value;
			}
		}
		return "";
	}
	this._value_checkbox = function(value) {
		if(value !== undefined) {
			if(value) {
				this.checked = true
			}
			else {
				this.checked = false;
			}
			u.f._update_checkbox_field.bind(this)();
			u.f.validate(this);
		}
		if(this.checked) {
			return this.value;
		}
		return "";
	}
	this._value_select = function(value) {
		if(value !== undefined) {
			var i, option;
			for(i = 0; i < this.options.length; i++) {
				option = this.options[i];
				if(option.value == value) {
					this.selectedIndex = i;
					u.f.validate(this);
					return this.options[this.selectedIndex].value;
				}
			}
			if (value === "") {
				this.selectedIndex = -1;
				u.f.validate(this);
				return "";
			}
		}
		return (this.selectedIndex >= 0 && this.default_value != this.options[this.selectedIndex].value) ? this.options[this.selectedIndex].value : "";
	}
	this._value_file = function(value) {
		if(value !== undefined) {
			if(value === "") {
				this.value = null;
			}
			else {
				u.bug('ADDING VALUES MANUALLY TO INPUT type="file" IS NOT SUPPORTED IN JAVASCRIPT');
			}
			u.f._update_filelist.bind(this)();
			u.f.validate(this);
		}
		if(this.files && this.files.length) {
			var i, file, files = [];
			for(i = 0; i < this.files.length; i++) {
				file = this.files[i];
				files.push(file);
			}
			return files;
		}
		else if(!this.files && this.value) {
			return this.value;
		}
		else if(this.field.uploaded_files && this.field.uploaded_files.length){
			return true;
		}
		return "";
	}
	this._changed = function(event) {
		if(fun(this[this._form._callback_changed])) {
			this[this._form._callback_changed](this);
		}
		else if(fun(this.field.input[this._form._callback_changed])) {
			this.field.input[this._form._callback_changed](this);
		}
		if(fun(this._form[this._form._callback_changed])) {
			this._form[this._form._callback_changed](this);
		}
	}
	this._updated = function(event) {
		if(event.keyCode != 9 && event.keyCode != 13 && event.keyCode != 16 && event.keyCode != 17 && event.keyCode != 18) {
			u.f.validate(this);
			if(fun(this[this._form._callback_updated])) {
				this[this._form._callback_updated](this);
			}
			else if(fun(this.field.input[this._form._callback_updated])) {
				this.field.input[this._form._callback_updated](this);
			}
			if(fun(this._form[this._form._callback_updated])) {
				this._form[this._form._callback_updated](this);
			}
		}
	}
	this._update_checkbox_field = function(event) {
		if(this.checked) {
			u.ac(this.field, "checked");
		}
		else {
			u.rc(this.field, "checked");
		}
	}
	this._update_filelist = function(event) {
		var i;
		var files = this.val();
		this.field.filelist.innerHTML = "";
		u.ae(this.field.filelist, "li", {html:this.field.hint ? u.text(this.field.hint) : u.text(this.label), class:"label"})
		if(files && files.length) {
			u.ac(this.field, "has_new_files");
			var i;
			for(i = 0; i < files.length; i++) {
				u.ae(this.field.filelist, "li", {html:files[i].name, class:"new"})
			}
			if(this.multiple) {
				for(i = 0; i < this.field.uploaded_files.length; i++) {
					u.ae(this.field.filelist, this.field.uploaded_files[i]);
				}
			}
			else {
				this.field.uploaded_files = [];
			}
		}
		else if(this.field.uploaded_files && this.field.uploaded_files.length) {
			u.rc(this.field, "has_new_files");
			var i;
			for(i = 0; i < this.field.uploaded_files.length; i++) {
				u.ae(this.field.filelist, this.field.uploaded_files[i]);
			}
		}
		else {
			u.rc(this.field, "has_new_files");
		}
	}
	this._mouseenter = function(event) {
		u.ac(this.field, "hover");
		u.ac(this, "hover");
		u.as(this.field, "zIndex", this._form._hover_z_index);
		u.f.positionHint(this.field);
	}
	this._mouseleave = function(event) {
		u.rc(this.field, "hover");
		u.rc(this, "hover");
		u.as(this.field, "zIndex", this.field._base_z_index);
		u.f.positionHint(this.field);
	}
	this._focus = function(event) {
		this.field.is_focused = true;
		this.is_focused = true;
		u.ac(this.field, "focus");
		u.ac(this, "focus");
		u.as(this.field, "zIndex", this._form._focus_z_index);
		u.f.positionHint(this.field);
		if(fun(this[this._form._callback_focused])) {
			this[this._form._callback_focused](this);
		}
		else if(fun(this.field.input[this._form._callback_focused])) {
			this.field.input[this._form._callback_focused](this);
		}
		if(fun(this._form[this._form._callback_focused])) {
			this._form[this._form._callback_focused](this);
		}
	}
	this._blur = function(event) {
		this.field.is_focused = false;
		this.is_focused = false;
		u.rc(this.field, "focus");
		u.rc(this, "focus");
		u.as(this.field, "zIndex", this.field._base_z_index);
		u.f.positionHint(this.field);
		this._used = true;
		if(fun(this[this._form._callback_blurred])) {
			this[this._form._callback_blurred](this);
		}
		else if(fun(this.field.input[this._form._callback_blurred])) {
			this.field.input[this._form._callback_blurred](this);
		}
		if(fun(this._form[this._form._callback_blurred])) {
			this._form[this._form._callback_blurred](this);
		}
	}
	this._button_focus = function(event) {
		u.ac(this, "focus");
		if(fun(this[this._form._callback_focused])) {
			this[this._form._callback_focused](this);
		}
		if(fun(this._form[this._form._callback_focused])) {
			this._form[this._form._callback_focused](this);
		}
	}
	this._button_blur = function(event) {
		u.rc(this, "focus");
		if(fun(this[this._form._callback_blurred])) {
			this[this._form._callback_blurred](this);
		}
		if(fun(this._form[this._form._callback_blurred])) {
			this._form[this._form._callback_blurred](this);
		}
	}
	this._validate = function(event) {
		u.f.validate(this);
	}
	this.inputOnEnter = function(node) {
		node.keyPressed = function(event) {
			if(this.nodeName.match(/input/i) && (event.keyCode == 40 || event.keyCode == 38)) {
				this._submit_disabled = true;
			}
			else if(this.nodeName.match(/input/i) && this._submit_disabled && (
				event.keyCode == 46 || 
				(event.keyCode == 39 && u.browser("firefox")) || 
				(event.keyCode == 37 && u.browser("firefox")) || 
				event.keyCode == 27 || 
				event.keyCode == 13 || 
				event.keyCode == 9 ||
				event.keyCode == 8
			)) {
				this._submit_disabled = false;
			}
			else if(event.keyCode == 13 && !this._submit_disabled) {
				u.e.kill(event);
				this.blur();
				this._form.submitInput = this;
				this._form.submitButton = false;
				this._form.submit(event, this);
			}
		}
		u.e.addEvent(node, "keydown", node.keyPressed);
	}
	this.buttonOnEnter = function(node) {
		node.keyPressed = function(event) {
			if(event.keyCode == 13 && !u.hc(this, "disabled") && fun(this.clicked)) {
				u.e.kill(event);
				this.clicked(event);
			}
		}
		u.e.addEvent(node, "keydown", node.keyPressed);
	}
	this.activateInput = function(iN) {
		u.e.addEvent(iN, "focus", this._focus);
		u.e.addEvent(iN, "blur", this._blur);
		if(u.e.event_support != "touch") {
			u.e.addEvent(iN, "mouseenter", this._mouseenter);
			u.e.addEvent(iN, "mouseleave", this._mouseleave);
		}
		u.e.addEvent(iN, "blur", this._validate);
		if(iN._form._label_style && fun(this.customLabelStyle[iN._form._label_style])) {
			this.customLabelStyle[iN._form._label_style](iN);
		}
		else {
			iN.default_value = "";
		}
	}
	this.activateButton = function(action) {
		if(action.type && action.type == "submit" || action.type == "reset") {
			action.onclick = function(event) {
				u.e.kill(event);
			}
		}
		u.ce(action);
		if(!action.clicked) {
			action.clicked = function(event) {
				if(!u.hc(this, "disabled")) {
					if(this.type && this.type.match(/submit/i)) {
						this._form._submit_button = this;
						this._form._submit_input = false;
						this._form.submit(event, this);
					}
					else if(this.type && this.type.match(/reset/i)) {
						this._form._submit_button = false;
						this._form._submit_input = false;
						this._form.reset(event, this);
					}
					else if(this.url) {
						if(event && (event.metaKey || event.ctrlKey)) {
							window.open(this.url);
						}
						else {
							if(obj(u.h) && u.h.is_listening) {
								u.h.navigate(this.url, this);
							}
							else {
								location.href = this.url;
							}
						}
					}
				}
			}
		}
		var action_name = action.name ? action.name : (action.parentNode.className ? u.superNormalize(action.parentNode.className) : (action.value ? u.superNormalize(action.value) : u.superNormalize(u.text(action))));
		if(action_name && !action._form.actions[action_name]) {
			action._form.actions[action_name] = action;
		}
		if(obj(u.k) && u.hc(action, "key:[a-z0-9]+")) {
			u.k.addKey(action, u.cv(action, "key"));
		}
		u.e.addEvent(action, "focus", this._button_focus);
		u.e.addEvent(action, "blur", this._button_blur);
	}
	this.positionHint = function(field) {
		if(field.help) {
			var custom_hint_position;
			for(custom_hint_position in this.customHintPosition) {
				if(u.hc(field, custom_hint_position)) {
					this.customHintPosition[custom_hint_position](field);
					return;
				}
			}
			var input_middle = field.input.offsetTop + (field.input.offsetHeight / 2);
			var help_top = input_middle - field.help.offsetHeight / 2;
			u.ass(field.help, {
				"top": help_top + "px"
			});
		}
	}
	this.updateFilelistStatus = function(form, response) {
		if(form && form.inputs && response && response.cms_status == "success" && response.cms_object && response.cms_object.mediae) {
			var mediae = JSON.parse(JSON.stringify(response.cms_object.mediae));
			var filelists = u.qsa("div.field.files ul.filelist", form);
			var i, j, k, filelist, old_files, old_file, new_files, new_files;
			for(i = 0; i < filelists.length; i++) {
				filelist = filelists[i];
				new_files = u.qsa("li.new", filelist);
				if(new_files.length) {
					old_files = u.qsa("li.uploaded", filelist);
					if(old_files.length) {
						for(j in mediae) {
							media = mediae[j];
							if(media.variant.match("^" + filelist.field.input.name.replace(/\[\]$/, "") + "(\-|$)")) {
								for(k = 0; k < old_files.length; k++) {
									old_file = old_files[k];
									if(u.cv(old_file, "media_id") == media.id) {
										delete mediae[j];
									}
								}
							}
						}
					}
					if(Object.keys(mediae).length) {
						for(j in mediae) {
							media = mediae[j];
							if(media.variant.match("^"+filelist.field.input.name.replace(/\[\]$/, "")+"(\-|$)")) {
								for(k = 0; k < new_files.length; k++) {
									new_file = new_files[k];
									if(u.text(new_file) == media.name || u.text(new_file)+".zip" == media.name) {
										new_file.innerHTML = media.name;
										u.rc(new_file, "new");
										u.ac(new_file, "uploaded media_id:"+media.id+" variant:"+media.variant+" format:"+media.format+" width:"+media.width+" height:"+media.height);
										delete mediae[j];
									}
								}
							}
						}
					}
				}
				filelist.field.uploaded_files = u.qsa("li.uploaded", filelist);
			}
		}
	}
	this.inputHasError = function(iN) {
		u.rc(iN, "correct");
		u.rc(iN.field, "correct");
		delete iN.is_correct;
		if(iN.val() !== "") {
			if(!iN.has_error && (iN._used || iN._form._bulk_operation)) {
				iN._form._error_inputs[iN.name] = true;
				u.ac(iN, "error");
				u.ac(iN.field, "error");
				iN.has_error = true;
				this.updateInputValidationState(iN);
			 }
		}
		else if(!iN.has_error && iN._used) {
			iN._form._error_inputs[iN.name] = true;
			u.ac(iN, "error");
			u.ac(iN.field, "error");
			iN.has_error = true;
			this.updateInputValidationState(iN);
		}
		else if(!iN._used) {
			delete iN._form._error_inputs[iN.name];
			u.rc(iN, "error");
			u.rc(iN.field, "error");
			delete iN.has_error;
		}
		this.positionHint(iN.field);
	}
	this.inputIsCorrect = function(iN) {
		u.rc(iN, "error");
		u.rc(iN.field, "error");
		delete iN.has_error;
		delete iN._form._error_inputs[iN.name];
		if(iN.val() !== "") {
			if(!iN.is_correct) {
				iN._used = true;
				u.ac(iN, "correct");
				u.ac(iN.field, "correct");
				iN.is_correct = true;
				this.updateInputValidationState(iN);
			}
		}
		else if(iN.is_correct || iN.has_error) {
			u.rc(iN, "correct");
			u.rc(iN.field, "correct");
			delete iN.is_correct;
			this.updateInputValidationState(iN);
		}
	}
	this.updateInputValidationState = function(iN) {
		if(iN.has_error && fun(iN[iN._form._callback_validation_failed])) {
			iN[iN._form._callback_validation_failed]();
		}
		else if(iN.is_correct && fun(iN[iN._form._callback_validation_passed])) {
			iN[iN._form._callback_validation_passed]();
		}
		this.updateFormValidationState(iN._form);
	}
	this.updateFormValidationState = function(_form) {
		if(!_form._bulk_operation) {
			if(Object.keys(_form._error_inputs).length) {
				_form._validation_state = "error";
				if(_form._error_inputs !== _form._reference_error_inputs) {
					if(fun(_form[_form._callback_validation_failed])) {
						_form[_form._callback_validation_failed](_form._error_inputs);
					}
				}
			}
			else if(u.qsa(".field.required", _form).length === u.qsa(".field.required.correct", _form).length) {
				if(fun(_form[_form._callback_validation_passed]) && _form._validation_state !== "correct") {
					_form[_form._callback_validation_passed]();
				}
				_form._validation_state = "correct";
			}
			else {
				_form._validation_state = "void";
			}
			_form._reference_error_inputs = JSON.parse(JSON.stringify(_form._error_inputs));
		}
	}
	this.bulkValidate = function(inputs) {
		if(inputs && inputs.length) {
			var _form = inputs[0]._form;
			_form._bulk_operation = true;
			var i;
			for(i = 0; i < inputs.length; i++) {
				u.f.validate(inputs[i]);
			}
			_form._bulk_operation = false;
			this.updateFormValidationState(_form);
		}
	}
	this.validate = function(iN) {
		if(!iN._form._validation || !iN.field) {
			return true;
		}
		var min, max, pattern;
		var validated = false;
		var compare_to = iN.getAttribute("data-compare-to");
		if(!u.hc(iN.field, "required") && iN.val() === "" && (!compare_to || iN._form.inputs[compare_to].val() === "")) {
			this.inputIsCorrect(iN);
			return true;
		}
		else if(u.hc(iN.field, "required") && iN.val() === "") {
			this.inputHasError(iN);
			return false;
		}
		var custom_validate;
		for(custom_validate in u.f.customValidate) {
			if(u.hc(iN.field, custom_validate)) {
				u.f.customValidate[custom_validate](iN);
				validated = true;
			}
		}
		if(!validated) {
			if(u.hc(iN.field, "password")) {
				min = Number(u.cv(iN.field, "min"));
				max = Number(u.cv(iN.field, "max"));
				min = min ? min : 8;
				max = max ? max : 255;
				pattern = iN.getAttribute("pattern");
				if(
					iN.val().length >= min && 
					iN.val().length <= max && 
					(!pattern || iN.val().match("^"+pattern+"$")) &&
					(!compare_to || iN.val() == iN._form.inputs[compare_to].val())
				) {
					this.inputIsCorrect(iN);
					if(compare_to) {
						this.inputIsCorrect(iN._form.inputs[compare_to]);
					}
				}
				else {
					this.inputHasError(iN);
					if(compare_to) {
						this.inputHasError(iN._form.inputs[compare_to]);
					}
				}
			}
			else if(u.hc(iN.field, "number")) {
				min = Number(u.cv(iN.field, "min"));
				max = Number(u.cv(iN.field, "max"));
				min = min ? min : 0;
				max = max ? max : 99999999999999999999999999999;
				pattern = iN.getAttribute("pattern");
				if(
					!isNaN(iN.val()) && 
					iN.val() >= min && 
					iN.val() <= max && 
					(!pattern || iN.val().match("^"+pattern+"$"))
				) {
					this.inputIsCorrect(iN);
				}
				else {
					this.inputHasError(iN);
				}
			}
			else if(u.hc(iN.field, "integer")) {
				min = Number(u.cv(iN.field, "min"));
				max = Number(u.cv(iN.field, "max"));
				min = min ? min : 0;
				max = max ? max : 99999999999999999999999999999;
				pattern = iN.getAttribute("pattern");
				if(
					!isNaN(iN.val()) && 
					Math.round(iN.val()) == iN.val() && 
					iN.val() >= min && 
					iN.val() <= max && 
					(!pattern || iN.val().match("^"+pattern+"$"))
				) {
					this.inputIsCorrect(iN);
				}
				else {
					this.inputHasError(iN);
				}
			}
			else if(u.hc(iN.field, "tel")) {
				pattern = iN.getAttribute("pattern");
				if(
					(
						(!pattern && iN.val().match(/^([\+0-9\-\.\s\(\)]){5,18}$/))
						||
						(pattern && iN.val().match("^"+pattern+"$"))
					)
					&&
					(!compare_to || iN.val() == iN._form.inputs[compare_to].val())
				) {
					this.inputIsCorrect(iN);
					if(compare_to) {
						this.inputIsCorrect(iN._form.inputs[compare_to]);
					}
				}
				else {
					this.inputHasError(iN);
					if(compare_to) {
						this.inputHasError(iN._form.inputs[compare_to]);
					}
				}
			}
			else if(u.hc(iN.field, "email")) {
				pattern = iN.getAttribute("pattern");
				if(
					(
						(!pattern && iN.val().match(/^([^<>\\\/%$])+\@([^<>\\\/%$])+\.([^<>\\\/%$]{2,20})$/))
						||
						(pattern && iN.val().match("^"+pattern+"$"))
					)
					&&
					(!compare_to || iN.val() == iN._form.inputs[compare_to].val())
				) {
					this.inputIsCorrect(iN);
					if(compare_to) {
						this.inputIsCorrect(iN._form.inputs[compare_to]);
					}
				}
				else {
					this.inputHasError(iN);
					if(compare_to) {
						this.inputHasError(iN._form.inputs[compare_to]);
					}
				}
			}
			else if(u.hc(iN.field, "text")) {
				min = Number(u.cv(iN.field, "min"));
				max = Number(u.cv(iN.field, "max"));
				min = min ? min : 1;
				max = max ? max : 10000000;
				pattern = iN.getAttribute("pattern");
				if(
					iN.val().length >= min && 
					iN.val().length <= max && 
					(!pattern || iN.val().match("^"+pattern+"$"))
				) {
					this.inputIsCorrect(iN);
				}
				else {
					this.inputHasError(iN);
				}
			}
			else if(u.hc(iN.field, "date")) {
				min = u.cv(iN.field, "min");
				max = u.cv(iN.field, "max");
				pattern = iN.getAttribute("pattern");
				if(
					(!min || new Date(decodeURIComponent(min)) <= new Date(iN.val())) &&
					(!max || new Date(decodeURIComponent(max)) >= new Date(iN.val())) &&
					(
						(!pattern && iN.val().match(/^([\d]{4}[\-\/\ ]{1}[\d]{2}[\-\/\ ][\d]{2})$/))
						||
						(pattern && iN.val().match("^"+pattern+"$"))
					)
				) {
					this.inputIsCorrect(iN);
				}
				else {
					this.inputHasError(iN);
				}
			}
			else if(u.hc(iN.field, "datetime")) {
				min = u.cv(iN.field, "min");
				max = u.cv(iN.field, "max");
				pattern = iN.getAttribute("pattern");
				if(
					(!min || new Date(decodeURIComponent(min)) <= new Date(iN.val())) &&
					(!max || new Date(decodeURIComponent(max)) >= new Date(iN.val())) &&
					(
						(!pattern && iN.val().match(/^([\d]{4}[\-\/\ ]{1}[\d]{2}[\-\/\ ][\d]{2} [\d]{2}[\-\/\ \:]{1}[\d]{2}[\-\/\ \:]{0,1}[\d]{0,2})$/))
						||
						(pattern && iN.val().match(pattern))
					)
				) {
					this.inputIsCorrect(iN);
				}
				else {
					this.inputHasError(iN);
				}
			}
			else if(u.hc(iN.field, "files")) {
				min = Number(u.cv(iN.field, "min"));
				max = Number(u.cv(iN.field, "max"));
				min = min ? min : 1;
				max = max ? max : 10000000;
				pattern = iN.getAttribute("accept");
				var i, value = iN.val(), files = [];
				if(iN.field.uploaded_files && iN.field.uploaded_files.length) {
					for(i = 0; i < iN.field.uploaded_files.length; i++) {
						files.push("." + u.cv(iN.field.uploaded_files[i], "format").toLowerCase());
					}
				}
				if(value && value.length) {
					for(i = 0; i < value.length; i++) {
						files.push(value[i].name.substring(value[i].name.lastIndexOf(".")).toLowerCase());
					}
				}
				if(
					(files.length >= min && files.length <= max)
					&&
					(!pattern || files.every(function(v) {return pattern.split(",").indexOf(v) !== -1}))
				) {
					this.inputIsCorrect(iN);
				}
				else {
					this.inputHasError(iN);
				}
			}
			else if(u.hc(iN.field, "select")) {
				if(iN.val() !== "") {
					this.inputIsCorrect(iN);
				}
				else {
					this.inputHasError(iN);
				}
			}
			else if(u.hc(iN.field, "checkbox|boolean|radiobuttons")) {
				if(iN.val() !== "") {
					this.inputIsCorrect(iN);
				}
				else {
					this.inputHasError(iN);
				}
			}
			else if(u.hc(iN.field, "string")) {
				min = Number(u.cv(iN.field, "min"));
				max = Number(u.cv(iN.field, "max"));
				min = min ? min : 1;
				max = max ? max : 255;
				pattern = iN.getAttribute("pattern");
				if(
					iN.val().length >= min &&
					iN.val().length <= max && 
					(!pattern || iN.val().match("^"+pattern+"$"))
					&&
					(!compare_to || iN.val() == iN._form.inputs[compare_to].val())
				) {
					this.inputIsCorrect(iN);
					if(compare_to) {
						this.inputIsCorrect(iN._form.inputs[compare_to]);
					}
				}
				else {
					this.inputHasError(iN);
					if(compare_to) {
						this.inputHasError(iN._form.inputs[compare_to]);
					}
				}
			}
		}
		if(u.hc(iN.field, "error")) {
			return false;
		}
		else {
			return true;
		}
	}
	this.getFormData = this.getParams = function(_form, _options) {
		var format = "formdata";
		var ignore_inputs = "ignoreinput";
		if(obj(_options)) {
			var _argument;
			for(_argument in _options) {
				switch(_argument) {
					case "ignore_inputs"    : ignore_inputs     = _options[_argument]; break;
					case "format"           : format            = _options[_argument]; break;
				}
			}
		}
		var i, input, select, textarea, param, params;
		if(format == "formdata") {
			params = new FormData();
		}
		else {
			params = new Object();
			params.append = function(name, value, filename) {
				this[name] = filename || value;
			}
		}
		if(_form._submit_button && _form._submit_button.name) {
			params.append(_form._submit_button.name, _form._submit_button.value);
		}
		var inputs = u.qsa("input", _form);
		var selects = u.qsa("select", _form)
		var textareas = u.qsa("textarea", _form)
		for(i = 0; i < inputs.length; i++) {
			input = inputs[i];
			if(!u.hc(input, ignore_inputs)) {
				if((input.type == "checkbox" || input.type == "radio") && input.checked) {
					if(fun(input.val)) {
						params.append(input.name, input.val());
					}
					else {
						params.append(input.name, input.value);
					}
				}
				else if(input.type == "file") {
					var f, file, files;
					if(fun(input.val)) {
						files = input.val();
					}
					else if(input.files) {
						files = input.files;
					}
					if(files && files.length) {
						for(f = 0; f < files.length; f++) {
							file = files[f];
							params.append(input.name, file, file.name);
						}
					}
					else {
						params.append(input.name, (input.value || ""));
					}
				}
				else if(!input.type.match(/button|submit|reset|file|checkbox|radio/i)) {
					if(fun(input.val)) {
						params.append(input.name, input.val());
					}
					else {
						params.append(input.name, input.value);
					}
				}
			}
		}
		for(i = 0; i < selects.length; i++) {
			select = selects[i];
			if(!u.hc(select, ignore_inputs)) {
				if(fun(select.val)) {
					params.append(select.name, select.val());
				}
				else {
					params.append(select.name, select.options[select.selectedIndex] ? select.options[select.selectedIndex].value : "");
				}
			}
		}
		for(i = 0; i < textareas.length; i++) {
			textarea = textareas[i];
			if(!u.hc(textarea, ignore_inputs)) {
				if(fun(textarea.val)) {
					params.append(textarea.name, textarea.val());
				}
				else {
					params.append(textarea.name, textarea.value);
				}
			}
		}
		if(format && fun(this.customDataFormat[format])) {
			return this.customDataFormat[format](params, _form);
		}
		else if(format == "formdata") {
			return params;
		}
		else if(format == "object") {
			delete params.append;
			return params;
		}
		else {
			var string = "";
			for(param in params) {
				if(!fun(params[param])) {
					string += (string ? "&" : "") + param + "=" + encodeURIComponent(params[param]);
				}
			}
			return string;
		}
	}
}
u.f.customBuild = {};
u.f.addForm = function(node, _options) {
	var form_name = "js_form";
	var form_action = "#";
	var form_method = "post";
	var form_class = "";
	if(obj(_options)) {
		var _argument;
		for(_argument in _options) {
			switch(_argument) {
				case "name"			: form_name				= _options[_argument]; break;
				case "action"		: form_action			= _options[_argument]; break;
				case "method"		: form_method			= _options[_argument]; break;
				case "class"		: form_class			= _options[_argument]; break;
			}
		}
	}
	var form = u.ae(node, "form", {"class":form_class, "name": form_name, "action":form_action, "method":form_method});
	return form;
}
u.f.addFieldset = function(node, _options) {
	var fieldset_class = "";
	if(obj(_options)) {
		var _argument;
		for(_argument in _options) {
			switch(_argument) {
				case "class"			: fieldset_class			= _options[_argument]; break;
			}
		}
	}
	return u.ae(node, "fieldset", {"class":fieldset_class});
}
u.f.addField = function(node, _options) {
	var field_name = "js_name";
	var field_label = "Label";
	var field_type = "string";
	var field_value = "";
	var field_options = [];
	var field_checked = false;
	var field_class = "";
	var field_id = "";
	var field_max = false;
	var field_min = false;
	var field_disabled = false;
	var field_readonly = false;
	var field_required = false;
	var field_pattern = false;
	var field_error_message = "There is an error in your input";
	var field_hint_message = "";
	if(obj(_options)) {
		var _argument;
		for(_argument in _options) {
			switch(_argument) {
				case "name"					: field_name			= _options[_argument]; break;
				case "label"				: field_label			= _options[_argument]; break;
				case "type"					: field_type			= _options[_argument]; break;
				case "value"				: field_value			= _options[_argument]; break;
				case "options"				: field_options			= _options[_argument]; break;
				case "checked"				: field_checked			= _options[_argument]; break;
				case "class"				: field_class			= _options[_argument]; break;
				case "id"					: field_id				= _options[_argument]; break;
				case "max"					: field_max				= _options[_argument]; break;
				case "min"					: field_min				= _options[_argument]; break;
				case "disabled"				: field_disabled		= _options[_argument]; break;
				case "readonly"				: field_readonly		= _options[_argument]; break;
				case "required"				: field_required		= _options[_argument]; break;
				case "pattern"				: field_pattern			= _options[_argument]; break;
				case "error_message"		: field_error_message	= _options[_argument]; break;
				case "hint_message"			: field_hint_message	= _options[_argument]; break;
			}
		}
	}
	var custom_build;
	if(field_type in u.f.customBuild) {
		return u.f.customBuild[field_type](node, _options);
	}
	field_id = field_id ? field_id : "input_"+field_type+"_"+field_name;
	field_disabled = !field_disabled ? (field_class.match(/(^| )disabled( |$)/) ? "disabled" : false) : "disabled";
	field_readonly = !field_readonly ? (field_class.match(/(^| )readonly( |$)/) ? "readonly" : false) : "readonly";
	field_required = !field_required ? (field_class.match(/(^| )required( |$)/) ? true : false) : true;
	field_class += field_disabled ? (!field_class.match(/(^| )disabled( |$)/) ? " disabled" : "") : "";
	field_class += field_readonly ? (!field_class.match(/(^| )readonly( |$)/) ? " readonly" : "") : "";
	field_class += field_required ? (!field_class.match(/(^| )required( |$)/) ? " required" : "") : "";
	field_class += field_min ? (!field_class.match(/(^| )min:[0-9]+( |$)/) ? " min:"+field_min : "") : "";
	field_class += field_max ? (!field_class.match(/(^| )max:[0-9]+( |$)/) ? " max:"+field_max : "") : "";
	if (field_type == "hidden") {
		return u.ae(node, "input", {"type":"hidden", "name":field_name, "value":field_value, "id":field_id});
	}
	var field = u.ae(node, "div", {"class":"field "+field_type+" "+field_class});
	var attributes = {};
	if(field_type == "string") {
		field_max = field_max ? field_max : 255;
		attributes = {
			"type":"text", 
			"id":field_id, 
			"value":field_value, 
			"name":field_name, 
			"maxlength":field_max, 
			"minlength":field_min,
			"pattern":field_pattern,
			"readonly":field_readonly,
			"disabled":field_disabled
		};
		u.ae(field, "label", {"for":field_id, "html":field_label});
		u.ae(field, "input", u.f.verifyAttributes(attributes));
	}
	else if(field_type == "email" || field_type == "tel" || field_type == "password") {
		field_max = field_max ? field_max : 255;
		attributes = {
			"type":field_type, 
			"id":field_id, 
			"value":field_value, 
			"name":field_name, 
			"maxlength":field_max, 
			"minlength":field_min,
			"pattern":field_pattern,
			"readonly":field_readonly,
			"disabled":field_disabled
		};
		u.ae(field, "label", {"for":field_id, "html":field_label});
		u.ae(field, "input", u.f.verifyAttributes(attributes));
	}
	else if(field_type == "number" || field_type == "integer" || field_type == "date" || field_type == "datetime") {
		attributes = {
			"type":field_type, 
			"id":field_id, 
			"value":field_value, 
			"name":field_name, 
			"max":field_max, 
			"min":field_min,
			"pattern":field_pattern,
			"readonly":field_readonly,
			"disabled":field_disabled
		};
		u.ae(field, "label", {"for":field_id, "html":field_label});
		u.ae(field, "input", u.f.verifyAttributes(attributes));
	}
	else if(field_type == "checkbox") {
		attributes = {
			"type":field_type, 
			"id":field_id, 
			"value":field_value ? field_value : "true", 
			"name":field_name, 
			"disabled":field_disabled,
			"checked":field_checked
		};
		u.ae(field, "input", {"name":field_name, "value":"false", "type":"hidden"});
		u.ae(field, "input", u.f.verifyAttributes(attributes));
		u.ae(field, "label", {"for":field_id, "html":field_label});
	}
	else if(field_type == "text") {
		attributes = {
			"id":field_id, 
			"html":field_value, 
			"name":field_name, 
			"maxlength":field_max, 
			"minlength":field_min,
			"pattern":field_pattern,
			"readonly":field_readonly,
			"disabled":field_disabled
		};
		u.ae(field, "label", {"for":field_id, "html":field_label});
		u.ae(field, "textarea", u.f.verifyAttributes(attributes));
	}
	else if(field_type == "select") {
		attributes = {
			"id":field_id, 
			"name":field_name, 
			"disabled":field_disabled
		};
		u.ae(field, "label", {"for":field_id, "html":field_label});
		var select = u.ae(field, "select", u.f.verifyAttributes(attributes));
		if(field_options) {
			var i, option;
			for(i = 0; i < field_options.length; i++) {
				option = field_options[i];
				if(option.value == field_value) {
					u.ae(select, "option", {"value":option.value, "html":option.text, "selected":"selected"});
				}
				else {
					u.ae(select, "option", {"value":option.value, "html":option.text});
				}
			}
		}
	}
	else if(field_type == "radiobuttons") {
		u.ae(field, "label", {"html":field_label});
		if(field_options) {
			var i, option;
			for(i = 0; i < field_options.length; i++) {
				option = field_options[i];
				var div = u.ae(field, "div", {"class":"item"});
				if(option.value == field_value) {
					u.ae(div, "input", {"value":option.value, "id":field_id+"-"+i, "type":"radio", "name":field_name, "checked":"checked"});
					u.ae(div, "label", {"for":field_id+"-"+i, "html":option.text});
				}
				else {
					u.ae(div, "input", {"value":option.value, "id":field_id+"-"+i, "type":"radio", "name":field_name});
					u.ae(div, "label", {"for":field_id+"-"+i, "html":option.text});
				}
			}
		}
	}
	else if(field_type == "files") {
		u.ae(field, "label", {"for":field_id, "html":field_label});
		u.ae(field, "input", {"id":field_id, "name":field_name, "type":"file"});
	}
	else {
		u.bug("input type not implemented")
	}
	if(field_hint_message || field_error_message) {
		var help = u.ae(field, "div", {"class":"help"});
		if (field_hint_message) {
			u.ae(help, "div", { "class": "hint", "html": field_hint_message });
		}
		if(field_error_message) {
			u.ae(help, "div", { "class": "error", "html": field_error_message });
		}
	}
	return field;
}
u.f.verifyAttributes = function(attributes) {
	for(attribute in attributes) {
		if(attributes[attribute] === undefined || attributes[attribute] === false || attributes[attribute] === null) {
			delete attributes[attribute];
		}
	}
	return attributes;
}
u.f.addAction = function(node, _options) {
	var action_type = "submit";
	var action_name = "js_name";
	var action_value = "";
	var action_class = "";
	if(obj(_options)) {
		var _argument;
		for(_argument in _options) {
			switch(_argument) {
				case "type"			: action_type			= _options[_argument]; break;
				case "name"			: action_name			= _options[_argument]; break;
				case "value"		: action_value			= _options[_argument]; break;
				case "class"		: action_class			= _options[_argument]; break;
			}
		}
	}
	var p_ul = node.nodeName.toLowerCase() == "ul" ? node : u.pn(node, {"include":"ul.actions"});
	if(!p_ul || !u.hc(p_ul, "actions")) {
		if(node.nodeName.toLowerCase() == "form") {
			p_ul = u.qs("ul.actions", node);
		}
		p_ul = p_ul ? p_ul : u.ae(node, "ul", {"class":"actions"});
	}
	var p_li = node.nodeName.toLowerCase() == "li" ? node : u.pn(node, {"include":"li"});
	if(!p_li || p_ul != p_li.parentNode) {
		p_li = u.ae(p_ul, "li", {"class":action_name});
	}
	else {
		p_li = node;
	}
	var action = u.ae(p_li, "input", {"type":action_type, "class":action_class, "value":action_value, "name":action_name})
	return action;
}
Util.Form.customLabelStyle["inject"] = function(iN) {
	if(!iN.type || !iN.type.match(/file|radio|checkbox/)) {
		iN.default_value = u.text(iN.label);
		u.e.addEvent(iN, "focus", u.f._changed_state);
		u.e.addEvent(iN, "blur", u.f._changed_state);
		if(iN.type.match(/number|integer|password|datetime|date/)) {
			iN.pseudolabel = u.ae(iN.parentNode, "span", {"class":"pseudolabel", "html":iN.default_value});
			iN.pseudolabel.iN = iN;
			u.as(iN.pseudolabel, "top", iN.offsetTop+"px");
			u.as(iN.pseudolabel, "left", iN.offsetLeft+"px");
			u.ce(iN.pseudolabel)
			iN.pseudolabel.inputStarted = function(event) {
				u.e.kill(event);
				this.iN.focus();
			}
		}
		u.f.updateDefaultState(iN);
	}
}
u.f._changed_state = function() {
	u.f.updateDefaultState(this);
}
u.f.updateDefaultState = function(iN) {
	if(iN.is_focused || iN.val() !== "") {
		u.rc(iN, "default");
		if(iN.val() === "") {
			iN.val("");
		}
	}
	else {
		if(iN.val() === "") {
			u.ac(iN, "default");
			iN.val(iN.default_value);
		}
	}
}
Util.Form.customInit["html"] = function(field) {
	field.type = "html";
	field.input = u.qs("textarea", field);
	field.input._form = field._form;
	field.input.label = u.qs("label[for='"+field.input.id+"']", field);
	field.input.field = field;
	field.input.val = u.f._value;
	u.f.textEditor(field);
}
Util.Form.customValidate["html"] = function(iN) {
	min = Number(u.cv(iN.field, "min"));
	max = Number(u.cv(iN.field, "max"));
	min = min ? min : 1;
	max = max ? max : 10000000;
	pattern = iN.getAttribute("pattern");
	if(
		u.text(iN.field._viewer) &&
		u.text(iN.field._viewer).length >= min && 
		u.text(iN.field._viewer).length <= max && 
		(!pattern || iN.val().match("^"+pattern+"$"))
	) {
		u.f.inputIsCorrect(iN);
	}
	else {
		u.f.inputHasError(iN);
	}
}
Util.Form.customHintPosition["html"] = function(field) {
	var input_middle = field._editor.offsetTop + (field._editor.offsetHeight / 2);
	var help_top = input_middle - field.help.offsetHeight / 2;
	u.ass(field.help, {
		"top": help_top + "px"
	});
}
u.f.textEditor = function(field) {
	field.text_support = "h1,h2,h3,h4,h5,h6,p";
	field.code_support = "code";
	field.list_support = "ul,ol";
	field.media_support = "png,jpg,mp4";
	field.ext_video_support = "youtube,vimeo";
	field.file_support = "download"; 
	field.allowed_tags = u.cv(field, "tags");
	if(!field.allowed_tags) {
		u.bug("allowed_tags not specified")
		return;
	}
	field.filterAllowedTags = function(type) {
		tags = this.allowed_tags.split(",");
		this[type+"_allowed"] = new Array();
		var tag, i;
		for(i = 0; i < tags.length; i++) {
			tag = tags[i];
			if(tag.match("^("+this[type+"_support"].split(",").join("|")+")$")) {
				this[type+"_allowed"].push(tag);
			}
		}
	}
	field.filterAllowedTags("text");
	field.filterAllowedTags("list");
	field.filterAllowedTags("media");
	field.filterAllowedTags("ext_video");
	field.filterAllowedTags("file");
	field.filterAllowedTags("code");
	field.file_add_action = field.getAttribute("data-file-add");
	field.file_delete_action = field.getAttribute("data-file-delete");
	field.media_add_action = field.getAttribute("data-media-add");
	field.media_delete_action = field.getAttribute("data-media-delete");
	field.item_id;
	var item_id_match = field._form.action.match(/\/([0-9]+)(\/|$)/);
	if(item_id_match) {
		field.item_id = item_id_match[1];
	}
	field._viewer = u.ae(field, "div", {"class":"viewer"});
	field._editor = u.ae(field, "div", {"class":"editor"});
	field._editor.field = field;
	u.ae(field._editor, field.indicator);
	field._editor.dropped = function() {
		this.field.update();
	}
	field.addOptions = function() {
		this.bn_show_raw = u.ae(this.input.label, "span", {"html":"(RAW HTML)"});
		this.bn_show_raw.field = this;
		u.ce(this.bn_show_raw);
		this.bn_show_raw.clicked = function() {
			if(u.hc(this.field.input, "show")) {
				u.rc(this.field.input, "show");
			}
			else {
				u.ac(this.field.input, "show");
			}
		}
		this.options = u.ae(this, "ul", {"class":"options"});
		this.bn_add = u.ae(this.options, "li", {"class":"add", "html":"+"});
		this.bn_add.field = field;
		u.ce(this.bn_add);
		u.ce(this.options);
		this.options.inputStarted = function(event) {
			u.e.kill(event);
		}
		this.bn_add.clicked = function(event) {
			if(u.hc(this.field.options, "show")) {
				u.rc(this.field.options, "show");
				u.rc(this.field, "optionsshown");
				if(this.start_event_id) {
					u.e.removeWindowStartEvent(this, this.start_event_id);
					delete this.start_event_id;
				}
			}
			else {
				u.ac(this.field.options, "show");
				u.ac(this.field, "optionsshown");
				this.start_event_id = u.e.addWindowStartEvent(this, this.clicked);
			}
		}
		if(this.text_allowed.length) {
			this.bn_add_text = u.ae(this.options, "li", {"class":"text", "html":"Text ("+this.text_allowed.join(", ")+")"});
			this.bn_add_text.field = this;
			u.ce(this.bn_add_text);
			this.bn_add_text.clicked = function(event) {
				this.field.addTextTag(this.field.text_allowed[0]);
				this.field.bn_add.clicked();
			}
		}
		if(this.list_allowed.length) {
			this.bn_add_list = u.ae(this.options, "li", {"class":"list", "html":"List ("+this.list_allowed.join(", ")+")"});
			this.bn_add_list.field = this;
			u.ce(this.bn_add_list);
			this.bn_add_list.clicked = function(event) {
				this.field.addListTag(this.field.list_allowed[0]);
				this.field.bn_add.clicked();
			}
		}
		if(this.code_allowed.length) {
			this.bn_add_code = u.ae(this.options, "li", {"class":"code", "html":"Code"});
			this.bn_add_code.field = this;
			u.ce(this.bn_add_code);
			this.bn_add_code.clicked = function(event) {
				this.field.addCodeTag(this.field.code_allowed[0]);
				this.field.bn_add.clicked();
			}
		}
		if(this.media_allowed.length && this.item_id && this.media_add_action && this.media_delete_action && !u.browser("IE", "<=9")) {
			this.bn_add_media = u.ae(this.options, "li", {"class":"list", "html":"Media ("+this.media_allowed.join(", ")+")"});
			this.bn_add_media.field = this;
			u.ce(this.bn_add_media);
			this.bn_add_media.clicked = function(event) {
				this.field.addMediaTag();
				this.field.bn_add.clicked();
			}
		}
		else if(this.media_allowed.length) {
			u.bug("some information is missing to support media upload:\nitem_id="+this.item_id+"\nmedia_add_action="+this.media_add_action+"\nmedia_delete_action="+this.media_delete_action);
		}
		if(this.ext_video_allowed.length) {
			this.bn_add_ext_video = u.ae(this.options, "li", {"class":"video", "html":"External video ("+this.ext_video_allowed.join(", ")+")"});
			this.bn_add_ext_video.field = this;
			u.ce(this.bn_add_ext_video);
			this.bn_add_ext_video.clicked = function(event) {
				this.field.addExternalVideoTag(this.field.ext_video_allowed[0]);
				this.field.bn_add.clicked();
			}
		}
		if(this.file_allowed.length && this.item_id && this.file_add_action && this.file_delete_action && !u.browser("IE", "<=9")) {
			this.bn_add_file = u.ae(this.options, "li", {"class":"file", "html":"Downloadable file"});
			this.bn_add_file.field = this;
			u.ce(this.bn_add_file);
			this.bn_add_file.clicked = function(event) {
				this.field.addFileTag();
				this.field.bn_add.clicked();
			}
		}
		else if(this.file_allowed.length) {
			u.bug("some information is missing to support file upload:\nitem_id="+this.item_id+"\nfile_add_action="+this.file_add_action+"\nfile_delete_action="+this.file_delete_action);
		}
	}
	field.update = function() {
		this.updateViewer();
		this.updateContent();
		if(fun(this.updated)) {
			this.updated(this.input);
		}
		if(fun(this.changed)) {
			this.changed(this.input);
		}
		if(this.input._form && fun(this.input._form.updated)) {
			this.input._form.updated(this.input);
		}
		if(this.input._form && fun(this.input._form.changed)) {
			this.input._form.changed(this.input);
		}
	}
	field.updateViewer = function() {
		var tags = u.qsa("div.tag", this);
		var i, tag, j, list, li, lis, div, p, a;
		this._viewer.innerHTML = "";
		for(i = 0; i < tags.length; i++) {
			tag = tags[i];
			if(u.hc(tag, this.text_allowed.join("|"))) {
				u.ae(this._viewer, tag._type.val(), {"html":tag._input.val()});
			}
			else if(u.hc(tag, this.list_allowed.join("|"))) {
				list = u.ae(this._viewer, tag._type.val());
				lis = u.qsa("div.li", tag);
				for(j = 0; j < lis.length; j++) {
					li = lis[j];
					li = u.ae(list, tag._type.val(), {"html":li._input.val()});
				}
			}
			else if(u.hc(tag, this.ext_video_allowed.join("|")) && tag._video_id) {
				div = u.ae(this._viewer, "div", {"class":tag._type.val()+" video_id:"+tag._video_id});
			}
			else if(u.hc(tag, "code")) {
				div = u.ae(this._viewer, "code", {"html":tag._input.val()});
			}
			else if(u.hc(tag, "file") && tag._variant) {
				div = u.ae(this._viewer, "div", {"class":"file item_id:"+tag._item_id+" variant:"+tag._variant+" name:"+encodeURIComponent(tag._name)+" filesize:"+tag._filesize});
				p = u.ae(div, "p");
				a = u.ae(p, "a", {"href":"/download/"+tag._item_id+"/"+tag._variant+"/"+tag._name, "html":tag._input.val()});
			}
			else if(u.hc(tag, "media") && tag._variant) {
				div = u.ae(this._viewer, "div", {"class":"media item_id:"+tag._item_id+" variant:"+tag._variant+" name:"+encodeURIComponent(tag._name)+" filesize:"+tag._filesize + " format:"+tag._format});
				p = u.ae(div, "p");
				a = u.ae(p, "a", {"href":"/images/"+tag._item_id+"/"+tag._variant+"/480x."+tag._format, "html":tag._input.val()});
			}
		}
	}
	field.updateContent = function() {
		u.bug("updateContent");
		var tags = u.qsa("div.tag", this);
		this.input.val("");
		var i, node, tag, type, value, j, html = "";
		for(i = 0; i < tags.length; i++) {
			tag = tags[i];
			if(u.hc(tag, this.text_allowed.join("|"))) {
				type = tag._type.val();
				html += '<'+type + (tag._classname ? (' class="'+tag._classname+'"') : '')+'>'+tag._input.val()+'</'+type+'>'+"\n";
			}
			else if(u.hc(tag, this.list_allowed.join("|"))) {
				type = tag._type.val();
				html += "<"+type+(tag._classname ? (' class="'+tag._classname+'"') : '')+">\n";
				lis = u.qsa("div.li", tag);
				for(j = 0; j < lis.length; j++) {
					li = lis[j];
					html += "\t<li>"+li._input.val()+"</li>\n";
				}
				html += "</"+type+">\n";
			}
			else if(u.hc(tag, this.ext_video_allowed.join("|")) && tag._video_id) {
				html += '<div class="'+tag._type.val()+' video_id:'+tag._video_id+'"></div>\n';
			}
			else if(u.hc(tag, "code")) {
				html += '<code'+(tag._classname ? (' class="'+tag._classname+'"') : '')+'>'+tag._input.val()+'</code>'+"\n";
			}
			else if(u.hc(tag, "media") && tag._variant) {
				html += '<div class="media item_id:'+tag._item_id+' variant:'+tag._variant+' name:'+encodeURIComponent(tag._name)+' filesize:'+tag._filesize+' format:'+tag._format+' width:'+tag._width+' height:'+tag._height+'">'+"\n";
				html += '\t<p><a href="/images/'+tag._item_id+'/'+tag._variant+'/480x.'+tag._format+'">'+tag._input.val()+"</a></p>";
				html += "</div>\n";
			}
			else if(u.hc(tag, "file") && tag._variant) {
				html += '<div class="file item_id:'+tag._item_id+' variant:'+tag._variant+' name:'+encodeURIComponent(tag._name)+' filesize:'+tag._filesize+'">'+"\n";
				html += '\t<p><a href="/download/'+tag._item_id+'/'+tag._variant+'/'+tag._name+'">'+tag._input.val()+"</a></p>";
				html += "</div>\n";
			}
		}
		this.input.val(html);
	}
	field.createTag = function(allowed_tags, type) {
		var tag = u.ae(this._editor, "div", {"class":"tag"});
		tag.field = this;
		tag._drag = u.ae(tag, "div", {"class":"drag"});
		tag._drag.field = this;
		tag._drag.tag = tag;
		this.createTagSelector(tag, allowed_tags);
		tag._type.val(type);
		tag.bn_remove = u.ae(tag, "div", {"class":"remove"});
		tag.bn_remove.field = this;
		tag.bn_remove.tag = tag;
		u.ce(tag.bn_remove);
		tag.bn_remove.clicked = function() {
			this.field.deleteTag(this.tag);
		}
		if(u.hc(tag, this.list_allowed.join("|")) || u.hc(tag, this.text_allowed.join("|")) || u.hc(tag, this.code_allowed.join("|"))) {
			tag.bn_classname = u.ae(tag, "div", {"class":"classname"});
			u.ae(tag.bn_classname, "span", {"html":"CSS"});
			tag.bn_classname.field = this;
			tag.bn_classname.tag = tag;
			u.ce(tag.bn_classname);
			tag.bn_classname.clicked = function() {
				this.field.classnameTag(this.tag);
			}
		}
		return tag;
	}
	field.deleteTag = function(tag) {
		if(u.qsa("div.tag", this).length > 1) {
			if(u.hc(tag, "file")) {
				this.deleteFile(tag);
			}
			else if(u.hc(tag, "media")) {
				this.deleteMedia(tag);
			}
			tag.parentNode.removeChild(tag);
			this._editor.updateTargets();
			this._editor.updateDraggables();
			this.update();
			this._form.submit();
		}
	}
	field.classnameTag = function(tag) {
		if(!u.hc(tag.bn_classname, "open")) {
			var form = u.f.addForm(tag.bn_classname, {"class":"labelstyle:inject"});
			var fieldset = u.f.addFieldset(form);
			var input_classname = u.f.addField(fieldset, {"label":"classname", "name":"classname", "error_message":"", "value":tag._classname});
			input_classname.tag = tag;
			u.ac(tag.bn_classname, "open");
			u.f.init(form);
			input_classname.input.focus();
			input_classname.input.blurred = function() {
				this.field.tag._classname = this.val();
				this.field.tag.bn_classname.removeChild(this._form);
				u.rc(this.field.tag.bn_classname, "open");
				this.field.tag.field.update();
			}
		}
	}
	field.createTagSelector = function(tag, allowed_tags) {
		var i, allowed_tag;
		tag._type = u.ae(tag, "ul", {"class":"type"});
		tag._type.field = this;
		tag._type.tag = tag;
		for(i = 0; allowed_tag = allowed_tags[i]; i++) {
			u.ae(tag._type, "li", {"html":allowed_tag, "class":allowed_tag});
		}
		tag._type.val = function(value) {
			if(value !== undefined) {
				var i, option;
				for(i = 0; i < this.childNodes.length; i++) {
					option = this.childNodes[i];
					if(u.text(option) == value) {
						if(this.selected_option) {
							u.rc(this.selected_option, "selected");
							u.rc(this.tag, u.text(this.selected_option));
						}
						u.ac(option, "selected");
						this.selected_option = option;
						u.ac(this.tag, value);
						return option;
					}
				}
				u.ac(this.childNodes[0], "selected");
				this.selected_option = this.childNodes[0];
				u.ac(this.tag, u.text(this.childNodes[0]));
				return this.childNodes[0];
			}
			else {
				return u.text(this.selected_option);
			}
		}
		if(allowed_tags.length > 1) {
			u.ce(tag._type);
			tag._type.clicked = function(event) {
				u.t.resetTimer(this.t_autohide);
				if(u.hc(this, "open")) {
					u.rc(this, "open");
					u.rc(this.tag, "focus");
					u.ass(this.field, {
						"zIndex": this.field._base_z_index
					});
					u.as(this, "top", 0);
					if(event.target) {
						this.val(u.text(event.target));
					}
					u.e.removeEvent(this, "mouseout", this.autohide);
					u.e.removeEvent(this, "mouseover", this.delayautohide);
					this.field.returnFocus(this.tag);
					this.field.update();
				}
				else {
					u.ac(this, "open");
					u.ac(this.tag, "focus");
					u.ass(this.field, {
						"zIndex": this.field._form._focus_z_index,
					});
					u.as(this, "top", -(this.selected_option.offsetTop) + "px");
					u.e.addEvent(this, "mouseout", this.autohide);
					u.e.addEvent(this, "mouseover", this.delayautohide);
				}
			}
			tag._type.hide = function() {
				u.rc(this, "open");
				if(!this.field.is_focused) {
					u.rc(this.tag, "focus");
					u.ass(this.field, {
						"zIndex": this.field._base_z_index
					});
					this.field.returnFocus(this);
				}
				u.as(this, "top", 0);
				u.e.removeEvent(this, "mouseout", this.autohide);
				u.e.removeEvent(this, "mouseover", this.delayautohide);
				u.t.resetTimer(this.t_autohide);
			}
			tag._type.autohide = function(event) {
				u.t.resetTimer(this.t_autohide);
				this.t_autohide = u.t.setTimer(this, this.hide, 800);
			}
			tag._type.delayautohide = function(event) {
				u.t.resetTimer(this.t_autohide);
			}
		}
	}
	field.addExternalVideoTag = function(type, node) {
		var tag = this.createTag(this.ext_video_allowed, type);
		tag._input = u.ae(tag, "div", {"class":"text", "contentEditable":true});
		tag._input.tag = tag;
		tag._input.field = this;
		if(node) {
			tag._video_id = u.cv(node, "video_id");
			tag._input.innerHTML = tag._video_id;
		}
		tag._input.val = function(value) {
			if(value !== undefined) {
				this.innerHTML = value;
			}
			return this.innerHTML;
		}
		u.e.addEvent(tag._input, "keydown", tag.field._changing_content);
		u.e.addEvent(tag._input, "keyup", this._changed_ext_video_content);
		u.e.addEndEvent(tag._input, this._changed_ext_video_content);
		u.e.addEvent(tag._input, "focus", tag.field._focused_content);
		u.e.addEvent(tag._input, "blur", tag.field._blurred_content);
		this._editor.updateTargets();
		this._editor.updateDraggables();
		return tag;
	}
	field._changed_ext_video_content = function(event) {
		if(this.val() && !this.val().replace(/<br>/, "")) {
			this.val("");
		}
		this.tag._video_id = this.val();
		this.tag.field.update();
	}
	field.addMediaTag = function(node) {
		var tag = this.createTag(["media"], "media");
		tag._input = u.ae(tag, "div", {"class":"text"});
		tag._input.tag = tag;
		tag._input.field = this;
		tag._input._form = this._form;
		if(node) {
			tag._name = u.cv(node, "name");
			tag._item_id = u.cv(node, "item_id");
			tag._filesize = u.cv(node, "filesize");
			tag._format = u.cv(node, "format");
			tag._variant = u.cv(node, "variant");
			tag._width = u.cv(node, "width");
			tag._height = u.cv(node, "height");
			tag._input.contentEditable = true;
			tag._input.innerHTML = u.qs("a", node).innerHTML;
			tag._image = u.ie(tag, "img");
			tag._image.src = "/images/"+tag._item_id+"/"+tag._variant+"/400x."+tag._format;
			tag._input.val = function(value) {
				if(value !== undefined) {
					this.innerHTML = value;
				}
				return this.innerHTML;
			}
			u.e.addEvent(tag._input, "keydown", tag.field._changing_content);
			u.e.addEvent(tag._input, "keyup", this._changed_media_content);
			u.e.addEndEvent(tag._input, this._changed_media_content);
			u.e.addEvent(tag._input, "focus", tag.field._focused_content);
			u.e.addEvent(tag._input, "blur", tag.field._blurred_content);
			u.ac(tag, "done");
		}
		else {
			tag._text = tag._input;
			tag._text.tag = tag;
			tag._text.field = this;
			tag._label = u.ae(tag._text, "label", {"html":"Drag media here"});
			tag._input = u.ae(tag._text, "input", {"type":"file", "name":"htmleditor_media[]"});
			tag._input.tag = tag;
			tag._input.field = this;
			tag._input._form = this._form;
			tag._input.val = function(value) {return false;}
			u.e.addEvent(tag._input, "change", this._media_updated);
			u.e.addEvent(tag._input, "focus", this._focused_content);
			u.e.addEvent(tag._input, "blur", this._blurred_content);
			if(u.e.event_pref == "mouse") {
				u.e.addEvent(tag._input, "mouseenter", u.f._mouseenter);
				u.e.addEvent(tag._input, "mouseleave", u.f._mouseleave);
			}
		}
		this._editor.updateTargets();
		this._editor.updateDraggables();
		return tag;
	}
	field.deleteMedia = function(tag) {
		var form_data = new FormData();
		form_data.append("csrf-token", this._form.inputs["csrf-token"].val());
		tag.response = function(response) {
			page.notify(response);
			if(response.cms_status && response.cms_status == "success") {
				this.field.update();
			}
		}
		u.request(tag, this.file_delete_action+"/"+tag._item_id+"/"+tag._variant, {"method":"post", "data":form_data});
	}
	field._media_updated = function(event) {
		var form_data = new FormData();
		form_data.append(this.name, this.files[0], this.value);
		form_data.append("csrf-token", this._form.inputs["csrf-token"].val());
		form_data.append("input-name", this.tag.field.input.name);
		this.response = function(response) {
			page.notify(response);
			if(response.cms_status && response.cms_status == "success") {
				this.parentNode.removeChild(this.tag._label);
				this.parentNode.removeChild(this);
				this.tag._input = this.tag._text;
				this.tag._variant = response.cms_object["variant"];
				this.tag._filesize = response.cms_object["filesize"]
				this.tag._format = response.cms_object["format"]
				this.tag._width = response.cms_object["width"]
				this.tag._height = response.cms_object["height"]
				this.tag._name = response.cms_object["name"]
				this.tag._item_id = response.cms_object["item_id"]
				this.tag._input.contentEditable = true;
				this.tag._image = u.ie(this.tag, "img");
				this.tag._image.src = "/images/"+this.tag._item_id+"/"+this.tag._variant+"/400x."+this.tag._format;
				this.tag._input.innerHTML = this.tag._name + " ("+ u.round((this.tag._filesize/1000), 2) +"Kb)";
				this.tag._input.val = function(value) {
					if(value !== undefined) {
						this.innerHTML = value;
					}
					return this.innerHTML;
				}
				u.e.addEvent(this.tag._input, "keydown", this.tag.field._changing_content);
				u.e.addEvent(this.tag._input, "keyup", this.tag.field._changed_media_content);
				u.e.addEndEvent(this.tag._input, this.tag.field._changed_media_content);
				u.e.addEvent(this.tag._input, "focus", this.tag.field._focused_content);
				u.e.addEvent(this.tag._input, "blur", this.tag.field._blurred_content);
				u.ac(this.tag, "done");
				this.tag.field.update();
				this.tag.field._form.submit();
			}
		}
		u.request(this, this.field.media_add_action+"/"+this.field.item_id, {"method":"post", "data":form_data});
	}
	field._changed_media_content = function(event) {
		if(this.val() && !this.val().replace(/<br>/, "")) {
			this.val("");
		}
		this.field.update();
	}
	field.addFileTag = function(node) {
		var tag = this.createTag(["file"], "file");
		tag._input = u.ae(tag, "div", {"class":"text"});
		tag._input.tag = tag;
		tag._input.field = this;
		tag._input._form = this._form;
		if(node) {
			tag._input.contentEditable = true;
			tag._variant = u.cv(node, "variant");
			tag._name = u.cv(node, "name");
			tag._item_id = u.cv(node, "item_id");
			tag._filesize = u.cv(node, "filesize");
			tag._input.innerHTML = u.qs("a", node).innerHTML;
			tag._input.val = function(value) {
				if(value !== undefined) {
					this.innerHTML = value;
				}
				return this.innerHTML;
			}
			u.e.addEvent(tag._input, "keydown", tag.field._changing_content);
			u.e.addEvent(tag._input, "keyup", this._changed_file_content);
			u.e.addEndEvent(tag._input, this._changed_file_content);
			u.e.addEvent(tag._input, "focus", tag.field._focused_content);
			u.e.addEvent(tag._input, "blur", tag.field._blurred_content);
			u.ac(tag, "done");
		}
		else {
			tag._text = tag._input;
			tag._text.tag = tag;
			tag._text.field = this;
			tag._label = u.ae(tag._text, "label", {"html":"Drag file here"});
			tag._input = u.ae(tag._text, "input", {"type":"file", "name":"htmleditor_file[]"});
			tag._input.tag = tag;
			tag._input.field = this;
			tag._input._form = this._form;
			tag._input.val = function(value) {return false;}
			u.e.addEvent(tag._input, "change", this._file_updated);
			u.e.addEvent(tag._input, "focus", this._focused_content);
			u.e.addEvent(tag._input, "blur", this._blurred_content);
			if(u.e.event_pref == "mouse") {
				u.e.addEvent(tag._input, "mouseenter", u.f._mouseenter);
				u.e.addEvent(tag._input, "mouseleave", u.f._mouseleave);
			}
		}
		this._editor.updateTargets();
		this._editor.updateDraggables();
		return tag;
	}
	field.deleteFile = function(tag) {
		var form_data = new FormData();
		form_data.append("csrf-token", this._form.inputs["csrf-token"].val());
		tag.response = function(response) {
			page.notify(response);
			if(response.cms_status && response.cms_status == "success") {
				this.field.update();
			}
		}
		u.request(tag, this.file_delete_action+"/"+tag._item_id+"/"+tag._variant, {"method":"post", "data":form_data});
	}
	field._file_updated = function(event) {
		var form_data = new FormData();
		form_data.append(this.name, this.files[0], this.value);
		form_data.append("csrf-token", this._form.inputs["csrf-token"].val());
		form_data.append("input-name", this.tag.field.input.name);
		this.response = function(response) {
			page.notify(response);
			if(response.cms_status && response.cms_status == "success") {
				this.parentNode.removeChild(this.tag._label);
				this.parentNode.removeChild(this);
				this.tag._variant = response.cms_object["variant"];
				this.tag._filesize = response.cms_object["filesize"]
				this.tag._name = response.cms_object["name"]
				this.tag._item_id = response.cms_object["item_id"]
				this.tag._text.contentEditable = true;
				this.tag._text.innerHTML = this.tag._name + " ("+ u.round((this.tag._filesize/1000), 2) +"Kb)";
				this.tag._input = this.tag._text;
				this.tag._input.val = function(value) {
					if(value !== undefined) {
						this.innerHTML = value;
					}
					return this.innerHTML;
				}
				u.e.addEvent(this.tag._input, "keydown", this.tag.field._changing_content);
				u.e.addEvent(this.tag._input, "keyup", this.tag.field._changed_file_content);
				u.e.addEvent(this.tag._input, "mouseup", this.tag.field._changed_file_content);
				u.e.addEvent(this.tag._input, "focus", this.tag.field._focused_content);
				u.e.addEvent(this.tag._input, "blur", this.tag.field._blurred_content);
				u.ac(this.tag, "done");
				this.tag.field.update();
				this.tag.field._form.submit();
			}
		}
		u.request(this, this.field.file_add_action+"/"+this.field.item_id, {"method":"post", "data":form_data});
	}
	field._changed_file_content = function(event) {
		if(this.val() && !this.val().replace(/<br>/, "")) {
			this.val("");
		}
		this.field.update();
	}
	field.addCodeTag = function(type, value) {
		var tag = this.createTag(this.code_allowed, type);
		tag._input = u.ae(tag, "div", {"class":"text", "contentEditable":true});
		tag._input.tag = tag;
		tag._input.field = this;
		tag._input._form = this._form;
		tag._input.val = function(value) {
			if(value !== undefined) {
				this.innerHTML = value;
			}
			return this.innerHTML;
		}
		tag._input.val(u.stringOr(value));
		u.e.addEvent(tag._input, "keydown", this._changing_code_content);
		u.e.addEvent(tag._input, "keyup", this._code_updated);
		u.e.addStartEvent(tag._input, this._code_selection_started);
		u.e.addEvent(tag._input, "focus", this._focused_content);
		u.e.addEvent(tag._input, "blur", this._blurred_content);
		if(u.e.event_pref == "mouse") {
			u.e.addEvent(tag._input, "mouseenter", u.f._mouseenter);
			u.e.addEvent(tag._input, "mouseleave", u.f._mouseleave);
		}
		u.e.addEvent(tag._input, "paste", this._pasted_content);
		tag.addNew = function() {
			this.field.addTextItem(this.field.text_allowed[0]);
		}
		this._editor.updateTargets();
		this._editor.updateDraggables();
		return tag;
	}
	field._code_selection_started = function(event) {
		this._selection_event_id = u.e.addWindowEndEvent(this, this.field._code_updated);
	}
	field._changing_code_content = function(event) {
		if(event.keyCode == 13 || event.keyCode == 9) {
			u.e.kill(event);
		}
		if(event.keyCode == 9 && event.shiftKey) {
			this.field.backwards_tab = true;
		}
	}
	field._code_updated = function(event) {
		if(this._selection_event_id) {
			u.e.removeWindowEndEvent(this, this._selection_event_id);
			delete this._selection_event_id;
		}
		var selection = window.getSelection(); 
		if(event.keyCode == 13) {
			u.e.kill(event);
			if(selection && selection.isCollapsed) {
				var br = document.createTextNode("\n");
				range = selection.getRangeAt(0);
				range.insertNode(br);
				range.collapse(false);
				var selection = window.getSelection();
				selection.removeAllRanges();
				selection.addRange(range);
			}
		}
		if(event.keyCode == 9) {
			u.e.kill(event);
			if(selection && selection.isCollapsed) {
				var br = document.createTextNode("\t");
				range = selection.getRangeAt(0);
				range.insertNode(br);
				range.collapse(false);
				var selection = window.getSelection();
				selection.removeAllRanges();
				selection.addRange(range);
			}
		}
		else if(event.keyCode == 8) {
			if(this.is_deletable) {
				u.e.kill(event);
				var all_tags = u.qsa("div.tag", this.field);
				var prev = this.field.findPreviousInput(this);
				if(prev) {
					this.tag.parentNode.removeChild(this.tag);
					prev.focus();
				}
				this.field._editor.updateTargets();
				this.field._editor.updateDraggables();
			}
			else if(!this.val() || !this.val().replace(/<br>/, "")) {
				this.is_deletable = true;
			}
			else if(selection.anchorNode != this && selection.anchorNode.innerHTML == "") {
				selection.anchorNode.parentNode.removeChild(selection.anchorNode);
			}
		}
		else {
			this.is_deletable = false;
		}
		this.field.hideSelectionOptions();
		if(selection && !selection.isCollapsed) {
			var node = selection.anchorNode;
			while(node != this) {
				if(node.nodeName == "HTML" || !node.parentNode) {
					break;
				}
				node = node.parentNode;
			}
			if(node == this) {
				this.field.showSelectionOptions(this, selection);
			}
		}
		this.field.update();
	}
	field.addListTag = function(type, value) {
		var tag = this.createTag(this.list_allowed, type);
		this.addListItem(tag, value);
		this._editor.updateTargets();
		this._editor.updateDraggables();
		return tag;
	}
	field.addListItem = function(tag, value) {
		var li = u.ae(tag, "div", {"class":"li"});
		li.tag = tag;
		li.field = this;
		li._input = u.ae(li, "div", {"class":"text", "contentEditable":true});
		li._input.li = li;
		li._input.tag = tag;
		li._input.field = this;
		li._input._form = this._form;
		li._input.val = function(value) {
			if(value !== undefined) {
				this.innerHTML = value;
			}
			return this.innerHTML;
		}
		li._input.val(u.stringOr(value));
		u.e.addEvent(li._input, "keydown", this._changing_content);
		u.e.addEvent(li._input, "keyup", this._changed_content);
		u.e.addStartEvent(li._input, this._selection_started);
		u.e.addEvent(li._input, "focus", this._focused_content);
		u.e.addEvent(li._input, "blur", this._blurred_content);
		if(u.e.event_pref == "mouse") {
			u.e.addEvent(li._input, "mouseenter", u.f._mouseenter);
			u.e.addEvent(li._input, "mouseleave", u.f._mouseleave);
		}
		u.e.addEvent(li._input, "paste", this._pasted_content);
		this._editor.updateTargets();
		this._editor.updateDraggables();
		return li;
	}
	field.addTextTag = function(type, value) {
		var tag = this.createTag(this.text_allowed, type);
		tag._input = u.ae(tag, "div", {"class":"text", "contentEditable":true});
		tag._input.tag = tag;
		tag._input.field = this;
		tag._input._form = this._form;
		tag._input.val = function(value) {
			if(value !== undefined) {
				this.innerHTML = value;
			}
			return this.innerHTML;
		}
		tag._input.val(u.stringOr(value));
		u.e.addEvent(tag._input, "keydown", this._changing_content);
		u.e.addEvent(tag._input, "keyup", this._changed_content);
		u.e.addStartEvent(tag._input, this._selection_started);
		u.e.addEvent(tag._input, "focus", this._focused_content);
		u.e.addEvent(tag._input, "blur", this._blurred_content);
		if(u.e.event_pref == "mouse") {
			u.e.addEvent(tag._input, "mouseenter", u.f._mouseenter);
			u.e.addEvent(tag._input, "mouseleave", u.f._mouseleave);
		}
		u.e.addEvent(tag._input, "paste", this._pasted_content);
		tag.addNew = function() {
			this.field.addTextItem(this.field.text_allowed[0]);
		}
		this._editor.updateTargets();
		this._editor.updateDraggables();
		return tag;
	}
	field._changing_content = function(event) {
		if(event.keyCode == 13) {
			u.e.kill(event);
		}
		if(event.keyCode == 9 && event.shiftKey) {
			this.field.backwards_tab = true;
		}
	}
	field._selection_started = function(event) {
		this._selection_event_id = u.e.addWindowEndEvent(this, this.field._changed_content);
	}
	field._changed_content = function(event) {
		if(this._selection_event_id) {
			u.e.removeWindowEndEvent(this, this._selection_event_id);
			delete this._selection_event_id;
		}
		var selection = window.getSelection(); 
		if(event.keyCode == 13) {
			u.e.kill(event);
			if(!event.ctrlKey && !event.metaKey) {
				if(u.hc(this.tag, this.field.list_allowed.join("|"))) {
					var new_li = this.field.addListItem(this.tag);
					var next_li = u.ns(this.li);
					if(next_li) {
						this.tag.insertBefore(new_li, next_li);
					}
					else {
						this.tag.appendChild(new_li);
					}
					new_li._input.focus();
				}
				else {
					var new_tag = this.field.addTextTag(this.field.text_allowed[0]);
					var next_tag = u.ns(this.tag);
					if(next_tag) {
						this.tag.parentNode.insertBefore(new_tag, next_tag);
					}
					else {
						this.tag.parentNode.appendChild(new_tag);
					}
					new_tag._input.focus();
				}
			}
			else {
				if(selection && selection.isCollapsed) {
					var br = document.createElement("br");
					range = selection.getRangeAt(0);
					range.insertNode(br);
					range.collapse(false);
					var selection = window.getSelection();
					selection.removeAllRanges();
					selection.addRange(range);
				}
			}
		}
		else if(event.keyCode == 8) {
			if(this.is_deletable) {
				u.e.kill(event);
				var all_tags = u.qsa("div.tag", this.field);
				var prev = this.field.findPreviousInput(this);
				if(u.hc(this.tag, this.field.list_allowed.join("|"))) {
					var all_lis = u.qsa("div.li", this.tag);
					if(prev || all_tags.length > 1) {
						this.li._input.blur();
						this.tag.removeChild(this.li);
						if(!u.qsa("div.li", this.tag).length) {
							this.tag.parentNode.removeChild(this.tag);
						}
					}
				}
				else {
					if(prev || all_tags.length > 1) {
						this.tag.parentNode.removeChild(this.tag);
					}
				}
				this.field._editor.updateTargets();
				this.field._editor.updateDraggables();
				if(prev) {
					prev.focus();
				}
				else {
					if(u.hc(this.tag, this.field.list_allowed.join("|"))) {
						var all_lis = u.qsa("div.li", this.tag);
						all_lis[0]._input.focus();
					}
					else {
						var all_tags = u.qsa("div.tag", this.field);
						all_tags[0]._input.focus();
					}
				}
			}
			else if(!this.val() || !this.val().replace(/<br>/, "")) {
				this.is_deletable = true;
			}
			else if(selection.anchorNode != this && selection.anchorNode.innerHTML == "") {
				selection.anchorNode.parentNode.removeChild(selection.anchorNode);
			}
		}
		else {
			this.is_deletable = false;
		}
		this.field.hideSelectionOptions();
		if(selection && !selection.isCollapsed) {
			u.bug("selection:", this);
			var node = selection.anchorNode;
			while(node != this) {
				if(node.nodeName == "HTML" || !node.parentNode) {
					break;
				}
				node = node.parentNode;
			}
			if(node == this) {
				this.field.showSelectionOptions(this, selection);
			}
		}
		this.field.update();
	}
	field._focused_content = function(event) {
		this.field.is_focused = true;
		u.ac(this.tag, "focus");
		u.ac(this.field, "focus");
		u.as(this.field, "zIndex", this.field._form._focus_z_index);
		u.f.positionHint(this.field);
		if(this.field.backwards_tab) {
			this.field.backwards_tab = false;
			var range = document.createRange();
			range.selectNodeContents(this);
			range.collapse(false);
			var selection = window.getSelection();
			selection.removeAllRanges();
			selection.addRange(range);
		}
	}
	field._blurred_content = function() {
		this.field.is_focused = false;
		u.rc(this.tag, "focus");
		u.rc(this.field, "focus");
		u.as(this.field, "zIndex", this.field._base_z_index);
		u.f.positionHint(this.field);
		this.field.hideSelectionOptions();
	}
	field._pasted_content = function(event) {
		u.e.kill(event);
		var i, node;
		var paste_content = event.clipboardData.getData("text/plain");
		if(paste_content !== "") {
			var selection = window.getSelection();
			if(!selection.isCollapsed) {
				selection.deleteFromDocument();
			}
			var paste_parts = paste_content.trim().split(/\n\r|\n|\r/g);
				var text_nodes = [];
				for(i = 0; i < paste_parts.length; i++) {
					text = paste_parts[i];
					u.bug("text part", text);
					text_nodes.push(document.createTextNode(text));
					if(paste_parts.length && i < paste_parts.length-1) {
						text_nodes.push(document.createElement("br"));
					}
				}
				for(i = text_nodes.length-1; i >= 0; i--) {
					node = text_nodes[i];
					var range = selection.getRangeAt(0);
					range.insertNode(node);
					selection.addRange(range);
				}
				selection.collapseToEnd();
		}
	}
	field.findPreviousInput = function(iN) {
		var prev = false;
		if(u.hc(iN.tag, this.list_allowed.join("|"))) {
			prev = u.ps(iN.li, {"exclude":".drag,.remove,.type"});
		}
		if(!prev) {
			prev = u.ps(iN.tag);
			if(prev && u.hc(prev, this.list_allowed.join("|"))) {
				var items = u.qsa("div.li", prev);
				prev = items[items.length-1];
			}
			else if(prev && u.hc(prev, "file")) {
				if(!prev._variant) {
					var prev_iN = this.findPreviousInput(prev._input);
					if(prev_iN) {
						prev = prev_iN.tag;
					}
					else {
						prev = false;
					}
				}
			}
		}
		if(!prev) {
			prev = u.qs("div.tag", this);
			if(u.hc(prev, this.list_allowed.join("|"))) {
				prev = u.qs("div.li", prev);
			}
			else if(prev && u.hc(prev, "file")) {
				if(!prev._variant) {
					var prev_iN = this.findPreviousInput(prev._input);
					if(prev_iN) {
						prev = prev_iN.tag;
					}
					else {
						prev = false;
					}
				}
			}
		}
		return prev && prev._input != iN ? prev._input : false;
	}
	field.returnFocus = function(tag) {
		if(u.hc(tag, this.text_allowed.join("|"))) {
			tag._input.focus();
		}
		else if(u.hc(tag, "code")) {
			tag._input.focus();
		}
		else if(u.hc(tag, this.list_allowed.join("|"))) {
			var li = u.qs("div.li", tag);
			li._input.focus();
		}
	}
	field.hideSelectionOptions = function() {
		if(this.selection_options && !this.selection_options.is_active) {
			this.selection_options.parentNode.removeChild(this.selection_options);
			this.selection_options = null;
		}
		this.update();
	}
	field.showSelectionOptions = function(node, selection) {
		var x = u.absX(node);
		var y = u.absY(node);
		this.selection_options = u.ae(document.body, "div", {"id":"selection_options"});
		u.as(this.selection_options, "top", y+"px");
		u.as(this.selection_options, "left", (x + node.offsetWidth) +"px");
		var ul = u.ae(this.selection_options, "ul", {"class":"options"});
		this.selection_options._link = u.ae(ul, "li", {"class":"link", "html":"Link"});
		this.selection_options._link.field = this;
		this.selection_options._link.tag = node;
		this.selection_options._link.selection = selection;
		u.ce(this.selection_options._link);
		this.selection_options._link.inputStarted = function(event) {
			u.e.kill(event);
			this.field.selection_options.is_active = true;
		}
		this.selection_options._link.clicked = function(event) {
			u.e.kill(event);
			this.field.addAnchorTag(this.selection, this.tag);
		}
		this.selection_options._em = u.ae(ul, "li", {"class":"em", "html":"Italic"});
		this.selection_options._em.field = this;
		this.selection_options._em.tag = node;
		this.selection_options._em.selection = selection;
		u.ce(this.selection_options._em);
		this.selection_options._em.inputStarted = function(event) {
			u.e.kill(event);
		}
		this.selection_options._em.clicked = function(event) {
			u.e.kill(event);
			this.field.addEmTag(this.selection, this.tag);
		}
		this.selection_options._strong = u.ae(ul, "li", {"class":"strong", "html":"Bold"});
		this.selection_options._strong.field = this;
		this.selection_options._strong.tag = node;
		this.selection_options._strong.selection = selection;
		u.ce(this.selection_options._strong);
		this.selection_options._strong.inputStarted = function(event) {
			u.e.kill(event);
		}
		this.selection_options._strong.clicked = function(event) {
			u.e.kill(event);
			this.field.addStrongTag(this.selection, this.tag);
		}
		this.selection_options._sup = u.ae(ul, "li", {"class":"sup", "html":"Superscript"});
		this.selection_options._sup.field = this;
		this.selection_options._sup.tag = node;
		this.selection_options._sup.selection = selection;
		u.ce(this.selection_options._sup);
		this.selection_options._sup.inputStarted = function(event) {
			u.e.kill(event);
		}
		this.selection_options._sup.clicked = function(event) {
			u.e.kill(event);
			this.field.addSupTag(this.selection, this.tag);
		}
		this.selection_options._span = u.ae(ul, "li", {"class":"span", "html":"CSS class"});
		this.selection_options._span.field = this;
		this.selection_options._span.tag = node;
		this.selection_options._span.selection = selection;
		u.ce(this.selection_options._span);
		this.selection_options._span.inputStarted = function(event) {
			u.e.kill(event);
			this.field.selection_options.is_active = true;
		}
		this.selection_options._span.clicked = function(event) {
			u.e.kill(event);
			this.field.addSpanTag(this.selection, this.tag);
		}
	}
	field.deleteOrEditOption = function(node) {
		node.over = function(event) {
			if(!this.bn_delete) {
				this.bn_delete = u.ae(document.body, "span", {"class":"delete_selection", "html":"X"});
				this.bn_delete.node = this;
				this.bn_delete.over = function(event) {
					u.t.resetTimer(this.node.t_out);
				}
				u.e.addEvent(this.bn_delete, "mouseover", this.bn_delete.over);
				u.ce(this.bn_delete);
				this.bn_delete.clicked = function() {
					u.e.kill(event);
					if(this.node.field.selection_options) {
						this.node.field.selection_options.is_active = false;
						this.node.field.hideSelectionOptions();
					}
					var fragment = document.createTextNode(this.node.innerHTML);
					this.node.parentNode.replaceChild(fragment, this.node);
					this.node.out();
					this.node.field.update();
				}
				u.as(this.bn_delete, "top", (u.absY(this)-5)+"px");
				u.as(this.bn_delete, "left", (u.absX(this)-5)+"px");
			}
			if(this.nodeName.toLowerCase() == "a" || this.nodeName.toLowerCase() == "span" && !this.bn_edit) {
				this.bn_edit = u.ae(document.body, "span", {"class":"edit_selection", "html":"?"});
				this.bn_edit.node = this;
				this.bn_edit.over = function(event) {
					u.t.resetTimer(this.node.t_out);
				}
				u.e.addEvent(this.bn_edit, "mouseover", this.bn_edit.over);
				u.ce(this.bn_edit);
				this.bn_edit.clicked = function() {
					u.e.kill(event);
					if(this.node.nodeName.toLowerCase() == "span") {
						this.node.field.editSpanTag(this.node);
					}
					else if(this.node.nodeName.toLowerCase() == "a") {
						this.node.field.editAnchorTag(this.node);
					}
				}
				u.as(this.bn_edit, "top", (u.absY(this)-5)+"px");
				u.as(this.bn_edit, "left", (u.absX(this)-23)+"px");
			}
		}
		node.out = function(event) {
			if(this.bn_delete) {
				document.body.removeChild(this.bn_delete);
				delete this.bn_delete;
			}
			if(this.bn_edit) {
				document.body.removeChild(this.bn_edit);
				delete this.bn_edit;
			}
		}
		u.e.hover(node, {"delay":1000});
	}
	field.activateInlineFormatting = function(input, tag) {
		var i, node;
		var inline_tags = u.qsa("a,strong,em,span", input);
		for(i = 0; i < inline_tags.length; i++) {
			node = inline_tags[i];
			node.field = input.field;
			node.tag = tag;
			this.deleteOrEditOption(node);
		}
	}
	field.addAnchorTag = function(selection, tag) {
		var range, a, url, target;
		var a = document.createElement("a");
		a.field = this;
		a.tag = tag;
		range = selection.getRangeAt(0);
		try {
			range.surroundContents(a);
			selection.removeAllRanges();
			this.anchorOptions(a);
			this.deleteOrEditOption(a);
		}
		catch(exception) {
			selection.removeAllRanges();
			this.hideSelectionOptions();
			alert("You cannot cross the boundaries of another selection. Yet.");
		}
	}
	field.anchorOptions = function(a) {
		var form = u.f.addForm(this.selection_options, {"class":"labelstyle:inject"});
		u.ae(form, "h3", {"html":"Link options"});
		var fieldset = u.f.addFieldset(form);
		var input_url = u.f.addField(fieldset, {
			"label":"url", 
			"name":"url", 
			"value":a.href.replace(location.protocol + "//" + document.domain, ""), 
			"pattern":"(http[s]?:\\/\\/|mailto:|tel:)[^$]+|\/[^$]*",
			"error_message":"Must start with /, http:// or https://, mailto: or tel:"
		});
		var input_target = u.f.addField(fieldset, {
			"type":"checkbox", 
			"label":"Open in new window?", 
			"checked":(a.target ? "checked" : false), 
			"name":"target", 
			"error_message":""
		});
		var bn_save = u.f.addAction(form, {
			"value":"Save link", 
			"class":"button"
		});
		u.f.init(form);
		form.a = a;
		form.field = this;
		form.submitted = function() {
			if(this.inputs["url"].val()) {
				this.a.href = this.inputs["url"].val();
			}
			else {
				this.a.removeAttribute("href");
			}
			if(this.inputs["target"].val()) {
				this.a.target = "_blank";
			}
			else {
				this.a.removeAttribute("target");
			}
			this.field.selection_options.is_active = false;
			this.field.hideSelectionOptions();
		}
	}
	field.editAnchorTag = function(a) {
		this.hideSelectionOptions();
		var x = u.absX(a.tag);
		var y = u.absY(a.tag);
		this.selection_options = u.ae(document.body, "div", {"id":"selection_options"});
		u.as(this.selection_options, "top", y+"px");
		u.as(this.selection_options, "left", (x + a.tag.offsetWidth) +"px");
		this.selection_options.is_active = false;
		this.anchorOptions(a);
	}
	field.addStrongTag = function(selection, tag) {
		var range, a, url, target;
		var strong = document.createElement("strong");
		strong.field = this;
		strong.tag = tag;
		range = selection.getRangeAt(0);
		try {
			range.surroundContents(strong);
			selection.removeAllRanges();
			this.deleteOrEditOption(strong);
			this.hideSelectionOptions();
		}
		catch(exception) {
			selection.removeAllRanges();
			this.hideSelectionOptions();
			alert("You cannot cross the boundaries of another selection. Yet.");
		}
	}
	field.addEmTag = function(selection, tag) {
		var range, a, url, target;
		var em = document.createElement("em");
		em.field = this;
		em.tag = tag;
		range = selection.getRangeAt(0);
		try {
			range.surroundContents(em);
			selection.removeAllRanges();
			this.deleteOrEditOption(em);
			this.hideSelectionOptions();
		}
		catch(exception) {
			selection.removeAllRanges();
			this.hideSelectionOptions();
			alert("You cannot cross the boundaries of another selection. Yet.");
		}
	}
	field.addSupTag = function(selection, tag) {
		var range, a, url, target;
		var sup = document.createElement("sup");
		sup.field = this;
		sup.tag = tag;
		range = selection.getRangeAt(0);
		try {
			range.surroundContents(sup);
			selection.removeAllRanges();
			this.deleteOrEditOption(sup);
			this.hideSelectionOptions();
		}
		catch(exception) {
			selection.removeAllRanges();
			this.hideSelectionOptions();
			alert("You cannot cross the boundaries of another selection. Yet.");
		}
	}
	field.addSpanTag = function(selection, tag) {
		var span = document.createElement("span");
		span.field = this;
		span.tag = tag;
		var range = selection.getRangeAt(0);
		try {
			range.surroundContents(span);
			selection.removeAllRanges();
			this.spanOptions(span);
			this.deleteOrEditOption(span);
		}
		catch(exception) {
			selection.removeAllRanges();
			this.hideSelectionOptions();
			alert("You cannot cross the boundaries of another selection. Yet.");
		}
	}
	field.editSpanTag = function(span) {
		this.hideSelectionOptions();
		var x = u.absX(span.tag);
		var y = u.absY(span.tag);
		this.selection_options = u.ae(document.body, "div", {"id":"selection_options"});
		u.as(this.selection_options, "top", y+"px");
		u.as(this.selection_options, "left", (x + span.tag.offsetWidth) +"px");
		this.selection_options.is_active = false;
		this.spanOptions(span);
	}
	field.spanOptions = function(span) {
		var form = u.f.addForm(this.selection_options, {"class":"labelstyle:inject"});
		u.ae(form, "h3", {"html":"CSS class"});
		var fieldset = u.f.addFieldset(form);
		var input_classname = u.f.addField(fieldset, {"label":"classname", "name":"classname", "value":span.className, "error_message":""});
		var bn_save = u.f.addAction(form, {"value":"Save class", "class":"button"});
		u.f.init(form);
		form.span = span;
		form.field = this;
		form.submitted = function() {
			if(this.inputs["classname"].val()) {
				this.span.className = this.inputs["classname"].val();
			}
			else {
				this.span.removeAttribute("class");
			}
			this.field.selection_options.is_active = false;
			this.field.hideSelectionOptions();
		}
	}
	field._viewer.innerHTML = field.input.val();
	u.sortable(field._editor, {"draggables":"div.tag", "targets":"div.editor"});
	var value, node, i, tag, j, lis, li;
	var nodes = u.cn(field._viewer, {"exclude":"br"});
	if(nodes.length) {
		for(i = 0; i < field._viewer.childNodes.length; i++) {
			node = field._viewer.childNodes[i];
			if(node.nodeName == "#text") {
				if(node.nodeValue.trim()) {
					var fragments = node.nodeValue.trim().split(/\n\r\n\r|\n\n|\r\r/g);
					if(fragments) {
						for(index in fragments) {
							value = fragments[index].replace(/\n\r|\n|\r/g, "<br>");
							tag = field.addTextTag("p", fragments[index]);
							field.activateInlineFormatting(tag._input, tag);
						}
					}
					else {
						value = node.nodeValue; 
						tag = field.addTextTag("p", value);
						field.activateInlineFormatting(tag._input, tag);
					}
				}
			}
			else if(field.text_allowed && node.nodeName.toLowerCase().match(field.text_allowed.join("|"))) {
				value = node.innerHTML.trim().replace(/(<br>|<br \/>)$/, "").replace(/\n\r|\n|\r/g, "<br>"); 
				tag = field.addTextTag(node.nodeName.toLowerCase(), value);
				if(node.className) {
					tag._classname = node.className;
				}
				field.activateInlineFormatting(tag._input, tag);
			}
			else if(node.nodeName.toLowerCase() == "code") {
				tag = field.addCodeTag(node.nodeName.toLowerCase(), node.innerHTML);
				if(node.className) {
					tag._classname = node.className;
				}
				field.activateInlineFormatting(tag._input, tag);
			}
			else if(field.list_allowed.length && node.nodeName.toLowerCase().match(field.list_allowed.join("|"))) {
				var lis = u.qsa("li", node);
				value = lis[0].innerHTML.trim().replace(/(<br>|<br \/>)$/, "").replace(/\n\r|\n|\r/g, "<br>");
				tag = field.addListTag(node.nodeName.toLowerCase(), value);
				if(node.className) {
					tag._classname = node.className;
				}
				var li = u.qs("div.li", tag);
				field.activateInlineFormatting(li._input, li);
				if(lis.length > 1) {
					for(j = 1; j < lis.length; j++) {
						li = lis[j];
						value = li.innerHTML.trim().replace(/(<br>|<br \/>)$/, "").replace(/\n\r|\n|\r/g, "<br>");
						li = field.addListItem(tag, value);
						field.activateInlineFormatting(li._input, li);
					}
				}
			}
			else if(u.hc(node, "youtube|vimeo")) {
				field.addExternalVideoTag(node.className.match(field.ext_video_allowed.join("|")[0]), node);
			}
			else if(u.hc(node, "file")) {
				field.addFileTag(node);
			}
			else if(u.hc(node, "media")) {
				field.addMediaTag(node);
			}
			else if(node.nodeName.toLowerCase().match(/dl|ul|ol/)) {
				var children = u.cn(node);
				for(j = 0; j < children.length; j++) {
					child = children[j];
					value = child.innerHTML.replace(/\n\r|\n|\r/g, "");
					tag = field.addTextTag(field.text_allowed[0], value);
					field.activateInlineFormatting(tag._input, tag);
				}
			}
			else if(node.nodeName.toLowerCase().match(/h1|h2|h3|h4|h5|code/)) {
				value = node.innerHTML.replace(/\n\r|\n|\r/g, "");
				tag = field.addTextTag(field.text_allowed[0], value);
				field.activateInlineFormatting(tag._input, tag);
			}
			else {
				alert("HTML contains unautorized node:" + node.nodeName + "\nIt has been altered to conform with SEO and design.");
			}
		}
	}
	else {
		value = field._viewer.innerHTML.replace(/\<br[\/]?\>/g, "\n");
		tag = field.addTextTag(field.text_allowed[0], value);
		field.activateInlineFormatting(tag._input, tag);
	}
	field._editor.updateTargets();
	field._editor.updateDraggables();
	field.updateViewer();
	field.addOptions();
}
Util.Form.customInit["location"] = function(field) {
	field.type = "location";
	field.inputs = u.qsa("input", field);
	field.input = field.inputs[0];
	for(j = 0; j < field.inputs.length; j++) {
		input = field.inputs[j];
		input._form = field._form;
		input.label = u.qs("label[for='"+input.id+"']", field);
		input.field = field;
		input.val = u.f._value;
		u.e.addEvent(input, "keyup", u.f._updated);
		u.e.addEvent(input, "change", u.f._changed);
		u.f.inputOnEnter(input);
		u.f.activateInput(input);
	}
	if(navigator.geolocation) {
		u.f.location(field);
	}
}
Util.Form.customValidate["location"] = function(iN) {
	var loc_fields = 0;
	if(iN.field.input) {
		loc_fields++;
		min = 1;
		max = 255;
		if(
			iN.field.input.val().length >= min &&
			iN.field.input.val().length <= max
		) {
			u.f.inputIsCorrect(iN.field.input);
		}
		else {
			u.f.inputHasError(iN.field.input);
		}
	}
	if(iN.field.lat_input) {
		loc_fields++;
		min = -90;
		max = 90;
		if(
			!isNaN(iN.field.lat_input.val()) && 
			iN.field.lat_input.val() >= min && 
			iN.field.lat_input.val() <= max
		) {
			u.f.inputIsCorrect(iN.field.lat_input);
		}
		else {
			u.f.inputHasError(iN.field.lat_input);
		}
	}
	if(iN.field.lon_input) {
		loc_fields++;
		min = -180;
		max = 180;
		if(
			!isNaN(iN.field.lon_input.val()) && 
			iN.field.lon_input.val() >= min && 
			iN.field.lon_input.val() <= max
		) {
			u.f.inputIsCorrect(iN.field.lon_input);
		}
		else {
			u.f.inputHasError(iN.field.lon_input);
		}
	}
	if(u.qsa("input.error", iN.field).length) {
		u.rc(iN.field, "correct");
		u.ac(iN.field, "error");
	}
	else if(u.qsa("input.correct", iN.field).length == loc_fields) {
		u.ac(iN.field, "correct");
		u.rc(iN.field, "error");
	}
}
Util.Form.location = function(field) {
	u.ac(field, "geolocation");
	field.lat_input = u.qs("div.latitude input", field);
	field.lat_input.autocomplete = "off";
	field.lat_input.field = field;
	field.lon_input = u.qs("div.longitude input", field);
	field.lon_input.autocomplete = "off";
	field.lon_input.field = field;
	field.showMap = function() {
		if(!window._mapsiframe) {
			var lat = this.lat_input.val() !== "" ? this.lat_input.val() : 0;
			var lon = this.lon_input.val() !== "" ? this.lon_input.val() : 0;
			var maps_url = "https://maps.googleapis.com/maps/api/js" + (u.gapi_key ? "?key="+u.gapi_key : "");
			var html = '<html><head>';
			html += '<style type="text/css">body {margin: 0;} #map {width: 300px; height: 300px;} #close {width: 25px; height: 25px; position: absolute; top: 0; left: 0; background: #ffffff; z-index: 10; border-bottom-right-radius: 10px; cursor: pointer;}</style>';
			html += '<script type="text/javascript" src="'+maps_url+'"></script>';
			html += '<script type="text/javascript">';
			html += 'var map, marker;';
			html += 'var initialize = function() {';
			html += '	window._map_loaded = true;';
			html += '	var close = document.getElementById("close");';
			html += '	close.onclick = function() {field.hideMap();};';
			html += '	var mapOptions = {center: new google.maps.LatLng('+lat+', '+lon+'),zoom: 15, streetViewControl: false, zoomControlOptions: {position: google.maps.ControlPosition.LEFT_CENTER}};';
			html += '	map = new google.maps.Map(document.getElementById("map"),mapOptions);';
			html += '	marker = new google.maps.Marker({position: new google.maps.LatLng('+lat+', '+lon+'), draggable:true});';
			html += '	marker.setMap(map);';
			html += '	marker.dragend = function(event_type) {';
			html += '		var lat_marker = Math.round(marker.getPosition().lat()*100000)/100000;';
			html += '		var lon_marker = Math.round(marker.getPosition().lng()*100000)/100000;';
			html += '		field.lon_input.val(lon_marker);';
			html += '		field.lat_input.val(lat_marker);';
			html += '	};';
			html += '	marker.addListener("dragend", marker.dragend);';
			html += '};';
			html += 'var centerMap = function(lat, lon) {';
			html += '	var loc = new google.maps.LatLng(lat, lon);';
			html += '	map.setCenter(loc);';
			html += '	marker.setPosition(loc);';
			html += '};';
			html += 'google.maps.event.addDomListener(window, "load", initialize);';
			html += '</script>';
			html += '</head><body><div id="map"></div><div id="close"></div></body></html>';
			window._mapsiframe = u.ae(document.body, "iframe", {"id":"geolocationmap"});
			window._mapsiframe.field = this;
			window._mapsiframe.doc = window._mapsiframe.contentDocument ? window._mapsiframe.contentDocument : window._mapsiframe.contentWindow.document;
			window._mapsiframe.doc.open();
			window._mapsiframe.doc.write(html);
			window._mapsiframe.doc.close();
			window._mapsiframe.doc.field = this;
			u.e.addEvent(window._mapsiframe.doc, "focus", function() {u.t.resetTimer(this.field.t_hide_map)});
		}
		else {
		}
		window._mapsiframe.contentWindow.field = this;
		u.as(window._mapsiframe, "left", (u.absX(this.bn_geolocation)+this.bn_geolocation.offsetWidth+10)+"px");
		u.as(window._mapsiframe, "top", (u.absY(this.bn_geolocation) + (this.bn_geolocation.offsetHeight/2) -(window._mapsiframe.offsetHeight/2))+"px");
	}
	field.updateMap = function() {
		if(window._mapsiframe && window._mapsiframe.contentWindow && window._mapsiframe.contentWindow._map_loaded) {
			window._mapsiframe.contentWindow.centerMap(this.lat_input.val(), this.lon_input.val());
		}
	}
	field.moveMap = function(event) {
		var factor;
		if(this._move_direction) {
			if(event && event.shiftKey) {
				factor = 0.001;
			}
			else {
				factor = 0.0001;
			}
			if(this._move_direction == "38") {
				this.lat_input.val(u.round(parseFloat(this.lat_input.val())+factor, 6));
			}
			else if(this._move_direction == "40") {
				this.lat_input.val(u.round(parseFloat(this.lat_input.val())-factor, 6));
			}
			else if(this._move_direction == "39") {
				this.lon_input.val(u.round(parseFloat(this.lon_input.val())+factor, 6));
			}
			else if(this._move_direction == "37") {
				this.lon_input.val(u.round(parseFloat(this.lon_input.val())-factor, 6));
			}
			this.updateMap();
		}
	}
	field.hideMap = function() {
		u.t.resetTimer(this.t_hide_map);
		if(window._mapsiframe) {
			document.body.removeChild(window._mapsiframe);
			window._mapsiframe = null;
		}
	}
	field._end_move_map = function(event) {
		this.field._move_direction = false;
	}
	field._start_move_map = function(event) {
		if(event.keyCode.toString().match(/37|38|39|40/)) {
			this.field._move_direction = event.keyCode;
			this.field.moveMap(event);
		}
	}
	u.e.addEvent(field.lat_input, "keydown", field._start_move_map);
	u.e.addEvent(field.lon_input, "keydown", field._start_move_map);
	u.e.addEvent(field.lat_input, "keyup", field._end_move_map);
	u.e.addEvent(field.lon_input, "keyup", field._end_move_map);
	field.lat_input.updated = field.lon_input.updated = function() {
		this.field.updateMap();
	}
	field.lat_input.focused = field.lon_input.focused = function() {
		u.t.resetTimer(this.field.t_hide_map);
		this.field.showMap();
	}
	field.lat_input.blurred = field.lon_input.blurred = function() {
		this.field.t_hide_map = u.t.setTimer(this.field, this.field.hideMap, 800);
	}
	field.bn_geolocation = u.ae(field, "div", {"class":"geolocation", "title":"Select current location"});
	field.bn_geolocation.field = field;
	u.ce(field.bn_geolocation);
	field.bn_geolocation.clicked = function() {
		this.transitioned = function() {
			var new_scale;
			if(this._scale == 1.4) {
				new_scale = 1;
			}
			else {
				new_scale = 1.4;
			}
			u.a.scale(this, new_scale);
		}
		this.transitioned();
		window._locationField = this.field;
		window._foundLocation = function(position) {
			var lat = position.coords.latitude;
			var lon = position.coords.longitude;
			window._locationField.lat_input.val(u.round(lat, 6));
			window._locationField.lon_input.val(u.round(lon, 6));
			window._locationField.lat_input.focus();
			window._locationField.lon_input.focus();
			u.a.transition(window._locationField.bn_geolocation, "none");
			u.a.scale(window._locationField.bn_geolocation, 1);
			window._locationField.showMap();
			window._locationField.updateMap();
		}
		window._noLocation = function() {
			window._locationField.lat_input.val(55.676098);
			window._locationField.lon_input.val(12.568337);
			window._locationField.lat_input.focus();
			window._locationField.lon_input.focus();
			u.a.transition(window._locationField.bn_geolocation, "none");
			u.a.scale(window._locationField.bn_geolocation, 1);
			window._locationField.showMap();
			window._locationField.updateMap();
		}
		navigator.geolocation.getCurrentPosition(window._foundLocation, window._noLocation);
	}
}
Util.absoluteX = u.absX = function(node) {
	if(node.offsetParent) {
		return node.offsetLeft + u.absX(node.offsetParent);
	}
	return node.offsetLeft;
}
Util.absoluteY = u.absY = function(node) {
	if(node.offsetParent) {
		return node.offsetTop + u.absY(node.offsetParent);
	}
	return node.offsetTop;
}
Util.relativeX = u.relX = function(node) {
	if(u.gcs(node, "position").match(/absolute/) == null && node.offsetParent && u.gcs(node.offsetParent, "position").match(/relative|absolute|fixed/) == null) {
		return node.offsetLeft + u.relX(node.offsetParent);
	}
	return node.offsetLeft;
}
Util.relativeY = u.relY = function(node) {
	if(u.gcs(node, "position").match(/absolute/) == null && node.offsetParent && u.gcs(node.offsetParent, "position").match(/relative|absolute|fixed/) == null) {
		return node.offsetTop + u.relY(node.offsetParent);
	}
	return node.offsetTop;
}
Util.actualWidth = u.actualW = function(node) {
	return parseInt(u.gcs(node, "width"));
}
Util.actualHeight = u.actualH = function(node) {
	return parseInt(u.gcs(node, "height"));
}
Util.eventX = function(event){
	return (event.targetTouches && event.targetTouches.length ? event.targetTouches[0].pageX : event.pageX);
}
Util.eventY = function(event){
	return (event.targetTouches && event.targetTouches.length ? event.targetTouches[0].pageY : event.pageY);
}
Util.browserWidth = u.browserW = function() {
	return document.documentElement.clientWidth;
}
Util.browserHeight = u.browserH = function() {
	return document.documentElement.clientHeight;
}
Util.htmlWidth = u.htmlW = function() {
	return document.body.offsetWidth + parseInt(u.gcs(document.body, "margin-left")) + parseInt(u.gcs(document.body, "margin-right"));
}
Util.htmlHeight = u.htmlH = function() {
	return document.body.offsetHeight + parseInt(u.gcs(document.body, "margin-top")) + parseInt(u.gcs(document.body, "margin-bottom"));
}
Util.pageScrollX = u.scrollX = function() {
	return window.pageXOffset;
}
Util.pageScrollY = u.scrollY = function() {
	return window.pageYOffset;
}
Util.History = u.h = new function() {
	this.popstate = ("onpopstate" in window);
	this.callbacks = [];
	this.is_listening = false;
	this.navigate = function(url, node, silent) {
		silent = silent || false;
		if((!url.match(/^http[s]?\:\/\//) || url.match(document.domain)) && (!node || !node._a || !node._a.target)) {
			if(this.popstate) {
				history.pushState({}, url, url);
				if(!silent) {
					this.callback(url);
				}
			}
			else {
				if(silent) {
					this.next_hash_is_silent = true;
				}
				location.hash = u.h.getCleanUrl(url);
			}
		}
		else {
			if(!node || !node._a || !node._a.target) {
				location.href = url;
			}
			else {
				window.open(this.url);
			}
		}
	}
	this.callback = function(url) {
		var i, recipient;
		for(i = 0; i < this.callbacks.length; i++) {
			recipient = this.callbacks[i];
			if(fun(recipient.node[recipient.callback])) {
				recipient.node[recipient.callback](url);
			}
		}
	}
	this.removeEvent = function(node, _options) {
		var callback_urlchange = "navigate";
		if(obj(_options)) {
			var argument;
			for(argument in _options) {
				switch(argument) {
					case "callback"		: callback_urlchange		= _options[argument]; break;
				}
			}
		}
		var i, recipient;
		for(i = 0; recipient = this.callbacks[i]; i++) {
			if(recipient.node == node && recipient.callback == callback_urlchange) {
				this.callbacks.splice(i, 1);
				break;
			}
		}
	}
	this.addEvent = function(node, _options) {
		var callback_urlchange = "navigate";
		if(obj(_options)) {
			var argument;
			for(argument in _options) {
				switch(argument) {
					case "callback"		: callback_urlchange		= _options[argument]; break;
				}
			}
		}
		if(!this.is_listening) {
			this.is_listening = true;
			if(this.popstate) {
				u.e.addEvent(window, "popstate", this._urlChanged);
			}
			else if("onhashchange" in window && !u.browser("explorer", "<=7")) {
				u.e.addEvent(window, "hashchange", this._hashChanged);
			}
			else {
				u.h._current_hash = window.location.hash;
				window.onhashchange = this._hashChanged;
				setInterval(
					function() {
						if(window.location.hash !== u.h._current_hash) {
							u.h._current_hash = window.location.hash;
							window.onhashchange();
						}
					}, 200
				);
			}
		}
		this.callbacks.push({"node":node, "callback":callback_urlchange});
	}
	this._urlChanged = function(event) {
		var url = u.h.getCleanUrl(location.href);
		if(event.state || (!event.state && event.path)) {
			u.h.callback(url);
		}
		else {
			history.replaceState({}, url, url);
		}
	}
	this._hashChanged = function(event) {
		if(!location.hash || !location.hash.match(/^#\//)) {
			location.hash = "#/"
			return;
		}
		var url = u.h.getCleanHash(location.hash);
		if(u.h.next_hash_is_silent) {
			delete u.h.next_hash_is_silent;
		}
		else {
			u.h.callback(url);
		}
	}
	this.trail = [];
	this.addToTrail = function(url, node) {
		this.trail.push({"url":url, "node":node});
	}
	this.getCleanUrl = function(string, levels) {
		string = string.replace(location.protocol+"//"+document.domain, "") ? string.replace(location.protocol+"//"+document.domain, "").match(/[^#$]+/)[0] : "/";
		if(!levels) {
			return string;
		}
		else {
			var i, return_string = "";
			var path = string.split("/");
			levels = levels > path.length-1 ? path.length-1 : levels;
			for(i = 1; i <= levels; i++) {
				return_string += "/" + path[i];
			}
			return return_string;
		}
	}
	this.getCleanHash = function(string, levels) {
		string = string.replace("#", "");
		if(!levels) {
			return string;
		}
		else {
			var i, return_string = "";
			var hash = string.split("/");
			levels = levels > hash.length-1 ? hash.length-1 : levels;
			for(i = 1; i <= levels; i++) {
				return_string += "/" + hash[i];
			}
			return return_string;
		}
	}
	this.resolveCurrentUrl = function() {
		return !location.hash ? this.getCleanUrl(location.href) : this.getCleanHash(location.hash);
	}
}
Util.Modules = u.m = new Object();
Util.init = function(scope) {
	var i, node, nodes, module;
	scope = scope && scope.nodeName ? scope : document;
	nodes = u.ges("i\:([_a-zA-Z0-9])+", scope);
	for(i = 0; i < nodes.length; i++) {
		node = nodes[i];
		while((module = u.cv(node, "i"))) {
			u.rc(node, "i:"+module);
			if(module && obj(u.m[module])) {
				u.m[module].init(node);
			}
		}
	}
}
Util.Keyboard = u.k = new function() {
	this.shortcuts = {};
	this.onkeydownCatcher = function(event) {
		u.k.catchKey(event);
	}
	this.addKey = function(node, key, _options) {
		node.callback_keyboard = "clicked";
		node.metakey_required = true;
		if(obj(_options)) {
			var argument;
			for(argument in _options) {
				switch(argument) {
					case "callback"		: node.callback_keyboard	= _options[argument]; break;
					case "metakey"		: node.metakey_required		= _options[argument]; break;
				}
			}
		}
		if(!this.shortcuts.length) {
			u.e.addEvent(document, "keydown", this.onkeydownCatcher);
		}
		if(!this.shortcuts[key.toString().toUpperCase()]) {
			this.shortcuts[key.toString().toUpperCase()] = new Array();
		}
		this.shortcuts[key.toString().toUpperCase()].push(node);
	}
	this.catchKey = function(event) {
		event = event ? event : window.event;
		var key = String.fromCharCode(event.keyCode);
		if(event.keyCode == 27) {
			key = "ESC";
		}
		if(this.shortcuts[key]) {
			var nodes, node, i;
			nodes = this.shortcuts[key];
			for(i = 0; i < nodes.length; i++) {
				node = nodes[i];
				if(u.contains(document.body, node)) {
					if(node.offsetHeight && ((event.ctrlKey || event.metaKey) || (!node.metakey_required || key == "ESC"))) {
						u.e.kill(event);
						if(fun(node[node.callback_keyboard])) {
							node[node.callback_keyboard](event);
						}
					}
				}
				else {
					this.shortcuts[key].splice(i, 1);
					if(!this.shortcuts[key].length) {
						delete this.shortcuts[key];
						break;
					}
					else {
						i--;
					}
				}
			}
		}
	}
}
Util.random = function(min, max) {
	return Math.round((Math.random() * (max - min)) + min);
}
Util.numToHex = function(num) {
	return num.toString(16);
}
Util.hexToNum = function(hex) {
	return parseInt(hex,16);
}
Util.round = function(number, decimals) {
	var round_number = number*Math.pow(10, decimals);
	return Math.round(round_number)/Math.pow(10, decimals);
}
Util.audioPlayer = function(_options) {
	_options = _options || {};
	_options.type = "audio";
	return u.mediaPlayer(_options);
}
Util.videoPlayer = function(_options) {
	_options = _options || {};
	_options.type = "video";
	return u.mediaPlayer(_options);
}
Util.mediaPlayer = function(_options) {
	var player = document.createElement("div");
	player.type = _options && _options.type || "video";
	u.ac(player, player.type+"player");
	player._autoplay = false;
	player._muted = false;
	player._loop = false;
	player._playsinline = false;
	player._crossorigin = "anonymous";
	player._controls = false;
	player._controls_playpause = false;
	player._controls_play = false;
	player._controls_pause = false;
	player._controls_stop = false;
	player._controls_zoom = false;
	player._controls_volume = false;
	player._controls_search = false;
	player._ff_skip = 2;
	player._rw_skip = 2;
	player.media = u.ae(player, player.type);
	if(player.media && fun(player.media.play)) {
		player.load = function(src, _options) {
			if(u.hc(this, "playing")) {
				this.stop();
			}
			u.setupMedia(this, _options);
			if(src) {
				this.media.src = u.correctMediaSource(this, src);
				this.media.load();
			}
		}
		player.play = function(position) {
			if(this.media.currentTime && position !== undefined) {
				this.media.currentTime = position;
			}
			if(this.media.src) {
				return this.media.play();
			}
		}
		player.loadAndPlay = function(src, _options) {
			var position = 0;
			if(obj(_options)) {
				var _argument;
				for(_argument in _options) {
					switch(_argument) {
						case "position"		: position		= _options[_argument]; break;
					}
				}
			}
			this.load(src, _options);
			return this.play(position);
		}
		player.pause = function() {
			this.media.pause();
		}
		player.stop = function() {
			this.media.pause();
			if(this.media.currentTime) {
				this.media.currentTime = 0;
			}
		}
		player.ff = function() {
			if(this.media.src && this.media.currentTime && this.mediaLoaded) {
				this.media.currentTime = (this.media.duration - this.media.currentTime >= this._ff_skip) ? (this.media.currentTime + this._ff_skip) : this.media.duration;
				this.media._timeupdate();
			}
		}
		player.rw = function() {
			if(this.media.src && this.media.currentTime && this.mediaLoaded) {
				this.media.currentTime = (this.media.currentTime >= this._rw_skip) ? (this.media.currentTime - this._rw_skip) : 0;
				this.media._timeupdate();
			}
		}
		player.togglePlay = function() {
			if(u.hc(this, "playing")) {
				this.pause();
			}
			else {
				this.play();
			}
		}
		player.volume = function(value) {
			this.media.volume = value;
			if(value === 0) {
				u.ac(this, "muted");
			}
			else {
				u.rc(this, "muted");
			}
		}
		player.toggleSound = function() {
			if(this.media.volume) {
				this.media.volume = 0;
				u.ac(this, "muted");
			}
			else {
				this.media.volume = 1;
				u.rc(this, "muted");
			}
		}
		player.mute = function() {
			this.media.muted = true;
		}
		player.unmute = function() {
			this.media.removeAttribute(muted);
		}
	}
	else {
		player.load = function() {}
		player.play = function() {}
		player.loadAndPlay = function() {}
		player.pause = function() {}
		player.stop = function() {}
		player.ff = function() {}
		player.rw = function() {}
		player.togglePlay = function() {}
	}
	u.setupMedia(player, _options);
	u.detectMediaAutoplay(player);
	return player;
}
u.setupMedia = function(player, _options) {
	if(obj(_options)) {
		var _argument;
		for(_argument in _options) {
			switch(_argument) {
				case "autoplay"     : player._autoplay               = _options[_argument]; break;
				case "muted"        : player._muted                  = _options[_argument]; break;
				case "loop"         : player._loop                   = _options[_argument]; break;
				case "playsinline"  : player._playsinline            = _options[_argument]; break;
				case "controls"     : player._controls               = _options[_argument]; break;
				case "ff_skip"      : player._ff_skip                = _options[_argument]; break;
				case "rw_skip"      : player._rw_skip                = _options[_argument]; break;
			}
		}
	}
	player.media.autoplay = player._autoplay;
	player.media.loop = player._loop;
	player.media.muted = player._muted;
	player.media.playsinline = player._playsinline;
	player.media.setAttribute("playsinline", player._playsinline);
	player.media.setAttribute("crossorigin", player._crossorigin);
	u.setupMediaControls(player, player._controls);
	player.currentTime = 0;
	player.duration = 0;
	player.mediaLoaded = false;
	player.metaLoaded = false;
	if(!player.media.player) {
		player.media.player = player;
		player.media._loadstart = function(event) {
			u.ac(this.player, "loading");
			if(fun(this.player.loading)) {
				this.player.loading(event);
			}
		}
		u.e.addEvent(player.media, "loadstart", player.media._loadstart);
		player.media._canplaythrough = function(event) {
			u.rc(this.player, "loading");
			if(fun(this.player.canplaythrough)) {
				this.player.canplaythrough(event);
			}
		}
		u.e.addEvent(player.media, "canplaythrough", player.media._canplaythrough);
		player.media._playing = function(event) {
			u.rc(this.player, "loading|paused");
			u.ac(this.player, "playing");
			if(fun(this.player.playing)) {
				this.player.playing(event);
			}
		}
		u.e.addEvent(player.media, "playing", player.media._playing);
		player.media._paused = function(event) {
			u.rc(this.player, "playing|loading");
			u.ac(this.player, "paused");
			if(fun(this.player.paused)) {
				this.player.paused(event);
			}
		}
		u.e.addEvent(player.media, "pause", player.media._paused);
		player.media._stalled = function(event) {
			u.rc(this.player, "playing|paused");
			u.ac(this.player, "loading");
			if(fun(this.player.stalled)) {
				this.player.stalled(event);
			}
		}
		u.e.addEvent(player.media, "stalled", player.media._paused);
		player.media._error = function(event) {
			if(fun(this.player.error)) {
				this.player.error(event);
			}
		}
		u.e.addEvent(player.media, "error", player.media._error);
		player.media._ended = function(event) {
			u.rc(this.player, "playing|paused");
			if(fun(this.player.ended)) {
				this.player.ended(event);
			}
		}
		u.e.addEvent(player.media, "ended", player.media._ended);
		player.media._loadedmetadata = function(event) {
			this.player.duration = this.duration;
			this.player.currentTime = this.currentTime;
			this.player.metaLoaded = true;
			if(fun(this.player.loadedmetadata)) {
				this.player.loadedmetadata(event);
			}
		}
		u.e.addEvent(player.media, "loadedmetadata", player.media._loadedmetadata);
		player.media._loadeddata = function(event) {
			this.player.mediaLoaded = true;
			if(fun(this.player.loadeddata)) {
				this.player.loadeddata(event);
			}
		}
		u.e.addEvent(player.media, "loadeddata", player.media._loadeddata);
		player.media._timeupdate = function(event) {
			this.player.currentTime = this.currentTime;
			if(fun(this.player.timeupdate)) {
				this.player.timeupdate(event);
			}
		}
		u.e.addEvent(player.media, "timeupdate", player.media._timeupdate);
	}
}
u.correctMediaSource = function(player, src) {
	var param = src.match(/\?[^$]+/) ? src.match(/(\?[^$]+)/)[1] : "";
	src = src.replace(/\?[^$]+/, "");
	if(player.type == "video") {
		src = src.replace(/(\.m4v|\.mp4|\.webm|\.ogv|\.3gp|\.mov)$/, "");
		if(player.flash) {
			return src+".mp4"+param;
		}
		else if(player.media.canPlayType("video/mp4")) {
			return src+".mp4"+param;
		}
		else if(player.media.canPlayType("video/ogg")) {
			return src+".ogv"+param;
		}
		else if(player.media.canPlayType("video/3gpp")) {
			return src+".3gp"+param;
		}
		else {
			return src+".mov"+param;
		}
	}
	else {
		src = src.replace(/(.mp3|.ogg|.wav)$/, "");
		if(player.flash) {
			return src+".mp3"+param;
		}
		if(player.media.canPlayType("audio/mpeg")) {
			return src+".mp3"+param;
		}
		else if(player.media.canPlayType("audio/ogg")) {
			return src+".ogg"+param;
		}
		else {
			return src+".wav"+param;
		}
	}
}
u.setupMediaControls = function(player, _options) {
	if(obj(_options)) {
		var _argument;
		for(_argument in _options) {
			switch(_argument) {
				case "playpause"    : player._controls_playpause     = _options[_argument]; break;
				case "play"         : player._controls_play          = _options[_argument]; break;
				case "stop"         : player._controls_stop          = _options[_argument]; break;
				case "pause"        : player._controls_pause         = _options[_argument]; break;
				case "volume"       : player._controls_volume        = _options[_argument]; break;
				case "search"       : player._controls_search        = _options[_argument]; break;
			}
		}
	}
	player._custom_controls = obj(_options) && (
		player._controls_playpause ||
		player._controls_play ||
		player._controls_stop ||
		player._controls_pause ||
		player._controls_volume ||
		player._controls_search
	) || false;
	if(player._custom_controls || !_options) {
		player.media.removeAttribute("controls");
	}
	else {
		player.media.controls = player._controls;
	}
	if(!player._custom_controls && player.controls) {
		player.removeChild(player.controls);
		delete player.controls;
	}
	else if(player._custom_controls) {
		if(!player.controls) {
			player.controls = u.ae(player, "div", {"class":"controls"});
			player.controls.player = player;
			player.controls.out = function() {
				u.a.transition(this, "all 0.3s ease-out");
				u.ass(this, {
					"opacity":0
				});
			}
			player.controls.over = function() {
				u.a.transition(this, "all 0.5s ease-out");
				u.ass(this, {
					"opacity":1
				});
			}
			u.e.hover(player.controls);
		}
		if(player._controls_playpause) {
			if(!player.controls.playpause) {
				player.controls.playpause = u.ae(player.controls, "a", {"class":"playpause"});
				player.controls.playpause.player = player;
				u.e.click(player.controls.playpause);
				player.controls.playpause.clicked = function(event) {
					this.player.togglePlay();
				}
			}
		}
		else if(player.controls.playpause) {
			player.controls.playpause.parentNode.removeChild(player.controls.playpause);
			delete player.controls.playpause;
		}
		if(player._controls_play) {
			if(!player.controls.play) {
				player.controls.play = u.ae(player.controls, "a", {"class":"play"});
				player.controls.play.player = player;
				u.e.click(player.controls.play);
				player.controls.play.clicked = function(event) {
					this.player.togglePlay();
				}
			}
		}
		else if(player.controls.play) {
			player.controls.play.parentNode.removeChild(player.controls.play);
			delete player.controls.play;
		}
		if(player._controls_pause) {
			if(!player.controls.pause) {
				player.controls.pause = u.ae(player.controls, "a", {"class":"pause"});
				player.controls.pause.player = player;
				u.e.click(player.controls.pause);
				player.controls.pause.clicked = function(event) {
					this.player.togglePlay();
				}
			}
		}
		else if(player.controls.pause) {
			player.controls.pause.parentNode.removeChild(player.controls.pause);
			delete player.controls.pause;
		}
		if(player._controls_stop) {
			if(!player.controls.stop) {
				player.controls.stop = u.ae(player.controls, "a", {"class":"stop" });
				player.controls.stop.player = player;
				u.e.click(player.controls.stop);
				player.controls.stop.clicked = function(event) {
					this.player.stop();
				}
			}
		}
		else if(player.controls.stop) {
			player.controls.stop.parentNode.removeChild(player.controls.stop);
			delete player.controls.stop;
		}
		if(player._controls_search) {
			if(!player.controls.search) {
				player.controls.search_ff = u.ae(player.controls, "a", {"class":"ff"});
				player.controls.search_ff._default_display = u.gcs(player.controls.search_ff, "display");
				player.controls.search_ff.player = player;
				player.controls.search_rw = u.ae(player.controls, "a", {"class":"rw"});
				player.controls.search_rw._default_display = u.gcs(player.controls.search_rw, "display");
				player.controls.search_rw.player = player;
				u.e.click(player.controls.search_ff);
				player.controls.search_ff.ffing = function() {
					this.t_ffing = u.t.setTimer(this, this.ffing, 100);
					this.player.ff();
				}
				player.controls.search_ff.inputStarted = function(event) {
					this.ffing();
				}
				player.controls.search_ff.clicked = function(event) {
					u.t.resetTimer(this.t_ffing);
				}
				u.e.click(player.controls.search_rw);
				player.controls.search_rw.rwing = function() {
					this.t_rwing = u.t.setTimer(this, this.rwing, 100);
					this.player.rw();
				}
				player.controls.search_rw.inputStarted = function(event) {
					this.rwing();
				}
				player.controls.search_rw.clicked = function(event) {
					u.t.resetTimer(this.t_rwing);
					this.player.rw();
				}
				player.controls.search = true;
			}
			else {
				u.as(player.controls.search_ff, "display", player.controls.search_ff._default_display);
				u.as(player.controls.search_rw, "display", player.controls.search_rw._default_display);
			}
		}
		else if(player.controls.search) {
			u.as(player.controls.search_ff, "display", "none");
			u.as(player.controls.search_rw, "display", "none");
		}
		if(player._controls_zoom && !player.controls.zoom) {}
		else if(player.controls.zoom) {}
		if(player._controls_volume && !player.controls.volume) {}
		else if(player.controls.volume) {}
	}
}
u.detectMediaAutoplay = function(player) {
	if(!u.media_autoplay_detection) {
		u.media_autoplay_detection = [player];
		u.test_autoplay = document.createElement("video");
		u.test_autoplay.check = function() {
			if(u.media_can_autoplay !== undefined && u.media_can_autoplay_muted !== undefined) {
				for(var i = 0, player; i < u.media_autoplay_detection.length; i++) {
					player = u.media_autoplay_detection[i];
					player.can_autoplay = u.media_can_autoplay;
					player.can_autoplay_muted = u.media_can_autoplay_muted;
					if(fun(player.ready)) {
						player.ready();
					}
				}
				u.media_autoplay_detection = true;
				u.test_autoplay.pause();
				delete u.test_autoplay;
			}
		}
		u.test_autoplay.playing = function(event) {
			u.media_can_autoplay = true;
			u.media_can_autoplay_muted = true;
			this.check();
		}
		u.test_autoplay.notplaying = function() {
			u.media_can_autoplay = false;
			u.test_autoplay.muted = true;
			var promise = u.test_autoplay.play();
			if(promise && fun(promise.then)) {
				promise.then(
					function(){
						if(u.test_autoplay) {
							u.t.resetTimer(window.u.test_autoplay.t_check);
							u.test_autoplay.playing_muted();
						}
					}
				).catch(
					function() {
						if(u.test_autoplay) {
							u.t.resetTimer(window.u.test_autoplay.t_check)
							u.test_autoplay.notplaying_muted();
						}
					}
				);
				u.test_autoplay.t_check = u.t.setTimer(u.test_autoplay, function(){
					u.test_autoplay.pause();
				}, 1000);
			}
		}
		u.test_autoplay.playing_muted = function() {
			u.media_can_autoplay_muted = true;
			this.check();
		}
		u.test_autoplay.notplaying_muted = function() {
			u.media_can_autoplay_muted = false;
			this.check();
		}
		u.test_autoplay.error = function(event) {
			u.media_can_autoplay = false;
			u.media_can_autoplay_muted = false;
			this.check();
		}
		u.e.addEvent(u.test_autoplay, "playing", u.test_autoplay.playing);
		u.e.addEvent(u.test_autoplay, "error", u.test_autoplay.error);
		if(u.test_autoplay.canPlayType("video/mp4")) {
			var data = "data:video/mp4;base64,AAAAIGZ0eXBpc29tAAACAGlzb21pc28yYXZjMW1wNDEAAAAIZnJlZQAAAxJtZGF0AAACoAYF//+c3EXpvebZSLeWLNgg2SPu73gyNjQgLSBjb3JlIDE1NyAtIEguMjY0L01QRUctNCBBVkMgY29kZWMgLSBDb3B5bGVmdCAyMDAzLTIwMTggLSBodHRwOi8vd3d3LnZpZGVvbGFuLm9yZy94MjY0Lmh0bWwgLSBvcHRpb25zOiBjYWJhYz0xIHJlZj0zIGRlYmxvY2s9MTowOjAgYW5hbHlzZT0weDM6MHgxMTMgbWU9aGV4IHN1Ym1lPTcgcHN5PTEgcHN5X3JkPTEuMDA6MC4wMCBtaXhlZF9yZWY9MSBtZV9yYW5nZT0xNiBjaHJvbWFfbWU9MSB0cmVsbGlzPTEgOHg4ZGN0PTEgY3FtPTAgZGVhZHpvbmU9MjEsMTEgZmFzdF9wc2tpcD0xIGNocm9tYV9xcF9vZmZzZXQ9LTIgdGhyZWFkcz0xIGxvb2thaGVhZF90aHJlYWRzPTEgc2xpY2VkX3RocmVhZHM9MCBucj0wIGRlY2ltYXRlPTEgaW50ZXJsYWNlZD0wIGJsdXJheV9jb21wYXQ9MCBjb25zdHJhaW5lZF9pbnRyYT0wIGJmcmFtZXM9MyBiX3B5cmFtaWQ9MiBiX2FkYXB0PTEgYl9iaWFzPTAgZGlyZWN0PTEgd2VpZ2h0Yj0xIG9wZW5fZ29wPTAgd2VpZ2h0cD0yIGtleWludD0yNTAga2V5aW50X21pbj0yNSBzY2VuZWN1dD00MCBpbnRyYV9yZWZyZXNoPTAgcmNfbG9va2FoZWFkPTQwIHJjPWNyZiBtYnRyZWU9MSBjcmY9MjMuMCBxY29tcD0wLjYwIHFwbWluPTAgcXBtYXg9NjkgcXBzdGVwPTQgaXBfcmF0aW89MS40MCBhcT0xOjEuMDAAgAAAABpliIQAM//+9uy+BTYUyFCXESoMDuxA1w9RcQAAAAlBmiJsQr/+RRgAAAAIAZ5BeQr/IeHeAgBMYXZjNTguMzUuMTAwAEIgCMEYOCEQBGCMHCEQBGCMHCEQBGCMHCEQBGCMHAAABXNtb292AAAAbG12aGQAAAAAAAAAAAAAAAAAAAPoAAAAeAABAAABAAAAAAAAAAAAAAAAAQAAAAAAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADAAACb3RyYWsAAABcdGtoZAAAAAMAAAAAAAAAAAAAAAEAAAAAAAAAeAAAAAAAAAAAAAAAAAAAAAAAAQAAAAAAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAAAAAEAAAAAAMAAAADAAAAAAACRlZHRzAAAAHGVsc3QAAAAAAAAAAQAAAHgAAAQAAAEAAAAAAedtZGlhAAAAIG1kaGQAAAAAAAAAAAAAAAAAADIAAAAGAFXEAAAAAAAtaGRscgAAAAAAAAAAdmlkZQAAAAAAAAAAAAAAAFZpZGVvSGFuZGxlcgAAAAGSbWluZgAAABR2bWhkAAAAAQAAAAAAAAAAAAAAJGRpbmYAAAAcZHJlZgAAAAAAAAABAAAADHVybCAAAAABAAABUnN0YmwAAACmc3RzZAAAAAAAAAABAAAAlmF2YzEAAAAAAAAAAQAAAAAAAAAAAAAAAAAAAAAAMAAwAEgAAABIAAAAAAAAAAEAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAY//8AAAAwYXZjQwFkAAr/4QAXZ2QACqzZTewEQAAAAwBAAAAMg8SJZYABAAZo6+PLIsAAAAAQcGFzcAAAAAEAAAABAAAAGHN0dHMAAAAAAAAAAQAAAAMAAAIAAAAAFHN0c3MAAAAAAAAAAQAAAAEAAAAoY3R0cwAAAAAAAAADAAAAAQAABAAAAAABAAAGAAAAAAEAAAIAAAAAHHN0c2MAAAAAAAAAAQAAAAEAAAADAAAAAQAAACBzdHN6AAAAAAAAAAAAAAADAAACwgAAAA0AAAAMAAAAFHN0Y28AAAAAAAAAAQAAADAAAAIudHJhawAAAFx0a2hkAAAAAwAAAAAAAAAAAAAAAgAAAAAAAAB1AAAAAAAAAAAAAAABAQAAAAABAAAAAAAAAAAAAAAAAAAAAQAAAAAAAAAAAAAAAAAAQAAAAAAAAAAAAAAAAAAAJGVkdHMAAAAcZWxzdAAAAAAAAAABAAAAdQAAAAAAAQAAAAABpm1kaWEAAAAgbWRoZAAAAAAAAAAAAAAAAAAArEQAABQAVcQAAAAAAC1oZGxyAAAAAAAAAABzb3VuAAAAAAAAAAAAAAAAU291bmRIYW5kbGVyAAAAAVFtaW5mAAAAEHNtaGQAAAAAAAAAAAAAACRkaW5mAAAAHGRyZWYAAAAAAAAAAQAAAAx1cmwgAAAAAQAAARVzdGJsAAAAZ3N0c2QAAAAAAAAAAQAAAFdtcDRhAAAAAAAAAAEAAAAAAAAAAAACABAAAAAArEQAAAAAADNlc2RzAAAAAAOAgIAiAAIABICAgBRAFQAAAAAAEX4AAAymBYCAgAISEAaAgIABAgAAABhzdHRzAAAAAAAAAAEAAAAFAAAEAAAAABxzdHNjAAAAAAAAAAEAAAABAAAABQAAAAEAAAAoc3RzegAAAAAAAAAAAAAABQAAABcAAAAGAAAABgAAAAYAAAAGAAAAFHN0Y28AAAAAAAAAAQAAAwsAAAAac2dwZAEAAAByb2xsAAAAAgAAAAH//wAAABxzYmdwAAAAAHJvbGwAAAABAAAABQAAAAEAAABidWR0YQAAAFptZXRhAAAAAAAAACFoZGxyAAAAAAAAAABtZGlyYXBwbAAAAAAAAAAAAAAAAC1pbHN0AAAAJal0b28AAAAdZGF0YQAAAAEAAAAATGF2ZjU4LjIwLjEwMA==";
			u.test_autoplay.volume = 0.01;
			u.test_autoplay.autoplay = true;
			u.test_autoplay.playsinline = true;
			u.test_autoplay.setAttribute("playsinline", true);
			u.test_autoplay.src = data;
			var promise = u.test_autoplay.play();
			if(promise && fun(promise.then)) {
				u.e.removeEvent(u.test_autoplay, "playing", u.test_autoplay.playing);
				u.e.removeEvent(u.test_autoplay, "error", u.test_autoplay.error);
				promise.then(
					u.test_autoplay.playing.bind(u.test_autoplay)
				).catch(
					u.test_autoplay.notplaying.bind(u.test_autoplay)
				);
			}
		}
		else {
			u.media_can_autoplay = true;
			u.media_can_autoplay_muted = true;
			u.t.setTimer(u.test_autoplay, function() {
				this.check();
			}, 20);
		}
	}
	else if(u.media_autoplay_detection !== true) {
		u.media_autoplay_detection.push(player);
	}
	else {
		u.t.setTimer(player, function() {
			this.can_autoplay = u.media_can_autoplay;
			this.can_autoplay_muted = u.media_can_autoplay_muted;
			if(fun(this.ready)){
				this.ready();
			}
		}, 20);
	}
}
u.navigation = function(_options) {
	var navigation_node = page;
	var callback_navigate = "_navigate";
	var initialization_scope = page.cN;
	if(obj(_options)) {
		var argument;
		for(argument in _options) {
			switch(argument) {
				case "callback"       : callback_navigate           = _options[argument]; break;
				case "node"           : navigation_node             = _options[argument]; break;
				case "scope"          : initialization_scope        = _options[argument]; break;
			}
		}
	}
	window._man_nav_path = window._man_nav_path ? window._man_nav_path : u.h.getCleanUrl(location.href, 1);
	navigation_node._navigate = function(url) {
		var clean_url = u.h.getCleanUrl(url);
		u.stats.pageView(url);
		if(
			!window._man_nav_path || 
			(!u.h.popstate && window._man_nav_path != u.h.getCleanHash(location.hash, 1)) || 
			(u.h.popstate && window._man_nav_path != u.h.getCleanUrl(location.href, 1))
		) {
			if(this.cN && fun(this.cN.navigate)) {
				this.cN.navigate(clean_url, url);
			}
		}
		else {
			if(this.cN.scene && this.cN.scene.parentNode && fun(this.cN.scene.navigate)) {
				this.cN.scene.navigate(clean_url, url);
			}
			else if(this.cN && fun(this.cN.navigate)) {
				this.cN.navigate(clean_url, url);
			}
		}
		if(!u.h.popstate) {
			window._man_nav_path = u.h.getCleanHash(location.hash, 1);
		}
		else {
			window._man_nav_path = u.h.getCleanUrl(location.href, 1);
		}
	}
	if(location.hash.length && location.hash.match(/^#!/)) {
		location.hash = location.hash.replace(/!/, "");
	}
	var callback_after_init = false;
	if(!this.is_initialized) {
		this.is_initialized = true;
		if(!u.h.popstate) {
			if(location.hash.length < 2) {
				window._man_nav_path = u.h.getCleanUrl(location.href);
				u.h.navigate(window._man_nav_path);
				u.init(initialization_scope);
			}
			else if(location.hash.match(/^#\//) && u.h.getCleanHash(location.hash) != u.h.getCleanUrl(location.href)) {
				callback_after_init = u.h.getCleanHash(location.hash);
			}
			else {
				u.init(initialization_scope);
			}
		}
		else {
			if(u.h.getCleanHash(location.hash) != u.h.getCleanUrl(location.href) && location.hash.match(/^#\//)) {
				window._man_nav_path = u.h.getCleanHash(location.hash);
				u.h.navigate(window._man_nav_path);
				callback_after_init = window._man_nav_path;
			}
			else {
				u.init(initialization_scope);
			}
		}
		var random_string = u.randomString(8);
		if(callback_after_init) {
			eval('navigation_node._initNavigation_'+random_string+' = function() {u.h.addEvent(this, {"callback":"'+callback_navigate+'"});u.h.callback("'+callback_after_init+'");}');
		}
		else {
			eval('navigation_node._initNavigation_'+random_string+' = function() {u.h.addEvent(this, {"callback":"'+callback_navigate+'"});}');
		}
		u.t.setTimer(navigation_node, "_initNavigation_"+random_string, 100);
	}
	else {
		u.h.callbacks.push({"node":navigation_node, "callback":callback_navigate});
	}
}
u.objectValues = function(obj) {
	var key, values = [];
	for(key in obj) {
		if(obj.hasOwnProperty(key)) {
			values.push(obj[key]);
		}
	}
	return values;
}
u.preloader = function(node, files, _options) {
	var callback_preloader_loaded = "loaded";
	var callback_preloader_loading = "loading";
	var callback_preloader_waiting = "waiting";
	node._callback_min_delay = 0;
	if(obj(_options)) {
		var _argument;
		for(_argument in _options) {
			switch(_argument) {
				case "loaded"               : callback_preloader_loaded       = _options[_argument]; break;
				case "loading"              : callback_preloader_loading      = _options[_argument]; break;
				case "waiting"              : callback_preloader_waiting      = _options[_argument]; break;
				case "callback_min_delay"   : node._callback_min_delay              = _options[_argument]; break;
			}
		}
	}
	if(!u._preloader_queue) {
		u._preloader_queue = document.createElement("div");
		u._preloader_processes = 0;
		if(u.e && u.e.event_support == "touch") {
			u._preloader_max_processes = 1;
		}
		else {
			u._preloader_max_processes = 2;
		}
	}
	if(node && files) {
		var entry, file;
		var new_queue = u.ae(u._preloader_queue, "ul");
		new_queue._callback_loaded = callback_preloader_loaded;
		new_queue._callback_loading = callback_preloader_loading;
		new_queue._callback_waiting = callback_preloader_waiting;
		new_queue._node = node;
		new_queue._files = files;
		new_queue.nodes = new Array();
		new_queue._start_time = new Date().getTime();
		for(i = 0; i < files.length; i++) {
			file = files[i];
			entry = u.ae(new_queue, "li", {"class":"waiting"});
			entry.i = i;
			entry._queue = new_queue
			entry._file = file;
		}
		u.ac(node, "waiting");
		if(fun(node[new_queue._callback_waiting])) {
			node[new_queue._callback_waiting](new_queue.nodes);
		}
	}
	u._queueLoader();
	return u._preloader_queue;
}
u._queueLoader = function() {
	if(u.qs("li.waiting", u._preloader_queue)) {
		while(u._preloader_processes < u._preloader_max_processes) {
			var next = u.qs("li.waiting", u._preloader_queue);
			if(next) {
				if(u.hc(next._queue._node, "waiting")) {
					u.rc(next._queue._node, "waiting");
					u.ac(next._queue._node, "loading");
					if(fun(next._queue._node[next._queue._callback_loading])) {
						next._queue._node[next._queue._callback_loading](next._queue.nodes);
					}
				}
				u._preloader_processes++;
				u.rc(next, "waiting");
				u.ac(next, "loading");
				if(next._file.match(/png|jpg|gif|svg/)) {
					next.loaded = function(event) {
						this.image = event.target;
						this._image = this.image;
						this._queue.nodes[this.i] = this;
						u.rc(this, "loading");
						u.ac(this, "loaded");
						u._preloader_processes--;
						if(!u.qs("li.waiting,li.loading", this._queue)) {
							u.rc(this._queue._node, "loading");
							if(fun(this._queue._node[this._queue._callback_loaded])) {
								this._queue._node[this._queue._callback_loaded](this._queue.nodes);
							}
						}
						u._queueLoader();
					}
					u.loadImage(next, next._file);
				}
				else if(next._file.match(/mp3|aac|wav|ogg/)) {
					next.loaded = function(event) {
						console.log(event);
						this._queue.nodes[this.i] = this;
						u.rc(this, "loading");
						u.ac(this, "loaded");
						u._preloader_processes--;
						if(!u.qs("li.waiting,li.loading", this._queue)) {
							u.rc(this._queue._node, "loading");
							if(fun(this._queue._node[this._queue._callback_loaded])) {
								this._queue._node[this._queue._callback_loaded](this._queue.nodes);
							}
						}
						u._queueLoader();
					}
					if(fun(u.audioPlayer)) {
						next.audioPlayer = u.audioPlayer();
						next.load(next._file);
					}
					else {
						u.bug("You need u.audioPlayer to preload MP3s");
					}
				}
				else {
				}
			}
			else {
				break
			}
		}
	}
}
u.loadImage = function(node, src) {
	var image = new Image();
	image.node = node;
	u.ac(node, "loading");
    u.e.addEvent(image, 'load', u._imageLoaded);
	u.e.addEvent(image, 'error', u._imageLoadError);
	image.src = src;
}
u._imageLoaded = function(event) {
	u.rc(this.node, "loading");
	if(fun(this.node.loaded)) {
		this.node.loaded(event);
	}
}
u._imageLoadError = function(event) {
	u.rc(this.node, "loading");
	u.ac(this.node, "error");
	if(fun(this.node.loaded) && typeof(this.node.failed) != "function") {
		this.node.loaded(event);
	}
	else if(fun(this.node.failed)) {
		this.node.failed(event);
	}
}
u._imageLoadProgress = function(event) {
	u.bug("progress")
	if(fun(this.node.progress)) {
		this.node.progress(event);
	}
}
u._imageLoadDebug = function(event) {
	u.bug("event:" + event.type);
	u.xInObject(event);
}
Util.createRequestObject = function() {
	return new XMLHttpRequest();
}
Util.request = function(node, url, _options) {
	var request_id = u.randomString(6);
	node[request_id] = {};
	node[request_id].request_url = url;
	node[request_id].request_method = "GET";
	node[request_id].request_async = true;
	node[request_id].request_data = "";
	node[request_id].request_headers = false;
	node[request_id].request_credentials = false;
	node[request_id].response_type = false;
	node[request_id].callback_response = "response";
	node[request_id].callback_error = "responseError";
	node[request_id].jsonp_callback = "callback";
	node[request_id].request_timeout = false;
	if(obj(_options)) {
		var argument;
		for(argument in _options) {
			switch(argument) {
				case "method"				: node[request_id].request_method			= _options[argument]; break;
				case "params"				: node[request_id].request_data				= _options[argument]; break;
				case "data"					: node[request_id].request_data				= _options[argument]; break;
				case "async"				: node[request_id].request_async			= _options[argument]; break;
				case "headers"				: node[request_id].request_headers			= _options[argument]; break;
				case "credentials"			: node[request_id].request_credentials		= _options[argument]; break;
				case "responseType"			: node[request_id].response_type			= _options[argument]; break;
				case "callback"				: node[request_id].callback_response		= _options[argument]; break;
				case "error_callback"		: node[request_id].callback_error			= _options[argument]; break;
				case "jsonp_callback"		: node[request_id].jsonp_callback			= _options[argument]; break;
				case "timeout"				: node[request_id].request_timeout			= _options[argument]; break;
			}
		}
	}
	if(node[request_id].request_method.match(/GET|POST|PUT|PATCH/i)) {
		node[request_id].HTTPRequest = this.createRequestObject();
		node[request_id].HTTPRequest.node = node;
		node[request_id].HTTPRequest.request_id = request_id;
		if(node[request_id].request_async) {
			node[request_id].HTTPRequest.statechanged = function() {
				if(this.readyState == 4 || this.IEreadyState) {
					u.validateResponse(this);
				}
			}
			if(fun(node[request_id].HTTPRequest.addEventListener)) {
				u.e.addEvent(node[request_id].HTTPRequest, "readystatechange", node[request_id].HTTPRequest.statechanged);
			}
		}
		try {
			if(node[request_id].request_method.match(/GET/i)) {
				var params = u.JSONtoParams(node[request_id].request_data);
				node[request_id].request_url += params ? ((!node[request_id].request_url.match(/\?/g) ? "?" : "&") + params) : "";
				node[request_id].HTTPRequest.open(node[request_id].request_method, node[request_id].request_url, node[request_id].request_async);
				if(node[request_id].response_type) {
					node[request_id].HTTPRequest.responseType = node[request_id].response_type;
				}
				if(node[request_id].request_timeout) {
					node[request_id].HTTPRequest.timeout = node[request_id].request_timeout;
				}
				if(node[request_id].request_credentials) {
					node[request_id].HTTPRequest.withCredentials = true;
				}
				if(typeof(node[request_id].request_headers) != "object" || (!node[request_id].request_headers["Content-Type"] && !node[request_id].request_headers["content-type"])) {
					node[request_id].HTTPRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				}
				if(obj(node[request_id].request_headers)) {
					var header;
					for(header in node[request_id].request_headers) {
						node[request_id].HTTPRequest.setRequestHeader(header, node[request_id].request_headers[header]);
					}
				}
				node[request_id].HTTPRequest.setRequestHeader("X-Requested-With", "XMLHttpRequest");
				node[request_id].HTTPRequest.send("");
			}
			else if(node[request_id].request_method.match(/POST|PUT|PATCH|DELETE/i)) {
				var params;
				if(obj(node[request_id].request_data) && node[request_id].request_data.constructor.toString().match(/function Object/i)) {
					params = JSON.stringify(node[request_id].request_data);
				}
				else {
					params = node[request_id].request_data;
				}
				node[request_id].HTTPRequest.open(node[request_id].request_method, node[request_id].request_url, node[request_id].request_async);
				if(node[request_id].response_type) {
					node[request_id].HTTPRequest.responseType = node[request_id].response_type;
				}
				if(node[request_id].request_timeout) {
					node[request_id].HTTPRequest.timeout = node[request_id].request_timeout;
				}
				if(node[request_id].request_credentials) {
					node[request_id].HTTPRequest.withCredentials = true;
				}
				if(!params.constructor.toString().match(/FormData/i) && (typeof(node[request_id].request_headers) != "object" || (!node[request_id].request_headers["Content-Type"] && !node[request_id].request_headers["content-type"]))) {
					node[request_id].HTTPRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				}
				if(obj(node[request_id].request_headers)) {
					var header;
					for(header in node[request_id].request_headers) {
						node[request_id].HTTPRequest.setRequestHeader(header, node[request_id].request_headers[header]);
					}
				}
				node[request_id].HTTPRequest.setRequestHeader("X-Requested-With", "XMLHttpRequest");
				node[request_id].HTTPRequest.send(params);
			}
		}
		catch(exception) {
			node[request_id].HTTPRequest.exception = exception;
			u.validateResponse(node[request_id].HTTPRequest);
			return;
		}
		if(!node[request_id].request_async) {
			u.validateResponse(node[request_id].HTTPRequest);
		}
	}
	else if(node[request_id].request_method.match(/SCRIPT/i)) {
		if(node[request_id].request_timeout) {
			node[request_id].timedOut = function(requestee) {
				this.status = 0;
				delete this.timedOut;
				delete this.t_timeout;
				Util.validateResponse({node: requestee.node, request_id: requestee.request_id, status:this.status});
			}
			node[request_id].t_timeout = u.t.setTimer(node[request_id], "timedOut", node[request_id].request_timeout, {node: node, request_id: request_id});
		}
		var key = u.randomString();
		document[key] = new Object();
		document[key].key = key;
		document[key].node = node;
		document[key].request_id = request_id;
		document[key].responder = function(response) {
			var response_object = new Object();
			response_object.node = this.node;
			response_object.request_id = this.request_id;
			response_object.responseText = response;
			u.t.resetTimer(this.node[this.request_id].t_timeout);
			delete this.node[this.request_id].timedOut;
			delete this.node[this.request_id].t_timeout;
			u.qs("head").removeChild(this.node[this.request_id].script_tag);
			delete this.node[this.request_id].script_tag;
			delete document[this.key];
			u.validateResponse(response_object);
		}
		var params = u.JSONtoParams(node[request_id].request_data);
		node[request_id].request_url += params ? ((!node[request_id].request_url.match(/\?/g) ? "?" : "&") + params) : "";
		node[request_id].request_url += (!node[request_id].request_url.match(/\?/g) ? "?" : "&") + node[request_id].jsonp_callback + "=document."+key+".responder";
		node[request_id].script_tag = u.ae(u.qs("head"), "script", ({"type":"text/javascript", "src":node[request_id].request_url}));
	}
	return request_id;
}
Util.JSONtoParams = function(json) {
	if(obj(json)) {
		var params = "", param;
		for(param in json) {
			params += (params ? "&" : "") + param + "=" + json[param];
		}
		return params
	}
	var object = u.isStringJSON(json);
	if(object) {
		return u.JSONtoParams(object);
	}
	return json;
}
Util.evaluateResponseText = function(responseText) {
	var object;
	if(obj(responseText)) {
		responseText.isJSON = true;
		return responseText;
	}
	else {
		var response_string;
		if(responseText.trim().substr(0, 1).match(/[\"\']/i) && responseText.trim().substr(-1, 1).match(/[\"\']/i)) {
			response_string = responseText.trim().substr(1, responseText.trim().length-2);
		}
		else {
			response_string = responseText;
		}
		var json = u.isStringJSON(response_string);
		if(json) {
			return json;
		}
		var html = u.isStringHTML(response_string);
		if(html) {
			return html;
		}
		return responseText;
	}
}
Util.validateResponse = function(HTTPRequest){
	var object = false;
	if(HTTPRequest) {
		var node = HTTPRequest.node;
		var request_id = HTTPRequest.request_id;
		var request = node[request_id];
		request.response_url = HTTPRequest.responseURL || request.request_url;
		delete request.HTTPRequest;
		if(request.finished) {
			return;
		}
		request.finished = true;
		try {
			request.status = HTTPRequest.status;
			if(HTTPRequest.status && !HTTPRequest.status.toString().match(/[45][\d]{2}/)) {
				if(HTTPRequest.responseType && HTTPRequest.response) {
					object = HTTPRequest.response;
				}
				else if(HTTPRequest.responseText) {
					object = u.evaluateResponseText(HTTPRequest.responseText);
				}
			}
			else if(HTTPRequest.responseText && typeof(HTTPRequest.status) == "undefined") {
				object = u.evaluateResponseText(HTTPRequest.responseText);
			}
		}
		catch(exception) {
			request.exception = exception;
		}
	}
	else {
		console.log("Lost track of this request. There is no way of routing it back to requestee.")
		return;
	}
	if(object !== false) {
		if(fun(request.callback_response)) {
			request.callback_response(object, request_id);
		}
		else if(fun(node[request.callback_response])) {
			node[request.callback_response](object, request_id);
		}
	}
	else {
		if(fun(request.callback_error)) {
			request.callback_error({error:true,status:request.status}, request_id);
		}
		else if(fun(node[request.callback_error])) {
			node[request.callback_error]({error:true,status:request.status}, request_id);
		}
		else if(fun(request.callback_response)) {
			request.callback_response({error:true,status:request.status}, request_id);
		}
		else if(fun(node[request.callback_response])) {
			node[request.callback_response]({error:true,status:request.status}, request_id);
		}
	}
}
u.scrollTo = function(node, _options) {
	node._callback_scroll_to = "scrolledTo";
	node._callback_scroll_cancelled = "scrollToCancelled";
	var offset_y = 0;
	var offset_x = 0;
	var scroll_to_x = 0;
	var scroll_to_y = 0;
	var to_node = false;
	node._force_scroll_to = false;
	if(obj(_options)) {
		var _argument;
		for(_argument in _options) {
			switch(_argument) {
				case "callback"             : node._callback_scroll_to            = _options[_argument]; break;
				case "callback_cancelled"   : node._callback_scroll_cancelled     = _options[_argument]; break;
				case "offset_y"             : offset_y                           = _options[_argument]; break;
				case "offset_x"             : offset_x                           = _options[_argument]; break;
				case "node"                 : to_node                            = _options[_argument]; break;
				case "x"                    : scroll_to_x                        = _options[_argument]; break;
				case "y"                    : scroll_to_y                        = _options[_argument]; break;
				case "scrollIn"             : scrollIn                           = _options[_argument]; break;
				case "force"                : node._force_scroll_to              = _options[_argument]; break;
			}
		}
	}
	if(to_node) {
		node._to_x = u.absX(to_node);
		node._to_y = u.absY(to_node);
	}
	else {
		node._to_x = scroll_to_x;
		node._to_y = scroll_to_y;
	}
	node._to_x = offset_x ? node._to_x - offset_x : node._to_x;
	node._to_y = offset_y ? node._to_y - offset_y : node._to_y;
	if (Util.support("scrollBehavior")) {
		var test = node.scrollTo({top:node._to_y, left:node._to_x, behavior: 'smooth'});
	}
	else {
		if(node._to_y > (node == window ? document.body.scrollHeight : node.scrollHeight)-u.browserH()) {
			node._to_y = (node == window ? document.body.scrollHeight : node.scrollHeight)-u.browserH();
		}
		if(node._to_x > (node == window ? document.body.scrollWidth : node.scrollWidth)-u.browserW()) {
			node._to_x = (node == window ? document.body.scrollWidth : node.scrollWidth)-u.browserW();
		}
		node._to_x = node._to_x < 0 ? 0 : node._to_x;
		node._to_y = node._to_y < 0 ? 0 : node._to_y;
		node._x_scroll_direction = node._to_x - u.scrollX();
		node._y_scroll_direction = node._to_y - u.scrollY();
		node._scroll_to_x = u.scrollX();
		node._scroll_to_y = u.scrollY();
		node._ignoreWheel = function(event) {
			u.e.kill(event);
		}
		if(node._force_scroll_to) {
			u.e.addEvent(node, "wheel", node._ignoreWheel);
		}
		node._scrollToHandler = function(event) {
			u.t.resetTimer(this.t_scroll);
			this.t_scroll = u.t.setTimer(this, this._scrollTo, 25);
		}
		node._cancelScrollTo = function() {
			if(!this._force_scroll_to) {
				u.t.resetTimer(this.t_scroll);
				this._scrollTo = null;
			}
		}
		node._scrollToFinished = function() {
			u.t.resetTimer(this.t_scroll);
			u.e.removeEvent(this, "wheel", this._ignoreWheel);
			this._scrollTo = null;
		}
		node._ZoomScrollFix = function(s_x, s_y) {
			if(Math.abs(this._scroll_to_y - s_y) <= 2 && Math.abs(this._scroll_to_x - s_x) <= 2) {
				return true;
			}
			return false;
		}
		node._scrollTo = function(start) {
			var s_x = u.scrollX();
			var s_y = u.scrollY();
			if((s_y == this._scroll_to_y && s_x == this._scroll_to_x) || this._force_scroll_to || this._ZoomScrollFix(s_x, s_y)) {
				if(this._x_scroll_direction > 0 && this._to_x > s_x) {
					this._scroll_to_x = Math.ceil(this._scroll_to_x + (this._to_x - this._scroll_to_x)/6);
				}
				else if(this._x_scroll_direction < 0 && this._to_x < s_x) {
					this._scroll_to_x = Math.floor(this._scroll_to_x - (this._scroll_to_x - this._to_x)/6);
				}
				else {
					this._scroll_to_x = this._to_x;
				}
				if(this._y_scroll_direction > 0 && this._to_y > s_y) {
					this._scroll_to_y = Math.ceil(this._scroll_to_y + (this._to_y - this._scroll_to_y)/6);
				}
				else if(this._y_scroll_direction < 0 && this._to_y < s_y) {
					this._scroll_to_y = Math.floor(this._scroll_to_y - (this._scroll_to_y - this._to_y)/6);
				}
				else {
					this._scroll_to_y = this._to_y;
				}
				if(this._scroll_to_x == this._to_x && this._scroll_to_y == this._to_y) {
					this._scrollToFinished();
					this.scrollTo(this._to_x, this._to_y);
					if(fun(this[this._callback_scroll_to])) {
						this[this._callback_scroll_to]();
					}
					return;
				}
				this.scrollTo(this._scroll_to_x, this._scroll_to_y);
				this._scrollToHandler();
			}
			else {
				this._cancelScrollTo();
				if(fun(this[this._callback_scroll_cancelled])) {
					this[this._callback_scroll_cancelled]();
				}
			}	
		}
		node._scrollTo();
	}
}
u.sortable = function(scope, _options) {
	scope._callback_picked = "picked";
	scope._callback_moved = "moved";
	scope._callback_dropped = "dropped";
	scope._draggable_selector;
	scope._target_selector;
	scope._layout;
	scope._allow_clickpick = false;
	scope._allow_nesting = false;
	scope._sorting_disabled = false;
	scope._distance_to_pick = 2;
	if(obj(_options)) {
		var _argument;
		for(_argument in _options) {
			switch(_argument) {
				case "picked"				: scope._callback_picked		= _options[_argument]; break;
				case "moved"				: scope._callback_moved			= _options[_argument]; break;
				case "dropped"				: scope._callback_dropped		= _options[_argument]; break;
				case "draggables"			: scope._draggable_selector		= _options[_argument]; break;
				case "targets"				: scope._target_selector		= _options[_argument]; break;
				case "layout"				: scope._layout					= _options[_argument]; break;
				case "allow_clickpick"		: scope._allow_clickpick		= _options[_argument]; break;
				case "allow_nesting"		: scope._allow_nesting			= _options[_argument]; break;
				case "sorting_disabled"		: scope._sorting_disabled		= _options[_argument]; break;
				case "distance_to_pick"		: scope._distance_to_pick		= _options[_argument]; break;
			}
		}
	}
	if(!fun(scope.resetSortableEvents)) {
		scope._sortableInputStart = function(event) {
			if(!this.draggable_node.scope._sorting_disabled) {
				this.draggable_node._start_event_x = u.eventX(event);
				this.draggable_node._start_event_y = u.eventY(event);
				this.draggable_node.current_xps = 0;
				this.draggable_node.current_yps = 0;
				this.draggable_node._move_timestamp = event.timeStamp;
				this.draggable_node._move_last_x = 0;
				this.draggable_node._move_last_y = 0;
				u.e.addMoveEvent(this.draggable_node, this.draggable_node.scope._sortablePick);
				u.e.addEndEvent(this.draggable_node, this.draggable_node.scope._cancelSortablePick);
				if(event.type.match(/mouse/)) {
		 			u.e.addOutEvent(this.draggable_node.drag, this.draggable_node.scope._sortableOut);
				}
				this.draggable_node.scope._org_css_user_select = document.body.style.userSelect;
				u.ass(document.body, {
					"user-select": "none"
				});
			}
		}
		scope._cancelSortablePick = function(event) {
			if(!this.scope._allow_clickpick) {
				this.scope.resetSortableEvents(this);
				u.ass(document.body, {
					"user-select": this.scope._org_css_user_select
				});
			}
		}
		scope._sortableOut = function(event) {
			var edoi = this.draggable_node._event_drop_out_id = u.randomString();
			document["_DroppedOutNode" + edoi] = this.draggable_node;
			eval('document["_DroppedOutMove' + edoi + '"] = function(event) {document["_DroppedOutNode' + edoi + '"].scope._sortablePick.bind(document["_DroppedOutNode' + edoi + '"])(event);}');
			u.e.addEvent(document, "mousemove", document["_DroppedOutMove" + edoi]);
			eval('document["_DroppedOutOver' + edoi + '"] = function(event) {document["_DroppedOutNode' + edoi + '"].scope.resetSortableOutEvents(document["_DroppedOutNode' + edoi + '"]);}');
			u.e.addEvent(this.draggable_node, "mouseover", document["_DroppedOutOver" + edoi]);
			eval('document["_DroppedOutEnd' + edoi + '"] = function(event) {u.bug("### up save");document["_DroppedOutNode' + edoi + '"].scope._cancelSortablePick.bind(document["_DroppedOutNode' + edoi + '"])(event);}');
			u.e.addEvent(document, "mouseup", document["_DroppedOutEnd" + edoi]);
		}
		scope._sortablePick = function(event) {
			var event_x = u.eventX(event);
			var event_y = u.eventY(event);
			this.current_x = event_x - this._start_event_x;
			this.current_y = event_y - this._start_event_y;
			var init_distance_x = Math.abs(this.current_x);
			var init_distance_y = Math.abs(this.current_y);
			if((init_distance_x > this.scope._distance_to_pick || init_distance_y > this.scope._distance_to_pick)) {
				this.scope.resetNestedSortableEvents(this);
				u.e.kill(event);
				this.scope._dragged_node = this;
				this._mouse_ox = event_x - u.absX(this);
				this._mouse_oy = event_y - u.absY(this);
				this.current_xps = Math.round(((this.current_x - this._move_last_x) / (event.timeStamp - this._move_timestamp)) * 1000);
				this.current_yps = Math.round(((this.current_y - this._move_last_y) / (event.timeStamp - this._move_timestamp)) * 1000);
				this._move_timestamp = event.timeStamp;
				this._move_last_x = this.current_x;
				this._move_last_y = this.current_y;
				this.scope._shadow_node = u.ae(this.parentNode, this.cloneNode(true));
				this.parentNode.insertBefore(this.scope._shadow_node, this);
				u.ac(this.scope._shadow_node, "shadow");
				this.scope._recalculateRelativeShadowOffset();
				var _start_width = u.gcs(this, "width");
				u.ass(this.scope._shadow_node, {
					width: _start_width,
					position: "absolute",
					left: ((event_x - this.scope._shadow_node._rel_ox) - this._mouse_ox) + "px",
					top: ((event_y - this.scope._shadow_node.rel_oy) - this._mouse_oy) + "px",
				});
				u.ac(this, "dragged");
				this._event_move_id = u.e.addWindowMoveEvent(this, this.scope._sortableDrag);
				this._event_end_id = u.e.addWindowEndEvent(this, this.scope._sortableDrop);
				if(fun(this.scope[this.scope._callback_picked])) {
					this.scope[this.scope._callback_picked](this);
				}
			}
		}
		scope._sortableDrag = function(event) {
			var i, node;
			var event_x = u.eventX(event);
			var event_y = u.eventY(event);
			var d_left = event_x - this._mouse_ox;
			var d_top = event_y - this._mouse_oy;
			this.current_x = event_x - this._start_event_x;
			this.current_y = event_y - this._start_event_y;
			this.current_xps = Math.round(((this.current_x - this._move_last_x) / (event.timeStamp - this._move_timestamp)) * 1000);
			this.current_yps = Math.round(((this.current_y - this._move_last_y) / (event.timeStamp - this._move_timestamp)) * 1000);
			this._move_timestamp = event.timeStamp;
			this._move_last_x = this.current_x;
			this._move_last_y = this.current_y;
			this.scope._detectAndInject(event_x, event_y);
			u.ass(this.scope._shadow_node, {
				"position": "absolute",
				"left": (d_left - this.scope._shadow_node._rel_ox)+"px",
				"top": (d_top - this.scope._shadow_node._rel_oy)+"px",
				"bottom": "auto"
			});
			if(fun(this.scope[this.scope._callback_moved])) {
				this.scope[this.scope._callback_moved](this);
			}
		}
		scope._sortableDrop = function(event) {
			u.e.kill(event);
			this.scope.resetSortableEvents(this);
			this.scope._shadow_node.parentNode.removeChild(this.scope._shadow_node);
			delete this.scope._shadow_node;
			u.rc(this, "dragged");
			this.scope._dragged_node = false;
			this.current_xps = 0;
			this.current_yps = 0;
			this._move_timestamp = event.timeStamp;
			this._move_last_x = 0;
			this._move_last_y = 0;
			this.scope.updateDraggables();
			u.ass(document.body, {
				"user-select": this.scope._org_css_user_select
			});
			if(fun(this.scope[this.scope._callback_dropped])) {
				this.scope[this.scope._callback_dropped](this);
			}
		}
		scope._recalculateRelativeShadowOffset = function() {
			if(this._shadow_node) {
				this._shadow_node._rel_ox = u.absX(this._shadow_node) - u.relX(this._shadow_node);
				this._shadow_node._rel_oy = u.absY(this._shadow_node) - u.relY(this._shadow_node);
			}
		}
		scope._detectAndInject = function(event_x, event_y) {
			for(i = this.draggable_nodes.length-1; i >= 0; i--) {
				node = this.draggable_nodes[i];
				if(this.target_nodes.indexOf(node.parentNode) !== -1) {
					if(node.parentNode._layout == "multiline") {
						var o_left = u.absX(node);
						var o_top = u.absY(node);
						var o_width = node.offsetWidth;
						var o_height = node.offsetHeight;
					 	if(event_x > o_left && event_x < o_left + o_width && event_y > o_top && event_y < o_top + o_height) {
							if(node !== this._dragged_node) {
								if(event_x < o_left + o_width/2) {
									node.parentNode.insertBefore(this._dragged_node, node);
								}
								else {
									var next = u.ns(node, {exclude: ".target,.dragged"});
									if(next) {
										node.parentNode.insertBefore(this._dragged_node, next);
									}
									else {
										node.parentNode.appendChild(this._dragged_node);
									}
								}
								this._recalculateRelativeShadowOffset();
								break;
							}
						}
					}
					else if(node.parentNode._layout == "horizontal") {
						var o_left = u.absX(node);
						var o_width = node.offsetWidth;
					 	if(event_x > o_left && event_x < o_left + o_width) {
							if(node !== this._dragged_node && !u.pn(node, {include:".dragged"})) {
								if(event_x < o_left + o_width/2) {
									node.parentNode.insertBefore(this._dragged_node, node);
								}
								else {
									var next = u.ns(node, {exclude: ".target,.dragged"});
									if(next) {
										node.parentNode.insertBefore(this._dragged_node, next);
									}
									else {
										node.parentNode.appendChild(this._dragged_node);
									}
								}
							}
							this._recalculateRelativeShadowOffset();
							break;
						}
					}
					else {
						var o_top, o_height;
						if(this._allow_nesting) {
							o_top = u.absY(node) - node._extra_height_top;
							o_height = node._top_node_height + node._extra_height_top + node._extra_height_bottom;
						}
						else {
							o_top = u.absY(node);
							o_height = node._top_node_height;
						}
					 	if(event_y >= o_top && event_y <= o_top + o_height) {
							if(node !== this._dragged_node && !u.pn(node, {include:".dragged"})) {
								if(this._allow_nesting) {
									if(event_y < o_top + (o_height / 3) && (!node.sub_target || !node.sub_target.childNodes.length || this._dragged_node.current_yps < 0)) {
										node.parentNode.insertBefore(this._dragged_node, node);
									}
									else if(event_y > o_top + ((o_height / 3) * 2)) {
										var next = u.ns(node, {exclude:".target,.dragged"});
										if(next) {
											node.parentNode.insertBefore(this._dragged_node, next);
										}
										else {
											node.parentNode.appendChild(this._dragged_node);
										}
									}
									else {
										if(!node.sub_target) {
											node.sub_target = u.ae(node, "ul", {"class":this._target_selector.replace(/([a-z]*.?)/, "").replace(/\./g, " ")});
											this.target_nodes.push(node.sub_target);
										}
										node.sub_target.insertBefore(this._dragged_node, node.sub_target.firstChild);
									}
								}
								else {
									if(event_y < o_top + o_height/2) {
										node.parentNode.insertBefore(this._dragged_node, node);
									}
									else {
										var next = u.ns(node);
										if(next) {
											node.parentNode.insertBefore(this._dragged_node, next);
										}
										else {
											node.parentNode.appendChild(this._dragged_node);
										}
									}
								}
								this._recalculateRelativeShadowOffset();
								break;
							}
							else {
								break;
							}
						}
					}
				}
			}
		}
		scope.resetSortableEvents = function(node) {
			u.e.removeMoveEvent(node, this._sortablePick);
			u.e.removeEndEvent(node, this._cancelSortablePick);
			u.e.removeOverEvent(node, this._sortableOver);
			if(node._event_move_id) {
				u.e.removeWindowMoveEvent(node, node._event_move_id);
				delete node._event_move_id;
			}
			if(node._event_end_id) {
				u.e.removeWindowEndEvent(node, node._event_end_id);
				delete node._event_end_id;
			}
			u.e.removeOutEvent(node.drag, this._sortableOut);
			this.resetSortableOutEvents(node);
		}
		scope.resetSortableOutEvents = function(node) {
			if(node._event_drop_out_id) {
				u.e.removeEvent(document, "mousemove", document["_DroppedOutMove" + node._event_drop_out_id]);
				u.e.removeEvent(node, "mouseover", document["_DroppedOutOver" + node._event_drop_out_id]);
				u.e.removeEvent(document, "mouseup", document["_DroppedOutEnd" + node._event_drop_out_id]);
				delete document["_DroppedOutMove" + node._event_drop_out_id];
				delete document["_DroppedOutOver" + node._event_drop_out_id];
				delete document["_DroppedOutEnd" + node._event_drop_out_id];
				delete document["_DroppedOutNode" + node._event_drop_out_id];
				delete node._event_drop_out_id;
			}
		}
		scope.resetNestedSortableEvents = function(node) {
			while(node && node != this) {
				if(node.drag) {
					this.resetSortableEvents(node);
				}
				node = node.parentNode;
			}
		}
		scope.getNodeOrder = function(_options) {
			var class_var = "item_id";
			if(obj(_options)) {
				var _argument;
				for(_argument in _options) {
					switch(_argument) {
						case "class_var"			: class_var 		= _options[_argument]; break;
					}
				}
			}
			this.updateDraggables();
			var order = [];
			var i, node, id;
			for(i = 0; i < this.draggable_nodes.length; i++) {
				node = this.draggable_nodes[i];
				id = u.cv(node, class_var);
				if(id) {
					order.push(id);
				}
				else {
					order.push(node);
				}
			}
			return order;
		}
		scope.getNodeRelations = function(_options) {
			var class_var = "item_id";
			if(obj(_options)) {
				var _argument;
				for(_argument in _options) {
					switch(_argument) {
						case "class_var"			: class_var 		= _options[_argument]; break;
					}
				}
			}
			this.updateDraggables();
			var structure = [];
			var i, node, id, relation, position;
			for(i = 0; i < this.draggable_nodes.length; i++) {
				node = this.draggable_nodes[i];
				id = u.cv(node, class_var);
				relation = this.getNodeRelation(node);
				position = this.getNodePositionInList(node);
				if(id) {
					structure.push({"id": id, "relation": relation, "position": position});
				}
				else {
					structure.push({"node": node, "relation": relation, "position": position});
				}
			}
			return structure;
		}
		scope.getNodePositionInList = function(node) {
			var pos = 1;
			var test_node = node;
			while(u.ps(test_node)) {
				test_node = u.ps(test_node);
				pos++;
			}
			return pos;
		}
		scope.getNodeRelation = function(node) {
			var relation = 0;
			var relation_node = u.pn(node, {"include":(this._draggable_selector ? this._draggable_selector : "li")});
			if(u.inNodeList(relation_node, this.draggable_nodes)) {
				var id = u.cv(relation_node, "item_id");
				if(id) {
					relation = id;
				}
				else {
					relation = relation_node;
				}
			}
			return relation;
		}
		scope.detectSortableLayout = function() {
			var i, target;
			for(i = 0; i < this.target_nodes.length; i++) {
				target = this.target_nodes[i];
					if((target._n_top || target._n_bottom) && (u.cn(target).length > 1 || target._n_display != "block")) {
						target._layout = "horizontal";
					}
					else if(target._n_left || target._n_right) {
						target._layout = "vertical";
					}
					else {
						target._layout = "multiline";
					}
			}
		}
		scope.updateDraggables = function() {
			var i, target, draggable_node;
			if(this.draggable_nodes && this.draggable_nodes.length) {
				for(i = 0; i < this.draggable_nodes.length; i++) {
					draggable_node = this.draggable_nodes[i];
					if(draggable_node && draggable_node.drag) {
						this.resetSortableEvents(draggable_node);
						u.e.removeStartEvent(draggable_node.drag, this._sortableInputStart);
						u.e.removeOverEvent(draggable_node, this._sortableOver);
						delete draggable_node.drag;
						delete draggable_node.sub_target;
						delete draggable_node.draggable_node;
					}
				}
			}
			delete scope.draggable_nodes;
			if(this._draggable_selector) {
				this.draggable_nodes = Array.prototype.slice.call(u.qsa(this._draggable_selector, this));
			}
			else {
				if(this.nodeName.toLowerCase() === "ul") {
					this.draggable_nodes = u.cn(this, {include:"li"});
				}
				else {
					this.draggable_nodes = [];
					for(i = 0; i < this.target_nodes.length; i++) {
						target = this.target_nodes[i];
						this.draggable_nodes = this.draggable_nodes.concat(u.cn(target, {include:"li"}));
					}
				}
			}
			for(i = 0; i < this.draggable_nodes.length; i++) {
				draggable_node = this.draggable_nodes[i];
				draggable_node.scope = this;
				draggable_node.drag = u.qs(".drag", draggable_node);
				if(!draggable_node.drag) {
					draggable_node.drag = draggable_node;
				}
				draggable_node.drag.draggable_node = draggable_node;
				draggable_node.draggable_node = draggable_node;
				var _top = draggable_node.offsetTop;
				var _height = draggable_node.offsetHeight;
				var _left = draggable_node.offsetLeft;
				var _width = draggable_node.offsetWidth;
				var _display = u.gcs(draggable_node, "display");
				draggable_node.parentNode._n_top = draggable_node.parentNode._n_top === undefined ? _top : (draggable_node.parentNode._n_top == _top ? draggable_node.parentNode._n_top : false);
				draggable_node.parentNode._n_left = draggable_node.parentNode._n_left === undefined ? _left : (draggable_node.parentNode._n_left == _left ? draggable_node.parentNode._n_left : false);
				draggable_node.parentNode._n_bottom = draggable_node.parentNode._n_bottom === undefined ? _top + _height : (draggable_node.parentNode._n_bottom == _top + _height ? draggable_node.parentNode._n_bottom : false);
				draggable_node.parentNode._n_right = draggable_node.parentNode._n_right === undefined ? _left + _width : (draggable_node.parentNode._n_right == _left + _width ? draggable_node.parentNode._n_right : false);
				draggable_node.parentNode._n_display = draggable_node.parentNode._n_display === undefined ? _display : (draggable_node.parentNode._n_display == _display ? draggable_node.parentNode._n_display : false);
				if(this._allow_nesting) {
					draggable_node.sub_target = u.qs(this._target_selector, draggable_node);
					if(draggable_node.sub_target) {
						var _position = u.gcs(draggable_node, "position");
						var node_height = _height - draggable_node.sub_target.offsetHeight;
						if(_position !== "static") {
							draggable_node._top_node_height = node_height - (node_height - draggable_node.sub_target.offsetTop);
						}
						else {
							draggable_node._top_node_height = node_height - (node_height - (draggable_node.sub_target.offsetTop - _top));
						}
					}
					else {
						draggable_node._top_node_height = _height;
					}
					var _margin_top = parseInt(u.gcs(draggable_node, "margin-top"));
					var _margin_bottom = parseInt(u.gcs(draggable_node, "margin-bottom"));
					var _box_sizing = u.gcs(draggable_node, "box-sizing");
					if(_box_sizing == "content-box") {
						var _border_top_width = parseInt(u.gcs(draggable_node, "border-top-width"));
						var _border_bottom_width = parseInt(u.gcs(draggable_node, "border-bottom-width"));
						draggable_node._extra_height_top = _margin_top + _border_top_width;
						draggable_node._extra_height_bottom = _margin_bottom + _border_bottom_width;
					}
					else {
						draggable_node._extra_height_top = _start_margin_top;
						draggable_node._extra_height_bottom = _start_margin_bottom;
					}
				}
				else {
					draggable_node._top_node_height = _height;
				}
				u.e.addStartEvent(draggable_node.drag, this._sortableInputStart);
			}
		}
		scope.updateTargets = function() {
			if(this._target_selector) {
				this.target_nodes = Array.prototype.slice.call(u.qsa(this._target_selector, this));
				if(u.elementMatches(this, this._target_selector)) {
					this.target_nodes.unshift(this);
				}
			}
			else {
				if(this.nodeName.toLowerCase() === "ul") {
					this.target_nodes = [this];
				}
				else {
					var i, target, target_nodes, parent_ul;
					this.target_nodes = [];
					target_nodes = u.qsa("ul", this);
					for(i = 0; i < target_nodes.length; i++) {
						target = target_nodes[i];
						if(this._allow_nesting) {
							this.target_nodes.push(target);
						}
						else {
							parent_ul = u.pn(target, {include:"ul"});
							if(!parent_ul || !u.contains(this, parent_ul)) {
								this.target_nodes.push(target);
							}
						}
					}
				}
			}
		}
	}
	scope.updateTargets();
	scope.updateDraggables();
	scope.detectSortableLayout();
	if(!scope.draggable_nodes.length || !scope.target_nodes.length) {
		return;
	}
}
Util.cutString = function(string, length) {
	var matches, match, i;
	if(string.length <= length) {
		return string;
	}
	else {
		length = length-3;
	}
	matches = string.match(/\&[\w\d]+\;/g);
	if(matches) {
		for(i = 0; i < matches.length; i++){
			match = matches[i];
			if(string.indexOf(match) < length){
				length += match.length-1;
			}
		}
	}
	return string.substring(0, length) + (string.length > length ? "..." : "");
}
Util.prefix = function(string, length, prefix) {
	string = string.toString();
	prefix = prefix ? prefix : "0";
	while(string.length < length) {
		string = prefix + string;
	}
	return string;
}
Util.randomString = function(length) {
	var key = "", i;
	length = length ? length : 8;
	var pattern = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ".split('');
	for(i = 0; i < length; i++) {
		key += pattern[u.random(0,35)];
	}
	return key;
}
Util.uuid = function() {
	var chars = '0123456789abcdef'.split('');
	var uuid = [], rnd = Math.random, r, i;
	uuid[8] = uuid[13] = uuid[18] = uuid[23] = '-';
	uuid[14] = '4';
	for(i = 0; i < 36; i++) {
		if(!uuid[i]) {
			r = 0 | rnd()*16;
			uuid[i] = chars[(i == 19) ? (r & 0x3) | 0x8 : r & 0xf];
		}
 	}
	return uuid.join('');
}
Util.stringOr = u.eitherOr = function(value, replacement) {
	if(value !== undefined && value !== null) {
		return value;
	}
	else {
		return replacement ? replacement : "";
	}	
}
Util.getMatches = function(string, regex) {
	var match, matches = [];
	while(match = regex.exec(string)) {
		matches.push(match[1]);
	}
	return matches;
}
Util.upperCaseFirst = u.ucfirst = function(string) {
	return string.replace(/^(.){1}/, function($1) {return $1.toUpperCase()});
}
Util.lowerCaseFirst = u.lcfirst = function(string) {
	return string.replace(/^(.){1}/, function($1) {return $1.toLowerCase()});
}
Util.normalize = function(string) {
	var table = {
		'À':'A',  'à':'a',
		'Á':'A',  'á':'a',
		'Â':'A',  'â':'a',
		'Ã':'A',  'ã':'a',
		'Ä':'A',  'ä':'a',
		'Å':'Aa', 'å':'aa',
		'Æ':'Ae', 'æ':'ae',
		'Ç':'C',  'ç':'c',
		'Č':'C',  'ć':'c',
		'Ć':'C',  'č':'c',
		'Đ':'D',  'đ':'d',  'ð':'d',
		'È':'E',  'è':'e',
		'É':'E',  'é':'e',
		'Ê':'E',  'ê':'e',
		'Ë':'E',  'ë':'e',
		'Ģ':'G',  'ģ':'g',
		'Ğ':'G',  'ğ':'g',
		'Ì':'I',  'ì':'i',
		'Í':'I',  'í':'i',
		'Î':'I',  'î':'i',
		'Ï':'I',  'ï':'i',
		'Ī':'I',  'ī':'i',
		'Ķ':'K',  'ķ':'k',
		'Ļ':'L',  'ļ':'l',
		'Ñ':'N',  'ñ':'n',
		'Ņ':'N',  'ņ':'n',
		'Ò':'O',  'ò':'o',
		'Ó':'O',  'ó':'o',
		'Ô':'O',  'ô':'o',
		'Õ':'O',  'õ':'o',
		'Ö':'O',  'ö':'o',
		'Ō':'O',  'ō':'o',
		'Ø':'Oe', 'ø':'oe',
		'Ŕ':'R',  'ŕ':'r',
		'Š':'S',  'š':'s',
		'Ş':'S',  'ş':'s',
		'Ṩ':'S',  'ṩ':'s',
		'Ù':'U',  'ù':'u',
		'Ú':'U',  'ú':'u',
		'Û':'U',  'û':'u',
		'Ü':'U',  'ü':'u',
		'Ū':'U',  'ū':'u',
		'Ų':'U',  'ų':'u',
		'Ŭ':'U',  'ŭ':'u',
		'Ý':'Y',  'ý':'y',
		'Ÿ':'Y',  'ÿ':'y',
		'Ž':'Z',  'ž':'z',
		'Þ':'B',  'þ':'b',
		'ß':'Ss',
		'@':' at ',
		'&':'and',
		'%':' percent',
		'\\$':'USD',
		'¥':'JPY',
		'€':'EUR',
		'£':'GBP',
		'™':'trademark',
		'©':'copyright',
		'§':'s',
		'\\*':'x',
		'×':'x'
	}
	var char, regex;
	for(char in table) {
		regex = new RegExp(char, "g");
		string = string.replace(regex, table[char]);
	}
	return string;
}
Util.superNormalize = function(string) {
	string = u.normalize(string);
	string = string.toLowerCase();
	string = u.stripTags(string);
	string = string.replace(/[^a-z0-9\_]/g, '-');
	string = string.replace(/-+/g, '-');
	string = string.replace(/^-|-$/g, '');
	return string;
}
Util.stripTags = function(string) {
	var node = document.createElement("div");
	node.innerHTML = string;
	return u.text(node);
}
Util.pluralize = function(count, singular, plural) {
	if(count != 1) {
		return count + " " + plural;
	}
	return count + " " + singular;
}
Util.isStringJSON = function(string) {
	if(string.trim().substr(0, 1).match(/[\{\[]/i) && string.trim().substr(-1, 1).match(/[\}\]]/i)) {
		try {
			var test = JSON.parse(string);
			if(obj(test)) {
				test.isJSON = true;
				return test;
			}
		}
		catch(exception) {
			console.log(exception)
		}
	}
	return false;
}
Util.isStringHTML = function(string) {
	if(string.trim().substr(0, 1).match(/[\<]/i) && string.trim().substr(-1, 1).match(/[\>]/i)) {
		try {
			var test = document.createElement("div");
			test.innerHTML = string;
			if(test.childNodes.length) {
				var body_class = string.match(/<body class="([a-z0-9A-Z_: ]+)"/);
				test.body_class = body_class ? body_class[1] : "";
				var head_title = string.match(/<title>([^$]+)<\/title>/);
				test.head_title = head_title ? head_title[1] : "";
				test.isHTML = true;
				return test;
			}
		}
		catch(exception) {}
	}
	return false;
}
Util.svg = function(svg_object) {
	var svg, shape, svg_shape;
	if(svg_object.name && u._svg_cache && u._svg_cache[svg_object.name]) {
		svg = u._svg_cache[svg_object.name].cloneNode(true);
	}
	if(!svg) {
		svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
		for(shape in svg_object.shapes) {
			Util.svgShape(svg, svg_object.shapes[shape]);
		}
		if(svg_object.name) {
			if(!u._svg_cache) {
				u._svg_cache = {};
			}
			u._svg_cache[svg_object.name] = svg.cloneNode(true);
		}
	}
	if(svg_object.title) {
		svg.setAttributeNS(null, "title", svg_object.title);
	}
	if(svg_object["class"]) {
		svg.setAttributeNS(null, "class", svg_object["class"]);
	}
	if(svg_object.width) {
		svg.setAttributeNS(null, "width", svg_object.width);
	}
	if(svg_object.height) {
		svg.setAttributeNS(null, "height", svg_object.height);
	}
	if(svg_object.id) {
		svg.setAttributeNS(null, "id", svg_object.id);
	}
	if(svg_object.node) {
		svg.node = svg_object.node;
	}
	if(svg_object.node) {
		svg_object.node.appendChild(svg);
	}
	return svg;
}
Util.svgShape = function(svg, svg_object) {
	svg_shape = document.createElementNS("http://www.w3.org/2000/svg", svg_object["type"]);
	svg_object["type"] = null;
	delete svg_object["type"];
	for(detail in svg_object) {
		svg_shape.setAttributeNS(null, detail, svg_object[detail]);
	}
	return svg.appendChild(svg_shape);
}
Util.browser = function(model, version) {
	var current_version = false;
	if(model.match(/\bedge\b/i)) {
		if(navigator.userAgent.match(/Windows[^$]+Gecko[^$]+Edge\/(\d+.\d)/i)) {
			current_version = navigator.userAgent.match(/Edge\/(\d+)/i)[1];
		}
	}
	if(model.match(/\bexplorer\b|\bie\b/i)) {
		if(window.ActiveXObject && navigator.userAgent.match(/MSIE (\d+.\d)/i)) {
			current_version = navigator.userAgent.match(/MSIE (\d+.\d)/i)[1];
		}
		else if(navigator.userAgent.match(/Trident\/[\d+]\.\d[^$]+rv:(\d+.\d)/i)) {
			current_version = navigator.userAgent.match(/Trident\/[\d+]\.\d[^$]+rv:(\d+.\d)/i)[1];
		}
	}
	if(model.match(/\bfirefox\b|\bgecko\b/i) && !u.browser("ie,edge")) {
		if(navigator.userAgent.match(/Firefox\/(\d+\.\d+)/i)) {
			current_version = navigator.userAgent.match(/Firefox\/(\d+\.\d+)/i)[1];
		}
	}
	if(model.match(/\bwebkit\b/i)) {
		if(navigator.userAgent.match(/WebKit/i) && !u.browser("ie,edge")) {
			current_version = navigator.userAgent.match(/AppleWebKit\/(\d+.\d)/i)[1];
		}
	}
	if(model.match(/\bchrome\b/i)) {
		if(window.chrome && !u.browser("ie,edge")) {
			current_version = navigator.userAgent.match(/Chrome\/(\d+)(.\d)/i)[1];
		}
	}
	if(model.match(/\bsafari\b/i)) {
		u.bug(navigator.userAgent);
		if(!window.chrome && navigator.userAgent.match(/WebKit[^$]+Version\/(\d+)(.\d)/i) && !u.browser("ie,edge")) {
			current_version = navigator.userAgent.match(/Version\/(\d+)(.\d)/i)[1];
		}
	}
	if(model.match(/\bopera\b/i)) {
		if(window.opera) {
			if(navigator.userAgent.match(/Version\//)) {
				current_version = navigator.userAgent.match(/Version\/(\d+)(.\d)/i)[1];
			}
			else {
				current_version = navigator.userAgent.match(/Opera[\/ ]{1}(\d+)(.\d)/i)[1];
			}
		}
	}
	if(current_version) {
		if(!version) {
			return current_version;
		}
		else {
			if(!isNaN(version)) {
				return current_version == version;
			}
			else {
				return eval(current_version + version);
			}
		}
	}
	else {
		return false;
	}
}
Util.segment = function(segment) {
	if(!u.current_segment) {
		var scripts = document.getElementsByTagName("script");
		var script, i, src;
		for(i = 0; i < scripts.length; i++) {
			script = scripts[i];
			seg_src = script.src.match(/\/seg_([a-z_]+)/);
			if(seg_src) {
				u.current_segment = seg_src[1];
			}
		}
	}
	if(segment) {
		return segment == u.current_segment;
	}
	return u.current_segment;
}
Util.system = function(os, version) {
	var current_version = false;
	if(os.match(/\bwindows\b/i)) {
		if(navigator.userAgent.match(/(Windows NT )(\d+.\d)/i)) {
			current_version = navigator.userAgent.match(/(Windows NT )(\d+.\d)/i)[2];
		}
	}
	else if(os.match(/\bmac\b/i)) {
		if(navigator.userAgent.match(/(Macintosh; Intel Mac OS X )(\d+[._]{1}\d)/i)) {
			current_version = navigator.userAgent.match(/(Macintosh; Intel Mac OS X )(\d+[._]{1}\d)/i)[2].replace("_", ".");
		}
	}
	else if(os.match(/\blinux\b/i)) {
		if(navigator.userAgent.match(/linux|x11/i) && !navigator.userAgent.match(/android/i)) {
			current_version = true;
		}
	}
	else if(os.match(/\bios\b/i)) {
		if(navigator.userAgent.match(/(OS )(\d+[._]{1}\d+[._\d]*)( like Mac OS X)/i)) {
			current_version = navigator.userAgent.match(/(OS )(\d+[._]{1}\d+[._\d]*)( like Mac OS X)/i)[2].replace(/_/g, ".");
		}
	}
	else if(os.match(/\bandroid\b/i)) {
		if(navigator.userAgent.match(/Android[ ._]?(\d+.\d)/i)) {
			current_version = navigator.userAgent.match(/Android[ ._]?(\d+.\d)/i)[1];
		}
	}
	else if(os.match(/\bwinphone\b/i)) {
		if(navigator.userAgent.match(/Windows[ ._]?Phone[ ._]?(\d+.\d)/i)) {
			current_version = navigator.userAgent.match(/Windows[ ._]?Phone[ ._]?(\d+.\d)/i)[1];
		}
	}
	if(current_version) {
		if(!version) {
			return current_version;
		}
		else {
			if(!isNaN(version)) {
				return current_version == version;
			}
			else {
				return eval(current_version + version);
			}
		}
	}
	else {
		return false;
	}
}
Util.support = function(property) {
	if(document.documentElement) {
		var style_property = u.lcfirst(property.replace(/^(-(moz|webkit|ms|o)-|(Moz|webkit|Webkit|ms|O))/, "").replace(/(-\w)/g, function(word){return word.replace(/-/, "").toUpperCase()}));
		if(style_property in document.documentElement.style) {
			return true;
		}
		else if(u.vendorPrefix() && (u.vendorPrefix()+u.ucfirst(style_property)) in document.documentElement.style) {
			return true;
		}
	}
	return false;
}
Util.vendor_properties = {};
Util.vendorProperty = function(property) {
	if(!Util.vendor_properties[property]) {
		Util.vendor_properties[property] = property.replace(/(-\w)/g, function(word){return word.replace(/-/, "").toUpperCase()});
		if(document.documentElement) {
			var style_property = u.lcfirst(property.replace(/^(-(moz|webkit|ms|o)-|(Moz|webkit|Webkit|ms|O))/, "").replace(/(-\w)/g, function(word){return word.replace(/-/, "").toUpperCase()}));
			if(style_property in document.documentElement.style) {
				Util.vendor_properties[property] = style_property;
			}
			else if(u.vendorPrefix() && (u.vendorPrefix()+u.ucfirst(style_property)) in document.documentElement.style) {
				Util.vendor_properties[property] = u.vendorPrefix()+u.ucfirst(style_property);
			}
		}
	}
	return Util.vendor_properties[property];
}
Util.vendor_prefix = false;
Util.vendorPrefix = function() {
	if(Util.vendor_prefix === false) {
		Util.vendor_prefix = "";
		if(document.documentElement && fun(window.getComputedStyle)) {
			var styles = window.getComputedStyle(document.documentElement, "");
			if(styles.length) {
				var i, style, match;
				for(i = 0; i < styles.length; i++) {
					style = styles[i];
					match = style.match(/^-(moz|webkit|ms)-/);
					if(match) {
						Util.vendor_prefix = match[1];
						if(Util.vendor_prefix == "moz") {
							Util.vendor_prefix = "Moz";
						}
						break;
					}
				}
			}
			else {
				var x, match;
				for(x in styles) {
					match = x.match(/^(Moz|webkit|ms|OLink)/);
					if(match) {
						Util.vendor_prefix = match[1];
						if(Util.vendor_prefix === "OLink") {
							Util.vendor_prefix = "O";
						}
						break;
					}
				}
			}
		}
	}
	return Util.vendor_prefix;
}
u.textscaler = function(node, _settings) {
	if(typeof(_settings) != "object") {
		_settings = {
			"*":{
				"unit":"rem",
				"min_size":1,
				"min_width":200,
				"min_height":200,
				"max_size":40,
				"max_width":3000,
				"max_height":2000
			}
		};
	}
	node.text_key = u.randomString(8);
	u.ac(node, node.text_key);
	node.text_settings = JSON.parse(JSON.stringify(_settings));
	node.scaleText = function() {
		var tag;
		for(tag in this.text_settings) {
			var settings = this.text_settings[tag];
			var width_wins = false;
			var height_wins = false;
			if(settings.width_factor && settings.height_factor) {
				if(window._man_text._height - settings.min_height < window._man_text._width - settings.min_width) {
					height_wins = true;
				}
				else {
					width_wins = true;
				}
			}
			if(settings.width_factor && !height_wins) {
				if(settings.min_width <= window._man_text._width && settings.max_width >= window._man_text._width) {
					var font_size = settings.min_size + (settings.size_factor * (window._man_text._width - settings.min_width) / settings.width_factor);
					settings.css_rule.style.setProperty("font-size", font_size + settings.unit, "important");
				}
				else if(settings.max_width < window._man_text._width) {
					settings.css_rule.style.setProperty("font-size", settings.max_size + settings.unit, "important");
				}
				else if(settings.min_width > window._man_text._width) {
					settings.css_rule.style.setProperty("font-size", settings.min_size + settings.unit, "important");
				}
			}
			else if(settings.height_factor) {
				if(settings.min_height <= window._man_text._height && settings.max_height >= window._man_text._height) {
					var font_size = settings.min_size + (settings.size_factor * (window._man_text._height - settings.min_height) / settings.height_factor);
					settings.css_rule.style.setProperty("font-size", font_size + settings.unit, "important");
				}
				else if(settings.max_height < window._man_text._height) {
					settings.css_rule.style.setProperty("font-size", settings.max_size + settings.unit, "important");
				}
				else if(settings.min_height > window._man_text._height) {
					settings.css_rule.style.setProperty("font-size", settings.min_size + settings.unit, "important");
				}
			}
		}
	}
	node.cancelTextScaling = function() {
		u.e.removeEvent(window, "resize", window._man_text.scale);
	}
	if(!window._man_text) {
		var man_text = {};
		man_text.nodes = [];
		var style_tag = document.createElement("style");
		style_tag.setAttribute("media", "all")
		style_tag.setAttribute("type", "text/css")
		man_text.style_tag = u.ae(document.head, style_tag);
		man_text.style_tag.appendChild(document.createTextNode(""))
		window._man_text = man_text;
		window._man_text._width = u.browserW();
		window._man_text._height = u.browserH();
		window._man_text.scale = function() {
			var _width = u.browserW();
			var _height = u.browserH();
			window._man_text._width = u.browserW();
			window._man_text._height = u.browserH();
			var i, node;
			for(i = 0; i < window._man_text.nodes.length; i++) {
				node = window._man_text.nodes[i];
				if(node.parentNode) { 
					node.scaleText();
				}
				else {
					window._man_text.nodes.splice(window._man_text.nodes.indexOf(node), 1);
					if(!window._man_text.nodes.length) {
						u.e.removeEvent(window, "resize", window._man_text.scale);
						window._man_text = false;
						break;
					}
				}
			}
		}
		u.e.addEvent(window, "resize", window._man_text.scale);
		window._man_text.precalculate = function() {
			var i, node, tag;
			for(i = 0; i < window._man_text.nodes.length; i++) {
				node = window._man_text.nodes[i];
				if(node.parentNode) { 
					var settings = node.text_settings;
					for(tag in settings) {
						if(settings[tag].max_width && settings[tag].min_width) {
							settings[tag].width_factor = settings[tag].max_width-settings[tag].min_width;
						}
						else if(node._man_text.max_width && node._man_text.min_width) {
							settings[tag].max_width = node._man_text.max_width;
							settings[tag].min_width = node._man_text.min_width;
							settings[tag].width_factor = node._man_text.max_width-node._man_text.min_width;
						}
						else {
							settings[tag].width_factor = false;
						}
						if(settings[tag].max_height && settings[tag].min_height) {
							settings[tag].height_factor = settings[tag].max_height-settings[tag].min_height;
						}
						else if(node._man_text.max_height && node._man_text.min_height) {
							settings[tag].max_height = node._man_text.max_height;
							settings[tag].min_height = node._man_text.min_height;
							settings[tag].height_factor = node._man_text.max_height-node._man_text.min_height;
						}
						else {
							settings[tag].height_factor = false;
						}
						settings[tag].size_factor = settings[tag].max_size-settings[tag].min_size;
						if(!settings[tag].unit) {
							settings[tag].unit = node._man_text.unit;
						}
					}
				}
			}
		}
	}
	var tag;
	node._man_text = {};
	for(tag in node.text_settings) {
		if(tag == "min_height" || tag == "max_height" || tag == "min_width" || tag == "max_width" || tag == "unit" || tag == "ref") {
			node._man_text[tag] = node.text_settings[tag];
			node.text_settings[tag] = null;
			delete node.text_settings[tag];
		}
		else {
			selector = "."+node.text_key + ' ' + tag + ' ';
			node.css_rules_index = window._man_text.style_tag.sheet.insertRule(selector+'{}', 0);
			node.text_settings[tag].css_rule = window._man_text.style_tag.sheet.cssRules[0];
		}
	}
	window._man_text.nodes.push(node);
	window._man_text.precalculate();
	node.scaleText();
}
Util.Timer = u.t = new function() {
	this._timers = new Array();
	this.setTimer = function(node, action, timeout, param) {
		var id = this._timers.length;
		param = param ? param : {"target":node, "type":"timeout"};
		this._timers[id] = {"_a":action, "_n":node, "_p":param, "_t":setTimeout("u.t._executeTimer("+id+")", timeout)};
		return id;
	}
	this.resetTimer = function(id) {
		if(this._timers[id]) {
			clearTimeout(this._timers[id]._t);
			this._timers[id] = false;
		}
	}
	this._executeTimer = function(id) {
		var timer = this._timers[id];
		this._timers[id] = false;
		var node = timer._n;
		if(fun(timer._a)) {
			node._timer_action = timer._a;
			node._timer_action(timer._p);
			node._timer_action = null;
		}
		else if(fun(node[timer._a])) {
			node[timer._a](timer._p);
		}
	}
	this.setInterval = function(node, action, interval, param) {
		var id = this._timers.length;
		param = param ? param : {"target":node, "type":"timeout"};
		this._timers[id] = {"_a":action, "_n":node, "_p":param, "_i":setInterval("u.t._executeInterval("+id+")", interval)};
		return id;
	}
	this.resetInterval = function(id) {
		if(this._timers[id]) {
			clearInterval(this._timers[id]._i);
			this._timers[id] = false;
		}
	}
	this._executeInterval = function(id) {
		var node = this._timers[id]._n;
		if(fun(this._timers[id]._a)) {
			node._interval_action = this._timers[id]._a;
			node._interval_action(this._timers[id]._p);
			node._interval_action = null;
		}
		else if(fun(node[this._timers[id]._a])) {
			node[this._timers[id]._a](this._timers[id]._p);
		}
	}
	this.valid = function(id) {
		return this._timers[id] ? true : false;
	}
	this.resetAllTimers = function() {
		var i, t;
		for(i = 0; i < this._timers.length; i++) {
			if(this._timers[i] && this._timers[i]._t) {
				this.resetTimer(i);
			}
		}
	}
	this.resetAllIntervals = function() {
		var i, t;
		for(i = 0; i < this._timers.length; i++) {
			if(this._timers[i] && this._timers[i]._i) {
				this.resetInterval(i);
			}
		}
	}
}
u.txt = function(index) {
	if(!u.translations) {
	}
	if(index == "assign") {
		u.bug("USING RESERVED INDEX: assign");
		return "";
	}
	if(u.txt[index]) {
		return u.txt[index];
	}
	u.bug("MISSING TEXT: "+index);
	return "";
}
u.txt["assign"] = function(obj) {
	for(x in obj) {
		u.txt[x] = obj[x];
	}
}
Util.getVar = function(param, url) {
	var string = url ? url.split("#")[0] : location.search;
	var regexp = new RegExp("(?:^|\b|&|\\?)"+param.replace(/[\[\]\(\)]{1}/g, "\\$&")+"\=([^\&\b]+)");
	var match = string.match(regexp);
	if(match && match.length > 1) {
		return decodeURIComponent(match[1]);
	}
	else {
		return "";
	}
}
if(document.documentMode && document.documentMode <= 10 && document.documentMode >= 8) {
	Util.appendElement = u.ae = function(_parent, node_type, attributes) {
		try {
			var node = (obj(node_type)) ? node_type : (node_type == "svg" ? document.createElementNS("http://www.w3.org/2000/svg", node_type) : document.createElement(node_type));
			if(attributes) {
				var attribute;
				for(attribute in attributes) {
					if(!attribute.match(/^(value|html)$/)) {
						node.setAttribute(attribute, attributes[attribute]);
					}
				}
			}
			node = _parent.appendChild(node);
			if(attributes) {
				if(attributes["value"]) {
					node.value = attributes["value"];
				}
				if(attributes["html"]) {
					node.innerHTML = attributes["html"];
				}
			}
			return node;
		}
		catch(exception) {
			u.exception("u.ae (desktop_ie10)", arguments, exception);
		}
	}
	Util.insertElement = u.ie = function(_parent, node_type, attributes) {
		try {
			var node = (obj(node_type)) ? node_type : (node_type == "svg" ? document.createElementNS("http://www.w3.org/2000/svg", node_type) : document.createElement(node_type));
			if(attributes) {
				var attribute;
				for(attribute in attributes) {
					if(!attribute.match(/^(value|html)$/)) {
						node.setAttribute(attribute, attributes[attribute]);
					}
				}
			}
			node = _parent.insertBefore(node, _parent.firstChild);
			if(attributes) {
				if(attributes["value"]) {
					node.value = attributes["value"];
				}
				if(attributes["html"]) {
					node.innerHTML = attributes["html"];
				}
			}
			return node;
		}
		catch(exception) {
			u.exception("u.ie (desktop_ie10)", arguments, exception);
		}
	}
}
if(document.documentMode && document.documentMode <= 11 && document.documentMode >= 8) {
	Util.hasClass = u.hc = function(node, classname) {
		var regexp = new RegExp("(^|\\s)(" + classname + ")(\\s|$)");
		if(node instanceof SVGElement) {
			if(regexp.test(node.className.baseVal)) {
				return true;
			}
		}
		else {
			if(regexp.test(node.className)) {
				return true;
			}
		}
		return false;
	}
	Util.addClass = u.ac = function(node, classname, dom_update) {
		var classnames = classname.split(" ");
		while(classnames.length) {
			classname = classnames.shift();
			var regexp = new RegExp("(^|\\s)" + classname + "(\\s|$)");
			if(node instanceof SVGElement) {
				if(!regexp.test(node.className.baseVal)) {
					node.className.baseVal += node.className.baseVal ? " " + classname : classname;
				}
			}
			else {
				if(!regexp.test(node.className)) {
					node.className += node.className ? " " + classname : classname;
				}
			}
		}
		dom_update = (!dom_update) || (node.offsetTop);
		return node.className;
	}
	Util.removeClass = u.rc = function(node, classname, dom_update) {
		var regexp = new RegExp("(^|\\s)(" + classname + ")(?=[\\s]|$)", "g");
		if(node instanceof SVGElement) {
			node.className.baseVal = node.className.baseVal.replace(regexp, " ").trim().replace(/[\s]{2}/g, " ");
		}
		else {
			node.className = node.className.replace(regexp, " ").trim().replace(/[\s]{2}/g, " ");
		}
		dom_update = (!dom_update) || (node.offsetTop);
		return node.className;
	}
}


/*u-settings.js*/
u.gapi_key = "AIzaSyAVqnYpqFln-qAYsp5rkEGs84mrhmGQB_I";


/*u-basics.js*/
Util.Modules["collapseHeader"] = new function() {
	this.init = function(div) {
		u.ac(div, "togglable");
		div._toggle_header = u.qs("h2,h3,h4", div);
		if(div._toggle_header) {
			u.wc(div, "div", {"class":"togglable_content"});
			u.ie(div, div._toggle_header);
			div._toggle_header.div = div;
			u.e.click(div._toggle_header);
			div._toggle_header.clicked = function() {
				if(this.div._toggle_is_closed) {
					u.ac(this.div, "open");
					u.ass(this.div, {
						height: "auto"
					});
					this.div._toggle_is_closed = false;
					u.saveNodeCookie(this.div, "open", 1, {"ignore_classvars":true, "ignore_classnames":"open"});
					u.addCollapseArrow(this);
					if(typeof(this.div.headerExpanded) == "function") {
						this.div.headerExpanded();
					}
				}
				else {
					u.rc(this.div, "open");
					u.ass(this.div, {
						height: this.offsetHeight+"px"
					});
					this.div._toggle_is_closed = true;
					u.saveNodeCookie(this.div, "open", 0, {"ignore_classvars":true, "ignore_classnames":"open"});
					u.addExpandArrow(this);
					if(typeof(this.div.headerCollapsed) == "function") {
						this.div.headerCollapsed();
					}
				}
			}
			var state = u.getNodeCookie(div, "open", {"ignore_classvars":true, "ignore_classnames":"open"});
			if(state === 0 || (state === false && !u.hc(div, "open"))) {
				div._toggle_header.clicked();
			}
			else {
				u.addCollapseArrow(div._toggle_header);
				u.ac(div, "open");
				if(typeof(div.headerExpanded) == "function") {
					div.headerExpanded();
				}
			}
		}
	}
}
u.addExpandArrow = function(node) {
	if(node.collapsearrow) {
		node.collapsearrow.parentNode.removeChild(node.collapsearrow);
		delete node.collapsearrow;
	}
	node.expandarrow = u.svgIcons("expandarrow", node);
}
u.addCollapseArrow = function(node) {
	if(node.expandarrow) {
		node.expandarrow.parentNode.removeChild(node.expandarrow);
		delete node.expandarrow;
	}
	node.collapsearrow = u.svgIcons("collapsearrow", node);
}
u.defaultFilters = function(div) {
	div._filter = u.ie(div, "div", {"class":"filter"});
	div._filter.div = div;
	var i, node, j, text_node;
	for(i = 0; i < div.nodes.length; i++) {
		node = div.nodes[i];
		node._c = "";
		var text_nodes = u.qsa("h2,h3,h4,h5,p,ul.info,dl,li.tag", node);
		for(j = 0; j < text_nodes.length; j++) {
			text_node = text_nodes[j];
			node._c += u.text(text_node).toLowerCase() + ";"; 
		}
	}
	var tags = u.qsa("li.tag", div.list);
	if(tags) {
		var tag, li, used_tags = [];
		div._filter._tags = u.ie(div._filter, "ul", {"class":"tags"});
		for(i = 0; i < tags.length; i++) {
			node = tags[i];
			tag = u.text(node);
			if(used_tags.indexOf(tag) == -1) {
				used_tags.push(tag);
			}
		}
		used_tags.sort();
		for(i = 0; i < used_tags.length; i++) {
			tag = used_tags[i];
			li = u.ae(div._filter._tags, "li", {"html":tag});
			li.tag = tag.toLowerCase();
			li._filter = div._filter;
			u.e.click(li);
			li.clicked = function(event) {
				if(u.hc(this, "selected")) {
					this._filter.selected_tags.splice(this._filter.selected_tags.indexOf(this.tag), 1);
					u.rc(this, "selected");
				}
				else {
					this._filter.selected_tags.push(this.tag);
					u.ac(this, "selected");
				}
				this._filter.form.updated();
			}
		}
		div._filter.selected_tags = [];
	}
	div._filter.form = u.f.addForm(div._filter, {"name":"filter", "class":"labelstyle:inject"});
	u.f.addField(div._filter.form, {"name":"filter", "label":"Type to filter"});
	u.f.init(div._filter.form);
	div._filter.form.div = div;
	div._filter.input = div._filter.form.inputs["filter"];
	div._filter.form.updated = function() {
		u.t.resetTimer(this.t_filter);
		this.t_filter = u.t.setTimer(this.div._filter, "filterItems", 400);
		u.ac(this.div._filter, "filtering");
	}
	div._filter.checkTags = function(node) {
		if(this.selected_tags.length) {
			var regex = new RegExp("("+this.selected_tags.join(";|")+";)", "g");
			var match = node._c.match(regex);
			if(!match || match.length != this.selected_tags.length) {
				return false;
			}
		}
		return true;
	}
	div._filter.filterItems = function() {
		var i, node;
		var query = this.input.val().toLowerCase();
		if(this.current_filter != query+","+this.selected_tags.join(",")) {
			this.current_filter = query + "," + this.selected_tags.join(",");
			for(i = 0; i < this.div.nodes.length; i++) {
				node = this.div.nodes[i];
				if(node._c.match(query) && this.checkTags(node)) {
					node._hidden = false;
					u.rc(node, "hidden", false);
					u.as(node, "display", "block", false);
				}
				else {
					node._hidden = true;
					u.ac(node, "hidden", false);
					u.as(node, "display", "none", false);
				}
			}
		}
		u.rc(this, "filtering");
		if(typeof(this.div.filtered) == "function") {
			this.div.filtered();
		}
	}
}
u.defaultSortableList = function(list) {
	list.div.save_order_url = list.div.getAttribute("data-item-order");
	if(list.div.save_order_url && list.div.csrf_token) {
		for(i = 0; node = list.div.nodes[i]; i++) {
			u.ac(node, "draggable");
			if(!u.qs(".drag", node)) {
				u.ie(node, "div", {"class":"drag"});
			}
		}
		u.sortable(list, {"targets":".items", "draggables":".draggable"});
		list.picked = function() {}
		list.dropped = function() {
			var order = this.getNodeOrder();
			this.orderResponse = function(response) {
				page.notify(response);
			}
			u.request(this, this.div.save_order_url, {
				"callback":"orderResponse", 
				"method":"post", 
				"data":"csrf-token=" + this.div.csrf_token + "&order=" + order.join(",")
			});
		}
	}
	else {
		u.rc(list.div, "sortable");
	}
}
u.enableTagging = function(node) {
	u.e.click(node._bn_add);
	node._bn_add.clicked = function() {
		if(u.hc(this.node, "edittags")) {
			this.innerHTML = "+";
			u.rc(this.node, "edittags");
			this.node._tag_options.parentNode.removeChild(this.node._tag_options);
			delete this.node._tag_options;
		}
		else {
			this.innerHTML = "-";
			u.ac(this.node, "edittags");
			u.activateTagging(this.node);
		}
	}
}
u.activateTagging = function(node) {
	if(node._text) {
		node._tag_options = u.ae(node._text, "div", {"class":"tagoptions"});
	}
	else {
		node._tag_options = u.ae(node, "div", {"class":"tagoptions"});
	}
	node._tags_context = node._tags.getAttribute("data-context");
	node._tag_form = u.f.addForm(node._tag_options, {"action": node.data_div.add_tag_url});
	u.f.addField(node._tag_form, {"type":"hidden", "name":"csrf-token", "value":node.data_div.csrf_token});
	var fieldset = u.f.addFieldset(node._tag_form);
	u.f.addField(fieldset, {
		"name":"tags", 
		"value":"", 
		"id":"tag_input_"+node._item_id, 
		"label":"Tag", 
		"hint_message":"Type to filter existing tags or add a new tag", 
		"error_message":"Tag must conform to tag value: context:value", 
		"pattern":(node._tags_context ? "^("+node._tags_context.split(/,|;/).join("|")+")\:[^$]+" : "[^$]+\:[^$]+")
	});
	u.f.addAction(node._tag_form, {"class":"button primary", "value":"Add new tag"});
	u.f.init(node._tag_form);
	node._tag_form.node = node;
	node._tag_form.inputs["tags"].updated = function() {
		if(this._form.node._new_tags) {
			var tags = u.qsa(".tag", this.form.node._new_tags);
			var i, tag;
			for(i = 0; tag = tags[i]; i++) {
				if(u.text(tag).toLowerCase().match(this.val().toLowerCase())) {
					u.as(tag, "display", "inline-block");
				}
				else {
					u.as(tag, "display", "none");
				}
			}
		}
	}
	node._tag_form.submitted = function(iN) {
		this.response = function(response) {
			page.notify(response);
			if(response.cms_status == "success") {
				this.inputs["tags"].val("");
				this.inputs["tags"].updated();
				this.inputs["tags"].focus();
				var new_tag = response.cms_object;
				var i, tag_node;
				var new_tags = u.qsa("li", this.node._new_tags);
				for(i = 0; tag_node = new_tags[i]; i++) {
					if(tag_node._id == new_tag.tag_id) {
						u.ae(this.node._tags, tag_node);
						return;
					}
				}
				this.node.data_div.all_tags.push({
					"id":new_tag.tag_id, 
					"context":new_tag.context, 
					"value":new_tag.value
				});
				tag_node = u.ae(this.node._tags, "li", {"class":"tag "+new_tag.context});
				u.ae(tag_node, "span", {"class":"context", "html":new_tag.context});
				u.ae(tag_node, document.createTextNode(":"));
				u.ae(tag_node, "span", {"class":"value", "html":new_tag.value});
				tag_node._context = new_tag.context;
				tag_node._value = new_tag.value;
				tag_node._id = new_tag.tag_id;
				tag_node.node = this.node;
				u.activateTag(tag_node);
			}
		}
		u.request(this, this.action+"/"+this.node._item_id, {"method":"post", "data" : this.getData()});
	}
	node._tag_form.inputs["tags"].focus();
	node._new_tags = u.ae(node._tag_options, "ul", {"class":"tags"});
	var used_tags = {};
	var item_tags = u.qsa("li:not(.add)", node._tags);
	var i, tag_node, tag, context, value;
	for(i = 0; tag_node = item_tags[i]; i++) {
		tag_node._context = u.qs(".context", tag_node).innerHTML;
		tag_node._value = u.qs(".value", tag_node).innerHTML;
		if(!used_tags[tag_node._context]) {
			used_tags[tag_node._context] = {}
		}
		if(!used_tags[tag_node._context][tag_node._value]) {
			used_tags[tag_node._context][tag_node._value] = tag_node;
		}
	}
	for(tag in node.data_div.all_tags) {
		context = node.data_div.all_tags[tag].context;
		if(
			(!node._tags_context || (context.match(new RegExp("^(" + node._tags_context.split(/,|;/).join("|") + ")$"))))
		) {
			value = node.data_div.all_tags[tag].value.replace(/ & /, " &amp; ");
			if(used_tags && used_tags[context] && used_tags[context][value]) {
				tag_node = used_tags[context][value];
			}
			else {
				tag_node = u.ae(node._new_tags, "li", {"class":"tag"});
				u.ae(tag_node, "span", {"class":"context", "html":context});
				u.ae(tag_node, document.createTextNode(":"));
				u.ae(tag_node, "span", {"class":"value", "html":value});
				tag_node._context = context;
				tag_node._value = value;
			}
			tag_node._id = node.data_div.all_tags[tag].id;
			tag_node.node = node;
			u.activateTag(tag_node);
		}
	}
}
u.activateTag = function(tag_node) {
	u.e.click(tag_node);
	tag_node.clicked = function() {
		if(u.hc(this.node, "edittags")) {
			if(this.parentNode == this.node._tags) {
				this.response = function(response) {
					page.notify(response);
					if(response.cms_status == "success") {
						u.ae(this.node._new_tags, this);
					}
				}
				u.request(this, this.node.data_div.delete_tag_url+"/"+this.node._item_id+"/" + this._id, {"method":"post", "data":"csrf-token=" + this.node.data_div.csrf_token});
			}
			else {
				this.response = function(response) {
					page.notify(response);
					if(response.cms_status == "success") {
						u.ie(this.node._tags, this)
					}
				}
				u.request(this, this.node.data_div.add_tag_url+"/"+this.node._item_id, {"method":"post", "data":"tags="+this._id+"&csrf-token=" + this.node.data_div.csrf_token});
			}
		}
	}
}
u.defaultSelectable = function(div) {
	div.bn_all = u.ie(div.list, "li", {"class":"all"});
	div.bn_all._text = u.ae(div.bn_all, "span", {"html":"Select all"});
	div.bn_all._checkbox = u.ie(div.bn_all, "input", {"type":"checkbox"});
	div.bn_all.onclick = function(event) {u.e.kill(event);}
	div.bn_all.div = div;
	div.bn_all._checkbox.div = div;
	u.e.click(div.bn_all._checkbox);
	div.bn_all._checkbox.clicked = function(event) {
		var i, node;
		u.e.kill(event);
		var inputs = u.qsa("li.item:not(.hidden) input:checked", this.div.list);
		for(i = 0; i < this.div.nodes.length; i++) {
			node = this.div.nodes[i];
			if(inputs.length) {
				node._checkbox.checked = false;
			}
			else if(!node._hidden) {
				node._checkbox.checked = true;
			}
		}
		this.div.bn_range._from.value = "";
		this.div.bn_range._to.value = "";
		this.div.bn_all.updateState();
	}
	div.bn_all.updateState = function() {
		this.div.checked_inputs = u.qsa("li.item input:checked", this.div.list);
		this.div.visible_inputs = u.qsa("li.item:not(.hidden) input", this.div.list);
		if(this.div.checked_inputs.length && this.div.checked_inputs.length == this.div.visible_inputs.length) {
			this._text.innerHTML = "Deselect all";
			u.rc(this, "deselect");
			this._checkbox.checked = true;
		}
		else if(this.div.checked_inputs.length) {
			this._text.innerHTML = "Deselect all";
			u.ac(this, "deselect");
			this._checkbox.checked = true;
		}
		else {
			this._text.innerHTML = "Select all";
			u.rc(this, "deselect");
			this._checkbox.checked = false;
		}
		if(fun(this.div.selectionUpdated)) {
			this.div.selectionUpdated(this.div.checked_inputs);
		}
	}
	div.bn_range = u.ae(div.bn_all, "div", {class:"range"});
	div.bn_range._text = u.ae(div.bn_range, "span", {html:"Select range:"});
	div.bn_range._from = u.ae(div.bn_range, "input", {type:"text", name:"range_from", maxlength:4});
	div.bn_range._text = u.ae(div.bn_range, "span", {html:"to"});
	div.bn_range._to = u.ae(div.bn_range, "input", {type:"text", name:"range_to", maxlength:4});
	div.bn_range.div = div;
	div.bn_range._from.bn_range = div.bn_range;
	div.bn_range._to.bn_range = div.bn_range;
	div.bn_range._updated = function(event) {
		var key = event.key;
		if(key == "ArrowUp" && event.shiftKey) {
			u.e.kill(event);
			this.value = this.value > 0 ? Number(this.value)+10 : 10;
		}
		else if(key == "ArrowUp") {
			u.e.kill(event);
			this.value = this.value > 0 ? Number(this.value)+1 : 1;
		}
		else if(key == "ArrowDown" && event.shiftKey) {
			u.e.kill(event);
			this.value = this.value > 10 ? Number(this.value)-10 : 1;
		}
		else if(key == "ArrowDown") {
			u.e.kill(event);
			this.value = this.value > 1 ? Number(this.value)-1 : 1;
		}
		else if((parseInt(key) != key) && (key != "Backspace" && key != "Delete" && key != "Tab" && key != "ArrowLeft" && key != "ArrowRight" && !event.metaKey && !event.ctrlKey)) {
			u.e.kill(event);
		}
		var value = false;
		var to, from;
		if(parseInt(key) == key) {
			value = this.value.length < 4 ? this.value + key : this.value;
		}
		else if(key == "Backspace") {
			value = this.value.substring(0, this.value.length-1);
		}
		else if(key == "Delete") {
			value = this.value.substring(1);
		}
		else if(key == "ArrowUp" || key == "ArrowDown") {
			value = this.value;
		}
		if(value !== false) {
			value = Number(value);
			if(this.name == "range_from") {
				if(Number(this.bn_range._to.value) < value) {
					this.bn_range._to.value = value;
				}
				from = value;
				to = Number(this.bn_range._to.value);
			}
			else if(this.name == "range_to") {
				if(!this.bn_range._from.value) {
					this.bn_range._from.value = 1;
				}
				else if(Number(this.bn_range._from.value) > value) {
					this.bn_range._from.value = value;
				}
				to = value;
				from = Number(this.bn_range._from.value);
			}
			to = to-1;
			from = from-1;
			if(!isNaN(from && !isNaN(to))) {
				var inputs = u.qsa("li.item:not(.hidden) input", this.bn_range.div.list);
				var i, input;
				for(i = 0; i < inputs.length; i++) {
					input = inputs[i];
					if(i >= from && i <= to) {
						input.checked = true;
					}
					else {
						input.checked = false;
					}
				}
				this.bn_range.div.bn_all.updateState();
			}
		}
	}
	u.e.addEvent(div.bn_range._from, "keypress", div.bn_range._updated);
	u.e.addEvent(div.bn_range._to, "keypress", div.bn_range._updated);
	for(i = 0; i < div.nodes.length; i++) {
		node = div.nodes[i];
		node.ua_id = u.cv(node, "ua_id");
		node.div = div;
		node._checkbox = u.ie(node, "input", {"type":"checkbox"});
		node._checkbox.node = node;
		u.e.click(node._checkbox);
		node._checkbox.onclick = function(event) {u.e.kill(event);}
		node._checkbox.inputStarted = function(event) {
			u.e.kill(event);
			document.body.selection_div = this.node.div;
			if(this.checked) {
				this.checked = false;
				document.body._multideselection = true;
			}
			else {
				this.checked = true;
				document.body._multiselection = true;
			}
			document.body.onmouseup = function(event) {
				this.onmouseup = null;
				this._multiselection = false;
				this._multideselection = false;
				this.selection_div.bn_all.updateState();
				delete document.body.selection_div;
			}
		}
		node._checkbox.onmouseover = function() {
			if(document.body._multiselection) {
				this.checked = true;
			}
			else if(document.body._multideselection) {
				this.checked = false;
			}
		}
	}
}
u.svgIcons = function(icon, node) {
	switch(icon) {
		case "expandarrow" : return u.svg({
			"name":"expandarrow",
			"node":node,
			"class":"arrow",
			"width":17,
			"height":17,
			"shapes":[
				{
					"type": "line",
					"x1": 2,
					"y1": 2,
					"x2": 7,
					"y2": 9
				},
				{
					"type": "line",
					"x1": 6,
					"y1": 9,
					"x2": 11,
					"y2": 2
				}
			]
		});
		case "collapsearrow" : return u.svg({
			"name":"collapsearrow",
			"node":node,
			"class":"arrow",
			"width":17,
			"height":17,
			"shapes":[
				{
					"type": "line",
					"x1": 2,
					"y1": 9,
					"x2": 7,
					"y2": 2
				},
				{
					"type": "line",
					"x1": 6,
					"y1": 2,
					"x2": 11,
					"y2": 9
				}
			]
		});
		case "totoparrow" : return u.svg({
			"name":"totoparrow",
			"node":node,
			"class":"arrow",
			"width":30,
			"height":30,
			"shapes":[
				{
					"type": "line",
					"x1": 2,
					"y1": 21,
					"x2": 16,
					"y2": 2
				},
				{
					"type": "line",
					"x1": 14,
					"y1": 2,
					"x2": 28,
					"y2": 21
				}
			]
		});
	}
}


/*beta-u-form-onebuttonform.js*/
Util.Modules["oneButtonForm"] = new function() {
	this.init = function(node) {
		if(!node.childNodes.length) {
			var csrf_token = node.getAttribute("data-csrf-token");
			if(csrf_token) {
				if(node.nodeName.toLowerCase() === "form") {
					node._ob_form = node;
				}
				else {
					var form_action = node.getAttribute("data-form-action");
					var form_target = node.getAttribute("data-form-target");
					if(form_action) {
						var form_options = {"action":form_action, "class":"confirm_action_form"};
						if(form_target) {
							form_options["target"] = form_target;
						}
						node._ob_form = u.f.addForm(node, form_options);
					}
					else {
						u.bug("oneButtonForm missin information");
						return;
					}
				}
				u.ae(node._ob_form, "input", {"type":"hidden","name":"csrf-token", "value":csrf_token});
				var inputs = node.getAttribute("data-inputs");
				if(inputs) {
					inputs = JSON.parse(inputs);
					for(input_name in inputs) {
						u.ae(node._ob_form, "input", {"type":"hidden","name":input_name, "value":inputs[input_name]});
					}
				}
				var button_value = node.getAttribute("data-button-value");
				var button_name = node.getAttribute("data-button-name");
				var button_class = node.getAttribute("data-button-class");
				u.f.addAction(node._ob_form, {"value":button_value, "class":"button" + (button_class ? " "+button_class : ""), "name":u.stringOr(button_name, "save")});
			}
		}
		else {
			if(node.nodeName.toLowerCase() === "form") {
				node._ob_form = node;
			}
			else {
				node._ob_form = u.qs("form", node);
			}
		}
		if(node._ob_form) {
			u.f.init(node._ob_form);
			node._ob_form._ob_node = node;
			node._ob_form._ob_submit_button = u.qs("input[type=submit]", node._ob_form);
			if(u.objectValues(node._ob_form.actions).indexOf(node._ob_form._ob_submit_button) === -1) {
				u.f.initButton(node._ob_form, node._ob_form._ob_submit_button);
			}
			node._ob_form._ob_submit_button.org_value = node._ob_form._ob_submit_button.value;
			node._ob_form._ob_submit_button.confirm_value = node.getAttribute("data-confirm-value");
			node._ob_form._ob_submit_button.wait_value = node.getAttribute("data-wait-value");
			node._ob_form._ob_success_function = node.getAttribute("data-success-function");
			node._ob_form._ob_success_location = node.getAttribute("data-success-location");
			node._ob_form._ob_error_function = node.getAttribute("data-error-function");
			node._ob_form._ob_dom_submit = node.getAttribute("data-dom-submit");
			node._ob_form._ob_download = node.getAttribute("data-download");
			node._ob_form.restore = function(event) {
				u.t.resetTimer(this.t_confirm);
				u.rc(this._ob_submit_button, "confirm");
				delete this._ob_submit_button._ob_wait_for_confirm;
				this._ob_submit_button.value = this._ob_submit_button.org_value;
			}
			node._ob_form.submitted = function(action) {
				if(!this._ob_submit_button._ob_wait_for_confirm && this._ob_submit_button.confirm_value) {
					u.ac(this._ob_submit_button, "confirm");
					this._ob_submit_button._ob_wait_for_confirm = true;
					this._ob_submit_button.value = this._ob_submit_button.confirm_value;
					this.t_confirm = u.t.setTimer(this, this.restore, 3000);
				}
				else {
					u.t.resetTimer(this.t_confirm);
					this.response = function(response) {
						u.rc(this, "submitting");
						u.rc(this._ob_submit_button, "disabled");
						if(fun(page.notify)) {
							page.notify(response);
						}
						this.restore();
						if(!response.cms_status || response.cms_status == "success") {
							if(response.cms_object && response.cms_object.constraint_error) {
								this._ob_submit_button.value = this._ob_submit_button.org_value;
								u.ac(this, "disabled");
							}
							else {
								if(this._ob_success_location) {
									u.ass(this._ob_submit_button, {
										"display": "none"
									});
									location.href = this._ob_success_location;
								}
								else if(this._ob_success_function) {
									if(fun(this._ob_node[this._ob_success_function])) {
										this._ob_node[this._ob_success_function](response);
									}
								}
								else if(fun(this._ob_node.confirmed)) {
									this._ob_node.confirmed(response);
								}
								else {
									u.bug("default return handling" + this._ob_success_location)
								}
							}
						}
						else {
							if(this._ob_error_function) {
								u.bug("error function:" + this._ob_error_function);
								if(fun(this._ob_node[this._ob_error_function])) {
									this._ob_node[this._ob_error_function](response);
								}
							}
							else if(fun(this._ob_node.confirmedError)) {
								u.bug("confirmedError");
								this._ob_node.confirmedError(response);
							}
						}
					}
					u.ac(this._ob_submit_button, "disabled");
					u.ac(this, "submitting");
					this._ob_submit_button.value = u.stringOr(this._ob_submit_button.wait_value, "Wait");
					if(this._ob_dom_submit) {
						u.bug("should submit:" + this._ob_download);
						if(this._ob_download) {
							this.response({"cms_status":"success"});
							u.bug("wait for download");
						}
						this.DOMsubmit();
					}
					else {
						u.request(this, this.action, {"method":"post", "data":this.getData()});
					}
				}
			}
		}
	}
}

/*beta-u-notifier.js*/
u.notifier = function(node) {
	var notifications = u.qs("div.notifications", node);
	if(!notifications) {
		node.notifications = u.ae(node, "div", {"id":"notifications"});
	}
	node.notifications.hide_delay = 4500;
	node.notifications.hide = function(node) {
		u.a.transition(this, "all 0.5s ease-in-out");
		u.a.translate(this, 0, -this.offsetHeight);
	}
	node.notify = function(response, _options) {
		var class_name = "message";
		if(obj(_options)) {
			var argument;
			for(argument in _options) {
				switch(argument) {
					case "class"	: class_name	= _options[argument]; break;
				}
			}
		}
		var output = [];
		if(obj(response) && response.isJSON) {
			var message = response.cms_message;
			var cms_status = typeof(response.cms_status) != "undefined" ? response.cms_status : "";
			if(obj(message)) {
				for(type in message) {
					if(str(message[type])) {
						output.push(u.ae(this.notifications, "div", {"class":class_name+" "+cms_status+" "+type, "html":message[type]}));
					}
					else if(obj(message[type]) && message[type].length) {
						var node, i;
						for(i = 0; i < message[type].length; i++) {
							_message = message[type][i];
							output.push(u.ae(this.notifications, "div", {"class":class_name+" "+cms_status+" "+type, "html":_message}));
						}
					}
				}
			}
			else if(str(message)) {
				output.push(u.ae(this.notifications, "div", {"class":class_name+" "+cms_status, "html":message}));
			}
			if(fun(this.notifications.show)) {
				this.notifications.show();
			}
		}
		else if(obj(response) && response.isHTML) {
			var login = u.qs(".scene.login form", response);
			var messages = u.qsa(".scene div.messages p", response);
			if(login && !u.qs("#login_overlay")) {
				// 
				// 
				this.autosave_disabled = true;
				if(page.t_autosave) {
					u.t.resetTimer(page.t_autosave);
				}
				var overlay = u.ae(document.body, "div", {"id":"login_overlay"});
				overlay.node = this;
				u.ae(overlay, login);
				u.as(document.body, "overflow", "hidden");
				var relogin = u.ie(login, "h1", {"class":"relogin", "html":(u.txt["relogin"] ? u.txt["relogin"] : "Your session expired")});
				login.overlay = overlay;
				u.ae(login, "input", {"type":"hidden", "name":"ajaxlogin", "value":"true"})
				u.f.init(login);
				login.inputs["username"].focus();
				login.submitted = function() {
					this.response = function(response) {
						if(response.isJSON && response.cms_status == "success") {
							var csrf_token = response.cms_object["csrf-token"];
							var data_vars = u.qsa("[data-csrf-token]", page);
							var input_vars = u.qsa("[name=csrf-token]", page);
							var dom_vars = u.qsa("*", page);
							var i, node;
							for(i = 0; i < data_vars.length; i++) {
								node = data_vars[i];
								node.setAttribute("data-csrf-token", csrf_token);
							}
							for(i = 0; i < input_vars.length; i++) {
								node = input_vars[i];
								node.value = csrf_token;
							}
							for(i = 0; i < dom_vars.length; i++) {
								node = dom_vars[i];
								if(node.csrf_token) {
									node.csrf_token = csrf_token;
								}
							}
							this.overlay.parentNode.removeChild(this.overlay);
							var multiple_overlays = u.qsa("#login_overlay");
							if(multiple_overlays) {
								for(i = 0; i < multiple_overlays.length; i++) {
									overlay = multiple_overlays[i];
									overlay.parentNode.removeChild(overlay);
								}
							}
							u.as(document.body, "overflow", "auto");
							this.overlay.node.autosave_disabled = false;
							if(this.overlay.node._autosave_node && this.overlay.node._autosave_interval) {
								u.t.setTimer(this.overlay.node._autosave_node, "autosave", this.overlay.node._autosave_interval);
							}
						}
						else {
							this.inputs["username"].focus();
							this.inputs["password"].val("");
							var error_message = u.qs(".errormessage", response);
							if(error_message) {
								this.overlay.node.notify({"isJSON":true, "cms_status":"error", "cms_message":error_message.innerHTML});
							}
							else {
								this.overlay.node.notify({"isJSON":true, "cms_status":"error", "cms_message":"An error occured"});
							}
						}
					}
					u.request(this, this.action, {"method":this.method, "data":this.getData()});
				}
			}
			else if(messages) {
				for(i = 0; i < messages.length; i++) {
					message = messages[i];
					output.push(u.ae(this.notifications, "div", {"class":message.className, "html":message.innerHTML}));
				}
			}
		}
		this.t_notifier = u.t.setTimer(this.notifications, this.notifications.hide, this.notifications.hide_delay, output);
	}
}


/*m-page.js*/
u.bug_console_only = true;
Util.Modules["page"] = new function() {
	this.init = function(page) {
		window.page = page;
		u.bug_force = true;
		u.bug("This site is built using the combined powers of body, mind and spirit. Well, and also Manipulator, Janitor and Detector");
		u.bug("Visit https://parentnode.dk for more information");
		u.bug_force = false;
		var i, node;
		page.hN = u.qs("#header", page);
		page.cN = u.qs("#content", page);
		page.nN = u.qs("#navigation", page);
		if(page.nN) {
			page.nN = page.hN.appendChild(page.nN);
		}
		page.fN = u.qs("#footer", page);
		page.resized = function() {
			this.browser_h = u.browserH();
			this.browser_w = u.browserW();
			if(this.cN && this.cN.scene && fun(this.cN.scene.resized)) {
				this.cN.scene.resized();
			}
		}
		page.scrolled = function() {
			this.scroll_y = u.scrollY();
			if(this.cN && this.cN.scene && fun(this.cN.scene.scrolled)) {
				this.cN.scene.scrolled();
			}
		}
		page.orientationchanged = function() {
			if(this.cN && this.cN.scene && fun(this.cN.scene.orientationchanged)) {
				this.cN.scene.orientationchanged();
			}
		}
		page.ready = function() {
			if(!this.is_ready) {
				this.is_ready = true;
				u.e.addEvent(window, "resize", this.resized);
				u.e.addEvent(window, "scroll", this.scrolled);
				u.e.addEvent(window, "orientationchange", this.orientationchanged);
				this.initHeader();
				u.notifier(this);
				u.navigation();
				this.resized();
			}
		}
		page.cN.navigate = function(url) {
			u.bug("page.navigated:", url);
			location.href = url;
		}
		page.initHeader = function() {
			var janitor = u.ie(this.hN, "ul", {"class":"janitor"});
			u.ae(janitor, u.qs(".servicenavigation .front", this.hN));
			var janitor_text = u.qs("li a", janitor);
			if(janitor_text) {
				janitor_text.innerHTML = "<span>"+janitor_text.innerHTML.split("").join("</span><span>")+"</span>"; 
				page.hN.janitor_spans = u.qsa("span", janitor_text);
				var i, span, j, section, node;
				for(i = 0; span = page.hN.janitor_spans[i]; i++) {
					if(i == 0) {
						u.ass(span, {
							"transform":"translate(-8px, 0)"
						});
					}
					else {
						u.ass(span, {
							"opacity":0,
							"transform":"translate(-8px, -30px)"
						});
					}
				}
				u.ass(janitor_text, {"opacity": 1});
			}
			u.ae(this, u.qs(".servicenavigation", this.hN));
			var sections = u.qsa("ul.navigation > li", this.nN);
			if(sections.length) {
				for(i = 0; section = sections[i]; i++) {
					section.header = u.qs("h3", section);
					if(section.header) {
						section.nodes = u.qsa("li", section);
						if(section.nodes.length) {
							if(section.header) {
								section.header.section = section;
								u.e.click(section.header);
								section.header.clicked = function(event) {
									u.e.kill(event);
									if(this.section.is_open) {
										this.section.is_open = false;
										u.as(this.section, "height", this.offsetHeight+"px");
										u.saveNodeCookie(this.section, "open", 0, {"ignore_classvars":true});
										u.addExpandArrow(this);
									}
									else {
										this.section.is_open = true;
										u.as(this.section, "height", "auto");
										u.saveNodeCookie(this.section, "open", 1, {"ignore_classvars":true});
										u.addCollapseArrow(this);
									}
								}
								var state = u.getNodeCookie(section, "open", {"ignore_classvars":true});
								if(!state) {
									section.is_open = true;
								}
								section.header.clicked();
							}
							for(j = 0; node = section.nodes[j]; j++) {
								u.ce(node, {"type":"link"});
								if(u.hc(node, "selected|path")) {
									if(!section.is_open) {
										section.header.clicked();
									}
								}
							}
						}
						else {
							u.ac(section, "empty");
						}
					}
					else {
						u.ce(section, {"type":"link"});
					}
				}
			}
			if(this.nN) {
				u.ass(this.nN, {
					"display":"none"
				});
			}
			if(sections.length && janitor_text) {
				if(u.e.event_support == "mouse") {
					u.e.hover(this.hN, {"delay_over":300});
				}
				else {
					u.e.click(page.hN);
					page.hN.clicked = function(event) {
						if(!this.is_open) {
							u.e.kill(event);
							this.over();
						}
					}
					page.hN.close = function(event) {
						if(this.is_open) {
							u.e.kill(event);
							this.out();
						}
					}
					u.e.addWindowEndEvent(page.hN, "close");
				}
				page.hN.over = function() {
					this.is_open = true;
					u.a.transition(page.nN, "none");
					page.nN.transitioned = null;
					u.t.resetTimer(this.t_navigation);
					u.a.transition(this, "all 0.3s ease-in-out");
					u.ass(this, {
						"width":"230px"
					});
					u.ass(page.nN, {
						"display":"block"
					});
					u.a.transition(page.nN, "all 0.3s ease-in");
					u.ass(page.nN, {
						"opacity":1
					});
					for(i = 0; span = page.hN.janitor_spans[i]; i++) {
						if(i == 0) {
							u.a.transition(span, "all 0.2s ease-in " + (i*50) + "ms");
							u.ass(span, {
								"transform":"translate(0, 0)"
							});
						}
						else {
							u.a.transition(span, "all 0.2s ease-in " + (i*50) + "ms");
							u.ass(span, {
								"opacity":1,
								"transform":"translate(0, 0)"
							});
						}
					}
				}
				page.hN.out = function() {
					this.is_open = false;
					u.a.transition(page.nN, "none");
					page.nN.transitioned = null;
					var span, i;
					for(i = 0; span = page.hN.janitor_spans[i]; i++) {
						if(i == 0) {
							u.a.transition(span, "all 0.2s ease-in " + ((page.hN.janitor_spans.length-i)*50) + "ms");
							u.ass(span, {
								"transform":"translate(-8px, 0)"
							});
						}
						else {
							u.a.transition(span, "all 0.2s ease-in " + ((page.hN.janitor_spans.length-i)*50) + "ms");
							u.ass(span, {
								"opacity":0,
								"transform":"translate(-8px, -30px)"
							});
						}
					}
					page.nN.transitioned = function() {
						u.ass(this, {
							"display":"none"
						});
					}
					u.a.transition(page.nN, "all 0.2s ease-in");
					u.ass(page.nN, {
						"opacity":0
					});
					u.a.transition(this, "all 0.2s ease-in-out 300ms");
					u.ass(this, {
						"width":"30px"
					});
				}
			}
		}
		page.ready();
	}
}
u.e.addDOMReadyEvent(u.init)


/*m-scene.js*/
Util.Modules["scene"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			page.cN.scene = this;
			page.resized();
		}
		scene.ready();
	}
}

/*m-login.js*/
Util.Modules["login"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			page.cN.scene = this;
			this._form = u.qs("form", this);
			u.f.init(this._form);
			this._form.inputs["username"].focus();
			page.resized();
		}
		scene.ready();
	}
}

/*m-default_list.js*/
Util.Modules["defaultList"] = new function() {
	this.init = function(div) {
		var i, node;
		div.list = u.qs("ul.items", div);
		if(!div.list) {
			div.list = u.ae(div, "ul", {"class":"items"});
		}
		div.list.div = div;
		div.csrf_token = div.getAttribute("data-csrf-token");
		div.nodes = u.qsa("li.item", div.list);
		var i, node;
		for(i = 0; node = div.nodes[i]; i++) {
			node._item_id = u.cv(node, "item_id");
			node.div = div;
			node._text = u.wc(node, "div", {"class":"text"});
		}
		div.scrolled = function() {
			var browser_h = u.browserH();
			var scroll_y = u.scrollY();
			var i, node, abs_y, initialized = 0;
			for(i = 0; node = this.nodes[i]; i++) {
				if(!node._ready) {
					abs_y = u.absY(node);
					if(abs_y - 200 < (scroll_y + browser_h) && (abs_y + 200) > scroll_y) {
						this.buildNode(node);
					}
				}
				else {
					initialized++;
				}
			}
			if(initialized == this.nodes.length && this.scroll_event_id) {
				u.e.removeWindowEvent(this, "scroll", this.scroll_event_id);
				this.scroll_event_id = false;
			}
		}
		div.scroll_event_id = u.e.addWindowEvent(div, "scroll", div.scrolled);
		div.buildNode = function(node) {
			node._actions = u.qsa(".actions li", node);
			var i, action, form, bn_detele, form_disable, form_enable;
			for(i = 0; action = node._actions[i]; i++) {
				if(u.hc(action, "status")) {
					if(!action.childNodes.length) {
						action.update_status_url = action.getAttribute("data-item-status");
						if(action.update_status_url) {
							form_disable = u.f.addForm(action, {"action":action.update_status_url+"/"+node._item_id+"/0", "class":"disable"});
							u.f.addField(form_disable, {"type":"hidden", "name":"csrf-token", "value":this.csrf_token});
							u.f.addAction(form_disable, {"value":"Disable", "class":"button status"});
							form_enable = u.f.addForm(action, {"action":action.update_status_url+"/"+node._item_id+"/1", "class":"enable"});
							u.f.addField(form_enable, {"type":"hidden", "name":"csrf-token", "value":this.csrf_token});
							u.f.addAction(form_enable, {"value":"Enable", "class":"button status"});
						}
					}
					else {
						form_disable = u.qs("form.disable", action);
						form_enable = u.qs("form.enable", action);
					}
					if(form_disable && form_enable) {
						u.f.init(form_disable);
						form_disable.submitted = function() {
							this.response = function(response) {
								page.notify(response);
								if(response.cms_status == "success") {
									u.ac(this.parentNode, "disabled");
									u.rc(this.parentNode, "enabled");
								}
							}
							u.request(this, this.action, {"method":"post", "data":this.getData()});
						}
						u.f.init(form_enable);
						form_enable.submitted = function() {
							this.response = function(response) {
								page.notify(response);
								if(response.cms_status == "success") {
									u.rc(this.parentNode, "disabled");
									u.ac(this.parentNode, "enabled");
								}
							}
							u.request(this, this.action, {"method":"post", "data":this.getData()});
						}
					}
				}
				else if(u.hc(action, "delete")) {
					action.node = node;
					u.m.oneButtonForm.init(action);
					action.confirmed = function(response) {
						if(response.cms_status == "success") {
							if(response.cms_object && response.cms_object.constraint_error) {
								u.ac(this.form.confirm_submit_button, "disabled");
							}
							else {
								this.node.parentNode.removeChild(this.node);
								this.node.div.scrolled();
								if(fun(this.node.div.list.updateDraggables)) {
									this.node.div.list.updateDraggables();
								}
							}
						}
					}
				}
			}
			if(div._images) {
				u.ass(node._text, {
					"padding-left": div._node_padding_left + "px"
				});
				node._format = u.cv(node, "format");
				node._variant = u.cv(node, "variant");
				if(node._format && node.div._media_width) {
					node._image_src = "/images/"+node._item_id+"/"+(node._variant ? node._variant+"/" : "")+node.div._media_width+"x."+node._format;
				}
				else {
					node._image_src = "/images/0/missing/"+node.div._media_width+"x.png";
				}
				if(node._image_src) {
					u.as(node, "backgroundImage", "url("+node._image_src+")");
				}
			}
			else if(div._audios) {
				u.ass(node._text, {
					"padding-left": div._node_padding_left + "px"
				});
				node._format = u.cv(node, "format");
				node._variant = u.cv(node, "variant");
				if(node._format) {
					node._audio = u.ie(node, "div", {"class":"audioplayer"});
					if(!page.audioplayer) {
						page.audioplayer = u.audioPlayer();
					}
					node._audio.scene = this;
					node._audio.url = "/audios/"+node._item_id+"/" + (node._variant ? node._variant+"/" : "") + "128."+node._format;
					u.e.click(node._audio);
					node._audio.clicked = function(event) {
						if(!u.hc(this.parentNode, "playing")) {
							var node, i;
							for(i = 0; node = this.scene.nodes[i]; i++) {
								u.rc(node, "playing");
							}
							page.audioplayer.loadAndPlay(this.url);
							u.ac(this.parentNode, "playing");
						}
						else {
							page.audioplayer.stop();
							u.rc(this.parentNode, "playing");
						}
					}
				}
				else {
					u.ac(audio, "disabled");
				}
			}
			else if(div._videos) {
			}
			node._ready = true;
			node.div.scrolled();
		}
		if(u.hc(div, "images") || u.hc(div, "videos") || u.hc(div, "audios")) {
			div._images = u.hc(div, "images");
			div._videos = u.hc(div, "videos");
			div._audios = u.hc(div, "audios");
			div._media_width = u.cv(div, "width") ? u.cv(div, "width") : (div._audios ? 40 : 100);
			div._node_padding_left = ((Number(div._media_width) - 15) + ((u.hc(div, "sortable") && div.list) ? 25 : 0));
		}
		if(u.hc(div, "taggable")) {
			div.add_tag_url = div.getAttribute("data-tag-add");
			div.delete_tag_url = div.getAttribute("data-tag-delete");
			div.get_tags_url = div.getAttribute("data-tag-get");
			if(div.csrf_token && div.get_tags_url && div.delete_tag_url && div.add_tag_url) {
				div.tagsResponse = function(response) {
					if(response.cms_status == "success" && response.cms_object) {
						this.all_tags = response.cms_object;
					}
					else {
						page.notify(response);
						this.all_tags = [];
					}
					var i, node;
					for(i = 0; node = this.nodes[i]; i++) {
						node.data_div = this;
						node._tags = u.qs("ul.tags", node);
						if(!node._tags) {
							node._tags = u.ae(node, "ul", {"class":"tags"});
						}
						node._tags.div = this;
						node._bn_add = u.ae(node._tags, "li", {"class":"add","html":"+"});
						node._bn_add.node = node;
						u.enableTagging(node);
					}
				}
				u.request(div, div.get_tags_url, {"callback":"tagsResponse", "method":"post", "data":"csrf-token=" + div.csrf_token});
			}
		}
		if(u.hc(div, "filters")) {
			u.defaultFilters(div);
			div.filtered = function() {
				this.scrolled();
				if(this.bn_all) {
					this.bn_all.updateState();
					this.bn_range._to.value = "";
					this.bn_range._from.value = "";
				}
			}
		}
		if(u.hc(div, "sortable")) {
			u.defaultSortableList(div.list);
		}
		if(u.hc(div, "selectable")) {
			u.defaultSelectable(div);
		}
		div.scrolled();
	}
}


/*m-default_edit.js*/
Util.Modules["defaultEdit"] = new function() {
	this.init = function(div) {
		div._item_id = u.cv(div, "item_id");
		var form = u.qs("form", div);
		form.div = div;
		form.h2_name = u.qs("#content .scene > h2.name");
		form.p_sindex = u.qs("#content .scene div.sindex p.sindex");
		var autosave_setting = u.cv(div, "autosave");
		if(autosave_setting == "off") {
			page.autosave_disabled = true;
		}
		u.f.init(form);
		form.submitFailed = function(iN) {
			u.bug("submit failed", iN);
			var first_error = Object.keys(this._error_inputs)[0];
			if(this.inputs[first_error]) {
				u.scrollTo(window, {node: this.inputs[first_error].field, offset_y: 170});
			}
		}
		form.submitted = function(iN) {
			u.t.resetTimer(page.t_autosave);
			this.response = function(response) {
				page.notify(response);
				u.f.updateFilelistStatus(this, response);
				if(response && response.cms_object) {
					if(response.cms_object.name && this.h2_name) {
						this.h2_name.innerHTML = response.cms_object.name;
					}
					if(response.cms_object.sindex && this.p_sindex) {
						this.p_sindex.innerHTML = response.cms_object.sindex;
					}
				}
			}
			u.request(this, this.action, {"method":"post", "data" : this.getData()});
		}
		form.updated = function() {
			this.change_state = true;
			u.t.resetTimer(page.t_autosave);
			if(!page.autosave_disabled) {
				page.t_autosave = u.t.setTimer(this, "autosave", page._autosave_interval);
			}
		}
		form.autosave = function() {
			if(!page.autosave_disabled && this.change_state) {
				for(name in this.inputs) {
					if(this.inputs[name].field) {
						if(!this.inputs[name].used) {
							if(u.hc(this.inputs[name].field, "required") && !this.inputs[name].val()) {
								return false;
							}
						}
						else {
							u.f.validate(this.inputs[name]);
						}
					}
				}
				if(!u.qs(".field.error", this)) {
					this.change_state = false;
					this.submitted();
				}
			}
			else {
			}
		}
		form.change_state = false;
		page._autosave_node = form;
		page._autosave_interval = 3000;
		page.t_autosave = u.t.setTimer(form, "autosave", page._autosave_interval);
		form.cancelBackspace = function(event) {
			if(event.keyCode == 8 && !u.qsa(".field.focus").length) {
				u.e.kill(event);
			}
		}
		u.e.addEvent(document.body, "keydown", form.cancelBackspace);
	}
}
Util.Modules["newSystemMessage"] = new function() {
	this.init = function(div) {
		var form = u.qs("form", div);
		form.div = div;
		form.ul_actions = u.qs("ul.actions", form);
		var fieldset = u.qs("fieldset.values", form);
		fieldset.h3_span = u.qs("h3 span.recipient", fieldset);
		fieldset.h3_span.innerHTML = "?";
		u.f.init(form);
		form.inputs["recipients"].keyup = function(event) {
			var recipients = this.val().replace(/,/g, ";").split(";");
			var fieldsets = u.qsa("fieldset.values", this._form);
			var i, recipient, inputs, input, labels, label, fieldset;
			if(event.key == "," || event.key == "," || event.key == "Delete" || event.key == "Backspace") {
				if(recipients.length < fieldsets.length && fieldsets.length > 1) {
					fieldsets[0].parentNode.removeChild(fieldsets[fieldsets.length-1]);
					fieldsets = u.qsa("fieldset.values", this._form);
				}
				else if(recipients.length > fieldsets.length) {
					fieldset = fieldsets[0].parentNode.insertBefore(fieldsets[0].cloneNode(true), this._form.ul_actions);
					fieldset.h3_span = u.qs("h3 span.recipient", fieldset);
					fieldsets = u.qsa("fieldset.values", this._form);
					inputs = u.qsa("input[type=text]", fieldset);
					for(i = 0; i < inputs.length; i++) {
						input = inputs[i];
						input.value = "";
						input.name = input.name.replace(/values\[[\d]+\]/, "values["+(fieldsets.length-1)+"]");
						input.id = input.id.replace(/values\[[\d]+\]/, "values["+(fieldsets.length-1)+"]");
					}
					labels = u.qsa("label", fieldset);
					for(i = 0; i < labels.length; i++) {
						label = labels[i];
						label.setAttribute("for", label.getAttribute("for").replace(/values\[[\d]+\]/, "values["+(fieldsets.length-1)+"]"));
					}
					u.f.init(this._form);
				}
			}
			for(i = 0; i < recipients.length; i++) {
				recipient = recipients[i];
				fieldsets[i].h3_span.innerHTML = recipient;
			}
		}
		u.e.addEvent(form.inputs["recipients"], "keyup", form.inputs["recipients"].keyup);
		form.submitted = function(iN) {
			u.ac(this, "submitting");
			this.response = function(response) {
				u.rc(this, "submitting");
				if(response.cms_status == "success") {
					var div_receipt = u.ae(this.div, "div", {class:"receipt"});
					u.ae(div_receipt, "p", {html:"Mail(s) was successfully sent to:"});
					var ul_receipt = u.ae(div_receipt, "ul", {class:"receipt"});
					var i;
					for(i = 0; i < response.cms_object.length; i++) {
						u.ae(ul_receipt, "li", {html:response.cms_object[i]})
					}
					this.parentNode.replaceChild(div_receipt, this);
				}
				page.notify(response);
			}
			u.request(this, this.action, {"method":"post", "data" : this.getData({"format":"formdata"})});
		}
	}
}
Util.Modules["sendMessage"] = new function() {
	this.init = function(div) {
		var form = u.qs("form", div);
		form.div = div;
		u.f.init(form);
		form.div_message_form = u.qs("div.item.message form");
		form.submitted = function(iN) {
			if(this.inputs["recipients"].val() || this.inputs["maillist_id"].val() || this.inputs["user_id"].val()) {
				this.div_message_form.submit();
				u.ac(this, "submitting");
				this.response = function(response) {
					u.rc(this, "submitting");
					page.notify(response);
					if(response.cms_status == "success") {
						u.ass(this, {
							display:"none",
						});
						this.div_receipt = u.ae(this.div, "div", {class:"receipt"});
						u.ae(this.div_receipt, "p", {html:"Mail(s) was successfully sent to:"});
						var ul_receipt = u.ae(this.div_receipt, "ul", {class:"receipt"});
						var i;
						for(i = 0; i < response.cms_object.length; i++) {
							u.ae(ul_receipt, "li", {html:response.cms_object[i]})
						}
						var ul_actions = u.ae(this.div_receipt, "ul", {class:"actions"});
						var action = u.f.addAction(ul_actions, {name:"send_another", value:"Send another", type:"button", class:"button"});
						action._form = this
						u.ce(action);
						action.clicked = function() {
							this._form.div_receipt.parentNode.removeChild(this._form.div_receipt);
							u.ass(this._form, {
								display: "block",
							});
						}
					}
				}
				u.request(this, this.action, {"method":"post", "data" : this.getData({"format":"formdata"})});
			}
			else {
				u.f.inputHasError(this.inputs["recipients"]);
				u.f.inputHasError(this.inputs["maillist_id"]);
			}
		}
	}
}


/*m-default_new.js*/
Util.Modules["defaultNew"] = new function() {
	this.init = function(form) {
		u.f.init(form);
		if(form.actions["cancel"]) {
			form.actions["cancel"].clicked = function(event) {
				location.href = this.url;
			}
		}
		form.submitted = function(iN) {
			this.response = function(response) {
				u.rc(this, "submitting");
				if(response.cms_status == "success" && response.cms_object) {
					if(response.return_to) {
						if(response.cms_object.item_id) {
							location.href = response.return_to + response.cms_object.item_id;
						}
						else if(response.cms_object.id) {
							location.href = response.return_to + response.cms_object.id;
						}
						else {
							location.href = response.return_to;
						}
					}
					else if(this.action.match(/\/save$/)) {
						if(response.cms_object.item_id) {
							location.href = this.action.replace(/\/save/, "/edit/")+response.cms_object.item_id;
						}
						else if (response.cms_object.id) {
							location.href = this.action.replace(/\/save/, "/edit/")+response.cms_object.id;
						}
					}
					else if(location.href.match(/\/new$/)) {
						if(response.cms_object.item_id) {
							location.href = location.href.replace(/\/new$/, "/edit/")+response.cms_object.item_id;
						}
						else if (response.cms_object.id) {
							location.href = location.href.replace(/\/new$/, "/edit/")+response.cms_object.id;
						}
					}
					else if(this.actions["cancel"]) {
						this.actions["cancel"].clicked();
					}
				}
				else {
					page.notify(response);
				}
			}
			u.ac(this, "submitting");
			u.request(this, this.action, {"method":"post", "data" : this.getData({"format":"formdata"})});
		}
	}
}

/*m-default_edit_status.js*/
Util.Modules["defaultEditStatus"] = new function() {
	this.init = function(node) {
		node._item_id = u.cv(node, "item_id");
		node.csrf_token = node.getAttribute("data-csrf-token");
		var action = u.qs("li.status");
		if(action) {
			if(!action.childNodes.length) {
				action.update_status_url = action.getAttribute("data-item-status");
				if(action.update_status_url) {
					form_disable = u.f.addForm(action, {"action":action.update_status_url+"/"+node._item_id+"/0", "class":"disable"});
					u.ae(form_disable, "input", {"type":"hidden","name":"csrf-token", "value":node.csrf_token});
					u.f.addAction(form_disable, {"value":"Disable", "class":"button status"});
					form_enable = u.f.addForm(action, {"action":action.update_status_url+"/"+node._item_id+"/1", "class":"enable"});
					u.ae(form_enable, "input", {"type":"hidden","name":"csrf-token", "value":node.csrf_token});
					u.f.addAction(form_enable, {"value":"Enable", "class":"button status"});
				}
			}
			else {
				form_disable = u.qs("form.disable", action);
				form_enable = u.qs("form.enable", action);
			}
			if(form_disable && form_enable) {
				u.f.init(form_disable);
				form_disable.submitted = function() {
					this.response = function(response) {
						page.notify(response);
						if(response.cms_status == "success") {
							u.ac(this.parentNode, "disabled");
							u.rc(this.parentNode, "enabled");
						}
					}
					u.request(this, this.action, {"method":this.method, "data":this.getData()});
				}
				u.f.init(form_enable);
				form_enable.submitted = function() {
					this.response = function(response) {
						page.notify(response);
						if(response.cms_status == "success") {
							u.rc(this.parentNode, "disabled");
							u.ac(this.parentNode, "enabled");
						}
					}
					u.request(this, this.action, {"method":this.method, "data":this.getData()});
				}
			}
		}
	}
}

/*m-default_edit_actions.js*/
Util.Modules["defaultEditActions"] = new function() {
	this.init = function(node) {
		var bn_duplicate = u.qs("li.duplicate", node);
		if(bn_duplicate) {
			bn_duplicate.duplicated = function(response) {
				console.log(response)
				location.href = location.href.replace(/edit\/.+/, "edit/"+response.cms_object["id"]);
			}
		}
	}
}


/*m-default_tags.js*/
Util.Modules["defaultTags"] = new function() {
	this.init = function(div) {
		div._item_id = u.cv(div, "item_id");
		div.csrf_token = div.getAttribute("data-csrf-token");
		div.add_tag_url = div.getAttribute("data-tag-add");
		div.delete_tag_url = div.getAttribute("data-tag-delete");
		div.get_tags_url = div.getAttribute("data-tag-get");
		div.data_div = div;
		if(div.csrf_token && div.get_tags_url && div.delete_tag_url && div.add_tag_url) {
			div._tags = u.qs("ul.tags", div);
			if(!div._tags) {
				div._tags = u.ae(div._tags, "ul", {"class":"tags"});
			}
			div._tags.div = div;
			div._tags_context = div._tags.getAttribute("data-context");
			div.tagsResponse = function(response) {
				if(response.cms_status == "success" && response.cms_object) {
					this.all_tags = response.cms_object;
				}
				else {
					page.notify(response);
					this.all_tags = [];
				}
				this._bn_add = u.ae(this._tags, "li", {"class":"add","html":"+"});
				this._bn_add.node = this;
				u.enableTagging(this);
			}
			u.request(div, div.get_tags_url + (div._tags_context ? "/"+div._tags_context : ""), {"callback":"tagsResponse", "method":"post", "data":"csrf-token=" + div.csrf_token});
		}
	}
}


/*m-default_media.js*/
Util.Modules["addMedia"] = new function() {
	this.init = function(div) {
		div.form = u.qs("form.upload", div);
		div.form.div = div;
		div.item_id = u.cv(div, "item_id");
		div.variant = u.cv(div, "variant");
		u.f.init(div.form);
		div.csrf_token = div.form.inputs["csrf-token"].val();
		div.delete_url = div.getAttribute("data-media-delete");
		div.update_name_url = div.getAttribute("data-media-name");
		div.save_order_url = div.getAttribute("data-media-order");
		div.media_input = div.form.inputs[div.variant+"[]"];
		div.filelist = div.media_input.field.filelist;
		div.previewlist = u.ae(div, "ul", {class:"previewlist"});
		div.previewlist.div = div;
		div.form.changed = function() {
			this.submit();
		}
		div.form.submitted = function() {
			this.div.media_input.blur();
			u.ac(this.div.media_input.field, "loading");
			this.response = function(response) {
				page.notify(response);
				if(response.cms_status == "success" && response.cms_object) {
					u.f.updateFilelistStatus(this, response);
					this.div.updatePreviews();
				}
				u.rc(this.div.media_input.field, "loading");
				this.div.media_input.val("");
			}
			u.request(this, this.action, {"method":"post", "data":this.getData()});
		}
		div.updatePreviews = function() {
			var nodes = u.qsa("li.uploaded,li.new", this.filelist);
			if(nodes) {
				var i, node, li;
				for(i = 0; i < nodes.length; i++) {
					node = nodes[i];
					if(!node.li_preview) {
						node.media_format = u.cv(node, "format");
						node.media_variant = u.cv(node, "variant");
						node.media_id = u.cv(node, "media_id");
						node.media_name = node.innerHTML;
						node.div = this;
						node.li_preview = u.ae(this.previewlist, "li", {class: "preview media_id:" + node.media_id});
						node.preview = u.ae(node.li_preview, "div", {class: "preview " + node.media_format});
						node.preview.node = node;
						if(node.media_format.match(/^(jpg|png|gif)$/i)) {
							this.addImagePreview(node);
						}
						else if(node.media_format.match(/^(mp3|ogg|wav|aac)$/i)) {
							this.addAudioPreview(node);
						}
						else if(node.media_format.match(/^(mov|mp4|ogv|3gp)$/i)) {
							this.addVideoPreview(node);
						}
						else if(node.media_format.match(/^zip$/i)) {
							this.addZipPreview(node);
						}
						else if(node.media_format.match(/^pdf$/i)) {
							this.addPdfPreview(node);
						}
						u.addRenameMediaForm(div, node);
						u.addDeleteMediaForm(div, node);
						node.delete_form.deleted = function(response) {
							this.node.div.filelist.removeChild(this.node);
							this.node.div.previewlist.removeChild(this.node.li_preview);
							this.node.div.media_input.field.uploaded_files = u.qsa("li.uploaded", this.node.div.filelist);
							if(fun(this.node.div.previewlist.updateDraggables)) {
								this.node.div.previewlist.updateDraggables();
							}
						}
					}
					else {
						u.ae(this.previewlist, node.li_preview);
					}
				}
			}
			if(fun(this.previewlist.updateDraggables)) {
				this.previewlist.updateDraggables();
			}
		}
		div.addImagePreview = function(node) {
			node.media_width = u.cv(node, "width");
			node.media_height = u.cv(node, "height");
			u.ass(node.preview, {
				"width": ((node.preview.offsetHeight/node.media_height) * node.media_width) + "px",
				"backgroundImage": "url(/images/"+this.item_id+"/"+node.media_variant+"/x"+node.preview.offsetHeight+"."+node.media_format+"?"+u.randomString(4)+")"
			});
		}
		div.addPdfPreview = function(node) {
			u.ass(node.preview, {
				"backgroundImage": "url(/images/0/pdf/30x.png)"
			});
		}
		div.addZipPreview = function(node) {
			u.ass(node.preview, {
				"backgroundImage": "url(/images/0/zip/30x.png)"
			});
		}
		div.addAudioPreview = function(node) {
			node.preview.audio_url = "/audios/"+this.item_id+"/"+node.media_variant+"/128."+node.media_format+"?"+u.randomString(4);
			u.addPlayMedia(this, node);
		}
		div.addVideoPreview = function(node) {
			node.media_width = u.cv(node, "width");
			node.media_height = u.cv(node, "height");
			u.ass(node.preview, {
				"width": ((node.preview.offsetHeight/node.media_height) * node.media_width) + "px",
			});
			node.preview.video_url = "/videos/"+this.item_id+"/"+node.media_variant+"/"+this.filelist.offsetWidth+"x."+node.media_format+"?"+u.randomString(4);
			u.addPlayMedia(this, node);
		}
		div.updatePreviews();
		if(u.hc(div, "sortable") && div.save_order_url) {
			u.sortable(div.previewlist);
			div.previewlist.picked = function(event) {}
			div.previewlist.dropped = function(event) {
				var order = this.getNodeOrder({class_var:"media_id"});
				this.response = function(response) {
					page.notify(response);
				}
				u.request(this, this.div.save_order_url+"/"+this.div.item_id, {
					"method":"post", 
					"data":"csrf-token=" + this.div.csrf_token + "&order=" + order.join(",")
				});
			}
		}
		else {
			u.rc(div, "sortable");
		}
	}
}
Util.Modules["addMediaSingle"] = new function() {
	this.init = function(div) {
		div.form = u.qs("form.upload", div);
		div.form.div = div;
		div.item_id = u.cv(div, "item_id");
		div.variant = u.cv(div, "variant");
		u.f.init(div.form);
		div.csrf_token = div.form.inputs["csrf-token"].val();
		div.delete_url = div.getAttribute("data-media-delete");
		div.update_name_url = div.getAttribute("data-media-name");
		div.media_input = div.form.inputs[div.variant+"[]"];
		div.filelist = div.media_input.field.filelist;
		div.form.changed = function() {
			this.submit();
		}
		div.form.submitted = function() {
			this.div.media_input.blur();
			u.ac(this.div.media_input.field, "loading");
			if(this.div.preview) {
				u.as(this.div.preview, "display", "none");
			}
			this.response = function(response) {
				page.notify(response);
				if(response.cms_status == "success" && response.cms_object) {
					u.f.updateFilelistStatus(this, response);
					this.div.updatePreview();
				}
				u.rc(this.div.media_input.field, "loading");
				this.div.media_input.val("");
			}
			u.request(this, this.action, {"method":"post", "data":this.getData()});
		}
		div.updatePreview = function() {
			if(this.preview) {
				this.preview.parentNode.removeChild(this.preview);
				delete this.preview;
			}
			var node = u.qs("li.uploaded", this.filelist);
			if(node) {
				node.media_format = u.cv(node, "format");
				node.media_variant = u.cv(node, "variant");
				node.media_name = node.innerHTML;
				node.div = this;
				node.preview = u.ae(this, "div", {class: "preview " + node.media_format});
				node.preview.node = node;
				this.preview = node.preview;
				u.ass(node.preview, {
					"width": this.filelist.offsetWidth + "px"
				});
				if(node.media_format.match(/^(jpg|png|gif)$/i)) {
					this.addImagePreview(node);
				}
				else if(node.media_format.match(/^(mp3|ogg|wav|aac)$/i)) {
					this.addAudioPreview(node);
				}
				else if(node.media_format.match(/^(mov|mp4|ogv|3gp)$/i)) {
					this.addVideoPreview(node);
				}
				else if(node.media_format.match(/^zip$/i)) {
					this.addZipPreview(node);
				}
				else if(node.media_format.match(/^pdf$/i)) {
					this.addPdfPreview(node);
				}
				u.addRenameMediaForm(div, node);
				u.addDeleteMediaForm(div, node);
				node.delete_form.deleted = function(response) {
					this.node.div.media_input.field.uploaded_files = false;
					this.node.div.media_input.val("");
					this.node.div.updatePreview();
				}
			}
		}
		div.addImagePreview = function(node) {
			node.media_width = u.cv(node, "width");
			node.media_height = u.cv(node, "height");
			u.ass(node.preview, {
				"height": ((this.filelist.offsetWidth/node.media_width) * node.media_height) + "px",
				"backgroundImage": "url(/images/"+this.item_id+"/"+node.media_variant+"/"+this.filelist.offsetWidth+"x."+node.media_format+"?"+u.randomString(4)+")"
			});
		}
		div.addPdfPreview = function(node) {
			u.ass(node.preview, {
				"backgroundImage": "url(/images/0/pdf/30x.png)"
			});
		}
		div.addZipPreview = function(node) {
			u.ass(node.preview, {
				"backgroundImage": "url(/images/0/zip/30x.png)"
			});
		}
		div.addAudioPreview = function(node) {
			node.preview.audio_url = "/audios/"+this.item_id+"/"+node.media_variant+"/128."+node.media_format+"?"+u.randomString(4);
			u.addPlayMedia(this, node);
		}
		div.addVideoPreview = function(node) {
			node.media_width = u.cv(node, "width");
			node.media_height = u.cv(node, "height");
			u.ass(node.preview, {
				"height": ((this.filelist.offsetWidth/node.media_width) * node.media_height) + "px"
			});
			node.preview.video_url = "/videos/"+this.item_id+"/"+node.media_variant+"/"+this.filelist.offsetWidth+"x."+node.media_format+"?"+u.randomString(4);
			u.addPlayMedia(this, node);
		}
		div.updatePreview();
	}
}
u.addPlayMedia = function(div, node) {
	node.bn_play = u.ae(node.preview, "div", {"class":"play"});
	node.bn_play.preview = node.preview;
	u.ce(node.bn_play);
	node.bn_play.clicked = function(event) {
		if(!this.player) {
			this.player = this.preview.audio_url ? u.audioPlayer() : u.videoPlayer();
			this.player.bn_play = this;
			this.player.ended = function() {
				u.rc(this.bn_play, "playing");
				delete this.parentNode.player;
				this.parentNode.removeChild(this);
			}
		}
		u.ae(this.preview, this.player);
		if(!u.hc(this, "playing")) {
			this.player.loadAndPlay(this.preview.audio_url ? this.preview.audio_url : this.preview.video_url);
			u.ac(this, "playing");
		}
		else {
			this.player.stop();
			u.rc(this, "playing");
			this.preview.removeChild(this.player);
			delete this.player;
		}
	}
}
u.addDeleteMediaForm = function(div, node) {
	node.delete_form = u.f.addForm(node.preview, {
		"action":div.delete_url+"/"+div.item_id+"/"+node.media_variant, 
		"class":"delete"
	});
	node.delete_form.node = node;
	u.f.addField(node.delete_form, {
		"type":"hidden",
		"name":"csrf-token", 
		"value":div.csrf_token
	});
	u.f.addAction(node.delete_form, {
		"class":"button delete"
	});
	node.delete_form.setAttribute("data-confirm-value", "Confirm");
	node.delete_form.setAttribute("data-success-function", "deleted");
	u.m.oneButtonForm.init(node.delete_form);
}
u.addRenameMediaForm = function(div, node) {
	node.update_name_form = u.f.addForm(node.preview, {
		"action":div.update_name_url+"/"+div.item_id+"/"+node.media_variant, 
		"class":"edit"
	});
	node.update_name_form.node = node;
	u.f.addField(node.update_name_form, {
		"type":"hidden",
		"name":"csrf-token", 
		"value":div.csrf_token
	});
	u.f.addField(node.update_name_form, {
		"type":"string",
		"name":"name", 
		"value":node.media_name, 
		"required":true
	});
	u.f.init(node.update_name_form);
	u.ce(node.update_name_form);
	node.update_name_form.inputStarted = function(event) {
		if(!u.hc(this.node.preview, "edit")) {
			u.e.kill(event);
		}
	}
	node.update_name_form.clicked = function(event) {
		u.ac(this.node.preview, "edit");
		this.inputs["name"].focus();
	}
	node.update_name_form.inputs["name"].blurred = function() {
		if(this.is_correct) {
			this._form.updateName();
		}
	}
	node.update_name_form.submitted = function() {
		this.inputs["name"].blur();
	}
	node.update_name_form.updateName = function() {
		u.rc(this.node.preview, "edit");
		this.response = function(response) {
			page.notify(response);
			if(response.cms_status !== "success") {
				this.inputs["name"].val(this.node.media_name);
			}
		}
		u.request(this, this.action, {"method":this.method, "data":this.getData()});
	}
}


/*m-default_comments.js*/
Util.Modules["defaultComments"] = new function() {
	this.init = function(div) {
		div.item_id = u.cv(div, "item_id");
		div.delete_comment_url = div.getAttribute("data-comment-delete");
		div.update_comment_url = div.getAttribute("data-comment-update");
		div.csrf_token = div.getAttribute("data-csrf-token");
		div._comments_form = u.qs("form", div);
		div._comments_list = u.qs("ul.comments", div);
		if(div._comments_form) {
			div._comments_form.div = div;
			u.f.init(div._comments_form);
			div.add_comment_url = div._comments_form.action;
			div._comments_form.submitted = function(iN) {
				this.response = function(response) {
					page.notify(response);
					if(response.cms_status == "success" && response.cms_object) {
						var comment_li = u.ae(this.div._comments_list, "li", {"class":"comment comment_id:"+response.cms_object["id"]});
						var info = u.ae(comment_li, "ul", {"class":"info"});
						u.ae(info, "li", {"class":"user", "html":response.cms_object["nickname"]});
						u.ae(info, "li", {"class":"created_at", "html":response.cms_object["created_at"]});
						u.ae(comment_li, "p", {"class":"comment", "html":response.cms_object["comment"]})
						this.div.initComment(comment_li);
						this.inputs["item_comment"].val("");
					}
				}
				u.bug("defaultComments save");
				u.request(this, this.action, {"method":"post", "data" : this.getData()});
			}
		}
		div.initComment = function(node) {
			node.div = this;
			if(this.delete_comment_url || this.update_comment_url) {
				var actions = u.ae(node, "ul", {"class":"actions"});
				var li;
				if(this.update_comment_url) {
					li = u.ae(actions, "li", {"class":"edit"});
					bn_edit = u.ae(li, "a", {"html":"Edit", "class":"button edit"});
					bn_edit.node = node;
					u.ce(bn_edit);
					bn_edit.clicked = function() {
						var actions, bn_cancel, bn_update, form;
						form = u.f.addForm(this.node, {"action":this.node.div.update_comment_url+"/"+this.node.div.item_id+"/"+u.cv(this.node, "comment_id"), "class":"edit"});
						u.ae(form, "input", {"type":"hidden","name":"csrf-token", "value":this.node.div.csrf_token});
						u.f.addField(form, {"type":"text", "name":"item_comment", "value": u.qs("p.comment", this.node).innerHTML});
						form.node = node;
						actions = u.ae(form, "ul", {"class":"actions"});
						bn_update = u.f.addAction(actions, {"value":"Update", "class":"button primary update", "name":"update"});
						bn_cancel = u.f.addAction(actions, {"value":"Cancel", "class":"button cancel", "type":"button", "name":"cancel"});
						u.f.init(form);
						form.submitted = function() {
							this.response = function(response) {
								page.notify(response);
								if(response.cms_status == "success") {
									u.qs("p.comment", this.node).innerHTML = this.inputs["item_comment"].val();
									this.parentNode.removeChild(this);
								}
							}
							u.request(this, this.action, {"method":"post", "data":this.getData()});
						}
						u.ce(bn_cancel);
						bn_cancel.clicked = function(event) {
							u.e.kill(event);
							this.form.parentNode.removeChild(this.form);
						}
					}
				}
				if(this.delete_comment_url) {
					li = u.ae(actions, "li", {"class":"delete"});
					var form = u.f.addForm(li, {"action":this.delete_comment_url+"/"+this.item_id+"/"+u.cv(node, "comment_id"), "class":"delete"});
					u.ae(form, "input", {"type":"hidden","name":"csrf-token", "value":this.csrf_token});
					form.node = node;
					bn_delete = u.f.addAction(form, {"value":"Delete", "class":"button delete", "name":"delete"});
					u.f.init(form);
					form.restore = function(event) {
						this.actions["delete"].value = "Delete";
						u.rc(this.actions["delete"], "confirm");
					}
					form.submitted = function() {
						if(!u.hc(this.actions["delete"], "confirm")) {
							u.ac(this.actions["delete"], "confirm");
							this.actions["delete"].value = "Confirm";
							this.t_confirm = u.t.setTimer(this, this.restore, 3000);
						}
						else {
							u.t.resetTimer(this.t_confirm);
							this.response = function(response) {
								page.notify(response);
								if(response.cms_status == "success") {
									if(response.cms_object && response.cms_object.constraint_error) {
										this.value = "Delete";
										u.ac(this, "disabled");
									}
									else {
										this.node.parentNode.removeChild(this.node);
									}
								}
							}
							u.request(this, this.action, {"method":"post", "data":this.getData()});
						}
					}
				}
			}
		}
		div.comments = u.qsa("li.comment", div._comments_list);
		var i, node;
		for(i = 0; node = div.comments[i]; i++) {
			div.initComment(node);
		}
	}
}


/*m-default_editors.js*/
Util.Modules["defaultEditors"] = new function() {
	this.init = function(div) {
		div.item_id = u.cv(div, "item_id");
		div.remove_editor_url = div.getAttribute("data-editor-remove");
		div.csrf_token = div.getAttribute("data-csrf-token");
		div._form_editors = u.qs("form.editors", div);
		div._list_editors = u.qs("ul.editors", div);
		if(div._form_editors) {
			div._form_editors.div = div;
			u.f.init(div._form_editors);
			div._form_editors.submitted = function(iN) {
				this.response = function(response) {
					page.notify(response);
					if(response.cms_status == "success" && response.cms_object && response.cms_object !== true) {
						if(!this.div._list_editors) {
							this.div._list_editors = u.ae(this.parentNode, "ul", {"class":"items editors"});
							this.parentNode.insertBefore(this.div._list_editors, this);
							var p_no_editors = u.qs("p", this.div);
							if(p_no_editors) {
								p_no_editors.parentNode.removeChild(p_no_editors);
							}
						}
						var editor_li = u.ae(this.div._list_editors, "li", {"class":"editor editor_id:"+response.cms_object["id"]});
						u.ae(editor_li, "h3", {"html":response.cms_object["nickname"]});
						this.div.initEditor(editor_li);
						this.reset();
					}
					else if(response.cms_status == "success" && response.cms_object && response.cms_object === true) {
						this.reset();
					}
				}
				u.request(this, this.action, {"method":"post", "data" : this.getData()});
			}
		}
		div.initEditor = function(node) {
			node.div = this;
			if(this.remove_editor_url) {
				node.editor_id = u.cv(node, "editor_id");
				node._ul_actions = u.ae(node, "ul", {"class":"actions"});
				node._li_remove = u.ae(node._ul_actions, "li", {"class":"remove"});
				node._form_remove = u.f.addForm(node._li_remove, {
					"action":this.remove_editor_url, 
					"class":"remove"
				});
				node._form_remove.node = node;
				u.f.addField(node._form_remove, {
					"type":"hidden",
					"name":"csrf-token", 
					"value":div.csrf_token
				});
				u.f.addField(node._form_remove, {
					"type":"hidden",
					"name":"editor_id", 
					"value":node.editor_id
				});
				u.f.addAction(node._form_remove, {
					"value":"Remove",
					"class":"button remove"
				});
				node._form_remove.setAttribute("data-success-function", "removed");
				node._form_remove.setAttribute("data-confirm-value", "Are you sure?");
				u.m.oneButtonForm.init(node._form_remove);
				node._form_remove.removed = function(response) {
					this.node.parentNode.removeChild(this.node);
				}
			}
		}
		div.editors = u.qsa("li.editor", div._list_editors);
		var i, node;
		for(i = 0; node = div.editors[i]; i++) {
			div.initEditor(node);
		}
	}
}


/*m-default_prices.js*/
Util.Modules["defaultPrices"] = new function() {
	this.init = function(div) {
		div.item_id = u.cv(div, "item_id");
		div.csrf_token = div.getAttribute("data-csrf-token");
		div.delete_price_url = div.getAttribute("data-price-delete");
		div._prices_form = u.qs("form", div);
		if(div._prices_form) {
			div._prices_form.div = div;
			div.add_price_url = div._prices_form.action;
			u.f.init(div._prices_form);
			div._prices_form.inputs["item_price_type"].changed = function() {
				if(this.val() == 3) {
					u.ac(this._form.inputs["item_price_quantity"].field, "required");
					u.ass(this._form.inputs["item_price_quantity"].field, {
						"display":"inline-block"
					})
				}
				else {
					u.rc(this._form.inputs["item_price_quantity"].field, "required");
					u.ass(this._form.inputs["item_price_quantity"].field, {
						"display":"none"
					})
				}
			}
			if(div._prices_form.inputs["item_price_type"].val() == 3) {
				u.ass(div._prices_form.inputs["item_price_quantity"].field, {
					"display":"inline-block"
				})
			}
			div._prices_form.submitted = function(iN) {
				this.response = function(response) {
					page.notify(response);
					if(response.cms_status == "success" && response.cms_object) {
						var price_li = u.ae(this.div._prices_list, "li", {"class":"pricedetails price_id:"+response.cms_object["id"]});
						var info = u.ae(price_li, "ul", {"class":"info"});
						u.ae(info, "li", {"class":"price", "html":response.cms_object["formatted_price"]});
						u.ae(info, "li", {"class":"vatrate", "html":response.cms_object["vatrate"]+"%"});
						if(response.cms_object["type"] == "offer") {
							u.ae(info, "li", {"class":"offer", "html":"Special offer"});
						}
						else if(response.cms_object["type"] == "bulk") {
							u.ae(info, "li", {"class":"bulk", "html":"Bulk price for "+response.cms_object["quantity"] + " items"});
						}
						else if(response.cms_object["type"] != "default") {
							u.ae(info, "li", {"class":"custom_price", "html":response.cms_object["description"]});
						}
						this.div.initPrice(price_li);
						this.reset();
					}
				}
				u.request(this, this.action, {"method":"post", "data" : this.getData()});
			}
		}
		div._prices_list = u.qs("ul.prices", div);
		div.initPrice = function(node) {
			node.div = this;
			if(this.delete_price_url) {
				var actions = u.ae(node, "ul", {"class":"actions"});
				var li;
				if(this.delete_price_url) {
					li = u.ae(actions, "li", {"class":"delete"});
					var form = u.f.addForm(li, {"action":this.delete_price_url+"/"+this.item_id+"/"+u.cv(node, "price_id"), "class":"delete"});
					u.ae(form, "input", {"type":"hidden","name":"csrf-token", "value":this.csrf_token});
					form.node = node;
					bn_delete = u.f.addAction(form, {"value":"Delete", "class":"button delete", "name":"delete"});
					u.f.init(form);
					form.restore = function(event) {
						this.actions["delete"].value = "Delete";
						u.rc(this.actions["delete"], "confirm");
					}
					form.submitted = function() {
						if(!u.hc(this.actions["delete"], "confirm")) {
							u.ac(this.actions["delete"], "confirm");
							this.actions["delete"].value = "Confirm";
							this.t_confirm = u.t.setTimer(this, this.restore, 3000);
						}
						else {
							u.t.resetTimer(this.t_confirm);
							this.response = function(response) {
								page.notify(response);
								if(response.cms_status == "success") {
									if(response.cms_object && response.cms_object.constraint_error) {
										this.value = "Delete";
										u.ac(this, "disabled");
									}
									else {
										this.node.parentNode.removeChild(this.node);
									}
								}
							}
							u.request(this, this.action, {"method":"post", "data":this.getData()});
						}
					}
				}
			}
		}
		div.prices = u.qsa("li.pricedetails", div._prices_list);
		var i, node;
		for(i = 0; node = div.prices[i]; i++) {
			div.initPrice(node);
		}
	}
}


/*m-default_subscriptionmethod.js*/
Util.Modules["defaultSubscriptionmethod"] = new function() {
	this.init = function(div) {
		div.item_id = u.cv(div, "item_id");
		div.csrf_token = div.getAttribute("data-csrf-token");
		div._sm_form = u.qs("form", div);
		div._sm_change_div = u.qs("div.change_subscription_method", div);
		div._sm_setting = u.qs("dl.info dd.subscription_method", div);
		if(div._sm_form) {
			div._sm_form.div = div;
			div.actions_change = u.ae(div, "ul", {"class":"actions change"});
			var li = u.ae(div.actions_change, "li", {"class":"change"});
			div.bn_change = u.ae(li, "a", {"class":"button primary", "html":"Change period"});
			div.bn_change.div = div;
			u.ce(div.bn_change);
			div.bn_change.clicked = function() {
				u.ass(this.div._sm_change_div, {
					"display":"block"
				});
				u.ass(this.div.actions_change, {
					"display":"none"
				});
			}
			u.f.init(div._sm_form);
			div._sm_form.submitted = function(iN) {
				this.response = function(response) {
					page.notify(response);
					if(response.cms_status == "success" && response.cms_object) {
						if(typeof(response.cms_object) == "object") {
							this.div._sm_setting.innerHTML = response.cms_object["name"];
						}
						else {
							this.div._sm_setting.innerHTML = "No renewal";
						}
						u.ass(this.div._sm_change_div, {
							"display":"none"
						});
						u.ass(this.div.actions_change, {
							"display":"block"
						});
					}
				}
				u.request(this, this.action, {"method":"post", "data" : this.getData()});
			}
		}
	}
}


/*m-default_sindex.js*/
Util.Modules["defaultSindex"] = new function() {
	this.init = function(div) {
		div.current_sindex = u.qs(".current_sindex", div);
		div.li_update = u.qs("li.update", div);
		div.li_update.div = div;
		div.li_update.confirmed = function(response) {
			if(this.div.current_sindex && response.cms_object) {
				this.div.current_sindex.innerHTML = response.cms_object;
			}
			if(this.div.current_sindex_input && response.cms_object) {
				this.div.current_sindex_input.val(response.cms_object);
			}
		}
		div.form_manual = u.qs("form.manual_sindex", div);
		if(div.form_manual) {
			div.form_manual.div = div;
			div.data_check_sindex = div.getAttribute("data-check-sindex");
			u.f.init(div.form_manual);
			div.current_sindex_input = div.form_manual.inputs["item_sindex"];
			div.form_manual.submitted = function(iN) {
				this.response = function(response) {
					page.notify(response);
					if(this.div.current_sindex && response.cms_object) {
						this.div.current_sindex.innerHTML = response.cms_object;
					}
					if(this.div.current_sindex_input && response.cms_object) {
						this.div.current_sindex_input.val(response.cms_object);
					}
				}
				u.request(this, this.action, {"method":"post", "data" : this.getData({"format":"formdata"})});
			}
			div.form_manual.updated = function() {
				u.t.resetTimer(this.t_check_sindex);
				this.t_check_sindex = u.t.setTimer(this, "checkSindex", 300);
			}
			div.form_manual.checkSindex = function() {
				if(this.div.data_check_sindex) {
					this.response = function(response) {
						if(response.cms_object) {
							u.f.inputIsCorrect(this.div.current_sindex_input);
						}
						else {
							u.f.inputHasError(this.div.current_sindex_input);
						}
					}
					u.request(this, div.data_check_sindex, {
						"data": this.getData(),
						"method": "post"
					});
				}
			}
		}
	}
}

/*m-default_owner.js*/
Util.Modules["defaultOwner"] = new function() {
	this.init = function(div) {
		div.form = u.qs("form", div);
		div.current_owner = u.qs(".current_owner", div);
		if(div.form) {
			div.form.div = div;
			u.f.init(div.form);
			div.form.submitted = function(iN) {
				this.response = function(response) {
					page.notify(response);
					if(this.div.current_owner && response.cms_object && response.cms_object["nickname"]) {
						this.div.current_owner.innerHTML = response.cms_object["nickname"];
					}
				}
				u.request(this, this.action, {"method":"post", "data" : this.getData({"format":"formdata"})});
			}
		}
	}
}

/*m-default_developer.js*/
Util.Modules["defaultDeveloper"] = new function() {
	this.init = function(div) {
		div.form = u.qs("form", div);
		div.form.div = div;
		if(div.form) {
			u.f.init(div.form);
			div.form.submitted = function(iN) {
				this.response = function(response) {
					page.notify(response);
				}
				u.request(this, this.action, {"method":"post", "data" : this.getData({"format":"formdata"})});
			}
		}
	}
}

/*m-event_tickets.js*/
Util.Modules["ticketEvent"] = new function() {
	this.init = function(div) {
		div.item_id = u.cv(div, "item_id");
		div.remove_event_url = div.getAttribute("data-event-remove");
		div.csrf_token = div.getAttribute("data-csrf-token");
		div._form_event = u.qs("form.event", div);
		div._list_events = u.qs("ul.events", div);
		if(div._form_event) {
			div._form_event.div = div;
			u.f.init(div._form_event);
			div._form_event.submitted = function(iN) {
				this.response = function(response) {
					page.notify(response);
					if(response.cms_status == "success" && response.cms_object) {
						if(!this.div._list_events) {
							this.div._list_events = u.ae(this.parentNode, "ul", {"class":"items events"});
							this.parentNode.insertBefore(this.div._list_events, this);
							var p_no_events = u.qs("p", this.div);
							if(p_no_events) {
								p_no_events.parentNode.removeChild(p_no_events);
							}
						}
						this.div._list_events.innerHTML = "";
						var event_li = u.ae(this.div._list_events, "li", {"class":"event event_id:"+response.cms_object["event_id"]});
						u.ae(event_li, "h3", {"html":response.cms_object["event_name"]});
						this.div.initEvent(event_li);
						this.reset();
					}
				}
				u.request(this, this.action, {"method":"post", "data" : this.getData()});
			}
		}
		div.initEvent = function(node) {
			node.div = this;
			if(this.remove_event_url) {
				node.event_id = u.cv(node, "event_id");
				node._ul_actions = u.ae(node, "ul", {"class":"actions"});
				node._li_remove = u.ae(node._ul_actions, "li", {"class":"remove"});
				node._form_remove = u.f.addForm(node._li_remove, {
					"action":this.remove_event_url, 
					"class":"remove"
				});
				node._form_remove.node = node;
				u.f.addField(node._form_remove, {
					"type":"hidden",
					"name":"csrf-token", 
					"value":div.csrf_token
				});
				u.f.addField(node._form_remove, {
					"type":"hidden",
					"name":"event_id", 
					"value":node.event_id
				});
				u.f.addAction(node._form_remove, {
					"value":"Remove from event",
					"class":"button remove"
				});
				node._form_remove.setAttribute("data-success-function", "removed");
				node._form_remove.setAttribute("data-confirm-value", "Are you sure?");
				u.m.oneButtonForm.init(node._form_remove);
				node._form_remove.removed = function(response) {
					this.node.parentNode.removeChild(this.node);
				}
			}
		}
		div.events = u.qsa("li.event", div._list_events);
		var i, node;
		for(i = 0; node = div.events[i]; i++) {
			div.initEvent(node);
		}
	}
}
Util.Modules["eventTickets"] = new function() {
	this.init = function(div) {
		div.item_id = u.cv(div, "item_id");
		div.remove_ticket_url = div.getAttribute("data-ticket-remove");
		div.csrf_token = div.getAttribute("data-csrf-token");
		div._form_ticket = u.qs("form.ticket", div);
		div._list_tickets = u.qs("ul.tickets", div);
		if(div._form_ticket) {
			div._form_ticket.div = div;
			u.f.init(div._form_ticket);
			div._form_ticket.submitted = function(iN) {
				this.response = function(response) {
					page.notify(response);
					if(response.cms_status == "success" && response.cms_object) {
						if(!this.div._list_tickets) {
							this.div._list_tickets = u.ae(this.parentNode, "ul", {"class":"items tickets"});
							this.parentNode.insertBefore(this.div._list_tickets, this);
							var p_no_tickets = u.qs("p", this.div);
							if(p_no_tickets) {
								p_no_tickets.parentNode.removeChild(p_no_tickets);
							}
						}
						var event_li = u.ae(this.div._list_tickets, "li", {"class":"ticket ticket_id:"+response.cms_object["ticket_id"]});
						u.ae(event_li, "h3", {"html":response.cms_object["ticket_name"]});
						this.inputs["item_ticket"].removeChild(this.inputs["item_ticket"].options[this.inputs["item_ticket"].selectedIndex]);
						this.div.initTicket(event_li);
						this.reset();
					}
				}
				u.request(this, this.action, {"method":"post", "data" : this.getData()});
			}
		}
		div.initTicket = function(node) {
			node.div = this;
			if(this.remove_ticket_url) {
				node.ticket_id = u.cv(node, "ticket_id");
				node._ul_actions = u.ae(node, "ul", {"class":"actions"});
				node._li_remove = u.ae(node._ul_actions, "li", {"class":"remove"});
				node._form_remove = u.f.addForm(node._li_remove, {
					"action":this.remove_ticket_url, 
					"class":"remove"
				});
				node._form_remove.node = node;
				u.f.addField(node._form_remove, {
					"type":"hidden",
					"name":"csrf-token", 
					"value":div.csrf_token
				});
				u.f.addField(node._form_remove, {
					"type":"hidden",
					"name":"ticket_id", 
					"value":node.ticket_id
				});
				u.f.addAction(node._form_remove, {
					"value":"Remove ticket",
					"class":"button remove"
				});
				node._form_remove.setAttribute("data-success-function", "removed");
				node._form_remove.setAttribute("data-confirm-value", "Are you sure?");
				u.m.oneButtonForm.init(node._form_remove);
				node._form_remove.removed = function(response) {
					u.ae(this.node.div._form_ticket.inputs["item_ticket"], "option", {"html": node.innerHTML, "value": node.ticket_id});
					this.node.parentNode.removeChild(this.node);
				}
			}
		}
		div.tickets = u.qsa("li.ticket", div._list_tickets);
		var i, node;
		for(i = 0; node = div.tickets[i]; i++) {
			div.initTicket(node);
		}
	}
}


/*m-navigations.js*/
Util.Modules["navigationNodes"] = new function() {
	this.init = function(div) {
		div.list = u.qs("ul.items", div);
		if(div.list) {
			div.list.update_order_url = div.getAttribute("data-item-order");
			div.list.csrf_token = div.getAttribute("data-csrf-token");
			div.list.nodes = u.qsa("li.item", div.list);
			var i, node;
			for(i = 0; node = div.list.nodes[i]; i++) {
				node.list = div.list;
				node.bn_delete = u.qs("li.delete", node);
				if(node.bn_delete) {
					node.bn_delete.node = node;
					node.bn_delete.confirmed = function(response) {
						this.node.parentNode.removeChild(this.node);
						this.node.list.updateNodeStructure();
					}
					var child_nodes = u.qs("ul.items li.item", node);
					var bn_delete_input =  u.qs("ul.actions li.delete input[type=submit]", node);
					if(child_nodes && bn_delete_input) {
						u.ac(bn_delete_input, "disabled");
					}
				}
			}
			div.list.dropped = function(event) {
				this.updateNodeStructure();
			}
			div.list.updateNodeStructure = function() {
				u.bug("updateNodeStructure");
				var structure = this.getNodeRelations();
				u.bug(structure);
				this.response = function(response) {
					page.notify(response);
				}
				u.request(this, this.update_order_url, {"method":"post", "data":"csrf-token="+this.csrf_token+"&structure="+JSON.stringify(structure)});
				var i, node;
				this.nodes = u.qsa("li.item", this);
				for(i = 0; node = this.nodes[i]; i++) {
					var child_nodes = u.qs("ul.items li.item", node);
					var bn_delete_input =  u.qs("ul.actions li.delete input[type=submit]", node);
					if(child_nodes && bn_delete_input) {
						u.ac(bn_delete_input, "disabled");
					}
					else {
						u.rc(bn_delete_input, "disabled");
					}
				}
			}
			u.sortable(div.list, {"allow_nesting":true, "targets":".items", "draggables":".draggable"});
		}
	}
}
Util.Modules["newNavigationNode"] = new function() {
	this.init = function(form) {
		u.f.init(form);
		form.submitted = function(iN) {
			this.response = function(response) {
				if(response.cms_status == "success" && response.cms_object) {
					location.href = this.actions["cancel"].url;
				}
				else {
					page.notify(response);
				}
			}
			u.request(this, this.action, {"method":"post", "data" : this.getData({"format":"formdata"})});
		}
	}
}
Util.Modules["editNavigationNode"] = new function() {
	this.init = function(div) {
		div._item_id = u.cv(div, "item_id");
		var form = u.qs("form", div);
		form.div = div;
		u.f.init(form);
		form.submitted = function(iN) {
			u.t.resetTimer(page.t_autosave);
			this.response = function(response) {
				page.notify(response);
			}
			u.request(this, this.action, {"method":"post", "data" : this.getData({"format":"formdata"})});
		}
		form.cancelBackspace = function(event) {
			if(event.keyCode == 8 && !u.qsa(".field.focus").length) {
				u.e.kill(event);
			}
		}
		u.e.addEvent(document.body, "keydown", form.cancelBackspace);
	}
}

/*m-users.js*/
Util.Modules["usernames"] = new function() {
	this.init = function(div) {
		var form;
		form = u.qs("form.email", div);
		u.f.init(form);
		var indicators = u.qsa(".indicator", form);
		u.ass(indicators[0], {"display":"none"});
		u.ass(indicators[1], {"display":"none"});
		var latest_verification_link = u.qs("div.email .send_verification_link p.reminded_at", div);
		latest_verification_link.date_time = u.qs("span.date_time", latest_verification_link);
		var send_verification_link = u.qs("li.send_verification_link", div);
		if(send_verification_link) {
			send_verification_link.form = u.qs("form", send_verification_link);
			send_verification_link.input = send_verification_link.form.lastChild;
		}
		else {
			u.as(latest_verification_link, "display", "none");
		}
		form.inputs.email.saved_email = form.inputs.email.val();
		form.inputs.verification_status.saved_status = form.inputs.verification_status.val();
		form.inputs.verification_status.current_status = form.inputs.verification_status.val();
		if(u.hc(latest_verification_link.date_time, "never")) {
			u.ass(latest_verification_link, {"display":"none"});
		}
		if (!form.inputs.email.saved_email) {
			form.inputs.verification_status.disabled = true;
			if(send_verification_link) {
				u.ac(send_verification_link.input, "disabled");
			}
		}
		else if( form.inputs["verification_status"].val()) {
			if(send_verification_link) {
				u.ac(send_verification_link.input, "disabled");
			}
		}
		else {
			form.inputs.verification_status.disabled = false;
			if(send_verification_link) {
				u.rc(send_verification_link.input, "disabled");
			}
		}
		form.inputs.email.updated = function() {
			if(this.val() != this.saved_email) {
				this._form.inputs.verification_status.val(0);
				u.ac(this._form.actions["save"], "primary");
				u.rc(this._form.actions["save"], "disabled");
				if(this.val()) {
					this._form.inputs.verification_status.disabled = false;
				}
				else {
					this._form.inputs.verification_status.disabled = true;
				}
			}
			else {
				this._form.inputs.verification_status.val(this._form.inputs.verification_status.current_status);
				u.ac(this._form.actions["save"], "disabled");
			}
		}
		form.inputs.verification_status.updated = function() {
			if(this.val() != this.saved_status) {		
				this.current_status = this.val();
				u.ac(this._form.actions["save"], "primary");
				u.rc(this._form.actions["save"], "disabled");
			}
			else if(this._form.inputs.email.val() != this._form.inputs.email.saved_email) {
				this.current_status = this.val();
				u.ac(this._form.actions["save"], "primary");
				u.rc(this._form.actions["save"], "disabled");
			}
			else {
				u.ac(this._form.actions["save"], "disabled");
			}
		}
		if(send_verification_link) {
			send_verification_link.confirmed = function(response) {
				if(!latest_verification_link) {
					latest_verification_link = u.qs("div.email .send_verification_link p.reminded_at", div);
					latest_verification_link.date_time = u.qs("span.date_time", latest_verification_link);
				}
				latest_verification_link.date_time.textContent = response.cms_object.reminded_at;
				u.ass(latest_verification_link, {"display":"block"});
				u.rc(send_verification_link, "invite");
				u.rc(send_verification_link.input, "invite");
				u.ac(send_verification_link, "reminder");
				u.ac(send_verification_link.input, "reminder");
				send_verification_link.input.value = "Send reminder";
				send_verification_link.form[1].value = "signup_reminder";
			}
		}
		form.submitted = function(iN) {
			if(!latest_verification_link) {
				latest_verification_link = u.qs("div.email .send_verification_link p span.date_time", div);
			}
			this.response = function(response) {
				if(response.cms_status == "error") {
					u.f.inputHasError(this.inputs["email"]);
				}
				else {
					this.inputs.email.saved_email = this.inputs.email.val();
					this.inputs.verification_status.saved_status = this.inputs.verification_status.val();
					u.ac(this.actions["save"], "disabled");
					if(response.cms_object.email_status == "UPDATED") {
						this.inputs.username_id.val(response.cms_object.username_id);
						if(send_verification_link.form.action.match(/sendVerificationLink\/$/)) {
							send_verification_link.form.action += this.inputs.username_id.val();
						}
						if(response.cms_object.verification_status == "VERIFIED") {
							u.ac(send_verification_link.input, "disabled");
							u.rc(this.actions["save"], "primary");
						}
						else if(response.cms_object.verification_status == "NOT_VERIFIED") {
							u.rc(send_verification_link.input, "disabled");
							u.rc(this.actions["save"], "primary");
						}
					}
					else if(response.cms_object.email_status == "UNCHANGED") {
						if(response.cms_object.verification_status == "VERIFIED") {
							u.ac(send_verification_link.input, "disabled");
							u.rc(this.actions["save"], "primary");
						}
						else if(response.cms_object.verification_status == "NOT_VERIFIED") {
							u.rc(send_verification_link.input, "disabled");
							u.rc(this.actions["save"], "primary");
						}
						response.cms_message.message.shift();
					}
					else {
						u.ac(send_verification_link.input, "disabled");
						u.rc(this.actions["save"], "primary");
						send_verification_link.form.action = "/janitor/admin/user/sendVerificationLink/"
						u.ass(latest_verification_link, {"display":"none"});
						u.rc(send_verification_link, "reminder");
						u.ac(send_verification_link, "invite");
						send_verification_link.input.value = "Send invite";
						send_verification_link.form[1].value = "verify_new_email";
					}
					page.notify(response);
				}
			}
			u.request(this, this.action, {"method":"post", "data" : this.getData()});
		}
		form = u.qs("form.mobile", div);
		u.f.init(form);
		form.updated = function() {
			u.ac(this.actions["save"], "primary");
		}
		form.submitted = function(iN) {
			this.response = function(response) {
				page.notify(response);
				if(response.cms_status == "error") {
					u.f.inputHasError(this.inputs["mobile"]);
				}
				else {
					u.rc(this.actions["save"], "primary");
				}
			}
			u.request(this, this.action, {"method":"post", "data" : this.getData()});
		}
	}
}
Util.Modules["password"] = new function() {
	this.init = function(div) {
		var password_state = u.qs("div.password_state", div);
		var new_password = u.qs("div.new_password", div);
		var a_create = u.qs(".password_missing a");
		var a_change = u.qs(".password_set a");
		a_create._new_password = new_password;
		a_change._new_password = new_password;
		a_create._password_state = password_state;
		a_change._password_state = password_state;
		u.ce(a_create);
		u.ce(a_change);
		a_create.clicked = a_change.clicked = function() {
			u.as(this._new_password, "display", "block");
			u.as(this._password_state, "display", "none");
		}
		var form = u.qs("form", div);
		form._password_state = password_state;
		form._new_password = new_password;
		u.f.init(form);
		form.actions["cancel"].clicked = function() {
			u.as(this.form._password_state, "display", "block");
			u.as(this.form._new_password, "display", "none");
		}
		form.submitted = function(iN) {
			this.response = function(response) {
				if(response.cms_status == "success") {
					u.ac(this._password_state, "set");
					u.as(this._password_state, "display", "block");
					u.as(this._new_password, "display", "none");
				}
				page.notify(response);
			}
			u.request(this, this.action, {"method":"post", "data" : this.getData()});
			this.reset();
		}
	}
}
Util.Modules["apitoken"] = new function() {
	this.init = function(div) {
		var token = u.qs("p.token", div);
		var renew_form = u.qs("form.renew", div);
		var disable_form = u.qs("form.disable", div);
		if(renew_form) {
			renew_form._token = token;
			if(disable_form) {
				renew_form.disable_form = disable_form;
			}
			u.f.init(renew_form);
			renew_form.submitted = function(iN) {
				this.response = function(response) {
					if(response.cms_status == "success") {
						this._token.innerHTML = response.cms_object;
						if(this.disable_form) {
							u.rc(this.disable_form.actions["disable"], "disabled");
						}
						page.notify({"isJSON":true, "cms_status":"success", "cms_message":"API token updated"});
					}
					else {
						page.notify({"isJSON":true, "cms_status":"error", "cms_message":"API token could not be updated"});
					}
				}
				u.request(this, this.action, {"method":"post", "data" : this.getData()});
			}
		}
		if(disable_form) {
			disable_form._token = token;
			u.f.init(disable_form);
			disable_form.submitted = function(iN) {
				this.response = function(response) {
					if(response.cms_status == "success") {
						this._token.innerHTML = "N/A";
						u.ac(this.actions["disable"], "disabled");
						page.notify({"isJSON":true, "cms_status":"success", "cms_message":"API token disabled"});
					}
					else {
						page.notify({"isJSON":true, "cms_status":"error", "cms_message":"API token could not be disabled"});
					}
				}
				u.request(this, this.action, {"method":"post", "data" : this.getData()});
			}
		}
	}
}
Util.Modules["editAddress"] = new function() {
	this.init = function(form) {
		u.f.init(form);
		form.actions["cancel"].clicked = function(event) {
			location.href = this.url;
		}
		form.submitted = function(iN) {
			this.response = function(response) {
				page.notify(response);
				if(response.cms_status == "success") {
					location.href = this.actions["cancel"].url;
				}
			}
			u.request(this, this.action, {"method":"post", "data" : this.getData()});
		}
	}
}
Util.Modules["customPrice"] = new function() {
	this.init = function(div) {
		div.info_price = u.qs("div.item dl.info span.price");
		div.custom_price = u.qs("h3.current_price span.price");
		var form = u.qs("form", div);
		if(form) {
			form.div = div;
			u.f.init(form);
			form.submitted = function(iN) {
				this.response = function(response) {
					page.notify(response);
					if(response.cms_status == "success") {
						if(this.div.info_price) {
							this.div.info_price.innerHTML = response.cms_object;
						}
						if(this.div.custom_price) {
							this.div.custom_price.innerHTML = response.cms_object;
						}
					}
				}
				u.request(this, this.action, {"method":"post", "data" : this.getData()});
			}
		}
	}
}
Util.Modules["maillists"] = new function() {
	this.init = function(div) {
		var i, node;
		div.maillists = u.qsa("ul.maillists > li", div);
		for(i = 0; node = div.maillists[i]; i++) {
			node.li_unsubscribe = u.qs("li.unsubscribe", node);
			node.li_subscribe = u.qs("li.subscribe", node);
			if(node.li_unsubscribe) {
				node.li_unsubscribe.node = node;
				node.li_unsubscribe.confirmed = function(response) {
					if(response.cms_status == "success") {
						u.rc(this.node, "subscribed");
					}
				}
			}
			if(node.li_subscribe) {
				node.li_subscribe.node = node;
				node.li_subscribe.confirmed = function(response) {
					if(response.cms_status == "success") {
						u.ac(this.node, "subscribed");
					}
				}
			}
		}
	}
}
Util.Modules["accessEdit"] = new function() {
	this.init = function(div) {
		div._item_id = u.cv(div, "item_id");
		var form = u.qs("form", div);
		u.f.init(form);
		form.actions["cancel"].clicked = function(event) {
			location.href = this.url;
		}
		form.submitted = function(iN) {
			this.response = function(response) {
				page.notify(response);
			}
			u.request(this, this.action, {"method":"post", "data" : this.getData()});
		}
		var i, group;
		var groups = u.qsa("li.action", form);
		for(i = 0; group = groups[i]; i++) {
			var h3 = u.qs("h3", group);
			h3.group = group;
			u.ce(h3)
			h3.clicked = function() {
				var i, input;
				var inputs = u.qsa("input[type=checkbox]", this.group);
				for(i = 0; input = inputs[i]; i++) {
					input.val(1);
				}
			}
		}
	}
}
Util.Modules["flushUserSession"] = new function() {
	this.init = function(div) {
		u.bug("div flushUserSession")
		div.csrf_token = div.getAttribute("data-csrf-token");
		div.flush_url = div.getAttribute("data-flush-url");
		var users = u.qsa("li.item:not(.current_user)", div);
		var i, user;
		for(i = 0; user = users[i]; i++) {
			var action = u.f.addAction(u.qs("ul.actions", user), {"type":"button", "class":"button", "value":"Flush"});
			action.div = div;
			action.user_id = u.cv(user, "user_id");
			u.ce(action);
			action.clicked = function() {
				this.response = function(response) {
					page.notify(response);
				}
				u.request(this, this.div.flush_url+"/"+this.user_id, {"method":"post", "data" : "csrf-token="+this.div.csrf_token});
			}
		}
	}
}
Util.Modules["unverifiedUsernames"] = new function() {
	this.init = function(div) {
		var i, node;
		u.bug("div", div)
		div.bn_remind_selected = u.qs("li.remind_selected input[type=submit]");
		div.selectionUpdated = function(inputs) {
			u.bug("inputs", inputs, this.bn_remind_selected);
			if(inputs.length > 0) {
				u.rc(this.bn_remind_selected, "disabled");
			}
			else {
				u.ac(this.bn_remind_selected, "disabled");
			}
			this.selected_username_ids = [];
			for(i = 0; i < inputs.length; i++) {
				node = inputs[i].node;
				node.username_id = u.cv(node, "username_id");
				this.selected_username_ids.push(node.username_id);
			}
			this.selected_username_ids = this.selected_username_ids.join();
			this.bn_remind_selected._form.inputs.selected_username_ids.val(this.selected_username_ids);
		}
		for(i = 0; node = div.nodes[i]; i++) {
			node.bn_remind = u.qs("ul.actions li.remind", node);
			node.bn_remind.node = node;
			node.bn_remind.reminded = function(response) {
				if(this.parentNode) {
					this.parentNode.removeChild(this);
				}
				if(this.node._checkbox.parentNode) {
					this.node._checkbox.parentNode.removeChild(this.node._checkbox);
				}
				if(response.cms_status == "success") {
					var reminded_at = u.qs("dd.reminded_at", this.node);
					var total_reminders = u.qs("dd.total_reminders", this.node);
					reminded_at.innerHTML = response.cms_object["reminded_at"] + " (just now)";
					u.ac(reminded_at, "system_warning");
					total_reminders.innerHTML = response.cms_object["total_reminders"];
					u.ac(total_reminders, "system_warning");
				}
				else {
					page.notify({"cms_status":"error", "cms_message":{"error":["Could not send message"]}, "isJSON":true});
				}
			}
		}
	}
}
Util.Modules["unverifiedUsernamesSelected"] = new function() {
	this.init = function(ul) {
		var bn_remind_selected = u.qs("li.remind_selected", ul);
		bn_remind_selected.reminded = function(response) {
			var obj, i, node;
			if(response.cms_status == "success") {
				for(i = 0; i < response.cms_object.length; i++) {
					obj = response.cms_object[i];
					node = u.ge("username_id:" + obj.username_id);
					if(node) {
						node.bn_remind.reminded({"cms_status":"success", "cms_object":obj});
					}
				}
			}
		}
	}
}

/*m-shop.js*/
Util.Modules["editDataSection"] = new function() {
	this.init = function(div) {
		var header = u.qs("h2", div);
		var form = u.qs("form", div);
		if(header && form) {
			var action = u.ae(header, "span", {"html":"edit"});
			action.change_form = form;
			u.ce(action);
			u.f.init(form);
			action.clicked = function(event) {
				if(this.change_form.is_open) {
					this.change_form.is_open = false;
					this.innerHTML = "Edit";
					this.change_form.reset();
					u.ass(this.change_form, {
						"display":"none"
					})
				}
				else {
					this.change_form.is_open = true;
					this.innerHTML = "Cancel";
					u.ass(this.change_form, {
						"display":"block"
					})
					u.f.init(this.change_form);
				}
			}
			form.submitted = function() {
				this.response = function(response) {
					page.notify(response);
					if(response && response.cms_status == "success") {
						location.reload(true);
					}
				}
				u.request(this, this.action, {"method":"post", "data":this.getData()});
			}
		}
	}
}
Util.Modules["newOrderFromCart"] = new function() {
	this.init = function(div) {
		var bn_convert = u.qs("li.convert", div);
		if(bn_convert) {
			bn_convert.confirmed = function(response) {
				u.bug("confirmed checkout")
				if(response.cms_status == "success" && response.cms_object) {
					location.href = location.href.replace(/\/cart\/edit\/.+/, "/order/edit/"+response.cms_object["id"]);
				}
			}
		}
	}
}
Util.Modules["cartItemsList"] = new function() {
	this.init = function(div) {
		u.bug("cartItemsList");
		div.total_cart_price = u.qs("dd.total_cart_price");
		var i, node;
		for(i = 0; node = div.nodes[i]; i++) {
			node.unit_price = u.qs("span.unit_price", node);
			node.total_price = u.qs("span.total_price", node);
			var quantity_form = u.qs("form.updateCartItemQuantity", node)
			if(quantity_form) {
				quantity_form.node = node;
				u.f.init(quantity_form);
				quantity_form.inputs["quantity"].updated = function() {
					u.ac(this._form.actions["update"], "primary");
					this._form.submit();
				}
				quantity_form.submitted = function() {
					this.response = function(response) {
						page.notify(response);
						if(response && response.cms_status == "success") {
							this.node.unit_price.innerHTML = response.cms_object["unit_price_formatted"];
							this.node.total_price.innerHTML = response.cms_object["total_price_formatted"];
							this.node.div.total_cart_price.innerHTML = response.cms_object["total_cart_price_formatted"];
				 			u.rc(this.actions["update"], "primary");
						}
					}
					u.request(this, this.action, {"method":"post", "data":this.getData()});
				}
			}
			var bn_delete = u.qs("ul.actions li.delete", node);
			if(bn_delete) {
				bn_delete.node = node;
				bn_delete.deletedFromCart = function(response) {
					if(response && response.cms_status == "success") {
						this.node.div.total_cart_price.innerHTML = response.cms_object["total_cart_price_formatted"];
					}
					this.confirmed(response);
				}
			}
			// 
			// 	
		}
	}
}
Util.Modules["orderItemsList"] = new function() {
	this.init = function(div) {
		u.bug("orderItemsList");
		div.total_order_price = u.qs("dd.total_order_price");
		div.order_status = u.qs("dd.status");
		div.payment_status = u.qs("dd.payment_status");
		div.shipping_status = u.qs("dd.shipping_status");
		var i, node;
		for(i = 0; node = div.nodes[i]; i++) {
			node.unit_price = u.qs("span.unit_price", node);
			node.total_price = u.qs("span.total_price", node);
			var quantity_form = u.qs("form.updateOrderItemQuantity", node)
			if(quantity_form) {
				quantity_form.node = node;
				u.f.init(quantity_form);
				quantity_form.inputs["quantity"].updated = function() {
					u.ac(this._form.actions["update"], "primary");
					this._form.submit();
				}
				quantity_form.submitted = function() {
					this.response = function(response) {
						page.notify(response);
						if(response && response.cms_status == "success") {
							this.node.unit_price.innerHTML = response.cms_object["unit_price_formatted"];
							this.node.total_price.innerHTML = response.cms_object["total_price_formatted"];
							this.node.div.total_order_price.innerHTML = response.cms_object["total_order_price_formatted"];
				 			u.rc(this.actions["update"], "primary");
						}
					}
					u.request(this, this.action, {"method":"post", "data":this.getData()});
				}
			}
			var bn_delete = u.qs("ul.actions li.delete", node);
			if(bn_delete) {
				bn_delete.node = node;
				bn_delete.deletedFromOrder = function(response) {
					if(response && response.cms_status == "success") {
						this.node.div.total_order_price.innerHTML = response.cms_object["total_order_price_formatted"];
					}
					this.confirmed(response);
				}
			}
			node.li_shipped = u.qs("ul.actions li.shipped", node);
			u.bug("node.li_shipped:" + node.li_shipped)
			if(node.li_shipped) {
				node.li_shipped.node = node;
				u.m.oneButtonForm.init(node.li_shipped);
				node.li_shipped.confirmed = function(response) {
					if(response.cms_status == "success") {
						if(this.node.div.order_status.innerHTML != response.cms_object["order_status_text"]) {
							location.reload(true);
						}
						this.node.div.order_status.innerHTML = response.cms_object["order_status_text"];
						this.node.div.shipping_status.innerHTML = response.cms_object["shipping_status_text"];
						this.node.div.payment_status.innerHTML = response.cms_object["payment_status_text"];
						u.rc(this.node, "shipped");
					}
				}
			}
			node.not_shipped = u.qs("ul.actions li.not_shipped", node);
			if(node.not_shipped) {
				node.not_shipped.node = node;
				u.m.oneButtonForm.init(node.not_shipped);
				node.not_shipped.confirmed = function(response) {
					if(response.cms_status == "success") {
						if(this.node.div.order_status.innerHTML != response.cms_object["order_status_text"]) {
							location.reload(true);
						}
						this.node.div.order_status.innerHTML = response.cms_object["order_status_text"];
						this.node.div.shipping_status.innerHTML = response.cms_object["shipping_status_text"];
						this.node.div.payment_status.innerHTML = response.cms_object["payment_status_text"];
						u.ac(this.node, "shipped");
					}
				}
			}
		}
	}
}
Util.Modules["orderList"] = new function() {
	this.init = function(div) {
		u.bug("orderList", div.nodes);
		div.pending_count = u.qs("ul.tab li.pending span", div);
		div.waiting_count = u.qs("ul.tab li.waiting span", div);
		div.complete_count = u.qs("ul.tab li.complete span", div);
		div.cancelled_count = u.qs("ul.tab li.cancelled span", div);
		div.all_count = u.qs("ul.tab li.all span", div);
		var i, node, bn_ship;
		for(i = 0; i < div.nodes.length; i++) {
			node = div.nodes[i];
			bn_ship = u.qs("ul.actions li.ship", node);
			if(bn_ship) {
				bn_ship.node = node;
				node.bn_ship = bn_ship;
				bn_ship.dd_shipping_status = u.qs("dd.shipping_status", node);
				bn_ship.confirmed = function(response) {
					if(response.cms_status === "success") {
						if(!this.node.bn_capture || u.hc(div, "pending")) {
							this.node.transitioned = function() {
								this.transitioned = function() {
									this.parentNode.removeChild(this);
								}
								u.a.transition(this, "all 0.3s ease-in-out");
								u.ass(this, {
									height: 0,
								});
							}
							u.a.transition(this.node, "all 0.3s ease-in-out");
							u.ass(this.node, {
								opacity: 0,
								height: this.node.offsetHeight+"px",
							});
						}
						else {
							delete this.node.bn_ship;
							this.parentNode.removeChild(this);
							this.dd_shipping_status.innerHTML = "Shipped";
							u.rc(this.dd_shipping_status, "unshipped");
							u.ac(this.dd_shipping_status, "shipped");
						}
					}
				}
			}
			bn_capture = u.qs("ul.actions li.capture", node);
			if(bn_capture) {
				bn_capture.node = node;
				node.bn_capture = bn_capture;
				bn_capture.dd_payment_status = u.qs("dd.payment_status", node);
				bn_capture.confirmed = function(response) {
					if(response.cms_status == "success") {
						if(!this.node.bn_ship || u.hc(div, "pending")) {
							this.node.transitioned = function() {
								this.transitioned = function() {
									this.parentNode.removeChild(this);
								}
								u.a.transition(this, "all 0.3s ease-in-out");
								u.ass(this, {
									height: 0,
								});
							}
							u.a.transition(this.node, "all 0.3s ease-in-out");
							u.ass(this.node, {
								opacity: 0,
								height: this.node.offsetHeight+"px",
							});
						}
						else {
							delete this.node.bn_capture;
							this.parentNode.removeChild(this);
							this.dd_payment_status.innerHTML = "Paid";
							u.rc(this.dd_payment_status, "unpaid");
							u.ac(this.dd_payment_status, "paid");
						}
					}
				}
			}
		}
	}
}
Util.Modules["capturePaymentNew"] = new function() {
	this.init = function(div) {
		var bn_capture = u.qs("li.capture", div)
		if(bn_capture) {
			var bn_back = u.qs("li.back a")
			bn_capture.bn_back = bn_back;
			bn_capture.confirmed = function(response) {
				if(response.cms_status == "success") {
					location.href = this.bn_back.href;
				}
			}
		}
	}
}


/*m-system.js*/
Util.Modules["cacheList"] = new function() {
	this.init = function(div) {
		u.bug("div cacheList")
		div.csrf_token = div.getAttribute("data-csrf-token");
		div.flush_url = div.getAttribute("data-flush-url");
		var entries = u.qsa("li.item", div);
		var i, entry;
		for(i = 0; entry = entries[i]; i++) {
			var actions = u.ae(entry, "ul", {"class":"actions"});
			var bn_view = u.f.addAction(actions, {"type":"button", "class":"button", "value":"Details"});
			bn_view.entry = entry;
			u.ce(bn_view);
			bn_view.clicked = function() {
				if(u.hc(this.entry, "show")) {
					u.rc(this.entry, "show");
				}
				else {
					u.ac(this.entry, "show");
				}
			}
			var bn_flush = u.f.addAction(actions, {"type":"button", "class":"button", "value":"Flush"});
			bn_flush.div = div;
			bn_flush.entry = entry;
			bn_flush.cache_key = entry.getAttribute("data-cache-key");
			u.ce(bn_flush);
			bn_flush.clicked = function() {
				this.response = function(response) {
					page.notify(response);
					if(response.cms_status == "success") {
						this.entry.parentNode.removeChild(this.entry);
					}
				}
				u.request(this, this.div.flush_url, {"method":"post", "data" : "csrf-token="+this.div.csrf_token+"&cache-key="+this.cache_key});
			}
		}
	}
}

/*m-profile.js*/
Util.Modules["editProfile"] = new function() {
	this.init = function(div) {
		div._item_id = u.cv(div, "item_id");
		var form = u.qs("form", div);
		form.div = div;
		u.f.init(form);
		form.submitted = function(iN) {
			this.response = function(response) {
				if(response.cms_status == "success") {
					response.cms_message = ["Profile updated"];
				}
				else {
					response.cms_message = ["Profile could not be updated"];
				}
				page.notify(response);
			}
			u.request(this, this.action, {"method":"post", "data" : this.getData({"format":"formdata"})});
		}
	}
}
Util.Modules["usernamesProfile"] = new function() {
	this.init = function(div) {
		u.bug("init usernamesProfile")
		var form;
		form = u.qs("form.email", div);
		u.f.init(form);
		form.updated = function() {
			u.ac(this.actions["save"], "primary");
		}
		form.submitted = function(iN) {
			this.response = function(response) {
				if(response.cms_object && response.cms_object.status == "USER_EXISTS") {
					page.notify({"isJSON":true, "cms_status":"error", "cms_message":["Email already exists"]});
					u.f.inputHasError(this.inputs["email"]);
				}
				else if(response.cms_status == "error") {
					page.notify({"isJSON":true, "cms_status":"error", "cms_message":["Email could not be updated"]});
					u.f.inputHasError(this.inputs["email"]);
				}
				else {
					u.rc(this.actions["save"], "primary");
					page.notify({"isJSON":true, "cms_status":"success", "cms_message":["Email updated"]});
				}
			}
			u.request(this, this.action, {"method":"post", "data" : this.getData()});
		}
		form = u.qs("form.mobile", div);
		u.f.init(form);
		form.updated = function() {
			u.ac(this.actions["save"], "primary");
		}
		form.submitted = function(iN) {
			this.response = function(response) {
				if(response.cms_object && response.cms_object.status == "USER_EXISTS") {
					page.notify({"isJSON":true, "cms_status":"error", "cms_message":["Mobile already exists"]});
					u.f.inputHasError(this.inputs["mobile"]);
				}
				else if(response.cms_status == "error") {
					page.notify({"isJSON":true, "cms_status":"error", "cms_message":["Mobile could not be updated"]});
					u.f.inputHasError(this.inputs["mobile"]);
				}
				else {
					u.rc(this.actions["save"], "primary");
					page.notify({"isJSON":true, "cms_status":"success", "cms_message":["Mobile updated"]});
				}
			}
			u.request(this, this.action, {"method":"post", "data" : this.getData()});
		}
	}
}
Util.Modules["passwordProfile"] = new function() {
	this.init = function(div) {
		var password_state = u.qs("div.password_state", div);
		var new_password = u.qs("div.new_password", div);
		var a_change = u.qs(".password_set a");
		a_change._new_password = new_password;
		a_change._password_state = password_state;
		u.ce(a_change);
		a_change.clicked = function() {
			u.as(this._new_password, "display", "block");
			u.as(this._password_state, "display", "none");
		}
		var form = u.qs("form", div);
		form._password_state = password_state;
		form._new_password = new_password;
		u.f.init(form);
		form.actions["cancel"].clicked = function() {
			u.as(this.form._password_state, "display", "block");
			u.as(this.form._new_password, "display", "none");
		}
		form.submitted = function(iN) {
			this.response = function(response) {
				if(response.cms_status == "success") {
					u.ac(this._password_state, "set");
					u.as(this._password_state, "display", "block");
					u.as(this._new_password, "display", "none");
					page.notify({"isJSON":true, "cms_status":"success", "cms_message":"Password updated"});
				}
				else {
					page.notify({"isJSON":true, "cms_status":"error", "cms_message":"Password could not be updated"});
				}
			}
			u.request(this, this.action, {"method":"post", "data" : this.getData()});
			this.reset();
		}
	}
}
Util.Modules["paymentMethods"] = new function() {
	this.init = function(div) {
		var payment_methods = u.qsa("li.payment_method", div);
		var i, payment_method;
		for(i = 0; i < payment_methods.length; i++) {
			payment_method = payment_methods[i];
			payment_method.action = u.qs("li.delete", payment_method);
			payment_method.action.payment_method = payment_method;
			payment_method.action.confirmed = function(response) {
				page.notify(response);
				if(response.cms_status && response.cms_status === "success") {
					this.payment_method.parentNode.removeChild(this.payment_method);
				}
			}
		}
	}
}
Util.Modules["apitokenProfile"] = new function() {
	this.init = function(div) {
		var token = u.qs("p.token", div);
		var form = u.qs("form", div);
		if(form) {
			form._token = token;
			u.f.init(form);
			form.submitted = function(iN) {
				this.response = function(response) {
					if(response.cms_status == "success") {
						this._token.innerHTML = response.cms_object;
						page.notify({"isJSON":true, "cms_status":"success", "cms_message":"API token updated"});
					}
					else {
						page.notify({"isJSON":true, "cms_status":"error", "cms_message":"API token could not be updated"});
					}
				}
				u.request(this, this.action, {"method":"post", "data" : this.getData()});
			}
		}
	}
}
Util.Modules["addressProfile"] = new function() {
	this.init = function(form) {
		u.f.init(form);
		form.actions["cancel"].clicked = function(event) {
			location.href = this.url;
		}
		form.submitted = function(iN) {
			this.response = function(response) {
				if(response.cms_status == "success") {
					location.href = this.actions["cancel"].url;
				}
				else {
					page.notify({"isJSON":true, "cms_status":"error", "cms_message":"Address could not be updated"});
				}
			}
			u.request(this, this.action, {"method":"post", "data" : this.getData()});
		}
	}
}
Util.Modules["maillistsProfile"] = new function() {
	this.init = function(div) {
		var i, node;
		div.maillists = u.qsa("ul.maillists > li", div);
		for(i = 0; node = div.maillists[i]; i++) {
			node.li_unsubscribe = u.qs("li.unsubscribe", node);
			node.li_subscribe = u.qs("li.subscribe", node);
			if(node.li_unsubscribe) {
				node.li_unsubscribe.node = node;
				node.li_unsubscribe.confirmed = function(response) {
					if(response.cms_status == "success") {
						page.notify({"isJSON":true, "cms_status":"success", "cms_message":"Unsubscribed from maillist"});
						u.rc(this.node, "subscribed");
					}
					else {
						page.notify({"isJSON":true, "cms_status":"error", "cms_message":"Could not unsubscribe"});
					}
				}
			}
			if(node.li_subscribe) {
				node.li_subscribe.node = node;
				node.li_subscribe.confirmed = function(response) {
					if(response.cms_status == "success") {
						u.ac(this.node, "subscribed");
						page.notify({"isJSON":true, "cms_status":"success", "cms_message":"Subscribed to maillist"});
					}
					else {
						page.notify({"isJSON":true, "cms_status":"error", "cms_message":"Could not subscribe to maillist"});
					}
				}
			}
		}
	}
}
Util.Modules["resetPassword"] = new function() {
	this.init = function(form) {
		u.f.init(form);
		form.submitted = function() {
			this.response = function(response) {
				if(response.cms_status == "success") {
					location.href = "/login";
				}
				else {
					page.notify({"isJSON":true, "cms_status":"error", "cms_message":"Password could not be updated"});
				}
			}
			u.request(this, this.action, {"method":"post", "data":this.getData()});
		}
	}
}
Util.Modules["cancellationProfile"] = new function() {
	this.init = function(div) {
		u.bug("init cancellationProfile")
		div.password = u.qs("div.field.password", div);
		div.form = u.qs("form.cancelaccount", div);
		if(div.form) {
			div.form.div = div;
			u.f.init(div.form);
			div.form.actions["cancelaccount"].org_value = div.form.actions["cancelaccount"].value;
			div.form.actions["cancelaccount"].confirm_value = "Cancelling your account cannot be undone. OK?";
			div.form.actions["cancelaccount"].submit_value = "Confirm";
			div.form.inputs["password"].updated = function() {
				u.bug("typing password")
				u.t.resetTimer(this._form.t_confirm);
			}
			div.form.restore = function(event) {
				u.t.resetTimer(this.t_confirm);
				this.actions["cancelaccount"].value = this.actions["cancelaccount"].org_value;
				u.rc(this.actions["cancelaccount"], "confirm");
				u.rc(this.actions["cancelaccount"], "signup");
				u.ass(this.div.password, {
					"display": "none"
				})
			}
			div.form.actions["cancelaccount"].clicked = function() {
				if(!u.hc(this, "confirm")) {
					u.ac(this, "confirm");
					this.value = this.confirm_value;
					this._form.t_confirm = u.t.setTimer(this._form, this._form.restore, 3000);
				}
				else if(!u.hc(this, "signup")) {
					u.ac(this, "signup");
					u.t.resetTimer(this._form.t_confirm);
					u.ass(this._form.div.password, {
						"display": "block"
					});
					this.value = this.submit_value;
					this._form.t_confirm = u.t.setTimer(this._form, this._form.restore, 5000);
				}
				else {
					this._form.submit();
				}
			}
			div.form.submitted = function() {
				this.response = function(response) {
					if(response.cms_status == "success" && !response.cms_object.error) {
						page.notify({"isJSON":true, "cms_status":"success", "cms_message":"Your account has been cancelled"});
						u.t.setTimer(this, function() {location.href = "/";}, 2000);
					}
					else {
						if(response.cms_object.error == "missing_values") {
							page.notify({"isJSON":true, "cms_status":"error", "cms_message":"Some information is missing."});
						}
						else if(response.cms_object.error == "wrong_password") {
							page.notify({"isJSON":true, "cms_status":"error", "cms_message":"The password is not correct."});
						}
						else if(response.cms_object.error == "unpaid_orders") {
							page.notify({"isJSON":true, "cms_status":"error", "cms_message":"You have unpaid orders.."});
						}
						else {
							page.notify({"isJSON":true, "cms_status":"error", "cms_message":"An unknown error occured."});
						}
					}
				}
				u.request(this, this.action, {"method":"post", "data":this.getData()});
			}
		}
	}
}


/*m-taglist_tags.js*/
Util.Modules["taglist_tags"] = new function() {
	this.init = function(div) {
		var items = u.qsa("li.item", div);
		for(var i = 0; i < items.length; i++) {
			li = items[i];
			var add = u.qs("ul.actions li.add", li);
			add.li = li;
			var remove = u.qs("ul.actions li.remove", li);
			remove.li = li;
			add.added = function(response) {
				console.log(response);
				u.addClass(this.li, "added");
			}
			remove.removed = function(response) {
				u.removeClass(this.li, "added");
			}
		}
	}
}


/*u-settings.js*/
u.gapi_key = 'AIzaSyAVqnYpqFln-qAYsp5rkEGs84mrhmGQB_I';


/*m-form.js*/
Util.Modules["restructure"] = new function() {
	this.init = function(div) {
		var result_code = u.qs("div.result code");
		var li_restructure = u.qs("li.restrucure");
		li_restructure.result_code = result_code;
		li_restructure.confirmedError = function(response) {
			if(obj(response) && response.isHTML) {
				this.result_code.innerHTML = response.innerHTML;
			}
			else if(obj(response) && response.isJSON) {
				this.result_code.innerHTML = JSON.stingify(response);
			}
			else if(response) {
				this.result_code.innerHTML = response;
			}
			console.log(response);
		}
		console.log(li_restructure);
	}
}
Util.Modules["generic"] = new function() {
	this.init = function(div) {
	}
}


/*m-departments.js*/
Util.Modules["department_pickupdates"] = new function() {
	this.init = function(div) {
		var pickupdates = u.qsa("li.item", div);
		for(var i = 0; i < pickupdates.length; i++) {
			var pickupdate = pickupdates[i];
			var add = u.qs("ul.actions li.add", pickupdate);
			add.pickupdate = pickupdate;
			var remove = u.qs("ul.actions li.remove", pickupdate);
			remove.pickupdate = pickupdate;
			add.added = function(response) {
				console.log(response);
				u.addClass(this.pickupdate, "added");
			}
			remove.removed = function(response) {
				u.removeClass(this.pickupdate, "added");
			}
		}
	}
}

/*m-user_group.js*/
Util.Modules["user_group"] = new function() {
	this.init = function(div) {
		var users = u.qsa("li.item", div);
		for(var i = 0; i < users.length; i++) {
			var user = users[i];
			user.user_group = u.qs("dd.user_group", user)
			var update = u.qs("ul.actions li.update", user);
			if(update) {
				update.user = user;
				update.confirmed = function(response) {
					if(response.cms_status == "success" && response.cms_object) {
						u.bug(this.user.user_group);
						this.user.user_group.innerHTML = response.cms_object.user_group;
						u.as(this, "display", "none");
					}
				}
			}
		}
	}
}
