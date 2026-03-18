@extends('layouts.user')

@section('title', '85 True Site Preparation')


@section('content')
    <!-- Export To Excel -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <!-- sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">
    <!-- Load Font Awesome for Icons -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">

    <!-- แสดงข้อความ error -->
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- แสดงข้อความสำเร็จ -->
    @if (session('success'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#22c55e',
                    customClass: {
                        title: 'swal-title',
                        content: 'swal-text'
                    }
                });
            });
        </script>
    @endif

    <!--แปลงเป็น Editable-->

    <style>
        /* Input field แบบ Excel */
        .excel-input {
            width: 100%;
            min-width: 100px;
            /* ให้ cell แสดงเต็มความยาวที่ต้องการ */

            background: transparent;
            /* พื้นหลังใส */
            font-size: 10px;
            text-align: center;
            box-sizing: border-box;
            /* รวม padding + border ใน width */
            white-space: nowrap;
            /* อยู่บรรทัดเดียว */

            text-overflow: ellipsis;
            /* แสดง ... ถ้ายาวเกิน */
            transition: all 0.2s;
            /* effect เวลา focus */
        }

        /* Focus / editable state */
        .excel-input:focus,
        .excel-input.active-hover {
            outline: 1px solid #3b82f6;
            /* สีฟ้า */
            background: #eef6ff;
            /* ฟีล Excel */
            border-radius: 10%;
            /* มุมโค้งเล็กน้อย */
        }

        /* Readonly cell */
        .readonly-cell {
            background-color: #f5f5f5;
            /* เทาอ่อน */
            cursor: not-allowed;
        }
    </style>

    <style>
        .swal-title,
        .swal-text {
            font-family: 'Sarabun', sans-serif;
        }

        .readonly-cell {
            background-color: transparent !important;
            color: #000;
        }
    </style>

    <style>
        /* input เงิน */
        .money-input {
            text-align: right;
        }

        /* readonly แต่ไม่เอาพื้นหลังเทา */
        input[readonly] {
            background-color: transparent !important;
            border: none;
            box-shadow: none;
            cursor: default;
        }

        /* กัน disabled ทำให้สีจาง */
        input[disabled] {
            background-color: transparent !important;
            color: inherit;
            opacity: 1;
        }
    </style>

    <!-- Hover สำหรับ Filter -->
    <style>
        .filter-active i {
            color: #60a5fa !important;
        }

        thead th:hover .filter-icon:not(.filter-active) i {
            color: #93c5fd;
        }

        .font-sarabun {
            font-family: 'Sarabun', sans-serif !important;
        }
    </style>


    <!-- collab new Project-->
    <script>
        function parseMoney(val) {
            if (!val) return 0;
            return parseFloat(val.replace(/,/g, '')) || 0;
        }

        function formatMoney(num) {
            return num.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        /* ===============================
           Store original value
        ================================ */
        $(document).on('focus', '.money-input', function() {
            $(this).data('original', $(this).val());
        });

        /* ===============================
           Allow only number + dot
           (ไม่แตะ value → cursor ไม่ดีด)
        ================================ */
        $(document).on('beforeinput', '.money-input', function(e) {
            if (e.data && !/[\d.]/.test(e.data)) {
                e.preventDefault();
            }
        });

        /* ===============================
           Realtime calculation ONLY
           (ห้าม set value ช่องที่พิมพ์)
        ================================ */
        $(document).on('input', '.money-input', function() {
            let row = $(this).closest('tr');

            // 🔹 1. อ่าน raw value ก่อน (ยังไม่ parse)
            let revenueRaw = row.find('[data-field="Estimated_Revenue_PJ"]').val();
            let serviceRaw = row.find('[data-field="Estimated_Service_Cost_PJ"]').val();
            let materialRaw = row.find('[data-field="Estimated_Material_Cost_PJ"]').val();
            let transportationRaw = row.find('[data-field="Estimated_Transportation_Cost_PJ"]').val();
            let otherRaw = row.find('[data-field="Estimated_Other_Cost_PJ"]').val();

            // 🔹 2. ถ้ายังพิมพ์ไม่จบ → STOP
            let incomplete = [revenueRaw, serviceRaw, materialRaw, transportationRaw, otherRaw].some(v =>
                v === '.' || (v && v.endsWith('.'))
            );

            if (incomplete) {
                return; // ❌ ยังไม่คำนวณ
            }

            // 🔹 3. ค่อย parse เมื่อค่าพร้อม
            let revenue = parseMoney(revenueRaw);
            let service = parseMoney(serviceRaw);
            let material = parseMoney(materialRaw);
            let transportation = parseMoney(transportationRaw);
            let other = parseMoney(otherRaw);

            let grossProfit = revenue - service - material - transportation - other;
            let grossMargin = revenue !== 0 ? (grossProfit / revenue) * 100 : 0;

            row.find('.gross-profit').val(formatMoney(grossProfit));
            row.find('.gross-margin').val(formatMoney(grossMargin) + '%');
        });


        /* ===============================
           Blur → validate + format
        ================================ */
        $(document).on('blur', '.money-input', function() {
            let field = $(this).data('field');
            let raw = $(this).val().replace(/,/g, '').trim();
            let old = $(this).data('original') ?? '';

            /* ---------- Revenue rule ---------- */
            if (field === 'Estimated_Revenue_PJ') {
                if (raw === '' || isNaN(raw) || parseFloat(raw) === 0) {
                    // revert ค่าเดิม
                    $(this).val(old);
                    return;
                }
            }

            /* ---------- Other money fields ---------- */
            if (raw === '' || isNaN(raw)) {
                $(this).val(old);
                return;
            }

            let num = parseFloat(raw);
            $(this).val(formatMoney(num));

            // update original ใหม่
            $(this).data('original', $(this).val());
        });
    </script>


    <!-- collab 85 calculation-->
    <script>
        /* ===============================
                               Calculate col27 = col22 - col24 - col26
                               (Realtime / Safe cursor)
                            ================================ */
        $(document).on('input', '.money-input', function() {
            let row = $(this).closest('tr');

            let col22Raw = row.find('[data-field="col22"]').val();
            let col24Raw = row.find('[data-field="col24"]').val();
            let col26Raw = row.find('[data-field="col26"]').val();

            /* 🔹 ถ้ายังพิมพ์ไม่จบ → STOP */
            let incomplete = [col22Raw, col24Raw, col26Raw].some(v =>
                v === '.' || (v && v.endsWith('.'))
            );

            if (incomplete) {
                return;
            }

            /* 🔹 parse เมื่อค่าพร้อม */
            let col22 = parseMoney(col22Raw);
            let col24 = parseMoney(col24Raw);
            let col26 = parseMoney(col26Raw);

            let result = col22 - col24 - col26;

            let target = row.find('[data-field="col27"]');
            if (target.length) {
                target.val(formatMoney(result));
            }
        });
    </script>



    <!-- Main Content -->
    <main class="flex-1 bg-gray-100 overflow-y-auto">

        <div class="flex justify-between items-center bg-white p-4 rounded-xl mb-5 shadow-md ">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 w-full items-stretch">

                <!-- Summary -->
                <div class="bg-white p-2 rounded-xl shadow-md min-h-[60px]">
                    <h3 class="text-sm font-sarabun text-gray-500 mb-2">Added Job Total</h3>
                    <div class="text-4xl font-sarabun text-blue-600 text-center">83</div>
                    <div class="text-sm text-gray-500 mt-1 text-center">
                        Completed: <span class="font-sarabun">90</span>
                    </div>
                </div>

                <!-- Reject -->
                <div class="bg-white p-2 rounded-xl shadow-md min-h-[60px]">
                    <h3 class="text-sm font-sarabun text-gray-500 mb-2">Reject</h3>
                    <div class="text-4xl font-sarabun text-red-600 text-center">83</div>
                </div>

                <!-- Pending -->
                <div class="bg-white p-2 rounded-xl shadow-md min-h-[60px]">
                    <h3 class="text-sm font-sarabun text-gray-500 mb-2 ">Pending</h3>
                    <div class="text-4xl font-sarabun text-orange-400 text-center">83</div>
                </div>

                <!-- Approved -->
                <div class="bg-white p-2 rounded-xl shadow-md min-h-[60px]">
                    <h3 class="text-sm font-sarabun text-gray-500 mb-2 ">Approved</h3>
                    <div class="text-4xl font-sarabun text-green-600 text-center">83</div>
                </div>

            </div>


        </div>


        <div class="bg-white p-4 rounded-xl shadow-md">

            <!-- Container ปุ่ม -->
            <div class="container mx-auto mb-2">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">

                    {{-- 1. ปุ่ม Export (ขวาใน Desktop) --}}
                    <div class="order-1 sm:order-2">
                        <button type="button" id="exportToExcel" onclick="exportToExcel()"
                            class="px-3 py-1.5 rounded-md font-sarabun text-sm text-white
                bg-gradient-to-r from-green-600 to-green-500
                shadow hover:shadow-md hover:scale-[1.02] transition-all">
                            <i class="fas fa-file-excel mr-2 text-lg"></i>
                            Export Visible Data
                        </button>
                    </div>

                    {{-- 2. ปุ่ม Permission (ซ้ายใน Desktop) --}}
                    @php
                        $isAdmin = Auth::check() && Auth::user()->status === 'Admin';
                    @endphp

                    @if ($isAdmin)
                        <div class="order-2 sm:order-1 flex items-center gap-3">
                            <button onclick="openPermissionModal()"
                                class="group flex items-center px-3 py-1.5 bg-white text-indigo-600 border border-indigo-200
    font-bold text-sm rounded-xl shadow-[0_2px_10px_-3px_rgba(79,70,229,0.2)]
    hover:bg-indigo-600 hover:text-white hover:border-indigo-600
    hover:shadow-[0_10px_20px_-5px_rgba(79,70,229,0.4)]
    hover:-translate-y-0.5 active:scale-95 active:translate-y-0
    transition-all duration-300 ease-out">


                                <i
                                    class="fas fa-user-shield mr-2.5 text-base transition-transform duration-300 group-hover:rotate-12"></i>
                                <span class="tracking-wide">Permission</span>
                            </button>
                        </div>
                    @endif

                </div>
            </div>

            <style>
                .sticky-col-1 {
                    position: sticky;
                    left: 0;
                    width: 160px;
                    z-index: 50;
                    background: white;
                }

                .sticky-col-2 {
                    position: sticky;
                    left: 184px;
                    width: 120px;
                    z-index: 50;
                    background: white;
                }

                .sticky-col-3 {
                    position: sticky;
                    left: 322px;
                    width: 130px;
                    z-index: 50;
                    background: white;
                }

                /* Header ต้องสูงกว่า */
                .sticky-header {
                    z-index: 120;
                    background: #172554;
                    /* blue-950 */
                }
            </style>

            <!-- Modal Manage Permissions 85-->
            <form action="{{ route('permissions.save_85', $projectCode) }}" method="POST">

                @csrf

                <input type="hidden" name="project_code" value="{{ $projectCode }}">


                <div id="permissionModal"
                    class="fixed inset-0 z-[500] hidden bg-black bg-opacity-50 flex items-center justify-center ">
                    <div class="bg-white w-full max-w-[1200px] h-[80vh] rounded-xl shadow-lg overflow-hidden flex flex-col">

                        <!-- Header -->
                        <div class="flex justify-between items-center p-4 border-b border-gray-300">
                            <h6 class="text-lg font-bold">Manage Permissions</h6>
                            <button type="button"
                                onclick="document.getElementById('permissionModal').classList.add('hidden')"
                                class="text-gray-500 hover:text-gray-800">&times;</button>

                        </div>

                        <!-- Body -->
                        <div class="overflow-auto flex-1 bg-gray-50 rounded-md max-h-[500px]">
                            <div style="width: 7500px;">
                                <table id="permissionTable"
                                    class="w-full border-separate border-spacing-0 text-center text-xs font-sarabun">

                                    <thead class="bg-blue-950 text-white">
                                        <tr class="h-8 text-xs">

                                            <!-- User ถึง  Project Role-->

                                            <th class="sticky top-0 sticky-col-1 sticky-header border px-1 group">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>User</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="0">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 sticky-col-2 sticky-header border px-1 group">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Project Member</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="1">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 sticky-col-3 sticky-header border px-1 group">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Project Role</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="2">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Customer Region ถึง  Estimated Other Cost สี green-->

                                            <th class="sticky top-0 border px-1 w-[120px]" style="background-color: green">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Customer<br>Region</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="3">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px]" style="background-color: green">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Estimated<br>Revenue</span>

                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="4">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px]" style="background-color: green">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Estimated<br>Service Cost</span>

                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="5">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px]"
                                                style="background-color: green">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Estimated<br> Material Cost</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="6">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[130px]"
                                                style="background-color: green">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Estimated<br>Transportation Cost</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="7">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px]"
                                                style="background-color: green">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Estimated<br>Other Cost</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="8">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Estimated Gross Profit ถึง  Estimated Gross Profit Margin  --- Read / invisible สี blue-->

                                            <th class="sticky top-0 border px-1 w-[120px]" style="background-color: blue">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Estimated<br>Gross Profit</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="9">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px]" style="background-color: blue">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Estimated<br>Gross Profit Margin</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="10">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Swap Code ถึง Upload BOQ  ----------------- Read / invisible สี blue-->

                                            <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Swap Code</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="11">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Survey (Plan)</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="12">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Survey (Actual)</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="13">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Go / Nogo</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="14">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Material<br>Ordered Date</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="15">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Material<br>Ready Date</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="16">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-sky-500 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Material<br>Withdrawn Date</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="17">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Material<br>Received Date</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="18">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Material - Actual Price</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="19">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Install Date (Plan)</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="20">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Install Date (Actual)</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="21">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Platform Type</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="22">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Project ID</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="23">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Current Step</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="24">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Ref No.</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="25">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>WO No. PROMs</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="26">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>FBOQ Price</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="27">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>PO No. PROMs</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="28">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Upload BOQ</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="29">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Revenue ถึง PO Remaining สี green-->

                                            <th class="sticky top-0 border px-1 w-[100px] bg-green-600 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Revenue</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="30">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-green-600 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>PO No. Actual</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="31">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-green-600 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>PO Amount Actual</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="32">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-green-600 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>INV 1st No.</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="33">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-green-600 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>INV 1st Price</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="34">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-green-600 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>INV 2nd No.</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="35">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-green-600 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>INV 2nd Price</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="36">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[100px] bg-green-600 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>PO Remaining</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="37">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- PR Proposed date (Email) ถึง Billing2 Approved Date (Email) สี blue-->

                                            <th class="sticky top-0 border px-1 w-[120px] bg-blue-850 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>PR Proposed date (Email)</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="38">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-blue-850 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>PR Approved Date (Email)</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="39">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-blue-850 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Billing1 Proposed <br> date (Email)</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="40">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-blue-850 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Billing1 Approved<br>Date (Email)</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="41">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-blue-850 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Billing2 Proposed <br> date (Email)</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="42">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-blue-850 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Billing2 Approved<br>Date (Email)</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="43">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>


                                            <!------------------------------------- Col34-Col50 ---------------------------------------->

                                            <!-- Col34 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col34</span>
                                                    <span class="filter-icon cursor-pointer" data-col="44"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col35 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col35</span>
                                                    <span class="filter-icon cursor-pointer" data-col="45"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col36 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col36</span>
                                                    <span class="filter-icon cursor-pointer" data-col="46"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col37 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col37</span>
                                                    <span class="filter-icon cursor-pointer" data-col="47"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col38 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col38</span>
                                                    <span class="filter-icon cursor-pointer" data-col="48"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col39 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col39</span>
                                                    <span class="filter-icon cursor-pointer" data-col="49"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col40 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col40</span>
                                                    <span class="filter-icon cursor-pointer" data-col="50"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col41 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col41</span>
                                                    <span class="filter-icon cursor-pointer" data-col="51"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col42 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col42</span>
                                                    <span class="filter-icon cursor-pointer" data-col="52"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col43 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col43</span>
                                                    <span class="filter-icon cursor-pointer" data-col="53"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col44 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col44</span>
                                                    <span class="filter-icon cursor-pointer" data-col="54"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col45 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col45</span>
                                                    <span class="filter-icon cursor-pointer" data-col="55"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col46 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col46</span>
                                                    <span class="filter-icon cursor-pointer" data-col="56"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col47 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col47</span>
                                                    <span class="filter-icon cursor-pointer" data-col="57"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col48 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col48</span>
                                                    <span class="filter-icon cursor-pointer" data-col="58"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col49 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col49</span>
                                                    <span class="filter-icon cursor-pointer" data-col="59"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col50 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col50</span>
                                                    <span class="filter-icon cursor-pointer" data-col="60"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                        </tr>
                                    </thead>


                                    <tbody id="permissionTableBody">
                                        @foreach ($users as $user)
                                            <tr
                                                class="group h-8 text-xs odd:bg-white even:bg-gray-50 hover:bg-red-100 border-b border-gray-200 transition table-row-divider">

                                                <!-- User -->
                                                <td data-col="0"
                                                    class="sticky-col-1 z-[40] bg-white group-hover:bg-red-100 px-2 transition">
                                                    {{ $user->name }}
                                                </td>

                                                <!-- Project Member -->
                                                <td data-col="1"
                                                    class="sticky-col-2 z-[40] group-hover:bg-red-100 px-2 transition">
                                                    <select name="member_status[{{ $user->id }}]"
                                                        class="text-xs p-1 border rounded w-full bg-white">


                                                        <option value="no"
                                                            {{ isset($permissions[$user->id]) && $permissions[$user->id]->member_status === 'no' ? 'selected' : '' }}>
                                                            No
                                                        </option>

                                                        <option value="yes"
                                                            {{ isset($permissions[$user->id]) && $permissions[$user->id]->member_status === 'yes' ? 'selected' : '' }}>
                                                            Yes
                                                        </option>

                                                    </select>
                                                </td>


                                                <!-- Project Role -->
                                                <td data-col="2"
                                                    class="sticky-col-3 z-[40] group-hover:bg-red-100 px-2 transition">
                                                    <select name="project_role[{{ $user->id }}]"
                                                        class="text-xs p-1 border rounded w-full bg-white hover:bg-gray-50 font-medium project-role"
                                                        data-user-id="{{ $user->id }}">

                                                        <!-- ค่าเริ่มต้น No -->
                                                        <option value=""
                                                            {{ !isset($permissions[$user->id]) || $permissions[$user->id]->project_role === null ? 'selected' : '' }}>
                                                            No
                                                        </option>

                                                        <option value="Project Manager"
                                                            {{ isset($permissions[$user->id]) && $permissions[$user->id]->project_role === 'Project Manager'
                                                                ? 'selected'
                                                                : '' }}>
                                                            Project Manager
                                                        </option>

                                                        <option value="Project Admin"
                                                            {{ isset($permissions[$user->id]) && $permissions[$user->id]->project_role === 'Project Admin'
                                                                ? 'selected'
                                                                : '' }}>
                                                            Project Admin
                                                        </option>

                                                        <option value="Site Supervisor"
                                                            {{ isset($permissions[$user->id]) && $permissions[$user->id]->project_role === 'Site Supervisor'
                                                                ? 'selected'
                                                                : '' }}>
                                                            Site Supervisor
                                                        </option>

                                                        <option value="Project Director"
                                                            {{ isset($permissions[$user->id]) && $permissions[$user->id]->project_role === 'Project Director'
                                                                ? 'selected'
                                                                : '' }}>
                                                            Project Director
                                                        </option>
                                                    </select>
                                                </td>



                                                {{-- ===== Project-level permissions ===== --}}
                                                @php
                                                    $projectFields = [
                                                        'Customer_Region_PJ',
                                                        'Estimated_Revenue_PJ',
                                                        'Estimated_Service_Cost_PJ',
                                                        'Estimated_Material_Cost_PJ',
                                                        'Estimated_Transportation_Cost_PJ',
                                                        'Estimated_Other_Cost_PJ',

                                                        // 🔥 2 อันใหม่ (read / invisible เท่านั้น)
                                                        'Estimated_Gross_Profit_PJ',
                                                        'Estimated_Gross_Profit_Margin_PJ',
                                                    ];

                                                    // field ที่ห้าม write
                                                    $readOnlyFields = [
                                                        'Estimated_Gross_Profit_PJ',
                                                        'Estimated_Gross_Profit_Margin_PJ',
                                                    ];
                                                @endphp

                                                @foreach ($projectFields as $index => $field)
                                                    <td data-col="{{ 3 + $index }}" class="px-2">
                                                        <select
                                                            name="{{ $field }}_permission[{{ $user->id }}]"
                                                            class="text-xs p-1 border rounded w-full bg-white project-permission"
                                                            data-field="{{ $field }}"
                                                            data-user-id="{{ $user->id }}">

                                                            <option value="invisible"
                                                                {{ isset($permissions[$user->id]) && ($permissions[$user->id]->$field ?? 'invisible') === 'invisible'
                                                                    ? 'selected'
                                                                    : '' }}>
                                                                Invisible
                                                            </option>

                                                            <option value="read"
                                                                {{ isset($permissions[$user->id]) && ($permissions[$user->id]->$field ?? '') === 'read' ? 'selected' : '' }}>
                                                                Read
                                                            </option>

                                                            {{-- ❌ ไม่ให้ Write สำหรับ Gross --}}
                                                            @if (!in_array($field, $readOnlyFields))
                                                                <option value="write"
                                                                    {{ isset($permissions[$user->id]) && ($permissions[$user->id]->$field ?? '') === 'write' ? 'selected' : '' }}>
                                                                    Write
                                                                </option>
                                                            @endif

                                                        </select>
                                                    </td>
                                                @endforeach


                                                <!-- Dynamic Columns -->
                                                @for ($i = 1; $i <= 50; $i++)
                                                    @php
                                                        // Go/NoGo เริ่มที่ col 11
                                                        $colIndex = 10 + $i; // 11 → 60
                                                    @endphp
                                                    <td data-col="{{ $colIndex }}" class="px-2">
                                                        <select
                                                            name="col{{ $i }}_permission[{{ $user->id }}]"
                                                            class="text-xs p-1 border rounded w-full bg-white hover:bg-gray-50 dynamic-col"
                                                            data-col="{{ $i }}"
                                                            data-user-id="{{ $user->id }}">
                                                            <option value="invisible"
                                                                {{ isset($permissions[$user->id]) && $permissions[$user->id]->{"col$i"} === 'invisible' ? 'selected' : '' }}>
                                                                Invisible</option>
                                                            <option value="read"
                                                                {{ isset($permissions[$user->id]) && $permissions[$user->id]->{"col$i"} === 'read' ? 'selected' : '' }}>
                                                                Read</option>
                                                            <option value="write"
                                                                {{ isset($permissions[$user->id]) && $permissions[$user->id]->{"col$i"} === 'write' ? 'selected' : '' }}>
                                                                Write</option>
                                                        </select>
                                                    </td>
                                                @endfor
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <!-- Footer -->
                        <div class="p-4 border-t border-gray-300 flex justify-end space-x-2">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                Save Permissions
                            </button>
                        </div>


                    </div>
                </div>
            </form>

            <style>
                .custom-container {
                    height: 60px;
                    /* Adjust the height of the container as needed */
                }

                .fixed-width-input {
                    height: 40px;
                    /* Adjust the height of the input field */
                    width: 170px;
                }

                .btn {
                    height: 40px;
                }

                #exportButton {
                    width: 125px;
                }
            </style>


            <style>
                .input-group {
                    position: relative;
                    display: flex;
                    flex-wrap: wrap;
                    align-items: center;
                    width: 25%;
                }

                .table-container {
                    width: 100%;
                    max-height: 500px;
                    overflow-x: auto;
                    overflow-y: auto;
                }


                .table-container table {
                    border-collapse: separate !important;
                    border-spacing: 0;
                }

                /* เส้นขอบเฉพาะด้านล่าง */
                .table-container th,
                .table-container td {
                    border: none !important;
                    box-shadow: inset 0 -1px 0 #ddd;
                }

                .table-container td {
                    border: 1px solid #ddd;
                    padding: 6px;
                    text-align: center;
                    white-space: nowrap;
                }

                .table-container th {
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: center;
                    white-space: nowrap;
                    position: sticky;
                    top: 0px;
                    text-align: center;
                    background-color: #172554;
                    /* เทียบเท่า bg-blue-950 */
                    color: white;
                    /* เพิ่มเพื่อให้ตัวหนังสืออ่านง่าย */
                }

                .table-container tr:hover td,
                .table-container tr:hover th {
                    outline-color: #ffffff !important;
                }

                .excel-input,
                .excel-input:hover,
                .excel-input:focus {
                    outline: none !important;
                    box-shadow: none !important;
                    border: none !important;
                }
            </style>


            <!-- ตารางข้อมูล โปรเจค-->
            <div>

                @php
                    $projectCols = [
                        'Customer_Region_PJ' => 'Customer Region',
                        'Estimated_Revenue_PJ' => 'Estimated Revenue',
                        'Estimated_Service_Cost_PJ' => 'Estimated Service Cost',
                        'Estimated_Material_Cost_PJ' => 'Estimated Material Cost',
                        'Estimated_Transportation_Cost_PJ' => 'Estimated Transportation Cost',
                        'Estimated_Other_Cost_PJ' => 'Estimated Other Cost',
                    ];

                    $moneyFields = [
                        'Estimated_Revenue_PJ',
                        'Estimated_Service_Cost_PJ',
                        'Estimated_Material_Cost_PJ',
                        'Estimated_Transportation_Cost_PJ',
                        'Estimated_Other_Cost_PJ',
                    ];

                @endphp

                <div class="table-container relative overflow-x-auto mt-2 h-[395px] font-sarabun">

                    <table
                        class="table min-w-max table-fixed border-separate border-spacing-0 font-sarabun
                            [--col-1:115px]
                            [--col-2:130px]"
                        id="table">

                        <thead class="bg-blue-950 text-white font-sarabun text-base sticky top-0 z-[200]">

                            {{-- 1. ย้ายบล็อกเช็คสิทธิ์มาไว้ตรงนี้ก่อน --}}
                            @php
                                $gpVisibility = $permissions[Auth::id()]->Estimated_Gross_Profit_PJ ?? 'invisible';
                                $gmVisibility =
                                    $permissions[Auth::id()]->Estimated_Gross_Profit_Margin_PJ ?? 'invisible';
                            @endphp

                            
							<tr class="exclude-summary text-xs text-center sticky top-0 z-[210]">
                                {{-- ช่องแรก (Refcode) --}}
                                <th class="sticky left-0 z-[220] py-1 border-b shadow-sm"
                                    style="background-color: white; color: red; width: var(--col-1); min-width: var(--col-1);">
                                    <span data-summary-field="Refcode_PJ" data-type="text"></span>
                                </th>

                                <th class="sticky left-[var(--col-1)] z-[210] py-1 border-b shadow-sm"
                                    style="background-color: white; width: var(--col-2); min-width: var(--col-2);">
                                </th>


                                <th style="background-color: white;"></th>
                                <th style="background-color: white;"></th>

                                {{-- วนลูปเฉพาะ Project Columns --}}
                                @foreach ($projectCols as $field => $label)
                                    @php
                                        $visibility = $permissions[Auth::id()]->$field ?? 'invisible';
                                        $isMoney = in_array($field, $moneyFields);
                                    @endphp
                                    <th class="border-b"
                                        style="background: white; color: red; {{ $visibility === 'invisible' ? 'display:none;' : '' }}">

                                        {{-- ตรวจสอบ: ถ้าอยู่ใน moneyFields ถึงจะสร้างตัวเลขสรุป (ข้าม Customer_Region_PJ อัตโนมัติ) --}}
                                        @if ($isMoney)
                                            <span data-summary-field="{{ $field }}" data-type="money"></span>
                                        @endif
                                    </th>
                                @endforeach

                                {{-- Gross Profit & Margin --}}
                                <th class="border-b"
                                    style="background: white; color: red; {{ $gpVisibility === 'invisible' ? 'display:none;' : '' }}">
                                    <span data-summary-field="Estimated_Gross_Profit_PJ" data-type="money"></span>
                                </th>
                                <th class="border-b"
                                    style="background: white; color: red; {{ $gmVisibility === 'invisible' ? 'display:none;' : '' }}">
                                    <span data-summary-field="Estimated_Gross_Profit_Margin_PJ" data-type="avg"></span>
                                </th>


                                {{-- ===== col 1–50 (SUMMARY HEADER) ===== --}}
                                @php
                                        $columnConfig = [
                                            //1 => ['type' => 'select', 'options' => ['Go', 'NoGo']],
                                            1 => ['type' => 'text'],
                                            2 => ['type' => 'date'],
                                            3 => ['type' => 'date'],
                                            4 => ['type' => 'text'],
                                            5 => ['type' => 'date'],
                                            6 => ['type' => 'date'],
                                            7 => ['type' => 'date'],
                                            8 => ['type' => 'date'],
                                            9 => ['type' => 'money'],
                                            10 => ['type' => 'date'],
                                            11 => ['type' => 'date'],
                                            12 => ['type' => 'text'],
                                            13 => ['type' => 'text'],
                                            14 => ['type' => 'text'],
                                            15 => ['type' => 'text'],
                                            16 => ['type' => 'text'],
                                            17 => ['type' => 'money'],
                                            18 => ['type' => 'text'],
                                            19 => ['type' => 'date'],

                                            20 => ['type' => 'money'],
                                            21 => ['type' => 'text'],
                                            22 => ['type' => 'money'],
                                            23 => ['type' => 'text'],
                                            24 => ['type' => 'money'],
                                            25 => ['type' => 'text'],
                                            26 => ['type' => 'money'],
                                            27 => ['type' => 'money', 'calculated' => true],

                                            28 => ['type' => 'date'],
                                            29 => ['type' => 'date'],
                                            30 => ['type' => 'date'],
                                            31 => ['type' => 'date'],
                                            32 => ['type' => 'date'],
                                            33 => ['type' => 'date'],
                                        ];
                                    @endphp

                                @for ($i = 1; $i <= 50; $i++)
                                    @php
                                        $col = "col$i";
                                        $config = $columnConfig[$i] ?? ['type' => 'text'];

                                        $visibility = $permissions[Auth::id()]->$col ?? 'invisible';
                                        $type = $config['type'];

                                        // map type → summary type
                                        $summaryType = match ($type) {
                                            'money' => 'money', // sum
                                            'text', 'date' => 'text', // count
                                            default => null, // date / select ไม่สรุป
                                        };
                                    @endphp

                                    <th class="border-b"
    style="background: white; color: red; {{ $visibility === 'invisible' ? 'display:none;' : '' }}">

                                        @if ($summaryType)
                                            <span data-summary-field="{{ $col }}"
                                                data-type="{{ $summaryType }}"></span>
                                        @endif
                                    </th>
                                @endfor

                            </tr>
								

                            <tr class="text-xs text-center">

                                <th
                                    class=" whitespace-nowrap text-center border-b border-blue-900 group sticky top-0 left-0 z-[150] bg-blue-950 w-[var(--col-1)]">
                                    <div class="flex items-center justify-center gap-2">
                                        <span class="tracking-wide font-sarabun text-xs  text-white/90">Refcode</span>

                                        <span
                                            class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                            data-table="main" data-col="Refcode_PJ">
                                            <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                        </span>
                                    </div>
                                </th>

                                <th
                                    class=" whitespace-nowrap text-center border-b border-blue-900 group sticky top-0 left-[var(--col-1)] z-[140] bg-blue-950 w-[var(--col-2)]">
                                    <div class="flex items-center justify-center gap-2">
                                        <span class="tracking-wide font-sarabun text-xs  text-white/90">Site Code</span>

                                        <span
                                            class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                            data-table="main" data-col="Site_Code_PJ">
                                            <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                        </span>
                                    </div>
                                </th>

                                <th class=" whitespace-nowrap text-center border-b border-blue-900">
                                    <div class="flex items-center justify-center gap-2">
                                        <span class="tracking-wide font-sarabun text-xs text-white/90">Job
                                            Description</span>

                                        <span
                                            class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                            data-table="main" data-col="Job_Description_PJ">
                                            <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                        </span>
                                    </div>
                                </th>

                                <th class="whitespace-nowrap text-center border-b border-blue-900">
                                    <div class="flex items-center justify-center gap-2">
                                        <span class="tracking-wide font-sarabun text-xs text-white/90">Office
                                            Code</span>

                                        <span
                                            class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                            data-table="main" data-col="Office_Code_PJ">
                                            <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                        </span>
                                    </div>
                                </th>


                                {{-- ===== Project Columns (thead) ===== --}}
                                @php $colIndex = 4; @endphp

                                @foreach ($projectCols as $field => $label)
                                    @php
                                        $visibility = $permissions[Auth::id()]->$field ?? 'invisible';
                                        $isMoney = in_array($field, $moneyFields);
                                    @endphp

                                    <th class="whitespace-nowrap text-center border-b border-blue-900 group"
                                        style="background:green;color:white;{{ $visibility === 'invisible' ? 'display:none;' : '' }}">
                                        <div class="flex items-center justify-center gap-2">
                                            @php
                                                $words = explode(' ', $label);
                                                $line1 = $words[0] ?? '';
                                                $line2 = implode(' ', array_slice($words, 1));
                                            @endphp

                                            <span
                                                class="tracking-wide font-sarabun text-xs text-white/90 text-center leading-tight">
                                                {{ $line1 }}
                                                @if ($line2)
                                                    <br>{{ $line2 }}
                                                @endif
                                            </span>

                                            <span
                                                class="filter-icon cursor-pointer inline-flex items-center opacity-60
                   group-hover:opacity-100 transition-opacity"
                                                data-table="main" data-col="{{ $field }}"
                                                data-type="{{ $isMoney ? 'money' : 'text' }}">
                                                <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                            </span>

                                        </div>
                                    </th>
                                    @php $colIndex++; @endphp
                                @endforeach



                                @php
                                    $gpVisibility = $permissions[Auth::id()]->Estimated_Gross_Profit_PJ ?? 'invisible';
                                    $gmVisibility =
                                        $permissions[Auth::id()]->Estimated_Gross_Profit_Margin_PJ ?? 'invisible';
                                @endphp



                                {{-- ===== Estimated Gross Profit ===== --}}
                                <th class="whitespace-nowrap text-center border-b border-blue-900 group"
                                    style="background-color:blue;color:white;
           {{ $gpVisibility === 'invisible' ? 'display:none;' : '' }}">
                                    <div class="flex items-center justify-center gap-2">
                                        <span class="tracking-wide font-sarabun text-xs text-white/90">
                                            Estimated <br> Gross Profit
                                        </span>

                                        <span
                                            class="filter-icon cursor-pointer inline-flex items-center opacity-60
                   group-hover:opacity-100 transition-opacity"
                                            data-table="main" data-col="Estimated_Gross_Profit_PJ" data-type="money">
                                            @php $colIndex++; @endphp
                                            <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                        </span>
                                    </div>
                                </th>

                                {{-- ===== Estimated Gross Profit Margin ===== --}}
                                <th class="whitespace-nowrap text-center border-b border-blue-900 group"
                                    style="background-color:blue;color:white;
           {{ $gmVisibility === 'invisible' ? 'display:none;' : '' }}">
                                    <div class="flex items-center justify-center gap-2">
                                        <span class="tracking-wide font-sarabun text-xs text-white/90">
                                            Estimated <br> Gross Profit Margin
                                        </span>

                                        <span
                                            class="filter-icon cursor-pointer inline-flex items-center opacity-60
                   group-hover:opacity-100 transition-opacity"
                                            data-table="main" data-col="Estimated_Gross_Profit_Margin_PJ"
                                            data-type="money">
                                            @php $colIndex++; @endphp
                                            <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                        </span>
                                    </div>
                                </th>




                                {{-- ===== CONFIG ===== --}}
                                @php
                                    // ===============================
                                    // Column labels
                                    // ===============================
                                    $columnLabels = [
                                        // Survey (เทา)
                                        1 => 'Swap Code',
                                        2 => 'Survey (Plan)',
                                        3 => 'Survey (Actual)',
                                        4 => 'Go / Nogo',
                                        5 => 'Material Ordered Date',
                                        6 => 'Material Ready Date',
                                        7 => 'Material Withdrawn Date',
                                        8 => 'Material Received Date',
                                        9 => 'Material - Actual Price',
                                        10 => 'Install Date (Plan)',
                                        11 => 'Install Date (Actual)',
                                        12 => 'Platform Type',
                                        13 => 'Project ID',
                                        14 => 'Current Step',
                                        15 => 'Ref No.',
                                        16 => 'WO No. PROMs',
                                        17 => 'FBOQ Price',
                                        18 => 'PO No. PROMs',
                                        19 => 'Upload BOQ',

                                        // สีเขียว
                                        20 => 'Revenue',
                                        21 => 'PO No. Actual',
                                        22 => 'PO Amount Actual',
                                        23 => 'INV 1st No.',
                                        24 => 'INV 1st Price',
                                        25 => 'INV 2nd No.',
                                        26 => 'INV 2nd Price.',
                                        27 => 'PO Remaining',

                                        // สีขาว
                                        28 => 'PR Proposed date (Email)',
                                        29 => 'PR Approved Date (Email)',
                                        30 => 'Billing1 Proposed date (Email)',
                                        31 => 'Billing1 Approved Date (Email)',
                                        32 => 'Billing2 Proposed date (Email)',
                                        33 => 'Billing2 Approved Date (Email)',
                                    ];

                                    // ===============================
                                    // Background colors (INLINE)
                                    // ===============================
                                    $columnBgColors = [
                                        // เทา
                                        1 => '#0ea5e9',
                                        2 => '#0ea5e9',
                                        3 => '#0ea5e9',
                                        4 => '#0ea5e9',
                                        5 => '#0ea5e9',
                                        6 => '#0ea5e9',
                                        7 => '#0ea5e9',
                                        8 => '#0ea5e9',
                                        9 => '#0ea5e9',
                                        10 => '#0ea5e9',
                                        11 => '#0ea5e9',
                                        12 => '#0ea5e9',
                                        13 => '#0ea5e9',
                                        14 => '#0ea5e9',
                                        15 => '#0ea5e9',
                                        16 => '#0ea5e9',
                                        17 => '#0ea5e9',
                                        18 => '#0ea5e9',
                                        19 => '#0ea5e9',

                                        // เขียว
                                        20 => '#16a34a',
                                        21 => '#16a34a',
                                        22 => '#16a34a',
                                        23 => '#16a34a',
                                        24 => '#16a34a',
                                        25 => '#16a34a',
                                        26 => '#16a34a',
                                        27 => '#16a34a',

                                        //ขาว
                                        28 => '#162456',
                                        29 => '#162456',
                                        30 => '#162456',
                                        31 => '#162456',
                                        32 => '#162456',
                                        33 => '#162456',
                                    ];

                                    $colIndex = 12;
                                @endphp





                                {{-- ===== THEAD ===== --}}
                                @for ($i = 1; $i <= 50; $i++)
                                    @php
                                        $col = "col$i";
                                        $visibility = $permissions[Auth::id()]->$col ?? 'invisible';

                                        $label = $columnLabels[$i] ?? "Col $i";
                                        $bgColor = $columnBgColors[$i] ?? '#FF0000'; // default เทา
                                    @endphp

                                    <th class="whitespace-nowrap text-center border-b border-blue-900
               text-white group"
                                        style="
            background-color: {{ $bgColor }};
            {{ $visibility === 'invisible' ? 'display:none;' : '' }}
        ">

                                        <div class="flex items-center justify-center gap-2">
                                            <span class="tracking-wide font-sarabun text-xs">
                                                {{ $label }}
                                            </span>

                                            <span
                                                class="filter-icon cursor-pointer inline-flex items-center
                       opacity-60 group-hover:opacity-100 transition-opacity"
                                                data-table="main" data-col="col{{ $i }}" data-type="text">
                                                <i class="fi fi-br-bars-filter text-xs"></i>
                                            </span>
                                        </div>
                                    </th>

                                    @php $colIndex++; @endphp
                                @endfor




                            </tr>
                        </thead>

                        <tbody id="tableBody" class="text-xs [&_input]:text-xs">

                            @foreach ($projectData as $item)
                                <tr
                                    class="group bg-white hover:bg-red-100 transition-colors font-sarabun duration-200 text-xs">

                                    {{-- Refcode --}}
                                    <td data-col="Refcode_PJ"
                                        class="sticky left-0 z-[120] w-[var(--col-1)] bg-white px-2 py-1 whitespace-nowrap text-center group-hover:bg-red-100 transition">
                                        {{ $item->Refcode_PJ }}
                                    </td>

                                    {{-- Site Code --}}
                                    <td data-col="Site_Code_PJ"
                                        class="sticky left-[var(--col-1)] z-[110] w-[var(--col-2)] bg-white px-2 py-1 whitespace-nowrap text-center group-hover:bg-red-100 transition">
                                        {{ $item->Site_Code_PJ }}
                                    </td>

                                    <td data-col="Job_Description_PJ">{{ $item->Job_Description_PJ }}</td>
                                    <td data-col="Office_Code_PJ">{{ $item->Office_Code_PJ }}</td>


                                    {{-- ===== Project Columns (tbody) ===== --}}
                                    @foreach ($projectCols as $field => $label)
                                        @php
                                            $visibility = $permissions[Auth::id()]->$field ?? 'invisible';
                                            $isRead = $visibility === 'read';
                                            $isInvisible = $visibility === 'invisible';
                                            $isMoney = in_array($field, $moneyFields);
                                            $originalValue = $item->$field ?? '';
                                        @endphp

                                        <td data-col="{{ $field }}" class="project-col {{ $field }}"
                                            style="{{ $isInvisible ? 'display:none;' : '' }}">

                                            <input type="text"
                                                class="excel-input {{ $isMoney ? 'money-input' : '' }} {{ $isRead ? 'readonly-cell' : '' }}"
                                                style="{{ $isMoney ? 'text-align:right;' : '' }}"
                                                value="{{ $isMoney
                                                    ? ($originalValue !== null && $originalValue !== ''
                                                        ? number_format((float) str_replace(',', '', $originalValue), 2)
                                                        : '')
                                                    : $originalValue }}"
                                                data-id="{{ $item->Refcode_PJ }}" data-field="{{ $field }}"
                                                data-original="{{ $originalValue }}"
                                                {{ $isRead ? 'readonly disabled tabindex=-1' : '' }}
                                                @if ($field === 'Estimated_Revenue_PJ') oninput="validateRevenue(this)" @endif>
                                        </td>
                                    @endforeach



                                    <!-- Gross Profit and Margin -->
                                    @php
                                        $revenue = (float) str_replace(',', '', $item->Estimated_Revenue_PJ ?? 0);
                                        $service = (float) str_replace(',', '', $item->Estimated_Service_Cost_PJ ?? 0);
                                        $material = (float) str_replace(
                                            ',',
                                            '',
                                            $item->Estimated_Material_Cost_PJ ?? 0,
                                        );
                                        $transportation = (float) str_replace(
                                            ',',
                                            '',
                                            $item->Estimated_Transportation_Cost_PJ ?? 0,
                                        );
                                        $other = (float) str_replace(',', '', $item->Estimated_Other_Cost_PJ ?? 0);

                                        $grossProfit = $revenue - $service - $material - $transportation - $other;
                                        $grossMargin = $revenue != 0 ? ($grossProfit / $revenue) * 100 : 0;
                                    @endphp

                                    <td data-col="Estimated_Gross_Profit_PJ"
                                        style="{{ $gpVisibility === 'invisible' ? 'display:none;' : '' }}">
                                        <input type="text" class="excel-input gross-profit text-end readonly-cell"
                                            style="text-align: right;" value="{{ number_format($grossProfit, 2) }}"
                                            readonly disabled tabindex="-1">
                                    </td>



                                    <td data-col="Estimated_Gross_Profit_Margin_PJ"
                                        style="{{ $gmVisibility === 'invisible' ? 'display:none;' : '' }}">
                                        <input type="text" class="excel-input gross-margin text-end readonly-cell"
                                            value="{{ number_format($grossMargin, 2) }}%" readonly disabled
                                            tabindex="-1">
                                    </td>


                                    {{-- ===== col 1–50 (tbody) ===== --}}
                                    @php
                                        $columnConfig = [
                                            //1 => ['type' => 'select', 'options' => ['Go', 'NoGo']],
                                            1 => ['type' => 'text'],
                                            2 => ['type' => 'date'],
                                            3 => ['type' => 'date'],
                                            4 => ['type' => 'select', 'options' => ['Go', 'NoGo']],
                                            5 => ['type' => 'date'],
                                            6 => ['type' => 'date'],
                                            7 => ['type' => 'date'],
                                            8 => ['type' => 'date'],
                                            9 => ['type' => 'money'],
                                            10 => ['type' => 'date'],
                                            11 => ['type' => 'date'],
                                            12 => ['type' => 'text'],
                                            13 => ['type' => 'text'],
                                            14 => ['type' => 'text'],
                                            15 => ['type' => 'text'],
                                            16 => ['type' => 'text'],
                                            17 => ['type' => 'money'],
                                            18 => ['type' => 'text'],
                                            19 => ['type' => 'date'],

                                            20 => ['type' => 'money'],
                                            21 => ['type' => 'text'],
                                            22 => ['type' => 'money'],
                                            23 => ['type' => 'text'],
                                            24 => ['type' => 'money'],
                                            25 => ['type' => 'text'],
                                            26 => ['type' => 'money'],
                                            27 => ['type' => 'money', 'calculated' => true],

                                            28 => ['type' => 'date'],
                                            29 => ['type' => 'date'],
                                            30 => ['type' => 'date'],
                                            31 => ['type' => 'date'],
                                            32 => ['type' => 'date'],
                                            33 => ['type' => 'date'],
                                        ];
                                    @endphp

                                    @for ($i = 1; $i <= 50; $i++)
                                        @php
                                            $col = "col$i";
                                            $config = $columnConfig[$i] ?? ['type' => 'text'];

                                            $visibility = $permissions[Auth::id()]->$col ?? 'invisible';
                                            $isRead = $visibility === 'read';
                                            $isInvisible = $visibility === 'invisible';

                                            $value = $item->$col;
                                        @endphp

                                        <td data-col="col{{ $i }}" class="col-{{ $i }}"
                                            style="{{ $isInvisible ? 'display:none;' : '' }}"
                                            title="{{ $value }}">

                                            @if (in_array($config['type'], ['text', 'date']))
                                                {{-- TEXT / DATE --}}
                                                <input type="{{ $config['type'] }}"
                                                    class="excel-input {{ $isRead ? 'readonly-cell' : '' }}"
                                                    value="{{ $value }}" data-id="{{ $item->Refcode_PJ }}"
                                                    data-field="{{ $col }}"
                                                    {{ $isRead ? 'readonly tabindex=-1' : '' }}>
                                            @elseif ($config['type'] === 'money')
                                                <input type="text"
                                                    class="excel-input money-input text-end {{ $isRead || ($config['calculated'] ?? false) ? 'readonly-cell' : '' }}"
                                                    value="{{ is_numeric($value) ? number_format($value, 2) : $value }}"
                                                    data-id="{{ $item->Refcode_PJ }}" data-field="{{ $col }}"
                                                    {{ $isRead || ($config['calculated'] ?? false) ? 'readonly tabindex=-1' : '' }}>
                                            @elseif ($config['type'] === 'select')
                                                {{-- SELECT --}}
                                                <select class="excel-input {{ $isRead ? 'readonly-cell' : '' }}"
                                                    data-id="{{ $item->Refcode_PJ }}" data-field="{{ $col }}"
                                                    style="{{ $isRead ? 'pointer-events:none;background:#f3f4f6;' : '' }}">
                                                    @foreach ($config['options'] as $option)
                                                        <option value="{{ $option }}"
                                                            {{ $value == $option ? 'selected' : '' }}>
                                                            {{ $option }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif


                                        </td>
                                    @endfor

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="listViewPagination"
                class="mt-4 p-4 sm:p-5 bg-white rounded-xl border border-gray-200 shadow-sm">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    {{-- Left: Rows per page --}}
                        <div class="flex items-center gap-2">
                            <!-- Label with Icon -->
                            <label class="flex items-center gap-1.5 text-xs font-sarabun font-medium text-gray-600 whitespace-nowrap">
                                <i class="fa-solid fa-list-ul text-indigo-400"></i>
                                แสดงรายการ:
                            </label>

                            @php
                                $total = $projectData->count();
                                $baseOptions = [10, 20, 50, 100];
                                $options = array_filter($baseOptions, fn($v) => $v < $total);
                                $options[] = $total;
                            @endphp

                            <!-- Custom Select Container -->
                            <div class="relative group">
                                @php
                                    $query = request()->except(['per_page','page']);
                                @endphp

                                <select 
                                onchange="window.location='{{ request()->url() }}?{{ http_build_query($query) }}&per_page='+this.value+'&page=1'"
                                class="appearance-none py-2 pl-3 pr-8 border border-gray-200 rounded-xl text-xs font-sarabun font-medium
                                bg-white text-gray-700 cursor-pointer min-w-[80px] text-center
                                hover:border-indigo-300 hover:bg-indigo-50/50
                                focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500
                                transition-all duration-300 shadow-sm hover:shadow-md">
                                    
                                    @foreach($options as $size)
                                        <option value="{{ $size }}"
                                            {{ $projectData->perPage() == $size ? 'selected' : '' }}>
                                            {{ $size == $total ? 'ทั้งหมด ('.$total.')' : $size }}
                                        </option>
                                    @endforeach
                                </select>
                                
                                <!-- Custom Dropdown Arrow -->
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
                                    <i class="fa-solid fa-chevron-down text-[9px] text-gray-400 
                                            group-hover:text-indigo-500 transition-colors"></i>
                                </div>
                            </div>
                        </div>

                    {{-- Center: Pagination --}}
                    <nav class="flex items-center gap-1.5">

                        {{-- Previous --}}
                        @if ($projectData->onFirstPage())
                            <button disabled
                                class="w-9 h-9 rounded-xl border opacity-30">
                                <i class="fa-solid fa-chevron-left text-xs"></i>
                            </button>
                        @else
                            <a href="{{ $projectData->previousPageUrl() }}"
                                class="w-9 h-9 flex items-center justify-center rounded-xl border hover:bg-indigo-600 hover:text-white transition">
                                <i class="fa-solid fa-chevron-left text-xs"></i>
                            </a>
                        @endif


                        {{-- Page Numbers --}}
                        @for ($i = 1; $i <= $projectData->lastPage(); $i++)
                            <a href="{{ $projectData->url($i) }}"
                                class="w-9 h-9 flex items-center justify-center rounded-xl text-xs
                                {{ $projectData->currentPage() == $i
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-white text-gray-600 border hover:bg-indigo-50' }}">
                                {{ $i }}
                            </a>
                        @endfor


                        {{-- Next --}}
                        @if ($projectData->hasMorePages())
                            <a href="{{ $projectData->nextPageUrl() }}"
                                class="w-9 h-9 flex items-center justify-center rounded-xl border hover:bg-indigo-600 hover:text-white transition">
                                <i class="fa-solid fa-chevron-right text-xs"></i>
                            </a>
                        @else
                            <button disabled
                                class="w-9 h-9 rounded-xl border opacity-30">
                                <i class="fa-solid fa-chevron-right text-xs"></i>
                            </button>
                        @endif

                    </nav>

                    {{-- Right: Summary --}}
                    <div>
                        <span class="text-xs text-gray-500 bg-gray-100 px-3 py-2 rounded-full">
                            แสดง
                            <span class="text-indigo-600 font-semibold">
                                {{ $projectData->firstItem() }}-{{ $projectData->lastItem() }}
                            </span>
                            จากทั้งหมด
                            <span class="font-semibold">
                                {{ $projectData->total() }}
                            </span>
                            รายการ
                        </span>
                    </div>

                </div>
            </div>
        </div>

    </main>

    <!-- ก้อน Filter ที่ใช้ทุกคอลั่ม -->
    <div id="column-filter-modal" class="fixed inset-0 z-[9999] hidden bg-transparent">
        <div id="column-filter-content" onclick="event.stopPropagation()"
            class="shadow-2xl bg-white rounded-xl flex flex-col w-[300px] h-[450px] absolute border border-gray-100">


            <div class="px-2 pt-2">
                <button type="button" onclick="clearColumnFilterExcel()"
                    class="w-full flex items-center gap-3 px-3 py-2 text-xs font-sarabun text-slate-600 hover:bg-red-50 hover:text-red-600 rounded-xl transition-all group">
                    <div class="w-7 h-7 flex items-center justify-center bg-slate-100 group-hover:bg-red-100 rounded-lg">
                        <i class="fa-solid fa-filter-circle-xmark"></i>
                    </div>
                    <span>Clear Filter from this column</span>
                </button>
            </div>

            <div class="px-2 pt-2">
                <button type="button" onclick="clearAllTableFilters()"
                    class="w-full flex items-center gap-3 px-3 py-2 text-xs font-sarabun text-slate-600 hover:bg-red-50 hover:text-red-600 rounded-xl transition-all group">
                    <div class="w-7 h-7 flex items-center justify-center bg-slate-100 group-hover:bg-red-100 rounded-lg">
                        <i class="fa-solid fa-broom"></i>
                    </div>
                    <span>Clear Filter from all columns</span>
                </button>
            </div>



            <!-- Selection and Sorting Controls -->
            <div class="px-4 pt-3 pb-2 border-b border-gray-100">
                <!-- Select / Deselect All -->
                <div class="flex justify-between space-x-2 mb-3">
                    <button type="button" id="selectAllFilter" onclick="selectAll()"
                        class="w-1/2 text-xs font-sarabun text-center bg-green-300 hover:bg-green-400 text-gray-800 rounded py-1">
                        Select All
                    </button>
                    <button type="button" id="deselectAllFilter" onclick="deselectAll()"
                        class="w-1/2 text-xs font-sarabun text-center bg-red-300 hover:bg-red-400 text-gray-800 rounded py-1">
                        Deselect All
                    </button>
                </div>

                <!-- Sort Buttons -->
                <div class="flex justify-between space-x-2">
                    <button type="button" onclick="sortAZ()"
                        class="w-1/2 text-xs font-sarabun text-center bg-gray-200 hover:bg-gray-300 text-gray-700 rounded py-1">
                        <i data-lucide="arrow-down-a-to-z" class="w-3.5 h-3.5"></i>
                        <span>Sort A &rarr; Z</span>
                    </button>
                    <button type="button" onclick="sortZA()"
                        class="w-1/2 text-xs font-sarabun text-center bg-gray-200 hover:bg-gray-300 text-gray-700 rounded py-1">
                        <i data-lucide="arrow-up-z-to-a" class="w-3.5 h-3.5"></i>
                        <span>Sort Z &rarr; A</span>
                    </button>
                </div>
            </div>

            <!-- Search Input -->
            <div class="px-4 py-3 border-b border-gray-100">
                <div class="relative">
                    <i data-lucide="search"
                        class="fa-solid fa-magnifying-glass w-4 h-4 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2"></i>
                    <input type="text" id="column-filter-search" placeholder=""
                        class="pl-9 pr-3 w-full h-9 outline-none bg-gray-50 border border-gray-200 rounded-lg text-sm transition-all focus:border-blue-400 focus:bg-white"
                        oninput="handleSearch(this.value)" onkeydown="handleSearchEnter(event)">
                </div>
            </div>

            <!-- Checkbox List -->
            <div id="column-filter-checkbox-list"
                class="overflow-y-auto font-sarabun px-4 py-2 text-sm max-h-60 flex-grow">
                <!-- Checkboxes generated by JS -->
            </div>

            <!-- Apply / Cancel Footer -->
            <div class="flex justify-end space-x-2 border-t px-4 py-3 bg-gray-50 rounded-b-xl">
                <button type="button" onclick="applyColumnFilter()"
                    class="bg-blue-600 text-white px-4 py-2 text-xs rounded-lg font-sarabun hover:bg-blue-700 transition shadow-md">OK</button>
                <button type="button" onclick="closeColumnFilterModal()"
                    class="bg-white border border-gray-300 text-gray-700 px-4 py-2 text-xs rounded-lg font-sarabun hover:bg-gray-100 transition shadow-sm">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        function storeOriginal(input) {
            input.dataset.original = input.value;
        }

        function validateRevenue(input) {
            let raw = input.value.replace(/,/g, '').trim();

            // ❌ ว่าง
            if (raw === '') {
                input.value = input.dataset.original ?? '';
                return;
            }

            // ❌ ไม่ใช่ตัวเลข
            if (isNaN(raw)) {
                input.value = input.dataset.original ?? '';
                return;
            }

            let num = parseFloat(raw);

            // ❌ ห้ามเป็น 0
            if (num === 0) {
                input.value = input.dataset.original ?? '';
                return;
            }

            // ✔ ถูกต้อง → อัปเดต original
            input.dataset.original = input.value;
        }
    </script>

    <!-- Auto Set Permission ตาม Role -->

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const readOnlyProjectFields = [
                'Estimated_Gross_Profit_PJ',
                'Estimated_Gross_Profit_Margin_PJ'
            ];

            const rolePermissions = {
                "": { // No role
                    cols: {
                        invisible: "all"
                    },
                    project: {
                        normal: "invisible",
                        readonly: "invisible"
                    }
                },

                "Project Manager": {
                    cols: {
                        write: Array.from({
                            length: 33
                        }, (_, i) => i + 1)
                        // col 1–35 = read
                        // col 35–50 = invisible
                    },
                    project: {
                        normal: "write",
                        readonly: "read"
                    }
                },

                "Project Admin": {
                    cols: {
                        read: Array.from({
                            length: 33
                        }, (_, i) => i + 1)
                        // col 1–35 = read
                        // col 35–50 = invisible
                    },
                    project: {
                        normal: "write",
                        readonly: "read"
                    }
                },

                "Site Supervisor": {
                    cols: {
                        read: Array.from({
                            length: 33
                        }, (_, i) => i + 1)
                        // col 1–35 = read
                        // col 35–50 = invisible
                    },
                    project: {
                        normal: "read",
                        readonly: "read"
                    }
                },

                "Project Director": {
                    cols: {
                        read: Array.from({
                            length: 33
                        }, (_, i) => i + 1)
                        // col 1–35 = read
                        // col 35–50 = invisible
                    },
                    project: {
                        normal: "read", // col สีเขียว
                        readonly: "read" // 2 read  Estimated Gross Profit ,  Estimated Gross Profit Margin
                    }
                }
            };

            document.querySelectorAll('.project-role').forEach(roleSelect => {

                roleSelect.addEventListener('change', function() {

                    const role = this.value;
                    const row = this.closest('tr');
                    const config = rolePermissions[role] || rolePermissions[""];

                    /* ===== col 1–50 ===== */
                    row.querySelectorAll('.dynamic-col').forEach(select => {
                        const col = parseInt(select.dataset.col);

                        select.value = 'invisible'; // reset

                        if (config.cols.write === "all") select.value = 'write';
                        else if (config.cols.read === "all") select.value = 'read';
                        else if (config.cols.write?.includes(col)) select.value = 'write';
                        else if (config.cols.read?.includes(col)) select.value = 'read';
                    });

                    /* ===== 4 Project Columns ===== */
                    row.querySelectorAll('.project-permission').forEach(select => {
                        const field = select.dataset.field;

                        if (readOnlyProjectFields.includes(field)) {
                            select.value = config.project.readonly;
                        } else {
                            select.value = config.project.normal;
                        }
                    });
                });
            });
        });

        document.querySelectorAll("select").forEach(select => {
            select.addEventListener("change", function() {

                const match = this.className.match(/perm-col-(\d+)/);
                if (!match) return;

                const col = match[1];
                const mode = this.value;

                const tds = document.querySelectorAll(".col-" + col);

                tds.forEach(td => {
                    const input = td.querySelector('.excel-input');
                    if (!input) return;

                    if (mode === "invisible") {
                        td.style.display = "none";
                    } else if (mode === "read") {
                        td.style.display = "";
                        input.readOnly = true;
                        input.style.backgroundColor = "#f8f8f8";
                    } else if (mode === "write") {
                        td.style.display = "";
                        input.readOnly = false;
                        input.style.backgroundColor = "transparent";
                    }
                });
            });
        });
    </script>


    <!--แปลงเป็น Editable-->

    <style>
        /* Input field แบบ Excel */
        .excel-input {
            width: 100%;
            min-width: 100px;
            /* ให้ cell แสดงเต็มความยาวที่ต้องการ */

            background: transparent;
            /* พื้นหลังใส */
            font-size: 10px;
            text-align: center;
            box-sizing: border-box;
            /* รวม padding + border ใน width */
            white-space: nowrap;
            /* อยู่บรรทัดเดียว */

            text-overflow: ellipsis;
            /* แสดง ... ถ้ายาวเกิน */
            transition: all 0.2s;
            /* effect เวลา focus */
        }

        .money-input {
            text-align: right;
            /* 👈 money ชิดขวา */
        }

        /* Focus / editable state */
        .excel-input:focus,
        .excel-input.active-hover {
            outline: 1px solid #3b82f6;
            /* สีฟ้า */
            background: #eef6ff;
            /* ฟีล Excel */
            border-radius: 10%;
            /* มุมโค้งเล็กน้อย */
        }

        /* Readonly cell */
        .readonly-cell {
            background-color: #f5f5f5;
            /* เทาอ่อน */
            cursor: not-allowed;
        }
    </style>


    <script>
        document.addEventListener('DOMContentLoaded', () => {

            document.querySelectorAll('.excel-input').forEach(el => {

                const save = () => {
                    if (el.disabled) return;

                    let value = el.value?.trim();
                    if (value === '') value = null;

                    // ถ้าเป็น money → เอา comma ออกก่อนส่ง
                    if (el.classList.contains('money-input') && value !== null) {
                        value = value.replace(/,/g, '');
                    }

                    fetch("{{ route('newjob.inlineUpdate_85') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                id: el.dataset.id,
                                field: el.dataset.field,
                                value: value
                            })
                        })
                        .then(r => r.json())
                        .then(res => {
                            if (res.success) {
                                el.classList.add('bg-green-100');
                                setTimeout(() => el.classList.remove('bg-green-100'), 400);
                            }
                        })
                        .catch(console.error);
                };

                /* ---------- BLUR / CHANGE ---------- */
                el.addEventListener('blur', save);
                el.addEventListener('change', save);

                /* ---------- ENTER = FORMAT + SAVE ---------- */
                el.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();

                        // 🔹 trigger blur เพื่อให้ money format ก่อน
                        el.blur();

                        // 🔹 save (อยู่ช่องเดิม)
                        save();
                    }
                });
            });

        });
    </script>





    <!-- Export -->
