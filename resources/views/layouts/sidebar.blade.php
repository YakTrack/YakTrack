<div id="sidebar" class="hidden absolute z-90 bg-white w-full border-b -mb-16 lg:-mb-0 lg:static lg:bg-transparent lg:border-b-0 lg:pt-0 lg:w-1/4 lg:block lg:border-0 xl:w-1/5">
   <div class="lg:block lg:relative lg:sticky">
        <nav class="pr-6 pt-24 overflow-y-auto text-base lg:text-lg lg:pl-0 lg:pr-8 sticky?lg:h-(screen-16) leading-loose">
            <ul class="list-reset">
                @foreach([
                    [
                        'name' => 'Home',
                        'route' => 'home',
                        'icon' => 'fas fa-tachometer-alt fa-fw'
                    ],
                    [
                        'name' => 'Sessions',
                        'route' => 'session.index',
                        'icon' => 'fas fa-stopwatch fa-fw'
                    ],
                    [
                        'name' => 'Tasks',
                        'route' => 'task.index',
                        'icon' => 'fas fa-check-square fa-fw'
                    ],
                    [
                        'name' => 'Sprints',
                        'route' => 'sprint.index',
                        'icon' => 'fas fa-calendar-times fa-fw'
                    ],
                    [
                        'name' => 'Projects',
                        'route' => 'project.index',
                        'icon' => 'fas fa-briefcase fa-fw'
                    ],
                    [
                        'name' => 'Invoices',
                        'route' => 'invoice.index',
                        'icon' => 'fas fa-file-invoice-dollar fa-fw'
                    ],
                    [
                        'name' => 'Clients',
                        'route' => 'client.index',
                        'icon' => 'fas fa-users fa-fw'
                    ],
                ] as $navLink)
                    <li class="mb-3 lg:mb-2">
                        <a class="no-underline text-grey-darkest hover:text-grey" href="{{ route($navLink['route']) }}">
                            <i class="{{ $navLink['icon'] }} text-grey"></i>
                            <span class=""> {{ $navLink['name'] }} </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
</div>
