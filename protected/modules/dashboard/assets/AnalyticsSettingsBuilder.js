// This js file automatically generates AnalyticsSettings.php
// Load up a Console that supports Analytics.js, and run this script
// Then copy the output to AnalyticsSettings.php
// Then strip out all "undefined variables" via string replace
// This should get you 99% of the way there.

// __ => " "
// ___ => "."
console.log("class AnalyticsSettings extends CiiSettingsModel");
console.log("{");

	// Write the form type out
	console.log("	public $form = 'application.modules.dashboard.views.analytics.form';");

	// Write each of the property values out
	$.each(analytics._providers, function(key, value) {
	    var k = key.replace(" ", "__").replace(" ", "__").replace(".", "___");
	    console.log("	protected $analyticsjs_" + k + "_enabled = false;");
	    $.each(value.prototype.defaults, function(value) {
	        console.log("	protected $analyticsjs_" + k + "_" + value + " = NULL;")
	    });
	    console.log("\n");
	});

	// Write the groups out
	console.log("	public function groups()");
	console.log("	{");
	console.log("		return array(");

		$.each(analytics._providers, function(key, value) {
		    var k = key.replace(" ", "__").replace(" ", "__").replace(".", "___");
		    var vars = "'analyticsjs_" + k + "_enabled', ";
		    $.each(value.prototype.defaults, function(value) {
		        vars += "'analyticsjs_" + k + "_" + value + "', ";
		    });
		    console.log("			'" + key + "' => array(" + vars.substr(0, vars.length - 2) + "),");
		});

	console.log("		);");
	console.log("	}");

	// Write the rules out
	console.log("	public function rules()");
	console.log("	{");
	console.log("		return array(");
	
		// Strings, booleans, and urls
		var strings, booleans = "";

		$.each(analytics._providers, function(key, value) {
		    var k = key.replace(" ", "__").replace(" ", "__").replace(".", "___");

		    // Add boolean values together
		    booleans += "analyticsjs_" + k + "_enabled, ";

		    $.each(value.prototype.defaults, function(value) {
		    	strings += "analyticsjs_" + k + "_" + value + ", ";
		    });
		});

		console.log("		array('" + booleans.substr(0, booleans.length - 2) + "', 'boolean'),");
	    console.log("		array('" + strings.substr(0, strings.length - 2) + "', 'length', 'max' => 255),");

	console.log("		);");
	console.log("	}");

	// Write the attributeLabels out
	console.log("	public function attributeLabels()");
	console.log("	{");
	console.log("		return array(");
	
		$.each(analytics._providers, function(key, value) {
		    var k = key.replace(" ", "__").replace(" ", "__").replace(".", "___");
		    console.log("			'analyticsjs_" + k + "_enabled' => '" + " Enabled" + "',");
		    $.each(value.prototype.defaults, function(value) {
		        console.log("			'analyticsjs_" + k + "_" + value + "' => '" + value + "',")
		    });
		});

	console.log("		);");
	console.log("	}");

console.log("}");