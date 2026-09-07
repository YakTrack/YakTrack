<?php

namespace App\Services;

use App\Models\Session;
use App\Support\DateTimeFormatter;
use Illuminate\Support\Facades\DB;

class SessionSplitter
{
    public function __construct(private DateTimeFormatter $dateTimeFormatter)
    {
    }

    /**
     * @param array<int, array{started_at: string, ended_at: string, sprint_id?: int|null, task_id?: int|null}> $segments
     *
     * @return array{original: Session, created: array<int, Session>}
     */
    public function split(Session $session, array $segments): array
    {
        return DB::transaction(function () use ($session, $segments) {
            $created = [];

            // Capture the source values once: index 0 updates $session in place, so
            // reading these off $session inside the loop would return mutated values
            // for later segments that omit an explicit task_id/sprint_id.
            $defaults = [
                'task_id'             => $session->task_id,
                'sprint_id'           => $session->sprint_id,
                'invoice_id'          => $session->invoice_id,
                'session_category_id' => $session->session_category_id,
                'comment'             => $session->comment,
                'is_billable'         => $session->is_billable,
            ];

            foreach ($segments as $index => $segment) {
                $attributes = $this->segmentAttributes($defaults, $segment);

                if ($index === 0) {
                    $session->update($attributes);
                    continue;
                }

                $created[] = Session::create($attributes);
            }

            return [
                'original' => $session->fresh(),
                'created'  => $created,
            ];
        });
    }

    /**
     * @param array{task_id: int|null, sprint_id: int|null, invoice_id: int|null, session_category_id: int|null, comment: string|null, is_billable: bool|int|null} $defaults
     * @param array{started_at: string, ended_at: string, sprint_id?: int|null, task_id?: int|null}                                                                $segment
     *
     * @return array<string, mixed>
     */
    private function segmentAttributes(array $defaults, array $segment): array
    {
        return [
            'started_at'          => $this->dateTimeFormatter->utcFormat($segment['started_at']),
            'ended_at'            => $this->dateTimeFormatter->utcFormat($segment['ended_at']),
            'task_id'             => array_key_exists('task_id', $segment) ? $segment['task_id'] : $defaults['task_id'],
            'sprint_id'           => array_key_exists('sprint_id', $segment) ? $segment['sprint_id'] : $defaults['sprint_id'],
            'invoice_id'          => $defaults['invoice_id'],
            'session_category_id' => $defaults['session_category_id'],
            'comment'             => $defaults['comment'],
            'is_billable'         => $defaults['is_billable'],
        ];
    }
}
