Util.Objects["departments"] = new function() {
	this.init = function(scene) {
		// u.bug("scene init:", scene);


		scene.resized = function() {
			// u.bug("scene.resized:", this);
		}

		scene.scrolled = function() {
			// u.bug("scene.scrolled:", this);
		}

		scene.ready = function() {
			// u.bug("scene.ready:", this);

			page.cN.scene = this;

			this.map = u.ae(this, "div", {class:"map"});
			this.map.scene = this;
			this.insertBefore(this.map, u.qs("div.departmentlist", this));


			this.map.APIloaded = function() {
				// Invoked when the googlemaps API from google is loaded
			}
			this.map.loaded = function() {
				// Invoked once the map has been created

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


		// scene is ready
		scene.ready();

	}

}
