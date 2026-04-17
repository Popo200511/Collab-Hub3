@extends('layouts.user')

@section('title', 'Purchase Order')

@section('content')

<!-- Export To Excel -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>


<script src="https://unpkg.com/lucide@latest"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>




<!-- Load Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">


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




<style>
    /* Custom Tailwind Configuration for primary colors */
    :root {
        --color-primary-green: #10B981;
        /* bg-green-500 */
    }

    .bg-primary-green {
        background-color: var(--color-primary-green);
    }

    .focus\:border-primary-green:focus {
        border-color: var(--color-primary-green);
    }

    /* Ensure smooth scrolling and clean focus rings */
    input:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.4);
        /* green-500 with alpha */
    }

    /* Utility for mobile responsiveness on main inputs */
    @media (max-width: 768px) {
        .input-group {
            width: 100%;
        }

        .main-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .main-controls>div {
            margin-bottom: 1rem;
        }
    }

    /* Hide the default spinner buttons on number inputs */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield;
    }

    .table-filter-trigger {
        pointer-events: auto;
    }
</style>



<div class="flex h-[calc(108.60dvh-4rem)] overflow-hidden">
    <main class="flex-1 overflow-y-auto p-2 bg-gray-50">
        <div class="pt-1">
            <header class="flex flex-wrap items-center justify-between gap-3 mb-4">

                <div class="flex items-center gap-2">
                    <!-- ปุ่มกด Invoice Reques Log -->
                    <div class="flex items-center gap-2">
                        <button type="button" class="shrink-0 bg-green-600 hover:bg-green-700 text-white font-medium 
                    py-1.5 px-4 rounded-lg shadow-sm
                    transition-all duration-200 flex items-center justify-center gap-1.5 text-xs sm:text-sm">
                            <span>Invoice Request Log</span>
                        </button>
                    </div>

                    <!-- ปุ่มกด PO Booking Log  -->
                    <div class="flex items-center gap-2">
                        <button type="button" class="shrink-0 bg-green-600 hover:bg-green-700 text-white font-medium 
                    py-1.5 px-4 rounded-lg shadow-sm
                    transition-all duration-200 flex items-center justify-center gap-1.5 text-xs sm:text-sm">
                            <span>Invoice Request Log</span>
                        </button>
                    </div>

                    <!-- ปุ่มกด PO Decrement  -->
                    <div class="flex items-center gap-2">
                        <button type="button" class="shrink-0 bg-green-600 hover:bg-green-700 text-white font-medium 
                    py-1.5 px-4 rounded-lg shadow-sm
                    transition-all duration-200 flex items-center justify-center gap-1.5 text-xs sm:text-sm">
                            <span>PO Decrement Log</span>
                        </button>
                    </div>
                </div>

                <!-- ปุ่มกด PO Received From Customer -->
                <div class="flex items-center gap-2">
                    <!-- ปุ่มกด PO Decrement -->
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openDecrementModal()" class="shrink-0 bg-indigo-600 hover:bg-indigo-700 text-white font-medium 
                        py-1.5 px-4 rounded-lg shadow-sm
                        transition-all duration-200 flex items-center justify-center gap-1.5 text-xs sm:text-sm">
                            <span>PO Decrement</span>
                        </button>
                    </div>

                    <button type="button" onclick="openPOModal()" class="shrink-0 bg-indigo-600 hover:bg-indigo-700 text-white font-medium 
                    py-1.5 px-4 rounded-lg shadow-sm
                    transition-all duration-200 flex items-center justify-center gap-1.5 text-xs sm:text-sm">
                        <span>PO Received From Customer</span>
                    </button>

                    <!-- ปุ่มกด Export Visible Data -->
                    <button type="button" id="exportToExcel" onclick="exportToExcel()" class="px-3 py-1.5 rounded-md font-sarabun text-sm text-white bg-gradient-to-r from-green-600 to-green-500
                    shadow hover:shadow-md hover:scale-[1.02] transition-all flex items-center gap-1.5">
                        <i class="fas fa-file-excel"></i>
                        <span class="hidden sm:inline">Export Visible Data</span>
                        <span class="sm:hidden">Export</span>
                    </button>
                </div>

                <!-- Modal กรอก Form PO Decrement -->
                <div id="decrementModal"
                    class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
                        <form action="{{ route('revenue-purchase.PO_Decrement') }}" method="POST" class="p-6 space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">PO Decrement</label>
                                <input type="text" name="po_decrement" required
                                    class="w-full border rounded-lg px-3 py-2">
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t">

                                <button type="button" onclick="closeDecrementModal()" class="px-4 py-2 bg-red-500 text-white rounded 
                                        hover:bg-red-600 hover:scale-105 
                                        transition duration-200 ease-in-out">
                                    ยกเลิก
                                </button>

                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded 
                                        hover:bg-blue-700 hover:scale-105 
                                        transition duration-200 ease-in-out shadow-md hover:shadow-lg">
                                    บันทึก
                                </button>

                            </div>
                        </form>
                    </div>
                </div>



                <!-- Modal PO Received From Customer -->
                <div id="poModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
                        <form action="{{ route('revenue-purchase.PO_Received') }}" method="POST" class="p-6 space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer Code</label>
                                    <input type="text" name="customer_code" readonly
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 text-gray-500 cursor-not-allowed focus:ring-0 focus:border-gray-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                                    <input type="text" name="customer_name" readonly
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 text-gray-500 cursor-not-allowed focus:ring-0 focus:border-gray-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">PO No.</label>
                                    <input type="text" name="po_no" required class="w-full border rounded-lg px-3 py-2">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">PO Amount</label>
                                        <input type="text" name="po_amount" id="po_amount" required
                                            class="w-full border rounded-lg px-3 py-2 text-right"
                                            oninput="formatNumber(this)">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">PO Received
                                            Date</label>
                                        <input type="date" name="po_received_date" required
                                            class="w-full border rounded-lg px-3 py-2">
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end gap-3 pt-4 border-t">

                                <button type="button" onclick="closePOModal()" class="px-4 py-2 bg-red-500 text-white rounded 
                                        hover:bg-red-600 hover:scale-105 
                                        transition duration-200 ease-in-out">
                                    ยกเลิก
                                </button>

                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded 
                                        hover:bg-blue-700 hover:scale-105 
                                        transition duration-200 ease-in-out shadow-md hover:shadow-lg">
                                    บันทึก
                                </button>

                            </div>
                        </form>
                    </div>
                </div>




                <!-- ฟังชั่น Modal ร่วมสำหรับทั้ง 2 แบบ -->
                <script>
                    // ✅ ฟังก์ชันเปิด Modal แบบ Universal
            function openModal(modalId) {
                const modal = document.getElementById(modalId);
                if (!modal) return;
                
                modal.classList.remove('hidden');
                
                // ✅ ชดเชย Scrollbar
                const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
                if (scrollbarWidth > 0) {
                    document.body.style.paddingRight = `${scrollbarWidth}px`;
                    document.body.style.overflow = 'hidden';
                }
            }

            // ✅ ฟังก์ชันปิด Modal แบบ Universal
            function closeModal(modalId) {
                const modal = document.getElementById(modalId);
                if (!modal) return;
                
                modal.classList.add('hidden');
                
                // ✅ คืนค่าเดิม (เช็คก่อนว่าไม่มี Modal อื่นเปิดอยู่)
                const otherModals = ['decrementModal', 'poModal'].filter(id => id !== modalId);
                const hasOtherOpen = otherModals.some(id => {
                    const m = document.getElementById(id);
                    return m && !m.classList.contains('hidden');
                });
                
                if (!hasOtherOpen) {
                    document.body.style.paddingRight = '';
                    document.body.style.overflow = '';
                }
            }

    // ✅ Helper functions สำหรับปุ่มแต่ละแบบ
    function openDecrementModal() { openModal('decrementModal'); }
    function closeDecrementModal() { closeModal('decrementModal'); }
    function openPOModal() { openModal('poModal'); }
    function closePOModal() { closeModal('poModal'); }

    // ✅ ปิดเมื่อกดพื้นหลัง
    document.addEventListener('DOMContentLoaded', function() {
        ['decrementModal', 'poModal'].forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) closeModal(modalId);
                });
            }
        });
    });

    // ✅ ปิดด้วย ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            ['decrementModal', 'poModal'].forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal && !modal.classList.contains('hidden')) {
                    e.preventDefault();
                    closeModal(modalId);
                }
            });
        }
    });
                </script>

                <!--ฟังชั่น Export -->
                <script>
                    function exportToExcel() {
                    // ✅ 1. ใช้ ID ตารางที่ถูกต้อง
                    const table = document.getElementById("customer-table-display");
                    if (!table) {
                        alert("ไม่พบตารางสำหรับ Export");
                        return;
                    }

                    // ✅ 2. ดึงเฉพาะหัวตาราง (thead)
                    const headerRows = table.querySelectorAll("thead tr");
                    let data = [];

                    // ✅ 3. Export Header
                    headerRows.forEach(row => {
                        const rowData = [];
                        const cells = row.querySelectorAll("th");
                        cells.forEach(cell => {
                            // ข้ามคอลัมน์ที่ซ่อนด้วย style="display:none"
                            if (cell.offsetParent === null) return;
                            
                            // ดึงเฉพาะข้อความ (ไม่เอาไอคอนฟิลเตอร์)
                            const span = cell.querySelector("span:not(.filter-icon)");
                            const value = span ? span.innerText.trim() : cell.innerText.trim();
                            if (value) rowData.push(value);
                        });
                        if (rowData.length > 0) data.push(rowData);
                    });

                    // ✅ 4. Export Body (เฉพาะแถวที่มองเห็น + ผ่านฟิลเตอร์)
                    const allRows = Array.from(document.querySelectorAll("#po-list-body tr"));
                    const visibleRows = allRows.filter(row => {
                        // เช็คทั้ง display:none และ offsetHeight
                        const style = window.getComputedStyle(row);
                        return style.display !== "none" && row.offsetHeight > 0;
                    });

                    visibleRows.forEach(row => {
                        const rowData = [];
                        const cells = row.querySelectorAll("td");
                        
                        cells.forEach(cell => {
                            // ข้ามคอลัมน์ที่ซ่อน
                            if (cell.offsetParent === null) return;
                            
                            let value = "";
                            const input = cell.querySelector("input");
                            const badge = cell.querySelector("span.inline-block"); // สำหรับ Customer Code badge
                            
                            if (badge) {
                                // ดึงค่าจาก badge (Customer Code)
                                value = badge.innerText.trim();
                            } else if (input && input.type !== "hidden") {
                                // ดึงค่าจาก input
                                value = input.value.trim();
                            } else {
                                // ดึงค่าจาก text ปกติ
                                value = cell.innerText.trim();
                            }
                            
                            // ✅ ลบคอมม่าและแปลงเป็นตัวเลขถ้าเป็นไปได้
                            const raw = value.replace(/,/g, "").replace("%", "").trim();
                            if (raw !== "" && !isNaN(raw) && !isNaN(parseFloat(raw))) {
                                rowData.push(parseFloat(raw));
                            } else {
                                rowData.push(value);
                            }
                        });
                        
                        if (rowData.length > 0) data.push(rowData);
                    });

                    // ✅ 5. สร้าง Excel File
                    if (data.length === 0) {
                        alert("ไม่มีข้อมูลสำหรับ Export");
                        return;
                    }

                    const wb = XLSX.utils.book_new();
                    const ws = XLSX.utils.aoa_to_sheet(data);

                    // ✅ 6. จัดรูปแบบคอลัมน์ตัวเลขให้ถูกต้องใน Excel
                    const range = XLSX.utils.decode_range(ws["!ref"]);
                    for (let R = 0; R <= range.e.r; R++) {
                        for (let C = 0; C <= range.e.c; C++) {
                            const cellAddr = XLSX.utils.encode_cell({ r: R, c: C });
                            const cell = ws[cellAddr];
                            if (cell && typeof cell.v === "number") {
                                cell.t = "n";        // type = number
                                cell.z = "0.00"; // format มีลูกน้ำ 2 ตำแหน่ง
                            }
                        }
                    }

                    // ✅ 7. ปรับความกว้างคอลัมน์อัตโนมัติ (เล็กน้อย)
                    const colWidths = [];
                    data[0]?.forEach((_, colIndex) => {
                        let maxWidth = 10;
                        data.forEach(row => {
                            if (row[colIndex]) {
                                const len = String(row[colIndex]).length;
                                if (len > maxWidth) maxWidth = len;
                            }
                        });
                        colWidths.push({ wch: Math.min(maxWidth + 2, 30) }); // จำกัดความกว้างสูงสุด 30
                    });
                    ws["!cols"] = colWidths;

                    XLSX.utils.book_append_sheet(wb, ws, "PO Revenue");
                    XLSX.writeFile(wb, `PO_Revenue_${new Date().toISOString().slice(0,10)}.xlsx`);
                }
                </script>
                
            <!-- SweetAlert เมื่อบันทึกสำเร็จให้แสดง SweetAlert บันทึกสำเร็จ -->
                @if(session('success'))
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ',
                            text: '{{ session('success') }}',
                            confirmButtonColor: '#2563eb',
                            confirmButtonText: 'ตกลง'
                        });
                    </script>
                @endif

            <!-- SweetAlert ยืนยันการบันทึก -->
                <script>
                    document.querySelectorAll("#poModal form, #decrementModal form")
                    .forEach(form => {
                        form.addEventListener("submit", function (e) {
                            e.preventDefault(); // ❗ หยุด submit ก่อน

                            Swal.fire({
                                title: 'ยืนยันการบันทึก?',
                                text: "คุณต้องการบันทึกข้อมูลใช่หรือไม่",
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#2563eb',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'ใช่, บันทึกเลย',
                                cancelButtonText: 'ยกเลิก'
                            }).then((result) => {
                                if (result.isConfirmed) {

                                    // 🔥 ลบ comma ก่อน submit (กัน DB พัง)
                                    const amount = form.querySelector("#po_amount");
                                    if (amount) {
                                        amount.value = amount.value.replace(/,/g, "");
                                    }

                                    form.submit(); // ✅ submit จริง
                                }
                            });
                        });
                    });

                    
                </script>

            </header>



            <!-- Main ตารางแสดงข้อมูล PO - Compact Design with Red Hover -->
            <div class="w-full flex flex-col shadow-xl rounded-xl ring-1 ring-gray-200 bg-white mt-4 overflow-hidden">

                <!-- Table Container with Responsive Scroll -->
                <div class="overflow-x-auto overflow-y-auto flex-1"
                    style="max-height: calc(100vh - 250px); min-height: 578px;">
                    <style>
                        /* Custom Compact Scrollbar */
                        .compact-table::-webkit-scrollbar {
                            height: 10px;
                            width: 6px;
                        }

                        .compact-table::-webkit-scrollbar-track {
                            background: #f8fafc;
                        }

                        .compact-table::-webkit-scrollbar-thumb {
                            background: #cbd5e1;
                            border-radius: 3px;
                        }

                        .compact-table::-webkit-scrollbar-thumb:hover {
                            background: #94a3b8;
                        }
                    </style>

                    <table id="customer-table-display"
                        class="min-w-full text-xs divide-y divide-gray-200 compact-table w-full">

                        <!-- Compact Header -->
                        <thead
                            class="sticky top-0 z-20 bg-gradient-to-r from-blue-950 to-blue-900 text-white shadow-sm">
                            <tr>
                                <!-- 1. Customer Code -->
                                <th
                                    class="whitespace-nowrap px-2 py-2 text-center border-b border-blue-800/50 min-w-[100px]">
                                    <div class="flex items-center justify-center gap-1 group">
                                        <span class="font-sarabun font-semibold text-[11px]">Customer Code</span>
                                        <button type="button"
                                            class="filter-icon cursor-pointer inline-flex items-center opacity-50 group-hover:opacity-100 transition-all hover:bg-red-100/20 rounded"
                                            onclick="toggleFilterDropdown('customer_code', this)"
                                            data-col="customer_code">
                                            <i class="fi fi-br-bars-filter"></i>
                                        </button>
                                    </div>
                                </th>

                                <!-- 2. Customer Name -->
                                <th
                                    class="whitespace-nowrap px-2 py-2 text-center border-b border-blue-800/50 min-w-[150px]">
                                    <div class="flex items-center justify-center gap-1 group">
                                        <span class="font-sarabun font-semibold text-[11px]">Customer Name</span>
                                        <button type="button"
                                            class="filter-icon cursor-pointer inline-flex items-center opacity-50 group-hover:opacity-100 transition-all hover:bg-red-100/20 rounded"
                                            onclick="toggleFilterDropdown('customer_name', this)"
                                            data-col="customer_name">
                                            <i class="fi fi-br-bars-filter"></i>
                                        </button>
                                    </div>
                                </th>

                                <!-- 3. PO No. -->
                                <th
                                    class="whitespace-nowrap px-2 py-2 text-center border-b border-blue-800/50 min-w-[120px]">
                                    <div class="flex items-center justify-center gap-1 group">
                                        <span class="font-sarabun font-semibold text-[11px]">PO No.</span>
                                        <button type="button"
                                            class="filter-icon cursor-pointer inline-flex items-center opacity-50 group-hover:opacity-100 transition-all hover:bg-red-100/20 rounded"
                                            onclick="toggleFilterDropdown('po_no', this)" data-col="po_no">
                                            <i class="fi fi-br-bars-filter"></i>
                                        </button>
                                    </div>
                                </th>

                                <!-- 4. PO Amount -->
                                <th
                                    class="whitespace-nowrap px-2 py-2 text-center border-b border-blue-800/50 min-w-[100px]">
                                    <div class="flex items-center justify-center gap-1 group">
                                        <span class="font-sarabun font-semibold text-[11px]">PO Amount</span>
                                        <button type="button"
                                            class="filter-icon cursor-pointer inline-flex items-center opacity-50 group-hover:opacity-100 transition-all hover:bg-red-100/20 rounded"
                                            onclick="toggleFilterDropdown('po_amount', this)" data-col="po_amount">
                                            <i class="fi fi-br-bars-filter"></i>
                                        </button>
                                    </div>
                                </th>

                                <!-- 5. PO Received Date -->
                                <th
                                    class="whitespace-nowrap px-2 py-2 text-center border-b border-blue-800/50 min-w-[100px]">
                                    <div class="flex items-center justify-center gap-1 group">
                                        <span class="font-sarabun font-semibold text-[11px]">PO Date</span>
                                        <button type="button"
                                            class="filter-icon cursor-pointer inline-flex items-center opacity-50 group-hover:opacity-100 transition-all hover:bg-red-100/20 rounded"
                                            onclick="toggleFilterDropdown('po_received_date', this)"
                                            data-col="po_received_date">
                                            <i class="fi fi-br-bars-filter"></i>
                                        </button>
                                    </div>
                                </th>

                                <!-- 6. PO Decrement -->
                                <th
                                    class="whitespace-nowrap px-2 py-2 text-center border-b border-blue-800/50 min-w-[110px]">
                                    <div class="flex items-center justify-center gap-1 group">
                                        <span class="font-sarabun font-semibold text-[11px]">PO Decrement</span>
                                        <button type="button"
                                            class="filter-icon cursor-pointer inline-flex items-center opacity-50 group-hover:opacity-100 transition-all hover:bg-red-100/20 rounded"
                                            onclick="toggleFilterDropdown('po_decrement', this)"
                                            data-col="po_decrement">
                                            <i class="fi fi-br-bars-filter"></i>
                                        </button>
                                    </div>
                                </th>

                                <!-- 7. PO Booking -->
                                <th
                                    class="whitespace-nowrap px-2 py-2 text-center border-b border-blue-800/50 min-w-[100px]">
                                    <div class="flex items-center justify-center gap-1 group">
                                        <span class="font-sarabun font-semibold text-[11px]">PO Booking</span>
                                        <button type="button"
                                            class="filter-icon cursor-pointer inline-flex items-center opacity-50 group-hover:opacity-100 transition-all hover:bg-red-100/20 rounded"
                                            onclick="toggleFilterDropdown('po_booking', this)" data-col="po_booking">
                                            <i class="fi fi-br-bars-filter"></i>
                                        </button>
                                    </div>
                                </th>

                                <!-- 8. Invoice Amount -->
                                <th
                                    class="whitespace-nowrap px-2 py-2 text-center border-b border-blue-800/50 min-w-[110px]">
                                    <div class="flex items-center justify-center gap-1 group">
                                        <span class="font-sarabun font-semibold text-[11px]">Invoice Amount</span>
                                        <button type="button"
                                            class="filter-icon cursor-pointer inline-flex items-center opacity-50 group-hover:opacity-100 transition-all hover:bg-red-100/20 rounded"
                                            onclick="toggleFilterDropdown('invoice_amount', this)"
                                            data-col="invoice_amount">
                                            <i class="fi fi-br-bars-filter"></i>
                                        </button>
                                    </div>
                                </th>

                                <!-- 9. Balanced PO (Booking) -->
                                <th
                                    class="whitespace-nowrap px-2 py-2 text-center border-b border-blue-800/50 min-w-[130px]">
                                    <div class="flex items-center justify-center gap-1 group">
                                        <span class="font-sarabun font-semibold text-[11px]">Balanced PO
                                            (Booking)</span>
                                        <button type="button"
                                            class="filter-icon cursor-pointer inline-flex items-center opacity-50 group-hover:opacity-100 transition-all hover:bg-red-100/20 rounded"
                                            onclick="toggleFilterDropdown('balanced_po_booking', this)"
                                            data-col="balanced_po_booking">
                                            <i class="fi fi-br-bars-filter"></i>
                                        </button>
                                    </div>
                                </th>

                                <!-- 10. Balanced PO (Invoice) -->
                                <th
                                    class="whitespace-nowrap px-2 py-2 text-center border-b border-blue-800/50 min-w-[130px]">
                                    <div class="flex items-center justify-center gap-1 group">
                                        <span class="font-sarabun font-semibold text-[11px]">Balanced PO
                                            (Invoice)</span>
                                        <button type="button"
                                            class="filter-icon cursor-pointer inline-flex items-center opacity-50 group-hover:opacity-100 transition-all hover:bg-red-100/20 rounded"
                                            onclick="toggleFilterDropdown('balanced_po_invoice', this)"
                                            data-col="balanced_po_invoice">
                                            <i class="fi fi-br-bars-filter"></i>
                                        </button>
                                    </div>
                                </th>

                            </tr>
                        </thead>

                        <!-- Compact Body with Red Hover -->
                        <tbody id="po-list-body" class="divide-y divide-gray-100 bg-white">
                            @forelse($data ?? [] as $row)
                            <tr
                                class="group hover:bg-red-50/70 transition-all duration-150 cursor-pointer border-b border-gray-50 last:border-0">

                                <!-- 1. Customer Code -->
                                <td data-column="customer_code"
                                    class="px-1 py-1 text-center text-xs font-sarabun text-gray-700 font-medium group-hover:text-red-700">
                                    <span
                                        class="inline-block px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 text-[10px] font-mono group-hover:bg-red-100 group-hover:text-red-800 transition-colors">
                                        {{ $row->customer_code ?? '-' }}
                                    </span>
                                </td>

                                <!-- 2. Customer Name -->
                                <td data-column="customer_name"
                                    class="px-1 py-1 text-center text-xs font-sarabun text-gray-600 truncate max-w-[150px] group-hover:text-red-700"
                                    title="{{ $row->customer_name }}">
                                    {{ Str::limit($row->customer_name ?? '-', 25) }}
                                </td>

                                <!-- 3. PO No. -->
                                <td data-column="po_no"
                                    class="px-1 py-1 text-center text-xs font-sarabun font-semibold text-indigo-600 group-hover:text-red-700">
                                    {{ $row->po_no ?? '-' }}
                                </td>

                                <!-- 4. PO Amount -->
                                <td data-column="po_amount"
                                    class="px-1 py-1.5 text-right text-xs font-sarabun font-medium text-gray-700 tabular-nums group-hover:text-red-700">
                                    {{ number_format($row->po_amount ?? 0, 0) }}
                                </td>

                                <!-- 5. PO Received Date -->
                                <td data-column="po_received_date"
                                    class="px-1 py-1 text-center text-xs font-sarabun text-gray-600 whitespace-nowrap group-hover:text-red-700">
                                    {{ $row->po_received_date ?
                                    \Carbon\Carbon::parse($row->po_received_date)->format('d/m/y') : '-' }}
                                </td>

                                <!-- 6. PO Decrement -->
                                <td data-column="po_decrement"
                                    class="px-1 py-1 text-right text-xs font-sarabun font-medium tabular-nums group-hover:text-red-700">
                                    @if(($row->po_decrement ?? 0) > 0)
                                    <span class="text-red-600 group-hover:text-red-800">-{{
                                        number_format($row->po_decrement, 0) }}</span>
                                    @else
                                    <span class="text-gray-300">-</span>
                                    @endif
                                </td>

                                <!-- 7. PO Booking -->
                                <td data-column="po_booking"
                                    class="px-1 py-1 text-right text-xs font-sarabun font-medium tabular-nums group-hover:text-red-700">
                                    @if(($row->po_booking ?? 0) > 0)
                                    <span class="text-blue-600 group-hover:text-red-700">{{
                                        number_format($row->po_booking, 0) }}</span>
                                    @else
                                    <span class="text-gray-300">-</span>
                                    @endif
                                </td>

                                <!-- 8. Invoice Amount -->
                                <td data-column="invoice_amount"
                                    class="px-1 py-1 text-right text-xs font-sarabun font-medium tabular-nums group-hover:text-red-700">
                                    @if(($row->invoice_amount ?? 0) > 0)
                                    <span class="text-green-600 group-hover:text-red-700">{{
                                        number_format($row->invoice_amount, 0) }}</span>
                                    @else
                                    <span class="text-gray-300">-</span>
                                    @endif
                                </td>

                                <!-- 9. Balanced PO (Booking) -->
                                <td data-column="balanced_po_booking"
                                    class="px-1 py-1 text-right text-xs font-sarabun font-bold tabular-nums group-hover:text-red-700">
                                    <span
                                        class="{{ ($row->balanced_po_booking ?? 0) < 0 ? 'text-red-600' : 'text-gray-800' }}">
                                        {{ number_format($row->balanced_po_booking ?? 0, 0) }}
                                    </span>
                                </td>

                                <!-- 10. Balanced PO (Invoice) -->
                                <td data-column="balanced_po_invoice"
                                    class="px-1 py-1 text-right text-xs font-sarabun font-bold tabular-nums group-hover:text-red-700">
                                    <span
                                        class="{{ ($row->balanced_po_invoice ?? 0) < 0 ? 'text-red-600' : 'text-gray-800' }}">
                                        {{ number_format($row->balanced_po_invoice ?? 0, 0) }}
                                    </span>
                                </td>

                            </tr>
                            @empty
                            <!-- Empty State -->
                            <tr>
                                <td colspan="10" class="px-4 py-8 text-center">
                                    <div class="flex flex-col items-center text-gray-400">
                                        <i class="fas fa-inbox text-xl mb-2 opacity-40"></i>
                                        <p class="text-xs font-medium text-gray-500">ไม่พบข้อมูล</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>


                <!-- Pagination -->
                <div id="listViewPagination"
                    class="flex flex-col lg:flex-row items-center justify-between space-y-4 lg:space-y-0 p-5 bg-white rounded-xl border border-gray-200 shadow-sm transition-all duration-300">

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
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                <i class="fa-solid fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>

                    <nav class="flex items-center space-x-2 order-1 lg:order-2" aria-label="Pagination">
                        {{-- Previous Button --}}
                        <button id="prevPageBtnList" onclick="goToPage(currentPage - 1)"
                            class="pagination-btn group flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200 text-gray-500 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all duration-300 disabled:opacity-30 disabled:pointer-events-none shadow-sm">
                            <i
                                class="fa-solid fa-chevron-left text-xs transition-transform group-hover:-translate-x-0.5"></i>
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
                            <i
                                class="fa-solid fa-chevron-right text-xs transition-transform group-hover:translate-x-0.5"></i>
                        </button>
                    </nav>

                    <div class="order-3 text-right">
                        <span id="paginationSummaryList"
                            class="text-xs font-sarabun text-gray-500 bg-gray-100 px-2 py-2 rounded-full">
                            แสดง <span class="text-indigo-600 font-sarabun">1-10</span> จากทั้งหมด <span
                                class="text-gray-900 font-sarabun">15</span> รายการ
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>




