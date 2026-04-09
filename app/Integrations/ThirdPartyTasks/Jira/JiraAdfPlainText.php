<?php

namespace App\Integrations\ThirdPartyTasks\Jira;

final class JiraAdfPlainText
{
    /**
     * @param  mixed  $description
     */
    public static function fromIssueField(mixed $description): string
    {
        if ($description === null || $description === '') {
            return '';
        }

        if (is_string($description)) {
            return $description;
        }

        if (! is_array($description)) {
            return '';
        }

        return trim(self::walk($description));
    }

    /**
     * @param  array<string, mixed>  $node
     */
    private static function walk(array $node): string
    {
        if (isset($node['text']) && is_string($node['text']) && empty($node['content'] ?? [])) {
            return $node['text'];
        }

        $parts = [];

        if (isset($node['content']) && is_array($node['content'])) {
            foreach ($node['content'] as $child) {
                if (is_array($child)) {
                    $parts[] = self::walk($child);
                }
            }
        }

        $glue = match ($node['type'] ?? '') {
            'doc' => "\n\n",
            'paragraph', 'heading' => '',
            default => "\n",
        };

        return implode($glue, array_filter($parts, fn (string $p): bool => $p !== ''));
    }
}
