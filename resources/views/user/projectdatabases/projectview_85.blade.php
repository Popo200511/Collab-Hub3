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
                    <button onclick="document.getElementById('permissionModal').classList.remove('hidden')" class="group flex items-center px-3 py-1.5 bg-white text-indigo-600 border border-indigo-200
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

        <!-- Modal -->
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
                            <table class="w-full border-collapse text-center text-xs font-sarabun">

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

                                        <th class="sticky top-0 border px-1 w-[140px]" style="background-color: green">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Customer Region</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="3">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <th class="sticky top-0 border px-1 w-[140px]" style="background-color: green">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Estimated Revenue</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="4">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <th class="sticky top-0 border px-1 w-[140px]" style="background-color: green">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Estimated Service Cost</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="5">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <th class="sticky top-0 border px-1 w-[140px]" style="background-color: green">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Estimated Material Cost</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="6">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <th class="sticky top-0 border px-1 w-[140px]" style="background-color: green">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Estimated Transportation Cost</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="7">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <th class="sticky top-0 border px-1 w-[140px]" style="background-color: green">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Estimated Other Cost</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="8">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <th class="sticky top-0 border px-1 w-[140px]" style="background-color: blue">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Estimated Gross Profit</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="9">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>


                                        <th class="sticky top-0 border px-1 w-[150px]" style="background-color: blue">
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Estimated Gross Profit Margin</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="10">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white>
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Swap Code</span>

                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="11">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white>
                                            <div class="flex items-center justify-center gap-2">
                                                <span>Survey (Plan)</span>
                                                <span
                                                    class="filter-icon cursor-pointer opacity-60 group-hover:opacity-100"
                                                    data-table="permission" data-col="12">
                                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                                </span>
                                            </div>
                                        </th>

                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">Survey
                                            (Plan)</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">Survey
                                            (Actual)</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">Go / Nogo
                                        </th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">Material
                                            Ordered Date</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">Material
                                            Ready Date</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">Material
                                            Withdrawn Date</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">Material
                                            Received Date</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">Material -
                                            Actual Price</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">Install
                                            Date (Plan)</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">Install
                                            Date (Actual)</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">Platform
                                            Type</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">Project ID
                                        </th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">Current
                                            Step</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">Ref No.
                                        </th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">WO No.
                                            PROMs</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">FBOQ Price
                                        </th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">PO No.
                                            PROMs</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-sky-500 text-white">Upload BOQ
                                        </th>

                                        <th class="sticky top-0 border px-1 w-[100px] bg-green-600 text-white">Revenue
                                        </th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-green-600 text-white">PO No.
                                            Actual</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-green-600 text-white">PO
                                            Amount Actual</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-green-600 text-white">INV 1st
                                            No.</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-green-600 text-white">INV 1st
                                            Price</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-green-600 text-white">INV 2nd
                                            No.</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-green-600 text-white">INV 2nd
                                            Price</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-green-600 text-white">PO
                                            Remaining</th>

                                        <th class="sticky top-0 border px-1 w-[100px] bg-blue-850 text-white">PR
                                            Proposed date (Email)</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-blue-850 text-white">PR
                                            Approved Date (Email)</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-blue-850 text-white">Billing1
                                            Proposed date (Email)</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-blue-850 text-white">Billing1
                                            Approved Date (Email)</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-blue-850 text-white">Billing2
                                            Proposed date (Email)</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-blue-850 text-white">Billing2
                                            Approved Date (Email)</th>

                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">Col34</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">Col35</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">Col36</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">Col37</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">Col38</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">Col39</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">Col40</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">Col41</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">Col42</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">Col43</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">Col44</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">Col45</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">Col46</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">Col47</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">Col48</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">Col49</th>
                                        <th class="sticky top-0 border px-1 w-[100px] bg-red-400 text-white">Col50</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($users as $user)
                                    <tr
                                        class="group h-8 text-xs odd:bg-white even:bg-gray-50 hover:bg-red-100 border-b border-gray-200 transition table-row-divider">

                                        <!-- User -->
                                        <td
                                            class="sticky left-0 z-[40] bg-white group-hover:bg-red-100 px-2 transition">
                                            {{ $user->name }}
                                        </td>

                                        <!-- Project Member -->
                                        <td
                                            class="sticky left-[169px] z-[40] bg-inherit group-hover:bg-red-100 px-2 transition">
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
                                        <td
                                            class="sticky left-[289px] z-[40] bg-inherit group-hover:bg-red-100 px-2 transition">
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

                                        @foreach ($projectFields as $field)
                                        <td class="px-2">
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
                                        @for ($i = 1; $i <= 50; $i++) <td class="px-2">
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
                padding: 8px;
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

                <table class="table min-w-max table-fixed border-separate border-spacing-0 font-sarabun
                            [--col-1:115px]
                            [--col-2:130px]" id="table">

                    <thead class="bg-blue-950 text-white font-sarabun text-base sticky top-0 z-[200]">

                        <tr class="text-xs text-center">

                            <th
                                class=" whitespace-nowrap text-center border-b border-blue-900 group sticky top-0 left-0 z-[150] bg-blue-950 w-[var(--col-1)]">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="tracking-wide font-sarabun text-xs  text-white/90">Refcode</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="0">
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
                                        data-col="1">
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
                                        data-col="2">
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
                                        data-col="3">
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
                   group-hover:opacity-100 transition-opacity" data-col="{{ $colIndex }}"
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
                   group-hover:opacity-100 transition-opacity" data-col="{{ $colIndex }}" data-type="money">
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
                   group-hover:opacity-100 transition-opacity" data-col="{{ $colIndex }}" data-type="money">
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
                            @for ($i = 1; $i <= 50; $i++) @php $col="col$i" ; $visibility=$permissions[Auth::id()]->$col
                                ?? 'invisible';

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
                                            {{ $label }}
                                        </span>

                                        <span class="filter-icon cursor-pointer inline-flex items-center
                       opacity-60 group-hover:opacity-100 transition-opacity" data-col="{{ $colIndex }}"
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
                            <td
                                class="sticky left-0 z-[120] w-[var(--col-1)] bg-white px-2 py-1 whitespace-nowrap text-center group-hover:bg-red-100 transition">
                                {{ $item->Refcode_PJ }}
                            </td>

                            {{-- Site Code --}}
                            <td
                                class="sticky left-[var(--col-1)] z-[110] w-[var(--col-2)] bg-white px-2 py-1 whitespace-nowrap text-center group-hover:bg-red-100 transition">
                                {{ $item->Site_Code_PJ }}
                            </td>

                            <td>{{ $item->Job_Description_PJ }}</td>
                            <td>{{ $item->Office_Code_PJ }}</td>


                            {{-- ===== Project Columns (tbody) ===== --}}
                            @foreach ($projectCols as $field => $label)
                            @php
                            $visibility = $permissions[Auth::id()]->$field ?? 'invisible';
                            $isRead = $visibility === 'read';
                            $isInvisible = $visibility === 'invisible';
                            $isMoney = in_array($field, $moneyFields);
                            $originalValue = $item->$field ?? '';
                            @endphp

                            <td class="project-col {{ $field }}" style="{{ $isInvisible ? 'display:none;' : '' }}">

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

                            <td style="{{ $gpVisibility === 'invisible' ? 'display:none;' : '' }}">
                                <input type="text" class="excel-input gross-profit text-end readonly-cell"
                                    style="text-align: right;" value="{{ number_format($grossProfit, 2) }}" readonly
                                    disabled tabindex="-1">
                            </td>



                            <td style="{{ $gmVisibility === 'invisible' ? 'display:none;' : '' }}">
                                <input type="text" class="excel-input gross-margin text-end readonly-cell"
                                    value="{{ number_format($grossMargin, 2) }}%" readonly disabled tabindex="-1">
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

                            @for ($i = 1; $i <= 50; $i++) @php $col="col$i" ; $config=$columnConfig[$i] ?? ['type'=>
                                'text'];

                                $visibility = $permissions[Auth::id()]->$col ?? 'invisible';
                                $isRead = $visibility === 'read';
                                $isInvisible = $visibility === 'invisible';

                                $value = $item->$col;
                                @endphp

                                <td class="col-{{ $i }}" style="{{ $isInvisible ? 'display:none;' : '' }}"
                                    title="{{ $value }}">

                                    @if (in_array($config['type'], ['text', 'date']))
                                    {{-- TEXT / DATE --}}
                                    <input type="{{ $config['type'] }}"
                                        class="excel-input {{ $isRead ? 'readonly-cell' : '' }}" value="{{ $value }}"
                                        data-id="{{ $item->Refcode_PJ }}" data-field="{{ $col }}" {{ $isRead
                                        ? 'readonly tabindex=-1' : '' }}>

                                    @elseif ($config['type'] === 'money')
                                    <input type="text"
                                        class="excel-input money-input text-end {{ $isRead || ($config['calculated'] ?? false) ? 'readonly-cell' : '' }}"
                                        value="{{ is_numeric($value) ? number_format($value, 2) : $value }}"
                                        data-id="{{ $item->Refcode_PJ }}" data-field="{{ $col }}" {{ $isRead ||
                                        ($config['calculated'] ?? false) ? 'readonly tabindex=-1' : '' }}>

                                    @elseif ($config['type'] === 'select')
                                    {{-- SELECT --}}
                                    <select class="excel-input {{ $isRead ? 'readonly-cell' : '' }}"
                                        data-id="{{ $item->Refcode_PJ }}" data-field="{{ $col }}"
                                        style="{{ $isRead ? 'pointer-events:none;background:#f3f4f6;' : '' }}">
                                        @foreach ($config['options'] as $option)
                                        <option value="{{ $option }}" {{ $value==$option ? 'selected' : '' }}>
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
                        <option value="10" selected>10 รายการ</option>
                        <option value="20">20 รายการ</option>
                    </select>
                    {{-- Custom Arrow Icon --}}
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </div>
                </div>
            </div>

            <nav class="flex items-center space-x-2 order-1 lg:order-2" aria-label="Pagination">
                {{-- Previous Button --}}
                <button id="prevPageBtnList" onclick="goToPage(currentPage - 1)"
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
                <button id="nextPageBtnList" onclick="goToPage(currentPage + 1)"
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
<div id="column-filter-modal" class="fixed inset-0 z-[300] hidden bg-transparent">
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
                let rowData = [];

                const cells = row.querySelectorAll("th, td");

                cells.forEach(cell => {
                    // ข้าม cell ที่ถูกซ่อน
                    if (cell.offsetParent === null) return;

                    let value = "";

                    // ถ้ามี input ให้ดึง value
                    const input = cell.querySelector("input");
                    if (input) {
                        value = input.value;
                    } else {
                        value = cell.innerText.trim();
                    }

                    rowData.push(value);
                });

                if (rowData.length > 0) {
                    data.push(rowData);
                }
            });

            // สร้าง workbook
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(data);

            XLSX.utils.book_append_sheet(wb, ws, "Visible Data");

            // export
            XLSX.writeFile(wb, "project_visible_data.xlsx");
        }