<!-- ก้อน Filter ที่ใช้ทุกคอลั่ม -->
<div id="column-filter-modal" class="fixed inset-0 z-[300] hidden bg-transparent">
    <div id="column-filter-content" onclick="event.stopPropagation()"
        class="shadow-2xl bg-white rounded-xl flex flex-col w-[300px] h-[450px] absolute border border-gray-100">


        <div class="px-2 pt-2">
            <button type="button" onclick="clearColumnFilterExcel()"
                class="w-full flex items-center gap-3 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-red-50 hover:text-red-600 rounded-xl transition-all group">
                <div class="w-7 h-7 flex items-center justify-center bg-slate-100 group-hover:bg-red-100 rounded-lg">
                    <i class="fa-solid fa-filter-circle-xmark"></i>
                </div>
                <span>Clear Filter from this column</span>
            </button>
        </div>

        <div class="px-2 pt-2">
            <button type="button" onclick="clearAllTableFilters()"
                class="w-full flex items-center gap-3 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-red-50 hover:text-red-600 rounded-xl transition-all group">
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
                    class="w-1/2 text-xs text-center bg-green-300 hover:bg-green-400 text-gray-800 rounded py-1">
                    Select All
                </button>
                <button type="button" id="deselectAllFilter" onclick="deselectAll()"
                    class="w-1/2 text-xs text-center bg-red-300 hover:bg-red-400 text-gray-800 rounded py-1">
                    Deselect All
                </button>
            </div>

            <!-- Sort Buttons -->
            <div class="flex justify-between space-x-2">
                <button type="button" onclick="sortAZ()"
                    class="w-1/2 text-xs text-center bg-gray-200 hover:bg-gray-300 text-gray-700 rounded py-1">
                    <i data-lucide="arrow-down-a-to-z" class="w-3.5 h-3.5"></i>
                    <span>Sort A &rarr; Z</span>
                </button>
                <button type="button" onclick="sortZA()"
                    class="w-1/2 text-xs text-center bg-gray-200 hover:bg-gray-300 text-gray-700 rounded py-1">
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
        <div id="column-filter-checkbox-list" class="overflow-y-auto px-4 py-2 text-sm max-h-60 flex-grow">
            <!-- Checkboxes generated by JS -->
        </div>

        <!-- Apply / Cancel Footer -->
        <div class="flex justify-end space-x-2 border-t px-4 py-3 bg-gray-50 rounded-b-xl">
            <button type="button" onclick="applyColumnFilter()"
                class="bg-blue-600 text-white px-4 py-2 text-xs rounded-lg font-semibold hover:bg-blue-700 transition shadow-md">OK</button>
            <button type="button" onclick="closeColumnFilterModal()"
                class="bg-white border border-gray-300 text-gray-700 px-4 py-2 text-xs rounded-lg font-semibold hover:bg-gray-100 transition shadow-sm">Cancel</button>
        </div>
    </div>
