Page
----

The active page


Put it on the config/app.php

```
'providers' => [
    ...,
    Backtheweb\ReCaptcha\ReCaptchaServiceProvider::class
];
```

And the facade alias
    
    'ReCaptcha' =>  Backtheweb\ReCaptcha\Facade::class

## Publish

    php artisan vendor:publish --provider="Backtheweb\ReCaptcha\ReCaptchaServiceProvider" --tag="config"