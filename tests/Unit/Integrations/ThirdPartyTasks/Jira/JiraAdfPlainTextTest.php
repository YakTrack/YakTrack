<?php

use App\Integrations\ThirdPartyTasks\Jira\JiraAdfPlainText;

it('extracts plain text from atlassian document format', function () {
    $adf = [
        'type' => 'doc',
        'version' => 1,
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'text', 'text' => 'Hello '],
                    ['type' => 'text', 'text' => 'world'],
                ],
            ],
        ],
    ];

    expect(JiraAdfPlainText::fromIssueField($adf))->toBe('Hello world');
});

it('returns strings unchanged', function () {
    expect(JiraAdfPlainText::fromIssueField('Plain'))->toBe('Plain');
});