</div>






<!-- ฟังชั่น Filter  -->
<script>
    /* =====================================================
   GLOBAL VARIABLES
===================================================== */
let openFilterColumn = null;
let filters = {}; // filters["customer_code"] = ["ABC", "XYZ"]
let originalColumnValues = {};
let allRows = [];
let visibleRows = [];
let totalRows = 0;
let rowsPerPage = 18;
let currentPage = 1;
let sortState = { col: null, direction: null };

const ICONS = {
    normal: `<i class="fi fi-br-bars-filter text-xs text-gray-300"></i>`,
    filter: `<i class="fi fi-br-bars-filter text-xs text-blue-500"></i>`,
    sortAsc: `<i class="fa-solid fa-arrow-down-a-z text-xs text-indigo-500"></i>`,
    sortDesc: `<i class="fa-solid fa-arrow-down-z-a text-xs text-indigo-500"></i>`
};

/* =====================================================
   HELPER: ดึงค่าจากเซลล์ด้วย data-column
===================================================== */
function getCellValue(row, colName) {
    const cell = row.querySelector(`td[data-column="${colName}"]`);
    if (!cell) return "";
    
    const input = cell.querySelector('input');
    const select = cell.querySelector('select');
    
    if (select) {
        return select.options[select.selectedIndex]?.text.trim() || "";
    }
    if (input && input.type !== 'hidden') {
        return input.value.trim();
    }
    return cell.innerText.trim();
}