</script>


<!-- ฟังชั่น Filter  -->
<script>
    let openFilterColumn = null;
        let filters = {}; // filters[col] = array OR null
        let originalColumnValues = {}; // ค่าทั้งหมดในแต่ละคอลัมน์ (สำหรับ Checkbox list)

        let allRows = []; // ทุก tr ใน tbody (ต้นฉบับ)
        let visibleRows = []; // tr ที่ผ่าน filter (สำหรับ pagination)
        let totalRows = 0;

        let rowsPerPage = 10;
        let currentPage = 1;




        /* -----------------------------------------------------
           INITIAL LOAD
        ----------------------------------------------------- */

        const ICONS = {
            normal: `<i class="fi fi-br-bars-filter text-xs text-gray-300 transition duration-150"></i>`,
            filter: `<i class="fi fi-br-bars-filter text-xs text-blue-500 transition duration-150"></i>`,
            sortAsc: `<i class="fa-solid fa-arrow-down-a-z text-xs text-indigo-500 transition duration-150"></i>`,
            sortDesc: `<i class="fa-solid fa-arrow-down-z-a text-xs text-indigo-500 transition duration-150"></i>`
        };





        document.addEventListener("DOMContentLoaded", () => {

            const trs = Array.from(document.querySelectorAll("#tableBody tr"));
            allRows = trs;
            visibleRows = allRows.slice();
            totalRows = visibleRows.length;

            rowsPerPage = 10;
            currentPage = 1;

            setupRowsPerPageOptions();
            renderPagination();

            // ✅ ผูก click ให้ filter icon ทุกคอลัมน์
            document.querySelectorAll(".filter-icon").forEach(icon => {
                icon.addEventListener("click", e => {
                    e.stopPropagation(); // กัน modal ปิดทันที
                    const col = Number(icon.dataset.col);
                    openColumnFilter(col);
                });
            });

        });



        /* -----------------------------------------------------
           FILTER
        ----------------------------------------------------- */
        function openColumnFilter(colIndex) {
            // ถ้าคลิกคอลัมน์เดิม → ปิด
            if (openFilterColumn === colIndex) {
                closeColumnFilterModal();
                return;
            }

            openFilterColumn = colIndex;

            // ✅ ล้างค่า search ทุกครั้งที่เปิดคอลัมน์ใหม่
            const searchInput = document.getElementById("column-filter-search");
            if (searchInput) {
                searchInput.value = "";
            }

            loadFilterValues(colIndex);
            updateFilterIcon(colIndex);

            showFilterModal(
                document.querySelector(`.filter-icon[data-col="${colIndex}"]`)
            );
        }





        function showFilterModal(icon) {
            const modal = document.getElementById("column-filter-modal");
            const box = document.getElementById("column-filter-content");

            modal.classList.remove("hidden");

            const rect = icon.getBoundingClientRect();
            const boxWidth = 300; // ความกว้าง filter popup ของคุณ
            const screenWidth = window.innerWidth;

            let left = rect.left;

            // ถ้าจะล้นจอ → ขยับไปทางซ้าย
            if (left + boxWidth > screenWidth - 10) {
                left = screenWidth - boxWidth - 10;
            }

            box.style.left = `${left}px`;
            box.style.top = `${rect.bottom + window.scrollY}px`;
        }


        function loadFilterValues(colIndex) {
            const list = document.getElementById("column-filter-checkbox-list");
            list.innerHTML = "";

            const sourceRows =
                Object.keys(filters).length === 0 ?
                allRows :
                visibleRows;

            const rawValues = sourceRows.map(row => getCellValue(row, colIndex));
            const values = [...new Set(rawValues)];

            // ให้ค่าว่างอยู่บนสุด
            values.sort((a, b) => {
                if (a === "" && b !== "") return -1;
                if (a !== "" && b === "") return 1;
                return a.localeCompare(b, undefined, {
                    numeric: true
                });
            });

            const selected = filters[colIndex] ?? [];

            values.forEach(v => {
                const checked = selected.includes(v) ? "checked" : "";

                list.innerHTML += `
                    <label class="filter-item flex items-center space-x-2 py-1 px-2 rounded text-xs cursor-pointer hover:bg-red-100">
                        <input type="checkbox" class="filter-checkbox" value="${v}" ${checked}>
                        <span>${v}</span>
                    </label>
                `;
            });
        }




        function getCellValue(row, colIndex) {
            const cell = row.children[colIndex];
            if (!cell) return "";

            // ✅ ถ้าเป็น select → ใช้ value
            const select = cell.querySelector("select");
            if (select) {
                return select.value?.trim() ?? "";
            }

            // ✅ ถ้าเป็น input → ใช้ value
            const input = cell.querySelector("input");
            if (input) {
                return input.value?.trim() ?? "";
            }

            // fallback → text
            return cell.textContent.trim();
        }




        function handleSearch(text) {
            const list = document.getElementById("column-filter-checkbox-list");
            const keyword = text.toLowerCase().trim();

            const items = list.querySelectorAll("label");

            items.forEach(label => {
                const checkbox = label.querySelector("input");
                const value = label.querySelector("span").innerText.toLowerCase();

                if (keyword === "") {
                    // ❌ ไม่พิมพ์อะไร → แสดงทั้งหมด แต่ไม่ติ๊ก
                    label.style.display = "";
                    checkbox.checked = false;
                } else if (value.includes(keyword)) {
                    // ✅ match → แสดง + ติ๊ก
                    label.style.display = "";
                    checkbox.checked = true;
                } else {
                    // ❌ ไม่ match → ซ่อน + ไม่ติ๊ก
                    label.style.display = "none";
                    checkbox.checked = false;
                }
            });
        }




        function selectAll() {
            document.querySelectorAll("#column-filter-checkbox-list .filter-checkbox")
                .forEach(cb => cb.checked = true);
        }

        function deselectAll() {
            document.querySelectorAll("#column-filter-checkbox-list .filter-checkbox")
                .forEach(cb => cb.checked = false);
        }

        function sortAZ() {
            if (openFilterColumn === null) return;
            sortTable(openFilterColumn, 'asc');
        }

        function sortZA() {
            if (openFilterColumn === null) return;
            sortTable(openFilterColumn, 'desc');
        }





        function applyColumnFilter() {
            const col = openFilterColumn;

            const checkboxes = document.querySelectorAll(".filter-checkbox");
            const selected = [...checkboxes]
                .filter(cb => cb.checked)
                .map(cb => cb.value);

            const total = checkboxes.length;

            // Excel rule
            if (selected.length === 0 || selected.length === total) {
                delete filters[col];
            } else {
                filters[col] = selected;
            }

            applyAllFilters();

            // ✅ update icon
            updateFilterIcon(col);

            closeColumnFilterModal(false);
        }



        function applyAllFilters() {
            visibleRows = allRows.filter(row => {
                for (let colKey in filters) {
                    const allowed = filters[colKey];
                    const colIndex = Number(colKey);

                    const value = getCellValue(row, colIndex);
                    if (!allowed.includes(value)) return false;
                }
                return true;
            });

            totalRows = visibleRows.length;

            currentPage = 1;
            renderPagination();
        }




        function closeColumnFilterModal() {
            document.getElementById("column-filter-modal").classList.add("hidden");
            openFilterColumn = null;
        }



        /* ปิด modal เมื่อคลิกข้างนอก */
        document.addEventListener("mousedown", e => {
            const modal = document.getElementById("column-filter-modal");
            if (modal.classList.contains("hidden")) return;

            const box = document.getElementById("column-filter-content");
            if (!box.contains(e.target)) closeColumnFilterModal();
        });

        /* -----------------------------------------------------
           PAGINATION (ทำงานร่วมกับ Filter)
        ----------------------------------------------------- */
        function setupRowsPerPageOptions() {
            const select = document.getElementById("rowsPerPageList");
            if (!select) return;
            select.innerHTML = "";

            const presets = [10, 20, 50, 100];

            presets.forEach(n => {
                if (n < allRows.length) {
                    let opt = document.createElement("option");
                    opt.value = n;
                    opt.textContent = `${n} แถว`;
                    select.appendChild(opt);
                }
            });

            let allOpt = document.createElement("option");
            allOpt.value = allRows.length;
            allOpt.textContent = `ทั้งหมด (${allRows.length} แถว)`;
            select.appendChild(allOpt);

            // ✅ บังคับ default = 10 เสมอถ้ามี
            if (select.querySelector('option[value="10"]')) {
                select.value = "10";
                rowsPerPage = 10;
            }
        }

        function renderPageNumbers(totalPages) {
            const container = document.getElementById("pageNumbersList");
            container.innerHTML = "";

            const maxButtons = 5;
            let start = Math.max(1, currentPage - 2);
            let end = Math.min(totalPages, start + maxButtons - 1);

            if (end - start < maxButtons - 1) {
                start = Math.max(1, end - maxButtons + 1);
            }

            for (let i = start; i <= end; i++) {
                const btn = document.createElement("button");
                btn.textContent = i;

                btn.className =
                    i === currentPage ?
                    "w-10 h-10 rounded-xl bg-indigo-600 text-white font-sarabun text-sm shadow-md" :
                    "w-10 h-10 rounded-xl bg-white text-gray-600 font-sarabun text-sm hover:bg-indigo-50 transition-all";

                btn.onclick = () => goToPage(i);
                container.appendChild(btn);
            }
        }




        function renderPagination() {
            const tbody = document.querySelector("#tableBody");

            visibleRows.forEach(tr => tbody.appendChild(tr));

            const totalPages = Math.max(1, Math.ceil(totalRows / rowsPerPage));

            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            allRows.forEach(r => r.style.display = "none");

            if (totalRows === 0) {
                document.getElementById("paginationSummaryList").innerText =
                    `แสดง 0-0 จากทั้งหมด 0 รายการ`;
                renderPageNumbers(1);
                return;
            }

            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            visibleRows.slice(start, end).forEach(r => r.style.display = "");

            document.getElementById("paginationSummaryList").innerText =
                `แสดง ${start + 1}-${Math.min(end, totalRows)} จากทั้งหมด ${totalRows} รายการ`;

            document.getElementById("prevPageBtnList").disabled = currentPage === 1;
            document.getElementById("nextPageBtnList").disabled = currentPage >= totalPages;


            renderPageNumbers(totalPages);
        }



        function goToPage(page) {
            const totalPages = Math.max(1, Math.ceil(totalRows / rowsPerPage));
            if (page < 1 || page > totalPages) return;

            currentPage = page;
            renderPagination();
        }


        function changeRowsPerPage(v) {
            rowsPerPage = parseInt(v);
            currentPage = 1;
            renderPagination();
        }