<script>
function exportToExcel() {
    const table = document.getElementById("table");
    const rows = table.querySelectorAll("tr");

    let data = [];

    rows.forEach(row => {
        // ❌ ข้าม row ที่ถูกซ่อน
        if (row.offsetParent === null) return;

        let rowData = [];
        const cells = row.querySelectorAll("th, td");

        cells.forEach(cell => {
            // ❌ ข้าม cell ที่ถูกซ่อน
            if (cell.offsetParent === null) return;

            let value = "";

            // input / select / textarea
            const input = cell.querySelector("input, select, textarea");
            if (input) {
                value = input.value;
            } else {
                value = cell.innerText.trim();
            }

            // ลบ comma
            const raw = value.replace(/,/g, "");

            // ถ้าเป็นตัวเลข → number
            if (raw !== "" && !isNaN(raw)) {
                rowData.push(Number(raw));
            } else {
                rowData.push(value);
            }
        });

        if (rowData.length > 0) {
            data.push(rowData);
        }
    });

    // ===== Excel =====
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(data);

    // บังคับ type number
    Object.keys(ws).forEach(cell => {
        if (cell[0] === "!") return;
        if (typeof ws[cell].v === "number") {
            ws[cell].t = "n";
            ws[cell].z = "0.00";
        }
    });

    XLSX.utils.book_append_sheet(wb, ws, "Visible Data");
    XLSX.writeFile(wb, "85_True_Site_Preparation.xlsx");
}
</script>


    <!-- ฟังชั่น Filter -->