/* =====================================================
   INIT
===================================================== */
document.addEventListener("DOMContentLoaded", () => {
    // ✅ ใช้ event delegation สำหรับ filter-icon
    document.addEventListener("click", (e) => {
        const icon = e.target.closest(".filter-icon");
        if (!icon) return;
        e.stopPropagation();
        openColumnFilter(icon.dataset.col, icon); // ✅ ส่งชื่อคอลัมน์เป็น string
    });

    // โหลดข้อมูลแถว
    const tbody = document.getElementById("po-list-body");
    if (tbody) {
        allRows = Array.from(tbody.querySelectorAll("tr"));
        visibleRows = [...allRows];
        totalRows = visibleRows.length;
    }

    setupRowsPerPageOptions();
    renderPagination();
});

/* =====================================================
   OPEN FILTER
===================================================== */
function openColumnFilter(colName, icon) {
    if (openFilterColumn === colName) {
        closeColumnFilterModal();
        return;
    }

    openFilterColumn = colName;

    // ล้างค้นหา
    const searchInput = document.getElementById("column-filter-search");
    if (searchInput) searchInput.value = "";

    loadFilterValues(colName);
    updateFilterIcon(colName);
    showFilterModal(icon);

    setTimeout(() => searchInput?.focus(), 100);
}

