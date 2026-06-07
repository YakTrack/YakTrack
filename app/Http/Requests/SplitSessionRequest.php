<?php

namespace App\Http\Requests;

use App\Models\Session;
use App\Support\DateTimeFormatter;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SplitSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Session $session */
        $session = $this->route('session');

        if ($session->isRunning()) {
            return [];
        }

        if ($this->has('segments')) {
            return [
                'segments'              => ['required', 'array', 'min:2'],
                'segments.*.started_at' => ['required', 'date'],
                'segments.*.ended_at'   => ['required', 'date'],
                'segments.*.sprint_id'  => ['nullable', 'integer', 'exists:sprints,id'],
                'segments.*.task_id'    => ['nullable', 'integer', 'exists:tasks,id'],
            ];
        }

        return [
            'split_time' => [
                'required',
                'date',
                'after:'.$session->localStartedAt->format('Y-m-d H:i:s'),
                'before:'.$session->localEndedAt->format('Y-m-d H:i:s'),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var Session $session */
            $session = $this->route('session');

            if ($session->isRunning() || !$this->has('segments')) {
                return;
            }

            $segments = $this->input('segments', []);

            if (!is_array($segments) || count($segments) < 2) {
                return;
            }

            $formatter = app(DateTimeFormatter::class);
            $sessionStartUtc = $formatter->utcFormat($session->localStartedAt->format('Y-m-d H:i:s'));
            $sessionEndUtc = $formatter->utcFormat($session->localEndedAt->format('Y-m-d H:i:s'));

            $firstSegmentStartUtc = $formatter->utcFormat($segments[0]['started_at'] ?? '');
            $lastSegmentEndUtc = $formatter->utcFormat($segments[count($segments) - 1]['ended_at'] ?? '');

            if ($firstSegmentStartUtc !== $sessionStartUtc) {
                $validator->errors()->add('segments.0.started_at', 'The first segment must start when the session starts.');
            }

            if ($lastSegmentEndUtc !== $sessionEndUtc) {
                $validator->errors()->add('segments.'.(count($segments) - 1).'.ended_at', 'The last segment must end when the session ends.');
            }

            foreach ($segments as $index => $segment) {
                $startedAtUtc = $formatter->utcFormat($segment['started_at'] ?? '');
                $endedAtUtc = $formatter->utcFormat($segment['ended_at'] ?? '');

                if (Carbon::parse($endedAtUtc)->lessThanOrEqualTo(Carbon::parse($startedAtUtc))) {
                    $validator->errors()->add("segments.{$index}.ended_at", 'Each segment must have a positive duration.');
                }

                if (Carbon::parse($startedAtUtc)->lessThan(Carbon::parse($sessionStartUtc))
                    || Carbon::parse($endedAtUtc)->greaterThan(Carbon::parse($sessionEndUtc))) {
                    $validator->errors()->add("segments.{$index}.started_at", 'Segments must stay within the session duration.');
                }

                if ($index > 0) {
                    $previousEndedAtUtc = $formatter->utcFormat($segments[$index - 1]['ended_at'] ?? '');

                    if ($startedAtUtc !== $previousEndedAtUtc) {
                        $validator->errors()->add("segments.{$index}.started_at", 'Segments must be contiguous with no gaps or overlaps.');
                    }
                }
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'split_time.required' => 'Select a split time.',
            'segments.required'   => 'Provide at least two segments to split the session.',
            'segments.min'        => 'Provide at least two segments to split the session.',
        ];
    }
}
