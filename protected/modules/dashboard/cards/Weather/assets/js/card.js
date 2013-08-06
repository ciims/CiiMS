var FcAlexkTPM = {

	id : null,

	uuid : null,

	target : null,

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

	apiKey : null,

	metric : false,

	geoIp : {
		"country_code" : null,
		"country_name" : null,
		"city" : null,
		"region" : null,
		"region_name" : null,
		"latitude" : null,
		"longitude" : null,
		"postal_code" : null,
		"area_code" : null,
		"metro_code" : null
	},

	load : function(id) {
		FcAlexkTPM.id = $(id).attr("id");
		FcAlexkTPM.uuid = "FcAlexkTPM-" + this.id;
		FcAlexkTPM.target = $("#FcAlexkTPM[data-attr-id='" + FcAlexkTPM.id + "']");
		FcAlexkTPM.apiKey = $("." + this.id + "-modal").find("input#Weather_apikey").val();
		FcAlexkTPM.metric = $("." + this.id + "-modal").find("input#Weather_metric").is(":checked");

		var canUseCard = true;

		if (!FcAlexkTPM.getGeoIp())
			canUseCard = false;

		if (FcAlexkTPM.apiKey == null || FcAlexkTPM.apiKey == undefined || FcAlexkTPM.apiKey == '' )
			canUseCard = false;

		if (canUseCard)
			FcAlexkTPM.getLocation();
		else
			FcAlexkTPM.showWarnings();
	},

	getGeoIp : function() {
		var key = FcAlexkTPM.uuid + "-geoip",
			response = JSON.parse(localStorage.getItem(key));

		if (response == undefined || response == null || response == '')
		{
			$.ajax({
				url :'https://j.maxmind.com/js/geoip.js', 
				async : false,
				dataType : 'script',
				success : function(script, textStatus) {

					console.log(textStatus);
					if (textStatus != "success")
						return false;

					var object = {
						"country_code" : geoip_country_code(),
						"country_name" : geoip_country_name(),
						"city" : geoip_city(),
						"region" : geoip_region(),
						"region_name" : geoip_region_name(),
						"latitude" : geoip_latitude(),
						"longitude" : geoip_longitude(),
						"postal_code" : geoip_postal_code(),
						"area_code" : geoip_area_code(),
						"metro_code" : geoip_metro_code()
					};

					response = object;
					localStorage.setItem(key, JSON.stringify(object));
				}
			});
			
		}

		FcAlexkTPM.geoIp = response;

		return true;
	},

	getLocation : function() {
		if (navigator.geolocation)
	    	navigator.geolocation.getCurrentPosition(FcAlexkTPM.showPosition);
	},

	getLocationString : function() {
		FcAlexkTPM.getGeoIp();

		if (FcAlexkTPM.geoIp.country_code == "US")
			return FcAlexkTPM.geoIp.city + ", " + FcAlexkTPM.geoIp.region;
		else
			return FcAlexkTPM.geoIp.city + "," + FcAlexkTPM.geoIp.region_name;
	},

	showPosition : function(position) {

		if (localStorage)
		{
			var key = FcAlexkTPM.uuid + "-last";
			var response = localStorage.getItem(key);
			var now = new Date().getTime().toString();

			if (response == undefined || response == null || response == '' || (response.timestamp + 900000) <= now)
			{
				// Syncronous request to laod data from Forecast.io
				$.ajax({
					type: 'POST',
					url : CiiDashboard.endPoint + "/card/callmethod/id/" + FcAlexkTPM.id + "/method/getCurrentConditions", 
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

			$(FcAlexkTPM.target).find(".location").html(FcAlexkTPM.getLocationString());
			$(FcAlexkTPM.target).find(".temperature .degrees").html(response.value.currently.temperature);

			if (FcAlexkTPM.metric)
				FcAlexkTPM.toCentigrade(response.value.currently.temperature);

			console.log(response.value);
		}
		else
			console.log("Local Storage is not supported on this device. This card requies localStorage to function");
	},

	toCentigrade : function(farenheit) {
		var celcius = Math.round((farenheit -32) * 5 / 9);
		$(FcAlexkTPM.target).find(".farenheit").removeClass("farenheit").addClass("celcius");
		$(FcAlexkTPM.target).find(".temperature .degrees").html(celcius);
	},

	showWarnings : function() {
		console.log("There's an issue with your setup");
	}
};