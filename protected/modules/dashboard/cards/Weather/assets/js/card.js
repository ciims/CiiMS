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

	// Card position data
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

		this.setLocationString(this);

		if (this.apiKey == null || this.apiKey == undefined || this.apiKey == '' )
			canUseCard = false;

		if (canUseCard)
			this.getLocation();
		else
			this.showWarnings();
	},

	/**
	 * Attempts to retrieve the geoIP information from the user
	 * 
	 * @return boolean   If we can use GeoIP. This is used by canUseCard outlined above.
	 */
	getGeoIp : function(callback) {
		var key = this.uuid + "-geoip",
			response = JSON.parse(localStorage.getItem(key)),
			self = this;

		if (response == undefined || response == null || response == '')
		{
			// This request MUST be syncronous, otherwise if we hit an error the card breaks.
			$.ajax({
				url :'https://j.maxmind.com/js/apis/geoip2/v2.0/geoip2.js', 
				dataType : 'script',
				success : function(script, textStatus) {

					if (textStatus != "success")
						return false;

					geoip2.city(function(data) {
						self.geoIp = response = data;
						localStorage.setItem(key, JSON.stringify(data));
						
						if (callback != undefined)
							callback(self.geoIp);
					});					
				}
			});			

			return true;
		}
		
		self.geoIp = response;

		if (callback != undefined)
			callback(self.geoIp);

		return true;
	},

	/**
	 * Retrieves the location using navigator.geoLocation, which should be more accurate than lat/long
	 */
	getLocation : function() {
		var self = this;
		if (navigator.geolocation)
	    	navigator.geolocation.getCurrentPosition(function(position) { self.position = position; self.showPosition(position, self); });
	    else
	    	this.showWarnings();

	},

	/**
	 * Set the location for the current objhect
	 * @param this  self   The current object
	 */
	setLocationString : function(self, id, target) {
		var response = null;

		self.getGeoIp(function(data) {
			if (data.country.iso_code == "US")
				response = data.city.names.en + ", " + data.subdivisions[0].iso_code;
			else
				response = data.citynames.en + "," + data.subdivisions[0].names.en;

			$(target).find(".location").text(response);
		});
	},

	/**
	 * Uses the current position to update the DOM
	 * @param  geoLocation position      geoLocation.position 
	 * @param  FcAlexkTPM  self          The containing class so that we can do callbacks correctly
	 */
	showPosition : function(position, self) {

		if (localStorage)
		{
			var key = self.uuid + "-last",
				response = localStorage.getItem(key),
				now = new Date().getTime().toString();

			// If we don't have any previous data from Forecast.io, or the previous data is older than 15 minutes, make another request
			var tmpResponse = JSON.parse(response);
			if (response == undefined || response == null || response == '' || (tmpResponse.timestamp + 900000) <= now)
			{
				// Syncronous request to laod data from Forecast.io
				$.ajax({
					type: 'POST',
					// See Weather.php (Weather::getCurrentConditions()) for how this callback works. Forecast.io has Allow-Access-Origin disabled
					url : CiiDashboard.endPoint + "/card/callmethod/id/" + self.id + "/method/getCurrentConditions", 
					data : { "latitude" : position.coords.latitude, "longitude" : position.coords.longitude },
					async : false, 
					success : function(data) {

						if (data.replace(/\s/g, "") == "Forbidden")
						{
							response = false;
							self.showWarnings();
							return false;
						}

						// Cache the data in localstorage for 15 minutes
						var object = JSON.stringify( { "value" : data, "timestamp": new Date().getTime() } );
						localStorage.setItem(key, object);
						response = JSON.parse(object),
						response.value = JSON.parse(response.value);
						self.displayWeather(self, self.id, self.target, response);
					}
				});				
			}
			else
			{
				response = JSON.parse(response);
				response.value = JSON.parse(response.value);
				self.displayWeather(self, self.id, self.target, response);
			}
		}
		else
			console.log("Local Storage is not supported on this device. This card requies localStorage to function");
	},

	/**
	 * Displays the weather for this card
	 * @param  this     self      The current object
	 * @param  JSON     response  The response data
	 */
	displayWeather : function(self, id, target, response) {
		self.setLocationString(self, id, target);

		if (response == false)
			return false;

		response.value.currently.temperature = Math.round(response.value.currently.temperature);

		// Update the card
		$(target).find(".temperature .degrees").html(response.value.currently.temperature);

		// Show stuff in centigrade if the person is cool.
		if (self.metric)
			self.toCentigrade(response.value.currently.temperature);

		var icon = response.value.currently.icon,
			icons = self.icons,
			display = icons[icon];

		// Display the appropriate icons
		$(target).find(".card-body .weather").addClass(display);
		$(target).find(".card-body .details").text(response.value.currently.summary);



	},

	/**
	 * Displays content in centigrade instead of farenheit
	 * @param  float farenheit    The temperate in farenheit
	 */
	toCentigrade : function(farenheit) {
		// Remember that one function that you haven't used since your first week of CS 101?
		// 
		var celcius = Math.round((farenheit -32) * 5 / 9);

		$(this.target).find(".farenheit").removeClass("farenheit").addClass("celcius");
		$(this.target).find(".temperature .degrees").html(celcius);
	},

	/**
	 * Show some pretty warnings... or somethign more productive
	 */
	showWarnings : function() {
		console.log("There's an issue with your setup");
	}
};