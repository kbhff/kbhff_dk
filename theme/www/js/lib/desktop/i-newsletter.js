Util.Objects["newsletter"] = new function() {
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
			
			// this.response = function(response) {
			//
			// 	console.log(response);
			//
			// 	var h2 = u.qs("h2", response);
			//
			// 	if(h2 && h2.innerHTML == "Næsten færdig...") {
			//
			// 		console.log("SUCCESS");
			// 	}
			// 	else {
			// 		console.log("ERROR");
			// 	}
			//
			// }
			//
			// u.request(this, this.action, {method: this.method, data: this.getData(), headers:{"accept":"text/html"}});
		}

	}
}