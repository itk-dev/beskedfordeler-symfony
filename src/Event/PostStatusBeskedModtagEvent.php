<?php

namespace Itkdev\BeskedfordelerBundle\Event;

/**
 * Event for PostStatusBeskedModtag.
 */
final class PostStatusBeskedModtagEvent extends AbstractBeskedModtagEvent
{
    /**
     * {@inheritdoc}
     */
    protected static string $type = 'PostStatusBeskedModtag';
}
