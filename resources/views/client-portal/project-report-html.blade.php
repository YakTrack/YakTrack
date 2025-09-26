<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $project->name }} - Project Report</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f8fafc;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .header {
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .header h1 {
            font-size: 28px;
            margin: 0 0 10px 0;
        }
        .header p {
            margin: 5px 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .project-info {
            background-color: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-item {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            display: block;
        }
        .stat-label {
            font-size: 12px;
            margin-top: 5px;
            opacity: 0.9;
        }
        .invoice-summary {
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .invoice-summary h3 {
            margin: 0 0 15px 0;
            font-size: 18px;
            color: #8b5cf6;
        }
        .invoice-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        .invoice-stat {
            text-align: center;
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 6px;
        }
        .invoice-stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
        }
        .invoice-stat-label {
            font-size: 12px;
            color: #6b7280;
            margin-top: 5px;
        }
        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }
        .task-item {
            margin-bottom: 25px;
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .task-name {
            font-weight: bold;
            font-size: 16px;
            color: #1f2937;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            color: white;
            white-space: nowrap;
        }
        .task-stats {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 20px;
        }
        .sessions-list {
            margin-top: 15px;
        }
        .session-item {
            padding: 15px;
            margin-bottom: 10px;
            background-color: #f9fafb;
            border-radius: 6px;
            border-left: 4px solid #3b82f6;
        }
        .session-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .session-date {
            font-size: 13px;
            color: #1f2937;
            font-weight: 500;
        }
        .session-duration {
            font-size: 13px;
            color: #6b7280;
        }
        .session-category {
            display: inline-block;
            background-color: #e5e7eb;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            color: #374151;
            margin-left: 10px;
        }
        .session-invoice {
            font-size: 11px;
            color: #059669;
            margin-left: 10px;
            font-weight: bold;
            background-color: #d1fae5;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .session-uninvoiced {
            font-size: 11px;
            color: #059669;
            margin-left: 10px;
            font-weight: bold;
            background-color: #fef3c7;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .session-comment {
            margin-top: 8px;
            font-size: 13px;
            color: #4b5563;
            font-style: italic;
            padding-left: 15px;
            border-left: 3px solid #d1d5db;
        }
        .invoice-info {
            margin-top: 30px;
            padding: 25px;
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            border-radius: 8px;
        }
        .invoice-item {
            margin-bottom: 15px;
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            background-color: rgba(255, 255, 255, 0.9);
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
            font-size: 14px;
            color: #1f2937;
            margin-bottom: 5px;
        }
        .invoice-details {
            font-size: 12px;
            color: #6b7280;
        }
        .footer {
            margin-top: 40px;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            background-color: #f9fafb;
        }
        .actions {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-secondary {
            background-color: white;
            color: #374151;
            border: 1px solid #d1d5db;
        }
        @media print {
            .actions { display: none; }
            body { padding: 0; }
            .container { box-shadow: none; }
        }
        @media (max-width: 768px) {
            .task-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .session-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
            .actions {
                position: static;
                justify-content: center;
                margin-top: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $project->name }}</h1>
            <p><strong>Project Report</strong></p>
            <p>Generated on {{ now()->setTimezone(env('DISPLAY_TIMEZONE', config('app.timezone', 'UTC')))->format('F j, Y \a\t g:i A T') }}</p>
            @if($project->description)
                <p>{{ $project->description }}</p>
            @endif
        </div>

        <div class="content">
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
                            <div>
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
                                <strong>Work Sessions:</strong>
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
                                                        if ($startDate->format('Y-m-d') === $endDate->format('Y-m-d')) {
                                                            $endFormatted = $endDate->format('g:i A');
                                                        } else {
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
                            <div style="text-align: center; padding: 20px; color: #9ca3af; font-style: italic;">
                                No sessions recorded for this task.
                            </div>
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
                        <div class="invoice-item">
                            <div class="invoice-header">
                                <span>{{ $invoice->number }}</span>
                                @if(isset($invoice->amount))
                                    <span style="color: #059669;">${{ number_format($invoice->amount / 100, 2) }}</span>
                                @endif
                            </div>
                            <div class="invoice-details">
                                {{ number_format($invoiceHours, 1) }} hours • {{ $invoiceSessions->count() }} sessions
                                @if(isset($invoice->date))
                                    • {{ Carbon\Carbon::parse($invoice->date)->format('M j, Y') }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="footer">
            <p>Generated by YakTrack Client Portal for {{ $clientUser->name }}</p>
            <p>Report Date: {{ now()->setTimezone(env('DISPLAY_TIMEZONE', config('app.timezone', 'UTC')))->format('F j, Y \a\t g:i A T') }}</p>
        </div>
    </div>

    <div class="actions">
        <a href="{{ route('client-portal.projects.report', $project) }}" class="btn btn-primary">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 16l-4-4h3V4h2v8h3l-4 4zm9-13v12a2 2 0 01-2 2H5a2 2 0 01-2-2V3a2 2 0 012-2h14a2 2 0 012 2z"/>
            </svg>
            Download PDF
        </a>
        <a href="{{ route('client-portal.projects.show', $project) }}" class="btn btn-secondary">
            ← Back to Project
        </a>
    </div>
</body>
</html>