/* =====================================================
   SHOW MODAL (จัดตำแหน่งให้ตรงคอลัมน์)
===================================================== */
function showFilterModal(icon) {
    const modal = document.getElementById("column-filter-modal");
    const box = document.getElementById("column-filter-content");
    
    modal.classList.remove("hidden");

    const rect = icon.getBoundingClientRect();
    const boxRect = box.getBoundingClientRect();
    const margin = 8;
    const headerHeight = 60; // ความสูงของ header ที่ sticky

    // คำนวณตำแหน่ง
    let left = rect.left + (rect.width / 2) - (boxRect.width / 2);
    let top = rect.bottom + margin;

    // กันล้นขวา
    if (left + boxRect.width > window.innerWidth - margin) {
        left = window.innerWidth - boxRect.width - margin;
    }
    // กันล้นซ้าย
    if (left < margin) left = margin;
    // กันล้นล่าง
    if (top + boxRect.height > window.innerHeight - margin) {
        top = window.innerHeight - boxRect.height - margin;
    }
    // ถ้า icon อยู่ใน header sticky → วางใต้ header แทน
    if (rect.top < headerHeight) {
        top = headerHeight + margin;
    }

    box.style.left = `${left}px`;
    box.style.top = `${top}px`;
}

/* =====================================================
   LOAD FILTER VALUES
===================================================== */
function loadFilterValues(colName) {
    const list = document.getElementById("column-filter-checkbox-list");
    list.innerHTML = "";

    const selected = filters[colName] ?? [];
    const values = new Set();

    // ดึงค่าจากแถวที่มองเห็น (เคารพฟิลเตอร์อื่น)
    const activeFilters = Object.keys(filters).filter(c => c !== colName);
    const sourceRows = activeFilters.length > 0 
        ? allRows.filter(row => {
            return activeFilters.every(c => {
                const val = getCellValue(row, c);
                return !filters[c] || filters[c].includes(val);
            });
        })
        : allRows;

    sourceRows.forEach(row => {
        const v = getCellValue(row, colName);
        if (v !== null && v !== undefined) values.add(v);
    });

    // สร้าง checkboxes
    Array.from(values).sort((a, b) => a.localeCompare(b, undefined, { numeric: true })).forEach(v => {
        const label = document.createElement("label");
        label.className = "flex items-center gap-2 py-1.5 px-2 rounded cursor-pointer hover:bg-red-100 transition";
        label.innerHTML = `
            <input type="checkbox" class="filter-checkbox" value="${v}" ${selected.includes(v) ? 'checked' : ''}>
            <span class="text-xs">${v === "" ? "(ว่าง)" : v}</span>
        `;
        list.appendChild(label);
    });
}

