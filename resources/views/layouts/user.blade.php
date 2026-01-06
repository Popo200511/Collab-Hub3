<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>

    <!-- Icons / Fonts -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Sarabun', sans-serif;
        }

        .font-sarabun {
            font-family: 'Sarabun', sans-serif !important;
        }

        .swal-title,
        .swal-text {
            font-family: 'Sarabun', sans-serif;
        }
    </style>

</head>

<body class="bg-gray-100">

    <div class="flex min-h-screen">

        <aside class="w-64 bg-blue-950 text-white flex flex-col p-2 space-y-1">

            <div
                class="flex justify-center items-center py-2 mx-3 mt-2 
            bg-white/10 rounded-xl backdrop-blur-sm shadow-sm">
                <h2 class="text-xl tracking-wide font-extrabold text-rose-500 tracking-wider uppercase">
                    Collab <span class="text-blue-700">Hub</span>
                </h2>
            </div>

            <div class="mt-4 space-y-4 overflow-y-auto h-[calc(98vh-120px)] pr-1">

                <a href="{{ route('home') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-md transition
                    @if (Request::is('billing*')) bg-white text-blue-800 shadow-md @else hover:bg-white hover:text-blue-800 @endif">
                    <i class="fas fa-home"></i>
                    <span class="text-base font-sarabun whitespace-nowrap">Home</span>
                </a>


                <!-- PR Dropdown -->
                <div x-data="{ open: false }" class="flex flex-col">
                    <!-- ปุ่ม PR -->
                    <button type="button" @click="open = !open"
                        class="flex items-center justify-between gap-2 px-3 py-2 rounded-md transition w-full
               hover:bg-white hover:text-blue-800">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-database"></i>
                            <span class="text-base font-sarabun ">Project Database</span>
                        </div>
                        <i :class="open ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'" class="text-white"></i>
                    </button>

                    <!-- เมนูย่อย -->
                    <div x-show="open || window.location.pathname.startsWith('/pr')" x-transition
                        class="flex flex-col pl-8 mt-2 space-y-1">


                        <!-- Project View -->
                        @if ($showProjectView16)
                            <a href="{{ route('project.projectview') }}"
                                class="px-3 py-1 rounded-md transition font-sarabun
       {{ Route::currentRouteName() === 'pr.purchase' ? 'bg-white text-blue-800' : 'text-blue hover:bg-white hover:text-blue-800' }}">
                                Project View 16
                            </a>
                        @endif


                    </div>
                </div>


                <!-- ERP -->
                <div x-data="{ open: false }" class="flex flex-col">
                    <!-- ปุ่ม PR -->
                    <button type="button" @click="open = !open"
                        class="flex items-center justify-between gap-2 px-3 py-2 rounded-md transition w-full
               hover:bg-white hover:text-blue-800">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-building"></i>
                            <span class="text-base font-sarabun ">ERP</span>
                        </div>
                        <i :class="open ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'" class="text-white"></i>
                    </button>

                    <!-- เมนูย่อย -->
                    <div x-show="open || window.location.pathname.startsWith('/pr')" x-transition
                        class="flex flex-col pl-8 mt-2 space-y-1">

                        <!-- Refcode -->
                        @if (Auth::check())
                            <a href="refcode/home"
                                class="px-3 py-1 rounded-md transition font-sarabun {{ Route::currentRouteName() === 'pr.home' ? 'bg-white text-blue-800' : 'text-blue hover:bg-white hover:text-blue-800' }}">
                                Refcode
                            </a>

                            <!-- Billing
                        <a href="billing/home"
                            class="px-3 py-1 rounded-md transition font-sarabun {{ Route::currentRouteName() === 'pr.purchase' ? 'bg-white text-blue-800' : 'text-blue hover:bg-white hover:text-blue-800' }}">
                            Billing

                        </a>

                        <a href="/import"
                            class="px-3 py-1 rounded-md transition font-sarabun {{ Route::currentRouteName() === 'pr.purchase' ? 'bg-white text-blue-800' : 'text-blue hover:bg-white hover:text-blue-800' }}">
                            Inventory

                        </a>
