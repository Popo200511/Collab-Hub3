@extends('layouts.user')

@section('title', '09 Infinera Installation Service')

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

        .date-wrapper {
            position: relative;
        }

        .date-wrapper .date-input {
            padding-right: 2.4rem;
        }

        .date-icon {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6b7280;
            transition: color 0.2s ease, transform 0.2s ease;
        }


        .date-icon:hover {
            color: #2563eb;
            transform: translateY(-50%) scale(1.1);
        }

        .date-icon.disabled {
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: none;
        }

        .date-empty,
        .date-input:placeholder-shown {
            color: #9ca3af;
        }

        .fp-footer {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            padding: 6px;
            border-top: 1px solid #e5e7eb;
        }

        .fp-btn {
            flex: 1;
            font-size: 12px;
            padding: 4px 0;
            border-radius: 4px;
            cursor: pointer;
            border: 1px solid #d1d5db;
            background: #f9fafb;
        }

        .fp-btn:hover {
            background: #e5e7eb;
        }

        .fp-today {
            color: #2563eb;
            font-weight: 600;
        }

        .fp-clear {
            color: #dc2626;
        }

        /* container ของวันที่ (ตัวการหลัก) */
        .flatpickr-days .dayContainer {
            min-width: 238px;
            max-width: 238px;
        }

        /* ===== Flatpickr PERFECT ALIGN ===== */
        .flatpickr-calendar {
            font-size: 11px;
            width: 238px !important;
        }

        /* แถวชื่อวัน */
        .flatpickr-weekdays {
            display: flex;
        }

        .flatpickr-weekday {
            flex: 0 0 34px;
            max-width: 34px;
            text-align: center;
            font-size: 10px;
        }

        /* container วันที่ */
        .flatpickr-days {
            display: flex;
        }

        .flatpickr-days .dayContainer {
            min-width: 238px;
            max-width: 238px;
        }

        /* ช่องวันที่ */
        .flatpickr-day {
            flex: 0 0 34px;
            max-width: 34px;
            height: 26px;
            line-height: 26px;
            font-size: 11px;
        }
    </style>




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


    <!-- Main Content -->
    <main class="flex-1 bg-gray-100 overflow-y-auto">

        <div class="flex justify-between items-center bg-white p-4 rounded-xl mb-5 shadow-md ">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 w-full items-stretch">

                <!-- Summary -->
                <div class="bg-white p-2 rounded-xl shadow-md min-h-[60px]">
                    <h3 class="text-sm font-sarabun text-gray-500 mb-2">Added Job Total</h3>
                    <div class="text-4xl font-sarabun text-blue-600 text-center">50</div>
                    <div class="text-sm text-gray-500 mt-1 text-center">
                        Completed: <span class="font-sarabun">90</span>
                    </div>
                </div>

                <!-- Reject -->
                <div class="bg-white p-2 rounded-xl shadow-md min-h-[60px]">
                    <h3 class="text-sm font-sarabun text-gray-500 mb-2">Reject</h3>
                    <div class="text-4xl font-sarabun text-red-600 text-center">70</div>
                </div>

                <!-- Pending -->
                <div class="bg-white p-2 rounded-xl shadow-md min-h-[60px]">
                    <h3 class="text-sm font-sarabun text-gray-500 mb-2 ">Pending</h3>
                    <div class="text-4xl font-sarabun text-orange-400 text-center">90</div>
                </div>

                <!-- Approved -->
                <div class="bg-white p-2 rounded-xl shadow-md min-h-[60px]">
                    <h3 class="text-sm font-sarabun text-gray-500 mb-2 ">Approved</h3>
                    <div class="text-4xl font-sarabun text-green-600 text-center">80</div>
                </div>

            </div>


        </div>


        <div class="bg-white p-4 rounded-xl shadow-md">

            <!-- Container ปุ่ม -->
            <div class="container mx-auto ">
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
                    left: 173.5px;
                    width: 120px;
                    z-index: 50;
                    background: white;
                }

                .sticky-col-3 {
                    position: sticky;
                    left: 303.9px;
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


            <!-- Modal Manage Permissions-->
            <form action="{{ route('permissions.save_09', $projectCode) }}" method="POST">
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
                                                    <span>Estimated<br>Material Cost</span>

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


                                            <th class="sticky top-0 border px-1 w-[130px]" style="background-color: blue">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Estimated<br>Gross Profit Margin</span>

                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="10">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>col1</span>

                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="11">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col2</span>

                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="12">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[140px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col3</span>

                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="13">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col4</span>

                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="14">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>


                                            <th class="sticky top-0 border px-1 w-[120px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col5</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="15">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[140px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col6</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="16">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col7</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="17">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col8</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="18">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col9</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="19">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col10</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="20">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col11</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="21">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col12</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="22">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col13</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="23">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col14</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="24">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col15</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="25">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col16</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="26">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col17</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="27">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col18</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="28">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col19</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="29">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col20</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="30">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col21</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="31">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col22</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="32">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col23</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="33">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <th class="sticky top-0 border px-1 w-[120px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col24</span>
                                                    <span
                                                        class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                        data-table="permission" data-col="34">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>


                                            <!-- Col25 -->

                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col25</span>
                                                    <span class="filter-icon cursor-pointer" data-col="35"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col26 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col26</span>
                                                    <span class="filter-icon cursor-pointer" data-col="36"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col27 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col27</span>
                                                    <span class="filter-icon cursor-pointer" data-col="37"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col28 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col28</span>
                                                    <span class="filter-icon cursor-pointer" data-col="38"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col29 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col29</span>
                                                    <span class="filter-icon cursor-pointer" data-col="39"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col30 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col30</span>
                                                    <span class="filter-icon cursor-pointer" data-col="40"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col31 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col31</span>
                                                    <span class="filter-icon cursor-pointer" data-col="41"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col32 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col32</span>
                                                    <span class="filter-icon cursor-pointer" data-col="42"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

                                            <!-- Col33 -->
                                            <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                                <div class="flex items-center justify-center gap-2">
                                                    <span>Col33</span>
                                                    <span class="filter-icon cursor-pointer" data-col="43"
                                                        data-table="permission">
                                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                    </span>
                                                </div>
                                            </th>

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
                                                    class="sticky-col-3  z-[40] group-hover:bg-red-100 px-2 transition">
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

                /* cell ปกติ */
                .truncate-cell {
                    max-width: 100px;
                    /* กำหนดความกว้างคอลัมน์ */
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    cursor: pointer;
                    position: relative;
                }

                /* ตอนขยาย */
                .truncate-cell.expanded {
                    white-space: normal;
                    overflow: visible;
                    background: #fff;
                    z-index: 50;
                    box-shadow: 0 8px 24px rgba(0, 0, 0, .15);
                    padding: 8px;
                    border-radius: 8px;
                    min-width: 300px;
                }

                .truncate-cell.expanded {
                    position: absolute;
                    white-space: normal;
                    width: 400px;
                    background: #fff;
                    z-index: 100;
                }
            </style>


            <!-- ตารางข้อมูลหลัก โปรเจค-->
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

                <div class="table-container relative overflow-x-auto h-[395px] mt-2 font-sarabun">

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
                                        1 => ['type' => 'text'],
                                        2 => ['type' => 'date'],
                                        3 => ['type' => 'date'],
                                        4 => ['type' => 'date'],
                                        5 => ['type' => 'date'],
                                        6 => ['type' => 'text'],
                                        7 => ['type' => 'text'],

                                        // Revenue Detail
                                        8 => ['type' => 'date'],
                                        9 => ['type' => 'date'],
                                        10 => ['type' => 'money'],
                                        11 => ['type' => 'money'],
                                        12 => ['type' => 'money'],
                                        13 => ['type' => 'money'],
                                        14 => ['type' => 'money'],

                                        // Service Cost Detail
                                        15 => ['type' => 'money'],
                                        16 => ['type' => 'money'],
                                        17 => ['type' => 'money'],
                                        18 => ['type' => 'money'],
                                        19 => ['type' => 'money'],
                                        20 => ['type' => 'money'],
                                        21 => ['type' => 'money'],
                                        22 => ['type' => 'text'],

                                        // Other cost Detail
                                        23 => ['type' => 'money'],
                                        24 => ['type' => 'money'],
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
                                        1 => 'Go/NoGo',
                                        2 => 'Survey Date',
                                        3 => 'Proposal Submitted by Subcon',
                                        4 => 'Started Date',
                                        5 => 'Finished Date',
                                        6 => 'FAT Doc Submitted by Subcon',
                                        7 => 'Remark',

                                        // Revenue (เหลือง)
                                        8 => 'Proposal Submitted Date',
                                        9 => 'Quotation Submitted Date',
                                        10 => 'Total PO',
                                        11 => 'Total Invoice',
                                        12 => '%Invoice',
                                        13 => 'Balanced PO',
                                        14 => 'Pending PO',

                                        // Billing (ฟ้า)
                                        15 => 'Total PR',
                                        16 => 'Pending PR',
                                        17 => 'Total WO',
                                        18 => 'Pending WO',
                                        19 => 'Total Billing',
                                        20 => 'Pending Billing',
                                        21 => 'Total Invoice',
                                        22 => 'Pending Invoice',

                                        // Other (เขียว)
                                        23 => 'Other cost',
                                        24 => 'Advance',
                                    ];

                                    // ===============================
                                    // Background colors (INLINE)
                                    // ===============================
                                    $columnBgColors = [
                                        // เทา
                                        1 => '#162456',
                                        2 => '#162456',
                                        3 => '#162456',
                                        4 => '#162456',
                                        5 => '#162456',
                                        6 => '#162456',
                                        7 => '#162456',

                                        // เหลือง
                                        8 => '#eab308',
                                        9 => '#eab308',
                                        10 => '#eab308',
                                        11 => '#eab308',
                                        12 => '#eab308',
                                        13 => '#eab308',
                                        14 => '#eab308',

                                        // ฟ้า
                                        15 => '#0ea5e9',
                                        16 => '#0ea5e9',
                                        17 => '#0ea5e9',
                                        18 => '#0ea5e9',
                                        19 => '#0ea5e9',
                                        20 => '#0ea5e9',
                                        21 => '#0ea5e9',
                                        22 => '#0ea5e9',

                                        // เขียว
                                        23 => '#16a34a',
                                        24 => '#16a34a',
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

                                    <td class="truncate-cell" onclick="toggleExpand(this)" data-col="Job_Description_PJ">
                                        {{ $item->Job_Description_PJ }}</td>
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
                                            1 => ['type' => 'select', 'options' => ['Go', 'NoGo']],
                                            2 => ['type' => 'date'],
                                            3 => ['type' => 'date'],
                                            4 => ['type' => 'date'],
                                            5 => ['type' => 'date'],
                                            6 => ['type' => 'text'],
                                            7 => ['type' => 'text'],

                                            // Revenue Detail
                                            8 => ['type' => 'date'],
                                            9 => ['type' => 'date'],
                                            10 => ['type' => 'money'],
                                            11 => ['type' => 'money'],
                                            12 => ['type' => 'money'],
                                            13 => ['type' => 'money'],
                                            14 => ['type' => 'money'],

                                            // Service Cost Detail
                                            15 => ['type' => 'money'],
                                            16 => ['type' => 'money'],
                                            17 => ['type' => 'money'],
                                            18 => ['type' => 'money'],
                                            19 => ['type' => 'money'],
                                            20 => ['type' => 'money'],
                                            21 => ['type' => 'money'],
                                            22 => ['type' => 'text'],

                                            // Other cost Detail
                                            23 => ['type' => 'money'],
                                            24 => ['type' => 'money'],
                                        ];
                                    @endphp

                                    @for ($i = 1; $i <= 50; $i++)
                                        @php
                                            $col = "col$i";
                                            $config = $columnConfig[$i] ?? ['type' => 'text'];

                                            $visibility = $permissions[Auth::id()]->$col ?? 'invisible';
                                            $isRead = $visibility === 'read';
                                            $isInvisible = $visibility === 'invisible';

                                            // กำหนดค่า $value ตามเงื่อนไขของคอลัมน์ 15 และ 17
                                            if ($i === 15) {
                                                $value = $item->Amount;
                                            } elseif ($i === 17) {
                                                $value = $item->wo_amount;
                                            } else {
                                                $value = $item->$col;
                                            }
                                        @endphp





                                        <td data-col="col{{ $i }}" class="col-{{ $i }}"
                                            style="{{ $isInvisible ? 'display:none;' : '' }}"
                                            title="{{ $value }}">

                                            {{-- TEXT --}}
                                            @if ($config['type'] === 'text')
                                                <input type="text"
                                                    class="excel-input {{ $isRead ? 'readonly-cell' : '' }}"
                                                    value="{{ $value }}" data-id="{{ $item->Refcode_PJ }}"
                                                    data-field="{{ $col }}"
                                                    {{ $isRead ? 'readonly tabindex=-1' : '' }}>

                                                {{-- DATE --}}
                                            @elseif ($config['type'] === 'date')
                                                <div class="date-wrapper">
                                                    <input type="text"
                                                        class="excel-input date-input {{ empty($value) ? 'date-empty' : '' }} {{ $isRead ? 'readonly-cell' : '' }}"
                                                        value="{{ $value ? \Carbon\Carbon::parse($value)->format('d-m-Y') : '' }}"
                                                        placeholder="DD-MMM-YYYY" inputmode="numeric"
                                                        pattern="\d{2}-\d{2}-\d{4}" data-id="{{ $item->Refcode_PJ }}"
                                                        data-field="{{ $col }}"
                                                        {{ $isRead ? 'readonly tabindex=-1' : '' }}>

                                                    <span class="date-icon {{ $isRead ? 'disabled' : '' }}">
                                                        <i class="fa-regular fa-calendar-days"></i>
                                                    </span>
                                                </div>

                                                {{-- MONEY --}}
                                            @elseif ($config['type'] === 'money')
                                                <div class="relative flex items-center group w-full">

                                                    @if ($i === 15 || $i === 17)
                                                        <a href="{{ url('/ERP/payment/home') }}?search={{ $item->Refcode_PJ }}"
                                                            target="_blank"
                                                            class="w-full text-end text-blue-600 underline cursor-pointer pr-2 block"
                                                            title="คลิกเพื่อดู Timeline ของ {{ $item->Refcode_PJ }}">
                                                            {{ is_numeric($value) ? number_format($value, 2) : $value }}
                                                        </a>
                                                    @else
                                                        <input type="text"
                                                            class="excel-input money-input text-end
            {{ $isRead || ($config['calculated'] ?? false) ? 'readonly-cell' : '' }}"
                                                            value="{{ is_numeric($value) ? number_format($value, 2) : $value }}"
                                                            data-id="{{ $item->Refcode_PJ }}"
                                                            data-field="{{ $col }}"
                                                            {{ $isRead || ($config['calculated'] ?? false) ? 'readonly' : '' }}>
                                                    @endif

                                                </div>

                                                {{-- SELECT --}}
                                            @elseif ($config['type'] === 'select')
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
                class="mt-4 flex flex-col lg:flex-row items-center justify-between space-y-4 lg:space-y-0 p-5 bg-white rounded-xl border border-gray-200 shadow-sm transition-all duration-300">

                <div class="flex items-center space-x-3 order-2 lg:order-1">
                    <label for="rowsPerPageList"
                        class="font-sarabun text-xs font-medium tracking-wide text-gray-600">แสดงรายการ:</label>
                    <div class="relative">
                        <select id="rowsPerPageList" onchange="changeRowsPerPage(this.value)"
                            class="block py-2 pl-4 pr-10 border border-gray-200 rounded-xl text-xs font-sarabun bg-gray-50 cursor-pointer appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                        </select>
                        {{-- Custom Arrow Icon --}}
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                </div>

                <nav class="flex items-center space-x-2 order-1 lg:order-2" aria-label="Pagination">
                    {{-- Previous Button --}}
                    <button id="prevPageBtnList" onclick="goToPage(tables.main.currentPage - 1)"
                        class="pagination-btn group flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200 text-gray-500 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all duration-300 disabled:opacity-30 disabled:pointer-events-none shadow-sm">
                        <i class="fa-solid fa-chevron-left text-xs transition-transform group-hover:-translate-x-0.5"></i>
                    </button>

                    {{-- Page Numbers Container --}}
                    <div id="pageNumbersList" class="flex items-center space-x-1">
                        {{-- ตัวอย่างปุ่ม Active --}}
                        <button
                            class="w-10 h-10 rounded-xl bg-indigo-600 text-white font-sarabun text-sm shadow-md shadow-indigo-200">1</button>
                        <button
                            class="w-10 h-10 rounded-xl bg-white text-gray-600 font-sarabun text-sm hover:bg-indigo-50 transition-all">2</button>
                        <button
                            class="w-10 h-10 rounded-xl bg-white text-gray-600 font-sarabun text-sm hover:bg-indigo-50 transition-all">3</button>
                    </div>

                    {{-- Next Button --}}
                    <button id="nextPageBtnList" onclick="goToPage(tables.main.currentPage + 1)"
                        class="pagination-btn group flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200 text-gray-500 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all duration-300 disabled:opacity-30 disabled:pointer-events-none shadow-sm">
                        <i class="fa-solid fa-chevron-right text-xs transition-transform group-hover:translate-x-0.5"></i>
                    </button>
                </nav>

                <div class="order-3 text-right">
                    <span id="paginationSummaryList"
                        class="text-xs font-sarabun text-gray-500 bg-gray-100 px-4 py-2 rounded-full">
                        แสดง <span class="text-indigo-600 font-sarabun">1-10</span> จากทั้งหมด <span
                            class="text-gray-900 font-sarabun">15</span> รายการ
                    </span>
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
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.date-wrapper').forEach(wrapper => {
                const input = wrapper.querySelector('.date-input');
                const icon = wrapper.querySelector('.date-icon');

                toggleDateValueStyle(input);

                if (input.hasAttribute('readonly')) return;

                const fp = flatpickr(input, {
                    dateFormat: 'd-m-Y',
                    allowInput: true,
                    clickOpens: false,
                    disableMobile: true,
                    locale: {
                        firstDayOfWeek: 1
                    },

                    onChange: function(selectedDates, dateStr) {
                        if (dateStr) {
                            input.classList.remove('date-empty');
                            input.classList.add('has-value');
                        } else {
                            input.classList.add('date-empty');
                            input.classList.remove('has-value');
                        }
                    },
                    onReady: function(_, __, instance) {
                        const footer = document.createElement('div');
                        footer.className = 'fp-footer';

                        const btnToday = document.createElement('button');
                        btnToday.type = 'button';
                        btnToday.innerHTML = '<i class="fa-solid fa-calendar-check"></i> Today';
                        btnToday.className = 'fp-btn fp-today';

                        const btnClear = document.createElement('button');
                        btnClear.type = 'button';
                        btnClear.innerHTML = '<i class="fa-solid fa-trash-can"></i> Clear';
                        btnClear.className = 'fp-btn fp-clear';

                        footer.appendChild(btnToday);
                        footer.appendChild(btnClear);
                        instance.calendarContainer.appendChild(footer);

                        btnToday.addEventListener('click', () => {
                            instance.setDate(new Date(), true);
                            input.classList.remove('date-empty');
                            toggleDateValueStyle(input);
                            instance.close();
                        });

                        btnClear.addEventListener('click', () => {
                            instance.clear();
                            input.value = '';
                            input.classList.add('date-empty');
                            toggleDateValueStyle(input);
                            instance.close();
                        });
                    },

                    onOpen: function(_, __, instance) {
                        input.classList.add('active-input');

                        instance._keyHandler = (e) => {
                            // Enter = Today
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                instance.setDate(new Date(), true);
                                input.classList.remove('date-empty');
                                toggleDateValueStyle(input);
                                instance.close();
                            }

                            // Esc = Close
                            if (e.key === 'Escape') {
                                e.preventDefault();
                                instance.close();
                            }
                        };

                        document.addEventListener('keydown', instance._keyHandler);
                    },

                    onClose: function(_, __, instance) {
                        input.classList.remove('active-input');

                        if (instance._keyHandler) {
                            document.removeEventListener('keydown', instance._keyHandler);
                            instance._keyHandler = null;
                        }
                    }
                });

                icon.addEventListener('click', () => fp.open());

                // ปรับปรุงการพิมพ์ให้รองรับ d-m-Y อัตโนมัติ (Optional)
                input.addEventListener('input', (e) => {
                    let v = e.target.value.replace(/\D/g, '').slice(0, 8);
                    if (v.length >= 2 && v.length < 4) v = v.slice(0, 2) + '-' + v.slice(2);
                    if (v.length >= 4) v = v.slice(0, 2) + '-' + v.slice(2, 4) + '-' + v.slice(4);
                    e.target.value = v;

                    toggleDateValueStyle(input);
                });
            });
        });

        function toggleDateValueStyle(input) {
            if (input.value && input.value.trim() !== '') {
                input.classList.add('has-value');
            } else {
                input.classList.remove('has-value');
            }
        }
    </script>

    <!-- ฟังชันสำหรับขยายข้อมูลที่ยาวลอยทับ column -->
    <script>
        /* ปิด cell ที่ขยาย เมื่อกด ESC */
        document.addEventListener("keydown", function(e) {
            if (e.key !== "Escape") return;

            document
                .querySelectorAll(".truncate-cell.expanded")
                .forEach(cell => cell.classList.remove("expanded"));
        });

        /* ปิด cell ที่ขยาย เมื่อคลิกนอก */
        document.addEventListener("mousedown", function(e) {
            const expandedCell = document.querySelector(".truncate-cell.expanded");
            if (!expandedCell) return;

            // ถ้าคลิกอยู่นอก cell ที่ขยาย → ปิด
            if (!expandedCell.contains(e.target)) {
                expandedCell.classList.remove("expanded");
            }
        });

        function toggleExpand(cell) {

            // ปิด cell อื่นก่อน
            document.querySelectorAll('.truncate-cell.expanded')
                .forEach(el => {
                    if (el !== cell) el.classList.remove('expanded');
                });

            cell.classList.toggle('expanded');
        }
    </script>

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
                            length: 0
                        }, (_, i) => i + 1)
                        // col 1–24 = read
                        // col 25–50 = invisible
                    },
                    project: {
                        normal: "write",
                        readonly: "read"
                    }
                },

                "Project Admin": {
                    cols: {
                        read: Array.from({
                            length: 0
                        }, (_, i) => i + 1)
                        // col 1–24 = read
                        // col 25–50 = invisible
                    },
                    project: {
                        normal: "write",
                        readonly: "read"
                    }
                },

                "Site Supervisor": {
                    cols: {
                        read: Array.from({
                            length: 0
                        }, (_, i) => i + 1)
                        // col 1–24 = read
                        // col 25–50 = invisible
                    },
                    project: {
                        normal: "read",
                        readonly: "read"
                    }
                },

                "Project Director": {
                    cols: {
                        read: Array.from({
                            length: 0
                        }, (_, i) => i + 1)
                        // col 1–24 = read
                        // col 25–50 = invisible
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

                    fetch("{{ route('newjob.inlineUpdate_09') }}", {
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
                let rowData = [];
                const cells = row.querySelectorAll("th, td");

                cells.forEach(cell => {
                    if (cell.offsetParent === null) return;

                    let value = "";

                    const input = cell.querySelector("input");
                    if (input) {
                        value = input.value;
                    } else {
                        value = cell.innerText.trim();
                    }

                    // ✅ ลบ comma
                    const raw = value.replace(/,/g, "");

                    // ✅ ถ้าเป็นตัวเลข → แปลงเป็น Number
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

            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(data);

            // ✅ บังคับ cell ที่เป็น number ให้เป็น Number format
            Object.keys(ws).forEach(cell => {
                if (cell[0] === "!") return;
                if (typeof ws[cell].v === "number") {
                    ws[cell].t = "n"; // number
                    ws[cell].z = "0.00"; // ไม่มี comma
                }
            });

            XLSX.utils.book_append_sheet(wb, ws, "Visible Data");
            XLSX.writeFile(wb, "09 Infinera Installation Service.xlsx");
        }
    </script>



    <!-- ฟังชั่น Pagination & Rows Per Page -->
    <script>
        // เพิ่มฟังก์ชันนี้เพื่อ initialize ให้สมบูรณ์
        function initializePagination() {
            const select = document.getElementById('rowsPerPageList');
            if (!select) return;
            
            // Clear existing options
            select.innerHTML = '';
            
            // เพิ่ม options แบบคงที่
            const options = [
                { value: 10, text: '10 รายการ' },
                { value: 20, text: '20 รายการ' },
                { value: 50, text: '50 รายการ' },
                { value: 100, text: '100 รายการ' }
            ];
            
            options.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt.value;
                option.textContent = opt.text;
                if (opt.value === 10) option.selected = true;
                select.appendChild(option);
            });
        }

        // เรียกใช้เมื่อ DOM โหลด
        document.addEventListener('DOMContentLoaded', function() {
            initializePagination();
            initTable("main");
        });
    </script>

 
    <!-- ฟังชั่น renderPagination -->
    <script>
        function renderPagination() {
            const t = tables.main;
            const totalRows = t.visibleRows.length;
            const totalPages = Math.max(1, Math.ceil(totalRows / t.rowsPerPage));
            
            // ป้องกัน currentPage เกิน
            if (t.currentPage > totalPages) t.currentPage = totalPages;
            
            const summaryEl = document.getElementById("paginationSummaryList");
            const prevBtn = document.getElementById("prevPageBtnList");
            const nextBtn = document.getElementById("nextPageBtnList");
            const pageContainer = document.getElementById("pageNumbersList");
            
            // กรณีไม่มีข้อมูล
            if (totalRows === 0) {
                if (summaryEl) {
                    summaryEl.innerHTML = `แสดง <span class="text-indigo-600 font-semibold">0</span> จากทั้งหมด <span class="font-semibold">0</span> รายการ`;
                }
                if (prevBtn) prevBtn.disabled = true;
                if (nextBtn) nextBtn.disabled = true;
                if (pageContainer) pageContainer.innerHTML = "";
                return;
            }
            
            // คำนวณช่วงที่แสดง
            const start = (t.currentPage - 1) * t.rowsPerPage + 1;
            const end = Math.min(start + t.rowsPerPage - 1, totalRows);
            
            if (summaryEl) {
                summaryEl.innerHTML = `แสดง <span class="text-indigo-600 font-semibold">${start}-${end}</span> จากทั้งหมด <span class="font-semibold">${totalRows}</span> รายการ`;
            }
            
            // Prev/Next buttons
            if (prevBtn) prevBtn.disabled = t.currentPage === 1;
            if (nextBtn) nextBtn.disabled = t.currentPage === totalPages;
            
            // สร้างปุ่มหมายเลขหน้า
            if (pageContainer) {
                pageContainer.innerHTML = "";
                
                // กำหนดช่วงหน้าที่จะแสดง (แสดง 5 หน้ารอบหน้าปัจจุบัน)
                let startPage = Math.max(1, t.currentPage - 2);
                let endPage = Math.min(totalPages, startPage + 4);
                
                // ปรับ startPage ถ้า endPage ไม่ถึง totalPages
                if (endPage - startPage < 4) {
                    startPage = Math.max(1, endPage - 4);
                }
                
                // ปุ่ม First page (ถ้าจำเป็น)
                if (startPage > 1) {
                    const firstBtn = createPageButton(1);
                    pageContainer.appendChild(firstBtn);
                    
                    if (startPage > 2) {
                        const dots = document.createElement('span');
                        dots.className = 'px-2 text-gray-400';
                        dots.textContent = '...';
                        pageContainer.appendChild(dots);
                    }
                }
                
                // ปุ่มหมายเลขหน้า
                for (let i = startPage; i <= endPage; i++) {
                    const btn = createPageButton(i);
                    pageContainer.appendChild(btn);
                }
                
                // ปุ่ม Last page (ถ้าจำเป็น)
                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) {
                        const dots = document.createElement('span');
                        dots.className = 'px-2 text-gray-400';
                        dots.textContent = '...';
                        pageContainer.appendChild(dots);
                    }
                    
                    const lastBtn = createPageButton(totalPages);
                    pageContainer.appendChild(lastBtn);
                }
            }
        }

        function createPageButton(pageNum) {
            const btn = document.createElement("button");
            btn.innerText = pageNum;
            btn.className = "w-10 h-10 rounded-xl font-sarabun text-sm transition-all " +
                (pageNum === tables.main.currentPage ?
                    "bg-indigo-600 text-white shadow-md" :
                    "bg-white text-gray-600 hover:bg-indigo-50 border border-gray-200");
            btn.onclick = () => goToPage(pageNum);
            return btn;
        }
    </script>


    <script>
        // แก้ไขฟังก์ชัน openColumnFilter ให้ทำงานถูกต้อง
function openColumnFilter(tableKey, colKey, icon) {
    const t = tables[tableKey];
    if (!t) {
        console.error("Table not found:", tableKey);
        return;
    }
    
    if (t.allRows.length === 0) {
        console.warn("Table has no rows:", tableKey);
        return;
    }
    
    openFilter = {
        table: tableKey,
        col: String(colKey)
    };
    openFilterColumn = colKey;
    
    const search = document.getElementById("column-filter-search");
    if (search) search.value = "";
    
    loadFilterValues(tableKey, colKey);
    showFilterModal(icon);
    
    setTimeout(() => {
        if (search) search.focus();
    }, 100);
}

// ปรับปรุง loadFilterValues
function loadFilterValues(tableKey, colIndex) {
    const t = tables[tableKey];
    const list = document.getElementById("column-filter-checkbox-list");
    if (!list) return;
    
    list.innerHTML = "";
    
    const selected = t.filters[colIndex] ?? [];
    originalFilterOrder = [];
    
    const values = new Set();
    
    // เก็บค่า unique จากทุกแถว
    t.allRows.forEach(row => {
        const v = getCellValue(row, colIndex, false);
        if (v !== null && v !== undefined) {
            values.add(v);
        }
    });
    
    // สร้าง checkboxes
    values.forEach(v => {
        const label = document.createElement("label");
        label.className = "flex items-center gap-2 py-1.5 cursor-pointer hover:bg-gray-50 rounded px-2";
        
        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.className = "filter-checkbox w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500";
        checkbox.value = v;
        checkbox.checked = selected.includes(v);
        
        const span = document.createElement("span");
        span.className = "text-xs font-sarabun text-gray-700";
        span.textContent = v === "" ? "(ว่าง)" : v;
        
        label.appendChild(checkbox);
        label.appendChild(span);
        list.appendChild(label);
        originalFilterOrder.push(label);
    });
}
    </script>



@endsection