/* =====================================================
   APPLY FILTER
===================================================== */
function applyColumnFilter() {
    const col = openFilterColumn;
    const checkboxes = document.querySelectorAll(".filter-checkbox");
    const selected = [...checkboxes].filter(cb => cb.checked).map(cb => cb.value);

    if (selected.length === 0 || selected.length === checkboxes.length) {
        delete filters[col];
    } else {
        filters[col] = selected;
    }

    applyAllFilters();
    updateFilterIcon(col);
    closeColumnFilterModal();
}

function applyAllFilters() {
    visibleRows = allRows.filter(row => {
        for (let colName in filters) {
            const allowed = filters[colName];
            const value = getCellValue(row, colName);
            if (!allowed.includes(value)) return false;
        }
        return true;
    });

    totalRows = visibleRows.length;
    currentPage = 1;
    
    // ถ้ามี sort อยู่ → sort ใหม่
    if (sortState.col && sortState.direction) {
        sortTable(sortState.col, sortState.direction);
        return;
    }
    
    renderPagination();
}

/* =====================================================
   PAGINATION
===================================================== */
function renderPagination() {
    const totalPages = Math.max(1, Math.ceil(totalRows / rowsPerPage));
    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    // ซ่อนทั้งหมดก่อน
    allRows.forEach(r => r.style.display = "none");

    // แสดงเฉพาะแถวที่มองเห็นในหน้าปัจจุบัน
    if (totalRows > 0) {
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        visibleRows.slice(start, end).forEach(r => r.style.display = "");
    }

    // อัปเดตข้อความสรุป
    const summary = document.getElementById("paginationSummaryList");
    if (summary) {
        const start = totalRows === 0 ? 0 : (currentPage - 1) * rowsPerPage + 1;
        const end = Math.min(currentPage * rowsPerPage, totalRows);
        summary.innerText = `แสดง ${start}-${end} จากทั้งหมด ${totalRows} รายการ`;
    }

    // ปุ่ม Prev/Next
    const prevBtn = document.getElementById("prevPageBtnList");
    const nextBtn = document.getElementById("nextPageBtnList");
    if (prevBtn) prevBtn.disabled = currentPage === 1;
    if (nextBtn) nextBtn.disabled = currentPage >= totalPages;

    // ปุ่มหมายเลขหน้า
    const container = document.getElementById("pageNumbersList");
    if (container) {
        container.innerHTML = "";
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement("button");
            btn.innerText = i;
            btn.className = `w-10 h-10 rounded-xl font-sarabun text-sm transition-all ${
                i === currentPage 
                    ? "bg-indigo-600 text-white shadow-md" 
                    : "bg-white text-gray-600 hover:bg-indigo-50 border border-gray-200"
            }`;
            btn.onclick = () => goToPage(i);
            container.appendChild(btn);
        }
    }
}

