<?php

namespace App\Integrations\ThirdPartyTasks;

final readonly class ExternalTaskPayload
{
    public function __construct(
        public string $externalKey,
        public string $title,
        public string $description,
    ) {
    }
}
