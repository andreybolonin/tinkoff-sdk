<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK\E2Card;

use function Personnage\Tinkoff\SDK\interpolate;

trait HasSignature
{
    /**
     * Transform array values to string.
     *
     * @param  array  $data
     * @return string
     */
    public static function getValues(array $data): string
    {
        ksort($data);

        return implode('', $data);
    }

    /**
     * Get message digest by GOST Р 34.11-94.
     *
     * @param string $values List arguments
     *
     * @return string
     */
    public static function digest(string $values): string
    {
        return hash('gost-crypto', $values, true);
    }

    /**
     * Calculate signature from digest message.
     *
     * @param  string  $digest
     * @param  string  $pemFile
     * @return string
     */
    public static function sign(string $digest, string $pemFile): string
    {
        $filename = function ($resource): string {
            return stream_get_meta_data($resource)['uri'];
        };

        fwrite($_ = tmpfile(), $digest);

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
