/*
asset-builder @ 2024-03-28 10:51:51
*/

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
	var samesite = "lax";
	var force = false;
	if(obj(_options)) {
		var _argument;
		for(_argument in _options) {
			switch(_argument) {
				case "expires"	: expires	= _options[_argument]; break;
				case "path"		: path		= _options[_argument]; break;
				case "samesite"	: samesite	= _options[_argument]; break;
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
		expires = ";expires="+(new Date((new Date()).getTime() + (1000*60*60*24*365))).toGMTString();
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
	samesite = ";samesite="+samesite;
	document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + path + expires + samesite;
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
			a.url = a.href;
			node.onclick = function(event) {
				event.preventDefault();
			}
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
Util.insertAfter = u.ia = function(insert_node, after_node) {
	var next_node = u.ns(after_node);
	if(next_node) {
		after_node.parentNode.insertBefore(insert_node, next_node);
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
				this.e_cancelPick = u.e.addWindowEndEvent(this, u.e._cancelPick);
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
		this.e_hold_options.event = this.e_hold_options.event || "hold";
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
			this.e_click_options.event = this.e_click_options.event || "click";
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
			this.e_rightclick_options.event = this.e_rightclick_options.event || "rightclick";
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
			this.e_dblclick_options.event = this.e_dblclick_options.event || "doubleclick";
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
			window["_DOMReady_" + id] = {
				id: id,
				action: action,
				callback: function(event) {
					if(fun(this.action)) {
						this.action.bind(window)(event);
					}
					else if(fun(this[this.action])){
						this[this.action].bind(window)(event);
					}
 					u.e.removeEvent(document, "DOMContentLoaded", window["_DOMReady_" + this.id].eventCallback); 
					delete window["_DOMReady_" + this.id];
				}
			}
			eval('window["_DOMReady_' + id + '"].eventCallback = function() {window["_DOMReady_'+id+'"].callback(event);}');
			u.e.addEvent(document, "DOMContentLoaded", window["_DOMReady_" + id].eventCallback);
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
		window["_Onload_" + id] = {
			id: id,
			action: action,
			callback: function(event) {
				if(fun(this.action)) {
					this.action.bind(window)(event);
				}
				else if(fun(this[this.action])){
					this[this.action].bind(window)(event);
				}
				u.e.removeEvent(document, "load", window["_Onload_" + this.id].eventCallback); 
				delete window["_Onload_" + this.id];
			}
		}
		eval('window["_Onload_' + id + '"].eventCallback = function() {window["_Onload_'+id+'"].callback(event);}');
		u.e.addEvent(window, "load", window["_Onload_" + id].eventCallback);
	}
}
u.e.addWindowEvent = function(node, type, action) {
	var id = u.randomString();
	window["_OnWindowEvent_"+ id] = {
		id: id,
		node: node,
		type: type,
		action: action,
		callback: function(event) {
			if(fun(this.action)) {
				this.action.bind(this.node)(event);
			}
			else if(fun(this[this.action])){
				this[this.action](event);
			}
		}
	};
	eval('window["_OnWindowEvent_' + id + '"].eventCallback = function(event) {window["_OnWindowEvent_'+ id + '"].callback(event);}');
	u.e.addEvent(window, type, window["_OnWindowEvent_" + id].eventCallback);
	return id;
}
u.e.removeWindowEvent = function(id) {
	if(window["_OnWindowEvent_" + id]) {
		u.e.removeEvent(window, window["_OnWindowEvent_"+id].type, window["_OnWindowEvent_"+id].eventCallback);
		delete window["_OnWindowEvent_"+id];
	}
}
u.e.addWindowStartEvent = function(node, action) {
	var id = u.randomString();
	window["_OnWindowStartEvent_"+ id] = {
		id: id,
		node: node,
		action: action,
		callback: function(event) {
			if(fun(this.action)) {
				this.action.bind(this.node)(event);
			}
			else if(fun(this[this.action])){
				this[this.action](event);
			}
		}
	};
	eval('window["_OnWindowStartEvent_' + id + '"].eventCallback = function(event) {window["_OnWindowStartEvent_'+ id + '"].callback(event);}');
	u.e.addStartEvent(window, window["_OnWindowStartEvent_" + id].eventCallback);
	return id;
}
u.e.removeWindowStartEvent = function(id) {
	if(window["_OnWindowStartEvent_" + id]) {
		u.e.removeStartEvent(window, window["_OnWindowStartEvent_"+id].eventCallback);
		delete window["_OnWindowStartEvent_"+id];
	}
}
u.e.addWindowMoveEvent = function(node, action) {
	var id = u.randomString();
	window["_OnWindowMoveEvent_"+ id] = {
		id: id,
		node: node,
		action: action,
		callback: function(event) {
			if(fun(this.action)) {
				this.action.bind(this.node)(event);
			}
			else if(fun(this[this.action])){
				this[this.action](event);
			}
		}
	};
	eval('window["_OnWindowMoveEvent_' + id + '"].eventCallback = function(event) {window["_OnWindowMoveEvent_'+ id + '"].callback(event);}');
	u.e.addMoveEvent(window, window["_OnWindowMoveEvent_" + id].eventCallback);
	return id;
}
u.e.removeWindowMoveEvent = function(id) {
	if(window["_OnWindowMoveEvent_" + id]) {
		u.e.removeMoveEvent(window, window["_OnWindowMoveEvent_"+id].eventCallback);
		delete window["_OnWindowMoveEvent_"+id];
	}
}
u.e.addWindowEndEvent = function(node, action) {
	var id = u.randomString();
	window["_OnWindowEndEvent_"+ id] = {
		id: id,
		node: node,
		action: action,
		callback: function(event) {
			if(fun(this.action)) {
				this.action.bind(this.node)(event);
			}
			else if(fun(this[this.action])){
				this[this.action](event);
			}
		}
	};
	eval('window["_OnWindowEndEvent_' + id + '"].eventCallback = function(event) {window["_OnWindowEndEvent_'+ id + '"].callback(event);}');
	u.e.addEndEvent(window, window["_OnWindowEndEvent_" + id].eventCallback);
	return id;
}
u.e.removeWindowEndEvent = function(id) {
	if(window["_OnWindowEndEvent_" + id]) {
		u.e.removeEndEvent(window, window["_OnWindowEndEvent_" + id].eventCallback);
		delete window["_OnWindowEndEvent_"+id];
	}
}
u.e.resetDragEvents = function(node) {
	node._moves_pick = 0;
	this.removeEvent(node, "mousemove", this._pick);
	this.removeEvent(node, "touchmove", this._pick);
	this.removeEvent(node, "mousemove", this._drag);
	this.removeEvent(node, "touchmove", this._drag);
	this.removeEvent(node, "mouseup", this._drop);
	this.removeEvent(node, "touchend", this._drop);
	this.removeWindowEndEvent(node.e_cancelPick);
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
			if(u.hc(field, "string|email|tel|number|integer|password")) {
				field.type = field.className.match(/(?:^|\b)(string|email|tel|number|integer|password)(?:\b|$)/)[0];
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
			else if(u.hc(field, "json")) {
				field.type = "json";
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
			else if(u.hc(field, "date|datetime")) {
				field.type = field.className.match(/(?:^|\b)(date|datetime)(?:\b|$)/)[0];
				field.input = u.qs("input", field);
				field.input._form = _form;
				field.input.label = u.qs("label[for='"+field.input.id+"']", field);
				field.input.field = field;
				field.input.val = this._value_date;
				u.e.addEvent(field.input, "keyup", this._updated);
				u.e.addEvent(field.input, "change", this._changed);
				u.e.addEvent(field.input, "change", this._updated);
				this.inputOnEnter(field.input);
				this.activateInput(field.input);
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
		if(field.virtual_input && !field.virtual_input.tabindex) {
			field.virtual_input.setAttribute("tabindex", 0);
			field.input.setAttribute("tabindex", 0);
		}
		else if(field.input && field.input.getAttribute("readonly")) {
			field.input.setAttribute("tabindex", -1);
		}
		else if(field.input && !field.input.tabindex) {
			field.input.setAttribute("tabindex", 0);
		}
	}
	this.initButton = function(_form, action) {
		action._form = _form;
		action.setAttribute("tabindex", 0);
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
	this._value_date = function(value) {
		if(value !== undefined) {
			this.value = value;
			if(value !== this.default_value) {
				u.rc(this, "default");
			}
			u.f.validate(this);
		}
		return (this.value != this.default_value) ? this.value.replace("T", " ") : "";
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
		u.f.positionHint(this.field);
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
		this.e_updated = event;
		u.ae(this.field.filelist, "li", {
			"html":this.field.hint ? u.text(this.field.hint) : u.text(this.label), class:"label",
		});
		if(files && files.length) {
			u.ac(this.field, "has_new_files");
			var i, file, li_file;
			this.field.filelist.load_queue = 0;
			for(i = 0; i < files.length; i++) {
				file = files[i];
				li_file = u.ae(this.field.filelist, "li", {"html":file.name, "class":"new format:"+file.name.substring(file.name.lastIndexOf(".")+1).toLowerCase()})
				li_file.input = this;
				if(file.type.match(/image/)) {
					li_file.image = new Image();
					li_file.image.li = li_file;
					u.ac(li_file, "loading");
					this.field.filelist.load_queue++;
					li_file.image.onload = function() {
						u.ac(this.li, "width:"+this.width);
						u.ac(this.li, "height:"+this.height);
						u.rc(this.li, "loading");
						this.li.input.field.filelist.load_queue--;
						delete this.li.image;
						u.f.filelistUpdated(this.li.input);
					}
					li_file.image.src = URL.createObjectURL(file);
				}
				else if(file.type.match(/video/)) {
					li_file.video = document.createElement("video");
					li_file.video.preload = "metadata";
					li_file.video.li = li_file;
					u.ac(li_file, "loading");
					this.field.filelist.load_queue++;
					li_file.video.onloadedmetadata = function() {
						u.bug("loaded", this);
						u.ac(this.li, "width:"+this.videoWidth);
						u.ac(this.li, "height:"+this.videoHeight);
						u.rc(this.li, "loading");
						this.li.input.field.filelist.load_queue--;
						delete this.li.video;
						u.f.filelistUpdated(this.li.input);
					}
					li_file.video.src = URL.createObjectURL(file);
				}
			}
			if(this.multiple) {
				for(i = 0; i < this.field.uploaded_files.length; i++) {
					u.ae(this.field.filelist, this.field.uploaded_files[i]);
				}
			}
			else {
				this.field.uploaded_files = [];
			}
			u.f.filelistUpdated(this);
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
	this.filelistUpdated = function(input) {
		if(input.field.filelist.load_queue === 0) {
			this._changed.bind(input.field.input)(input.e_updated);
			this._updated.bind(input.field.input)(input.e_updated);
			delete input.e_updated;
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
			var input_middle, help_top;
			if(field.virtual_input) {
				input_middle = field.virtual_input.parentNode.offsetTop + (field.virtual_input.parentNode.offsetHeight / 2);
			}
			else {
				input_middle = field.input.offsetTop + (field.input.offsetHeight / 2);
			}
			help_top = input_middle - field.help.offsetHeight / 2;
			u.ass(field.help, {
				"top": help_top + "px"
			});
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
		this.positionHint(iN.field);
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
			else if(u.hc(iN.field, "json")) {
				min = Number(u.cv(iN.field, "min"));
				max = Number(u.cv(iN.field, "max"));
				min = min ? min : 2;
				max = max ? max : 10000000;
				if(
					iN.val().length >= min && 
					iN.val().length <= max && 
					(function(value) {
						try {
							JSON.parse(value);
							return true;
						}
						catch(exception) {
							return false;
						}
					}(iN.val()))
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
				if(pattern) {
					pattern = pattern.split(",");
				}
				var i, files = Array.prototype.slice.call(u.qsa("li:not(.label)", iN.field.filelist));
				var min_width = Number(iN.getAttribute("data-min-width"));
				var min_height = Number(iN.getAttribute("data-min-height"));
				var allowed_sizes = iN.getAttribute("data-allowed-sizes");
				if(allowed_sizes) {
					allowed_sizes = allowed_sizes.split(",");
				}
				var allowed_proportions = iN.getAttribute("data-allowed-proportions");
				if(allowed_proportions) {
					allowed_proportions = allowed_proportions.split(",");
					for(i = 0; i < allowed_proportions.length; i++) {
						allowed_proportions[i] = u.round(eval(allowed_proportions[i]), 4);
					}
				}
				if(
					(files.length >= min && files.length <= max)
					&&
					(!pattern || files.every(function(node) {return pattern.indexOf("."+u.cv(node, "format")) !== -1}))
					&&
					(!min_width || files.every(function(node) {return u.cv(node, "width") >= min_width}))
					&&
					(!min_height || files.every(function(node) {return u.cv(node, "height") >= min_height}))
					&&
					(!allowed_sizes || files.every(function(node) {return allowed_sizes.indexOf(u.cv(node, "width")+"x"+u.cv(node, "height")) !== -1}))
					&&
					(!allowed_proportions || files.every(function(node) {return allowed_proportions.indexOf(u.round(Number(u.cv(node, "width"))/Number(u.cv(node, "height")), 4)) !== -1}))
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
Util.Form.customInit["html"] = function(field) {
	field.type = "html";
	field.input = u.qs("textarea", field);
	field.input._form = field._form;
	field.input.label = u.qs("label[for='"+field.input.id+"']", field);
	field.input.field = field;
	field._html_value = function(value) {
		if(value !== undefined) {
			this.value = value;
			if(value !== this.default_value) {
				u.rc(this, "default");
			}
			u.f.validate(this);
		}
		return (this.value != this.default_value && u.text(this.field._viewer)) ? this.value : "";
	}
	field.input.val = field._html_value;
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
	field.insertBefore(field._viewer, field.help)
	field._viewer.field = field;
	field._editor = u.ae(field, "div", {"class":"editor"});
	field.insertBefore(field._editor, field.help)
	field._editor.field = field;
	if(!fun(u.f.fixFieldHTML)) {
		u.ae(field._editor, field.indicator);
	}
	field._editor.picked = function() {
		u.ac(this, "reordering");
	}
	field._editor.dropped = function() {
		u.rc(this, "reordering");
		this.field.update();
	}
	field.addRawHTMLButton = function() {
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
	field.createTag = function(allowed_tags, type, className) {
		var tag = u.ae(this._editor, "div", {"class":"tag"});
		tag.field = this;
		tag._drag = u.ae(tag, "div", {"class":"drag"});
		tag._drag.field = this;
		tag._drag.tag = tag;
		this.createTagSelector(tag, allowed_tags);
		tag._type.val(type);
		if(className) {
			tag._classname = className;
		}
		this.addTagOptions(tag);
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
		}
	}
	field.classnameTag = function(tag) {
		if(!u.hc(tag.bn_classname, "open")) {
			var form = u.f.addForm(tag.bn_classname, {"class":"labelstyle:inject"});
			var fieldset = u.f.addFieldset(form);
			var input_classname = u.f.addField(fieldset, {"label":"classname", "name":"classname", "error_message":"", "value":tag._classname});
			input_classname.tag = tag;
			u.ac(tag.bn_classname, "open");
			u.ac(tag, "classname_open");
			u.f.init(form);
			input_classname.input.focus();
			input_classname.input.blurred = function() {
				this.field.tag._classname = this.val();
				this.field.tag.bn_classname.removeChild(this._form);
				u.rc(this.field.tag.bn_classname, "open");
				u.rc(this.field.tag, "classname_open");
				if(!this.field.tag.mirror) {
					this.field.tag.mirror = u.ae(this.field.tag, "span", {"class":"classname"});
				}
				if(this.field.tag._classname && this.field.tag._classname != "") {
					this.field.tag.mirror.innerHTML = this.field.tag._classname;
				}
				else {
					this.field.tag.mirror.parentNode.removeChild(this.field.tag.mirror);
					delete this.field.tag.mirror;
				}
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
			tag._type.inputStarted = function(event) {
				var selection = window.getSelection();
				if(selection && selection.type && u.contains(this.tag, selection.anchorNode)) {
					u.e.kill(event);
				}
			}
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
	field.addTagOptions = function(tag) {
		tag.ul_tag_options = u.ae(tag, "ul", {"class":"tag_options"});
		tag.bn_add = u.ae(tag.ul_tag_options, "li", {"class":"add", "html":"+"});
		tag.bn_add.field = field;
		tag.bn_add.tag = tag;
		u.ce(tag.bn_add);
		u.ce(tag.ul_tag_options);
		tag.ul_tag_options.inputStarted = function(event) {
			u.e.kill(event);
		}
		tag.bn_add.clicked = function(event) {
			this.cleanupOptions = function(event) {
				if(this.field.ul_new_tag_options) {
					this.field.ul_new_tag_options.parentNode.removeChild(this.field.ul_new_tag_options);
					delete this.field.ul_new_tag_options;
					if(this.start_event_id) {
						u.e.removeWindowStartEvent(this.start_event_id);
						delete this.start_event_id;
					}
				}
			}
			if(this.field.ul_new_tag_options) {
				this.cleanupOptions();
			}
			this.start_event_id = u.e.addWindowStartEvent(this, this.cleanupOptions);
			this.field.ul_new_tag_options = u.ae(this.field._editor, "ul", {"class":"new_tag_options"});
			u.ia(this.field.ul_new_tag_options, this.tag);
			if(this.field.text_allowed.length) {
				this.bn_add_text = u.ae(this.field.ul_new_tag_options, "li", {"class":"text", "html":"Text ("+this.field.text_allowed.join(", ")+")"});
				this.bn_add_text.field = this.field;
				this.bn_add_text.tag = this.tag;
				u.ce(this.bn_add_text);
				this.bn_add_text.inputStarted = function(event) {
					u.e.kill(event);
				}
				this.bn_add_text.clicked = function(event) {
					var tag = this.field.addTextTag(this.field.text_allowed[0]);
					u.ia(tag, this.tag);
					this.tag.bn_add.cleanupOptions();
					tag._input.focus();
				}
			}
			if(this.field.list_allowed.length) {
				this.bn_add_list = u.ae(this.field.ul_new_tag_options, "li", {"class":"list", "html":"List ("+this.field.list_allowed.join(", ")+")"});
				this.bn_add_list.field = this.field;
				this.bn_add_list.tag = this.tag;
				u.ce(this.bn_add_list);
				this.bn_add_list.inputStarted = function(event) {
					u.e.kill(event);
				}
				this.bn_add_list.clicked = function(event) {
					var tag = this.field.addListTag(this.field.list_allowed[0]);
					u.ia(tag, this.tag);
					this.tag.bn_add.cleanupOptions();
					tag._input.focus();
				}
			}
			if(this.field.code_allowed.length) {
				this.bn_add_code = u.ae(this.field.ul_new_tag_options, "li", {"class":"code", "html":"Code"});
				this.bn_add_code.field = this.field;
				this.bn_add_code.tag = this.tag;
				u.ce(this.bn_add_code);
				this.bn_add_code.inputStarted = function(event) {
					u.e.kill(event);
				}
				this.bn_add_code.clicked = function(event) {
					var tag = this.field.addCodeTag(this.field.code_allowed[0]);
					u.ia(tag, this.tag);
					this.tag.bn_add.cleanupOptions();
					tag._input.focus();
				}
			}
			if(this.field.media_allowed.length && this.field.item_id && this.field.media_add_action && this.field.media_delete_action && !u.browser("IE", "<=9")) {
				this.bn_add_media = u.ae(this.field.ul_new_tag_options, "li", {"class":"list", "html":"Media ("+this.field.media_allowed.join(", ")+")"});
				this.bn_add_media.field = this.field;
				this.bn_add_media.tag = this.tag;
				u.ce(this.bn_add_media);
				this.bn_add_media.inputStarted = function(event) {
					u.e.kill(event);
				}
				this.bn_add_media.clicked = function(event) {
					var tag = this.field.addMediaTag();
					u.ia(tag, this.tag);
					this.tag.bn_add.cleanupOptions();
					tag._input.focus();
				}
			}
			else if(this.field.media_allowed.length) {
				u.bug("some information is missing to support media upload:\nitem_id="+this.field.item_id+"\nmedia_add_action="+this.field.media_add_action+"\nmedia_delete_action="+this.field.media_delete_action);
			}
			if(this.field.ext_video_allowed.length) {
				this.bn_add_ext_video = u.ae(this.field.ul_new_tag_options, "li", {"class":"video", "html":"External video ("+this.field.ext_video_allowed.join(", ")+")"});
				this.bn_add_ext_video.field = this.field;
				this.bn_add_ext_video.tag = this.tag;
				u.ce(this.bn_add_ext_video);
				this.bn_add_ext_video.inputStarted = function(event) {
					u.e.kill(event);
				}
				this.bn_add_ext_video.clicked = function(event) {
					var tag = this.field.addExternalVideoTag(this.field.ext_video_allowed[0]);
					u.ia(tag, this.tag);
					this.tag.bn_add.cleanupOptions();
					tag._input.focus();
				}
			}
			if(this.field.file_allowed.length && this.field.item_id && this.field.file_add_action && this.field.file_delete_action && !u.browser("IE", "<=9")) {
				this.bn_add_file = u.ae(this.field.ul_new_tag_options, "li", {"class":"file", "html":"Downloadable file"});
				this.bn_add_file.field = this.field;
				this.bn_add_file.tag = this.tag;
				u.ce(this.bn_add_file);
				this.bn_add_file.inputStarted = function(event) {
					u.e.kill(event);
				}
				this.bn_add_file.clicked = function(event) {
					var tag = this.field.addFileTag();
					u.ia(tag, this.tag);
					this.tag.bn_add.cleanupOptions();
					tag._input.focus();
				}
			}
			else if(this.field.file_allowed.length) {
				u.bug("some information is missing to support file upload:\nitem_id="+this.field.item_id+"\nfile_add_action="+this.field.file_add_action+"\nfile_delete_action="+this.field.file_delete_action);
			}
		}
		tag.bn_remove = u.ae(tag.ul_tag_options, "li", {"class":"remove"});
		tag.bn_remove.field = this;
		tag.bn_remove.tag = tag;
		u.ce(tag.bn_remove);
		tag.bn_remove.clicked = function() {
			this.field.deleteTag(this.tag);
		}
		tag.bn_classname = u.ae(tag.ul_tag_options, "li", {"class":"classname"});
		u.ae(tag.bn_classname, "span", {"html":"CSS"});
		tag.bn_classname.field = this;
		tag.bn_classname.tag = tag;
		u.ce(tag.bn_classname);
		tag.bn_classname.clicked = function() {
			this.field.classnameTag(this.tag);
		}
		if(tag._classname) {
			if(!tag.mirror) {
				tag.mirror = u.ae(tag, "span", {"class":"classname", "html":tag._classname});
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
			if(tag._format.match(/gif|png|jpg|svg|avif|webp/)) {
				tag._image = u.ie(tag, "img");
				tag._image.src = "/images/"+tag._item_id+"/"+tag._variant+"/400x."+tag._format;
			}
			else if(tag._format.match(/mp4|mov/)) {
				tag._image = u.ie(tag, "video");
				tag._image.src = "/videos/"+tag._item_id+"/"+tag._variant+"/400x."+tag._format;
			}
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
				if(this.tag._format.match(/gif|png|jpg|svg|avif|webp/)) {
					this.tag._image = u.ie(this.tag, "img");
					this.tag._image.src = "/images/"+this.tag._item_id+"/"+this.tag._variant+"/400x."+this.tag._format;
				}
				else if(this.tag._format.match(/mp4|mov/)) {
					this.tag._image = u.ie(this.tag, "video");
					this.tag._image.src = "/videos/"+this.tag._item_id+"/"+this.tag._variant+"/400x."+this.tag._format;
				}
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
	field.addCodeTag = function(type, value, className) {
		var tag = this.createTag(this.code_allowed, type, className);
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
			u.e.removeWindowEndEvent(this._selection_event_id);
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
	field.addListTag = function(type, value, className) {
		var tag = this.createTag(this.list_allowed, type, className);
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
		tag._input = li._input;
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
	field.addTextTag = function(type, value, className) {
		var tag = this.createTag(this.text_allowed, type, className);
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
			u.e.removeWindowEndEvent(this._selection_event_id);
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
		else {
			this.field.hideSelectionOptions();
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
	}
	field._pasted_content = function(event) {
		u.e.kill(event);
		var i, node, text, range, new_tag, current_tag, selection, paste_parts, text_parts, text_nodes;
		var paste_content = event.clipboardData.getData("text/plain");
		if(paste_content !== "") {
			selection = window.getSelection();
			if(!selection.isCollapsed) {
				selection.deleteFromDocument();
			}
			if(u.hc(this.tag, "ul|ol")) {
				u.bug("must be handled – paste in list input");
			}
			if(u.hc(this.tag, "code")) {
				paste_parts = [paste_content];
			}
			else {
				paste_parts = paste_content.trim().split(/\n\r\n\r|\n\n|\r\r/g);
			}
			text_tags = [];
			for(i = 0; i < paste_parts.length; i++) {
				text_block = paste_parts[i].trim();
				if(text_block) {
					nodes = [];
					text_parts = text_block.split(/\n\r|\n|\r/g);
					for(j = 0; j < text_parts.length; j++) {
						text = text_parts[j];
						nodes.push(document.createTextNode(text));
						if(j < text_parts.length - 1) {
							nodes.push(document.createElement("br"));
						}
					}
					text_tags.push(nodes);
				}
			}
			current_tag = this.tag;
			for(i = 0; i < text_tags.length; i++) {
				nodes = text_tags[i];
				for(j = 0; j < nodes.length; j++) {
					node = nodes[j];
					selection = window.getSelection();
					range = selection.getRangeAt(0);
					range.insertNode(node);
					selection.addRange(range);
					selection.collapseToEnd();
				}
				if(i < text_tags.length - 1) {
					new_tag = this.field.addTextTag(this.field.text_allowed[0]);
					u.ia(new_tag, current_tag);
					current_tag = new_tag;
					current_tag._input.focus();
				}
			}
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
		this.hideSelectionOptions();
		this.hideDeleteOrEditOptions();
		this.selection_options = u.ae(node.field._editor, "div", {"class":"selection_options"});
		node.field._editor.insertBefore(this.selection_options, node.tag);
		var ul = u.ae(this.selection_options, "ul", {"class":"options"});
		this.selection_options._link = u.ae(ul, "li", {"class":"link", "html":"Link"});
		this.selection_options._link.field = this;
		this.selection_options._link.tag = node.tag;
		this.selection_options._link.selection = selection;
		u.ce(this.selection_options._link);
		this.selection_options._link.inputStarted = function(event) {
			u.e.kill(event);
		}
		this.selection_options._link.clicked = function(event) {
			u.e.kill(event);
			this.field.addAnchorTag(this.selection, this.tag);
		}
		this.selection_options._em = u.ae(ul, "li", {"class":"em", "html":"Italic"});
		this.selection_options._em.field = this;
		this.selection_options._em.tag = node.tag;
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
		this.selection_options._strong.tag = node.tag;
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
		this.selection_options._sup.tag = node.tag;
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
		this.selection_options._span.tag = node.tag;
		this.selection_options._span.selection = selection;
		u.ce(this.selection_options._span);
		this.selection_options._span.inputStarted = function(event) {
			u.e.kill(event);
		}
		this.selection_options._span.clicked = function(event) {
			u.e.kill(event);
			this.field.addSpanTag(this.selection, this.tag);
		}
	}
	field.hideDeleteOrEditOptions = function(node) {
		var options = u.qsa(".delete_selection, .edit_selection");
		var i, option;
		for(i = 0; i < options.length; i++) {
			option = options[i];
			if(!node || option.node !== node) {
				option.node.out();
			}
		}
	}
	field.deleteOrEditOption = function(node) {
		node.over = function(event) {
			this.field.hideDeleteOrEditOptions(this);
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
		u.e.hover(node, {"delay":500});
	}
	field.activateInlineFormatting = function(input, tag) {
		var i, node;
		var inline_tags = u.qsa("a,strong,em,span", input);
		for(i = 0; i < inline_tags.length; i++) {
			node = inline_tags[i];
			node.field = input.field;
			node.tag = tag;
			if(!u.text(node)) {
				node.parentNode.removeChild(node);
			}
			else {
				this.deleteOrEditOption(node);
			}
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
			this.editAnchorTag(a);
			this.deleteOrEditOption(a);
		}
		catch(exception) {
			u.bug("exception", exception)
			selection.removeAllRanges();
			this.hideSelectionOptions();
			alert("You cannot cross the boundaries of another selection. Yet.");
		}
	}
	field.anchorOptions = function(a) {
		var form = u.f.addForm(this.selection_options, {"class":"labelstyle:inject"});
		var fieldset = u.f.addFieldset(form);
		var input_url = u.f.addField(fieldset, {
			"label":"url", 
			"name":"url",
			"required": true,
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
		form.inputs["url"].focus();
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
		this.hideDeleteOrEditOptions();
		this.selection_options = u.ae(a.field._editor, "div", {"class":"selection_options"});
		a.field._editor.insertBefore(this.selection_options, a.tag);
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
			this.editSpanTag(span);
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
		this.hideDeleteOrEditOptions();
		this.selection_options = u.ae(span.field._editor, "div", {"class":"selection_options"});
		span.field._editor.insertBefore(this.selection_options, span.tag);
		this.spanOptions(span);
	}
	field.spanOptions = function(span) {
		var form = u.f.addForm(this.selection_options, {"class":"labelstyle:inject"});
		var fieldset = u.f.addFieldset(form);
		var input_classname = u.f.addField(fieldset, {"label":"CSS class", "name":"classname", "value":span.className, "error_message":""});
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
	field._viewer.innerHTML = field.input.value;
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
				tag = field.addTextTag(node.nodeName.toLowerCase(), value, node.className);
				field.activateInlineFormatting(tag._input, tag);
			}
			else if(node.nodeName.toLowerCase() == "code") {
				tag = field.addCodeTag(node.nodeName.toLowerCase(), node.innerHTML, node.className);
				field.activateInlineFormatting(tag._input, tag);
			}
			else if(field.list_allowed.length && node.nodeName.toLowerCase().match(field.list_allowed.join("|"))) {
				var lis = u.qsa("li", node);
				value = lis[0].innerHTML.trim().replace(/(<br>|<br \/>)$/, "").replace(/\n\r|\n|\r/g, "<br>");
				tag = field.addListTag(node.nodeName.toLowerCase(), value, node.className);
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
	field._editor.detectSortableLayout();
	field.updateViewer();
	field.updateContent();
	field.addRawHTMLButton();
}
Util.Form.customLabelStyle["inject"] = function(iN) {
	if(!iN.type || !iN.type.match(/file|radio|checkbox/)) {
		iN.default_value = u.text(iN.label);
		u.e.addEvent(iN, "focus", u.f._changed_state);
		u.e.addEvent(iN, "blur", u.f._changed_state);
		u.e.addEvent(iN, "change", u.f._changed_state);
		if(iN.type.match(/number|integer|password/)) {
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
		if(iN.field.virtual_input) {
			u.rc(iN.field.virtual_input, "default");
		}
		if(iN.val() === "" && !iN.type.match(/date|datetime/)) {
			iN.val("");
		}
	}
	else {
		if(iN.val() === "") {
			u.ac(iN, "default");
			if(obj(iN.field.virtual_input)) {
				u.ac(iN.field.virtual_input, "default");
			}
			if(!iN.type.match(/date|datetime/)) {
				iN.val(iN.default_value);
			}
		}
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
u.googlemaps = new function() {
	this.api_loading = false;
	this.api_loaded = false;
	this.api_load_queue = [];
	this.map = function(map, center, _options) {
		map._center_latitude = center[0];
		map._center_longitude = center[1];
		map._zoom = 10;
		map._streetview = false;
		map._scrollwheel = true;
		map._styles = false;
		map._disable_ui = false;
		map._fullscreenControl = false;
		map._zoomControl = true;
		map._zoomControlOptions = false;
		map._keyboardShortcuts = false;
		if(obj(_options)) {
			var _argument;
			for(_argument in _options) {
				switch(_argument) {
					case "zoom"                  : map._zoom                    = _options[_argument]; break;
					case "scrollwheel"           : map._scrollwheel             = _options[_argument]; break;
					case "streetview"            : map._streetview              = _options[_argument]; break;
					case "styles"                : map._styles                  = _options[_argument]; break;
					case "disableUI"             : map._disable_ui              = _options[_argument]; break;
					case "fullscreenControl"     : map._fullscreenControl       = _options[_argument]; break;
					case "zoomControl"           : map._zoomControl             = _options[_argument]; break;
					case "zoomControlOptions"    : map._zoomControlOptions      = _options[_argument]; break;
					case "keyboardShortcuts"     : map._keyboardShortcuts       = _options[_argument]; break;
				}
			}
		}
		var map_key = u.randomString(8);
		window[map_key] = function() {
			u.googlemaps.api_loaded = true;
			u.googlemaps.api_loading = false;
			var map;
			while(u.googlemaps.api_load_queue.length) {
				map = u.googlemaps.api_load_queue.shift();
				map.init();
			}
		}
		map.init = function() {
			var mapOptions = {
				center: new google.maps.LatLng(this._center_latitude, this._center_longitude), 
				zoom: this._zoom, 
				scrollwheel: this._scrollwheel, 
				streetViewControl: this._streetview, 
				zoomControl: this._zoomControl, 
				zoomControlOptions: this._zoomControlOptions ? this._zoomControlOptions : {position: google.maps.ControlPosition.LEFT_TOP}, 
				styles: this._styles, 
				disableDefaultUI: this._disable_ui,
				fullscreenControl: this._fullscreenControl,
				keyboardShortcuts: this._keyboardShortcuts,
			};
			this.g_map = new google.maps.Map(this, mapOptions);
			this.g_map.m_map = this
			if(fun(this.APIloaded)) {
				this.APIloaded();
			}
			google.maps.event.addListener(this.g_map, 'tilesloaded', function() {
				if(fun(this.m_map.tilesloaded)) {
					this.m_map.tilesloaded();
				}
			});
			google.maps.event.addListenerOnce(this.g_map, 'tilesloaded', function() {
				if(fun(this.m_map.loaded)) {
					this.m_map.loaded();
				}
			});
		}
		if(!this.api_loaded && !this.api_loading) {
			u.ae(document.head, "script", {"src":"https://maps.googleapis.com/maps/api/js?callback="+map_key+(u.gapi_key ? "&key="+u.gapi_key : "")});
			this.api_loading = true;
			this.api_load_queue.push(map);
		}
		else if(this.api_loading) {
			this.api_load_queue.push(map);
		}
		else {
			map.init();
		}
	}
	this.addMarker = function(map, coords, _options) {
		var _icon;
		var _label = null;
		if(obj(_options)) {
			var _argument;
			for(_argument in _options) {
				switch(_argument) {
					case "icon"           : _icon               = _options[_argument]; break;
					case "label"          : _label              = _options[_argument]; break;
				}
			}
		}
		var marker = new google.maps.Marker({position: new google.maps.LatLng(coords[0], coords[1]), animation:google.maps.Animation.DROP, icon: _icon, label: _label});
		marker.setMap(map.g_map);
		marker.g_map = map.g_map;
		google.maps.event.addListener(marker, 'click', function() {
			if(fun(this.clicked)) {
				this.clicked();
			}
		});
		google.maps.event.addListener(marker, 'mouseover', function() {
			if(fun(this.entered)) {
				this.entered();
			}
		});
		google.maps.event.addListener(marker, 'mouseout', function() {
			if(fun(this.exited)) {
				this.exited();
			}
		});
		return marker;
	}
	this.removeMarker = function(map, marker, _options) {
		marker._animation = true;
		if(obj(_options)) {
			var _argument;
			for(_argument in _options) {
				switch(_argument) {
					case "animation"      : marker._animation            = _options[_argument]; break;
				}
			}
		}
		if(marker._animation) {
			var key = u.randomString(8);
			marker.pick_step = 0;
			marker.c_zoom = (1 << map.getZoom());
			marker.c_projection = map.getProjection();
			marker.c_exit = map.getBounds().getNorthEast().lat();
			marker._pickUp = function() {
				var new_position = this.c_projection.fromLatLngToPoint(this.getPosition());
				new_position.y -= (20*this.pick_step) / this.c_zoom; 
				new_position = this.c_projection.fromPointToLatLng(new_position);
				this.setPosition(new_position);
				if(this.c_exit < new_position.lat()) {
					this.setMap(null);
					if(fun(this.removed)) {
						this.removed();
					}
				}
				else{
					this.pick_step++;
					u.t.setTimer(this, this._pickUp, 20);
				}
			}
			marker._pickUp();
		}
		else {
			marker.setMap(null);
		}
	}
	this.infoWindow = function(map) {
		map.g_infowindow = new google.maps.InfoWindow({"maxWidth":250});
		google.maps.event.addListener(map.g_infowindow, 'closeclick', function() {
			if(this._marker && fun(this._marker.closed)) {
				this._marker.closed();
				this._marker = false;
			}
		});
	}
	this.showInfoWindow = function(map, marker, content) {
		map.g_infowindow.setContent(content);
		map.g_infowindow.open(map, marker);
		map.g_infowindow._marker = marker;
	}
	this.hideInfoWindow = function(map) {
		map.g_infowindow.close();
		if(map.g_infowindow._marker && fun(map.g_infowindow._marker.closed)) {
			map.g_infowindow._marker.closed();
			map.g_infowindow._marker = false;
		}
		map.g_infowindow._marker = false;
	}
	this.zoom = function() {
	}
	this.center = function() {
	}
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
			}
			else if(location.hash.match(/^#\//) && u.h.getCleanHash(location.hash) != u.h.getCleanUrl(location.href)) {
				callback_after_init = u.h.getCleanHash(location.hash);
			}
			else {
			}
		}
		else {
			if(u.h.getCleanHash(location.hash) != u.h.getCleanUrl(location.href) && location.hash.match(/^#\//)) {
				window._man_nav_path = u.h.getCleanHash(location.hash);
				u.h.navigate(window._man_nav_path);
				callback_after_init = window._man_nav_path;
			}
			else {
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
u.overlay = function (_options) {
	var title = "Overlay";
	var drag = true;
	var width = 400;
	var height = 400;
	var content_scroll = false;
	var classname = "";
	if(obj(_options)) {
		var _argument;
		for(_argument in _options) {
			switch(_argument) {
				case "title"            : title             = _options[_argument]; break;
				case "drag"             : drag              = _options[_argument]; break;
				case "class"            : classname         = _options[_argument]; break;
				case "width"            : width             = _options[_argument]; break;
				case "height"           : height            = _options[_argument]; break;
				case "content_scroll"   : content_scroll    = _options[_argument]; break;
			}
		}
	}
	if (width > 500) {
		classname = " large " + classname;
	}
	else {
		classname = " small " + classname;
	}
	if(content_scroll) {
		classname += "content_scroll"
	}
	var overlay = u.ae(document.body, "div", {
		"class": "overlay" + classname, 
		"tabindex": "-1"
	});
	overlay.protection = u.ae(document.body, "div", {
		"class": "overlay_protection"
	});
	u.ass(overlay, {
		"opacity": 0,
		"width": width + "px",
		"height": height + "px",
		"left": ((u.browserW() - width) / 2) + "px",
		"top": ((u.browserH() - height) / 2) + "px",
	});
	overlay.w = width;
	overlay.h = height;
	if (window._overlay_stack_index) {
		u.ass(overlay.protection, { "z-index": window._overlay_stack_index});
		u.ass(overlay, { "z-index": window._overlay_stack_index + 1 });
	}
	window._overlay_stack_index = Number(u.gcs(overlay, "z-index")) + 2;
	u.as(document.body, "overflow", "hidden");
	overlay._resized = function (event) {
		u.ass(this, {
			"left": ((u.browserW() - this.w) / 2) + "px",
			"top": ((u.browserH() - this.h) / 2) + "px",
		});
		u.ass(this.div_content, {
			"height": ((this.offsetHeight - this.div_header.offsetHeight) - this.div_footer.offsetHeight - parseInt(u.gcs(this, "border-bottom")) - parseInt(u.gcs(this, "border-top"))) + "px"
		});
		if(fun(this.resized)) {
			this.resized(event);
		}
	}
	u.e.addWindowEvent(overlay, "resize", overlay._resized);
	overlay.div_header = u.ae(overlay, "div", {class:"header"});
	if(title) {
		overlay.div_header.h2 = u.ae(overlay.div_header, "h2", {html: title});
		overlay.div_header.overlay = overlay;
	}
	overlay.div_content = u.ae(overlay, "div", {class: "content"});
	overlay.div_content.overlay = overlay;
	overlay.div_footer = u.ae(overlay, "div", {class: "footer"});
	overlay.div_footer.overlay = overlay;
	if (drag) {
		u.e.drag(overlay.div_header, overlay.div_header);
		overlay._x = 0;
		overlay._y = 0;
		overlay.div_header.moved = function (event) {
			var new_x = this.overlay._x + this.current_x;
			var new_y = this.overlay._y + this.current_y;
			u.ass(this.overlay, {
				"transform": "translate(" + new_x + "px, " + new_y + "px)",
			});
		}
		overlay.div_header.dropped = function (event) {
			this.overlay._x += this.current_x;
			this.overlay._y += this.current_y;
		}
	}
	overlay.close = function (event) {
		u.as(document.body, "overflow", "auto");
		document.body.removeChild(this);
		document.body.removeChild(this.protection);
		if(fun(this.closed)) {
			this.closed(event);
		}
	}
	overlay.x_close = u.ae(overlay.div_header, "div", {class: "close"});
	overlay.x_close.overlay = overlay;
	u.ce(overlay.x_close);
	overlay.x_close.clicked = function (event) {
		this.overlay.close(event);
	}
	overlay._resized();
	u.ass(overlay, {
		"transition": "opacity .4s ease-in-out .1s",
		"opacity": 1,
	});
	return overlay;
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
				if(next._file.match(/png|jpg|gif|svg|avif|webp/)) {
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
				var _z_index;
				if(this._z_index != "auto") {
					_z_index = this._z_index + 1;
				}
				else {
					_z_index = 55;
				}
				u.ass(this.scope._shadow_node, {
					width: _start_width,
					position: "absolute",
					left: ((event_x - this.scope._shadow_node._rel_ox) - this._mouse_ox) + "px",
					top: ((event_y - this.scope._shadow_node.rel_oy) - this._mouse_oy) + "px",
					"z-index": _z_index,
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
				u.e.removeWindowMoveEvent(node._event_move_id);
				delete node._event_move_id;
			}
			if(node._event_end_id) {
				u.e.removeWindowEndEvent(node._event_end_id);
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
					if((target._n_top || target._n_bottom) && (u.cn(target, {include: this._draggable_selector}).length > 1 || target._n_display != "block")) {
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
				draggable_node._z_index = u.gcs(draggable_node, "zIndex");
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
	if(svg_object.viewBox) {
		svg.setAttributeNS(null, "viewBox", svg_object.viewBox);
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
	var detail, svg_shape;
	svg_shape = document.createElementNS("http://www.w3.org/2000/svg", svg_object["type"]);
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
u.template = function(template, json, _options) {
	var string = "";
	var template_string = "";
	var clone, container, item_template, dom, node_list, type_template, type_parent;
	var append_to_node = false;
	if (obj(_options)) {
		var _argument;
		for (_argument in _options) {
			switch (_argument) {
				case "append": 	append_to_node = _options[_argument];			break;
			}
		}
	}
	if(obj(template) && typeof(template.nodeName) != "undefined") {
		type_template = "HTML";
	}
	else if(obj(template) && JSON.stringify(template)) {
		type_template = "JSON";
	}
	else if(str(template) && template.match(/^(\{|\[)/)) {
		type_template = "JSON_STRING";
	}
	else if(str(template) && template.match(/^<.+>$/)) {
		type_template = "HTML_STRING";
	}
	else if(str(template)) {
		type_template = "STRING";
	}
	if(type_template == "HTML_STRING" || type_template == "HTML") {
		if(type_template == "HTML") {
			clone = template.cloneNode(true);
			u.rc(clone, "template");
			if(template.nodeName == "LI") {
				type_parent = "ul";
				container = document.createElement(type_parent);
			}
			else if(template.nodeName == "TR") {
				type_parent = "table";
				container = document.createElement("table").appendChild(document.createElement("tbody"));
			}
			else {
				type_parent = "div";
				container = document.createElement("div");
			}
			container.appendChild(clone);
			template_string = container.innerHTML;
			template_string = template_string.replace(/href\=\"([^\"]+)\"/g, function(string) {return decodeURIComponent(string);});
			template_string = template_string.replace(/src\=\"([^\"]+)\"/g, function(string) {return decodeURIComponent(string);});
		}
		else {
			if(template.match(/^<li/i)) {
				type_parent = "ul";
			}
			else if(template.match(/^<tr/i)) {
				type_parent = "table";
			}
			else {
				type_parent = "div";
			}
			template_string = template;
		}
	}
	else if(type_template == "JSON") {
		template_string = JSON.stringify(template).replace(/^{/g, "MAN_JSON_START").replace(/}$/g, "MAN_JSON_END");
	}
	else if(type_template == "JSON_STRING") {
		template_string = template.replace(/^{/g, "MAN_JSON_START").replace(/}$/g, "MAN_JSON_END");
	}
	else if(type_template == "STRING") {
		template_string = template;
	}
	if(obj(json) && ((json.length == undefined && Object.keys(json).length) || json.length)) {
		if(json.length) {
			for(_item in json) {
				if(json.hasOwnProperty(_item)) {
					item_template = template_string;
					string += item_template.replace(/\{(.+?)\}/g, function(string) {
						var key = string.toString().replace(/[\{\}]/g, "");
						if(str(json[_item][key]) && json[_item][key]) {
							return json[_item][key].toString().replace(/(\\|\"|\')/g, "\\$1").replace(/\n/g, "\\n");
						}
						else if(typeof(json[_item][key]) == "number") {
							return "MAN_NUM" + json[_item][key] + "MAN_NUM";
						}
						else if(typeof(json[_item][key]) == "boolean") {
							return "MAN_BOOL" + json[_item][key] + "MAN_BOOL";
						}
						else if(json[_item][key] === null) {
							return "MAN_NULL";
						}
						else if(obj(json[_item][key])) {
							return "MAN_OBJ" + JSON.stringify(json[_item][key]).replace(/(\"|\')/g, "\\$1") + "MAN_OBJ";
						}
						else {
							return "";
						}
					});
				}
			}
		}
		else {
			string += template_string.replace(/\{(.+?)\}/g, function(string) {
				var key = string.toString().replace(/[\{\}]/g, "");
				if(str(json[key]) && json[key]) {
					return json[key].replace(/(\\|\"|\')/g, "\\$1").replace(/\n/g, "\\n");
				}
				else if(typeof(json[key]) == "number") {
					return "MAN_NUM" + json[key] + "MAN_NUM";
				}
				else if(typeof(json[key]) == "boolean") {
					return "MAN_BOOL" + json[key] + "MAN_BOOL";
				}
				else if(json[key] === null) {
					return "MAN_NULL";
				}
				else if(obj(json[key])) {
					return "MAN_OBJ" + JSON.stringify(json[key]).replace(/(\"|\')/g, "\\$1") + "MAN_OBJ";
				}
				else {
					return "";
				}
			});
		}
	}
	if(type_template == "HTML_STRING" || type_template == "HTML") {
		string = string.replace(/MAN_(BOOL|NUM)(.+?(?=MAN_(BOOL|NUM)))MAN_(BOOL|NUM)/g, "$2");
		string = string.replace(/MAN_NULL/g, "");
		string = string.replace(/MAN_OBJ(.+?(?=MAN_OBJ))MAN_OBJ/g, function(string) {
			string = string.replace(/MAN_OBJ(.+?(?=MAN_OBJ))MAN_OBJ/g, "$1");
			return string.replace(/\\(\\|"|')/g, "$1");
		});
		string = string.replace(/\\(\\|"|')/g, "$1");
		if(type_parent == "table") {
			dom = document.createElement("div");
			dom.innerHTML = "<table><tbody>"+string+"</tbody></table>";
			dom = u.qs("tbody", dom);
		}
		else {
			dom = document.createElement(type_parent);
			dom.innerHTML = string;
		}
		if(append_to_node) {
			node_list = [];
			while(dom.childNodes.length) {
				node_list.push(u.ae(append_to_node, dom.childNodes[0]));
			}
			return node_list;
		}
		return dom.childNodes;
	}
	else if(type_template == "JSON_STRING" || type_template == "JSON") {
		string = string.replace(/[\"]?MAN_(BOOL|NUM)(.+?(?=MAN_(BOOL|NUM)))MAN_(BOOL|NUM)[\"]?/g, "$2");
		string = string.replace(/[\"]?MAN_NULL[\"]?/g, "null");
		string = string.replace(/[\"]?MAN_OBJ(.+?(?=MAN_OBJ))MAN_OBJ[\"]?/g, function(string) {
			string = string.replace(/[\"]?MAN_OBJ(.+?(?=MAN_OBJ))MAN_OBJ[\"]?/g, "$1");
			return string.replace(/\\("|')/g, "$1");
		});
		return eval("["+string.replace(/MAN_JSON_START/g, "{").replace(/MAN_JSON_END/g, "},")+"]");
	}
	else if(type_template == "STRING") {
		string = string.replace(/MAN_(BOOL|NUM)(.+?(?=MAN_(BOOL|NUM)))MAN_(BOOL|NUM)/g, "$2");
		string = string.replace(/MAN_NULL/g, "");
		string = string.replace(/MAN_OBJ(.+?(?=MAN_OBJ))MAN_OBJ/g, function(string) {
			string = string.replace(/MAN_OBJ(.+?(?=MAN_OBJ))MAN_OBJ/g, "$1");
			return string.replace(/\\(\\|"|')/g, "$1");
		});
		return string.replace(/\\(\\|"|')/g, "$1");
	}
}
Util.Timer = u.t = new function() {
	this._timers = new Array();
	this.setTimer = function(node, action, timeout, param) {
		var id = this._timers.length;
		param = param != undefined ? param : {"target":node, "type":"timeout"};
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
Util.Modules["page"] = new function() {
	this.init = function(page) {
		page.hN = u.qs("#header");
		page.hN.ul_service = u.qs("ul.servicenavigation", page.hN);
		page.cN = u.qs("#content", page);
		page.nN = u.qs("#navigation", page);
		page.nN = page.insertBefore(page.nN, page.cN);
		page.fN = u.qs("#footer");
		page.fN.ul_service = u.qs("ul.servicenavigation", page.fN);
		page.resized = function() {
			this.browser_h = u.browserH();
			this.browser_w = u.browserW();
			this.available_height = this.browser_h - this.hN.offsetHeight - this.nN.offsetHeight - this.fN.offsetHeight;
			u.as(this.cN, "min-height", "auto");
			if(this.available_height >= this.cN.offsetHeight) {
				u.as(this.cN, "min-height", this.available_height+"px", false);
			}
			if(this.cN && this.cN.scene && typeof(this.cN.scene.resized) == "function") {
				this.cN.scene.resized();
			}
		}
		page.scrolled = function() {
			page.scrolled_y = u.scrollY();
			if(this.cN && this.cN.scene && typeof(this.cN.scene.scrolled) == "function") {
				this.cN.scene.scrolled();
			}
		}
		page.ready = function() {
			if(!this.is_ready) {
				this.is_ready = true;
				this.cN.scene = u.qs(".scene", this);
				u.e.addWindowEvent(this, "resize", this.resized);
				u.e.addWindowEvent(this, "scroll", this.scrolled);
				this.initHeader();
				this.initNavigation();
				this.acceptCookies();
				this.resized();
			}
		}
		page.initHeader = function() {
		}
		page.initNavigation = function() {
			page.nN_nodes = u.qsa("li.indent0", page.nN);
			var z_index_counter = 100;
			for (var i = 0; i < page.nN_nodes.length; i++) {
				var nav_node = page.nN_nodes[i];
				nav_node.subnav = u.qs("ul", nav_node);
				if (nav_node.subnav) {
					u.e.hover(nav_node, {
						"delay":"200"
					});
					nav_node.is_over = false;
					nav_node.over = function(event) {
						nav_node.is_over = true;
						z_index_counter++;
						u.ass(this.subnav, {
							"display":"block",
							"z-index": z_index_counter
						});
						u.a.transition(this.subnav, "all 0.3s ease-out")
						u.ass(this.subnav, {
							"opacity":"1"
						});
					}
					nav_node.out = function(event) {
						nav_node.is_over = false;
						this.subnav.transitioned = function() {
							if(!nav_node.is_over) {
								u.ass(this, {
									"display":"none"
								});
							}
						};
						u.a.transition(this.subnav, "all 0.15s ease-out");
						u.ass(this.subnav, {
							"opacity":"0"
						});
					}
				}
			}
		}
		page.acceptCookies = function() {
			if(u.terms_version && !u.getCookie(u.terms_version)) {
				var terms = u.ie(document.body, "div", {"class":"terms_notification"});
				u.ae(terms, "h3", {"html":u.stringOr(u.txt["terms-headline"], "Flere grøntsager, <br />færre kager")});
				u.ae(terms, "p", {"html":u.stringOr(u.txt["terms-paragraph"], "Vi beskytter dit privatliv og bruger kun funktionelle cookies.")});
				var bn_accept = u.ae(terms, "a", {"class":"accept", "html":u.stringOr(u.txt["terms-accept"], "Accepter")});
				bn_accept.terms = terms;
				u.ce(bn_accept);
				bn_accept.clicked = function() {
					this.terms.parentNode.removeChild(this.terms);
					u.saveCookie(u.terms_version, true, {"path":"/", "expires":false});
				}
				if(!location.href.match(u.terms_link)) {
					var bn_details = u.ae(terms, "a", {"class":"details", "html":u.stringOr(u.txt["terms-details"], "Læs mere"), "href":u.terms_link});
					u.ce(bn_details, {"type":"link"});
				}
				u.a.transition(terms, "all 0.5s ease-in");
				u.ass(terms, {
					"opacity": 1
				});
			}
		}
		page.ready();
	}
}
u.e.addDOMReadyEvent(u.init);
Util.Modules["scene"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
		}
		scene.ready();
	}
}
Util.Modules["banner"] = new function() {
	this.init = function(div) {
		var variant = u.cv(div, "variant");
		var format = u.cv(div, "format");
		if(variant == "random" || !variant) {
			variant = u.random(1, 4);
		}
		var image = u.ae(div, "img", {class:"fit-width"});	
		u.ae(div, "div", {class:"logo"});
		image.loaded = function(queue) {
			this.onload = function() {
				if(page) {
					page.resized();
				}
			}
			this.src = queue[0].image.src;
			if(page) {
				page.resized();
			}
		}
		u.preloader(image, ["/img/banners/desktop/pi_" + variant + "." + format]);
	}
}
u.f.fixFieldHTML = function(field) {
	if(field.indicator && field.label) {
		u.ae(field.label, field.indicator);
	}
}
u.f.customHintPosition = {};
u.f.customHintPosition["string"] = function() {}
u.f.customHintPosition["email"] = function() {}
u.f.customHintPosition["number"] = function() {}
u.f.customHintPosition["integer"] = function() {}
u.f.customHintPosition["password"] = function() {}
u.f.customHintPosition["tel"] = function() {}
u.f.customHintPosition["text"] = function() {}
u.f.customHintPosition["select"] = function() {}
u.f.customHintPosition["checkbox"] = function() {}
u.f.customHintPosition["radiobuttons"] = function() {}
u.f.customHintPosition["date"] = function() {}
u.f.customHintPosition["datetime"] = function() {}
u.f.customHintPosition["files"] = function() {}
u.f.customHintPosition["html"] = function() {}


/*u-settings.js*/
u.ga_account = '';
u.ga_domain = '';
u.gapi_key = 'AIzaSyAnZTViVnr4jxGyNQCCMGO0hnJ8NjsKqjo';
u.terms_version = "terms_v1";
u.terms_link = "/persondata";
u.txt["terms-headline"] = "Flere grøntsager, <br />færre kager";
u.txt["terms-paragraph"] = "Vi beskytter dit privatliv og bruger kun funktionelle cookies.";
u.txt["terms-accept"] = "Accepter";
u.txt["terms-details"] = "Læs mere";


/*beta-u-paymentcards.js*/
u.paymentCards = new function() {
	this.payment_cards = [
		{
			"type": 'maestro',
			"patterns": [5018, 502, 503, 506, 56, 58, 639, 6220, 67],
			"format": /([\d]{1,4})([\d]{1,4})?([\d]{1,4})?([\d]{1,4})?([\d]{1,4})?/,
			"card_length": [12,13,14,15,16,17,18,19],
			"cvc_length": [3],
			"luhn": true
		},
		{
			"type": 'forbrugsforeningen',
			"patterns": [600],
			"format": /([\d]{1,4})([\d]{1,4})?([\d]{1,4})?([\d]{1,4})?/,
			"card_length": [16],
			"cvc_length": [3],
			"luhn": true,
		},
		{
			"type": 'dankort',
			"patterns": [5019],
			"format": /([\d]{1,4})([\d]{1,4})?([\d]{1,4})?([\d]{1,4})?/,
			"card_length": [16],
			"cvc_length": [3],
			"luhn": true
		},
		{
			"type": 'visa',
			"patterns": [4],
			"format": /([\d]{1,4})([\d]{1,4})?([\d]{1,4})?([\d]{1,4})?/,
			"card_length": [13, 16],
			"cvc_length": [3],
			"luhn": true
		},
		{
			"type": 'mastercard',
			"patterns": [51, 52, 53, 54, 55, 22, 23, 24, 25, 26, 27],
			"format": /([\d]{1,4})([\d]{1,4})?([\d]{1,4})?([\d]{1,4})?/,
			"card_length": [16],
			"cvc_length": [3],
			"luhn": true
		},
		{
			"type": 'amex',
			"patterns": [34, 37],
			"format": /(\d{1,4})([\d]{0,6})?(\d{1,5})?/,
			"card_length": [15],
			"cvc_length": [3,4],
			"luhn": true
		}
	];
	this.validateCardNumber = function(card_number) {
		var card = this.getCardTypeFromNumber(card_number);
		if(card && parseInt(card_number) == card_number) {
			var i, allowed_length;
			for(i = 0; i < card.card_length.length; i++) {
				allowed_length = card.card_length[i];
				if(card_number.length == allowed_length) {
					if(card.luhn) {
						return this.luhnCheck(card_number);
					}
					else {
						return true;
					}
				}
			}
		}
		return false;
	}
	this.validateExpDate = function(month, year) {
		if(
			this.validateExpMonth(month) && 
			this.validateExpYear(year) && 
			new Date(year, month-1) >= new Date(new Date().getFullYear(), new Date().getMonth())
		) {
			return true;
		}
		return false;
	}
	this.validateExpMonth = function(month) {
		if(month && parseInt(month) == month && month >= 1 && month <= 12) {
			return true;
		}
		return false;
	}
	this.validateExpYear = function(year) {
		if(year && parseInt(year) == year && new Date(year, 0) >= new Date(new Date().getFullYear(), 0)) {
			return true;
		}
		return false;
	}
	this.validateCVC = function(cvc, card_number) {
		var cvc_length = [3,4];
		if(card_number && parseInt(card_number) == card_number) {
			var card = this.getCardTypeFromNumber(card_number);
			if(card) {
				cvc_length = card.cvc_length;
			}
		}
		if(cvc && parseInt(cvc) == cvc) {
			var i, allowed_length;
			for(i = 0; i < cvc_length.length; i++) {
				allowed_length = cvc_length[i];
				if(cvc.toString().length == allowed_length) {
					return true;
				}
			}
		}
		return false;
	}
	this.getCardTypeFromNumber = function(card_number) {
		var i, j, card, pattern, regex;
		for(i = 0; card = this.payment_cards[i]; i++) {
			for(j = 0; j < card.patterns.length; j++) {
				pattern = card.patterns[j];
				if(card_number.match('^' + pattern)) {
					return card;
				}
			}
		}
		return false;
	}
	this.formatCardNumber = function(card_number) {
		var card = this.getCardTypeFromNumber(card_number);
		if(card) {
			var matches = card_number.match(card.format);
			if(matches) {
				var matched_text = matches[0];
				matches.shift(); 
				var unmatched_suffix = card_number.slice(matched_text.length);
				matches.push(unmatched_suffix);
				card_number = matches.join(" ").trim().replace(/ +/g, " ");
			}
		}
		return card_number;
	}
	this.luhnCheck = function(card_number) {
		var ca, sum = 0, mul = 1;
		var len = card_number.length;
		while (len--) {
			ca = parseInt(card_number.charAt(len),10) * mul;
			sum += ca - (ca>9)*9;
			mul ^= 3;
		};
		return (sum%10 === 0) && (sum > 0);
	};
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
						if(typeof(page) !== 'undefined' && obj(page) && fun(page.notify)) {
							page.notify(response);
						}
						else if(typeof(app) !== 'undefined' && obj(app) && fun(app.notify)) {
							app.notify(response);
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

/*m-article.js*/
Util.Modules["article"] = new function() {
	this.init = function(article) {
		u.bug("article init:", article);
		article.csrf_token = article.getAttribute("data-csrf-token");
		article.header = u.qs("h1,h2,h3", article);
		article.header.article = article;
		var i, image;
		article._images = u.qsa("div.image,div.media", article);
		for(i = 0; image = article._images[i]; i++) {
			image.node = article;
			image.caption = u.qs("p a", image);
			if(image.caption) {
				image.caption.removeAttribute("href");
			}
			image._id = u.cv(image, "item_id");
			image._format = u.cv(image, "format");
			image._variant = u.cv(image, "variant");
			if(image._id && image._format) {
				image._image_src = "/images/" + image._id + "/" + (image._variant ? image._variant+"/" : "") + "540x." + image._format;
				image.loaded = function(queue) {
					u.ac(this, "loaded");
					this._image = u.ie(this, "img");
					this._image.image = this;
					this._image.src = queue[0].image.src;
					if(this.node.article_list) {
						this.node.article_list.correctScroll(this.node, this, -10);
					}
					// 	
				}
				u.preloader(image, [image._image_src]);
			}
		}
		var video;
		article._videos = u.qsa("div.youtube, div.vimeo", article);
		for (i = 0; video = article._videos[i]; i++) {
			video._src = u.cv(video, "video_id");
			video._type = video._src.match(/youtube|youtu\.be/) ? "youtube" : "vimeo";
			if (video._type == "youtube") {
				video._id = video._src.match(/watch\?v\=/) ? video._src.split("?v=")[1] : video._src.split("/")[video._src.split("/").length-1];
				video.iframe = u.ae(video, "iframe", {
					src: 'https://www.youtube.com/embed/'+video._id+'?autoplay=false&loop=0&color=f0f0ee&modestbranding=1&rel=0&playsinline=1',
					id: "ytplayer",
					type: "text/html",
					webkitallowfullscreen: true,
					mozallowfullscreen: true,
					allowfullscreen: true,
					frameborder: 0,
					allow: "autoplay",
					sandbox:"allow-same-origin allow-scripts",
					width: "100%",
					height: video.offsetWidth / 1.7777,
				});
			}
			else {
				video._id = video._src.split("/")[video._src.split("/").length-1];
				video.iframe = u.ae(video, "iframe", {
					src: 'https://player.vimeo.com/video/'+video._id+'?autoplay=false&loop=0&byline=0&portrait=0',
					webkitallowfullscreen: true,
					mozallowfullscreen: true,
					allowfullscreen: true,
					frameborder: 0,
					sandbox:"allow-same-origin allow-scripts",
					width: "100%",
					height: video.offsetWidth / 1.7777,
				});
			}
			console.log(video._id)
		}
		// 
	}
}


/*m-articles.js*/
Util.Modules["articles"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			page.cN.scene = this;
			var nodes = u.qsa("div.posts ul.items li.item", this);
			var i, node
			if(nodes) {
				for(i = 0; node = nodes[i]; i++) {
					node.image = u.qs("div.image", node);
					if(node.image) {
						// 
						node.image._id = u.cv(node.image, "item_id");
						node.image._format = u.cv(node.image, "format");
						node.image._variant = u.cv(node.image, "variant");
						if(node.image._id && node.image._format) {
							node.image._image_src = "/images/" + node.image._id + "/" + (node.image._variant ? node.image._variant+"/" : "") + "300x200." + node.image._format;
							node.image.loaded = function(queue) {
								u.ac(this, "loaded");
								u.ass(this, {
									"backgroundImage": "url("+queue[0].image.src+")"
								})
								// 	
							}
							u.preloader(node.image, [node.image._image_src]);
						}
					}
					u.ce(node, {type:"link", use: "h3 a"});
				}
			}
		}
		scene.ready();
	}
}


/*m-front.js*/
Util.Modules["front"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			page.cN.scene = this;
			var nodes = u.qsa("div.news ul.items li.item", scene);
			var i, node
			if(nodes) {
				for(i = 0; node = nodes[i]; i++) {
					node.image = u.qs("div.image", node);
					if(node.image) {
						// 
						node.image._id = u.cv(node.image, "item_id");
						node.image._format = u.cv(node.image, "format");
						node.image._variant = u.cv(node.image, "variant");
						if(node.image._id && node.image._format) {
							node.image._image_src = "/images/" + node.image._id + "/" + (node.image._variant ? node.image._variant+"/" : "") + "300x200." + node.image._format;
							node.image.loaded = function(queue) {
								u.ac(this, "loaded");
								u.ass(this, {
									"backgroundImage": "url("+queue[0].image.src+")"
								})
								// 	
							}
							u.preloader(node.image, [node.image._image_src]);
						}
					}
					u.ce(node, {type:"link", use: "h3 a"});
				}
			}
		}
		scene.ready();
	}
}


/*m-faq.js*/
Util.Modules["faq"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			var questions = u.qsa("ul.items li.question", this);
			var i, question, header;
			for(i = 0; i < questions.length; i++) {
				question = questions[i];
				header = u.qs("h2,h3", question);
				header.question = question;
				u.addExpandArrow(header);
				u.ce(header);
				header.clicked = function() {
					if(this.is_open) {
						this.is_open = false;
						u.rc(this.question, "open");
						u.addExpandArrow(this);
						u.deleteNodeCookie(this.question, "state");
					}
					else {
						this.is_open = true;
						u.ac(this.question, "open");
						u.addCollapseArrow(this);
						u.saveNodeCookie(this.question, "state", "open", {ignore_classnames:"open"});
					}
					if(!this.answer) {
						this.response = function(response) {
							if(response.isHTML) {
								this.answer = u.qs(".scene .article .articlebody", response);
								u.ae(this.question, this.answer);
							}
						}
						u.request(this, this.url);
					}
				}
				if(u.getNodeCookie(question, "state") == "open") {
					header.clicked();
				}
			}
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
			var form_login = u.qs("form.login", this);
			u.f.init(form_login);
			form_login.inputs["username"].focus();
		}
		scene.ready();
	}
}


/*m-accept_terms.js*/
Util.Modules["accept_terms"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			var form_accept = u.qs("form.accept", this);
			form_accept.scene = this;
			u.f.init(form_accept);
			form_accept.actions["reject"].clicked = function() {
				this._form.scene.overlay = u.overlay({title:"Vil du udmeldes?", height:200,width:600, class:"confirm_cancel_membership"});
				var p_warning = u.ae(this._form.scene.overlay.div_content, "p", {
					html:"Du er ved at melde dig ud af KBHFF. Pga. lovgivning og hensyn til persondata kan du ikke være medlem af KBHFF uden at acceptere vores vilkår. Vi håber du vil genoverveje."
				});
				var ul_actions = u.ae(this._form.scene.overlay.div_content, "ul", {
					class:"actions"
				})
				var delete_me = u.f.addAction(ul_actions, {"type":"button", "name":"delete_me", "class":"button","value":"Meld mig ud af KBHFF"});
				var regret = u.f.addAction(ul_actions, {"type":"button", "name":"regret", "class":"button primary", "value":"Fortryd udmelding"});
				u.e.click(delete_me)
				delete_me.scene = this._form.scene;
				delete_me.clicked = function () {
					this.response = function(response) {
						var form_confirm_cancellation = u.qs(".confirm_cancellation", response);
						form_confirm_cancellation.scene = this.scene;
						u.ass(p_warning, {"display":"none"});
						u.ass(ul_actions, {"display":"none"});
						u.ae(this.scene.overlay.div_content, form_confirm_cancellation);
						u.f.init(form_confirm_cancellation);
						form_confirm_cancellation.submitted = function () {
							var data = this.getData();
							this.response = function(response) {
								this.is_requesting = false;
								u.rc(this, "loading");
								if (response.cms_object == "JS-request") {
									console.log(response);
									location.href = "/";
								}
								else if (response != "JS-request") {
									if (message = u.qs("div.messages", response)) {
										u.ass(this, {"display":"none"})
										console.log(this);
										u.ae(this.scene.overlay.div_content, message);
										var ul_actions = u.ae(this.scene.overlay.div_content, "ul", {
											class:"actions"
										});
										var button_close = u.f.addAction(ul_actions, {"type":"button", "name":"button_close", "class":"button button_close primary","value":"Luk"});
										button_close.scene = this.scene;
										u.e.click(button_close)
										button_close.clicked = function () {
											this.scene.overlay.close ();
										}
									}
									else {
										location.href = "/";
									}
								}
							}
							if (!this.is_requesting) {
								this.is_requesting = true;
								u.ac(this, "loading");
								u.request(this, this.action, {"data":data, "method":"POST", "headers":{"X-Requested-With":"XMLHttpRequest"}});
							}
						}
					}
					if (!this.is_requesting) {
						this.is_requesting = true;
						u.ac(this, "loading");
						u.request(this, "/profil/opsig");
					}
				}
				u.e.click(regret);
				regret.scene = this._form.scene;
				regret.clicked = function () {
					this.scene.overlay.close ();
				}
			}
		}
		scene.ready();
	}
}


/*m-confirm_account.js*/
Util.Modules["confirm_account"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			u.bug("scene.ready:", this);
			var confirm_account = u.qs("form.confirm_account", this);
			u.f.init(confirm_account);
		}
		scene.ready();
	}
}


/*m-create_password.js*/
Util.Modules["create_password"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			u.bug("scene.ready:", this);
			var confirm_account = u.qs("form.create_password", this);
			u.f.init(confirm_account);
		}
		scene.ready();
	}
}


/*m-update_userinfo_form.js*/
Util.Modules["update_userinfo_form"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			var form = u.qs("form");
			u.f.init(form, this);
		}
		scene.ready();
	}
}


/*m-update_user_information.js*/
Util.Modules["user_information"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			var form = u.qs("form");
			u.f.init(form, this);
		}
		scene.ready();
	}
}


/*m-update_user_password.js*/
Util.Modules["user_password"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			var form = u.qs("form");
			u.f.init(form, this);
		}
		scene.ready();
	}
}


/*m-delete_user_information.js*/
Util.Modules["delete_user_information"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			var confirm_cancellation = u.qs("form.confirm_cancellation", this);
			if (confirm_cancellation) {
				u.f.init(confirm_cancellation);
			}
		}
		scene.ready();
	}
}


/*m-signupfees.js*/
Util.Modules["signupfees"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
		// 	
		// 	
		// 	
		}
		scene.ready();
	}
}


/*m-shop.js*/
Util.Modules["shop"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
			if(this.sidebar) {
				this.sidebar.start_y = u.absY(this.sidebar);
				this.scrolled();
			}
		}
		scene.scrolled = function() {
			if(this.sidebar) {
				// 	
				if(this.sidebar.start_y < page.scrolled_y && this.sidebar.offsetHeight < page.browser_h) {
					if(!this.sidebar.is_fixed) {
						this.sidebar.is_fixed = true;
						u.ac(this.sidebar, "fixed");
					}
					u.ass(this.sidebar, {
						transform: "translate3d(0, "+(page.scrolled_y - this.sidebar.start_y)+"px, 0)",
					});
				}
				else if(this.sidebar.is_fixed) {
					this.sidebar.is_fixed = false;
					u.rc(this.sidebar, "fixed");
					u.ass(this.sidebar, {
						transform: "none",
					});
				}
			}
		}
		scene.ready = function() {
			var form_login = u.qs("form.login", this);
			if(form_login) {
				u.f.init(form_login);
				form_login.inputs["username"].focus();
			}
			var products = u.qsa("li.product", this);
			if(products) {
				var i, j, pickupdate, product, image;
				for(i = 0; i < products.length; i++) {
					product = products[i];
					var images = u.qsa("div.image,div.media", product);
					for(j = 0; j < images.length; j++) {
						image = images[j];
						image.product = product;
						image._id = u.cv(image, "item_id");
						image._format = u.cv(image, "format");
						image._variant = u.cv(image, "variant");
						if(image._id && image._format) {
							image._image_src = "/images/" + image._id + "/" + (image._variant ? image._variant+"/" : "") + "380x." + image._format;
							image.loaded = function(queue) {
								u.ac(this, "loaded");
								u.ass(this, {
									"backgroundImage": "url("+queue[0].image.src+")"
								})
							}
							u.preloader(image, [image._image_src]);
						}
					}
					var pickupdate;
					var div_pickupdates = u.qs("div.pickupdates", product);
					div_pickupdates.ul_pickupdates = u.qs("ul.pickupdates", product);
					div_pickupdates.pickupdates = u.qsa("li.pickupdate", product);
					div_pickupdates.total_width = div_pickupdates.pickupdates.length*56;
					if(div_pickupdates.total_width >= div_pickupdates.offsetWidth) {
						div_pickupdates.current_x = 0;
						u.ac(div_pickupdates, "scroll");
						u.ass(div_pickupdates.ul_pickupdates, {
							width: div_pickupdates.total_width+"px"
						});
						div_pickupdates.bn_left = u.ae(div_pickupdates, "span", {"class":"left"});
						div_pickupdates.bn_left.div_pickupdates = div_pickupdates;
						u.ce(div_pickupdates.bn_left);
						div_pickupdates.bn_left.clicked = function() {
							if(this.div_pickupdates.current_x < 0) {
								this.div_pickupdates.current_x = this.div_pickupdates.current_x + 56;
								u.ass(this.div_pickupdates.ul_pickupdates, {
									transition: "all 0.5s ease-in-out",
									transform: "translate(" + this.div_pickupdates.current_x + "px, 0)"
								});
							}
							this.div_pickupdates.updateArrows();
						}
						div_pickupdates.bn_right = u.ae(div_pickupdates, "span", {"class":"right"});
						div_pickupdates.bn_right.div_pickupdates = div_pickupdates;
						u.ce(div_pickupdates.bn_right);
						div_pickupdates.bn_right.clicked = function() {
							if(this.div_pickupdates.current_x > this.div_pickupdates.offsetWidth - this.div_pickupdates.total_width) {
								this.div_pickupdates.current_x = this.div_pickupdates.current_x - 56;
								u.ass(this.div_pickupdates.ul_pickupdates, {
									transition: "all 0.5s ease-in-out",
									transform: "translate(" + this.div_pickupdates.current_x + "px, 0)"
								});
							}
							this.div_pickupdates.updateArrows();
						};
						div_pickupdates.updateArrows = function() {
							if(this.current_x <= this.offsetWidth - this.total_width) {
								u.ass(this.bn_right, {opacity: 0.3});
							}
							else {
								u.ass(this.bn_right, {opacity: 1});
							}
							if(this.current_x >= 0) {
								u.ass(this.bn_left, {opacity: 0.3});
							}
							else {
								u.ass(this.bn_left, {opacity: 1});
							}
						}
						div_pickupdates.updateArrows();
					}
					for(j = 0; j < div_pickupdates.pickupdates.length; j++) {
						pickupdate = div_pickupdates.pickupdates[j];
						pickupdate.scene = this;
						pickupdate.bn_add = u.qs("div.add", pickupdate);
						if(pickupdate.bn_add) {
							pickupdate.bn_add.title = "Tilføj til kurven";
							pickupdate.bn_add.pickupdate = pickupdate;
							pickupdate.bn_add.confirmed = function(response) {
								if(response.isHTML) {
									var scene_cart = u.qs("div.cart", this.pickupdate.scene);
									var response_cart = u.qs("div.cart", response);
									u.ass(response_cart, {
										opacity: 0.5
									});
									scene_cart.parentNode.replaceChild(response_cart, scene_cart);
									u.ass(response_cart, {
										transition: "opacity 0.1s ease-in-out",
										opacity: 1
									});
								}
							}
						}
					}
				}
			}
			this.sidebar = u.qs("div.sidebar", this);
			if(this.sidebar) {
				this.sidebar.start_y = u.absY(this.sidebar);
			}
		}
		scene.ready();
	}
}
Util.Modules["cart"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			this.total_cart_price = u.qs("div.total span.total_price", this);
			u.bug("this.total_cart_price", this.total_cart_price);
			this.cart_nodes = u.qsa("ul.items li.item", this);
			var i, node;
			for(i = 0; node = this.cart_nodes[i]; i++) {
				node.scene = this;
				node.item_id = u.cv(node, "id");
				node.pickupdate = u.cv(node, "date");
				node.unit_price = u.qs("span.unit_price", node);
				node.total_price = u.qs("span.total_price", node);
				node.quantity = u.qs("input[name=quantity]", node);
				var quantity_form = u.qs("form.updateCartItemQuantity", node)
				if(quantity_form) {
					quantity_form.node = node;
					u.f.init(quantity_form);
					quantity_form.inputs["quantity"].updated = function() {
						if(parseInt(this.val()) < 1) {
							this.val(1);
						}
							this._form.submit();
					}
					quantity_form.submitted = function() {
						this.response = function(response) {
				 			u.rc(this.actions["update"], "primary");
							if(response) {
								var total_price = u.qs("div.scene div.total span.total_price", response);
								var item_row;
								if(this.node.pickupdate) {
									item_row = u.ge("id:"+this.node.item_id+" date:"+this.node.pickupdate, response);
								}
								else {
									item_row = u.ge("id:"+this.node.item_id, response);
								}
								var item_total_price = u.qs("span.total_price", item_row);
								var item_unit_price = u.qs("span.unit_price", item_row);
								var item_quantity = u.qs("input[name=quantity]", item_row);
								this.node.scene.total_cart_price.innerHTML = total_price.innerHTML;
								this.node.total_price.innerHTML = item_total_price.innerHTML;
								this.node.unit_price.innerHTML = item_unit_price.innerHTML;
								this.node.quantity.value = item_quantity.value;
							}
							else {
								u.ac(this._form.actions["update"], "primary");
							}
						}
						u.request(this, this.action, {"method":"post", "data":this.getData()});
					}
				}
				// 			
			}
		}
		scene.ready();
	}
}
Util.Modules["checkout"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			var form_login = u.qs("form.login", this);
			if(form_login) {
				u.f.init(form_login);
				form_login.inputs["username"].focus();
			}
		}
		scene.ready();
	}
}
Util.Modules["shopProfile"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			var form = u.qs("form.details", this);
			if(form) {
				u.f.init(form);
			}
		}
		scene.ready();
	}
}
Util.Modules["shopAddress"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			var addresses = u.qsa("ul.addresses li.address", this);
			if(addresses) {
				var i, address, highest = 0;
				for(i = 0; i < addresses.length; i++) {
					address = addresses[i];
					if(address.offsetHeight > highest) {
						highest = address.offsetHeight;
					}
				}
				for(i = 0; i < addresses.length; i++) {
					address = addresses[i];
					u.ass(address, {
						height: highest+"px"
					})
				}
			}
			var form = u.qs("form.address", this);
			if(form) {
				u.f.init(form);
			}
		}
		scene.ready();
	}
}


/*m-signup.js*/
Util.Modules["signup"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			page.cN.scene = this;
			var signup_form = u.qs("form.signup", this);
			var place_holder = u.qs("div.articlebody .placeholder.signup", this);
			if(signup_form && place_holder) {
				place_holder.parentNode.replaceChild(signup_form, place_holder);
			}
			if(signup_form) {
				u.f.init(signup_form);
			}
			var verify_form = u.qs("form.verify_code", this);
			if(verify_form) {
				u.f.init(verify_form);
			}
			// 
			page.resized();
		}
		scene.ready();
	}
}


/*m-comments.js*/
Util.Modules["comments"] = new function() {
	this.init = function(div) {
		div.item_id = u.cv(div, "item_id");
		div.list = u.qs("ul.comments", div);
		div.comments = u.qsa("li.comment", div.list);
		div.header = u.qs("h2", div);
		div.header.div = div;
		u.addExpandArrow(div.header);
		u.ce(div.header);
		div.header.clicked = function() {
			if(u.hc(this.div, "open")) {
				u.rc(this.div, "open");
				u.addExpandArrow(this);
				u.saveCookie("comments_open_state", 0, {"path":"/"});
			}
			else {
				u.ac(this.div, "open");
				u.addCollapseArrow(this);
				u.saveCookie("comments_open_state", 1, {"path":"/"});
			}
		}
		div.comments_open_state = u.getCookie("comments_open_state", {"path":"/"});
		if(div.comments_open_state == 1) {
			div.header.clicked();
		}
		div.initComment = function(node) {
			node.div = this;
		}
		div.csrf_token = div.getAttribute("data-csrf-token");
		div.add_comment_url = div.getAttribute("data-comment-add");
		if(div.add_comment_url && div.csrf_token) {
			div.actions = u.ae(div, "ul", {"class":"actions"});
			div.bn_comment = u.ae(u.ae(div.actions, "li", {"class":"add"}), "a", {"html":u.txt["add_comment"], "class":"button primary comment"});
			div.bn_comment.div = div;
			u.ce(div.bn_comment);
			div.bn_comment.clicked = function() {
				var actions, bn_add, bn_cancel;
				u.as(this.div.actions, "display", "none");
				this.div.form = u.f.addForm(this.div, {"action":this.div.add_comment_url+"/"+this.div.item_id, "class":"add labelstyle:inject"});
				this.div.form.div = div;
				u.ae(this.div.form, "input", {"type":"hidden","name":"csrf-token", "value":this.div.csrf_token});
				u.f.addField(this.div.form, {"type":"text", "name":"item_comment", "label":u.txt["comment"]});
				actions = u.ae(this.div.form, "ul", {"class":"actions"});
				bn_add = u.f.addAction(actions, {"value":u.txt["add_comment"], "class":"button primary update", "name":"add"});
				bn_add.div = div;
				bn_cancel = u.f.addAction(actions, {"value":u.txt["cancel"], "class":"button cancel", "type":"button", "name":"cancel"});
				bn_cancel.div = div;
				u.f.init(this.div.form);
				this.div.form.submitted = function() {
					this.response = function(response) {
						if(response.cms_status == "success" && response.cms_object) {
							if(!div.list) {
								var p = u.qs("p", div);
								if(p) {
									p.parentNode.removeChild(p);
								}
								div.list = u.ie(div, "ul", {"class":"comments"});
								div.insertBefore(div.list, div.actions);
							}
							var comment_li = u.ae(this.div.list, "li", {"class":"comment comment_id:"+response.cms_object["id"]});
							var info = u.ae(comment_li, "ul", {"class":"info"});
							u.ae(info, "li", {"class":"created_at", "html":response.cms_object["created_at"]});
							u.ae(info, "li", {"class":"author", "html":response.cms_object["nickname"]});
							u.ae(comment_li, "p", {"class":"comment", "html":response.cms_object["comment"]})
							this.div.initComment(comment_li);
							this.parentNode.removeChild(this);
							u.as(this.div.actions, "display", "");
						}
					}
					u.request(this, this.action, {"method":"post", "data":this.getData()});
				}
				u.ce(bn_cancel);
				bn_cancel.clicked = function(event) {
					u.e.kill(event);
					this.div.form.parentNode.removeChild(this.div.form);
					u.as(this.div.actions, "display", "");
				}
			}
		}
		else {
			u.ae(div, "p", {"html": (u.txt["login_to_comment"] ? u.txt["login_to_comment"] : "Login or signup to comment")});
		}
		var i, node;
		for(i = 0; node = div.comments[i]; i++) {
			div.initComment(node);
		}
	}
}


/*m-stripe.js*/
Util.Modules["stripe"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.beforeUnload = function(event) {
			// 
			// 
			return "test";
		}
		scene.ready = function() {
			page.cN.scene = this;
			this.card_form = u.qs("form.card", this);
			u.f.customValidate["card"] = function(iN) {
				var card_number = iN.val().replace(/ /g, "");
				if(u.paymentCards.validateCardNumber(card_number)) {
					u.f.inputIsCorrect(iN);
					u.f.validate(iN._form.inputs["card_cvc"]);
				}
				else {
					u.f.inputHasError(iN);
				}
			}
			u.f.customValidate["exp_month"] = function(iN) {
				var month = iN.val();
				var year = iN._form.inputs["card_exp_year"].val();
				if(year && parseInt(year) < 100) {
					year = parseInt("20"+year);
				}
				if(u.paymentCards.validateExpMonth(month)) {
					u.f.inputIsCorrect(iN);
				}
				else {
					u.f.inputHasError(iN);
				}
				if(!iN.validating_year) {
					iN._form.inputs["card_exp_year"].validating_month = true;
					u.f.validate(iN._form.inputs["card_exp_year"]);
					iN._form.inputs["card_exp_year"].validating_month = false;
				}
			}
			u.f.customValidate["exp_year"] = function(iN) {
				var year = iN.val();
				var month = iN._form.inputs["card_exp_month"].val();
				if(year && parseInt(year) < 100) {
					year = parseInt("20"+year);
				}
				if(!iN.validating_month) {
					iN._form.inputs["card_exp_month"].validating_year = true;
					u.f.validate(iN._form.inputs["card_exp_month"]);
					iN._form.inputs["card_exp_month"].validating_year = false;
				}
				if(u.paymentCards.validateExpDate(month, year)) {
					u.f.inputIsCorrect(iN);
				}
				else if(!month && u.paymentCards.validateExpYear(year)) {
					u.rc(iN, "correct");
					u.rc(iN.field, "correct");
				}
				else {
					u.f.inputHasError(iN);
					u.f.inputHasError(iN._form.inputs["card_exp_month"]);
				}
			}
			u.f.customValidate["cvc"] = function(iN) {
				var cvc = iN.val();
				var card_number = iN._form.inputs["card_number"].val().replace(/ /g, "");
				if(u.paymentCards.validateCVC(cvc, card_number)) {
					u.f.inputIsCorrect(iN);
				}
				else {
					u.f.inputHasError(iN);
				}
			}
			u.f.init(this.card_form);
			this.card_form.submitted = function() {
				if(!this.is_submitting) {
					this.is_submitting = true;
					this.DOMsubmit();
				}
			}
			this.card_form.inputs["card_number"].updated = function(iN) {
				var value = this.val();
				this.value = u.paymentCards.formatCardNumber(value.replace(/ /g, ""));
				var card = u.paymentCards.getCardTypeFromNumber(value);
				if(card && card.type != this.current_card) {
					if(this.current_card) {
						u.rc(this, this.current_card);
					}
					this.current_card = card.type;
					u.ac(this, this.current_card);
				}
				else if(!card) {
					if(this.current_card) {
						u.rc(this, this.current_card);
					}
				}
			}
			this.card_form.inputs["card_exp_year"].changed = function(iN) {
				var year = parseInt(this.val());
				if(year > 99) {
					if(year > 2000 && year < 2100) {
						this.val(year-2000);
					}
				}
			}
			this.card_form.inputs["card_exp_month"].changed = function(iN) {
				var month = parseInt(this.val());
				if(month < 10) {
					this.val("0"+month);
				}
			}
			// 
			page.resized();
		}
		scene.ready();
	}
}

/*m-member_help_accept_terms.js*/
Util.Modules["member_help_accept_terms"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			var form_accept_terms = u.qs("form.accept", this);
			u.f.init(form_accept_terms);
		}
		scene.ready();
	}
}


/*m-member_help_signup.js*/
Util.Modules["member_help_signup"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			page.cN.scene = this;
			var signup_form = u.qs("form.member_help_signup", this);
			if(signup_form) {
				u.f.init(signup_form);
			}
			page.resized();
		}
		scene.ready();
	}
}


/*m-member_help_payment.js*/
Util.Modules["member_help_payment"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			u.bug("scene.ready:", this);
			var payment_options = u.qs("div.payment_options", this);
			var mobilepay_form = u.qs("form.mobilepay", payment_options);
			if(mobilepay_form) {
				var mobilepay_fieldset = u.qs("fieldset", mobilepay_form);
				var mobilepay_checkbox_field = u.qs(".field.checkbox", mobilepay_fieldset);
				var mobilepay_checkbox = u.qs("input[type=checkbox]", mobilepay_checkbox_field);
				var mobilepay_code_div = u.qs("div.code", mobilepay_fieldset);
			}
			var cash_form = u.qs("form.cash", payment_options);
			if(cash_form) {
				var cash_fieldset = u.qs("fieldset", cash_form);
				var cash_checkbox_field = u.qs(".field.checkbox", cash_fieldset);
				var cash_checkbox = u.qs("input[type=checkbox]", cash_checkbox_field);
				var cash_instructions = u.qs("div.instructions", cash_fieldset);
			}
			var card_form = u.qs("div.card", payment_options);
			if(card_form) {
				var card_fieldset = u.qs("div.fieldset", card_form);
			}
			var fieldset_height = u.actualHeight(mobilepay_fieldset);
			var mobilepay_code_div_height = u.actualHeight(mobilepay_code_div);
			if(mobilepay_form) {
				u.f.init(mobilepay_form);
			}
			if(cash_form) {
				u.as(cash_fieldset, "height", fieldset_height + "px"); 
				u.as(cash_instructions, "height", mobilepay_code_div_height + "px"); 
				u.f.init(cash_form);
			}
			if(card_form) {
				u.as(card_fieldset, "height", fieldset_height + "px"); 
				card_form.submitted = function() {
					if(!this.is_submitting) {
						this.is_submitting = true;
						this.DOMsubmit();
					}
				}
				// 
				// 
				// 
			}
			if(mobilepay_form && cash_form) {
				u.e.addEvent(mobilepay_checkbox_field, "change", function() {
					if(u.hc(mobilepay_checkbox_field, "checked")) {
						if(u.hc(cash_checkbox_field, "checked")) {
							u.rc(cash_checkbox_field, "checked")
							cash_checkbox.checked = false;
							u.f.validate(cash_checkbox);
						}
					}
				});
				u.e.addEvent(cash_checkbox_field, "change", function() {
					if(u.hc(cash_checkbox_field, "checked")) {
						if(u.hc(mobilepay_checkbox_field, "checked")) {
							u.rc(mobilepay_checkbox_field, "checked")
							mobilepay_checkbox.checked = false;
							u.f.validate(mobilepay_checkbox);
						}
					}
				});
			}
			if(cash_form) {
				var cash_tab = u.ie(payment_options, "h4", {"class":"tab cash_tab","html":"Kontant"});
				u.e.click(cash_tab);
				cash_tab.clicked = function () {
					u.as(mobilepay_form, "display", "none");
					u.as(card_form, "display", "none");
					u.as(cash_form, "display", "block");
					u.as(mobilepay_tab, "backgroundColor", "#BBBBBB");
					u.as(card_tab, "backgroundColor", "#BBBBBB");
					u.as(cash_tab, "backgroundColor", "#f2f2f2")
				}
			}
			if(card_form) {
				var card_tab = u.ie(payment_options, "h4", {"class":"tab card_tab","html":"Betalingskort"});
				u.e.click(card_tab);
				card_tab.clicked = function () {
					u.as(mobilepay_form, "display", "none");
					u.as(cash_form, "display", "none");
					u.as(card_form, "display", "block");
					u.as(mobilepay_tab, "backgroundColor", "#BBBBBB");
					u.as(cash_tab, "backgroundColor", "#BBBBBB");
					u.as(card_tab, "backgroundColor", "#f2f2f2")
				}
			}
			if(mobilepay_form) {
				var mobilepay_tab = u.ie(payment_options, "h4", {"class":"tab mobilepay_tab","html":"MobilePay"});
				u.e.click(mobilepay_tab);
				mobilepay_tab.clicked = function () {
					u.as(cash_form, "display", "none");
					u.as(card_form, "display", "none");
					u.as(mobilepay_form, "display", "block");
					u.as(cash_tab, "backgroundColor", "#BBBBBB");
					u.as(card_tab, "backgroundColor", "#BBBBBB");
					u.as(mobilepay_tab, "backgroundColor", "#f2f2f2");
				}
			}
		}
		scene.ready();
	}
}


/*m-member_help_index.js*/
Util.Modules["member_help"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			page.cN.scene = this;
			var search_form = u.qs("form.search_user", this);
			if(search_form) {
				search_form.scene = this;
				search_form.ul_users = u.qs("ul.users", this);
				search_form.h3_header = u.qs("div.users h3", this);
				search_form.p_no_results = u.qs("div.users p.no_results", this);
				search_form.p_type_to_search = u.qs("div.users p.type_to_search", this);
				search_form.template = u.qs("li.template", this);
				search_form.search_timeout = 300
				u.f.init(search_form);
				search_form.updated = function () {
					u.t.resetTimer(this.t_search);
					this.current_search = this.inputs.search_member.val();
					if (this.current_search.length > 3) {
						this.readyToSearch()
					}
					else {
						this.ul_users.innerHTML = "";
						u.ac(this.h3_header, "hidden");
						u.ac(this.p_no_results, "hidden");
						u.rc(this.p_type_to_search, "hidden");
					}
				}
				search_form.readyToSearch = function () {
					this.t_search = u.t.setTimer(this, this.search, this.search_timeout)
				}
				search_form.search = function () {
					this.response = function(response) {
						console.log(response);
						this.ul_users.innerHTML = "";
						this.users = u.template(this.template, response.cms_object.users);
						if(this.users.length) {
							u.rc(this.h3_header, "hidden");
							u.ac(this.p_no_results, "hidden");
							u.ac(this.p_type_to_search, "hidden");
							while (this.users.length) {
								this.user_info = u.qsa("ul.user_info li.search", this.users[0]);
								var i, user_info;
								for (i = 0; i < this.user_info.length; i++) {
									user_info = this.user_info[i];
									var match = this.current_search;
									var re = new RegExp(match, 'i');
									if (user_info.innerHTML.match(re)) {
										user_info.innerHTML = user_info.innerHTML.replace(re, "<span class=\"highlight_string\">$&</span>");
									}
								}
								u.ae(this.ul_users, this.users[0]); 
							}
						}
						else {
							u.ac(this.h3_header, "hidden");
							u.rc(this.p_no_results, "hidden");
							u.rc(this.p_type_to_search, "hidden");
						}
					}
					u.request(this, this.action+"soeg", {"method":"post", "data":this.getData()});
				}
			}	
				page.resized();
		}
		scene.ready();
	}
}


/*m-newsletter.js*/
Util.Modules["newsletter"] = new function() {
	this.init = function(div) {
		var form = u.qs("form", div);
		form.div = div;
		u.f.init(form);
		form.submitted = function() {
			this.DOMsubmit();
			this.reset();
			u.ae(this.div, "p", {html:"Tak for din tilmelding – husk at bekræfte din e-mailadresse via den tilsendte email."})
			u.ass(this, {
				display: "none"
			});
		}
	}
}

/*m-departments.js*/
Util.Modules["departments"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			page.cN.scene = this;
			this.map = u.ae(this, "div", {class:"map"});
			this.map.scene = this;
			this.insertBefore(this.map, u.qs("div.departmentlist", this));
			this.map.APIloaded = function() {
			}
			this.map.loaded = function() {
				u.googlemaps.infoWindow(this);
				var departments = u.qsa("ul.departments li.department", this.scene);
				var i, department;
				for(i = 0; i < departments.length; i++) {
					department = departments[i];
					department.latitude = parseFloat(u.qs("li.latitude", department).getAttribute("content"));
					department.longitude = parseFloat(u.qs("li.longitude", department).getAttribute("content"));
					if(department.latitude && department.longitude) {
						var marker = u.googlemaps.addMarker(this, [department.latitude, department.longitude]);
						marker.department = department;
						marker.g_map = this;
						marker.clicked = function() {
							var department_name = u.qs("h3 a", this.department).innerHTML;
							var email = u.qs("li.email a", this.department).getAttribute("content");
							u.googlemaps.showInfoWindow(this.g_map, this, '<h3><a href="/afdelinger/'+department_name+'">'+department_name+'</a></h3><p><a href="mailto:'+email+'">'+email+'</a></p>');
						}
					}
				}
			}
			u.googlemaps.map(this.map, [55.683801, 12.538368], {zoom: 11, disableUI: true, scrollwheel: false});
			page.resized();
		}
		scene.ready();
	}
}


/*m-department_view.js*/
Util.Modules["departmentView"] = new function() {
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


/*m-user_profile.js*/
Util.Modules["user_profile"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			this.initMembershipBox();
			this.initUserinfoBox();
			this.initRenewalBox();
			this.initOrderList();
		}
		scene.initMembershipBox = function() {
			var box_membership = u.qs(".membership > .c-box", this);
			var button_membership = u.qs(".membership li.change-membership", this);
			var button_cancel = u.qs(".membership li.cancel-membership", this);
			var button_department = u.qs(".membership li.change-department", this);
			var section_user_group = u.qs(".section.user_group");
			var right_panel = u.qs(".c-one-third", this);
			if(button_department) {
				button_department.scene = this;
				u.clickableElement(button_department); 
				button_department.clicked = function() {
					this.response = function(response) {
						this.is_requesting = false;
						u.rc(this, "loading");
						var form_department = u.qs(".form_department", response);
						form_department.scene = this.scene;
						var warning = u.qs("p.warning", response);
						var form_fieldset = u.qs("fieldset", form_department);
						var div_fields = u.qs("div.fields", box_membership);
						var divs_membership = u.qsa(".membership-info", div_fields)	;
						var ul_buttons = u.qs("ul.actions", div_fields);
						u.ass(divs_membership[3], {"display":"none"});
						u.ass(ul_buttons, {"display":"none"});
						u.ae(box_membership, form_department);
						u.f.init(form_department);
						u.ie(form_department, div_fields);
						u.ae(div_fields, form_fieldset);
						if(warning) {
							u.ae(div_fields, warning);
						}
						form_department.submitted = function() {
							var data = this.getData();
							this.response = function(response) {
								this.is_requesting = false;
								u.rc(this, "loading");
								var div_membership = u.qs(".membership .fields", response);
								box_membership.replaceChild(div_membership, form_department);
								message = u.qs("div.messages", response);
								if (message) {
									u.ie(box_membership, message);
									message.transitioned = function() {
										message.innerHTML = "";
									}
									u.a.transition(message, "all 4s ease-in");
									u.a.opacity(message, 0.5);	
								}
								this.scene.initMembershipBox();
							}
							if (!this.is_requesting) {
								this.is_requesting = true;
								u.ac(this, "loading");
								u.request(this, this.action, {"data":data, "method":"POST"});
							}
						}
						form_department.actions["cancel"].clicked = function() {
							this.response = function(response) {
								this.is_requesting = false;
								u.rc(this, "loading");
								var div_membership = u.qs(".membership .fields", response);
								box_membership.replaceChild(div_membership, this._form);
								this._form.scene.initMembershipBox();
							}
							if (!this.is_requesting) {
								this.is_requesting = true;
								u.ac(this, "loading");
								u.request(this, this.baseURI);
							}
						}
					}
					if (!this.is_requesting) {
						this.is_requesting = true;
						u.ac(this, "loading");
						u.request(this, this.url);
					}
				}
			}
			if(button_membership) {
				button_membership.scene = this;
				u.clickableElement(button_membership); 
				button_membership.clicked = function() {
					this.response = function(response) {
						this.is_requesting = false;
						u.rc(this, "loading");
						var form_membership = u.qs(".form_membership", response);
						form_membership.scene = this.scene;
						var form_fieldset = u.qs("fieldset", form_membership);
						var div_fields = u.qs("div.fields", box_membership);
						var divs_membership = u.qsa(".membership-info", div_fields)	;
						var ul_buttons = u.qs("ul.actions", div_fields);
						u.ass(divs_membership[2], {"display":"none"});
						u.ass(ul_buttons, {"display":"none"});
						u.ae(box_membership, form_membership);
						u.f.init(form_membership);
						u.ie(form_membership, div_fields);
						div_fields.insertBefore(form_fieldset, divs_membership[1].nextSibling);
						form_membership.submitted = function() {
							var data = this.getData();
							this.response = function(response) {
								this.is_requesting = false;
								u.rc(this, "loading");
								var div_membership = u.qs(".membership .fields", response);
								box_membership.replaceChild(div_membership, form_membership);
								var new_section_user_group = u.qs(".section.user_group", response);
								section_user_group.parentNode.replaceChild(new_section_user_group, section_user_group);
								if (message = u.qs("div.messages", response)) {
									u.ie(box_membership, message);
									message.transitioned = function() {
										message.innerHTML = "";
									}
									u.a.transition(message, "all 4s ease-in");
									u.a.opacity(message, 0.5);	
								}
								this.scene.initMembershipBox();
							}
							if (!this.is_requesting) {
								this.is_requesting = true;
								u.ac(this, "loading");
								u.request(this, this.action, {"data":data, "method":"POST"});
							}
						}
						form_membership.actions["cancel"].clicked = function() {
							this.response = function(response) {
								this.is_requesting = false;
								u.rc(this, "loading");
								var div_membership = u.qs(".membership .fields", response);
								box_membership.replaceChild(div_membership, this._form);
								this._form.scene.initMembershipBox();
							}
							if (!this.is_requesting) {
								this.is_requesting = true;
								u.ac(this, "loading");
								u.request(this, this.baseURI);
							}
						}
					}
					if (!this.is_requesting) {
						this.is_requesting = true;
						u.ac(this, "loading");
						u.request(this, this.url);
					}
				}
			}
			if(button_cancel) {
				button_cancel.scene = this; 
				u.clickableElement(button_cancel);
				button_cancel.clicked = function() {
					this.scene.url = this.url;
					console.log(this.scene.url);
					this.scene.overlay = u.overlay({title:"Du er ved at udmelde et medlem.", height:200,width:600, class:"confirm_cancel_membership"});
					var p_warning = u.ae(this.scene.overlay.div_content, "p", {
						html:"Du er ved at melde et medlem ud af KBHFF. Er du sikker?"
					});
					var ul_actions = u.ae(this.scene.overlay.div_content, "ul", {
						class:"actions"
					});
					var delete_me = u.f.addAction(ul_actions, {"type":"button", "name":"delete_me", "class":"button delete_me","value":"Meld medlemmet ud af KBHFF"});
					var regret = u.f.addAction(ul_actions, {"type":"button", "name":"regret", "class":"button regret primary", "value":"Fortryd udmelding"});
					delete_me.scene = this.scene;
					regret.scene = this.scene;
					u.e.click(delete_me)
					delete_me.clicked = function () {
						this.response = function(response) {
							this.is_requesting = false;
							u.rc(this, "loading");
							var confirm_cancellation = u.qs(".scene.delete_user_information", response);
							confirm_cancellation.scene = this.scene;
							u.ass(this.scene.overlay.div_header.h2, {"display":"none"});
							u.ass(p_warning, {"display":"none"});
							u.ass(ul_actions, {"display":"none"});
							u.ae(this.scene.overlay.div_content, confirm_cancellation);
							var form_confirm_cancellation = u.qs("form.confirm_cancellation");
							form_confirm_cancellation.scene = this.scene;
							u.f.init(form_confirm_cancellation);
							form_confirm_cancellation.submitted = function () {
								var data = this.getData();
								this.response = function(response) {
									this.is_requesting = false;
									u.rc(this, "loading");
									if (response.cms_object == "JS-request") {
										location.href = "/medlemshjaelp";
									}
									else if (response.cms_object != "JS-request") {
										if (message = u.qs("div.messages", response)) {
											u.ass(this, {"display":"none"})
											u.ae(this.scene.overlay.div_content, message);
											var ul_actions = u.ae(this.scene.overlay.div_content, "ul", {
												class:"actions"
											});
											var button_close = u.f.addAction(ul_actions, {"type":"button", "name":"button_close", "class":"button button_close primary","value":"Luk"});
											button_close.scene = this.scene;
											u.e.click(button_close)
											button_close.clicked = function () {
												this.scene.overlay.close ();
											}
										}
										else {
											location.href = "/medlemshjaelp";
										}
									}
								}
								if (!this.is_requesting) {
									this.is_requesting = true;
									u.ac(this, "loading");
									u.request(this, this.action, {"data":data, "method":"POST", "headers":{"X-Requested-With":"XMLHttpRequest"}});
								}
							}
						}
						if (!this.is_requesting) {
							this.is_requesting = true;
							u.ac(this, "loading");
							u.request(this, this.scene.url);
						}
					}
					u.e.click(regret)
					regret.clicked = function () {
						this.scene.overlay.close ();
					}
				}
			}
		}
		scene.initUserinfoBox = function() {
			var box_userinfo = u.qs(".user > .c-box", this);
			var button_userinfo = u.qs(".user li", this);
			button_userinfo.scene = this;
			u.clickableElement(button_userinfo);
			button_userinfo.clicked = function() {
				this.response = function(response) {
					this.is_requesting = false;
					u.rc(this, "loading");
					var form_userinfo = u.qs(".form_user", response);
					form_userinfo.scene = this.scene;
					var div_fields = u.qs("div.fields", box_userinfo);
					box_userinfo.replaceChild(form_userinfo, div_fields);
					u.f.init(form_userinfo);
					form_userinfo.submitted = function() {
						var data = this.getData();
						this.response = function(response) {
							this.is_requesting = false;
							u.rc(this, "loading");
							var div_userinfo = u.qs(".user .fields", response);
							box_userinfo.replaceChild(div_userinfo, form_userinfo);
							if (message = u.qs("div.messages", response)) {
								u.ie(box_userinfo, message);
								message.transitioned = function() {
									message.innerHTML = "";
								}
								u.a.transition(message, "all 4s ease-in");
								u.a.opacity(message, 0.5);	
							}
							this.scene.initUserinfoBox();
						}
						if (!this.is_requesting) {
							this.is_requesting = true;
							u.ac(this, "loading");
							u.request(this, this.action, {"data":data, "method":"POST"});
						}
					}
					form_userinfo.actions["cancel"].clicked = function() {
						this.response = function(response) {
							this.is_requesting = false;
							u.rc(this, "loading");
							var div_userinfo = u.qs(".user .fields", response);
							box_userinfo.replaceChild(div_userinfo, form_userinfo);
							this._form.scene.initUserinfoBox();
						}
						if (!this.is_requesting) {
							this.is_requesting = true;
							u.ac(this, "loading");
							u.request(this, this.url);
						}
					}
				}
				if (!this.is_requesting) {
					this.is_requesting = true;
					u.ac(this, "loading");
					u.request(this, this.url);
				}
			}
		}
		scene.initRenewalBox = function() {
			var box_renewal = u.qs(".renewal > .c-box", this);
			var button_renewal = u.qs(".renewal li", this);
			if(button_renewal) {
				button_renewal.scene = this;
				u.clickableElement(button_renewal);
				button_renewal.clicked = function() {
					this.response = function(response) {
						this.is_requesting = false;
						u.rc(this, "loading");
						var form_renewal = u.qs(".form_renewal", response);
						form_renewal.scene = this.scene;
						var div_fields = u.qs("div.fields", box_renewal);
						box_renewal.replaceChild(form_renewal, div_fields);
						u.f.init(form_renewal);
						form_renewal.submitted = function() {
							var data = this.getData();
							this.response = function(response, request_id) {
								this.is_requesting = false;
								u.rc(this, "loading");
								if (message = u.qs("p.error", this)) {
									message.parentNode.removeChild(message);
								}
								if (message = u.qs("div.messages > p.error", response)) {
									u.ass(message, {
										"padding-bottom":"5px",
										"transform":"translate3d(0px, -10px, 0px)",
										"opacity":"0"
									});
									u.ie(this, message);	
									u.a.transition(message, "all .5s ease");
									u.ass(message, {
										"transform":"translate3d(0px, 0, 0px)",
										"opacity":"1"
									});
								}
								var div_renewal = u.qs(".renewal .fields", response);
								if(div_renewal) {
									box_renewal.replaceChild(div_renewal, this);
									if (message = u.qs("p.message", response)) {
										var fields = u.qs("div.fields", box_renewal);
										u.ie(fields, message);
										if (message.t_done) {
											u.t.resetTimer(t_done);
										}
										message.done = function() {
											u.ass(this, {
												"transition":"all .5s ease",
												"transform":"translate3d(0px, -10px, 0px)",
												"opacity":"0"
											});
											u.t.setTimer(this, function() {
												this.parentNode.removeChild(this);
											}, 500);
										}
										message.transitioned = function() {
											this.t_done = u.t.setTimer(this, this.done, 2400);
										}
										u.ass(message, {
											"color":"#3e8e17",
											"padding-bottom":"5px",
											"transform":"translate3d(0px, -10px, 0px)",
											"opacity":"0"
										});
										u.a.transition(message, "all 1s ease");
										u.ass(message, {
											"transform":"translate3d(0px, 0, 0px)",
											"opacity":"1"
										});
									}
									this.scene.initRenewalBox();
								}
								else {
									if(this[request_id].request_url != this[request_id].response_url) {
										location.href = this[request_id].response_url;
									}
								}
							}
							if (!this.is_requesting) {
								this.is_requesting = true;
								u.ac(this, "loading");
								u.request(this, this.action, {"data":data, "method":"POST"});
							}
						}
						form_renewal.actions["cancel"].clicked = function() {
							this.response = function(response) {
								this.is_requesting = false;
								u.rc(this, "loading");
								var div_userinfo = u.qs(".renewal .fields", response);
								box_renewal.replaceChild(div_userinfo, this._form);
								this._form.scene.initRenewalBox();
							}
							if (!this.is_requesting) {
								this.is_requesting = true;
								u.ac(this, "loading");
								u.request(this, this.url);
							}
						}
					}
					if (!this.is_requesting) {
						this.is_requesting = true;
						u.ac(this, "loading");
						u.request(this, this.url);
					}
				}
			}
		}
		scene.initOrderList = function() {
			var orders = u.qsa("li.order_item", this);
			var i, order;
			for(i = 0; i < orders.length; i++) {
				order = orders[i];
				order.order_item_id = u.cv(order, "order_item_id");
				order.span_pickupdate = u.qs("span.pickupdate", order);
				order.span_date = u.qs("span.date", order.span_pickupdate);
				order.bn_edit = u.qs("li.change a.button:not(.disabled)", order);
				if(order.bn_edit) {
					order.bn_edit.order = order;
					u.ce(order.bn_edit);
					order.bn_edit.clicked = function() {
						if(u.hc(this.order, "edit")) {
							this.form.submit();
						}
						else {
							u.ac(this.order, "edit");
							this.response = function(response) {
								this.form = u.qs("form", response);
								if(this.form) {
									this.form.order = this.order;
									this.innerHTML = "Gem";
									u.ac(this, "primary");
									u.ae(this.order.span_pickupdate, this.form);
									u.f.init(this.form);
									this.form.submitted = function() {
										this.response = function(response) {
											this.order.bn_edit.form.parentNode.removeChild(this.order.bn_edit.form);
											delete this.order.bn_edit.form;
											this.order.span_date.innerHTML = u.qs("span.date", u.ge("order_item_id:"+this.order.order_item_id, response)).innerHTML;
											u.rc(this.order, "edit");
											this.order.bn_edit.innerHTML = "Ret";
											u.rc(this.order.bn_edit, "primary");
										}
										u.request(this, this.action, {"method":"post", "data":this.getData()});
									}
								}
							}
							u.request(this, this.url);
						}
					}
				}
			}
		}
		scene.ready();
	}
}


/*m-profile.js*/
Util.Modules["profile"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			//			
			this.initMembershipBox();
			this.initUserinfoBox();
			this.initPasswordBox();
			this.initRenewalBox();
			this.initOrderList();
		}
		scene.initMembershipBox = function() {
			var box_membership = u.qs(".membership > .c-box", this);
			var button_membership = u.qs(".membership li.change-info", this);
			var button_cancel = u.qs(".membership li.cancel-membership", this);
			var right_panel = u.qs(".c-one-third", this);
			var box_department = u.qs(".section.department", this);
			if(button_membership) {
				button_membership.scene = this;
				button_membership.a = u.qs("a", button_membership);
				if(button_membership.a.getAttribute("href")) {
					u.clickableElement(button_membership); 
					button_membership.clicked = function() {
						this.response = function(response) {
							this.is_requesting = false;
							u.rc(this, "loading");
							var form_department = u.qs(".form_department", response);
							form_department.scene = this.scene;
							var warning = u.qs("p.warning", response);
							var div_fields = u.qs("div.fields", box_membership);
							var divs_membership = u.qsa(".membership-info", div_fields);
							var ul_buttons = u.qs("ul.actions", div_fields);
							u.ass(divs_membership[3], {"display":"none"});
							u.ass(ul_buttons, {"display":"none"});
							u.ae(box_membership, form_department);
							u.f.init(form_department);
							u.ae(div_fields, form_department);
							form_department.save = u.qs(".save input", form_department);
							form_department.department_select = u.qs("#input_department_id", form_department);
							form_department.department_select.initial_value = form_department.department_select.value;
							form_department.changed = function() {
								if(this.department_select.value === this.department_select.initial_value) {
									u.ac(this.save, "disabled")
								}
								else {
									u.rc(this.save, "disabled");
								}
							}
							if(warning) {
								u.ae(div_fields, warning);
							}
							form_department.submitted = function() {
								var data = this.getData();
								this.response = function(response) {
									this.is_requesting = false;
									u.rc(this, "loading");
									var new_fields = u.qs(".membership .fields", response);
									box_membership.replaceChild(new_fields, div_fields);
									var old_department_address = u.qs(".department .c-box");
									var new_department_address = u.qs(".department .c-box", response);
									box_department.replaceChild(new_department_address, old_department_address);
									if (message = u.qs("p.message", response)) {
										var fields = u.qs("div.fields", box_membership)
										u.ie(fields, message);
										if (message.t_done) {
											u.t.resetTimer(t_done);
										}
										message.done = function() {
											u.ass(this, {
												"transition":"all .5s ease",
												"transform":"translate3d(0px, -10px, 0px)",
												"opacity":"0"
											});
											u.t.setTimer(this, function() {
												this.parentNode.removeChild(this);
											}, 500);
										}
										message.transitioned = function() {
											this.t_done = u.t.setTimer(this, this.done, 2400);
										}
										u.ass(message, {
											"color":"#3e8e17",
											"padding-bottom":"5px",
											"transform":"translate3d(0px, -10px, 0px)",
											"opacity":"0"
										});
										u.a.transition(message, "all .5s ease");
										u.ass(message, {
											"transform":"translate3d(0px, 0, 0px)",
											"opacity":"1"
										});
									}
									this.scene.initMembershipBox();
								}
								if (!this.is_requesting) {
									this.is_requesting = true;
									u.ac(this, "loading");
									u.request(this, this.action, {"data":data, "method":"POST"});
								}
							}
							form_department.actions["cancel"].clicked = function() {
								this.response = function(response) {
									this.is_requesting = false;
									u.rc(this, "loading");
									var new_fields = u.qs(".membership .fields", response);
									box_membership.replaceChild(new_fields, div_fields);
									this._form.scene.initMembershipBox();
								}
								if (!this.is_requesting) {
									this.is_requesting = true;
									u.ac(this, "loading");
									u.request(this, "/profil");
								}
							}
						}
						if (!this.is_requesting) {
							this.is_requesting = true;
							u.ac(this, "loading");
							u.request(this, "/profil/afdeling");
						}
					}
				}
			}
			if(button_cancel) {
				button_cancel.scene = this; 
				u.clickableElement(button_cancel);
				button_cancel.clicked = function() {
					this.scene.overlay = u.overlay({title:"Vil du virkelig udmeldes?", height:365,width:600, class:"confirm_cancel_membership"});
					var p_warning = u.ae(this.scene.overlay.div_content, "p", {
						html:"Hvis du udmelder dig, bliver din konto slettet. Du vil ikke kunne logge ind, og du giver afkald på alle fremtidige bestillinger – også selv om du har betalt dem."
					});
					var p_alternative = u.ae(this.scene.overlay.div_content, "p", {
						html:"Du kan i stedet vælge at deaktivere dit medlemskab, når det udløber. Dette gøres ved at fravælge automatisk fornyelse nederst til højre på Min Side. Som inaktivt medlem betaler du ikke kontingent, og du kan ikke lave nye bestillinger, men du kan stadig se dine eksisterende ordrer, vagter m.m."
					});
					p_confirmation = u.ae(this.scene.overlay.div_content, "p", {
						html:"Er du sikker på, at du vil melde dig ud af KBHFF?"
					});
					var ul_actions = u.ae(this.scene.overlay.div_content, "ul", {
						class:"actions"
					});
					var delete_me = u.f.addAction(ul_actions, {"type":"button", "name":"delete_me", "class":"button delete_me","value":"Ja, slet mine data"});
					var regret = u.f.addAction(ul_actions, {"type":"button", "name":"regret", "class":"button regret primary", "value":"Fortryd udmelding"});
					delete_me.scene = this.scene;
					regret.scene = this.scene;
					u.e.click(delete_me)
					delete_me.clicked = function () {
						this.response = function(response) {
							this.is_requesting = false;
							u.rc(this, "loading");
							var form_confirm_cancellation = u.qs(".confirm_cancellation", response);
							form_confirm_cancellation.scene = this.scene;
							u.ass(p_warning, {"display":"none"});
							u.ass(p_alternative, {"display":"none"});
							u.ass(p_confirmation, {"display":"none"});
							u.ass(ul_actions, {"display":"none"});
							u.ae(this.scene.overlay.div_content, form_confirm_cancellation);
							u.f.init(form_confirm_cancellation);
							form_confirm_cancellation.submitted = function () {
								var data = this.getData();
								this.response = function(response) {
									this.is_requesting = false;
									u.rc(this, "loading");
									if (response.cms_object == "JS-request") {
										console.log(response);
										location.href = "/";
									}
									else if (response != "JS-request") {
										if (message = u.qs("div.messages", response)) {
											u.ass(this, {"display":"none"})
											console.log(this);
											u.ae(this.scene.overlay.div_content, message);
											var ul_actions = u.ae(this.scene.overlay.div_content, "ul", {
												class:"actions"
											});
											var button_close = u.f.addAction(ul_actions, {"type":"button", "name":"button_close", "class":"button button_close primary","value":"Luk"});
											button_close.scene = this.scene;
											u.e.click(button_close)
											button_close.clicked = function () {
												this.scene.overlay.close ();
											}
										}
										else {
											location.href = "/";
										}
									}
								}
								if (!this.is_requesting) {
									this.is_requesting = true;
									u.ac(this, "loading");
									u.request(this, this.action, {"data":data, "method":"POST", "headers":{"X-Requested-With":"XMLHttpRequest"}});
								}
							}
						}
						if (!this.is_requesting) {
							this.is_requesting = true;
							u.ac(this, "loading");
							u.request(this, "/profil/opsig");
						}
					}
					u.e.click(regret)
					regret.clicked = function () {
						this.scene.overlay.close ();
					}
				}
			}
		}
		scene.initUserinfoBox = function() {
			var box_userinfo = u.qs(".user > .c-box", this);
			var button_userinfo = u.qs(".user li", this);
			var intro_header = u.qs(".section.intro > h1", this);
			var span_name = u.qs("span.name", this);
			if(button_userinfo) {
				button_userinfo.scene = this;
				u.clickableElement(button_userinfo);
				button_userinfo.clicked = function() {
					this.response = function(response) {
						this.is_requesting = false;
						u.rc(this, "loading");
						var form_userinfo = u.qs(".form_user", response);
						form_userinfo.scene = this.scene;
						var div_fields = u.qs("div.fields", box_userinfo);
						box_userinfo.replaceChild(form_userinfo, div_fields);
						u.f.init(form_userinfo);
						form_userinfo.submitted = function() {
							var data = this.getData();
							this.response = function(response) {
								this.is_requesting = false;
								u.rc(this, "loading");
								var div_userinfo = u.qs(".user .fields", response);
								box_userinfo.replaceChild(div_userinfo, form_userinfo);
								var new_name = u.qs("span.name", response);
								intro_header.replaceChild(new_name, span_name);
								if (message = u.qs("p.message", response)) {
									var fields = u.qs("div.fields", box_userinfo);
									u.ie(fields, message);
									if (message.t_done) {
										u.t.resetTimer(t_done);
									}
									message.done = function() {
										u.ass(this, {
											"transition":"all .5s ease",
											"transform":"translate3d(0px, -10px, 0px)",
											"opacity":"0"
										});
										u.t.setTimer(this, function() {
											this.parentNode.removeChild(this);
										}, 500);
									}
									message.transitioned = function() {
										this.t_done = u.t.setTimer(this, this.done, 2400);
									}
									u.ass(message, {
										"color":"#3e8e17",
										"padding-bottom":"5px",
										"transform":"translate3d(0px, -10px, 0px)",
										"opacity":"0"
									});
									u.a.transition(message, "all .5s ease");
									u.ass(message, {
										"transform":"translate3d(0px, 0, 0px)",
										"opacity":"1"
									});
								}
								this.scene.initUserinfoBox();
							}
							if (!this.is_requesting) {
								this.is_requesting = true;
								u.ac(this, "loading");
								u.request(this, this.action, {"data":data, "method":"POST"});
							}
						}
						form_userinfo.actions["cancel"].clicked = function() {
							this.response = function(response) {
								this.is_requesting = false;
								u.rc(this, "loading");
								var div_userinfo = u.qs(".user .fields", response);
								box_userinfo.replaceChild(div_userinfo, form_userinfo);
								this._form.scene.initUserinfoBox();
							}
							if (!this.is_requesting) {
								this.is_requesting = true;
								u.ac(this, "loading");
								u.request(this, "/profil");
							}
						}
					}
					if (!this.is_requesting) {
						this.is_requesting = true;
						u.ac(this, "loading");
						u.request(this, "/profil/bruger");
					}
				}
			}
		}
		scene.initPasswordBox = function() {
			var box_password = u.qs(".password > .c-box", this);
			var button_password = u.qs(".password li", this);
			if(button_password) {
				button_password.scene = this;
				u.clickableElement(button_password);
				button_password.clicked = function() {
					this.response = function(response) {
						this.is_requesting = false;
						u.rc(this, "loading");
						var form_password = u.qs(".form_password", response);
						form_password.scene = this.scene;
						var div_fields = u.qs("div.fields", box_password);
						box_password.replaceChild(form_password, div_fields);
						u.f.init(form_password);
						form_password.submitted = function() {
							var data = this.getData();
							this.response = function(response) {
								this.is_requesting = false;
								u.rc(this, "loading");
								if (message = u.qs("p.error", this)) {
									message.parentNode.removeChild(message);
								}
								if (message = u.qs("div.messages > p.error", response)) {
									u.ass(message, {
										"padding-bottom":"5px",
										"transform":"translate3d(0px, -10px, 0px)",
										"opacity":"0"
									});
									u.ie(this, message);	
									u.a.transition(message, "all .5s ease");
									u.ass(message, {
										"transform":"translate3d(0px, 0, 0px)",
										"opacity":"1"
									});
								}
								var div_password = u.qs(".password .fields", response);
								box_password.replaceChild(div_password, this);
								if (message = u.qs("p.message", response)) {
									var fields = u.qs("div.fields", box_password);
									u.ie(fields, message);
									if (message.t_done) {
										u.t.resetTimer(t_done);
									}
									message.done = function() {
										u.ass(this, {
											"transition":"all .5s ease",
											"transform":"translate3d(0px, -10px, 0px)",
											"opacity":"0"
										});
										u.t.setTimer(this, function() {
											this.parentNode.removeChild(this);
										}, 500);
									}
									message.transitioned = function() {
										this.t_done = u.t.setTimer(this, this.done, 2400);
									}
									u.ass(message, {
										"color":"#3e8e17",
										"padding-bottom":"5px",
										"transform":"translate3d(0px, -10px, 0px)",
										"opacity":"0"
									});
									u.a.transition(message, "all 1s ease");
									u.ass(message, {
										"transform":"translate3d(0px, 0, 0px)",
										"opacity":"1"
									});
								}
								this.scene.initPasswordBox();
							}
							if (!this.is_requesting) {
								this.is_requesting = true;
								u.ac(this, "loading");
								u.request(this, this.action, {"data":data, "method":"POST"});
							}
						}
						form_password.actions["cancel"].clicked = function() {
							this.response = function(response) {
								this.is_requesting = false;
								u.rc(this, "loading");
								var div_userinfo = u.qs(".password .fields", response);
								box_password.replaceChild(div_userinfo, this._form);
								this._form.scene.initPasswordBox();
							}
							if (!this.is_requesting) {
								this.is_requesting = true;
								u.ac(this, "loading");
								u.request(this, "/profil");
							}
						}
					}
					if (!this.is_requesting) {
						this.is_requesting = true;
						u.ac(this, "loading");
						u.request(this, "/profil/kodeord");
					}
				}
			}
		}
		scene.initRenewalBox = function() {
			var box_renewal = u.qs(".renewal > .c-box", this);
			var button_renewal = u.qs(".renewal li", this);
			if(button_renewal) {
				button_renewal.scene = this;
				u.clickableElement(button_renewal);
				button_renewal.clicked = function() {
					this.response = function(response) {
						this.is_requesting = false;
						u.rc(this, "loading");
						var form_renewal = u.qs(".form_renewal", response);
						form_renewal.scene = this.scene;
						var div_fields = u.qs("div.fields", box_renewal);
						box_renewal.replaceChild(form_renewal, div_fields);
						u.f.init(form_renewal);
						form_renewal.submitted = function() {
							var data = this.getData();
							this.response = function(response, request_id) {
								this.is_requesting = false;
								u.rc(this, "loading");
								if (message = u.qs("p.error", this)) {
									message.parentNode.removeChild(message);
								}
								if (message = u.qs("div.messages > p.error", response)) {
									u.ass(message, {
										"padding-bottom":"5px",
										"transform":"translate3d(0px, -10px, 0px)",
										"opacity":"0"
									});
									u.ie(this, message);	
									u.a.transition(message, "all .5s ease");
									u.ass(message, {
										"transform":"translate3d(0px, 0, 0px)",
										"opacity":"1"
									});
								}
								var div_renewal = u.qs(".renewal .fields", response);
								if(div_renewal) {
									box_renewal.replaceChild(div_renewal, this);
									if (message = u.qs("p.message", response)) {
										var fields = u.qs("div.fields", box_renewal);
										u.ie(fields, message);
										if (message.t_done) {
											u.t.resetTimer(t_done);
										}
										message.done = function() {
											u.ass(this, {
												"transition":"all .5s ease",
												"transform":"translate3d(0px, -10px, 0px)",
												"opacity":"0"
											});
											u.t.setTimer(this, function() {
												this.parentNode.removeChild(this);
											}, 500);
										}
										message.transitioned = function() {
											this.t_done = u.t.setTimer(this, this.done, 2400);
										}
										u.ass(message, {
											"color":"#3e8e17",
											"padding-bottom":"5px",
											"transform":"translate3d(0px, -10px, 0px)",
											"opacity":"0"
										});
										u.a.transition(message, "all 1s ease");
										u.ass(message, {
											"transform":"translate3d(0px, 0, 0px)",
											"opacity":"1"
										});
									}
									this.scene.initRenewalBox();
								}
								else {
									if(this[request_id].request_url != this[request_id].response_url) {
										location.href = this[request_id].response_url;
									}
								}
							}
							if (!this.is_requesting) {
								this.is_requesting = true;
								u.ac(this, "loading");
								u.request(this, this.action, {"data":data, "method":"POST"});
							}
						}
						form_renewal.actions["cancel"].clicked = function() {
							this.response = function(response) {
								this.is_requesting = false;
								u.rc(this, "loading");
								var div_userinfo = u.qs(".renewal .fields", response);
								box_renewal.replaceChild(div_userinfo, this._form);
								this._form.scene.initRenewalBox();
							}
							if (!this.is_requesting) {
								this.is_requesting = true;
								u.ac(this, "loading");
								u.request(this, "/profil");
							}
						}
					}
					if (!this.is_requesting) {
						this.is_requesting = true;
						u.ac(this, "loading");
						u.request(this, this.url);
					}
				}
			}
		}
		scene.initOrderList = function() {
			var orders = u.qsa("li.order_item", this);
			var i, order;
			for(i = 0; i < orders.length; i++) {
				order = orders[i];
				order.order_item_id = u.cv(order, "order_item_id");
				order.span_pickupdate = u.qs("span.pickupdate", order);
				order.span_date = u.qs("span.date", order.span_pickupdate);
				order.span_change_until = u.qs("span.change-until", order);
				order.bn_edit = u.qs("li.change a.button:not(.disabled)", order);
				if(order.bn_edit) {
					order.bn_edit.order = order;
					u.ce(order.bn_edit);
					order.bn_edit.clicked = function() {
						if(u.hc(this.order, "edit")) {
							this.form.submit();
						}
						else {
							u.ac(this.order, "edit");
							this.response = function(response) {
								this.form = u.qs("form", response);
								if(this.form) {
									this.form.order = this.order;
									this.innerHTML = "Gem";
									u.ac(this, "primary");
									u.ae(this.order.span_pickupdate, this.form);
									u.f.init(this.form);
									this.form.submitted = function() {
										this.response = function(response) {
											this.order.bn_edit.form.parentNode.removeChild(this.order.bn_edit.form);
											delete this.order.bn_edit.form;
											this.order.span_change_until.innerHTML = u.qs("span.change-until", u.ge("order_item_id:"+this.order.order_item_id, response)).innerHTML;
											this.order.span_date.innerHTML = u.qs("span.date", u.ge("order_item_id:"+this.order.order_item_id, response)).innerHTML;
											u.rc(this.order, "edit");
											this.order.bn_edit.innerHTML = "Ret";
											u.rc(this.order.bn_edit, "primary");
										}
										u.request(this, this.action, {"method":"post", "data":this.getData()});
									}
								}
							}
							u.request(this, this.url);
						}
					}
				}
			}
		}
		scene.ready();
	}
}


/*m-order_history.js*/
Util.Modules["order_history"] = new function() {
	this.init = function(scene) {
		var log_entries = u.qsa(".log_entries", scene);
		var i, log_entry;
		for(i = 0; i < log_entries.length; i++) {
			log_entry = log_entries[i];
			u.addExpandArrow(log_entry);
			u.ce(log_entry);
			log_entry.clicked = function() {
				u.tc(this, "open");
			}
		}
	}
}


/*m-forgot.js*/
Util.Modules["forgot"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			this.form = u.qs("form.request_password", this);
			u.f.init(this.form);
			this.form.scene = this;
			this.form.submitted = function() {
				var data = this.getData();
				this.response = function(response) {
					if (u.qs("form.verify_code", response)) {
						this.scene.verifyForm(response);
					}
					else {
						this.scene.showMessage(this, response);
					}
				}
				u.request(this, this.action, {"data":data, "method":"POST"});
			}
		}
		scene.verifyForm = function(response) {
			this.form_code = u.qs("form.verify_code", response);
			var p_code = u.qs("div.login p", response);
			this.form.parentNode.replaceChild(this.form_code, this.form);
			u.ie(this.form_code, p_code);
			u.f.init(this.form_code);
			this.form_code.scene = this;
			this.form_code.submitted = function() {
				data = this.getData();
				this.response = function(response) {
					if (u.qs("form.reset_password", response)) {
						this.scene.resetForm(response);
					}
					else {
						this.scene.showMessage(this, response);
					}
				}
				u.request(this, this.action, {"data":data, "method":"POST"});
			}
		}
		scene.resetForm = function(response) {
			this.form_reset = u.qs("form.reset_password", response);
			this.form_code.parentNode.replaceChild(this.form_reset, this.form_code);
			u.f.init(this.form_reset);
			this.form_reset.scene = this;
		}
		scene.showMessage = function(form, response) {
			var response_error = u.qs("p.errormessage", response);
			var scene_error = u.qs("p.errormessage", this);
			if (!scene_error) {
				u.ie(form, response_error);
			}
			else {
				form.replaceChild(response_error, scene_error);
				u.a.transition(response_error, "all 0.15s linear", animationDone);
				u.a.scale(response_error, 1.05);
				function animationDone() {
					u.a.transition(this, "all 0.15s linear");
					u.a.scale(this, 1);
				}
			}
		}
		scene.ready();
	}
}


/*m-tally.js*/
Util.Modules["tally"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			if(!u.qs(".section.tally.closed")) {
				this.tally_id = u.cv(this, "tally_id");
				this.initStartCash();
				this.initEndCash();
				this.initDeposited();
				this.initPayouts();
				this.initMiscRevenues();
				this.comment_form = u.qs("form.comment", this);
				this.comment_form.scene = this;
				u.f.init(this.comment_form);
				this.comment_form.submitted = function(iN) {
					if(iN.hasAttribute("formaction")) {
						this.action = iN.getAttribute("formaction");
					}
					this.response = function(response) {
						var error_message = u.qs(".messages .error", response);
						if(error_message) {
							if(this.p_error) {
								this.p_error.parentNode.removeChild(this.p_error);
							} 
							this.p_error = u.ie(u.qs(".section.tally"), "p", {
								html:error_message.innerHTML,
								class:"error"
							});
							u.scrollTo(window, {node: this.p_error, "offset_y": 20});
						}
						else if(u.qs(".scene.shop_shift", response)) {
							location.href = "/butiksvagt"; 
						}
						else {
							location.href = "/butiksvagt/kasse/"+this.scene.tally_id;
						}
					} 
					u.request(this, this.action, {"method":"POST", "params":u.f.getParams(this)});
				}
				this.calculated_sales_by_the_piece = u.qs(".calculated_sales span.sum", this);
				this.change = u.qs(".change span.sum", this);
			}
			// 
			page.resized();
		}
		scene.initStartCash = function() {
			this.start_cash = u.qs(".start_cash .view");
			this.start_cash.bn_edit = u.qs("li.edit", this.start_cash);
			this.start_cash.bn_edit.scene = this;
			this.start_cash.amount = u.qs("span.value", this.start_cash);
			this.start_cash.form = u.qs("form", this.start_cash);
			this.start_cash.form.scene = this;
			u.f.init(this.start_cash.form);
			this.start_cash.form.actions["edit"].clicked = function() {
				u.ac(this._form.scene.start_cash, "edit");
				this._form.inputs["start_cash"].focus();
			}
			this.start_cash.form.submitted = function() {
				this.response = function(response) {
					var new_amount = u.text(u.qs(".start_cash .view .amount span.value", response));
					this.scene.start_cash.amount.innerHTML = new_amount;
					u.rc(this.scene.start_cash, "edit");
					this.scene.updateCalculatedValues(response);
				}
				u.request(this, this.action, {"method":"post", "data":this.getData()});
			}
		}
		scene.initEndCash = function() {
			this.end_cash = u.qs(".end_cash .view");
			this.end_cash.bn_edit = u.qs("li.edit", this.end_cash);
			this.end_cash.bn_edit.scene = this;
			this.end_cash.amount = u.qs("span.value", this.end_cash);
			this.end_cash.form = u.qs("form", this.end_cash);
			this.end_cash.form.scene = this;
			u.f.init(this.end_cash.form);
			this.end_cash.form.actions["edit"].clicked = function() {
				u.ac(this._form.scene.end_cash, "edit");
				this._form.inputs["end_cash"].focus();
			}
			this.end_cash.form.submitted = function() {
				this.response = function(response) {
					var new_amount = u.text(u.qs(".end_cash .view .amount span.value", response));
					this.scene.end_cash.amount.innerHTML = new_amount;
					u.rc(this.scene.end_cash, "edit");
					this.scene.updateCalculatedValues(response);
				}
				u.request(this, this.action, {"method":"post", "data":this.getData()});
			}
		}
		scene.initDeposited = function() {
			this.deposited = u.qs(".deposited .view");
			this.deposited.bn_edit = u.qs("li.edit", this.deposited);
			this.deposited.bn_edit.scene = this;
			this.deposited.amount = u.qs("span.value", this.deposited);
			this.deposited.form = u.qs("form", this.deposited);
			this.deposited.form.scene = this;
			u.f.init(this.deposited.form);
			this.deposited.form.actions["edit"].clicked = function() {
				u.ac(this._form.scene.deposited, "edit");
				this._form.inputs["deposited"].focus();
			}
			this.deposited.form.submitted = function() {
				this.response = function(response) {
					var new_amount = u.text(u.qs(".deposited .view .amount span.value", response));
					this.scene.deposited.amount.innerHTML = new_amount;
					u.rc(this.scene.deposited, "edit");
					this.scene.updateCalculatedValues(response);
				}
				u.request(this, this.action, {"method":"post", "data":this.getData()});
			}
		}
		scene.initPayouts = function() {
			this.tally_section = u.qs(".section.tally", this);
			this.payouts = u.qs(".payouts", this);
			this.payouts.delete_forms = u.qsa("li.payout .delete", this.payouts);
			if(this.payouts.delete_forms) {
				var i, delete_form;
				for (i = 0; i < this.payouts.delete_forms.length; i++) {
					delete_form = this.payouts.delete_forms[i];
					delete_form.scene = this;
					u.m.oneButtonForm.init(delete_form);
					delete_form.confirmed = function(response) {
						this.payouts = u.qs("div.payouts", response);
						this.scene.tally_section.replaceChild(this.payouts, this.scene.payouts);
						this.scene.updateCalculatedValues(response);
						this.scene.initPayouts();
					}
				}
			}
			this.payouts.div_add = u.qs("div.add_payout", this.payouts);
			this.payouts.bn_add = u.qs("li.add_payout", this.payouts);
			this.payouts.bn_add.scene = this;
			u.ce(this.payouts.bn_add);
			this.payouts.bn_add.clicked = function() {
				this.response = function(response) {
					u.ac(this.scene.payouts.div_add, "open");
					this.scene.payouts.div_add.form = u.qs("form.add_payout", response);
					this.scene.payouts.div_add.form.scene = this.scene;
					u.ae(this.scene.payouts.div_add, this.scene.payouts.div_add.form);
					u.f.init(this.scene.payouts.div_add.form);
					this.scene.payouts.div_add.form.inputs["payout_name"].focus();
					this.scene.payouts.div_add.form.submitted = function() {
						this.response = function(response) {
							u.rc(this.scene.payouts.div_add, "open");
							this.parentNode.removeChild(this);
							this.payouts = u.qs("div.payouts", response);
							this.scene.tally_section.replaceChild(this.payouts, this.scene.payouts);
							this.scene.updateCalculatedValues(response);
							this.scene.initPayouts();
						}
						u.request(this, this.action, {"method":"post", "data":this.getData()});
					}
				}
				u.request(this, "/butiksvagt/kasse/" + this.scene.tally_id + "/udbetaling");
			}
		}
		scene.initMiscRevenues = function() {
			this.tally_section = u.qs(".section.tally", this);
			this.revenues = u.qs(".misc_revenues", this);
			this.revenues.delete_forms = u.qsa("li.revenue .delete", this.revenues);
			if(this.revenues.delete_forms) {
				var i, delete_form;
				for (i = 0; i < this.revenues.delete_forms.length; i++) {
					delete_form = this.revenues.delete_forms[i];
					delete_form.scene = this;
					u.m.oneButtonForm.init(delete_form);
					delete_form.confirmed = function(response) {
						this.revenues = u.qs("div.misc_revenues", response);
						this.scene.tally_section.replaceChild(this.revenues, this.scene.revenues);
						this.scene.updateCalculatedValues(response);
						this.scene.initMiscRevenues();
					}
				}
			}
			this.revenues.div_add = u.qs("div.add_revenue", this.revenues);
			this.revenues.bn_add = u.qs("li.add_revenue", this.revenues);
			this.revenues.bn_add.scene = this;
			u.ce(this.revenues.bn_add);
			this.revenues.bn_add.clicked = function() {
				this.response = function(response) {
					u.ac(this.scene.revenues.div_add, "open");
					this.scene.revenues.div_add.form = u.qs("form.add_revenue", response);
					this.scene.revenues.div_add.form.scene = this.scene;
					u.ae(this.scene.revenues.div_add, this.scene.revenues.div_add.form);
					u.f.init(this.scene.revenues.div_add.form);
					this.scene.revenues.div_add.form.inputs["revenue_name"].focus();
					this.scene.revenues.div_add.form.submitted = function() {
						this.response = function(response) {
							u.rc(this.scene.revenues.div_add, "open");
							this.parentNode.removeChild(this);
							this.revenues = u.qs("div.misc_revenues", response);
							this.scene.tally_section.replaceChild(this.revenues, this.scene.revenues);
							this.scene.updateCalculatedValues(response);
							this.scene.initMiscRevenues();
						}
						u.request(this, this.action, {"method":"post", "data":this.getData()});
					}
				}
				u.request(this, "/butiksvagt/kasse/" + this.scene.tally_id + "/andre-indtaegter");
			}
		}
		scene.updateCalculatedValues = function(response) {
			var calculated_sales_by_the_piece = u.qs(".calculated_sales span.sum", response);
			if(calculated_sales_by_the_piece) {
				u.pn(this.calculated_sales_by_the_piece).replaceChild(calculated_sales_by_the_piece, this.calculated_sales_by_the_piece);
				this.calculated_sales_by_the_piece = calculated_sales_by_the_piece;
			}
			var change = u.qs(".change span.sum", response);
			if(change) {
				u.pn(this.change).replaceChild(change, this.change);
				this.change = change;
			}
		}
		scene.ready();
	}
}


/*m-shop_shift.js*/
Util.Modules["shop_shift"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			var form = u.qs("form.choose_date");
			u.f.init(form);
			form.updated = function() {
				this.submit();
			}
			var confirm_delivery_listings = u.qsa(".orders.items .listing");
			var i, listing;
			for(i = 0; i < confirm_delivery_listings.length; i++) {
				listing = confirm_delivery_listings[i];
				this.initDeliveryButton(listing);
			}
		}
		scene.initDeliveryButton = function(listing) {
			listing.order_item_id = u.cv(listing, "order_item_id");
			listing.list = u.qs(".orders.items .list");
			listing.scene = this;
			listing.li = u.qs("li.confirm", listing);
			listing.li.listing = listing;
			this.status = u.qs("ul.status", this);
			listing.li.confirmed = function(response) {
				response.listing = u.ge("order_item_id:" + listing.order_item_id, response);
				response.listing.li = u.qs("li.confirm", response.listing);
				response.status = u.qs("ul.status", response);
				this.listing.list.replaceChild(response.listing, this.listing);
				this.listing.scene.status.parentNode.replaceChild(response.status, this.listing.scene.status);
				u.m.oneButtonForm.init(response.listing.li);
				this.listing.scene.initDeliveryButton(response.listing);
			}
		}
		scene.ready();
	}
}


/*m-massmail.js*/
Util.Modules["massmail"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			u.bug("scene.ready", this);
			var form = u.qs("form");
			u.f.init(form, this);
			form.scene = this;
			form.p_status = u.qs(".status", form);
			form.updated = function(iN) {
				window.onbeforeunload = function() {
					return 'Du har ændringer, der ikke er gemt!';
				}
			}
			form.submitted = function(iN) {
				if(iN.hasAttribute("formaction")) {
					form.response = function(response) {
						if(u.qs(".scene.mass_mail_receipt", response)) {
							this.p_status.innerHTML = "Test-mail blev afsendt.";
						}
						else {
							this.p_status.innerHTML = "Noget gik galt...";
						}
						this.p_status.transitioned = function() {
							u.t.setTimer(this, function() {
								u.a.transition(this, "all 1s ease-in");
								u.as(this, "opacity", "0");
							}, 1500);
						}
						u.a.transition(this.p_status, "all 0.5s ease-in");
						u.as(this.p_status, "opacity", "1");
					}
					u.request(this, iN.getAttribute("formaction"), {"method":"POST", "params":u.f.getParams(this)});
				}
				else {
					form.DOMsubmit();
				}
			}
		}
		scene.ready();
	}
}


/*u-expandarrow.js*/
u.addExpandArrow = function(node) {
	if(node.collapsearrow) {
		node.collapsearrow.parentNode.removeChild(node.collapsearrow);
		node.collapsearrow = false;
	}
	node.expandarrow = u.svg({
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
}
u.addCollapseArrow = function(node) {
	if(node.expandarrow) {
		node.expandarrow.parentNode.removeChild(node.expandarrow);
		node.expandarrow = false;
	}
	node.collapsearrow = u.svg({
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
}
u.addPreviousArrow = function(node) {
	node.arrow = u.svg({
		"name":"prevearrow",
		"node":node,
		"class":"arrow",
		"width":17,
		"height":17,
		"shapes":[
			{
				"type": "line",
				"x1": 9,
				"y1": 2,
				"x2": 2,
				"y2": 7
			},
			{
				"type": "line",
				"x1": 2,
				"y1": 6,
				"x2": 9,
				"y2": 11
			}
		]
	});
}
u.addNextArrow = function(node) {
	node.arrow = u.svg({
		"name":"nextearrow",
		"node":node,
		"class":"arrow",
		"width":17,
		"height":17,
		"shapes":[
			{
				"type": "line",
				"x1": 2,
				"y1": 2,
				"x2": 9,
				"y2": 7
			},
			{
				"type": "line",
				"x1": 9,
				"y1": 6,
				"x2": 2,
				"y2": 11
			}
		]
	});
}


/*m-purchasing.js*/
Util.Modules["purchasing"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			var form = u.qs("form.choose_date");
			u.f.init(form);
			form.updated = function() {
				this.submit();
			}
			this.products = u.qsa("div.products li.listing", this);
			var i, product, image;
			for(i = 0; i < this.products.length; i++) {
				product = this.products[i];
				image = u.qs("span.image", product);
				image._id = u.cv(image, "item_id");
				image._format = u.cv(image, "format");
				image._variant = u.cv(image, "variant");
				if(image._id && image._format && image._variant) {
					u.ass(image, {
						backgroundImage: "url(/images/" + image._id + "/" + (image._variant ? image._variant+"/" : "") + "50x50." + image._format+")"
					});
				}
			}
		}
		scene.ready();
	}
}
Util.Modules["add_edit_product"] = new function() {
	this.init = function(scene) {
		scene.resized = function() {
		}
		scene.scrolled = function() {
		}
		scene.ready = function() {
			var form = u.qs("form");
			u.f.init(form, this);
			form.scene = this;
			next_wednesday = " - ";
			this.first_pickupdate_span = u.qs(".first_pickupdate span", this);
			form.inputs["start_availability_date"].changed = function(iN) {
				var first_pickupdate = "-";
				if(iN.value) {
					var next_wednesday_date = this.form.scene.getNextDayOfTheWeek("Wednesday", false, new Date(iN.value));
					first_pickupdate = next_wednesday_date.toLocaleDateString('da-DA', {year:"numeric", month:"2-digit", day:"2-digit"});
				}
				this.form.scene.first_pickupdate_span.innerHTML = first_pickupdate;
			}
			this.last_pickupdate_span = u.qs(".last_pickupdate span", this);
			form.inputs["end_availability_date"].changed = function(iN) {
				var last_pickupdate = "-";
				if(iN.value) {
					var previous_wednesday_date = this.form.scene.getPreviousDayOfTheWeek("Wednesday", false, new Date(iN.value));
					last_pickupdate = previous_wednesday_date.toLocaleDateString('da-DA', {year:"numeric", month:"2-digit", day:"2-digit"});
				}
				this.form.scene.last_pickupdate_span.innerHTML = last_pickupdate;
			}
		}
		scene.getNextDayOfTheWeek = function(dayName, excludeToday = true, refDate = new Date()) {
			var dayOfWeek = ["sun","mon","tue","wed","thu","fri","sat"]
							  .indexOf(dayName.slice(0,3).toLowerCase());
			if (dayOfWeek < 0) return;
			refDate.setHours(0,0,0,0);
			refDate.setDate(refDate.getDate() + (!!excludeToday ? 1 : 0) + 
							(dayOfWeek + 7 - refDate.getDay() - (!!excludeToday ? 1 : 0)) % 7);
			return refDate;
		}
		scene.getPreviousDayOfTheWeek = function(dayName, excludeToday = true, refDate = new Date()) {
			var dayOfWeek = ["sun","mon","tue","wed","thu","fri","sat"]
							  .indexOf(dayName.slice(0,3).toLowerCase());
			if (dayOfWeek < 0) return;
			refDate.setHours(0,0,0,0);
			refDate.setDate(refDate.getDate() + (!!excludeToday ? 1 : 0) +
							(dayOfWeek - 7 - refDate.getDay() - (!!excludeToday ? 1 : 0)) % 7);
			return refDate;
		}
		scene.ready();
	}
}

