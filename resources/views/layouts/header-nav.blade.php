<div class="bg-white fixed flex border-b border-grey-lighter pin-t pin-x z-100 h-16 items-center shadow">

    <nav class="flex justify-between flex-wrap bg-white pl-4 py-3 pr-0 h-16 max-w-5xl w-full mx-auto">
        @include('svgs.logo')
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