<script>
const tables = {
    main: {
        tbody: "#tableBody",
        allRows: [],
        visibleRows: [],
        filters: {},
        sort: { col: null, dir: null },
        rowsPerPage: 10,
        currentPage: 1
    },

    permission: {
        tbody: "#permissionTableBody",
        allRows: [],
        visibleRows: [],
        filters: {},
        sort: { col: null, dir: null }
    }
};

const urlParams = new URLSearchParams(window.location.search);
const perPage = parseInt(urlParams.get("per_page"));

if (perPage) {
    tables.main.rowsPerPage = perPage;
}

let openFilter = { table: null, col: null };
let openFilterColumn = null;
let originalFilterOrder = [];

/* =====================================================
   ICONS
===================================================== */
const ICONS = {
    normal: `<i class="fi fi-br-bars-filter text-xs text-gray-300"></i>`,
    filter: `<i class="fi fi-br-bars-filter text-xs text-blue-500"></i>`,
    sortAsc: `<i class="fa-solid fa-arrow-down-a-z text-xs text-indigo-500"></i>`,
    sortDesc: `<i class="fa-solid fa-arrow-down-z-a text-xs text-indigo-500"></i>`
};

/* =====================================================
   INIT
===================================================== */
document.addEventListener("DOMContentLoaded", () => {
    initTable("main");
});

