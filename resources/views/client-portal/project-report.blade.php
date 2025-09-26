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
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $project->name }}</h1>
        <p><strong>Project Report</strong></p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
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
                                        {{ $session->started_at->format('M j, Y g:i A') }} - {{ $session->ended_at ? $session->ended_at->format('g:i A') : 'Now' }}
                                        @if($session->sessionCategory)
                                            <span class="session-category">{{ $session->sessionCategory->name }}</span>
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

    <div class="footer">
        <p>Generated by YakTrack Client Portal for {{ $clientUser->name }}</p>
        <p>Report Date: {{ now()->format('F j, Y \a\t g:i A T') }}</p>
    </div>
</body>
</html>