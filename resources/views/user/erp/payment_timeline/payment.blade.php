@extends('layouts.user')
@section('title', 'ERP Payment Timeline - Payment')
@section('content')

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">

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

<style>
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }
</style>

<style>
    /* บังคับความสูงแถวให้คงที่ 40px เสมอ */
    #oldDataBody tr {
        height: 20px !important;
        max-height: 20px !important;
    }

    #oldDataBody td {
        padding: 4px 8px;
        vertical-align: middle;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* ตกแต่ง Scrollbar ให้ดูสะอาดตา (Optional) */
    .overflow-y-auto::-webkit-scrollbar {
        width: 8px;
    }

    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
</style>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>

<div class="text-center mt-4 mb-2">

    <h2 class="text-2xl font-sarabun font-extrabold text-gray-900 italic uppercase">
        Payment <span class="text-blue-600">Timeline {{ $count }}</span>
    </h2>
</div>

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

<style>
    .swal-title,
    .swal-text {
        font-family: 'Sarabun', sans-serif;
    }
</style>
@endif

<!-- แสดงข้อความข้อผิดพลาด -->
@if ($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: 'error',
                    title: 'ข้อผิดพลาด!',
                    // ดึงข้อความแรกจาก Error Bag มาแสดง
                    text: '{{ $errors->first() }}',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#d33',
                    customClass: {
                        title: 'swal-title',
                        content: 'swal-text'
                    }
                });
            });
</script>
@endif


<div class="flex justify-between items-center mx-3 mt-2 gap-4">

    <div class="flex items-center gap-3">
        <label for="searchInput" class="font-sarabun text-sm font-medium text-gray-600">ช่องค้นหา:</label>
        <div class="relative flex items-center">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
            </span>

            <input type="text" id="searchInput" value="{{ request('search') }}" placeholder="ค้นหาข้อมูลที่ต้องการ"
                autocomplete="off" {{-- ปรับ Rounded เป็น lg ทั้งก้อน --}}
                class="border border-gray-300 rounded-lg pl-9 pr-3 py-1.5 text-sm w-[300px] focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all shadow-sm">

            @if ($search)
            <div x-data="{ on: {{ $showAll ? 'true' : 'false' }} }" class="flex items-center gap-2 ml-2">

                <label class="flex items-center cursor-pointer">

                    <!-- Toggle -->
                    <div class="relative">
                        <input type="checkbox" class="sr-only" x-model="on" @change="
                    if(on){
                        window.location='{{ request()->fullUrlWithQuery(['show_all' => 1]) }}'
                    }else{
                        window.location='{{ request()->fullUrlWithQuery(['show_all' => null]) }}'
                    }
                ">

                        <!-- background -->
                        <div class="w-11 h-6 bg-gray-300 rounded-full shadow-inner transition"
                            :class="on ? 'bg-green-500' : 'bg-gray-300'">
                        </div>

                        <!-- circle -->
                        <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition"
                            :class="on ? 'translate-x-5' : ''">
                        </div>
                    </div>

                    <!-- label -->
                    <span class="ml-2 text-sm font-sarabun text-gray-700">
                        Show ALL
                    </span>

                </label>
            </div>
            @endif

            {{-- ปุ่มล้างค่าแบบเงื่อนไข: แสดงเมื่อมีข้อความในช่องค้นหา
            @if (request('search'))
            <a href="{{ url()->current() }}" class="ml-2 text-xs text-red-400 hover:text-red-500 transition-colors"
                title="ล้างการค้นหา">
                <i class="fa-solid fa-circle-xmark text-lg"></i>
            </a>
            @endif
            --}}

        </div>
    </div>

    <div class="flex items-center gap-2">
        @if (Auth::check() && Auth::user()->status === 'Admin')
        <button type="button" onclick="openModal('Import Purchase (PR)', '{{ route('pr.purchase.import') }}')"
            class="px-3 py-1.5 rounded-md font-sarabun text-sm text-white bg-blue-600 hover:bg-blue-700 shadow hover:shadow-md hover:scale-[1.02] transition-all flex items-center">
            <i class="fas fa-file-import mr-1.5 text-sm"></i> Import PR
        </button>

        <button type="button" onclick="openModal('Import Work Order (WO)', '{{ route('wo.import') }}')"
            class="px-3 py-1.5 rounded-md font-sarabun text-sm text-white bg-blue-600 hover:bg-blue-700 shadow hover:shadow-md hover:scale-[1.02] transition-all flex items-center">
            <i class="fas fa-file-import mr-1.5 text-sm"></i> Import WO
        </button>

        <button type="button" onclick="openModal('Import Billing (Billing)', '{{ route('billing.import') }}')"
            class="px-3 py-1.5 rounded-md font-sarabun text-sm text-white bg-blue-600 hover:bg-blue-700 shadow hover:shadow-md hover:scale-[1.02] transition-all flex items-center">
            <i class="fas fa-file-import mr-1.5 text-sm"></i> Import Billing
        </button>

        {{-- Export Dropdown --}}
        <div class="relative" x-data="{ open: false }">
            <button type="button" @click="open = !open"
                class="px-3 py-1.5 rounded-md font-sarabun text-sm text-white bg-gradient-to-r from-green-600 to-green-500 shadow hover:shadow-md hover:scale-[1.02] transition-all flex items-center">
                <i class="fas fa-file-excel mr-1.5 text-sm"></i> Export
                <i class="fas fa-chevron-down ml-1.5 text-xs transition-transform duration-200"
                    :class="{ 'rotate-180': open }"></i>
            </button>

            <div x-show="open" x-cloak @click.outside="open = false" x-transition
                class="absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg border border-gray-100 z-50">
                <button type="button" @click="exportPayment(); open = false"
                    class="w-full text-left px-4 py-2 text-sm font-sarabun text-gray-700 hover:bg-green-50 hover:text-green-700 flex items-center gap-2 rounded-t-md">
                    <i class="fas fa-calendar-day text-green-600 text-xs"></i> Export Visible Date
                </button>
                <button type="button" onclick="exportPaymentTotal();"
                    class="w-full text-left px-4 py-2 text-sm font-sarabun text-gray-700 hover:bg-green-50 hover:text-green-700 flex items-center gap-2 rounded-b-md">
                    <i class="fas fa-calendar-alt text-green-600 text-xs"></i> Export Total Data
                </button>

            </div>
        </div>
        @endif
    </div>
