# CiiMS API
The CiiMS API module provides basic access to common methods and data. The CiiMS API is a JSON REST API which supports GET, POST, and DELETE. POSTS requests should be sent as JSON encoded form fields for simplicity.

### API Objectives
The API has been designed with serveral components in mind:

- Performance
- Security
- Simplicity

### Accessing the API
The CiiMS API can be accessed via ```/api``` of your CiiMS instance.

### Appropriate Request Headers
When making a request to the API you have 2 options for interaction, you can either send raw JSON via ```application/json``` as a raw request __OR__ you can send ```application/x-www-form-urlencoded``` form data and serialize your parameters as you would in jQuery. If any raw request body is recieved the API will assume that the data you sent is ```application/json``` and will interpret the data as that.

### Responses
All responses from the API will be returned as JSON objects and will at minimum contain the HTTP response code sent with the headers, a error message if applicable, and an object called "response" which will contain the response. If an occur occurs, (depending on the resource), the response will be an empty JSON object or NULL.

	{ "status" : <http_status_code>, "message" : null, "response" : { } }

### A Note About Timestamps
Timestamps will be returned as unixtime, but may be offset by the servers timezone due to the way CiiMS currenlty stores and records timestamps.

-------------------------------------------------------

# Available Methods
The following methods are available in the CiiMS API.


## Authentication [/user/token]
While many resources from the API do not require authentication, any resource that modifies content (POST) or provides access to restricted content (such as password protects posts, or drafts owned by a contributor) requires an authentication.

#### [POST] [/user/token] Authenticating a User, Retrieving a Long Life Token

Before you can perform authenticated actions, you must first request an AUTH TOKEN. The AUTH TOKEN's provided by the API are long life API tokens and will not expire unless the origin password is changed, a new long life token is requests, or the token is explicity deleted.

In order to allow multiple devices and resources to concurrently access the API a user may have multiple long life tokens in use.

To request a new token for your application, submit the following payload to /user/authenticate.

    { "email" : "<user@email.tld>", "password" : "<password>", "name" : "<application_name>" }

If the users credentials are valid, you will be assigned a long life token for that application. If a long life token for that application and user already exist, a new token will be generated.

    { "status" : 200, "message" : null, "response" : { "name" : "<application_name>", "token" : "<token_string>" } }

If the users credentials are invalid, or if too many authentication requests for the user have been made in too short of a time a HTTP 400 status code will be returned.

    { "status" : 400, "message" : "Unable to authenticate", "response" : { } }

Once authenticated, all subsequent requests can be sent with the following HEADERS. Every request that requires authentication will accept this request.
    
    X-Auth-Email : <email>
    X-Auth-Token : <auth_token>

It's important to remember that these are long life tokens and should be treated with the same security levels as the user's password. While requests to change confidential user's information requires additional security (mainly the user's existing password), almost every other request can be manipulated using this authentication. If you ever believe that the users LLT has been comprimised, you should immediately request that the token be deauthenticated.

#### [DELETE] [/user/token/<token>] Deauthenticates a long life token

This method will kill the requested long life token belonging to the user associated to the session. Use this method if you believe the API token has been comprimised or if you wish to force new authentication.

-------------------------------------------------------

## Event [/event]
The Event API is a new feature to CiiMS and allows custom events to be triggered and captured for later processing. This new feature will integrate with Analytics.js and will allow a custom CiiMS analytics.js provider to recieve events sent by the provider.

In the future, this may also provide support for event notifications.

-------------------------------------------------------

## Category [/category]
The Category API allows users to access all viewable categories in the system, and if properly authenticated and privileges to manipulate particular categories.

#### [GET] [/category]
Lists all categories in the system. 

