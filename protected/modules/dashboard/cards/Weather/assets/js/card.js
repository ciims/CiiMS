var FcAlexkTPM = {

	// The string id of the card. This must be set by the loader
	id : null,

	// The card uuid. This is used to uniquely identify this particular piece of javascript
	uuid : null,

	// The target DOM node we want to manipulate
	target : null,

	// Forecast.io => climacons icon conversion
	icons : {
		"clear-day" : "sun", 
		"clear-night" : "moon", 
		"rain" : "rain", 
		"snow" : "snow",
		"sleet" : "sleet", 
		"wind" : "wind", 
		"fog" : "fog", 
		"cloudy" : "cloud", 
		"partly-cloudy-day" : "cloud sun", 
		"partly-cloudy-night" : "cloud moon",
		"hail" : "hail", 
		"lightning" : "lightning", 
		"tornado" : "tornado"
	},

	// Forecast.io APIKey
	apiKey : null,

	// Whether or not to use farenheit or centigrade
	metric : false,

	// Cache data loaded from GeoIP
	geoIp : null,

	position : null,

	/**
	 * Load function is the bootstrap method all cards inherit.
	 * 
	 * @param  string id  The unique ID of the card we want this card to manipulate
	 */
	load : function(id) {
		// Set some variables
		this.id = id;
		this.uuid = "FcAlexkTPM-" + this.id;
		this.target = "#FcAlexkTPM[data-attr-id='" + this.id + "']";
		this.apiKey = $("." + this.id + "-modal").find("input#Weather_apikey").val();
		this.metric = $("." + this.id + "-modal").find("input#Weather_metric").is(":checked");

		var canUseCard = true;

		if (!this.getGeoIp())
			canUseCard = false;

		if (this.apiKey == null || this.apiKey == undefined || this.apiKey == '' )
			canUseCard = false;

		if (canUseCard)
			this.getLocation();
		else
			this.showWarnings();
	},

	getGeoIp : function() {
		var key = this.uuid + "-geoip",
			response = JSON.parse(localStorage.getItem(key));

		if (response == undefined || response == null || response == '')
		{
			$.ajax({
				url :'https://j.maxmind.com/js/apis/geoip2/v2.0/geoip2.js', 
				async : false,
				dataType : 'script',
				success : function(script, textStatus) {

					if (textStatus != "success")
						return false;

					geoip2.city(function(data) {
						var object = data;

						response = object;
						localStorage.setItem(key, JSON.stringify(object));
					});					
				}
			});
			
		}

		this.geoIp = response;

		return true;
	},

	getLocation : function() {
		var self = this;
		if (navigator.geolocation)
	    	navigator.geolocation.getCurrentPosition(function(position) { self.position = position; self.showPosition(position, self); });

	},

	getLocationString : function() {
		this.getGeoIp();

		if (this.geoIp.country.iso_code == "US")
			return this.geoIp.city.names.en + ", " + this.geoIp.subdivisions[0].iso_code;
		else
			return this.geoIp.citynames.en + "," + this.geoIp.subdivisions[0].names.en;
	},

	showPosition : function(position, self) {
		
		if (localStorage)
		{
			var key = self.uuid + "-last";
			var response = localStorage.getItem(key);
			var now = new Date().getTime().toString();

			if (response == undefined || response == null || response == '' || (response.timestamp + 900000) <= now)
			{
				// Syncronous request to laod data from Forecast.io
				$.ajax({
					type: 'POST',
					url : CiiDashboard.endPoint + "/card/callmethod/id/" + self.id + "/method/getCurrentConditions", 
					data : { "latitude" : position.coords.latitude, "longitude" : position.coords.longitude },
					async : false,
					success : function(data) {
						var object = JSON.stringify( { "value" : data, "timestamp": new Date().getTime() } );
						localStorage.setItem(key, object);
						response = JSON.parse(data);
						response.value = JSON.parse(response.value);
					}
				});				
			}
			else
			{
				response = JSON.parse(response);
				response.value = JSON.parse(response.value);
			}

			$(self.target).find(".location").html(self.getLocationString());
			$(self.target).find(".temperature .degrees").html(response.value.currently.temperature);

			if (self.metric)
				self.toCentigrade(response.value.currently.temperature);

			///console.log(response.value);
		}
		else
			console.log("Local Storage is not supported on this device. This card requies localStorage to function");
	},

	toCentigrade : function(farenheit) {
		var celcius = Math.round((farenheit -32) * 5 / 9);
		$(this.target).find(".farenheit").removeClass("farenheit").addClass("celcius");
		$(this.target).find(".temperature .degrees").html(celcius);
	},

	showWarnings : function() {
		console.log("There's an issue with your setup");
	}
};