</div>

<div id="refcodeModal"
    class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 opacity-0 invisible transition-opacity duration-300">
    <div id="modalContent"
        class="bg-white w-[600px] rounded-xl shadow-xl p-6 relative transform scale-95 transition-all duration-300">

        <button onclick="closeModal()" class="absolute top-3 right-3 text-gray-500 hover:text-red-500">✕</button>

        <h2 id="modalTitle" class="text-lg font-medium font-sarabun text-gray-700 mb-4">Import Data</h2>

        <form action="{{ route('pr.purchase.import') }}" method="POST" enctype="multipart/form-data" id="csvForm"
            class="flex items-center gap-2">
            @csrf
            <input type="file" name="xlsx_file_add" accept=".xlsx" required
                class="border border-gray-300 rounded px-2 py-1 w-[400px]">

            <button type="submit" id="uploadButton"
                class="ml-auto bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded w-[150px] flex items-center justify-center shrink-0">
                บันทึก
            </button>
        </form>

        <div id="loadingSpinner" class="hidden mt-4 text-center text-sm text-gray-600"></div>
    </div>
</div>

<script>
    // 1. ฟังก์ชันเปิด Modal และตั้งค่าเป้าหมาย
        function openModal(title, actionUrl) {
            const modal = document.getElementById('refcodeModal');
            const content = document.getElementById('modalContent');
            const modalTitle = document.getElementById('modalTitle');
            const csvForm = document.getElementById('csvForm');

            modalTitle.innerText = title; // เปลี่ยนชื่อหัวข้อ
            csvForm.action = actionUrl; // เปลี่ยน URL ที่จะส่งไป

            modal.classList.remove('invisible', 'opacity-0');
            modal.classList.add('opacity-100');
            setTimeout(() => {
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 50);
        }

        // 2. ฟังก์ชันปิด Modal และหยุดการทำงาน
        function closeModal() {
            window.stop(); // หยุดการอัปโหลดทันที

            const modal = document.getElementById('refcodeModal');
            const content = document.getElementById('modalContent');
            const btn = document.getElementById('uploadButton');
            const csvForm = document.getElementById('csvForm');

            // รีเซ็ตปุ่มบันทึก
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
            btn.innerHTML = 'บันทึก';
            btn.style.width = '';

            // รีเซ็ตฟอร์ม (ล้างไฟล์ที่เลือกค้างไว้)
            csvForm.reset();

            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0');
            setTimeout(() => {
                modal.classList.add('invisible');
            }, 300);
        }

        // 3. ดักจับตอนกด Submit บันทึก
        document.getElementById('csvForm').addEventListener('submit', function() {
            const btn = document.getElementById('uploadButton');

            // ล็อคขนาดปุ่มไม่ให้ดึ๋ง
            btn.style.width = btn.offsetWidth + 'px';
            btn.style.height = btn.offsetHeight + 'px';

            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');

            // ใส่ไอคอนหมุนและจัดกึ่งกลาง
            btn.innerHTML = `
            <div class="flex items-center justify-center gap-2">
                <i class="fa-solid fa-spinner animate-spin"></i>
                <span>กำลังบันทึก...</span>
            </div>
        `;
        });
</script>




<!-- ตาราง Showdata เก่าเปิดมาเจอเลย-->
<div
    class="mt-2 ml-2 w-[98%] h-[580px] overflow-x-auto overflow-y-auto rounded-lg border border-gray-300 shadow-lg flex flex-col justify-start bg-white">

    <table id="table-export-all" class="w-full border-separate border-spacing-0">

        <thead class="sticky top-0 z-30 bg-white shadow-md">
            <tr class="text-xs text-center h-[40px]">

                <th class="sticky left-0 z-10 bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Refcode</span>
                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Job Description</span>
                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Vendors</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Remark</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>PR NO.</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>PR Issued Date</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>PR Approved Date</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>PR Amount</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>WO No.</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>WO Issued Date</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>WO Approved Date</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>WO Amount</span>

                    </div>
                </th>


                @for ($i = 1; $i <= 12; $i++) <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Billing{{ $i }} No.</span>
                    </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Billing{{ $i }} Issued Date</span>
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Billing{{ $i }} Signed</span>
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Billing{{ $i }} Amount</span>
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Billing{{ $i }} AP No.</span>
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Billing{{ $i }} Due Date - Confirm</span>
                        </div>
                    </th>
                    @endfor


            </tr>
        </thead>

        <tbody class="text-xs text-center bg-white" id="oldDataBody">
            @foreach ($timeline as $prNo => $items)
            @php
            // เรียง Billing ตาม billNo โดยแปลงเป็นตัวเลขก่อน
            $sortedItems = $items
            ->sortBy(function ($item) {
            return $item->billNo ? (int) preg_replace('/\D/', '', $item->billNo) : PHP_INT_MAX;
            })
            ->values();

            // ดึงข้อมูลหลักของ PR
            $firstItem = $sortedItems->first();
            @endphp

            <tr class="divide-x divide-gray-100 border border-gray-200 hover:bg-red-100 hover:text-red-600">
                {{-- ข้อมูลหลัก PR / WO --}}
                <td class="sticky left-0 z-10 bg-white whitespace-nowrap px-2">
                    {{ $firstItem->ref_code }}
                </td>
                <td class="whitespace-nowrap px-2 text-left">{{ $firstItem->project_name }}</td>
                <td class="whitespace-nowrap px-2 text-left">{{ $firstItem->Vendors }}</td>
                <td class="whitespace-nowrap px-2 text-left">{{ $firstItem->Remark }}</td>
                <td class="whitespace-nowrap px-2">{{ $firstItem->PR_MR_No }}</td>
                <td class="whitespace-nowrap px-2">{{ $firstItem->Delivery_Date }}</td>
                <td class="whitespace-nowrap px-2">{{ $firstItem->Approve_Date }}</td>
                <td class="whitespace-nowrap px-2 text-right">{{ $firstItem->Amount }}</td>

                <!-- WO -->
                <td class="whitespace-nowrap px-2">{{ $firstItem->docno }}</td>
                <td class="whitespace-nowrap px-2">
                    {{ $firstItem->docdate ? date('d-m-Y', strtotime($firstItem->docdate)) : '' }}
                </td>
                <td class="whitespace-nowrap px-2">
                    {{ $firstItem->docapp_dt ? date('d-m-Y', strtotime($firstItem->docapp_dt)) : '' }}
                </td>
                <td class="whitespace-nowrap px-2 text-right">{{ number_format($firstItem->wo_amount, 2) }}</td>

                {{-- วนลูปสร้างช่อง Billing 1 ถึง 12 --}}
                @for ($i = 0; $i < 12; $i++) @php $bill=$sortedItems->get($i);
                    @endphp

                    @if ($bill)
                    <td class="whitespace-nowrap px-2 bg-green-50">{{ $bill->billNo }}</td>
                    <td class="whitespace-nowrap px-2 bg-green-50">{{ $bill->addDate }}</td>
                    <td class="whitespace-nowrap px-2 bg-green-50">{{ $bill->Sign }}</td>
                    <td class="whitespace-nowrap px-2 text-right bg-green-50">
                        {{ number_format($bill->bill_amount, 2) }}
                    </td>
                    <td class="whitespace-nowrap px-2 bg-green-50">{{ $bill->voucherNo }}</td>
                    <td class="whitespace-nowrap px-2 bg-green-50">{{ $bill->dueDate }}</td>
                    @else
                    {{-- ช่องว่าง 6 ช่องให้ตรงกับ Header --}}
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    @endif
                    @endfor
            </tr>
            @endforeach
        </tbody>

    </table>


</div>

<div id="loadingOverlay"
    class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-[2px]">
    <div class="flex flex-col items-center bg-white p-6 rounded-xl shadow-2xl">
        {{-- ตัว Spinner หมุนๆ ใช้ CSS --}}
        <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
        <p class="mt-4 font-sarabun text-sm font-bold text-gray-700">กำลังค้นหาข้อมูล...</p>
    </div>
</div>

<div class="mt-2 px-4 max-w-[1100px] mx-auto">
    {{ $timeline->appends(['search' => request('search')])->links() }}
</div>




@endsection


<!-- Auto Search -->
<script>
    let searchTimeout;

    function performSearch() {
        const searchInput = document.getElementById('searchInput');
        if (!searchInput) return;

        const searchValue = searchInput.value.trim();
        const url = new URL(window.location.href);

        // Loading
        const loader = document.getElementById('loadingOverlay');
        if (loader) {
            loader.classList.remove('hidden');
        }

        // search
        if (searchValue) {
            url.searchParams.set('search', searchValue);
        } else {
            url.searchParams.delete('search');
        }

        // ⭐ รีเซ็ต toggle
        url.searchParams.delete('show_all');

        // reset page
        url.searchParams.set('page', 1);

        window.location.href = url.href;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('searchInput');

        if (input) {

            input.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    performSearch();
                }, 1000);
            });

            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(searchTimeout);
                    performSearch();
                }
            });

        }
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ดักจับการคลิกที่ลิงก์ทั้งหมดภายใน div ที่ครอบ Pagination
        // เราอ้างอิงจากคลาส .font-sarabun หรือพื้นที่รอบๆ pagination
        const paginationContainer = document.querySelector('.max-w-\\[1100px\\]');

        if (paginationContainer) {
            paginationContainer.addEventListener('click', function(e) {
                // ตรวจสอบว่าสิ่งที่คลิกคือแท็ก <a> (ปุ่มเปลี่ยนหน้า) 
                // หรือเป็นองค์ประกอบภายในปุ่มเปลี่ยนหน้า
                const anchor = e.target.closest('a');

                if (anchor && anchor.href) {
                    // แสดง Loading ทันที
                    const loader = document.getElementById('loadingOverlay');
                    const loadingText = document.querySelector('#loadingOverlay p');

                    if (loader) {
                        if (loadingText) loadingText.innerText = "กำลังเปลี่ยนหน้า...";
                        loader.classList.remove('hidden');
                    }
                }
            });
        }
    });