function clearColumnFilterExcel() {
    const { table, col } = openFilter;
    if (!table || col === null) return;

    const t = tables[table];

    delete t.filters[col];

    if (t.sort.col === col) {
        t.sort = { col: null, dir: null };
    }

    t.currentPage = 1;

    if (table === "main") {
        t.rowsPerPage = 10;
        const select = document.getElementById("rowsPerPageList");
        if (select) select.value = "10";
    }

    applyAll(table);

    document.getElementById("column-filter-search").value = "";
    closeColumnFilterModal();
}



function clearAllTableFilters() {
    const { table } = openFilter;
    if (!table) return;

    const t = tables[table];

    t.filters = {};
    t.sort = { col: null, dir: null };
    t.currentPage = 1;

    // ✅ RESET rowsPerPage กลับค่าเริ่มต้น
    if (table === "main") {
        t.rowsPerPage = 10;
        const select = document.getElementById("rowsPerPageList");
        if (select) select.value = "10";
    }

    applyAll(table);

    document.getElementById("column-filter-search").value = "";
    closeColumnFilterModal();
}


function selectAll() {
    document
        .querySelectorAll("#column-filter-checkbox-list .filter-checkbox")
        .forEach(cb => {
            cb.checked = true;                 // ⭐ เพิ่ม
            cb.closest("label").style.display = "";
        });
}


