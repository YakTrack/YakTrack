<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>YakTrack</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">
</head>

<body>
    <div class="wrapper" id="app">
        @include('layouts.header-nav')
        <div class="container-fluid">
            <div class="row">
                @include('layouts.sidebar')
                <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                    <br>
                    @yield('breadcrumbs')
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2"> @yield('title') </h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            @yield('top-right-toolbar')
                        </div>
                    </div>
                    @include('layouts.messages')
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <!-- JavaScripts -->
    <script   src="https://code.jquery.com/jquery-2.2.4.js"   integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI="   crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.26/vue.js"></script>
    <script src="/js/app.js"></script>
</body>

</html>