</script>


<!-- export -->
<script>
    function exportPayment() {
        // 1. ดึง Element ตาราง
        const table = document.querySelector("table");

        // 2. สร้าง Workbook จาก Table โดยตรง
        // sheetjs จะไล่เก็บ <th> ทั้งหมดที่คุณทำวนลูปไว้ 1-12 มาเป็น Header ให้ทันที
        const wb = XLSX.utils.table_to_book(table, {
            sheet: "Payment_Timeline",
            raw: false // ให้รักษารูปแบบข้อความที่เห็นบนหน้าจอ
        });

        // 3. ปรับแต่งชื่อไฟล์ให้มี วันที่-เวลา จะได้ไม่ซ้ำกัน
        const now = new Date();
        const dateString = now.toLocaleDateString().replace(/\//g, '-');
        const fileName = `Payment_Timeline_${dateString}.xlsx`;

        // 4. สั่ง Download
        XLSX.writeFile(wb, fileName);

        // 5. แจ้งเตือนความสำเร็จด้วย SweetAlert2 (ตามที่คุณใช้อยู่ในโปรเจกต์)
        Swal.fire({
            icon: 'success',
            title: 'Export สำเร็จ!',
            text: 'ไฟล์ Excel ของคุณถูกสร้างเรียบร้อยแล้ว',
            confirmButtonColor: '#22c55e',
            timer: 2000
        });
    }
</script>



<!-- export ทั้งหมด (รวมข้อมูลที่มองไม่เห็นด้วย) -->


<script>
    async function exportPaymentTotal() {
        // 1. ดึงข้อมูลทั้งหมดจาก API
        const response = await fetch('/payment/export-json');
        const data = await response.json();

        // 2. map ข้อมูลใหม่ โดยตัด column id ออก
        const filteredData = data.map(({
            id,
            ...rest
        }) => rest);

        // 3. แปลง JSON เป็น worksheet
        const ws = XLSX.utils.json_to_sheet(filteredData);

        // 4. สร้าง workbook
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Payment_Timeline");

        // 5. ตั้งชื่อไฟล์
        const now = new Date();
        const dateString = now.toISOString().slice(0, 10);
        const fileName = `Payment_Timeline_${dateString}.xlsx`;

        // 6. สั่งดาวน์โหลด
        XLSX.writeFile(wb, fileName);

        // 7. แจ้งเตือนความสำเร็จ
        Swal.fire({
            icon: 'success',
            title: 'Export สำเร็จ!',
            text: 'ไฟล์ Excel ของคุณถูกสร้างเรียบร้อยแล้ว',
            confirmButtonColor: '#22c55e',
            timer: 2000
        });
    }
</script>