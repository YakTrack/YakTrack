<?php

namespace App\Models\Queries;

use App\Models\Session;

class IndexSessionQuery
{
    protected $paginate = false;

    protected $perPage = null;

    protected $offset = null;

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

        if ($this->offset) {
            $query->offset($this->offset);
        }

        return $this->paginate ? $query->paginate($this->perPage) : $query->get();
    }

    public function paginate($perPage = null)
    {
        $this->paginate = !is_null($perPage);

        $this->perPage = $perPage;

        return $this;
    }

    public function offset($offset = null)
    {
        $this->offset = $offset;

        return $this;
    }

    public function startedAfter($query, $dateTime)
    {
        $query->startedAfter($dateTime);
    }
}
