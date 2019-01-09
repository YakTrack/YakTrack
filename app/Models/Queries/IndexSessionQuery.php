<?php

namespace App\Models\Queries;

use App\Models\Session;

class IndexSessionQuery
{
    public function execute()
    {
        $query = Session::orderBy('started_at', 'desc')
            ->with(['task.project.client', 'invoice', 'thirdPartyApplicationSessions', 'sprint']);

        collect([
            'started_after',
        ])->filter(function ($filter) {
            return request()->has($filter);
        })->each(function ($filter) use ($query) {
            $query = $this->{camel_case($filter)}($query, request($filter));
        });

        return $query->get();
    }

    public function startedAfter($query, $dateTime)
    {
        $query->startedAfter($dateTime);
    }
}
