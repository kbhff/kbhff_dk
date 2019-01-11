Util.Objects["comments"] = new function() {
	this.init = function(div) {
//		u.bug("comment init:", div);


		div.item_id = u.cv(div, "item_id");

		div.list = u.qs("ul.comments", div);
		div.comments = u.qsa("li.comment", div.list);

		div.header = u.qs("h2", div);
		div.header.div = div;
		u.addExpandArrow(div.header);
		// Make header clickable and add click event 
		u.ce(div.header);
		div.header.clicked = function() {
			// if header is open, close it, add expand arrow and save cookie.
			if(u.hc(this.div, "open")) {

				u.rc(this.div, "open");
				u.addExpandArrow(this);
				u.saveCookie("comments_open_state", 0, {"path":"/"});
			}
			else {
			// if header is not open, open it, add collapse arrow and save cookie.
				u.ac(this.div, "open");
				u.addCollapseArrow(this);
				u.saveCookie("comments_open_state", 1, {"path":"/"});
			}
		}
		// get cookies and initialize click event if cookie == 1
		// that is if header is open, has added collapse arrow and saved cookie 
		div.comments_open_state = u.getCookie("comments_open_state", {"path":"/"});
		if(div.comments_open_state == 1) {
			div.header.clicked();
		}


		// comment initialization (still not doing anything)
		div.initComment = function(node) {

			node.div = this;

		}


		// CMS interaction urls
		div.csrf_token = div.getAttribute("data-csrf-token");
		div.add_comment_url = div.getAttribute("data-comment-add");

		// if interaction data available
		if(div.add_comment_url && div.csrf_token) {

			// add initial add comment button
			div.actions = u.ae(div, "ul", {"class":"actions"});
			div.bn_comment = u.ae(u.ae(div.actions, "li", {"class":"add"}), "a", {"html":u.txt["add_comment"], "class":"button primary comment"});
			div.bn_comment.div = div;

			u.ce(div.bn_comment);
			div.bn_comment.clicked = function() {

				var actions, bn_add, bn_cancel;

				// hide original add button
				u.as(this.div.actions, "display", "none");

				// add comment form specific to interaction data
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

				// handle form submit
			
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

							// remove add comment form
							this.parentNode.removeChild(this);

							// show original add button
							u.as(this.div.actions, "display", "");
						}
					}
					u.request(this, this.action, {"method":"post", "params":u.f.getParams(this)});

				}

				// handle cancel
				u.ce(bn_cancel);
				bn_cancel.clicked = function(event) {
					u.e.kill(event);
					this.div.form.parentNode.removeChild(this.div.form);

					// show original add button
					u.as(this.div.actions, "display", "");
				}
			}
		}
		else {
			u.ae(div, "p", {"html": (u.txt["login_to_comment"] ? u.txt["login_to_comment"] : "Login or signup to comment")});
		}


		// initalize existing comments
		var i, node;
		for(i = 0; node = div.comments[i]; i++) {
			div.initComment(node);
		}

	}
}
