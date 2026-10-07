<?php

namespace App\Integrations\ThirdPartyTasks\Jira;

use Exception;

/**
 * Raised when Jira rejects the stored integration credentials, so an expired or
 * revoked API token is not reported to the user as an empty set of results.
 */
final class JiraAuthenticationException extends Exception
{
}
