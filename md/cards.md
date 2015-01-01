# Dashboard Cards

Since CiiMS 1.0, dashboard cards have provided dashboard users with deeper insight into their site and the world around them. With CiiMS 2.0, dashboard cards have received yet another major upgrade, making them more useful and easier to develop with.

In 2.0, dashboard cards are asynchronous, client side Javascript widgets that can be installed from the main dashboard view (/dashboard). Like their predecessors cards can be resized, reskined, and can store their own settings in CiiMS' database. These features have been built upon to provide a new and better card management system, as well as several new API's to build, developing, and deploying cards.

<img src="/images/cards/003.PNG" class="img" />
## Examples

For an example of a simple card, check out the [basic-card](https://github.com/charlesportwoodii/ciims-basic-card) repository on Github.

## Installing Cards

Dashboard cards can be installed directly through the CiiMS dashboard. When you first access the dashboard, a pulsing "plus" button will appear in the upper left hand corner of the window.
<img src="/images/cards/001.PNG" class="img" />

Clicking on this button will open the card selection menu, which will list all available cards that can currently be installed to CiiMS. From here you can select any available card to install by clicking the "Install" button in the upper right hand corner of the window pane.

<img src="/images/cards/002.png" class="img" />

## Resizing Cards

Some cards may offer resizing options, allowing you to resize them to either show more or less information. If a card supports resizing, a "resize" button will appear at the bottom left hand side of the card. Once clicked upon the card will resize itself to the next available card size. The example below shows the 4 possible sizes a card can be adjusted to.

<img src="/images/cards/003.PNG" class="img" />

## Editings Card Settings

Like their predecessors in 1.9, cards support dynamic settings which an be updated and stored in the database, allowing them to remember specific settings, such as API keys, tokens, usernames, and other information. To access the settings for a particular card, simply click the "gears" button in the lower right of any card to pull up the card settings pane.

<img src="/images/cards/004.PNG" class="img" />

From here you can alter any settings for the card, update the card, and even uninstall the card from the dashboard.

# Developing Cards

Developing cards in CiiMS 2.0 is now significantly easier thanks to a dedicated API for cards. The next section will cover the basics of a card, how you can install a test card to your CiiMS instance, and how to go about publishing your card.

## Card Anatomy

Dashboard cards are made up of 6 basic components, but can be extended upon to provide greater flexability. These components are as follows

### Card.json

Each card should come with file called ```card.json``` which describes the basic details of a card. This file contains the version information, the name, the description,. the image, any and all properties that should be made available to the card, and the available resizes for the card. All of these fields are required.

```
{
    "name": "BasicCard",
    "description": "This is a basic card that illustrates the capabilities of dashboard cards.",
    "image": "card.png",
    "version": "0.0.1",
    "footerText": "basic-card",
    "properties": {
        "setting1": {
            "name": "Awesome Setting #1",
            "type": "text",
            "value": null
        },
        "setting2": {
            "name": "Awesome Setting #2",
            "type": "email",
            "value": null
        }
    },
    "availableTileSizes": [
        "square",
        "rectangle",
        "rectangleTall",
        "squareBig"
    ]
}
```

__Card Name__

The most important part of this file is the ```name``` field, as this field will be used in the Javascript file used to bootstrap your card.

__Properties__

The next important part of this file is the properties section, which defines all available settings for your card. Each property should have a unique string for the key, and should ahve the following attributes:

```
{
    "name": "Awesome Setting #1",
    "type": "text",
    "value": null
}
```

The setting name can be any descriptive name that you want, the type can be any valid HTML5 input type, and the value can either be a default value you want to provide, or NULL.

__Resize Options__

Each card should define what resize options it supports. There are 4 available resize options that CiiMS supports:

```
"availableTileSizes": [
    "square",
    "rectangle",
    "rectangleTall",
    "squareBig"
]
```

At minimum you should define a single resize option. The recommended default size is "square".

__Image__

To make your card more identifiable, you should supply an 550x350 px image of your card. the relative path to the image can be defined using this property. The recommended file name is ```card.png```.

## Card.js

Each card should also come with a specific ```card.js``` file which will serve as the core file which the card will be displayed from. At minimum, the card should look as follows:

```
;(function() {
	var BasicCard = new CardPrototype({
		name: "BasicCard",
		init: function() {},
		reload: function() {},
		render: function() {}
	});
}(this));
```
Your asynchronous class should extend the ```CardPrototype``` class with the property ```name``` which matches the name of the card as defined in the ```card.json``` file, and three methods that you can override ```init```, ```reload```, and ```render```. These methods will fire at the expected time (when the card is initialized, when the card is rendered, and whenever the reload event is called, eg card installation, card re-shuffling/re-ordering, and so forth).

### Accessing Card Properties

@ TODO: Remember how to retrieve card data from the CardPrototype extended object. The _cards_ know how to fetch their own data, but I don't remember the API methods to pull this data from the prototype object.

All cards are stored in a global object called ```cards```, so if you know the id the data can be pulled from ```cards[id]``` trivialy.

## Card.css

Cards can have their own, customizable CSS files. The root of each card during the rendering phrase will be assigned the following class which you can explicitly target

```
.cardname.toLowerCase()-version.replace('.', '/')
```

For a card named BasicCard with a version of 0.0.1, this card will look as follows:

```
.basiccard-0-0-1
```

> __NOTE:__ Some propeties may require using ```!important``` to override their classes.

## Card.html

Cards should also include a file called ```card.html```, which will provide any custom markup that you may need. This makup can be whatever you want.


## Installing Development Cards

Once you have started development of your card, you'll want to test it with your CiiMS instance. To test your card in your instance, first make your source code available at a URL endpoint available to your CiiMS instance, then open up the browser console and run the following console command:

```
Dashboard.installCard(url);
```

The URL provided should point to the path where ```card.json``` is located at. Eg if your ```card.json``` file is available at ```//cards.example.com/mycard/card.json```, ```//cards.example.com/mycard``` is the URL you'll want to use.

> __NOTE:__ As Javascript widgets, cards have some access to critical data on your instance such as the version information for CiiMS, authorization data, and all HTML on your page. As of such you should only install card from trusted sources over HTTPS. __NEVER__ install a card from an untrusted source.

## Publishing

If you want your card to be made available for any CiiMS user to install it from the dashboard, you'll need to submit a pull request to the [https://github.com/ciims/cards](https://github.com/ciims/cards) repository. Instructions for how to publish and update cards are available there.

## Alternative Publishing

Alternatively, you can setup your own authoritative publishing source by cloning the https://github.com/ciims/cards repository and adding the cards that you so desire. With your CiiMS' ```protected/config/params.php``` file you can override the ```cards``` property of this array to point to your authoritative source. More information can be found in the https://github.com/ciims/cards repository.