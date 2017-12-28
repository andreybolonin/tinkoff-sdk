<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK\E2Card;

use function Personnage\Tinkoff\SDK\interpolate;

trait HasSignature
{
    /**
     * Get path to X509 file.
     *
     * @return string
     */
    abstract public function getPemFile(): string;

    /**
     * Get message digest by GOST Р 34.11-94.
     *
     * @param array $values List arguments
     *
     * @return string
     */
    private function digest(array $values): string
    {
        ksort($values);

        return hash('gost-crypto', implode('', $values), true);
    }

    /**
     * Get serial number of X509.
     *
     * @return string
     */
    private function getSerialNumber(): string
    {
        $text = openssl_x509_parse('file://'.$this->getPemFile(), true);

        return $text['serialNumber'];
    }

    /**
     * Calculate signature from digest message.
     *
     * @param  array|string $message
     * @return string
     */
    private function sign($message): string
    {
        $filename = function ($resource): string {
            return stream_get_meta_data($resource)['uri'];
        };

        if (\is_array($message)) {
            /*
             * Remove DigestValue field if it exists
             */
            unset($message['DigestValue']);
            $message = $this->digest($message);
        }

        fwrite($_ = tmpfile(), $message);

        static $smime = <<<'CMD'
openssl smime -sign -signer %signer% -engine gost -gost89 -binary -noattr -nocerts -outform DER -in %in% -out %out%
CMD;
        shell_exec(interpolate($smime, [
            'in' => $filename($_),
            'out' => $filename($__ = tmpfile()),
            'signer' => $this->getPemFile(),
        ]));

        $output = shell_exec('openssl asn1parse -inform DER -in ' . $filename($__));

        return hex2bin(trim(substr($output, -129)));
    }
}
