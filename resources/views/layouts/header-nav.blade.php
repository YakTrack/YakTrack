<div class="bg-white fixed flex border-b border-grey-lighter pin-t pin-x z-100 h-16 items-center shadow">
    {{--
    <nav class="w-full max-w-5xl mx-auto px-6 flex">
        <a class="" href="#"> {{ config('app.name') }} </a>
        <input class="" type="text" placeholder="Search" aria-label="Search">
        <ul class="navbar-nav px-3">
            <li class="nav-item text-nowrap">
                <form action="{{ url('logout') }}" method="post">
                    {{ csrf_field() }}
                    <button class="btn btn-link nav-link" id="logout-button">Sign out</button>
                </form>
            </li>
        </ul>
    </nav> --}}

    <nav class="flex justify-between flex-wrap bg-white py-3 px-0 h-16 max-w-5xl w-full mx-auto">
        <div class="flex-1 items-left flex-no-shrink text-white m-1">
            <span class="text-2xl tracking-tight text-grey-darkest font-thin"> {{ config('app.name') }} </span>
        </div>
        <div class="flex-1">
            <form action="{{ url('logout') }}" method="post" class="float-right">
                {{ csrf_field() }}
                <button class="btn btn-link nav-link" id="logout-button">Log out</button>
            </form>
        </div>
    </nav>

</div>
