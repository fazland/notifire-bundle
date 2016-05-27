Notifire Bundle  
===============
Notifire Bundle provides integration of [Notifire](https://github.com/fazland/Notifire) library into the [Symfony](https://github.com/symfony/symfony) Framework.

Notifire is a PHP library that centralizes the management of notifications (e-mails, sms, push notifications, etc.), check its [GitHub page](https://github.com/fazland/Notifire) to learn more.

Requirements
------------
- php >= 5.5.0
- symfony/symfony >= 2.7
- fazland/notifire >= 1.0

Installation
------------
The suggested installation method is via [composer](https://getcomposer.org/):

```sh
$ composer require fazland/notifire-bundle
```

Using Notifire Bundle
---------------------
First of all, register `NotifireBundle` in your `AppKernel.php`:
```php
public function registerBundles()
{
    return [
        // ...
        new NotifireBundle(),
        // ...
    ];
}
```

The configuration of Notifire Bundle is simple, just include in your `app/config/config.xml` (or equivalent) something like the following:
```xml
<notifire:config>
    <notifire:swiftmailer enabled="true" auto_configure_mailers="true" />
    <notifire:twilio enabled="true">
        <notifire:service
            name="%twilio_name%"
            account_sid="%twilio_account_sid%"
            auth_token="%twilio_auth_token%"
            from_phone="%twilio_from_phone%"
        />
    </notifire:twilio>
    <notifire:mailgun enabled="true">
        <notifire:mailer
            api_key="api_key"
            domain="example.org"
        />
    </notifire:mailgun>
</notifire:config>
```

YAML version:
```yml
notifire:
    swiftmailer:
        enabled: true
        auto_configure_mailers: true
    twilio:
        enabled: true
        services:
            service:
                name: '%twilio_name%'
                account_sid: '%twilio_account_sid%'
                auth_token: '%twilio_auth_token%'
                from_phone: '%twilio_from_phone%'
    mailgun:
        enabled: true
        mailers:
            mailer:
                domain: '%mailgun_domain%'
                api_key: '%mailgun_api_key%'
                
```

This configuration snippet registers in `Notifire` all your existing [SwiftMailer](https://github.com/swiftmailer/swiftmailer)'s mailers, the specified
[Twilio](https://github.com/twilio/twilio-php)'s services and [Mailgun](https://github.com/mailgun/mailgun-php)'s mailers.

If you want to register by instance only a set of SwiftMailer` mailers just use:
```xml
<!-- ... -->
    <notifire:swiftmailer enabled="true" auto_configure_mailers="false">
        <notifire:mailer>%your_mailer%</notifire:mailer>
    </notifire:swiftmailer>
<!-- ... -->
```
or in YAML:

```yml
# ...
    swiftmailer:
        enabled: true
        auto_configure_mailers: false
        mailers:
            mailer: '%your_mailer%'
# ... 
```

This configuration will provide `Notifire` configured and set in your container and its `handlers` ready to send your notifications!

As usual, just create an e-mail with `Notifire::email()` and send it:
```php
$email = Notifire::email([
    'provider' => 'swiftmailer',
    'mailer' => 'default'
]);

$email
    ->addFrom('test@fazland.com')
    ->addTo('info@example.org')
    ->setSubject('Only wonderful E-mails with Notifire!')
    ->addPart(Part::create($body, 'text/html'))
    ->send()
;
```

Contributing
------------
Contributions are welcome. Feel free to open a PR or file an issue here on GitHub!

License
-------
Notifire Bundle is licensed under the MIT License - see the [LICENSE](https://github.com/fazland/notifire-bundle/blob/master/LICENSE) file for details

