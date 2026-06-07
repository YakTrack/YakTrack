<?php

namespace App\Services;

use App\Models\Session;
use App\Support\DateTimeFormatter;
use Illuminate\Support\Facades\DB;

class SessionSplitter
{
    public function __construct(private DateTimeFormatter $dateTimeFormatter) {}

    /**
     * @param  array<int, array{started_at: string, ended_at: string, sprint_id?: int|null, task_id?: int|null}>  $segments
     * @return array{original: Session, created: array<int, Session>}
     */
    public function split(Session $session, array $segments): array
    {
        return DB::transaction(function () use ($session, $segments) {
            $created = [];

            foreach ($segments as $index => $segment) {
                $attributes = $this->segmentAttributes($session, $segment);

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
     * @param  array{started_at: string, ended_at: string, sprint_id?: int|null, task_id?: int|null}  $segment
     * @return array<string, mixed>
     */
    private function segmentAttributes(Session $session, array $segment): array
    {
        return [
            'started_at'          => $this->dateTimeFormatter->utcFormat($segment['started_at']),
            'ended_at'            => $this->dateTimeFormatter->utcFormat($segment['ended_at']),
            'task_id'             => array_key_exists('task_id', $segment) ? $segment['task_id'] : $session->task_id,
            'sprint_id'           => array_key_exists('sprint_id', $segment) ? $segment['sprint_id'] : $session->sprint_id,
            'invoice_id'          => $session->invoice_id,
            'session_category_id' => $session->session_category_id,
            'comment'             => $session->comment,
            'is_billable'         => $session->is_billable,
        ];
    }
}
