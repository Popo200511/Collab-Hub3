@extends('layouts.user')

@section('title', 'PO หรือ Purchase Order ใบสั่งซื้อ')


<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- Font: Inter -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">

<!-- Load Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


<!-- Export To Excel -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<!-- Hover สำหรับ Filter -->
<style>
    .filter-active i {
        color: #60a5fa !important;
    }

    thead th:hover .filter-icon:not(.filter-active) i {
        color: #93c5fd;
    }
</style>

<style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f1f5f9;
        /* Slate 100 as background */
    }

    .text-header {
        font-size: 2.25rem;
        /* 3xl */
        font-weight: 800;
        /* Extra bold */
        color: #1e293b;
        /* Slate 900 */
    }

    .table-striped tbody tr:nth-child(odd) {
        background-color: #f8fafc;
        /* Sky 50 for subtle striping */
    }

    /* Custom class to manage the hover visibility */
    .group:hover .po-detail-preview {
        display: block;
    }

    /* Adjustments for fixed width hover table */
    .po-detail-preview {
        max-width: 90vw;
        /* Ensure it fits on smaller screens */
    }
</style>
</head>

@section('content')

<!-- Container for temporary messages (e.g., error notification) -->
<div id="messageContainer" class="fixed top-5 right-5 z-50"></div>

