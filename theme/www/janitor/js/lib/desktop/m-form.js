// generic 
Util.Modules["restructure"] = new function() {
	this.init = function(div) {

		var div_result = u.qs("div.result");
		var result_code = u.qs("code", div_result);

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
			// console.log(response);
			
		}
		// console.log(li_restructure);

		li_restructure.progress = function(response) {
			u.ie(div_result, result_code.cloneNode(true), {html:response.innerHTML});
			// u.bug("I made it somewhere", response);
		}
	}
}


// generic 
Util.Modules["generic"] = new function() {
	this.init = function(div) {



	}
}

