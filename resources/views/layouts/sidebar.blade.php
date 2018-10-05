<nav class="col-md-2 d-none d-md-block bg-light sidebar">
    <div class="sidebar-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ active()->route('home') }}" href="{{ route('home') }}" >
                    <i class="fas fa-tachometer-alt fa-fw"></i>
                    <span>Home</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ active()->route('session.*') }}" href="{{ route('session.index') }}">
                    <i class="fas fa-stopwatch fa-fw"></i>
                    <span>Sessions</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ active()->route('task.*') }}" href="{{ route('task.index') }}">
                    <i class="fas fa-check-square fa-fw"></i>
                    <span>Tasks</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ active()->route('task.*') }}" href="{{ route('sprint.index') }}">
                    <i class="fas fa-calendar-times fa-fw"></i>
                    <span>Sprints</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ active()->route('task.*') }}" href="{{ route('project.index') }}">
                    <i class="fa fa-briefcase fa-fw"></i>
                    <span>Projects</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ active()->route('client.*') }}" href="{{ route('client.index') }}">
                    <i class="fa fa-users fa-fw"></i>
                    <span>Clients</span>
                </a>
            </li>
        </ul>
    </div>
</nav>