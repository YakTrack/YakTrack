<?php

namespace App\Integrations\ThirdPartyTasks\Contracts;

interface VerifiesExternalTaskConnection
{
    /**
     * Confirm API credentials can reach the third-party service.
     *
     * @param array<string, string> $credentials Keys depend on the driver (see implementation).
     */
    public function verify(array $credentials): void;
}