function goToPage(page) {
    currentPage = page;
    renderPagination();
}

function changeRowsPerPage(v) {
    if (v === 'all') {
        rowsPerPage = totalRows; // 🔥 แสดงทั้งหมด
    } else {
        rowsPerPage = parseInt(v) || 10;
    }

    currentPage = 1;
    renderPagination();
}

// ฟังชันสำหรับ Dropdown จำนวนรายการ
function setupRowsPerPageOptions() {
    const select = document.getElementById("rowsPerPageList");
    if (!select) return;
    
    select.innerHTML = `
        <option value="10" ${rowsPerPage === 10 ? 'selected' : ''}>10 รายการ</option>
        <option value="20" ${rowsPerPage === 20 ? 'selected' : ''}>20 รายการ</option>
        <option value="50" ${rowsPerPage === 50 ? 'selected' : ''}>50 รายการ</option>
        <option value="100" ${rowsPerPage === 100 ? 'selected' : ''}>100 รายการ</option>
        <option value="all">ทั้งหมด (<span id="totalCountOption">${totalRows} แถว</span>)</option>
    `;
}

/* =====================================================
   SORT
===================================================== */
function sortTable(colName, direction) {
    sortState = { col: colName, direction };
    
    visibleRows.sort((a, b) => {
        let v1 = getCellValue(a, colName);
        let v2 = getCellValue(b, colName);
        
        // ลองแปลงเป็นตัวเลข
        const n1 = parseFloat(v1.replace(/,/g, '').replace('%', ''));
        const n2 = parseFloat(v2.replace(/,/g, '').replace('%', ''));
        
        if (!isNaN(n1) && !isNaN(n2)) {
            return direction === 'asc' ? n1 - n2 : n2 - n1;
        }
        
        // fallback เป็น text
        return direction === 'asc' 
            ? v1.localeCompare(v2, 'th', { numeric: true })
            : v2.localeCompare(v1, 'th', { numeric: true });
    });
    
    // ย้าย DOM ตามลำดับใหม่
    const tbody = document.getElementById("po-list-body");
    visibleRows.forEach(row => tbody.appendChild(row));
    
    currentPage = 1;
    renderPagination();
    updateAllColumnIcons();
}