function deselectAll() {
    document
        .querySelectorAll("#column-filter-checkbox-list .filter-checkbox")
        .forEach(cb => {
            cb.checked = false;
            cb.closest("label").style.display = ""; // เผื่อถูก search ซ่อน
        });
}

function handleSearch(text) {
    const keyword = text.toLowerCase().trim();
    const list = document.getElementById("column-filter-checkbox-list");

    // reset order
    originalFilterOrder.forEach(label => list.appendChild(label));

    const { table, col } = openFilter;
    const selected = tables[table].filters[col] ?? [];

    // ✅ ลบ search → แค่แสดงทั้งหมด (ไม่ยุ่ง checkbox)
    if (keyword === "") {
        originalFilterOrder.forEach(label => {
            const cb = label.querySelector("input");
            label.style.display = "";
            cb.checked = selected.includes(cb.value);
        });
        return;
    }

    const startsWith = [];
    const includes = [];

    originalFilterOrder.forEach(label => {
        const text = label.querySelector("span").innerText.toLowerCase();
        const cb = label.querySelector("input");

        if (text.startsWith(keyword)) {
            label.style.display = "";
            cb.checked = true;
            startsWith.push(label);
        }
        else if (text.includes(keyword)) {
            label.style.display = "";
            cb.checked = true;
            includes.push(label);
        }
        else {
            label.style.display = "none";
            cb.checked = false;
        }
    });

    [...startsWith, ...includes].forEach(label => list.appendChild(label));
}




