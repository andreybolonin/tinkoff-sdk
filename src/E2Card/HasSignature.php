<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK\E2Card;

use function Personnage\Tinkoff\SDK\interpolate;

trait HasSignature
{
    /**
     * Get message digest by GOST Р 34.11-94.
     *
     * @param array $values List arguments
     *
     * @return string
     */
    protected static function digest(array $values): string
    {
        ksort($values);

        return hash('gost-crypto', implode('', $values), true);
    }

    /**
     * Calculate signature from digest message.
     *
     * @param  string|array  $message
     * @param  string        $pemFile
     * @return string
     */
    protected static function sign($message, string $pemFile): string
    {
        $filename = function ($resource): string {
            return stream_get_meta_data($resource)['uri'];
        };

        if (\is_array($message)) {
            /*
             * Remove DigestValue field if it exists
             */
            unset($message['DigestValue']);
            $message = static::digest($message);
        }

        fwrite($_ = tmpfile(), $message);

        static $smime = <<<'CMD'
openssl smime -sign -signer %signer% -engine gost -gost89 -binary -noattr -nocerts -outform DER -in %in% -out %out%
CMD;
        shell_exec(interpolate($smime, [
            'in' => $filename($_),
            'out' => $filename($__ = tmpfile()),
            'signer' => $pemFile,
        ]));

        $output = shell_exec('openssl asn1parse -inform DER -in ' . $filename($__));

        return hex2bin(trim(substr($output, -129)));
    }
}
