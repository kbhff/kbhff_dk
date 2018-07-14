Util.Objects["accept_terms"] = new function() {
	this.init = function(scene) {

		scene.resized = function() {
//			u.bug("scene.resized:" + u.nodeId(this));
		}

		scene.scrolled = function() {
//			u.bug("scene.scrolled:" + u.nodeId(this))
		}

		scene.ready = function() {
			u.bug("scene.ready:" + u.nodeId(this));

			var form_accept = u.qs("form.accept", this);
			u.f.init(form_accept);

			form_accept.actions["reject"].clicked = function() {
				alert("hej")
				// var overlay = u.overlay({title:"warning", height:200,width:200});
				// u.ae(overlay.div_content, "p",{html:"hej"});
			}
		}

		// scene is ready
		scene.ready();
	}
}
