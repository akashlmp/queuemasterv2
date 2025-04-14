<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Queuing</title>

    <!-- Prevent FOUC (Flash of Unstyled Content) -->
    <style>
        body {
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .container-fluid {
            padding: 0;
        }

        /* Pagination Styles */
        .pagination-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
        }

        .pagination-wrapper a {
            text-decoration: none;
        }

        .pagination-wrapper nav {
            width: 100%;
        }

        .pagination-wrapper svg {
            width: 20px;
        }

        .pagination-wrapper .flex {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .pagination-wrapper .flex-1 {
            flex: 1;
        }

        .pagination-wrapper .sm\:hidden {
            display: none;
        }

        .pagination-wrapper .sm\:flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .pagination-wrapper .relative {
            align-items: center;
        }

        .pagination-wrapper .relative a:hover {
            background-color: #e9ecef;
        }

        .pagination-wrapper .relative span.active {
            background-color: #007bff;
            color: #fff;
            border-color: #007bff;
        }
    </style>

    <!-- Fonts and Icons -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Preload CSS -->
    <link rel="preload" href="{{ asset('asset/css/signin.css') }}" as="style">
    <link rel="preload" href="{{ asset('asset/css/Home.css') }}" as="style">
    <link rel="preload" href="{{ asset('asset/css/style.css') }}" as="style">
    <link rel="preload" href="{{ asset('asset/css/bootstrap.min.css') }}" as="style">

    <!-- Load CSS after Preload -->
    <link rel="stylesheet" href="{{ asset('asset/css/signin.css') }}">
    <link rel="stylesheet" href="{{ asset('asset/css/Home.css') }}">
    <link rel="stylesheet" href="{{ asset('asset/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('asset/css/bootstrap.min.css') }}">

    <!-- jQuery -->
    <script src="{{ asset('asset/js/jquery.min.js') }}" type="text/javascript"></script>

    <!-- Defer JavaScript -->
    <script src="{{ asset('asset/js/signin.js') }}" type="text/javascript" defer></script>
    <script src="{{ asset('asset/js/main.js') }}" type="text/javascript" defer></script>
    <script src="{{ asset('asset/js/bootstrap.bundle.min.js') }}" type="text/javascript" defer></script>

</head>

<body>
    <!-- Content -->
    <div class="container-fluid p-0" id="main-content">
        @yield('content')
    </div>

    <!-- Prevent FOUC & Show Page -->
    <script type="text/javascript">
        window.onload = function() {
            document.body.style.visibility = 'visible';
            document.body.style.opacity = '1';
        };
    </script>
</body>

</html>
