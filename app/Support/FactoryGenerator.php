<?php

namespace App\Support;

class FactoryGenerator
{
    public function projectName()
    {
        return collect([
            $this->projectVerb(),
            $this->nounAndArticle(),
        ])->implode(' ');
    }

    public function taskName()
    {
        return collect([
            $this->storyUser(),
            $this->taskVerb(),
            $this->nounAndArticle(),
        ])->implode(' ');
    }

    protected function projectVerb()
    {
        return collect([
            'build',
            'calculate',
            'compose',
            'consider',
            'construct',
            'design',
            'develop',
            'document',
            'estimate',
            'explore',
            'rebuild',
            'reconsider',
            'redesign',
            'refactor',
        ])->random();
    }

    protected function taskVerb()
    {
        return collect([
            'click',
            'update',
            'view',
            'edit',
            'update',
            'drag',
            'search through',
            'filter',
            'delete',
            'deactivate',
            'activate',
            'enable',
            'disable',
            'exit',
            'login to',
            'logout of',
            'switch to',
            'switch from',
            'navigate to',
            'navigate away from',
        ])->random();
    }

    protected function nounAndArticle()
    {
        $isPlural = array_random([true, false]);
        $noun = $isPlural ? $this->pluralNoun() : $this->noun();
        $article = $isPlural ? $this->pluralArticle() : $this->article();

        return collect([
            $article,
            $noun,
        ])->implode(' ');
    }

    protected function article()
    {
        return collect([
            'the',
            'a',
            'one',
            'another',
            'the first',
            'the last',
            'the latest',
            'the newest',
            'the oldest',
        ])->random();
    }

    protected function pluralArticle()
    {
        return collect([
            'the',
            'two',
            'some',
            'many',
            'multiple',
            'the first',
            'the last',
            'the latest',
            'the newest',
            'the oldest',
        ])->random();
    }

    protected function noun()
    {
        return collect([
            'app',
            'application',
            'approach',
            'book',
            'class',
            'client',
            'estimate',
            'library',
            'package',
            'plan',
            'process',
            'project',
            'quote',
            'script',
            'server',
            'task',
            'tool',
            'website',
            'workflow',
            'blog post',
        ])->random();
    }

    protected function pluralNoun()
    {
        return str_plural($this->noun());
    }

    protected function storyUser()
    {
        return collect([
            'as a user I can',
            'as an admin I can',
            'as a guest I can',
            'as a vendor I can',
            'as a subscriber I can',
        ])->random();
    }
}
