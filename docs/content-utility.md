# Content Utility
The content utility will allow you to simply get and interact with a URIs content

```php
$utility = new ContentUtility('https://google.com');
```

## client() : Client
Get the client that the utility is using to communicate with the resource.

```php
$utility->client();
```

## response() : Myerscode\Resource\Response
Get a Response object content, which contains the status of the response and string value.

```php
$utility->dom();
```

## content() : string
Get the string response of the URIs content

```php
$utility->content();
```

## dom() : Myerscode\Resource\Dom
Get a Dom object of the URIs content

```php
$utility->dom();
```

## url() : string
Get the URI that the utility is using

```php
$utility->url();
```