##### Example Response
    
    {"status":200,"message":null,"response":[{"id":"1","parent_id":"1","name":"New Category Name","slug":"newcategoryslug","created":1377734784,"updated":1382721259}}

#### [POST] [/category]
Creates a new category if the user is a site manager or administrator. 

The request must include the following fields:

    name
    slug

The following fields are optional (default value is assumed)

    parent_id : 1

##### Example Request

    { "name" : "category_name", "slug", "category_slug", "parent_id" : 1 }

##### Example Response
    
    {
        "status": 200,
        "message": null,
        "response": {
            "parent_id": 1,
            "name": "category_name",
            "slug": "category_slug",
            "created": 1382722520,
            "updated": 1382722520,
            "id": "147"
        }
    }

#### [/category/<id>]
Allows for modification and retrieval of categories.

#### [GET]
Retrieves a category with a given id

##### Example Response

    {
        "status": 200,
        "message": null,
        "response": {
            "parent_id": 1,
            "name": "category_name",
            "slug": "category_slug",
            "created": 1382722520,
            "updated": 1382722520,
            "id": "147"
        }
    }
#### [POST]
Modifies a category with a given id

CiiMS will only override values that you specify.

##### Example Request

    { "name" : "category_name", "slug", "category_slug", "parent_id" : 1 }

##### Example Response
    
        {
        "status": 200,
        "message": null,
        "response": {
            "parent_id": 1,
            "name": "category_name",
            "slug": "category_slug",
            "created": 1382722520,
            "updated": 1382722520,
            "id": "147"
        }
    }
    
#### [DELETE]
DELETE requests will permanently delete categories from the database. The only limitation on this request is that the root category cannot be deleted. The response for this will either be true or false depending upon if the request was successful or not.

##### Example Response

    {
        "status": 200,
        "message": null,
        "response": [true]
    }

-------------------------------------------------------

## Content [/content]
The Content API allows users to retrieve content by a particular content_id or slug. Authenticated users with appropriate privileges can also create new pieces of content and edit existing entries.

#### [GET]
Provides a list of available pieces of content. This response is very dependant upon whether the user is authenticated or not.

If the user is not authenticated, they may freely browse currently published content. This included password protected content.

Authenticated users who have the appropriate permissions will also see drafts that they are currently authoring, or drafts available in the system.

#### [POST]
Allows the creation of new comment

### [/content/<id>]

#### [GET]
Users may request published content. If the content piece requires a password AND they do not have editing access to a document, the user must supply a password via GET

	password

Administrators and content creators do not have to pass this option, as they have full read access to the content piece.

#### [POST]
Allows for modification of a given content piece

#### [DELETE]
Permanently deletes a content peice from the database

-------------------------------------------------------

## Comment [/comment]
The Comment API allows users to post and edit comments created by them, and for administrators to manage comments for their instance. The Comment API will be disabled if the site administrator has enabled Disqus comments for their site.

#### [POST]
Allows the creation of new comments

### [/comment/<id>/]
Allows for the manipulation of existing comments

#### [GET]
Retrieves a comment with a given id

#### [POST]
Updates a comment with a given id

#### [DELETE]
Permanently deletes a content with a given id

-------------------------------------------------------

## Setting [/setting]
The Setting API allows administrators to modify various settings for their site

### [/setting/<class>] [index|appearance|analytics|social|email]
Allows the manipulation of various settings that are found in the dashboard

#### [GET]
Retrieves all settings for a particular <class>

##### Example Response

    {
        "status": 200,
        "message": null,
        "response": {
            "name": "CiiMS Test Site",
            "dateFormat": "F jS, Y",
            "timeFormat": "H:i",
            "timezone": "America\/Chicago",
            "defaultLanguage": "en_US",
            "offline": "0",
            "bcrypt_cost": "13",
            "searchPaginationSize": "10",
            "categoryPaginationSize": "10",
            "contentPaginationSize": "10",
            "autoApproveComments": "1",
            "notifyAuthorOnComment": "1",
            "useDisqusComments": "0",
            "disqus_shortname": null,
            "sphinx_enabled": "0",
            "sphinxHost": "localhost",
            "sphinxPort": "9312",
            "sphinxSource": null
        }
    }

#### [POST]
Modifies a setting set for a particular <class>. Note that you are only able to set existing attributes. You only need to pass the attributes you want to change. For all intensive purposes all values should be treated as either ```null``` or as a ```string```, even if they are returned as an integer.

##### Example Request
    
    {
        "name" : "New Name",
        "usedisqusComments" : 1
    }

##### Example Response

    {
        "status": 200,
        "message": null,
        "response": {
            "name": "New Name",             // This value has changed
            "dateFormat": "F jS, Y",
            "timeFormat": "H:i",
            "timezone": "America\/Chicago",
            "defaultLanguage": "en_US",
            "offline": "0",
            "bcrypt_cost": "13",
            "searchPaginationSize": "10",
            "categoryPaginationSize": "10",
            "contentPaginationSize": "10",
            "autoApproveComments": "1",
            "notifyAuthorOnComment": "1",
            "useDisqusComments": "1",       // This value has changed
            "disqus_shortname": null,
            "sphinx_enabled": "0",
            "sphinxHost": "localhost",
            "sphinxPort": "9312",
            "sphinxSource": null
        }
    }

### [/setting/theme?type=(desktop|mobile|tablet)]

    Allows for retrieval and modification of various theme settings for the currently active theme of the selected type. Endpoint will default to ```desktop``` if not specified

#### [GET]

    Retrieves the theme options for a given theme. An empty object will be returned if nothing is available.

##### Example Response

    {
        "status": 200,
        "message": null,
        "response": {
            "twitterHandle": null,
            "twitterTweetsToFetch": "1",
            "splashLogo": null,
            "menu": "dashboard|blog"
        }
    }

#### [POST]

    Updates the theme settings.

##### Example Request

    {
        "twitterHandle" : "ciims",
        "menu" : "blog|dashboard"
    }

##### Example Response
    
     {
        "status": 200,
        "message": null,
        "response": {
            "twitterHandle": "ciims",
            "twitterTweetsToFetch": "1",
            "splashLogo": null,
            "menu": "blog|dashboard"
        }
    }

-------------------------------------------------------

## User [/user]
The User API endpoint provides access to allow a user to manipulate their own information, and if they are an administrator manipulate the user details of all users in their site.

#### [GET]
Will retrieve all users in the system. Only authenticated users can access this method

##### Example Response
    {
        "status": 200,
        "message": null,
        "response": [{
            "id": "1",
            "email": email@example.com",
            "firstName" : "Example",
            "lastName" : "Example",
            "displayName" "Example",
            "about": " ",
            "user_role": "9",
            "status": "1",
            "created": 1377734805,
            "updated": 1382800693
        }, {
            "id": "15",
            "email": "email@example.com",
            "firstName" : "Example",
            "lastName" : "Example",
            "displayName" "Example",
            "about": " ",
            "user_role": "1",
            "status": "1",
            "created": 1382792044,
            "updated": 1382799142
        }]
    }

#### [POST]
Will send an invitation out to new users. This method requires administrative access as either an Administrator or a Site Manager

The following fields are required:

    email


The following fields are optional:

    firstName
    lastName
    displayName
    about
    user_role

With the exception of ```user_role```, the user will be able to override any predefined values you set when they create their accounts. It's recommended that you just provide the email as in the example below.

##### Example Request
    
    {
        "email" : "email@example.com"
    }

##### Example Response

    {
        "status": 200,
        "message": null,
        "response": {
            "id": "1",
            "email": "email@example.com"
            "firstName": "",
            "lastName": "",
            "displayName": "",
            "about": "",
            "user_role": "1",
            "status": "1",
            "created": 1377734805,
            "updated": 1382800693
        }
    }

### [/user/<id>]
Allows for the modification of existing user data. If the user is currently authenticated they can modify their own user information via this endpoint. If the user is an admin or a site manager they can alter other users information.

#### [GET]
Retrieves user information for a given user. This is a privileges command.

##### Example Response

    {
        "status": 200,
        "message": null,
        "response": {
            "id": "1",
            "email": "email@example.com"
            "firstName": "",
            "lastName": "",
            "displayName": "",
            "about": "",
            "user_role": "1",
            "status": "1",
            "created": 1377734805,
            "updated": 1382800693
        }
    }

#### [POST]
Allows for modification of a given user. If the user is authenticated they will be able to change their own information. Administrative approval as either an Administrator or a Site Manager is required to make changes to other users. Please note the following:

1. The user's password can be changed from this endpoint. The user WILL NOT be notified that their password has been changed IF just their password is changed.
2. If the user's email address is changed a password verification process will be triggered. The users WILL be notified via email of the change, and will be required to go through the verification process before they can login again.

The user will still be notified of the email change in this instance. It's _HIGHLY_ recommended that you allow the normal password/email change policies built into CiiMS handle this.

##### Example Request
    
    {
        "email" : "email@example.com",
        "password" : "changeme7",
        "firstName" : "Example",
        "lastName" : "Example",
        "displayName" "Example",
        "about" : "I'm an Example!"
        "user_role" : 9, // Can only be changed by site managers/administrators
        "status" : 1 // Can only be changed by site managers/administrators
    }

##### Example Response

    {
        "status": 200,
        "message": null,
        "response": {
            "id": "1",
            "email": "email@example.com"
            "firstName": "",
            "lastName": "",
            "displayName": "",
            "about": "",
            "user_role": "1",
            "status": "1",
            "created": 1377734805,
            "updated": 1382800693
        }
    }
