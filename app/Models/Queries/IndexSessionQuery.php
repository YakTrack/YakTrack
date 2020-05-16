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
            ->addSelect([
                'task_name' => function ($query) {
                    $query
                        ->select('name')
                        ->from('tasks')
                        ->whereColumn('id', 'sessions.task_id');
                },
                'project_id' => function ($query) {
                    $query
                        ->select('project_id')
                        ->from('tasks')
                        ->whereColumn('id', 'sessions.task_id');
                },
                'project_name' => function ($query) {
                    $query
                        ->select('name')
                        ->from('projects')
                        ->whereColumn('id', 'project_id');
                },
                'client_id' => function ($query) {
                    $query
                        ->select('client_id')
                        ->from('projects')
                        ->whereColumn('id', 'project_id');
                },
                'client_name' => function ($query) {
                    $query
                        ->select('name')
                        ->from('clients')
                        ->whereColumn('id', 'client_id');
                },
                'invoice_number' => function ($query) {
                    $query
                        ->select('number')
                        ->from('invoices')
                        ->whereColumn('id', 'invoice_id');
                },
                'sprint_name' => function ($query) {
                    $query
                        ->select('name')
                        ->from('sprints')
                        ->whereColumn('id', 'sprint_id');
                },
            ]);

        collect([
            'started-after',
            'started-before',
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

    public function startedBefore($query, $dateTime)
    {
        $query->startedBefore($dateTime);
    }
}
