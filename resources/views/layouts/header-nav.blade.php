<nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#"> {{ config('app.name') }} </a>
    <input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search">
    <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
            <form action="{{ url('logout') }}" method="post">
                {{ csrf_field()  }}
                <button class="btn btn-link nav-link" id="logout-button">Sign out</button>
            </form>
        </li>
    </ul>
</nav>