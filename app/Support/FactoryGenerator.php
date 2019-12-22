<?php

namespace App\Support;

class FactoryGenerator
{
    public function projectName()
    {
        return collect([
            $this->projectVerb(),
            $this->projectNounAndArticle(),
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

    protected function projectNounAndArticle()
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
}
