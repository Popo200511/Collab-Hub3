<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    @include('sweetalert::alert')

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.2/font/bootstrap-icons.min.css">

    <!-- favicon  -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>


    <!-- Bootstrap Datepicker -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

    <!-- Bootstrap Datepicker JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js">
    </script>

    <link rel="stylesheet" href="">

    <!-- นำเข้า Bootstrap Icons ถ้ายังไม่ได้นำเข้า -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">

    <!-- tailwindcss -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>



<body>

    {{-- ซ่อน navbar เฉพาะหน้า login , home ,user.sda.home , purchase-orders.index --}}
    @if (!request()->routeIs([
        'login',
        'home',
        'user.addjob.home',
        'user.sda.home',
        'purchase-orders.index'
    ]))




    <!-- Navbar -->
    <nav
        class="bg-gradient-to-r from-[rgb(177,230,207)] to-[rgb(182,247,247)] shadow-[rgba(0, 0, 0, 0.24) 0px 3px 8px]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- โลโก้ -->
                <div class="flex items-center">
                    <a href="{{ url('/home') }}">
                        <img src="{{ asset('/GTN.jpg') }}" alt="GTN Logo" class="h-10">
                    </a>
                </div>

                <!-- เมนูหลัก -->
                <div class="hidden md:flex items-center space-x-6">
                    @guest
                    @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-green-600 px-4 py-2 transition">
                        {{ __('Login') }}
                    </a>
                    @endif
                    @else
                    <div class="relative">
                        <button id="user-menu-button" class="text-gray-700 hover:text-green-600 px-4 py-2 transition">
                            {{ Auth::user()->name }}
                        </button>
                        <div id="dropdown-menu" class="hidden absolute mt-2 w-48 bg-white border rounded-lg shadow-lg">
                            <a href="/home"
                                class="block px-4 py-2 text-gray-700 hover:bg-emerald-200">ดูข้อมูลทั้งหมด</a>
                            <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-700 hover:bg-emerald-200"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                ออกจากระบบ
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                    @endguest
                </div>

                <!-- ปุ่มเปิดเมนู (มือถือ) -->
                <div class="md:hidden flex items-center">
                    <button id="menu-toggle" class="text-gray-700 focus:outline-none">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-16 6h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- เมนู (มือถือ) -->
        <div id="mobile-menu" class="md:hidden hidden bg-white border-t shadow-md">
            @guest
            @if (Route::has('login'))
            <a href="{{ route('login') }}" class="block text-gray-700 px-4 py-2 hover:bg-gray-200">Login</a>
            @endif
            @else
            <a href="/home" class="block text-gray-700 px-4 py-2 hover:bg-emerald-200">ดูข้อมูลทั้งหมด</a>
            <a href="{{ route('logout') }}" class="block text-gray-700 px-4 py-2 hover:bg-emerald-200"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                ออกจากระบบ
            </a>
            @endguest
        </div>
    </nav>

    @endif


    <!-- เนื้อหาหลัก -->
    <div class="containor ">
        @yield('content')
    </div>
    </div>


    <!-- JavaScript สำหรับ Dropdown & Mobile Menu -->
    <script>
        // เปิด-ปิดเมนูมือถือ
    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("mobile-menu").classList.toggle("hidden");
    });

    // เปิด-ปิด Dropdown
    document.getElementById("user-menu-button").addEventListener("click", function() {
        document.getElementById("dropdown-menu").classList.toggle("hidden");
    });
    </script>
</body>

</html>