## EAnalytics Javascript Tracking

EAnalytics is a [Yii Framework](www.yiiframework.com) wrapper for [Analytics.js Library](https://github.com/segment.io/analytics.js). Rather than loading in a dozen different extensions for each analytics service you want to use, this extension allows you to specify various tracking providers in a single config file and call various methods via a single interface.


## Configuring

Clone the repository to your extensions folder and add the following to your config/main.php:

    'preload' => array('EAnalytics'),
	'components' => array(
		'class' => 'ext.analytics.EAnalytics',
		'lowerBounceRate' => false,
		'providers' => array(
			// List of Providers
		)
	)

The extension must be preloaded if you want analytics.js to load up on page load

#### lowerBounceRate

Some analytics services treat any user who only visits a single page as a "bounce". This setting fires off 3 events 15s, 30s, and 60s after the initial page to load which can help determine if a user only needed to visit your site for a single page or actually bounced.

Setting this value to "true" will eanble this feature. By default it is disabled.

#### providers

This is an array of all the providers that you wish to enable. This array is transformed into JSON and injected into __analytics.initialize()__ on page load.

You may view the list of available providers [at segment.io's integration page](https://segment.io/docs/integrations)

## LICENSE

(The MIT License)

Copyright (c) 2013 Charles R. Portwood II <charlesportwoodii@ethreal.net>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the 'Software'), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