-->
                        @endif
                    </div>
                </div>


                <div x-data="{ open: false }" class="flex flex-col">
                    <!-- ปุ่ม IT Support -->
                    <button type="button" @click="open = !open"
                        class="flex items-center justify-between gap-2 px-3 py-2 rounded-md transition w-full
               hover:bg-white hover:text-blue-800">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-headset"></i>
                            <span class="text-base font-sarabun ">IT Support</span>
                        </div>
                        <i :class="open ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'" class="text-white"></i>
                    </button>

                    <!-- เมนูย่อย -->
                    <div x-show="open || window.location.pathname.startsWith('/pr')" x-transition
                        class="flex flex-col pl-8 mt-2 space-y-1">

                        <!-- Add Member -->
                        <a href="https://sites.google.com/team-gtn.com/it-clinic/home"
                            class="px-3 py-1 rounded-md transition font-sarabun {{ Route::currentRouteName() === 'pr.home' ? 'bg-white text-blue-800' : 'text-blue hover:bg-white hover:text-blue-800' }}">
                            IT Clinic
                        </a>

                        <!-- Member Total -->
                        <a href="https://drive.google.com/drive/u/0/folders/1EEtlhGBVFtDj0f2nsi-5eOO26dTw6WsS"
                            class="px-3 py-1 rounded-md transition font-sarabun {{ Route::currentRouteName() === 'pr.purchase' ? 'bg-white text-blue-800' : 'text-blue hover:bg-white hover:text-blue-800' }}">
                            Report

                        </a>

                        @php
                            $isAuthorized = Auth::check() && Auth::user()->status == 'Admin';
                        @endphp

                        <a href="{{ $isAuthorized ? 'https://onedrive.live.com/:x:/g/personal/83EA148C542F6F94/EZRvL1SMFOoggIOW3wAAAAABlgZcLYR_-c6XGPd8omyOUA?resid=83EA148C542F6F94!57238&ithint=file%2Cxlsx&e=4%3Af683edb2bd394b05a4823a9a2d7762b8&sharingv2=true&fromShare=true&at=9&migratedtospo=true&redeem=aHR0cHM6Ly8xZHJ2Lm1zL3gvYy84M0VBMTQ4QzU0MkY2Rjk0L0VaUnZMMVNNRk9vZ2dJT1czd0FBQUFBQmxnWmNMWVJfLWM2WEdQZDhvbXlPVUE_ZT00OmY2ODNlZGIyYmQzOTRiMDVhNDgyM2E5YTJkNzc2MmI4JnNoYXJpbmd2Mj10cnVlJmZyb21TaGFyZT10cnVlJmF0PTk' : '#' }}"
                            target="blank" class="px-3 py-1 rounded-md transition font-sarabun {{ $isAuthorized ? 'target=_blank' : 'onclick=event.preventDefault();' }} {{ Route::currentRouteName() === 'pr.purchase' ? 'bg-white text-blue-800' : 'text-blue hover:bg-white hover:text-blue-800' }}">
                            Databases
                        </a>
                    </div>
                </div>


                <div x-data="{ open: false }" class="flex flex-col">
                    <!-- ปุ่ม IT Support -->
                    <button type="button" @click="open = !open"
                        class="flex items-center justify-between gap-2 px-3 py-2 rounded-md transition w-full
               hover:bg-white hover:text-blue-800">
                        <div class="flex items-center gap-2">
                            <i class="fa-regular fa-user"></i>
                            <span class="text-base font-sarabun">Admin</span>
                        </div>
                        <i :class="open ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'" class="text-white"></i>
                    </button>

                    <!-- เมนูย่อย -->
                    <div x-show="open || window.location.pathname.startsWith('/pr')" x-transition
                        class="flex flex-col pl-8 mt-2 space-y-1">

                        @php
                            $isAuthorized = Auth::check() && Auth::user()->status == 'Admin';
                        @endphp

                        @if ($isAuthorized)
                            <!-- Add Member -->
                            <a href="{{ route('sda.register') }}"
                                class="px-3 py-1 rounded-md transition font-sarabun
       {{ Route::currentRouteName() === 'pr.purchase'
           ? 'bg-white text-blue-800'
           : 'text-blue hover:bg-white hover:text-blue-800' }}">
                                Add Member
                            </a>
                        @endif


                        <!-- Member Total
                        <a href="#"
                            class="px-3 py-1 rounded-md transition font-sarabun {{ Route::currentRouteName() === 'pr.purchase' ? 'bg-white text-blue-800' : 'text-blue hover:bg-white hover:text-blue-800' }}">
                            Member Total
                        </a>
 -->
                    </div>
                </div>


                <div x-data="{ open: false }" class="flex flex-col">
                    <!-- ปุ่ม IT Support -->
                    <button type="button" @click="open = !open"
                        class="flex items-center justify-between gap-2 px-3 py-2 rounded-md transition w-full
               hover:bg-white hover:text-blue-800">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-briefcase"></i>
                            <span class="text-base font-sarabun ">New Job Assignment</span>
                        </div>
                        <i :class="open ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'" class="text-white"></i>
                    </button>

                    <!-- เมนูย่อย -->
                    <div x-show="open || window.location.pathname.startsWith('/pr')" x-transition
                        class="flex flex-col pl-8 mt-2 space-y-1">

                        @php
                            $isAuthorized = Auth::check() && Auth::user()->status == 'Admin';
                        @endphp

                        <!-- Add Member -->
                        <a href="{{ $isAuthorized ? route('user.sda.home') : route('addjob.user') }}" target="_blank"
                            class="px-3 py-1 rounded-md transition font-sarabun {{ Route::currentRouteName() === 'newjobassignment.addjob' ? 'bg-white text-blue-800' : 'text-blue hover:bg-white hover:text-blue-800' }}">
                            Add Job
                        </a>


                        <!-- PO -->
                        <a href="PO/purchase"
                            class="px-3 py-1 rounded-md transition font-sarabun {{ Route::currentRouteName() === 'newjobassignment.addjob' ? 'bg-white text-blue-800' : 'text-blue hover:bg-white hover:text-blue-800' }}">
                            PO
                        </a>


                    </div>
                </div>

            </div>

            <!-- ปุ่มออกจากระบบ -->
            <div class="mt-auto px-2 pb-4 mt-2">
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="flex items-center justify-center gap-2 w-full 
               bg-red-600 text-white font-sarabun py-2 rounded-lg shadow-md
               transition transform hover:bg-red-700 hover:scale-[1.02]">
                    <i class="fas fa-sign-out-alt"></i>
                    ออกจากระบบ
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>

        </aside>

        {{-- ===== Main Content ===== --}}
        <main class="flex-1 overflow-auto">
            @yield('content')
        </main>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.12.0/cdn.min.js" defer></script>
</body>

</html>