let permissionTableInited = false;

function openPermissionModal() {
    document.getElementById("permissionModal").classList.remove("hidden");

    if (!permissionTableInited) {
        setTimeout(() => {
            initTable("permission");
            permissionTableInited = true;
        }, 0);
    }
}



function initTable(tableKey) {
    const t = tables[tableKey];
    const rows = document.querySelectorAll(`${t.tbody} tr`);

    t.allRows = [...rows];
    t.visibleRows = [...rows];
    t.filters = {};
    t.sort = { col: null, dir: null };
    t.currentPage = 1;

    // ✅ แสดง 10 แถวแรกทันทีก่อน render เต็ม
    t.allRows.forEach((r, i) => {
        r.style.display = i < t.rowsPerPage ? "" : "none";
    });

    renderTable(tableKey);
}

/* =====================================================
   OPEN FILTER
===================================================== */
function openColumnFilter(tableKey, colKey, icon) {
    if (!tables[tableKey] || tables[tableKey].allRows.length === 0) {
        console.warn("Table not ready:", tableKey);
        return;
    }

    openFilter = { table: tableKey, col: String(colKey) };
    openFilterColumn = colKey;

    const search = document.getElementById("column-filter-search");
    search.value = "";

    loadFilterValues(tableKey, colKey);
    showFilterModal(icon);

    setTimeout(() => search.focus(), 0);
}




