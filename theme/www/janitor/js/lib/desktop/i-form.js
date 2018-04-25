// generic 
Util.Objects["restructure"] = new function() {
	this.init = function(div) {

		var result_code = u.qs("div.result code");
		var li_restructure = u.qs("li.restrucure");
		li_restructure.result_code = result_code;

		li_restructure.confirmedError = function(response) {

			this.result_code.innerHTML = response;
			console.log(response);
			
		}
		console.log(li_restructure);
	}
}


// generic 
Util.Objects["generic"] = new function() {
	this.init = function(div) {



	}
}

