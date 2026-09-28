<?php

use App\Integrations\ThirdPartyTasks\Jira\JiraRestClient;

it('recognises queries shaped like jira issue keys', function (string $query) {
    expect(JiraRestClient::looksLikeIssueKey($query))->toBeTrue();
})->with([
    'uppercase'              => 'KEY-1',
    'lowercase'              => 'key-1',
    'underscore in project'  => 'MY_PROJ-12',
    'digits in project'      => 'AB2-7',
    'surrounding whitespace' => '  KEY-1  ',
]);

it('does not treat free text as a jira issue key', function (string $query) {
    expect(JiraRestClient::looksLikeIssueKey($query))->toBeFalse();
})->with([
    'plain word'          => 'login',
    'missing number'      => 'KEY-',
    'missing project'     => '-12',
    'starts with a digit' => '1KEY-2',
    'internal space'      => 'KEY -1',
    'trailing text'       => 'KEY-1 login',
    'multiple dashes'     => 'KEY-1-2',
]);