/* =====================================================
   FILTER MODAL
===================================================== */
function showFilterModal(icon) {
    const modal = document.getElementById("column-filter-modal");
    const box = document.getElementById("column-filter-content");

    modal.classList.remove("hidden");

    const rect = icon.getBoundingClientRect();
    const boxRect = box.getBoundingClientRect();

    const margin = 8;

    // ✅ header sticky height (ปรับตามของจริง)
    const STICKY_HEADER_HEIGHT = 48;

    // ✅ left = ตรงคอลัมน์ที่กด
    let left = rect.left;

    // ✅ top = ใต้ header เสมอ (ถ้า icon อยู่ใน sticky)
    let top =
        rect.top <= STICKY_HEADER_HEIGHT
            ? STICKY_HEADER_HEIGHT + margin
            : rect.bottom + margin;

    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;

    // กันล้นขวา
    if (left + boxRect.width > viewportWidth) {
        left = viewportWidth - boxRect.width - margin;
    }

    // กันล้นซ้าย
    if (left < margin) {
        left = margin;
    }

    // กันล้นล่าง
    if (top + boxRect.height > viewportHeight) {
        top = viewportHeight - boxRect.height - margin;
    }

    box.style.left = `${left}px`;
    box.style.top = `${top}px`;
}



function closeColumnFilterModal() {
    document.getElementById("column-filter-modal").classList.add("hidden");
    openFilterColumn = null;
}

/* =====================================================
   LOAD FILTER VALUES
===================================================== */

function loadFilterValues(tableKey, colIndex) {
    const t = tables[tableKey];
    const list = document.getElementById("column-filter-checkbox-list");
    list.innerHTML = "";

    const selected = t.filters[colIndex] ?? [];
    originalFilterOrder = [];

    const values = new Set();
    let hasRealValue = false;

    // ⭐ สำคัญมาก
    const activeFilters = Object.keys(t.filters).filter(c => c != colIndex);

    const sourceRows =
        activeFilters.length > 0
            ? t.allRows.filter(row => {
                return activeFilters.every(c => {
                    const v = getCellValue(row, c, false) ?? "";
                    return t.filters[c].includes(v);
                });
            })
            : t.allRows;

    sourceRows.forEach(row => {
        const v = getCellValue(row, colIndex, false);

        if (v !== null) {
            values.add(v);
            if (v !== "") hasRealValue = true;
        }
    });

    if (!hasRealValue) values.add("");

    values.forEach(v => {
        const label = document.createElement("label");
        label.className = "flex gap-2 text-xs py-1";

        const displayText = v === "" ? "DD/MMM/YYYY" : v;
        label.innerHTML = `
            <input type="checkbox"
                class="filter-checkbox"
                value="${v}"
                ${selected.includes(v) ? "checked" : ""}>
            <span>${displayText}</span>
        `;

        list.appendChild(label);
        originalFilterOrder.push(label);
    });
}




/* =====================================================
   APPLY FILTER
===================================================== */
function applyColumnFilter() {
    const { table, col } = openFilter;
    const t = tables[table];

    const checked = [...document.querySelectorAll(".filter-checkbox")]
        .filter(cb => cb.checked)
        .map(cb => cb.value); // ❌ ไม่ lower

    if (checked.length === 0) delete t.filters[col];
    else t.filters[col] = checked;

    applyAll(table);
    closeColumnFilterModal();
}




function applyAll(tableKey) {
    const t = tables[tableKey];

    t.visibleRows = t.allRows.filter(row => {
        for (let col in t.filters) {
            let cellVal = getCellValue(row, col, false);

            // ✅ สำคัญมาก
            if (cellVal === null) {
                cellVal = "";
            }

            if (!t.filters[col].includes(cellVal)) return false;
        }
        return true;
    });

    t.currentPage = 1;
    applySort(tableKey);
    renderTable(tableKey);
}





function reorderDOM(tableKey) {
    const t = tables[tableKey];
    const tbody = document.querySelector(t.tbody);

    t.visibleRows.forEach(row => {
        tbody.appendChild(row); // ⭐ ย้าย DOM จริง
    });
}




// ฟังชั่น sortAZ / sortZA
function sortAZ() {
    sortTable(openFilter.table, openFilter.col, "asc");
    closeColumnFilterModal();
}

function sortZA() {
    sortTable(openFilter.table, openFilter.col, "desc");
    closeColumnFilterModal();
}



function sortTable(tableKey, col, dir) {
    if (!tableKey || col === null || col === undefined) {
        console.warn("Sort ignored: no active column");
        return;
    }

    const t = tables[tableKey];
    t.sort = { col: String(col), dir };

    applySort(tableKey);
    renderTable(tableKey);
}

function parseDMY(value) {
    if (!value) return null;

    // รองรับเฉพาะ DD/MMM/YYYY เท่านั้น
    const parts = value.split("/");
    if (parts.length !== 3) return null;

    const day   = parseInt(parts[0], 10);
    const month = parseInt(parts[1], 10);
    const year  = parseInt(parts[2], 10);

    if (
        isNaN(day)   || day < 1 || day > 31 ||
        isNaN(month) || month < 1 || month > 12 ||
        isNaN(year)
    ) {
        return null;
    }

    return { day, month, year };
}

function parseDMYToTime(value) {
    if (!value) return null;


    if (value === "DD-MMM-YYYY") return null;

    const parts = value.split("-");
    if (parts.length !== 3) return null;

    const day   = parseInt(parts[0], 10);
    const month = parseInt(parts[1], 10) - 1;
    const year  = parseInt(parts[2], 10);

    if (
        isNaN(day) || isNaN(month) || isNaN(year)
    ) return null;

    return new Date(year, month, day).getTime();
}


function applySort(tableKey) {
    const t = tables[tableKey];
    if (!t.sort.col || !t.sort.dir) return;

    const colKey = String(t.sort.col);

    t.visibleRows.sort((a, b) => {
        const v1 = getCellValue(a, colKey, false);
        const v2 = getCellValue(b, colKey, false);

        // =============================
        // ⭐ DATE SORT (DD-MMM-YYYY)
        // =============================
        const d1 = parseDMYToTime(v1);
        const d2 = parseDMYToTime(v2);

        // ✅ ถ้าไม่มีวันที่จริง → ดันลงล่างเสมอ
        if (d1 === null && d2 !== null) return 1;
        if (d1 !== null && d2 === null) return -1;
        if (d1 !== null && d2 !== null) {
            return t.sort.dir === "asc" ? d1 - d2 : d2 - d1;
        }

        // =============================
        // SORT NUMBER
        // =============================
        const n1 = parseFloat(v1.replace(/,/g, "").replace('%',''));
        const n2 = parseFloat(v2.replace(/,/g, "").replace('%',''));

        if (!isNaN(n1) && !isNaN(n2)) {
            return t.sort.dir === "asc" ? n1 - n2 : n2 - n1;
        }

        // =============================
        // SORT TEXT (fallback สุดท้าย)
        // =============================
        return t.sort.dir === "asc"
            ? v1.localeCompare(v2, 'th', { sensitivity: "base" })
            : v2.localeCompare(v1, 'th', { sensitivity: "base" });
    });

    reorderDOM(tableKey);
}

