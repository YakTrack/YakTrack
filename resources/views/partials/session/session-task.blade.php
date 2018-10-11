@if ($session->task)
    @if ($session->task->project)
        <span class="badge badge-secondary">{{ $session->task->project->name }}</span>
    @endif
    @if ($session->task->parent)
        <span class="badge badge-light">{{ $session->task->parent->name }}</span>
    @endif
    {{ $session->task->name }}
@endif