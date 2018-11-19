u.f.fixFieldHTML = function(field) {
	if(field._indicator) {
		u.ae(u.qs("label", field), field._indicator);
	}
}

u.f.customHintPosition = {};
u.f.customHintPosition["string"] = function() {}
u.f.customHintPosition["email"] = function() {}
u.f.customHintPosition["number"] = function() {}
u.f.customHintPosition["password"] = function() {}
u.f.customHintPosition["tel"] = function() {}
u.f.customHintPosition["text"] = function() {}
u.f.customHintPosition["select"] = function() {}
u.f.customHintPosition["checkbox"] = function() {}
u.f.customHintPosition["radiobuttons"] = function() {}