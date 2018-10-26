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
    <link rel="stylesheet" href="/css/fontawesome.css">
    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">
</head>

<body class="font-source-sans font-normal leading-normal bg-grey-lighter text-grey-darkest">
    <div class="wrapper" id="app">
        @include('layouts.header-nav')
        <div class="w-full max-w-5xl mx-auto px-6">
            <div class="lg:flex -mx-6">
                @include('layouts.sidebar')
                <div id="content-wrapper" class="min-h-screen w-full lg:static lg:max-h-full lg:overflow-visible">
                    <main role="main" class="lg:sticky w-full py-24">
                        @yield('breadcrumbs')
                        <div class="flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                            <div class="flex-1">
                                <h1 class="h2"> @yield('title') </h1>
                            </div>
                            <div class="btn-toolbar mb-2 mb-md-0 flex-1 text-right">
                                @yield('top-right-toolbar')
                            </div>
                        </div>
                        @include('layouts.messages')
                        @yield('content')
                    </main>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScripts -->
    <script src="/js/app.js" async></script>
</body>

</html>
