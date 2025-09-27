<?php

namespace App\Models\Queries;

use App\Models\Session;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class IndexSessionQuery
{
    protected bool $paginate = false;

    protected ?int $perPage = null;

    protected ?int $offset = null;

    /**
     * @return Collection<int, \App\Models\Session>|LengthAwarePaginator<\App\Models\Session>
     */
    public function execute(): Collection|LengthAwarePaginator
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

    public function paginate(?int $perPage = null): self
    {
        $this->paginate = !is_null($perPage);

        $this->perPage = $perPage;

        return $this;
    }

    public function offset(?int $offset = null): self
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @param Builder<\App\Models\Session> $query
     */
    public function startedAfter(Builder $query, string $dateTime): void
    {
        $query->startedAfter(\Carbon\Carbon::parse($dateTime));
    }

    /**
     * @param Builder<\App\Models\Session> $query
     */
    public function startedBefore(Builder $query, string $dateTime): void
    {
        $query->startedBefore(\Carbon\Carbon::parse($dateTime));
    }
}
