<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $project->name }} - Project Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin: 0 0 10px 0;
            color: #1f2937;
        }
        .header p {
            margin: 5px 0;
            color: #6b7280;
        }
        .project-info {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 1px solid #e5e7eb;
        }
        .project-info h2 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #1f2937;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        .stat-item {
            display: table-cell;
            text-align: center;
            padding: 15px;
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            vertical-align: top;
        }
        .stat-item:first-child {
            border-radius: 8px 0 0 8px;
        }
        .stat-item:last-child {
            border-radius: 0 8px 8px 0;
        }
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            display: block;
        }
        .stat-label {
            font-size: 11px;
            color: #6b7280;
            margin-top: 5px;
        }
        .tasks-section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        .task-item {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background-color: #ffffff;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .task-header {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .task-name {
            display: table-cell;
            font-weight: bold;
            font-size: 14px;
            color: #1f2937;
            vertical-align: middle;
        }
        .task-status {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            color: white;
            white-space: nowrap;
        }
        .task-stats {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 15px;
        }
        .sessions-list {
            margin-top: 10px;
        }
        .session-item {
            padding: 10px;
            margin-bottom: 8px;
            background-color: #f9fafb;
            border-radius: 6px;
            border: 1px solid #f3f4f6;
        }
        .session-header {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        .session-date {
            display: table-cell;
            font-size: 11px;
            color: #1f2937;
            font-weight: bold;
        }
        .session-duration {
            display: table-cell;
            text-align: right;
            font-size: 11px;
            color: #6b7280;
        }
        .session-category {
            display: inline-block;
            background-color: #e5e7eb;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            color: #374151;
            margin-left: 10px;
        }
        .session-comment {
            margin-top: 5px;
            font-size: 11px;
            color: #4b5563;
            font-style: italic;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
        .page-break {
            page-break-before: always;
        }
        .invoice-info {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9fafb;
            border-radius: 6px;
            border-left: 4px solid #3b82f6;
        }
        .invoice-summary {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #fef3c7;
            border-radius: 8px;
            border-left: 4px solid #f59e0b;
        }
        .invoice-summary h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #92400e;
            font-weight: bold;
        }
        .invoice-stats {
            display: table;
            width: 100%;
        }
        .invoice-stat {
            display: table-cell;
            padding: 5px 10px;
            text-align: center;
            border-right: 1px solid #d1d5db;
        }
        .invoice-stat:last-child {
            border-right: none;
        }
        .invoice-stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
        }
        .invoice-stat-label {
            font-size: 11px;
            color: #6b7280;
            margin-top: 2px;
        }
        .session-invoice {
            font-size: 10px;
            color: #059669;
            margin-left: 10px;
            font-weight: bold;
        }
        .session-uninvoiced {
            font-size: 10px;
            color: #059669;
            margin-left: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $project->name }}</h1>
        <p><strong>Project Report</strong></p>
        <p>Generated on {{ now()->setTimezone(env('DISPLAY_TIMEZONE', config('app.timezone', 'UTC')))->format('F j, Y \a\t g:i A T') }}</p>
        @if($project->description)
            <p>{{ $project->description }}</p>
        @endif
    </div>

    <div class="stats-grid">
        <div class="stat-item">
            <span class="stat-value">{{ $project->tasks->count() }}</span>
            <div class="stat-label">Total Tasks</div>
        </div>
        <div class="stat-item">
            <span class="stat-value">{{ $project->tasks->sum(function($task) { return $task->sessions->count(); }) }}</span>
            <div class="stat-label">Total Sessions</div>
        </div>
        <div class="stat-item">
            <span class="stat-value">{{ number_format($project->tasks->sum(function($task) { return $task->sessions->sum('duration_in_seconds') / 3600; }), 1) }}h</span>
            <div class="stat-label">Total Hours</div>
        </div>
        <div class="stat-item">
            <span class="stat-value">{{ $project->tasks->where('taskStatus.is_completed', true)->count() }}</span>
            <div class="stat-label">Completed Tasks</div>
        </div>
    </div>

    <!-- Invoice Summary -->
    @php
        $allSessions = $project->tasks->flatMap(function($task) { return $task->sessions; });
        $invoicedSessions = $allSessions->whereNotNull('invoice_id');
        $uninvoicedSessions = $allSessions->whereNull('invoice_id');

        $totalHours = $allSessions->sum('duration_in_seconds') / 3600;
        $invoicedHours = $invoicedSessions->sum('duration_in_seconds') / 3600;
        $uninvoicedHours = $uninvoicedSessions->sum('duration_in_seconds') / 3600;

        $uniqueInvoices = $invoicedSessions->pluck('invoice')->whereNotNull()->unique('id');
    @endphp

    <div class="invoice-summary">
        <h3>Billing Summary</h3>
        <div class="invoice-stats">
            <div class="invoice-stat">
                <div class="invoice-stat-value">{{ number_format($totalHours, 1) }}h</div>
                <div class="invoice-stat-label">Total Hours</div>
            </div>
            <div class="invoice-stat">
                <div class="invoice-stat-value">{{ number_format($invoicedHours, 1) }}h</div>
                <div class="invoice-stat-label">Invoiced Hours</div>
            </div>
            <div class="invoice-stat">
                <div class="invoice-stat-value" style="color: #dc2626;">{{ number_format($uninvoicedHours, 1) }}h</div>
                <div class="invoice-stat-label">Uninvoiced Hours</div>
            </div>
            <div class="invoice-stat">
                <div class="invoice-stat-value">{{ $uniqueInvoices->count() }}</div>
                <div class="invoice-stat-label">Total Invoices</div>
            </div>
        </div>
    </div>

    <div class="tasks-section">
        <h2 class="section-title">Tasks & Sessions</h2>

        @forelse($project->tasks as $task)
            <div class="task-item">
                <div class="task-header">
                    <div class="task-name">{{ $task->name }}</div>
                    <div class="task-status">
                        @if($task->taskStatus)
                            <span class="status-badge" style="background-color: {{ $task->taskStatus->color }};">
                                {{ $task->taskStatus->name }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="task-stats">
                    {{ $task->sessions->count() }} sessions • {{ number_format($task->sessions->sum('duration_in_seconds') / 3600, 1) }}h total
                </div>

                @if($task->sessions->count() > 0)
                    <div class="sessions-list">
                        <strong style="font-size: 12px;">Work Sessions:</strong>
                        @foreach($task->sessions as $session)
                            <div class="session-item">
                                <div class="session-header">
                                    <div class="session-date">
                                        @php
                                            $displayTimezone = env('DISPLAY_TIMEZONE', config('app.timezone', 'UTC'));

                                            $startDate = $session->started_at->setTimezone($displayTimezone);
                                            $endDate = $session->ended_at ? $session->ended_at->setTimezone($displayTimezone) : null;
                                            $startFormatted = $startDate->format('D, M j, Y g:i A');

                                            if ($endDate) {
                                                // If same day, show only time for end date
                                                if ($startDate->format('Y-m-d') === $endDate->format('Y-m-d')) {
                                                    $endFormatted = $endDate->format('g:i A');
                                                } else {
                                                    // Different day, show full date with day of week
                                                    $endFormatted = $endDate->format('D, M j, Y g:i A');
                                                }
                                            } else {
                                                $endFormatted = 'Now';
                                            }
                                        @endphp
                                        {{ $startFormatted }} - {{ $endFormatted }}
                                        @if($session->sessionCategory)
                                            <span class="session-category">{{ $session->sessionCategory->name }}</span>
                                        @endif
                                        @if($session->invoice)
                                            <span class="session-invoice">Invoiced</span>
                                        @else
                                            <span class="session-uninvoiced">Not Invoiced</span>
                                        @endif
                                    </div>
                                    <div class="session-duration">{{ $session->duration_for_humans }}</div>
                                </div>
                                @if($session->comment)
                                    <div class="session-comment">{{ $session->comment }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="font-size: 11px; color: #9ca3af; font-style: italic;">No sessions recorded for this task.</div>
                @endif
            </div>
        @empty
            <div style="text-align: center; padding: 40px; color: #9ca3af;">
                <p>No tasks found for this project.</p>
            </div>
        @endforelse
    </div>

    <!-- Invoice Details -->
    @if($uniqueInvoices->count() > 0)
        <div class="invoice-info">
            <h2 class="section-title">Associated Invoices</h2>
            @foreach($uniqueInvoices as $invoice)
                @php
                    $invoiceSessions = $allSessions->where('invoice_id', $invoice->id);
                    $invoiceHours = $invoiceSessions->sum('duration_in_seconds') / 3600;
                @endphp
                <div style="margin-bottom: 15px; padding: 10px; border: 1px solid #e5e7eb; border-radius: 6px; background-color: #ffffff;">
                    <div style="font-weight: bold; font-size: 12px; color: #1f2937; margin-bottom: 5px;">
                        {{ $invoice->number }}
                        @if(isset($invoice->amount))
                            <span style="float: right; color: #059669;">${{ number_format($invoice->amount / 100, 2) }}</span>
                        @endif
                    </div>
                    <div style="font-size: 11px; color: #6b7280;">
                        {{ number_format($invoiceHours, 1) }} hours • {{ $invoiceSessions->count() }} sessions
                        @if(isset($invoice->created_at))
                            • {{ Carbon\Carbon::parse($invoice->date)->format('M j, Y') }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="footer">
        <p>Generated by YakTrack Client Portal for {{ $clientUser->name }}</p>
        <p>Report Date: {{ now()->setTimezone(env('DISPLAY_TIMEZONE', config('app.timezone', 'UTC')))->format('F j, Y \a\t g:i A T') }}</p>
    </div>
</body>
</html>
