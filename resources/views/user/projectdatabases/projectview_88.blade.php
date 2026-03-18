@extends('layouts.user')

@section('title', '88 True New site Project')


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
        color: #ffffff;
    }
    .fp-footer {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        padding: 6px;
        border-top: 1px solid rgb(255, 255, 255);
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
    /* ===============================
                                 Utils
                                ================================ */
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



<!-- collab 88 calculation-->
<script>
    /* ===============================
                                                       Calculate col39 = col37 + col38
                                                       (Realtime / Safe cursor)
                                                    ================================ */
        $(document).on('input', '.money-input', function() {
            let row = $(this).closest('tr');

            let col37Raw = row.find('[data-field="col37"]').val();
            let col38Raw = row.find('[data-field="col38"]').val();


            /* 🔹 ถ้ายังพิมพ์ไม่จบ → STOP */
            let incomplete = [col37Raw, col38Raw].some(v =>
                v === '.' || (v && v.endsWith('.'))
            );

            if (incomplete) {
                return;
            }

            /* 🔹 parse เมื่อค่าพร้อม */
            let col37 = parseMoney(col37Raw);
            let col38 = parseMoney(col38Raw);

            let result = col37 + col38;

            let target = row.find('[data-field="col39"]');
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
                    <button type="button" id="exportToExcel" onclick="exportToExcel()" class="px-3 py-1.5 rounded-md font-sarabun text-sm text-white
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
                    <button onclick="openPermissionModal()" class="group flex items-center px-3 py-1.5 bg-white text-indigo-600 border border-indigo-200
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
                left: 208px;
                width: 120px;
                z-index: 50;
                background: white;
            }

            .sticky-col-3 {
                position: sticky;
                left: 365px;
                width: 140px;
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

        <!-- Modal Manage Permissions 88-->
        <form action="{{ route('permissions.save_88', $projectCode) }}" method="POST">

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
                        <div style="width: 17500px;">
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

                                        <th class="sticky top-0 border px-1 w-[120px]" style="background-color: green">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Estimated<br>Material Cost</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="6">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <th class="sticky top-0 border px-1 w-[130px]" style="background-color: green">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Estimated<br>Transportation Cost</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="7">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <th class="sticky top-0 border px-1 w-[120px]" style="background-color: green">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Estimated<br>Other Cost</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="8">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Read / invisible   -->
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

                                        <!-- Col 1-35  -->

                                        <!-- Owner Old Ste-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Owner Old Ste</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="11">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Site NAME_T-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Site NAME_T</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="12">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Go / Nogo-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Go / Nogo</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="13">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>



                                        <!-- Scope of Work-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Scope of Work</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="14">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Tower Actual -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Tower Actual</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="15">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- SAQ Survey Plan -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>SAQ Survey Plan</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="16">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- SAQ Survey Actual -->
                                        <th class="sticky top-0 border px-1 w-[110px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>SAQ Survey Actual</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="17">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- CR Plan -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>CR Plan</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="18">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- CR Actual -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>CR Actual</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="19">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- CR File -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>CR File</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="20">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- TSSR Plan -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>TSSR Plan</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="21">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- TSSR Actual -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>TSSR Actual</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="22">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- TSSR File -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>TSSR File</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="23">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- FEA Approve -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>FEA Approve</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="24">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Building Permit Plan -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Building<br>Permit Plan</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="25">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Building Permit Actual -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Building<br>Permit Actual</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="26">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Building Permit File -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Building<br>Permit File</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="27">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Building Permit Cost -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Building<br>Permit Cost</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="28">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Building Propose -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Building<br>Propose</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="29">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Civil Start Plan -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Civil Start Plan</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="30">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Civil Start Actual -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Civil Start Actual</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="31">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Tower Start Plan -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Tower Start Plan</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="32">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Tower Start Actual -->
                                        <th class="sticky top-0 border px-1 w-[110px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Tower Start Actual</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="33">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- AC Plan -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>AC Plan</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="34">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- AC Actual -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>AC Actual</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="35">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- AC Temp Cost -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>AC Temp Cost</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="36">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Meter No. -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Meter No.</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="37">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- User No. -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>User No.</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="38">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- PAT Plan -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>PAT Plan</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="39">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- PAT Actual -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>PAT Actual</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="40">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Clear Dif Plan -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Clear Dif Plan</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="41">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Clear Dif actual -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Clear Dif Actual</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="42">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- FAT Plan -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>FAT Plan</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="43">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- FAT Actual -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>FAT Actual</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="44">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Extra Work Detail -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Extra<br>Work Detail</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="45">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>


                                        <!-- สีเขียวอ่อน 36-47 -->

                                        <!-- Extra Work Propose -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Extra<br>Work Propose</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="46">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Extra Work Cost -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Extra<br>Work Cost</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="47">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- PBOQ Standard -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>PBOQ<br>Standard</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="48">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- FBOQ (Standard + Extra) -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>FBOQ<br>(Standard + Extra)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="49">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Quo. No. -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Quo. No.</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="50">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Quo. Propose -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Quo. Propose</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="51">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Quo. Approved -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Quo. Approved</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="52">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- PO No. -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>PO No.</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="53">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- PO Amount -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>PO Amount</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="54">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- PO Year -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>PO Year</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="55">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- PO Amount (Design)	 -->
                                        <th class="sticky top-0 border px-1 w-[120px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>PO Amount (Design)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="56">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- PO Amount (Civil)	 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>PO Amount (Civil)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="57">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>


                                        <!-- สีส้มอ่อน 48-54 -->

                                        <!-- Project ID	 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Project ID</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="58">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Ref. No. (Design)	 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Ref. No. (Design)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="59">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Ref. No. (Civil) -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Ref. No. (Civil)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="60">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Current Step (Design) -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Current<br>Step (Design)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="61">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Current Step (Civil) -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Current<br>Step (Civil)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="62">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Upload FBOQ (Design) -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Upload<br>FBOQ (Design)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="63">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Upload FBOQ (Civil) -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Upload<br>FBOQ (Civil)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="64">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- สีเขียวอ่อน 55-67 -->

                                        <!-- INV 1st Date (Design) -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>INV 1st Date<br>(Design)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="65">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- INV 1st No. (Design) -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>INV 1st No.<br>(Design)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="66">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- INV 1st Price (Design) -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>INV 1st Price<br>(Design)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="67">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- INV 2nd Date (Design) -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>INV 2nd Date<br>(Design)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="68">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- INV 2nd No. (Design) -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>INV 2nd No.<br>(Design)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="69">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- INV 2nd Price (Design) -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>INV 2nd Price<br>(Design)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="70">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- INV 1st Date (Civil) -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>INV 1st Date<br>(Civil)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="71">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- INV 1st No. (Civil) -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>INV 1st No.<br>(Civil)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="72">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- INV 1st Price (Civil)	 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>INV 1st Price<br>(Civil)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="73">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--INV 2nd Date (Civil)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>INV 2nd Date<br>(Civil)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="74">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--	INV 2nd No. (Civil)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>INV 2nd No.<br>(Civil)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="75">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--INV 2nd Price (Civil)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>INV 2nd Price<br>(Civil)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="76">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--PO Remaining-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-lime-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>PO Remaining</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="77">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>


                                        <!-- สีเหลือง 68-95 -->

                                        <!--PR Propose in Email (SAQ)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>PR Propose<br>in Email (SAQ)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="78">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--PR Approved in Email (SAQ)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>PR Approved<br>in Email (SAQ)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="79">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--Billing1 Proposed in Email (SAQ)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Billing1 Proposed<br>in Email (SAQ)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="80">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--Billing1 Approved in Email (SAQ)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Billing1 Approved<br>in Email (SAQ)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="81">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--Billing2 Proposed in Email (SAQ)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Billing2 Proposed<br>in Email (SAQ)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="82">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--Billing2 Approved in Email (SAQ)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Billing2 Approved<br>in Email (SAQ)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="83">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--PR Propose in Email (CR)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>PR Propose<br>in Email (CR)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="84">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--PR Approved in Email (CR)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>PR Approved<br>in Email (CR)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="85">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--Billing1 Proposed in Email (CR)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Billing1 Proposed<br>in Email (CR)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="86">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--Billing1 Approved in Email (CR)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Billing1 Approved<br>in Email (CR)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="87">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--PR Propose in Email (Design)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>PR Propose<br>in Email (Design)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="88">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--PR Approved in Email (Design)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>PR Approved<br>in Email (Design)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="89">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--Billing1 Proposed in Email (Design)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Billing1 Proposed<br>in Email (Design)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="90">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--Billing1 Approved in Email (Design)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Billing1 Approved<br>in Email (Design)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="91">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--PR Propose in Email (Pile)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>PR Propose<br>in Email (Pile)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="92">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--PR Approved in Email (Pile)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>PR Approved<br>in Email (Pile)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="93">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--Billing1 Proposed in Email (Pile)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Billing1 Proposed<br>in Email (Pile)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="94">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--Billing1 Approved in Email (Pile)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Billing1 Approved<br>in Email (Pile)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="95">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--PR Propose in Email (Civil)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>PR Propose<br>in Email (Civil)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="96">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--PR Approved in Email (Civil)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>PR Approved<br>in Email (Civil)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="97">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--Billing1 Proposed in Email (Civil)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Billing1 Proposed<br>in Email (Civil)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="98">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--Billing1 Approved in Email (Civil)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Billing1 Approved<br>in Email (Civil)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="99">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--Billing2 Proposed in Email (Civil)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Billing2 Proposed<br>in Email (Civil)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="100">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--Billing2 Approved in Email (Civil)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Billing2 Approved<br>in Email (Civil)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="101">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--Billing3 Proposed in Email (Civil)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Billing3 Proposed<br>in Email (Civil)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="102">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--Billing3 Approved in Email (Civil)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Billing3 Approved<br>in Email (Civil)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="103">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--Billing4 Proposed in Email (Civil)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Billing4 Proposed<br>in Email (Civil)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="104">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!--Billing4 Approved in Email (Civil)-->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-amber-500 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Billing4 Approved<br>in Email (Civil)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="105">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>


                                        <!------------------------------------- Col96-Col120 ---------------------------------------->

                                        <!-- Col96 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col96</span>
                                                <span class="filter-icon cursor-pointer" data-col="106"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col97 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col97</span>
                                                <span class="filter-icon cursor-pointer" data-col="107"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col98 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col98</span>
                                                <span class="filter-icon cursor-pointer" data-col="108"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col99 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col99</span>
                                                <span class="filter-icon cursor-pointer" data-col="109"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col100 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col100</span>
                                                <span class="filter-icon cursor-pointer" data-col="110"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col101 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col101</span>
                                                <span class="filter-icon cursor-pointer" data-col="111"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col102 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col102</span>
                                                <span class="filter-icon cursor-pointer" data-col="112"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col103 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col103</span>
                                                <span class="filter-icon cursor-pointer" data-col="113"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col104 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col104</span>
                                                <span class="filter-icon cursor-pointer" data-col="114"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col105 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col105</span>
                                                <span class="filter-icon cursor-pointer" data-col="115"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col106 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col106</span>
                                                <span class="filter-icon cursor-pointer" data-col="116"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col107 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col107</span>
                                                <span class="filter-icon cursor-pointer" data-col="117"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col108 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col108</span>
                                                <span class="filter-icon cursor-pointer" data-col="118"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col109 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col109</span>
                                                <span class="filter-icon cursor-pointer" data-col="119"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col110 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col110</span>
                                                <span class="filter-icon cursor-pointer" data-col="120"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col111 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col111</span>
                                                <span class="filter-icon cursor-pointer" data-col="121"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col112 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col112</span>
                                                <span class="filter-icon cursor-pointer" data-col="122"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col113 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col113</span>
                                                <span class="filter-icon cursor-pointer" data-col="123"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col114 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col114</span>
                                                <span class="filter-icon cursor-pointer" data-col="124"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col115 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col115</span>
                                                <span class="filter-icon cursor-pointer" data-col="125"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col116 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col116</span>
                                                <span class="filter-icon cursor-pointer" data-col="126"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col117 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col117</span>
                                                <span class="filter-icon cursor-pointer" data-col="127"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col118 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col118</span>
                                                <span class="filter-icon cursor-pointer" data-col="128"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col119 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col119</span>
                                                <span class="filter-icon cursor-pointer" data-col="129"
                                                    data-table="permission">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <!-- Col120 -->
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Col120</span>
                                                <span class="filter-icon cursor-pointer" data-col="130"
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


                                                <option value="no" {{ isset($permissions[$user->id]) &&
                                                    $permissions[$user->id]->member_status === 'no' ? 'selected' : ''
                                                    }}>
                                                    No
                                                </option>

                                                <option value="yes" {{ isset($permissions[$user->id]) &&
                                                    $permissions[$user->id]->member_status === 'yes' ? 'selected' : ''
                                                    }}>
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
                                                <option value="" {{ !isset($permissions[$user->id]) ||
                                                    $permissions[$user->id]->project_role === null ? 'selected' : '' }}>
                                                    No
                                                </option>

                                                <option value="Project Manager" {{ isset($permissions[$user->id]) &&
                                                    $permissions[$user->id]->project_role === 'Project Manager'
                                                    ? 'selected'
                                                    : '' }}>
                                                    Project Manager
                                                </option>

                                                <option value="Project Admin" {{ isset($permissions[$user->id]) &&
                                                    $permissions[$user->id]->project_role === 'Project Admin'
                                                    ? 'selected'
                                                    : '' }}>
                                                    Project Admin
                                                </option>

                                                <option value="Site Supervisor" {{ isset($permissions[$user->id]) &&
                                                    $permissions[$user->id]->project_role === 'Site Supervisor'
                                                    ? 'selected'
                                                    : '' }}>
                                                    Site Supervisor
                                                </option>

                                                <option value="Project Director" {{ isset($permissions[$user->id]) &&
                                                    $permissions[$user->id]->project_role === 'Project Director'
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
                                            <select name="{{ $field }}_permission[{{ $user->id }}]"
                                                class="text-xs p-1 border rounded w-full bg-white project-permission"
                                                data-field="{{ $field }}" data-user-id="{{ $user->id }}">

                                                <option value="invisible" {{ isset($permissions[$user->id]) &&
                                                    ($permissions[$user->id]->$field ?? 'invisible') === 'invisible'
                                                    ? 'selected'
                                                    : '' }}>
                                                    Invisible
                                                </option>

                                                <option value="read" {{ isset($permissions[$user->id]) &&
                                                    ($permissions[$user->id]->$field ?? '') === 'read' ? 'selected' : ''
                                                    }}>
                                                    Read
                                                </option>

                                                {{-- ❌ ไม่ให้ Write สำหรับ Gross --}}
                                                @if (!in_array($field, $readOnlyFields))
                                                <option value="write" {{ isset($permissions[$user->id]) &&
                                                    ($permissions[$user->id]->$field ?? '') === 'write' ? 'selected' :
                                                    '' }}>
                                                    Write
                                                </option>
                                                @endif

                                            </select>
                                        </td>
                                        @endforeach



                                        <!-- Dynamic Columns -->
                                        @for ($i = 1; $i <= 120; $i++) @php $colIndex=10 + $i; @endphp <td
                                            data-col="{{ $colIndex }}" class="px-2">
                                            <select name="col{{ $i }}_permission[{{ $user->id }}]"
                                                class="text-xs p-1 border rounded w-full bg-white hover:bg-gray-50 dynamic-col"
                                                data-col="{{ $i }}" data-user-id="{{ $user->id }}">
                                                <option value="invisible" {{ isset($permissions[$user->id]) &&
                                                    $permissions[$user->id]->{"col$i"} === 'invisible' ? 'selected' : ''
                                                    }}>
                                                    Invisible</option>
                                                <option value="read" {{ isset($permissions[$user->id]) &&
                                                    $permissions[$user->id]->{"col$i"} === 'read' ? 'selected' : '' }}>
                                                    Read</option>
                                                <option value="write" {{ isset($permissions[$user->id]) &&
                                                    $permissions[$user->id]->{"col$i"} === 'write' ? 'selected' : '' }}>
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

            <div class="table-container relative overflow-x-auto h-[395px] font-sarabun">

                <table class="table min-w-max table-fixed border-separate border-spacing-0 font-sarabun
                            [--col-1:115px]
                            [--col-2:130px]" id="table">

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

                                 @php
                            $columnConfig = [
                            //1 => ['type' => 'select', 'options' => ['Go', 'NoGo']],
                            1 => ['type' => 'text'],
                            2 => ['type' => 'text'],
                            3 => ['type' => 'text'],
                            4 => ['type' => 'text'],
                            5 => ['type' => 'text'],

                            6 => ['type' => 'date'],
                            7 => ['type' => 'date'],
                            8 => ['type' => 'date'],
                            9 => ['type' => 'date'],
                            10 => ['type' => 'date'],
                            11 => ['type' => 'date'],
                            12 => ['type' => 'date'],
                            13 => ['type' => 'date'],
                            14 => ['type' => 'date'],
                            15 => ['type' => 'date'],
                            16 => ['type' => 'date'],
                            17 => ['type' => 'date'],

                            18 => ['type' => 'money'],
                            19 => ['type' => 'date'],
                            20 => ['type' => 'date'],
                            21 => ['type' => 'date'],
                            22 => ['type' => 'date'],
                            23 => ['type' => 'date'],
                            24 => ['type' => 'date'],
                            25 => ['type' => 'date'],

                            26 => ['type' => 'money'],
                            27 => ['type' => 'text'],
                            28 => ['type' => 'text'],
                            29 => ['type' => 'date'],
                            30 => ['type' => 'date'],
                            31 => ['type' => 'date'],
                            32 => ['type' => 'date'],
                            33 => ['type' => 'date'],
                            34 => ['type' => 'date'],
                            35 => ['type' => 'text'],

                            36 => ['type' => 'date'],
                            37 => ['type' => 'money'],
                            38 => ['type' => 'money'],
                            39 => ['type' => 'money', 'calculated' => true], // 39 = 37 + 38
                            40 => ['type' => 'text'],
                            41 => ['type' => 'date'],
                            42 => ['type' => 'date'],
                            43 => ['type' => 'text'],
                            44 => ['type' => 'money'],
                            45 => ['type' => 'text'],
                            46 => ['type' => 'money'],
                            47 => ['type' => 'money'],

                            48 => ['type' => 'text'],
                            49 => ['type' => 'text'],
                            50 => ['type' => 'text'],
                            51 => ['type' => 'text'],
                            52 => ['type' => 'text'],
                            53 => ['type' => 'date'],
                            54 => ['type' => 'date'],

                            55 => ['type' => 'date'],
                            56 => ['type' => 'text'],
                            57 => ['type' => 'money'],
                            58 => ['type' => 'date'],
                            59 => ['type' => 'text'],
                            60 => ['type' => 'money'],
                            61 => ['type' => 'date'],
                            62 => ['type' => 'text'],
                            63 => ['type' => 'money'],
                            64 => ['type' => 'date'],
                            65 => ['type' => 'text'],
                            66 => ['type' => 'money'],
                            67 => ['type' => 'money'],

                            68 => ['type' => 'date'],
                            69 => ['type' => 'date'],
                            70 => ['type' => 'date'],
                            71 => ['type' => 'date'],
                            72 => ['type' => 'date'],
                            73 => ['type' => 'date'],
                            74 => ['type' => 'date'],
                            75 => ['type' => 'date'],
                            76 => ['type' => 'date'],
                            77 => ['type' => 'date'],
                            78 => ['type' => 'date'],
                            79 => ['type' => 'date'],
                            80 => ['type' => 'date'],
                            81 => ['type' => 'date'],
                            82 => ['type' => 'date'],
                            83 => ['type' => 'date'],
                            84 => ['type' => 'date'],
                            85 => ['type' => 'date'],
                            86 => ['type' => 'date'],
                            87 => ['type' => 'date'],
                            88 => ['type' => 'date'],
                            89 => ['type' => 'date'],
                            90 => ['type' => 'date'],
                            91 => ['type' => 'date'],
                            92 => ['type' => 'date'],
                            93 => ['type' => 'date'],
                            94 => ['type' => 'date'],
                            95 => ['type' => 'date'],
                            ];
                            @endphp


							@for ($i = 1; $i <= 120; $i++)
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

                                    <span class="filter-icon cursor-pointer inline-flex items-center opacity-60
                   group-hover:opacity-100 transition-opacity" data-table="main" data-col="{{ $field }}"
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
                            <th class="whitespace-nowrap text-center border-b border-blue-900 group" style="background-color:blue;color:white;
           {{ $gpVisibility === 'invisible' ? 'display:none;' : '' }}">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="tracking-wide font-sarabun text-xs text-white/90">
                                        Estimated <br> Gross Profit
                                    </span>

                                    <span class="filter-icon cursor-pointer inline-flex items-center opacity-60
                   group-hover:opacity-100 transition-opacity" data-table="main" data-col="Estimated_Gross_Profit_PJ"
                                        data-type="money">
                                        @php $colIndex++; @endphp
                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                    </span>
                                </div>
                            </th>

                            {{-- ===== Estimated Gross Profit Margin ===== --}}
                            <th class="whitespace-nowrap text-center border-b border-blue-900 group" style="background-color:blue;color:white;
           {{ $gmVisibility === 'invisible' ? 'display:none;' : '' }}">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="tracking-wide font-sarabun text-xs text-white/90">
                                        Estimated <br> Gross Profit Margin
                                    </span>

                                    <span class="filter-icon cursor-pointer inline-flex items-center opacity-60
                   group-hover:opacity-100 transition-opacity" data-table="main" data-col="Estimated_Gross_Profit_Margin_PJ" 
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
                            // สีฟ้า
                            1 => 'Owner Old Ste',
                            2 => 'Site NAME_T',
                            3 => 'Go / Nogo',
                            4 => 'Scope of Work',
                            5 => 'Tower Actual',
                            6 => 'SAQ Survey Plan',
                            7 => 'SAQ Survey Actual',
                            8 => 'CR Plan',
                            9 => 'CR Actual',
                            10 => 'CR File',
                            11 => 'TSSR Plan',
                            12 => 'TSSR Actual',
                            13 => 'TSSR File',
                            14 => 'FEA Approve',
                            15 => 'Building Permit Plan',
                            16 => 'Building Permit Actual',
                            17 => 'Building Permit File',
                            18 => 'Building Permit Cost',
                            19 => 'Building Propose',
                            20 => 'Civil Start Plan',
                            21 => 'Civil Start Actual',
                            22 => 'Tower Start Plan',
                            23 => 'Tower Start Actual',
                            24 => 'AC Plan',
                            25 => 'AC Actual',
                            26 => 'AC Temp Cost',
                            27 => 'Meter No.',
                            28 => 'User No.',
                            29 => 'PAT Plan',
                            30 => 'PAT Actual',
                            31 => 'Clear Dif Plan',
                            32 => 'Clear Dif actual',
                            33 => 'FAT Plan',
                            34 => 'FAT Actual',
                            35 => 'Extra Work Detail',

                            // สีเขียวอ่อน
                            36 => 'Extra Work Propose',
                            37 => 'Extra Work Cost',
                            38 => 'PBOQ Standard',
                            39 => 'FBOQ <span style="color:red">(Standrad + Extra)</span>',
                            40 => 'Quo. No.',
                            41 => 'Quo. Propose',
                            42 => 'Quo. Approved',
                            43 => 'PO No.',
                            44 => 'PO Amount',
                            45 => 'PO Year',
                            46 => 'PO Amount (Design)',
                            47 => 'PO Amount (Civil)',

                            //สีส้มอ่อน
                            48 => 'Project ID',
                            49 => 'Ref. No. (Design)',
                            50 => 'Ref. No. (Civil)',
                            51 => 'Current Step (Design)',
                            52 => 'Current Step (Civil)',
                            53 => 'Upload FBOQ (Design)',
                            54 => 'Upload FBOQ (Civil)',

                            //สีเขียว
                            55 => 'INV 1st Date (Design)',
                            56 => 'INV 1st No. (Design)',
                            57 => 'INV 1st Price (Design)',
                            58 => 'INV 2nd Date (Design)',
                            59 => 'INV 2nd No. (Design)',
                            60 => 'INV 2nd Price (Design)',
                            61 => 'INV 1st Date (Civil)',
                            62 => 'INV 1st No. (Civil)',
                            63 => 'INV 1st Price (Civil)',
                            64 => 'INV 2nd Date (Civil)',
                            65 => 'INV 2nd No. (Civil)',
                            66 => 'INV 2nd Price (Civil)',
                            67 => 'PO Remaining',

                            //สีเหลือง
                            68 => 'PR Propose in Email (SAQ)',
                            69 => 'PR Approved in Email (SAQ)',
                            70 => 'Billing1 Proposed in Email (SAQ)',
                            71 => 'Billing1 Approved in Email (SAQ)',
                            72 => 'Billing2 Proposed in Email (SAQ)',
                            73 => 'Billing2 Approved in Email (SAQ)',
                            74 => 'PR Propose in Email (CR)',
                            75 => 'PR Approved in Email (CR)',
                            76 => 'Billing1 Proposed in Email (CR)',
                            77 => 'Billing1 Approved in Email (CR)',
                            78 => 'PR Propose in Email (Design)',
                            79 => 'PR Approved in Email (Design)',
                            80 => 'Billing1 Proposed in Email (Design)',
                            81 => 'Billing1 Approved in Email (Design)',
                            82 => 'PR Propose in Email (Pile)',
                            83 => 'PR Approved in Email (Pile)',
                            84 => 'Billing1 Proposed in Email (Pile)',
                            85 => 'Billing1 Approved in Email (Pile)',
                            86 => 'PR Propose in Email (Civil)',
                            87 => 'PR Approved in Email (Civil)',
                            88 => 'Billing1 Proposed in Email (Civil)',
                            89 => 'Billing1 Approved in Email (Civil)',
                            90 => 'Billing2 Proposed in Email (Civil)',
                            91 => 'Billing2 Approved in Email (Civil)',
                            92 => 'Billing3 Proposed in Email (Civil)',
                            93 => 'Billing3 Approved in Email (Civil)',
                            94 => 'Billing4 Proposed in Email (Civil)',
                            95 => 'Billing4 Approved in Email (Civil)',
                            ];

                            // ===============================
                            // Background colors (INLINE)
                            // ===============================
                            $columnBgColors = [
                            // ฟ้า 1-35
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
                            20 => '#0ea5e9',
                            21 => '#0ea5e9',
                            22 => '#0ea5e9',
                            23 => '#0ea5e9',
                            24 => '#0ea5e9',
                            25 => '#0ea5e9',
                            26 => '#0ea5e9',
                            27 => '#0ea5e9',
                            28 => '#0ea5e9',
                            29 => '#0ea5e9',
                            30 => '#0ea5e9',
                            31 => '#0ea5e9',
                            32 => '#0ea5e9',
                            33 => '#0ea5e9',
                            34 => '#0ea5e9',
                            35 => '#0ea5e9',

                            // เขียวอ่อน 36-47
                            36 => '#92D050',
                            37 => '#92D050',
                            38 => '#92D050',
                            39 => '#92D050',
                            40 => '#92D050',
                            41 => '#92D050',
                            42 => '#92D050',
                            43 => '#92D050',
                            44 => '#92D050',
                            45 => '#92D050',
                            46 => '#92D050',
                            47 => '#92D050',

                            // ส้มอ่อน 48-54
                            48 => '#FFC000',
                            49 => '#FFC000',
                            50 => '#FFC000',
                            51 => '#FFC000',
                            52 => '#FFC000',
                            53 => '#FFC000',
                            54 => '#FFC000',

                            //เขียวอ่อ่น 55-67
                            55 => '#92D050',
                            56 => '#92D050',
                            57 => '#92D050',
                            58 => '#92D050',
                            59 => '#92D050',
                            60 => '#92D050',
                            61 => '#92D050',
                            62 => '#92D050',
                            63 => '#92D050',
                            64 => '#92D050',
                            65 => '#92D050',
                            66 => '#92D050',
                            67 => '#92D050',

                            // สีเหลืองอ่อน 68-95
                            68 => '#FFC000',
                            69 => '#FFC000',
                            70 => '#FFC000',
                            71 => '#FFC000',
                            72 => '#FFC000',
                            73 => '#FFC000',
                            74 => '#FFC000',
                            75 => '#FFC000',
                            76 => '#FFC000',
                            77 => '#FFC000',
                            78 => '#FFC000',
                            79 => '#FFC000',
                            80 => '#FFC000',
                            81 => '#FFC000',
                            82 => '#FFC000',
                            83 => '#FFC000',
                            84 => '#FFC000',
                            85 => '#FFC000',
                            86 => '#FFC000',
                            87 => '#FFC000',
                            88 => '#FFC000',
                            89 => '#FFC000',
                            90 => '#FFC000',
                            91 => '#FFC000',
                            92 => '#FFC000',
                            93 => '#FFC000',
                            94 => '#FFC000',
                            95 => '#FFC000',
                            ];

                            $colIndex = 12;
                            @endphp





                            {{-- ===== THEAD ===== --}}
                            @for ($i = 1; $i <= 120; $i++) @php $col="col$i" ; $visibility=$permissions[Auth::id()]->
                                $col ?? 'invisible';

                                $label = $columnLabels[$i] ?? "Col $i";
                                $bgColor = $columnBgColors[$i] ?? '#FF0000'; // default เทา
                                @endphp

                                <th class="whitespace-nowrap text-center border-b border-blue-900
               text-white group" style="
            background-color: {{ $bgColor }};
            {{ $visibility === 'invisible' ? 'display:none;' : '' }}
        ">

                                    <div class="flex items-center justify-center gap-2">
                                        <span class="tracking-wide font-sarabun text-xs">
                                            {!! $label !!}
                                        </span>

                                        <span class="filter-icon cursor-pointer inline-flex items-center
                       opacity-60 group-hover:opacity-100 transition-opacity" data-table="main" data-col="col{{ $i }}"
                                            data-type="text">
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
                        <tr class="group bg-white hover:bg-red-100 transition-colors font-sarabun duration-200 text-xs">

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
                                    style="{{ $isMoney ? 'text-align:right;' : '' }}" value="{{ $isMoney
                                                    ? ($originalValue !== null && $originalValue !== ''
                                                        ? number_format((float) str_replace(',', '', $originalValue), 2)
                                                        : '')
                                                    : $originalValue }}" data-id="{{ $item->Refcode_PJ }}"
                                    data-field="{{ $field }}" data-original="{{ $originalValue }}" {{ $isRead
                                    ? 'readonly disabled tabindex=-1' : '' }} @if ($field==='Estimated_Revenue_PJ' )
                                    oninput="validateRevenue(this)" @endif>
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
                                    style="text-align: right;" value="{{ number_format($grossProfit, 2) }}" readonly
                                    disabled tabindex="-1">
                            </td>



                            <td data-col="Estimated_Gross_Profit_Margin_PJ"
                                style="{{ $gmVisibility === 'invisible' ? 'display:none;' : '' }}">
                                <input type="text" class="excel-input gross-margin text-end readonly-cell"
                                    value="{{ number_format($grossMargin, 2) }}%" readonly disabled tabindex="-1">
                            </td>


                            {{-- ===== col 1–120 (tbody) ===== --}}
                            @php
                            $columnConfig = [
                            //1 => ['type' => 'select', 'options' => ['Go', 'NoGo']],
                            1 => ['type' => 'text'],
                            2 => ['type' => 'text'],
                            3 => ['type' => 'select', 'options' => ['Go', 'NoGo']],
                            4 => ['type' => 'text'],
                            5 => ['type' => 'text'],

                            6 => ['type' => 'date'],
                            7 => ['type' => 'date'],
                            8 => ['type' => 'date'],
                            9 => ['type' => 'date'],
                            10 => ['type' => 'date'],
                            11 => ['type' => 'date'],
                            12 => ['type' => 'date'],
                            13 => ['type' => 'date'],
                            14 => ['type' => 'date'],
                            15 => ['type' => 'date'],
                            16 => ['type' => 'date'],
                            17 => ['type' => 'date'],

                            18 => ['type' => 'money'],
                            19 => ['type' => 'date'],
                            20 => ['type' => 'date'],
                            21 => ['type' => 'date'],
                            22 => ['type' => 'date'],
                            23 => ['type' => 'date'],
                            24 => ['type' => 'date'],
                            25 => ['type' => 'date'],

                            26 => ['type' => 'money'],
                            27 => ['type' => 'text'],
                            28 => ['type' => 'text'],
                            29 => ['type' => 'date'],
                            30 => ['type' => 'date'],
                            31 => ['type' => 'date'],
                            32 => ['type' => 'date'],
                            33 => ['type' => 'date'],
                            34 => ['type' => 'date'],
                            35 => ['type' => 'text'],

                            36 => ['type' => 'date'],
                            37 => ['type' => 'money'],
                            38 => ['type' => 'money'],
                            39 => ['type' => 'money', 'calculated' => true], // 39 = 37 + 38
                            40 => ['type' => 'text'],
                            41 => ['type' => 'date'],
                            42 => ['type' => 'date'],
                            43 => ['type' => 'text'],
                            44 => ['type' => 'money'],
                            45 => ['type' => 'text'],
                            46 => ['type' => 'money'],
                            47 => ['type' => 'money'],

                            48 => ['type' => 'text'],
                            49 => ['type' => 'text'],
                            50 => ['type' => 'text'],
                            51 => ['type' => 'text'],
                            52 => ['type' => 'text'],
                            53 => ['type' => 'date'],
                            54 => ['type' => 'date'],

                            55 => ['type' => 'date'],
                            56 => ['type' => 'text'],
                            57 => ['type' => 'money'],
                            58 => ['type' => 'date'],
                            59 => ['type' => 'text'],
                            60 => ['type' => 'money'],
                            61 => ['type' => 'date'],
                            62 => ['type' => 'text'],
                            63 => ['type' => 'money'],
                            64 => ['type' => 'date'],
                            65 => ['type' => 'text'],
                            66 => ['type' => 'money'],
                            67 => ['type' => 'money'],

                            68 => ['type' => 'date'],
                            69 => ['type' => 'date'],
                            70 => ['type' => 'date'],
                            71 => ['type' => 'date'],
                            72 => ['type' => 'date'],
                            73 => ['type' => 'date'],
                            74 => ['type' => 'date'],
                            75 => ['type' => 'date'],
                            76 => ['type' => 'date'],
                            77 => ['type' => 'date'],
                            78 => ['type' => 'date'],
                            79 => ['type' => 'date'],
                            80 => ['type' => 'date'],
                            81 => ['type' => 'date'],
                            82 => ['type' => 'date'],
                            83 => ['type' => 'date'],
                            84 => ['type' => 'date'],
                            85 => ['type' => 'date'],
                            86 => ['type' => 'date'],
                            87 => ['type' => 'date'],
                            88 => ['type' => 'date'],
                            89 => ['type' => 'date'],
                            90 => ['type' => 'date'],
                            91 => ['type' => 'date'],
                            92 => ['type' => 'date'],
                            93 => ['type' => 'date'],
                            94 => ['type' => 'date'],
                            95 => ['type' => 'date'],
                            ];
                            @endphp

                            @for ($i = 1; $i <= 120; $i++) @php $col="col$i" ; $config=$columnConfig[$i] ?? ['type'=>
                                'text'];

                                $visibility = $permissions[Auth::id()]->$col ?? 'invisible';
                                $isRead = $visibility === 'read';
                                $isInvisible = $visibility === 'invisible';

                                $value = $item->$col;
                                @endphp

                                <td data-col="col{{ $i }}" class="col-{{ $i }}"
                                style="{{ $isInvisible ? 'display:none;' : '' }}"
                                title="{{ $value }}">

                                {{-- TEXT --}}
                                @if ($config['type'] === 'text')
                                    <input type="text"
                                        class="excel-input {{ $isRead ? 'readonly-cell' : '' }}"
                                        value="{{ $value }}"
                                        data-id="{{ $item->Refcode_PJ }}"
                                        data-field="{{ $col }}"
                                        {{ $isRead ? 'readonly tabindex=-1' : '' }}>

                                {{-- DATE --}}
                               @elseif ($config['type'] === 'date')
                                <div class="date-wrapper">
                                    <input type="text"
                                        class="excel-input date-input {{ empty($value) ? 'date-empty' : '' }} {{ $isRead ? 'readonly-cell' : '' }}"
                                        value="{{ $value ? \Carbon\Carbon::parse($value)->format('d-m-Y') : '' }}"
                                        placeholder="DD-MMM-YYYY"
                                        inputmode="numeric"
                                        pattern="\d{2}-\d{2}-\d{4}"
                                        data-id="{{ $item->Refcode_PJ }}"
                                        data-field="{{ $col }}"
                                        {{ $isRead ? 'readonly tabindex=-1' : '' }}>

                                    <span class="date-icon {{ $isRead ? 'disabled' : '' }}">
                                        <i class="fa-regular fa-calendar-days"></i>
                                    </span>
                                </div>

                                {{-- MONEY --}}
                                @elseif ($config['type'] === 'money')
                                    <input type="text"
                                        class="excel-input money-input text-end
                                            {{ $isRead || ($config['calculated'] ?? false) ? 'readonly-cell' : '' }}"
                                        value="{{ is_numeric($value) ? number_format($value, 2) : $value }}"
                                        data-id="{{ $item->Refcode_PJ }}"
                                        data-field="{{ $col }}"
                                        {{ $isRead || ($config['calculated'] ?? false) ? 'readonly tabindex=-1' : '' }}>

                                {{-- SELECT --}}
                                @elseif ($config['type'] === 'select')
                                    <select class="excel-input {{ $isRead ? 'readonly-cell' : '' }}"
                                        data-id="{{ $item->Refcode_PJ }}"
                                        data-field="{{ $col }}"
                                        style="{{ $isRead ? 'pointer-events:none;background:#f3f4f6;' : '' }}">
                                        @foreach ($config['options'] as $option)
                                            <option value="{{ $option }}" {{ $value == $option ? 'selected' : '' }}>
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
        <div id="column-filter-checkbox-list" class="overflow-y-auto font-sarabun px-4 py-2 text-sm max-h-60 flex-grow">
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
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.date-wrapper').forEach(wrapper => {
        const input = wrapper.querySelector('.date-input');
        const icon  = wrapper.querySelector('.date-icon');

        toggleDateValueStyle(input);

        if (input.hasAttribute('readonly')) return;

        const fp = flatpickr(input, {
            dateFormat: 'd-m-Y',
            allowInput: true,
            clickOpens: false,
            disableMobile: true,
            locale: { firstDayOfWeek: 1 },

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
            if (v.length >= 2 && v.length < 4) v = v.slice(0,2) + '-' + v.slice(2);
            if (v.length >= 4) v = v.slice(0,2) + '-' + v.slice(2,4) + '-' + v.slice(4);
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
                            length: 95
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
                            length: 95
                        }, (_, i) => i + 1)
                        // col 1–36 = read
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
                            length: 95
                        }, (_, i) => i + 1)
                        // col 1–36 = read
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
                            length: 95
                        }, (_, i) => i + 1)
                        // col 1–36 = read
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

                    fetch("{{ route('newjob.inlineUpdate_88') }}", {
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
    XLSX.writeFile(wb, "88_True_New_Site_Project.xlsx");
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