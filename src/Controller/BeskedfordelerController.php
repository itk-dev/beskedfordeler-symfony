<?php

namespace Itkdev\BeskedfordelerBundle\Controller;

use Itkdev\BeskedfordelerBundle\Exception\Exception;
use Itkdev\BeskedfordelerBundle\Helper\MessageHelper;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BeskedfordelerController implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use LoggerAwareTrait;

    public function __construct(LoggerInterface $logger)
    {
        $this->setLogger($logger);
    }

    /**
     * Handle PostStatusBeskedModtag.
     *
     * @see https://digitaliseringskataloget.dk/integration/sf1601
     */
    public function postStatusBeskedModtag(Request $request, MessageHelper $messageHelper): Response
    {
        try {
            $message = $request->getContent();

            $messageHelper->dispatch(
                MessageHelper::MESSAGE_TYPE_POST_STATUS_BESKED_MODTAG,
                $message,
                $request->server->get('REQUEST_TIME')
            );
        } catch (\Throwable $throwable) {
            $this->logger->error($throwable->getMessage(), [
                'throwable' => $throwable,
            ]);

            // Report only our own exceptions.
            if ($throwable instanceof Exception) {
                return $this->buildResponse(40, $throwable->getMessage());
            }
        }

        return $this->buildResponse(20);
    }

    /**
     * Build response.
     *
     * @see self::buildResponseDocument()
     */
    private function buildResponse(int $statusCode, string $errorMessage = null): Response
    {
        $document = $this->buildResponseDocument($statusCode, $errorMessage);

        $status = Response::HTTP_OK;
        $headers = ['content-type' => 'application/xml'];
        $content = $document->saveXML();

        return new Response($content, $status, $headers);
    }

    /**
     * Build Outputdokument.
     *
     * @param int         $statusCode
     *                                  The status code
     * @param string|null $errorMessage
     *                                  The error message if any
     *
     * @return \DOMDocument
     *                      The Outputdokument
     */
    private function buildResponseDocument(int $statusCode, string $errorMessage = null): \DOMDocument
    {
        $xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<ns2:ModtagBeskedOutput xmlns="urn:oio:sagdok:3.0.0" xmlns:ns2="urn:oio:sts:1.0.0">
 <StandardRetur>
  <StatusKode/>
  <FejlbeskedTekst/>
 </StandardRetur>
</ns2:ModtagBeskedOutput>
XML;

        $document = new \DOMDocument();
        $document->loadXML($xml);
        $xpath = new \DOMXPath($document);
        $xpath->registerNamespace('default', 'urn:oio:sagdok:3.0.0');

        $xpath->query('//default:StatusKode')->item(0)->nodeValue = $statusCode;
        $xpath->query('//default:FejlbeskedTekst')->item(0)->nodeValue = $errorMessage;

        return $document;
    }
}
