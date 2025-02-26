<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เว็บตารางงาน</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="shortcut icon" type="x-icon" href="{{ asset('img/calendar.png') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        body {
            font-family: "Noto Sans Thai", serif;
            font-weight: 400;
            font-variation-settings: "wdth" 100;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        /* .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(180deg, #343a40, #1d2124);
            color: #fff;
            padding-top: 20px;
            transition: width 0.3s ease-in-out;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar .sidebar-header {
            text-align: center;
            padding: 20px;
            transition: all 0.3s ease-in-out;
        }

        .sidebar .sidebar-header img {
            max-width: 100%;
            height: auto;
            transition: width 0.3s ease-in-out;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            padding: 15px 20px;
            transition: padding 0.3s ease-in-out;
        }

        .sidebar.collapsed ul li {
            padding: 15px 10px;
            text-align: center;
        }

        .sidebar ul li a {
            color: #adb5bd;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: background 0.3s, color 0.3s;
            font-size: 1rem;
        }

        .sidebar ul li a i {
            margin-right: 10px;
            transition: margin-right 0.3s;
        }

        .sidebar.collapsed ul li a i {
            margin-right: 0;
        }

        .sidebar ul li a:hover {
            background-color: #495057;
            color: #ffffff;
        }

        .sidebar ul li a.active {
            background-color: #495057;
            color: #ffffff;
        }

        .sidebar .collapse-button {
            padding: 10px;
            text-align: center;
        }

        .sidebar .cl-toggle {
            position: absolute;
            top: 10px;
            left: 10px;
            cursor: pointer;
            color: #ffffff;
            font-size: 1.5rem;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease-in-out;
            background-color: #f8f9fa;
        }

        .sidebar.collapsed + .content {
            margin-left: 80px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            .sidebar.collapsed {
                width: 60px;
            }
            .content {
                margin-left: 200px;
            }
            .sidebar.collapsed + .content {
                margin-left: 60px;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 100px;
            }
            .sidebar.collapsed {
                width: 50px;
            }
            .content {
                margin-left: 100px;
            }
            .sidebar.collapsed + .content {
                margin-left: 50px;
            }
            .sidebar ul li a {
                font-size: 0.8rem;
            }
        }
            .sidebar-logo {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 20%; /* มุมมนเหมือนหน้า login */
            transition: width 0.3s ease-in-out, height 0.3s ease-in-out;
        }

        /* เมื่อ sidebar ถูกย่อ */
        .sidebar.collapsed .sidebar-logo {
            width: 50px;
            height: 50px;
            border-radius: 20%; /* ยังคงมุมมนเหมือนเดิม */
        } */
    </style>
</head>
<body>
<!-- <div class="sidebar">
    <div class="cl-toggle">
        <i class="fa fa-bars"></i>
    </div>
    <div class="sidebar-header">
    <a href="{{ url('/') }}">
        <img id="sidebar-logo" src="{{ asset('img/calendar.png') }}" alt="Logo" class="sidebar-logo">
    </a>

    </div>

    <div class="collapse-button">
        <button id="sidebar-collapse" class="btn btn-default"><i class="fa fa-angle-left"></i></button>
    </div>
</div> -->

<div class="content">
    <main>
        @yield('content')
    </main>
</div>
<script>
    $(document).ready(function () {
        $('.cl-toggle, #sidebar-collapse').on('click', function () {
            $('.sidebar').toggleClass('collapsed');
            $('.content').toggleClass('expanded');
            
            // Change logo based on sidebar state
            const isCollapsed = $('.sidebar').hasClass('collapsed');
            const logo = $('#sidebar-logo');
            if (isCollapsed) {
                logo.attr('src', '{{ asset('img/calendar.png') }}');
                logo.css('width', '50px'); // Adjust size if needed
            } else {
                logo.attr('src', '{{ asset('img/calendar.png') }}');
                logo.css('width', '100px'); // Adjust size if needed
            }
        });
    });
</script>

</body>
</html>
