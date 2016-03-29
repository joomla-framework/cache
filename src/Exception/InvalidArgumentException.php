<?php

namespace Joomla\Cache\Exception;

use Psr\Cache\InvalidArgumentException as PsrInvalidArgumentExceptionInterface;

/**
 * Joomla! Caching Class Invalid Argument Exception
 *
 * @since  1.0
 */
class InvalidArgumentException extends \InvalidArgumentException implements PsrInvalidArgumentExceptionInterface
{

}