# google-auth-wrapper
Uses google-api-php-client and simplifies configuration / use
## Getting started with composer
```
"require": {
  "barnabynorman/google-auth-wrapper"
}
```
Make sure to include the autoloader:
```
require_once '/path/to/your-project/vendor/autoload.php';
```
Register with google at:
[https://github.com/googleapis/google-api-php-client/blob/main/docs/README.md](https://github.com/googleapis/google-api-php-client/blob/main/docs/README.md)

Note your:
- Client ID
- Client Secret

## Basic use
```php
<?php
require_once 'vendor/autoload.php';

// This is your landing place
$redirectUrl = 'https://research.familynorman.org.uk';

$clientId = 'your google client ID';
$clientSecret = 'your google client Secret';

$auth = new GoogleAuthWrapper($clientId, $clientSecret, $redirectUrl);

$authResponse = $auth->doLogin();

if (is_array($authResponse)) {
    // Redirect response as part of login processs
	header('Location: ' . $authResponse['redirect']);

} elseif ($authResponse !== false) {
    // Authenticated with google

    // Test the email address
    switch ($authResponse) {
        case 'bob@test.com':
        // Content here
        break;

        case 'jane@test.com':
        // Content here
        break;

        case 'jane@test.com':
        // Content here
        break;

        case 'mary@test.com':
        // Content here
        break;

        default:
        // Show login page or 404 etc
    }
}
```