function sortByDatePart(part) {
    const { table, col } = openFilter;
    if (!table || col == null) return;

    tables[table].sort = {
        col: String(col),
        dir: part // day | month | year
    };

    applySort(table);
    renderTable(table);
    closeColumnFilterModal();
}




/* =====================================================
   RENDER TABLE
===================================================== */
function renderTable(tableKey) {
    const t = tables[tableKey];
    const rows = t.visibleRows;

    t.allRows.forEach(r => r.style.display = "none");

    if (tableKey === "main") {
        updateRowsPerPageDropdown(); // ⭐⭐⭐

        const start = (t.currentPage - 1) * t.rowsPerPage;
        const end = start + t.rowsPerPage;

        rows.slice(start, end).forEach(r => r.style.display = "");
        renderPagination();
    } else {
        rows.forEach(r => r.style.display = "");
    }

    updateIcons(tableKey);
}




/* =====================================================
   PAGINATION (MAIN ONLY)
===================================================== */
function renderPagination() {
    const t = tables.main;
    const totalRows = t.visibleRows.length;
    const totalPages = Math.max(1, Math.ceil(totalRows / t.rowsPerPage));

    // ป้องกัน currentPage เกิน
    if (t.currentPage > totalPages) t.currentPage = totalPages;

    const summaryEl = document.getElementById("paginationSummaryList");

    // ✅ กรณีไม่มีข้อมูลเลย
    if (totalRows === 0) {
        summaryEl.innerHTML =
            `แสดง <span class="text-indigo-600 font-semibold">0</span>
             จากทั้งหมด <span class="font-semibold">0</span> รายการ`;

        document.getElementById("prevPageBtnList").disabled = true;
        document.getElementById("nextPageBtnList").disabled = true;
        document.getElementById("pageNumbersList").innerHTML = "";
        return;
    }

    // ✅ คำนวณจากแถวจริงในตาราง
    const start = (t.currentPage - 1) * t.rowsPerPage + 1;
    const end = Math.min(start + t.rowsPerPage - 1, totalRows);

    summaryEl.innerHTML =
        `แสดง <span class="text-indigo-600 font-semibold">${start}-${end}</span>
         จากทั้งหมด <span class="font-semibold">${totalRows}</span> รายการ`;

    // prev / next
    document.getElementById("prevPageBtnList").disabled = t.currentPage === 1;
    document.getElementById("nextPageBtnList").disabled = t.currentPage === totalPages;

    // page numbers
    const container = document.getElementById("pageNumbersList");
    container.innerHTML = "";

    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement("button");
        btn.innerText = i;
        btn.className =
            "w-10 h-10 rounded-xl font-sarabun text-sm transition-all " +
            (i === t.currentPage
                ? "bg-indigo-600 text-white shadow-md"
                : "bg-white text-gray-600 hover:bg-indigo-50");

        btn.onclick = () => goToPage(i);
        container.appendChild(btn);
    }
}

function updateRowsPerPageOptions() {
    const t = tables.main;
    const totalRows = t.visibleRows.length;
    const select = document.getElementById("rowsPerPageList");

    if (!select) return;

    [...select.options].forEach(opt => {
        const val = parseInt(opt.value);

        if (val > totalRows && totalRows !== 0) {
            opt.disabled = true;
        } else {
            opt.disabled = false;
        }
    });

    // ถ้า rowsPerPage มากกว่าข้อมูลจริง → ปรับอัตโนมัติ
    if (t.rowsPerPage > totalRows && totalRows > 0) {
        t.rowsPerPage = totalRows;
        select.value = totalRows;
        t.currentPage = 1;
    }
}

function updateRowsPerPageDropdown() {
    const t = tables.main;
    const select = document.getElementById("rowsPerPageList");
    if (!select) return;

    const total = t.visibleRows.length;
    const current = t.rowsPerPage;

    select.innerHTML = "";

    // ✅ กรณีไม่มีข้อมูล
    if (total === 0) {
        select.innerHTML = `<option value="10">ทั้งหมด (0 แถว)</option>`;
        t.rowsPerPage = 10;      
        t.currentPage = 1;
        return;
    }

    const options = [10, 20];

    options.forEach(v => {
        if (v < total) {
            select.innerHTML += `
                <option value="${v}" ${current === v ? "selected" : ""}>
                    ${v} รายการ
                </option>
            `;
        }
    });

    select.innerHTML += `
        <option value="${total}" ${current === total ? "selected" : ""}>
            ทั้งหมด (${total} แถว)
        </option>
    `;

    // ✅ FIX เพิ่มความปลอดภัย
    if (t.rowsPerPage <= 0) {
        t.rowsPerPage = Math.min(10, total);
        t.currentPage = 1;
    }
}


function goToPage(p) {
    tables.main.currentPage = p;
    renderTable("main");
}

/* =====================================================
   ICON UPDATE
===================================================== */
function updateIcons(tableKey) {
    const t = tables[tableKey];

    document.querySelectorAll(`.filter-icon[data-table="${tableKey}"]`)
        .forEach(icon => {
            const col = icon.dataset.col;


            if (t.sort.col === col) {
                icon.innerHTML = t.sort.dir === "asc" ? ICONS.sortAsc : ICONS.sortDesc;
            } else if (t.filters[col]) {
                icon.innerHTML = ICONS.filter;
            } else {
                icon.innerHTML = ICONS.normal;
            }
        });
}

/* =====================================================
   HELPERS
===================================================== */
function getCellValue(row, colKey, toLower = true) {
    const cell = row.querySelector(`td[data-col="${colKey}"]`);
    if (!cell) return "";

    let value = "";

    const select = cell.querySelector("select");
    const input  = cell.querySelector("input");

    if (select) {
        const opt = select.options[select.selectedIndex];

        // ⭐ ใช้ text ที่แสดงจริง
        if (opt && opt.textContent) {
            value = opt.textContent.trim();
        } else {
            value = cell.textContent.trim();
        }

    } else if (input && input.type !== "hidden") {
        // ⭐ input ที่ user เห็นจริง
        value = input.value.trim();

    } else {
        // ⭐ fallback สุดท้าย
        value = cell.textContent.trim();
    }

    return toLower ? value.toLowerCase() : value;
}




/* =====================================================
   FILTER ICON CLICK
===================================================== */
document.addEventListener("click", e => {
    const icon = e.target.closest(".filter-icon");
    if (!icon) return;

    e.stopPropagation();
    openColumnFilter(icon.dataset.table, icon.dataset.col, icon);

});

/* =====================================================
   CLOSE MODAL ON OUTSIDE
===================================================== */
document.addEventListener("mousedown", e => {
    const modal = document.getElementById("column-filter-modal");
    if (!modal || modal.classList.contains("hidden")) return;

    if (!document.getElementById("column-filter-content").contains(e.target))
        closeColumnFilterModal();
});

document.addEventListener("keydown", e => {
    const modal = document.getElementById("column-filter-modal");
    if (!modal || modal.classList.contains("hidden")) return;
    if (document.activeElement.id !== "column-filter-search") return;

    if (e.key === "Enter") {
        e.preventDefault();
        applyColumnFilter();
    }

    if (e.key === "Escape") {
        e.preventDefault();
        closeColumnFilterModal();
    }
});


function changeRowsPerPage(value) {
    const v = parseInt(value);
    if (!v) return;

    tables.main.rowsPerPage = v;
    tables.main.currentPage = 1;
    renderTable("main");
}


</script>








    <script>
        /**
         * 1. ฟังก์ชันดึงแถวที่แสดงอยู่จริง
         * เช็คทั้ง display: none และ offsetHeight เพื่อความแม่นยำหลังจาก Filter
         */
        function getVisibleRows() {
            return Array.from(document.querySelectorAll('#tableBody tr')).filter(row => {
                const style = window.getComputedStyle(row);
                return style.display !== 'none' && style.visibility !== 'hidden' && row.offsetHeight > 0;
            });
        }

        /**
         * 2. ฟังก์ชันคำนวณและอัปเดตยอดสรุป
         */
        function updateAllColumnSummaries() {
            const visibleRows = getVisibleRows();

            document.querySelectorAll('[data-summary-field]').forEach(el => {
                const fieldName = el.dataset.summaryField;
                const type = el.dataset.type;

                let sum = 0;
                let count = 0;

                visibleRows.forEach(row => {
                    // ค้นหา td ที่มี data-col ตรงกับ field ที่ต้องการสรุป
                    const cell = row.querySelector(`td[data-col="${fieldName}"]`);
                    if (cell) {
                        const input = cell.querySelector('input');
                        const rawValue = input ? input.value : cell.innerText;

                        // ล้างค่าคอมม่าและ % ออกก่อนคำนวณ
                        const cleanValue = parseFloat(String(rawValue).replace(/,/g, '').replace('%',
                            '')) || 0;

                        sum += cleanValue;
                        if (String(rawValue).trim() !== '') {
                            count++;
                        }
                    }
                });

                // แสดงผลลัพธ์ตามประเภท
                if (type === 'money') {
                    el.innerText = sum.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                } else if (type === 'text') {
                    el.innerText = count.toLocaleString();
                } else if (type === 'avg') {
                    const avg = count > 0 ? sum / count : 0;
                    el.innerText = avg.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) + ' %';
                }
            });
        }

        /**
         * 3. ระบบตรวจจับการเปลี่ยนแปลงของตาราง (หัวใจสำคัญ)
         * เมื่อมีการกด OK ใน Filter และแถวถูกซ่อน/แสดง ฟังก์ชันนี้จะทำงานทันที
         */
        const summaryObserver = new MutationObserver((mutations) => {
            // ใช้ debounce เล็กน้อยเพื่อไม่ให้รันซ้ำซ้อนเกินไปในเสี้ยววินาที
            clearTimeout(window.summaryTimeout);
            window.summaryTimeout = setTimeout(() => {
                updateAllColumnSummaries();
            }, 50);
        });

        document.addEventListener('DOMContentLoaded', () => {
            const tableTarget = document.getElementById('tableBody');
            if (tableTarget) {
                // ตรวจสอบการเปลี่ยน Attributes (เช่น style="display:none") และการเพิ่ม/ลบแถว
                summaryObserver.observe(tableTarget, {
                    attributes: true,
                    childList: true,
                    subtree: true,
                    attributeFilter: ['style', 'class']
                });

                // ดักจับกรณีมีการพิมพ์ตัวเลขสดๆ ในตาราง
                tableTarget.addEventListener('input', updateAllColumnSummaries);
            }

            // รันครั้งแรกตอนโหลดหน้า
            updateAllColumnSummaries();
        });
    </script>

@endsection
