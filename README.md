# Beskedfordeler bundle

See <https://digitaliseringskataloget.dk/integration/sf1461> for details on the
Beskedfordeler.

## Installation

Require the bundle:

```sh
composer require itk-dev/beskedfordeler-symfony
```

Enable the bundle:

```php
// config/bundles.php
return [
    // …
    Itkdev\BeskedfordelerBundle\ItkdevBeskedfordelerBundle::class => ['all' => true],
];
```

Import routes:

```yaml
# config/routes/itkdev_beskedfordeler.yaml
itkdev_beskedfordeler:
  resource: '@BeskedfordelerBundle/Resources/config/routes.php'
```

Make the `/beskedfordeler` routes publically accessible:

```yaml
# config/packages/security.yaml
security:
    # …
    access_control:
        # …
        - { path: ^/beskedfordeler, role: PUBLIC_ACCESS }
        # …
```

Routes:

* `/beskedfordeler/PostStatusBeskedModtag`

## Event subscriber

An event subscriber must be created to do something useful when getting a
message from Beskedfordeler:

```php
<?php
// src/EventSubscriber/BeskedfordelerEventSubscriber.php
namespace App\EventSubscriber;

use Itkdev\BeskedfordelerBundle\Event\PostStatusBeskedModtagEvent;
use Itkdev\BeskedfordelerBundle\Helper\MessageHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BeskedfordelerEventSubscriber implements EventSubscriberInterface
{
    private MessageHelper $messageHelper;

    public function __construct(private MessageHelper $messageHelper)
    {
    }

    public static function getSubscribedEvents() {
        return [
            PostStatusBeskedModtagEvent::class => 'postStatusBeskedModtag',
        ];
    }

    public function postStatusBeskedModtag(PostStatusBeskedModtagEvent $event): void {
        // Do something with the event.
        try {
            $data = $this->messageHelper->getBeskeddata($event->getDocument()->saveXML());
            // …
        } catch (\Throwable $exception) {
            // Log the exception.
        }
    }
}
```

## Development

See [docs/DEVELOPMENT.md](docs/DEVELOPMENT.md).
