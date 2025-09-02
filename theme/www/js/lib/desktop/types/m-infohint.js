Util.Modules["infohint"] = new function() {
	this.init = function(span) {


		u.ac(span.parentNode, "infohintparent");
		span.q = u.ae(span.parentNode, "span", {"class":"q", "html":"i"});


		span.parentNode.insertBefore(span.q, span);


	}
}
