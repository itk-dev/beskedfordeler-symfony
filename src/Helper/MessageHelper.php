<?php

namespace Itkdev\BeskedfordelerBundle\Helper;

use Itkdev\BeskedfordelerBundle\Event\AbstractBeskedModtagEvent;
use Itkdev\BeskedfordelerBundle\Event\PostStatusBeskedModtagEvent;
use Itkdev\BeskedfordelerBundle\Exception\InvalidMessageException;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Message helper.
 */
final class MessageHelper
{
    use LoggerAwareTrait;

    public const MESSAGE_TYPE_POST_STATUS_BESKED_MODTAG = 'PostStatusBeskedModtag';

    /**
     * The event dispatcher.
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * Constructor.
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, LoggerInterface $logger)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->setLogger($logger);
    }

    /**
     * Get event type form message type.
     */
    private function getEventType(string $messageType): string
    {
        switch ($messageType) {
            case self::MESSAGE_TYPE_POST_STATUS_BESKED_MODTAG:
                return PostStatusBeskedModtagEvent::class;

            default:
                throw new InvalidMessageException(sprintf('Invalid message type: %s', $messageType));
        }
    }

    /**
     * Dispatch message event.
     */
    public function dispatch(string $type, string $message, int $createdAt): AbstractBeskedModtagEvent
    {
        $document = new \DOMDocument();
        if (empty(trim($message)) || !@$document->loadXML($message)) {
            throw new InvalidMessageException(sprintf('Invalid XML: %s', $message));
        }

        $eventType = $this->getEventType($type);
        $event = new $eventType($document, $createdAt);

        return $this->eventDispatcher->dispatch($event);
    }

    /**
     * Get decoded beskeddata as XML.
     *
     * @return string|null
     *                     The XML
     */
    public function getBeskeddataXml(string $message): ?string
    {
        $document = new \DOMDocument();
        if (@$document->loadXML($message)) {
            $xpath = new \DOMXPath($document);
            $xpath->registerNamespace('data', 'urn:besked:kuvert:1.0');
            if ($nodes = $xpath->query('//data:Base64')) {
                foreach ($nodes as $node) {
                    assert($node instanceof \DOMElement);
                    $data = new \DOMDocument();
                    $data->formatOutput = true;
                    if (@$data->loadXML(base64_decode($node->nodeValue))) {
                        return $data->saveXML();
                    }
                }
            }
        }

        return null;
    }

    /**
     * Get decoded beskeddata as array.
     *
     * @return array|null
     *                    The data if any
     *
     * @phpstan-return array<string, mixed>
     */
    public function getBeskeddata(string $message): ?array
    {
        $xml = $this->getBeskeddataXml($message);
        if (null !== $xml) {
            // Create SimpleXMLElement instance and convert to array in two levels.
            return array_map(static function ($value) {
                return $value instanceof \SimpleXMLElement ? (array) $value : $value;
            }, (array) (new \SimpleXMLElement($xml)));
        }

        return null;
    }
}