function sortAZ() {
    if (openFilterColumn) {
        sortTable(openFilterColumn, 'asc');
        closeColumnFilterModal();
    }
}

function sortZA() {
    if (openFilterColumn) {
        sortTable(openFilterColumn, 'desc');
        closeColumnFilterModal();
    }
}

/* =====================================================
   ICON UPDATE
===================================================== */
function updateFilterIcon(colName) {
    const icon = document.querySelector(`.filter-icon[data-col="${colName}"]`);
    if (!icon) return;
    
    const isFiltered = filters[colName]?.length > 0;
    const isSorted = sortState.col === colName;
    
    if (isSorted) {
        icon.innerHTML = sortState.direction === 'asc' ? ICONS.sortAsc : ICONS.sortDesc;
    } else if (isFiltered) {
        icon.innerHTML = ICONS.filter;
    } else {
        icon.innerHTML = ICONS.normal;
    }
}

function updateAllColumnIcons() {
    document.querySelectorAll(".filter-icon").forEach(icon => {
        const col = icon.dataset.col;
        const isSorted = sortState.col === col;
        const isFiltered = filters[col]?.length > 0;
        
        if (isSorted) {
            icon.innerHTML = sortState.direction === 'asc' ? ICONS.sortAsc : ICONS.sortDesc;
        } else if (isFiltered) {
            icon.innerHTML = ICONS.filter;
        } else {
            icon.innerHTML = ICONS.normal;
        }
    });
}

/* =====================================================
   CLEAR FILTERS
===================================================== */
function clearColumnFilterExcel() {
    if (openFilterColumn) {
        delete filters[openFilterColumn];
        applyAllFilters();
        loadFilterValues(openFilterColumn);
        updateFilterIcon(openFilterColumn);
        closeColumnFilterModal();
    }
}


function clearAllTableFilters() {
    filters = {};
    sortState = { col: null, direction: null };
    visibleRows = [...allRows];
    totalRows = visibleRows.length;
    currentPage = 1;
    renderPagination();
    updateAllColumnIcons();
    closeColumnFilterModal();
}

/* =====================================================
   MODAL CLOSE
===================================================== */
function closeColumnFilterModal() {
    document.getElementById("column-filter-modal")?.classList.add("hidden");
    openFilterColumn = null;
}

// ปิดเมื่อคลิกนอก modal
document.addEventListener("mousedown", (e) => {
    const modal = document.getElementById("column-filter-modal");
    const box = document.getElementById("column-filter-content");
    if (modal && !modal.classList.contains("hidden") && box && !box.contains(e.target)) {
        closeColumnFilterModal();
    }
});

// ปิดด้วย ESC / Apply ด้วย Enter
document.addEventListener("keydown", (e) => {
    const modal = document.getElementById("column-filter-modal");
    if (!modal || modal.classList.contains("hidden")) return;
    
    if (e.key === "Escape") {
        e.preventDefault();
        closeColumnFilterModal();
    }
    if (e.key === "Enter" && document.activeElement?.id === "column-filter-search") {
        e.preventDefault();
        applyColumnFilter();
    }
});

/* =====================================================
   SEARCH IN FILTER
===================================================== */
function handleSearch(text) {
    const keyword = text.toLowerCase().trim();
    const labels = document.querySelectorAll("#column-filter-checkbox-list label");
    
    labels.forEach(label => {
        const checkbox = label.querySelector("input");
        const value = label.querySelector("span")?.innerText.toLowerCase() || "";
        
        if (keyword === "") {
            label.style.display = "";
            checkbox.checked = false;
        } else if (value.includes(keyword)) {
            label.style.display = "";
            checkbox.checked = true;
        } else {
            label.style.display = "none";
            checkbox.checked = false;
        }
    });
}

function handleSearchEnter(e) {
    if (e.key === "Enter") {
        e.preventDefault();
        applyColumnFilter();
    }
}

function selectAll() {
    document.querySelectorAll("#column-filter-checkbox-list .filter-checkbox")
        .forEach(cb => cb.checked = true);
}

function deselectAll() {
    document.querySelectorAll("#column-filter-checkbox-list .filter-checkbox")
        .forEach(cb => cb.checked = false);
}
</script>












@endsection