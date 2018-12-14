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
        @yield('page')
    </div>

    <!-- JavaScripts -->
    <script src="/js/app.js" async></script>
</body>

</html>