<div id="app"
    class="mx-auto w-full bg-white shadow-2xl rounded-2xl p-4 sm:p-6 md:p-8 lg:p-10 h-auto min-h-[600px] sm:min-h-[730px]">

    <header class="mb-4 flex flex-row items-center justify-between gap-4">

        <!-- ปุ่ม PO Decrement และ ปุ่ม PO Received From Customer -->
        <button type="button" onclick="openDecrementModal()" class="shrink-0 bg-indigo-600 hover:bg-indigo-700 text-white font-medium 
                    py-1.5 px-4 rounded-lg shadow-sm
                    transition-all duration-200 flex items-center justify-center gap-1.5 text-xs sm:text-sm">
            <i class="fas fa-minus text-[10px]"></i>
            <span>PO Decrement</span>
        </button>

        <button type="button" onclick="openPOModal()" class="shrink-0 bg-indigo-600 hover:bg-indigo-700 text-white font-medium 
                py-1.5 px-4 rounded-lg shadow-sm
                transition-all duration-200 flex items-center justify-center gap-1.5 text-xs sm:text-sm">
            <i class="fas fa-plus text-[10px]"></i>
            <span>PO Received From Customer</span>
        </button>

        <!-- Modal PO Decrement -->
        <div id="decrementModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
                <form action="{{ route('po.purchase.PO_Decrement') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PO Decrement *</label>
                        <input type="text" name="po_decrement" required class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" onclick="closeDecrementModal()"
                            class="px-4 py-2 bg-gray-100 rounded">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded">Confirm</button>
                    </div>
                </form>
            </div>
        </div>



        <!-- Modal PO Received From Customer -->
        <div id="poModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
                <form action="{{ route('po.purchase.PO_Received') }}" method="POST" class="p-6 space-y-4">
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
                                <input type="number" name="po_amount" step="0.01" min="0" required
                                    class="w-full border rounded-lg px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">PO Received Date</label>
                                <input type="date" name="po_received_date" required
                                    class="w-full border rounded-lg px-3 py-2">
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" onclick="closePOModal()"
                            class="px-4 py-2 bg-gray-100 rounded">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded">Save PO</button>
                    </div>
                </form>
            </div>
        </div>

    </header>

    <!-- 1. PO List View (Index) -->
    <div id="listView" class="view-section">

        <!-- Section Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 mb-4 sm:mb-6">
            <h2
                class="text-lg sm:text-2xl font-bold text-gray-800 flex items-center justify-center sm:justify-start gap-2">
                <i class="fas fa-list text-indigo-500 text-base sm:text-xl"></i>
                <span>ประวัติการสั่งซื้อ</span>
            </h2>

            <button type="button" id="exportPOToExcel" onclick="exportPOToExcel()" class="w-full sm:w-auto rounded-xl bg-green-500 hover:bg-green-600 active:bg-green-700
                       px-4 sm:px-6 py-2.5 sm:py-2 text-sm font-bold text-white 
                       shadow-lg hover:shadow-xl transition-all duration-200 
                       transform hover:scale-[1.02] active:scale-[0.98]
                       flex items-center justify-center gap-2">
                <i class="fas fa-file-excel"></i>
                <span class="hidden sm:inline">Export</span>
                <span class="sm:hidden">Export</span>
            </button>
        </div>

        <!-- Table Container -->
        <div id="tableContainer" class="overflow-hidden rounded-xl shadow-xl border border-gray-200">

            <!-- Horizontal Scroll Wrapper for Table -->
            <div class="overflow-x-auto">
                <!-- กำหนดความสูงแบบ responsive -->
                <div class="max-h-64 sm:max-h-80 md:max-h-96 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200 table-auto sm:table-fixed w-full">
                        <thead class="bg-indigo-600 text-white sticky top-0 z-10">
                            <tr>
                                @php
                                $columns = [
                                ['key' => 'customer_code', 'label' => 'Customer Code', 'class' => 'w-24 sm:w-32
                                bg-purple-600 text-white'],
                                ['key' => 'customer_name', 'label' => 'Customer Name', 'class' => 'w-24 sm:w-32
                                bg-purple-600 text-white'],
                                ['key' => 'po_no', 'label' => 'PO No.', 'class' => 'w-20 sm:w-28 bg-purple-600
                                text-white'],
                                ['key' => 'total_amount', 'label' => 'PO Amount', 'class' => 'w-24 sm:w-32 bg-purple-600
                                text-white'],
                                ['key' => 'po_received_date', 'label' => 'PO Received date', 'class' => 'w-24 sm:w-32
                                bg-purple-600 text-white'],
                                ['key' => 'po_decrement', 'label' => 'PO Decrement', 'class' => 'w-24 sm:w-32'],
                                ['key' => 'po_booking', 'label' => 'PO Booking', 'class' => 'w-24 sm:w-32'],
                                ['key' => 'invoice_amount', 'label' => 'Invoice Amount', 'class' => 'w-24 sm:w-32'],
                                ['key' => 'balanced_po_booking', 'label' => 'Balanced PO (Booking)', 'class' => 'w-24
                                sm:w-32'],
                                ['key' => 'balanced_po_invoice', 'label' => 'Balanced PO (Invoice)', 'class' => 'w-24
                                sm:w-32'],
                                ];
                                @endphp

                                @foreach($columns as $col)
                                <th
                                    class="{{ $col['class'] }} whitespace-nowrap px-2 sm:px-4 py-3 text-center border-b border-indigo-500">
                                    <div class="flex items-center justify-center gap-1 sm:gap-2">
                                        <span class="tracking-wide text-[10px] sm:text-xs text-white/90">
                                            {{ $col['label'] }}
                                        </span>
                                        <button type="button"
                                            class="filter-icon cursor-pointer inline-flex items-center opacity-60 hover:opacity-100 transition-opacity p-0.5 rounded hover:bg-white/10"
                                            onclick="toggleFilterDropdown('{{ $col['key'] }}')"
                                            data-column="{{ $col['key'] }}">
                                            <i class="fi fi-br-bars-filter text-[10px] sm:text-xs"></i>
                                        </button>
                                    </div>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody id="po-list-body" class="divide-y divide-gray-100 bg-white">
                            <!-- Rows injected by JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty State -->
            <p id="noPoMessage" class="hidden px-4 sm:px-6 py-8 sm:py-12 text-center text-gray-500">
                <span class="inline-flex flex-col items-center">
                    <i class="fas fa-inbox text-3xl sm:text-4xl mb-2 sm:mb-3 opacity-40"></i>
                    <span class="text-sm sm:text-base font-medium">ไม่พบรายการใบสั่งซื้อ</span>
                    <span class="text-xs sm:text-sm text-gray-400">เริ่มสร้างใบสั่งซื้อแรกของคุณ</span>
                </span>
            </p>

            <!-- Pagination - Responsive Layout -->
            <div id="listViewPagination"
                class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4 p-4 sm:p-5 bg-gray-50 border-t border-gray-200">

                <!-- Rows Per Page -->
                <div class="flex items-center justify-center sm:justify-start gap-2 sm:gap-3 order-2 sm:order-1">
                    <label for="rowsPerPageList"
                        class="text-[10px] sm:text-xs font-medium text-gray-600 whitespace-nowrap">แสดง:</label>
                    <div class="relative">
                        <select id="rowsPerPageList" onchange="changeRowsPerPage(this.value)" class="block py-1.5 sm:py-2 pl-3 sm:pl-4 pr-8 sm:pr-10 
                                   border border-gray-200 rounded-lg sm:rounded-xl 
                                   text-[10px] sm:text-xs bg-white cursor-pointer 
                                   appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500/20 
                                   focus:border-indigo-500 transition-all min-w-[70px] sm:min-w-auto">
                            <option value="10" selected>10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 sm:px-3 text-gray-400">
                            <i class="fa-solid fa-chevron-down text-[8px] sm:text-[10px]"></i>
                        </div>
                    </div>
                    <span class="text-[10px] sm:text-xs text-gray-500 hidden sm:inline">รายการ</span>
                </div>

                <!-- Page Numbers -->
                <nav class="flex items-center justify-center gap-1 sm:gap-2 order-1 sm:order-2" aria-label="Pagination">
                    <button id="prevPageBtnList" onclick="goToPage(currentPage - 1)" class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl 
                               border border-gray-200 text-gray-500 
                               hover:bg-indigo-600 hover:text-white hover:border-indigo-600 
                               transition-all duration-200 disabled:opacity-30 disabled:pointer-events-none">
                        <i class="fa-solid fa-chevron-left text-[10px] sm:text-xs"></i>
                    </button>

                    <div id="pageNumbersList" class="flex items-center gap-1">
                        <!-- Dynamic buttons -->
                    </div>

                    <button id="nextPageBtnList" onclick="goToPage(currentPage + 1)" class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl 
                               border border-gray-200 text-gray-500 
                               hover:bg-indigo-600 hover:text-white hover:border-indigo-600 
                               transition-all duration-200 disabled:opacity-30 disabled:pointer-events-none">
                        <i class="fa-solid fa-chevron-right text-[10px] sm:text-xs"></i>
                    </button>
                </nav>

                <!-- Summary -->
                <div class="flex items-center justify-center sm:justify-end order-3">
                    <span id="paginationSummaryList"
                        class="text-[10px] sm:text-xs text-gray-500 bg-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-full border border-gray-200 whitespace-nowrap">
                        <span class="font-medium text-indigo-600">1-10</span> จาก <span class="font-medium">15</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. PO Detail View (Show) -->
    <div id="showView" class="view-section hidden">

        <!-- Back Button + Export -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 mb-4 sm:mb-6">
            <button onclick="setView('list')" class="w-full sm:w-auto text-indigo-600 hover:text-indigo-800 flex items-center justify-center sm:justify-start 
                            transition-colors font-medium p-2 sm:p-2 -ml-1 sm:-ml-2 rounded-lg hover:bg-indigo-50">
                <i class="fas fa-arrow-left h-4 w-4 sm:h-5 sm:w-5 mr-2"></i>
                <span class="text-sm sm:text-base">กลับไปยังรายการ</span>
            </button>

            <button type="button" id="exportPOandItemToExcel" onclick="exportPOandItemToExcel()" class="w-full sm:w-auto rounded-xl bg-green-500 hover:bg-green-600 active:bg-green-700
                            px-4 sm:px-6 py-2.5 sm:py-2 text-sm font-bold text-white 
                            shadow-lg hover:shadow-xl transition-all duration-200 
                            transform hover:scale-[1.02] active:scale-[0.98]
                            flex items-center justify-center gap-2">
                <i class="fas fa-file-excel"></i>
                <span>Export</span>
            </button>
        </div>

        <div class="bg-white p-4 rounded-lg border border-indigo-100 shadow-sm mb-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 items-start">

                <div class="col-span-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">PO Number</p>
                    <p id="detailPoNo"
                        class="text-base sm:text-lg font-bold text-indigo-600 mt-0.5 break-all leading-tight">
                        {{ $po->po_no }}
                    </p>
                </div>

                <div class="col-span-1 md:col-span-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Customer</p>
                    <p id="detailCustomer" class="text-sm sm:text-base font-semibold text-gray-700 mt-0.5 truncate">
                        {{ $po->customer_name }}
                    </p>
                </div>

                <div class="col-span-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">PO Date</p>
                    <p id="detailPoDate" class="text-sm font-medium text-gray-600 mt-0.5">
                        {{ $po->po_date }}
                    </p>
                </div>

                <div class="col-span-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status</p>
                    <div class="mt-1">
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-50 text-green-700 border border-green-200">
                            <i class="fas fa-circle text-[6px] mr-1.5"></i>
                            <span>ดำเนินการอยู่</span>
                        </span>
                    </div>
                </div>

            </div>
        </div>

        <div class="flex items-center mb-3">
            <div class="w-1 h-5 bg-green-500 rounded-full mr-2.5"></div>
            <h3 class="text-sm sm:text-base font-bold text-gray-700 tracking-tight">
                รายการสินค้า/บริการ
            </h3>
        </div>

        <div class="overflow-hidden rounded-xl shadow-lg border border-gray-200">
            <!-- Horizontal Scroll for Wide Table -->
            <div class="overflow-x-auto">
                <div class="max-h-48 sm:max-h-64 md:max-h-[350px] overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200 w-full table-auto">
                        <thead class="bg-green-600 text-white sticky top-0 z-10">
                            <tr>
                                <th
                                    class="px-2 sm:px-4 py-2 sm:py-3 text-left text-[9px] sm:text-xs font-bold uppercase tracking-wider whitespace-nowrap rounded-tl-xl">
                                    Refcode
                                </th>
                                <th
                                    class="px-2 sm:px-4 py-2 sm:py-3 text-left text-[9px] sm:text-xs font-bold uppercase tracking-wider min-w-[120px] sm:min-w-[200px]">
                                    Job Description
                                </th>
                                <th
                                    class="px-2 sm:px-4 py-2 sm:py-3 text-right text-[9px] sm:text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                                    Booking Date
                                </th>
                                <th
                                    class="px-2 sm:px-4 py-2 sm:py-3 text-right text-[9px] sm:text-xs font-bold uppercase tracking-wider whitespace-nowrap w-14 sm:w-20">
                                    Booking No.
                                </th>
                                <th
                                    class="px-2 sm:px-4 py-2 sm:py-3 text-right text-[9px] sm:text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                                    PO No.
                                </th>
                                <th
                                    class="px-2 sm:px-4 py-2 sm:py-3 text-right text-[9px] sm:text-xs font-bold uppercase tracking-wider whitespace-nowrap hidden sm:table-cell">
                                    PO Amount
                                </th>
                                <th
                                    class="px-2 sm:px-4 py-2 sm:py-3 text-right text-[9px] sm:text-xs font-bold uppercase tracking-wider whitespace-nowrap hidden sm:table-cell">
                                    Status
                                </th>
                                <th
                                    class="px-2 sm:px-4 py-2 sm:py-3 text-right text-[9px] sm:text-xs font-bold uppercase tracking-wider whitespace-nowrap hidden md:table-cell">
                                    Delete Reason
                                </th>
                            </tr>
                        </thead>
                        <tbody id="po-items-detail-body" class="divide-y divide-gray-100 bg-white">
                            <!-- Items rendered by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Action Buttons - Stack on Mobile -->
        <div class="mt-6 flex flex-row justify-end gap-2">
            <button onclick="showTemporaryMessage('ฟังก์ชันแก้ไข PO ยังไม่เปิดใช้งาน', 'warning')" class="flex-1 sm:flex-none bg-white border border-amber-500 text-amber-600 hover:bg-amber-50 
                px-4 py-1.5 rounded-lg font-medium text-sm
                transition-colors duration-200 flex items-center justify-center gap-2">
                <i class="fas fa-pen-to-square text-xs"></i>
                <span>แก้ไข</span>
            </button>

            <button onclick="showTemporaryMessage('ฟังก์ชันลบ PO ยังไม่เปิดใช้งาน', 'danger')" class="flex-1 sm:flex-none bg-white border border-red-500 text-red-600 hover:bg-red-50 
                px-4 py-1.5 rounded-lg font-medium text-sm
                transition-colors duration-200 flex items-center justify-center gap-2">
                <i class="fas fa-trash-can text-xs"></i>
                <span>ลบ</span>
            </button>
        </div>
    </div>
</div>

<!-- Filter Modal - Responsive Positioning -->
<div id="column-filter-modal" class="fixed inset-0 z-[9999] hidden bg-black/30 backdrop-blur-[2px]">
    <div id="column-filter-content" onclick="event.stopPropagation()" class="shadow-2xl bg-white rounded-2xl flex flex-col 
               w-[95vw] sm:w-[300px] max-w-sm 
               h-auto max-h-[85vh] sm:h-[450px] 
               absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 sm:left-auto sm:top-auto sm:translate-x-0 sm:translate-y-0
               border border-gray-100">

        <!-- Filter Actions -->
        <div class="p-3 sm:p-4 border-b border-gray-100 space-y-2">
            <button onclick="clearColumnFilterExcel()" class="w-full flex items-center gap-3 px-3 py-2 text-xs text-gray-600 
                       hover:bg-red-50 hover:text-red-600 rounded-xl transition-colors group">
                <div
                    class="w-7 h-7 flex items-center justify-center bg-gray-100 group-hover:bg-red-100 rounded-lg transition-colors flex-shrink-0">
                    <i class="fa-solid fa-filter-circle-xmark text-sm"></i>
                </div>
                <span class="truncate">ล้างฟิลเตอร์คอลัมน์นี้</span>
            </button>
            <button onclick="clearAllTableFilters()" class="w-full flex items-center gap-3 px-3 py-2 text-xs text-gray-600 
                       hover:bg-red-50 hover:text-red-600 rounded-xl transition-colors group">
                <div
                    class="w-7 h-7 flex items-center justify-center bg-gray-100 group-hover:bg-red-100 rounded-lg transition-colors flex-shrink-0">
                    <i class="fa-solid fa-broom"></i>
                </div>
                <span class="truncate">ล้างฟิลเตอร์ทั้งหมด</span>
            </button>
        </div>

        <!-- Selection/Sort Controls -->
        <div class="px-3 sm:px-4 py-3 border-b border-gray-100 space-y-3">
            <div class="flex gap-2">
                <button onclick="selectAll()"
                    class="flex-1 py-1.5 text-xs font-medium bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 transition-colors">
                    เลือกทั้งหมด
                </button>
                <button onclick="deselectAll()"
                    class="flex-1 py-1.5 text-xs font-medium bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                    ไม่เลือกทั้งหมด
                </button>
            </div>
            <div class="flex gap-2">
                <button onclick="sortAZ()"
                    class="flex-1 py-1.5 text-xs font-medium bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center justify-center gap-1">
                    <i data-lucide="arrow-down-a-to-z" class="w-3 h-3"></i> A→Z
                </button>
                <button onclick="sortZA()"
                    class="flex-1 py-1.5 text-xs font-medium bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center justify-center gap-1">
                    <i data-lucide="arrow-up-z-to-a" class="w-3 h-3"></i> Z→A
                </button>
            </div>
        </div>

        <!-- Search -->
        <div class="px-3 sm:px-4 py-3 border-b border-gray-100">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" id="column-filter-search" placeholder="ค้นหา..."
                    class="pl-9 pr-3 w-full h-9 outline-none bg-gray-50 border border-gray-200 rounded-lg text-sm transition-all focus:border-indigo-400 focus:bg-white">
            </div>
        </div>

        <!-- Checkbox List -->
        <div id="column-filter-checkbox-list" class="overflow-y-auto px-3 sm:px-4 py-2 text-sm space-y-1 flex-grow">
        </div>

        <!-- Apply Buttons -->
        <div class="flex justify-end gap-2 p-3 sm:p-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
            <button onclick="closeColumnFilterModal()"
                class="bg-white border border-gray-300 text-gray-700 px-4 py-2 text-xs rounded-lg hover:bg-gray-100 transition shadow-sm">
                ยกเลิก
            </button>
            <button onclick="applyColumnFilter()"
                class="bg-indigo-600 text-white px-4 py-2 text-xs rounded-lg hover:bg-indigo-700 transition shadow-md">
                ตกลง
            </button>
        </div>
    </div>
</div>


<!-- ฟังชั่น Modal สำหรับ PO Decrement -->
<script>
    function openDecrementModal() {
        const modal = document.getElementById('decrementModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // ล็อก scroll พื้นหลัง
    }

    function closeDecrementModal() {
        const modal = document.getElementById('decrementModal');
        modal.classList.add('hidden');
        document.body.style.overflow = ''; // ปลดล็อก scroll
    }

    // ปิดเมื่อกดที่พื้นหลังสีดำ
    document.getElementById('decrementModal').addEventListener('click', function(e) {
        if (e.target === this) closeDecrementModal();
    });

    // ปิดเมื่อกดปุ่ม ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDecrementModal();
    });
</script>

<!-- ฟังชั่น Modal สำหรับ PO Received From Customer -->
<script>
    function openPOModal() {
        const modal = document.getElementById('poModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // ล็อก scroll พื้นหลัง
    }

    function closePOModal() {
        const modal = document.getElementById('poModal');
        modal.classList.add('hidden');
        document.body.style.overflow = ''; // ปลดล็อก scroll
    }

    // ปิดเมื่อกดที่พื้นหลังสีดำ
    document.getElementById('poModal').addEventListener('click', function(e) {
        if (e.target === this) closePOModal();
    });

    // ปิดเมื่อกดปุ่ม ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closePOModal();
    });
</script>


<!-- ฟังชั่น Filter  -->
<script>
    let currentColumn = null;
let allValues = [];
let filterState = {};           // filtered / closed / open
let filterStateValues = {};     // เก็บค่า checkbox ที่เลือกแต่ละ column
let tempSelected = []; // เก็บค่าที่ติ๊กก่อน Apply


// เปิด modal
function toggleFilterDropdown(column) {
    const modal = document.getElementById("column-filter-modal");

    // ถ้าเปิดอยู่ และกดคอลัมน์เดิม → ปิด
    if (!modal.classList.contains("hidden") && currentColumn === column) {
        closeColumnFilterModal();
        return;
    }

    currentColumn = column;

    loadColumnValues(column);
    showColumnFilterModal(column, allValues);

    const btn = document.querySelector(`button[data-column="${column}"]`);
    if (btn) {
        btn.innerHTML = `<i class="fi fi-br-bars-filter"></i>`;
    }
}

function showColumnFilterModal(column, values) {
    currentColumn = column;
    
    const list = document.getElementById('column-filter-checkbox-list');
    list.innerHTML = '';

    // โหลดค่าที่เคย apply ไปแล้ว
    const applied = filterStateValues[column] ? [...filterStateValues[column]] : [];

    // temp จะใช้เฉพาะตอนที่ popup เปิด
    tempSelected = [...applied];

    values.forEach(v => {
        const id = `chk-${column}-${v}`;
        const wrapper = document.createElement('div');
        wrapper.className = 'flex items-center space-x-2 mb-1';
        wrapper.innerHTML = `
            <input type="checkbox" id="${id}" value="${v}" class="column-filter-checkbox">
            <label for="${id}" class="text-sm">${v}</label>
        `;
        list.appendChild(wrapper);

        let checkbox = wrapper.querySelector("input");

        // ถ้าค่าที่เคย apply ถูกเลือก → ติ๊กไว้
        if (applied.includes(v)) checkbox.checked = true;

        // checkbox เปลี่ยนแต่ยังไม่ filter
        checkbox.addEventListener("change", () => {
            if (checkbox.checked) {
                if (!tempSelected.includes(v)) {
                    tempSelected.push(v);
                }
            } else {
                tempSelected = tempSelected.filter(x => x !== v);
            }
        });
    });

    // แสดง modal
    document.getElementById("column-filter-modal").classList.remove("hidden");
}



// Apply Filter
function applyColumnFilter() {
    const checkedValues = filterStateValues[currentColumn] || [];
    const btn = document.querySelector(`button[data-column="${currentColumn}"]`);

    if (checkedValues.length > 0) {
        btn.innerHTML = `<i class="fas fa-filter"></i>`;
        filterState[currentColumn] = "filtered";
    } else {
        btn.innerHTML = `<i class="fas fa-chevron-down"></i>`;
        filterState[currentColumn] = "closed";
    }

    closeColumnFilterModal();
    filterAllColumns();
}

// ปิด modal
function closeColumnFilterModal() {
    const modal = document.getElementById('column-filter-modal');
    modal.classList.add('hidden');
    if (!currentColumn) return;

    const btn = document.querySelector(`button[data-column="${currentColumn}"]`);
    if (!btn) return;

    if (filterState[currentColumn] !== "filtered") {
        btn.innerHTML = `<i class="fas fa-chevron-down"></i>`;
        filterState[currentColumn] = "closed";
    }
}

// Load values จาก table
function loadColumnValues(column) {
    const rows = Array.from(document.querySelectorAll('#po-list-body tr'));
    
    // ดึงเฉพาะแถวที่ยังไม่ถูกซ่อน
    const values = rows
        .filter(row => row.style.display !== "none")
        .map(row => {
            const cell = row.querySelector(`td[data-column="${column}"]`);
            return cell ? cell.textContent.trim() : "";
        })
        .filter(v => v !== "");

    allValues = [...new Set(values)];
}


// Filter ทุกคอลัมน์
function filterAllColumns() {
    const rows = document.querySelectorAll('#po-list-body tr');

    rows.forEach(row => {
        let visible = true;

        for (let col in filterStateValues) {
            const selected = filterStateValues[col];
            const cell = row.querySelector(`td[data-column="${col}"]`);
            if (!cell) continue;

            if (selected.length > 0 && !selected.includes(cell.textContent.trim())) {
                visible = false;
                break;
            }
        }

        row.style.display = visible ? '' : 'none';
    });
}


// Search
function handleSearch(text) {
    const keyword = text.toLowerCase().trim();
    const wrappers = document.querySelectorAll("#column-filter-checkbox-list > div");

    wrappers.forEach(wrapper => {
        const label = wrapper.querySelector("label");
        const value = label.textContent.toLowerCase();

        if (value.includes(keyword)) {
            wrapper.style.display = "flex";
        } else {
            wrapper.style.display = "none";
        }
    });
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

// Select/Deselect All
function selectAll() {
    document.querySelectorAll('#column-filter-checkbox-list input').forEach(chk => chk.checked = true);
    filterStateValues[currentColumn] = [...allValues];
    filterAllColumns();
}
function deselectAll() {
    document.querySelectorAll('#column-filter-checkbox-list input').forEach(chk => chk.checked = false);
    filterStateValues[currentColumn] = [];
    filterAllColumns();
}

function updateColumnIcon(column) {
    const btn = document.querySelector(`button[data-column="${column}"]`);
    const selected = filterStateValues[column];

    if (selected.length > 0) {
        btn.innerHTML = `<i class="fas fa-filter"></i>`;
        filterState[column] = "filtered";
    } else {
        btn.innerHTML = `<i class="fas fa-chevron-up"></i>`;
        filterState[column] = "open";
    }
}

</script>





<script>
    function exportPOandItemToExcel() {
    // ====== 1. ดึงข้อมูล PO Header ======
    const poNo = document.getElementById('detailPoNo').textContent.trim();
    const customer = document.getElementById('detailCustomer').textContent.trim();
    const poDate = document.getElementById('detailPoDate').textContent.trim();
    const totalOriginal = document.getElementById('detailTotalOriginalAmount').textContent.trim();
    const totalBalanced = document.getElementById('detailTotalBalancedAmount').textContent.trim();

    // ====== 2. ดึงข้อมูล PO Items จาก <tbody> ======
    const tableBody = document.getElementById('po-items-detail-body');
    if (!tableBody || tableBody.rows.length === 0) {
        alert("ไม่มีรายการสินค้า/บริการสำหรับ Export");
        return;
    }

    const items = Array.from(tableBody.rows).map(row => ({
        "Refcode": row.cells[0].textContent.trim(),
        "Job Description": row.cells[1].textContent.trim(),
        "Booking Date": row.cells[2].textContent.trim(),
        "Booking No.": row.cells[3].textContent.trim(),
        "PO No.	": row.cells[4].textContent.trim(),
        "PO Amount": row.cells[5].textContent.trim(),
        "Status	": row.cells[6].textContent.trim(),
        "Delete Reason": row.cells[7].textContent.trim(),
    }));

    // ====== 3. สร้าง worksheet สำหรับ Header ======
    const headerData = [
        ["PO NO", poNo],
        ["Customer", customer],
        ["PO Date", poDate],
        ["Total Amount", totalOriginal],
        ["Balanced Amount", totalBalanced],
        [] // เว้นบรรทัดว่าง
    ];
    const wsHeader = XLSX.utils.aoa_to_sheet(headerData);

    // ====== 4. สร้าง worksheet สำหรับ Items ======
    const wsItems = XLSX.utils.json_to_sheet(items, { origin: 0 });

    // ====== 5. สร้าง Workbook และเพิ่ม Sheet ======
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, wsHeader, "PO Header");
    XLSX.utils.book_append_sheet(wb, wsItems, "PO Items");

    // ====== 6. ดาวน์โหลดไฟล์ Excel ======
    XLSX.writeFile(wb, `PO_${poNo}.xlsx`);
}
</script>





<!-- Export PO to Excel -->
<script>
    function exportPOToExcel() {
            if (!purchaseOrders || purchaseOrders.length === 0) {
                alert("ไม่มีข้อมูลสำหรับ Export");
                return;
            }

            // แปลงข้อมูลเป็นรูปแบบ array ของ object
            const data = purchaseOrders.map(po => ({
                "PO No.": po.po_no,
                "Customer Name": po.customer_name,
                "PO Date": po.po_date,
                "Total Amount": po.total_amount,
                "Balanced Amount": po.balanced_amount
            }));

            // สร้าง worksheet
            const ws = XLSX.utils.json_to_sheet(data);

            // สร้าง workbook และเพิ่ม worksheet
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Purchase Orders");

            // ดาวน์โหลดไฟล์ Excel
            XLSX.writeFile(wb, "purchase_orders.xlsx");
        }
</script>



<script>
    window.purchaseOrders = @json($purchaseOrders ?? []);
</script>


<script>
    // --- Helper ---
        const formatCurrency = (num) => Number(num || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");


        let currentPoId = null;

        // --- View Switch ---
        function setView(view, poId = null) {
            const listView = document.getElementById('listView');
            const showView = document.getElementById('showView');

            if (view === 'show' && poId !== null) {
                listView.classList.add('hidden');
                showView.classList.remove('hidden');
                currentPoId = poId;
                renderPODetail(poId);
            } else {
                showView.classList.add('hidden');
                listView.classList.remove('hidden');
                currentPoId = null;
            }
        }

        // --- Render PO List ---
        function renderPOList() {
            const listBody = document.getElementById('po-list-body');
            listBody.innerHTML = purchaseOrders.map(po => `
                <tr onclick="setView('show', ${po.purchase_order_id})"
                    class="cursor-pointer hover:bg-red-500 transition table w-full table-fixed">

                    <td class="w-1/5 px-2 py-2 font-semibold text-indigo-700">${po.po_no}</td>

                    <td class="w-2/5 px-2 py-2">${po.customer_name}</td>

                    <td class="w-1/5 px-2 py-2">${po.po_date}</td>

                    <td class="w-1/5 px-2 py-2 text-right">${formatCurrency(po.total_amount)}</td>

                    <td class="w-1/5 px-2 py-2 text-right font-bold text-blue-600">
                        ${formatCurrency(po.balanced_amount)}
                    </td>

                </tr>

            `).join('');
        }

// --- Render PO Detail & Items ---
function renderPODetail(poId) {

    // 1. โหลดข้อมูล Items ของ PO ด้วย AJAX
    fetch("{{ url('/po-items') }}/" + poId)
        .then(res => res.json())
        .then(data => {

            const relatedItems = data.items;

            // ========== 2. หาข้อมูลหัว PO ==========
            const po = purchaseOrders.find(p => p.purchase_order_id == poId);
            if (!po) return;

            document.getElementById('detailPoNo').textContent = po.po_no;
            document.getElementById('detailCustomer').textContent = po.customer_name;
            document.getElementById('detailPoDate').textContent = po.po_date;

            // ========== 3. Render Items ==========
            const body = document.getElementById('po-items-detail-body');
            body.innerHTML = relatedItems.map(item => `
                <tr>
                    <td class="px-4 py-3">${item.item_code}</td>
                    <td class="px-4 py-3">${item.item_description}</td>
                    <td class="px-4 py-3 text-right">${formatCurrency(item.unit_price)}</td>
                    <td class="px-4 py-3 text-right">${item.qty}</td>
                    <td class="px-4 py-3 text-right">${formatCurrency(item.amount)}</td>
                    <td class="px-4 py-3 text-right">${item.used_qty || 0}</td>
                    <td class="px-4 py-3 text-right">${formatCurrency(item.used_amount || 0)}</td>
                    <td class="px-4 py-3 text-right">${item.balanced_qty}</td>
                </tr>
            `).join('');

            // ========== 4. Summary ==========
            const totalOriginal = relatedItems.reduce((sum, i) => sum + Number(i.amount), 0);
            const totalBalanced = relatedItems.reduce((sum, i) => sum + Number(i.balanced_amount), 0);

            document.getElementById('detailTotalOriginalAmount').textContent = formatCurrency(totalOriginal);
            document.getElementById('detailTotalBalancedAmount').textContent = formatCurrency(totalBalanced);
        });
}

</script>






<!-- ฟังชั่นสำหรับ Iterator Pagination -->
<script>
    let currentPage = 1;
        let rowsPerPage = 10;

        function renderTable() {
            const tbody = document.getElementById('po-list-body');
            tbody.innerHTML = '';

            if (!purchaseOrders || purchaseOrders.length === 0) {
                document.getElementById('noPoMessage').classList.remove('hidden');
                return;
            } else {
                document.getElementById('noPoMessage').classList.add('hidden');
            }

            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            const pageItems = purchaseOrders.slice(start, end);

            pageItems.forEach(po => {
                const row = document.createElement('tr');
                row.className = "cursor-pointer hover:bg-red-300 transition";
                row.onclick = () => setView('show', po.purchase_order_id);

            
                tbody.appendChild(row);
            });

            renderPagination();
        }

        function renderPagination() {
            const totalPages = Math.ceil(purchaseOrders.length / rowsPerPage);
            const pageNumbersDiv = document.getElementById('pageNumbersList');
            pageNumbersDiv.innerHTML = '';

            let startPage = currentPage - 1;
            let endPage = currentPage + 1;

            if (startPage < 1) {
                startPage = 1;
                endPage = Math.min(3, totalPages);
            }
            if (endPage > totalPages) {
                endPage = totalPages;
                startPage = Math.max(1, totalPages - 2);
            }

            for (let i = startPage; i <= endPage; i++) {
                const btn = document.createElement('button');
                btn.innerText = i;
                btn.className = `px-3 py-2 rounded-lg font-semibold ${i === currentPage ? 'bg-indigo-600 text-white' : 'text-indigo-600 hover:bg-indigo-100'}`;
                btn.onclick = () => goToPage(i);
                pageNumbersDiv.appendChild(btn);
            }

            document.getElementById('prevPageBtnList').disabled = currentPage === 1;
            document.getElementById('nextPageBtnList').disabled = currentPage === totalPages;

            const start = (currentPage - 1) * rowsPerPage + 1;
            const end = Math.min(currentPage * rowsPerPage, purchaseOrders.length);
            document.getElementById('paginationSummaryList').innerText = `แสดง ${start}-${end} จากทั้งหมด ${purchaseOrders.length} รายการ`;
        }


        function goToPage(page) {
            const totalPages = Math.ceil(purchaseOrders.length / rowsPerPage);
            if (page < 1) page = 1;
            if (page > totalPages) page = totalPages;
            currentPage = page;
            renderTable();
        }

        function changeRowsPerPage(value) {
            rowsPerPage = parseInt(value);
            currentPage = 1;
            renderTable();
        }

        // initial render
        renderTable();


</script>


@endsection