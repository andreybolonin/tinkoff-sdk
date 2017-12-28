<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Parse a stream to array.
 *
 * @param StreamInterface $stream
 * @return array
 */
function stream_decode(StreamInterface $stream): array
{
    return json_decode((string) $stream, true);
}

/**
 * Get all contents from message instance.
 *
 * @param MessageInterface $message
 * @return array
 */
function message_get_body(MessageInterface $message): array
{
    return stream_decode($message->getBody());
}

/**
 * Interpolates context values into the message placeholders.
 *
 * @param string $message
 * @param array  $context
 * @return string
 */
function interpolate(string $message, array $context = []): string
{
    $replace = [];
    foreach ($context as $key => $val) {
        $replace['%' . $key . '%'] = $val;
    }

    return strtr($message, $replace);
}

/**
 * Encode values for transfer by http.
 *
 * @param  string|array  $values
 * @param  string        $separator
 * @return string
 */
function pack($values, string $separator = '|'): string
{
    if (is_array($values)) {
        return http_build_query($values, '', $separator);
    }

    return urlencode(urldecode($values));
}

/**
 * Decode data after receive.
 *
 * @param  string|null $data
 * @param  string      $separator
 * @return array
 */
function unpack($data, string $separator = '|'): array
{
    if (empty($data)) {
        return [];
    }

    $coercion = function ($value) {
        switch (strtolower($value)) {
            case 'y':
            case 'true':
                return true;
            case 'n':
            case 'false':
                return false;
            case 'null':
                return null;
            default:
                return $value;
        }
    };

    $values = [];

    foreach (explode($separator, urldecode($data)) as $value) {
        list($k, $v) = explode('=', $value);

        $values[$k] = $coercion($v);
    }

    return $values;
}
