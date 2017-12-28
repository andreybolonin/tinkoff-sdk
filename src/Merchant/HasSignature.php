<?php declare(strict_types=1);

namespace Personnage\Tinkoff\SDK\Merchant;

trait HasSignature
{
    /**
     * Get signature for values.
     *
     * @param array   $values
     * @param string  $secret
     *
     * @return string
     */
    public function sign(array $values, string $secret): string
    {
        unset($values['Token']);

        $values['Password'] = $secret;

        ksort($values);

        return hash('sha256', implode('', $values));
    }
}