</script>


<!-- ฟังชั่น Sort A -> Z Sort Z -> A -->
<script>
    let sortState = {
            col: null,
            direction: null // 'asc' | 'desc'
        };

        function sortTable(colIndex, direction) {
            if (colIndex == null) return;

            sortState.col = colIndex;
            sortState.direction = direction;

            visibleRows.sort((a, b) => {
                let v1 = getCellValue(a, colIndex);
                let v2 = getCellValue(b, colIndex);

                const n1 = parseFloat(v1.replace(/,/g, ""));
                const n2 = parseFloat(v2.replace(/,/g, ""));

                if (!isNaN(n1) && !isNaN(n2)) {
                    return direction === 'asc' ? n1 - n2 : n2 - n1;
                }

                return direction === 'asc' ?
                    v1.localeCompare(v2, undefined, {
                        numeric: true
                    }) :
                    v2.localeCompare(v1, undefined, {
                        numeric: true
                    });
            });

            currentPage = 1;
            renderPagination();
            updateAllColumnIcons();
        }


        function handleSearchEnter(e) {
            if (e.key === "Enter") {
                e.preventDefault(); // กัน form submit (ถ้ามี)
                applyColumnFilter(); // = กด OK
            }
        }

        document.addEventListener("keydown", e => {
            if (e.key === "Escape") {
                closeColumnFilterModal();
            }
        });



        function clearColumnFilterExcel() {
            if (openFilterColumn === null) return;

            const col = openFilterColumn;

            // 1. ลบ filter ของคอลัมน์นี้
            delete filters[col];

            // 2. apply filter ใหม่ (ยังเหลือ filter คอลัมน์อื่น)
            applyAllFilters();

            // 3. reload checkbox จากข้อมูลในตารางปัจจุบัน
            loadFilterValues(col);

            // 4. update icon
            updateFilterIcon(col);
        }



        function updateFilterIcon(colIndex) {
            const iconWrap = document.querySelector(`.filter-icon[data-col="${colIndex}"]`);
            if (!iconWrap) return;

            const isFiltered = filters[colIndex] && filters[colIndex].length > 0;

            iconWrap.innerHTML = isFiltered ?
                ICONS.filter :
                ICONS.normal;
        }

        function updateAllColumnIcons() {
            document.querySelectorAll(".filter-icon").forEach(icon => {
                const col = Number(icon.dataset.col);

                // 1. sort มาก่อน
                if (sortState.col === col) {
                    icon.innerHTML =
                        sortState.direction === "asc" ?
                        ICONS.sortAsc :
                        ICONS.sortDesc;
                    return;
                }

                // 2. filter รองลงมา
                if (filters[col]) {
                    icon.innerHTML = ICONS.filter;
                    return;
                }

                // 3. ปกติ
                icon.innerHTML = ICONS.normal;
            });
        }

        function clearAllTableFilters() {

            // 1. ล้าง filter ทุกคอลัมน์
            filters = {};

            // 2. ล้าง sort state
            sortState.col = null;
            sortState.direction = null;

            // 3. คืน visibleRows เป็นลำดับต้นฉบับ
            visibleRows = allRows.slice();

            totalRows = visibleRows.length;

            // 4. reset pagination
            currentPage = 1;
            renderPagination();

            // 5. update icon ทุกคอลัมน์
            updateAllColumnIcons();

            // 6. ปิด modal
            closeColumnFilterModal();
        }
</script>



@endsection