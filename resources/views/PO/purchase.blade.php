@extends('layouts.Tailwind')

@section('title', 'Purchase Order')

@section('content')

<!-- Export To Excel -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/papaparse@5.4.1/papaparse.min.js"></script>


<script src="https://unpkg.com/lucide@latest"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">




<!-- Load Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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

    <!-- Sidebar -->
    @include('layouts.user')
    <main class="flex-1 overflow-y-auto p-5 bg-gray-50">
        <div class="pt-1">
            <!-- Main Input Controls -->
            <div class="flex justify-center mb-8">
                <div class="flex items-end gap-4 flex-nowrap">


                    <!-- PO NO. Input -->
                    <div class="flex flex-col input-group">
                        <label for="po_no" class="text-sm text-gray-700 mb-1">PO NO.</label>
                        <input list="po_no_list" id="po_no" name="po_no"
                            class="w-full border rounded-lg px-4 py-2 focus:ring focus:border-primary-green shadow-sm"
                            placeholder="กรอกหรือเลือก PO NO.">
                        <datalist id="po_no_list">
                            <!-- Populated by JS -->
                        </datalist>
                    </div>

                    <!-- PO Date Input -->
                    <div class="flex flex-col input-group">
                        <label for="po_date" class=" text-sm text-gray-700 mb-1">PO Date</label>
                        <input type="date" id="po_date" name="po_date"
                            class="w-full border rounded-lg px-4 py-2 focus:ring focus:border-primary-green shadow-sm">
                    </div>

                    <!-- Customer Input -->
                    <div class="flex flex-col input-group">
                        <label for="customer" class="text-sm text-gray-700 mb-1">Customer</label>
                        <input type="text" id="customer" list="customer_list" placeholder="พิมพ์ชื่อลูกค้า"
                            class="w-full border rounded-lg px-4 py-2 focus:ring focus:border-primary-green shadow-sm"
                            autocomplete="off">
                        <datalist id="customer_list">
                            @foreach ($customers as $c)
                            <option value="{{ $c->name }}" data-id="{{ $c->id }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    <!-- Add PO Button -->
                    <button id="add-po-btn" onclick="openModal('modal-xxl')" class="relative flex items-center h-10 w-50 px-4 text-xs font-bold transition-all duration-300 ease-in-out 
           bg-emerald-500 rounded-lg group hover:bg-emerald-600 active:bg-emerald-700 text-white 
           shadow-xl shadow-emerald-400/50 hover:shadow-2xl hover:shadow-emerald-500/50 
           transform hover:scale-[1.03] focus:ring-4 focus:ring-emerald-300">

                        <span class="relative flex items-center">
                            <i class="fas fa-plus-circle mr-2 text-lg"></i>
                            <span>Add Item</span>
                        </span>

                        {{-- ลบเอฟเฟกต์ Span ที่ซับซ้อน (Wave Effect) ออก เพื่อให้โค้ดสะอาดขึ้น --}}
                    </button>



                    <!-- ปุ่มประวัติการขอ PO -->
                    <a href="{{ route('purchase-order.show', 1) }}" class="inline-flex items-center justify-center gap-3
                        h-10 px-6
                        text-sm font-semibold
                        text-white bg-indigo-600
                        rounded-xl
                        shadow-lg shadow-indigo-500/40
                        transition-colors duration-300
                        hover:bg-indigo-700
                        whitespace-nowrap leading-none">

                        <i class="fas fa-history text-lg leading-none"></i>
                        <span class="leading-none">ประวัติการขอ PO</span>
                    </a>
                </div>
            </div>




            <!-- Form for PO Submission -->
            <form id="saveForm" method="POST" action="{{ route('purchase-order.save') }}">
                <input type="hidden" name="po_no" id="hidden_po_no">
                <input type="hidden" name="po_date" id="hidden_po_date">
                <input type="hidden" id="hidden_customer" name="customer_id">
                <input type="hidden" id="hidden_total_amount" name="total_amount">

                <!-- Main Display Table Container -->
                <div class="w-full h-[350px] flex flex-col shadow-lg rounded-t-lg ring-1 ring-gray-300">
                    <div class="overflow-x-auto flex-1">
                        <table id="customer-table-display" class="min-w-full text-sm divide-y divide-gray-200 h-full">
                            <thead class="sticky top-0 z-10 bg-blue-950 text-white shadow text-center">
                                <tr class="text-center">

                                    <!-- 1. Item Code Filter -->
                                    <th class="whitespace-nowrap text-center border-b border-blue-900">
                                        <div class="flex items-center justify-center gap-2">
                                            <span class="tracking-wide font-sarabun text-xs text-white/90">Item
                                                Code</span>

                                            <span
                                                class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                                onclick="toggleFilterDropdown('item_code', this)" data-col="item_code">
                                                <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                            </span>
                                        </div>
                                    </th>

                                    <!-- 2. Item Description Filter -->
                                    <th class="whitespace-nowrap text-center border-b border-blue-900">
                                        <div class="flex items-center justify-center gap-2">
                                            <span class="tracking-wide font-sarabun text-xs text-white/90">Item
                                                Description</span>

                                            <span
                                                class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                                onclick="toggleFilterDropdown('item_description', this)"
                                                data-col="item_description">
                                                <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                            </span>
                                        </div>
                                    </th>

                                    <!-- 3. Unit Price Filter -->
                                    <th class="whitespace-nowrap text-center border-b border-blue-900">
                                        <div class="flex items-center justify-center gap-2">
                                            <span class="tracking-wide font-sarabun text-xs text-white/90">Unit
                                                Price</span>

                                            <span
                                                class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                                onclick="toggleFilterDropdown('unit_price', this)" data-col="unit_price">
                                                <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                            </span>
                                        </div>
                                    </th>

                                    <!-- 4. Unit -->
                                    <th class="whitespace-nowrap text-center border-b border-blue-900">
                                        <div class="flex items-center justify-center gap-2">
                                            <span class="tracking-wide font-sarabun text-xs text-white/90">Unit</span>

                                            <span
                                                class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                                onclick="toggleFilterDropdown('unit', this)" data-col="unit">
                                                <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                            </span>
                                        </div>
                                    </th>

                                    <!-- 5. Quantity -->
                                    <th class="whitespace-nowrap text-center border-b border-blue-900">
                                        <div class="flex items-center justify-center gap-2">
                                            <span
                                                class="tracking-wide font-sarabun text-xs text-white/90">Quantity</span>

                                            <span
                                                class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                                onclick="toggleFilterDropdown('quantity', this)" data-col="quantity">
                                                <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                            </span>
                                        </div>
                                    </th>

                                    <!-- 6. Amount -->
                                    <th class="whitespace-nowrap text-center border-b border-blue-900">
                                        <div class="flex items-center justify-center gap-2">
                                            <span class="tracking-wide font-sarabun text-xs text-white/90">Amount</span>

                                            <span
                                                class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                                onclick="toggleFilterDropdown('amount', this)" data-col="amount">
                                                <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                            </span>
                                        </div>
                                    </th>

                                    <th class="py-3 px-4 font-medium">Remove</th>
                                </tr>
                            </thead>
                            <tbody id="customer-table-body" class="bg-white divide-y divide-gray-100">
                                <!-- Selected items will be added here -->
                                <tr id="no-items-placeholder" class="text-center text-gray-500">
                                    <td colspan="7" class="py-8 text-lg italic">
                                        ยังไม่มีรายการสินค้าถูกเลือก. กรุณากด "Add Item" เพื่อเริ่มต้น.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Grand Total Footer -->
                <div id="grand-total-footer"
                    class="flex justify-end w-full p-3 bg-green-100 font-bold border-t-4 border-green-500 rounded-b-lg shadow-xl hidden">
                    <div class="flex items-center justify-between w-full md:w-1/2 lg:w-1/3 xl:w-1/4">
                        <span class="text-base text-gray-700">ยอดรวมทั้งหมด (Total Amount):</span>
                        <span id="grand-total-display" class="text-2xl text-green-700 ml-4">0.00</span>
                    </div>
                </div>

                <!-- Save/Cancel Buttons -->
                <div id="save-cancel-buttons-container" class="flex justify-center gap-4 mt-8 hidden">

                    <button type="submit" id="submit-po-button" class="bg-blue-600 text-white px-4 py-2 text-xs rounded-lg font-sarabun
                        hover:bg-blue-700 transition shadow-md">
                        OK
                    </button>

                    <button type="button" id="cancel-button" class="bg-red-500 text-white px-4 py-2 text-xs rounded-lg font-sarabun
                        hover:bg-red-600 transition shadow-md">
                        Cancel
                    </button>

                </div>
            </form>
        </div>
    </main>
</div>





{{-- Modal + ADD PO Customer --}}
<div id="modal-xxl"
    class="fixed inset-0 z-50 hidden items-center justify-center  bg-opacity-60 transition-opacity duration-300">
    <div
        class="bg-white w-full max-w-7xl h-[90vh] rounded-xl shadow-2xl flex flex-col relative transform scale-95 opacity-0 transition-all duration-300">

        <!-- Modal Header -->
        <div class="flex justify-between items-center border-b px-6 py-4 bg-gray-50 rounded-t-xl">
            <h2 class="text-2xl font-bold text-slate-800">Add Purchase Order Items</h2>
            <button onclick="closeModal('modal-xxl')"
                class="text-slate-500 hover:text-slate-900 text-3xl transition duration-150">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Sub-header (PO Info & Controls) -->
        <div class="px-6 pt-4">
            <!-- PO Info Display -->
            <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-4 mb-4 shadow-md">
                <h3 class="text-lg font-semibold text-green-800 mb-2">ข้อมูล PO ที่กำลังสร้าง</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-700">
                    <p><strong>PO No.:</strong> <span id="modal_po_no" class="font-medium text-gray-900">N/A</span>
                    </p>
                    <p><strong>Date PO:</strong> <span id="modal_po_date" class="font-medium text-gray-900">N/A</span>
                    </p>
                    <p><strong>Customer:</strong> <span id="modal_customer" class="font-medium text-gray-900">N/A</span>
                    </p>
                </div>
            </div>

            <!-- File & Export Controls -->
            <div class="flex items-center space-x-3 mb-4 flex-wrap">
                <label class="block">
                    <span class="sr-only">Choose File</span>
                    <input type="file" id="importCustomerFile" accept=".csv,.xlsx,.xls" class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-violet-50 file:text-violet-700
                                hover:file:bg-violet-100
                                cursor-pointer" />
                </label>

                <!-- Updated to call handleImportCheck() -->
                <button id="checkDataButton" type="button" onclick="handleImportCheck()"
                    class="rounded-xl bg-blue-500 px-6 py-2 text-sm font-bold text-white shadow-lg hover:bg-blue-600 transition transform hover:scale-105">
                    <i class="fas fa-file-import mr-2"></i> เช็คข้อมูล CSV
                </button>

                <button type="button" id="exportItemsToExcel"
                    onclick="showMessage('Export Action', 'Exporting item data to Excel...')"
                    class="rounded-xl bg-green-500 px-6 py-2 text-sm font-bold text-white shadow-lg hover:bg-green-600 transition transform hover:scale-105">
                    <i class="fas fa-file-excel mr-2"></i> Export visible Data
                </button>
            </div>
        </div>


        <!-- Item Selection Table -->
        <div class="overflow-y-auto px-6 pb-6 flex-grow">
            <table id="modal-items-table" class="min-w-full border border-gray-300 text-sm rounded-lg shadow-inner">
                <thead class="bg-blue-950 text-white sticky top-0 z-20">
                    <tr class="text-center">

                        <!-- checkbox -->
                        <th class="py-3 px-3 w-9 whitespace-nowrap text-center border-b border-blue-900">
                            <input type="checkbox" id="selectAllCheckbox" title="Select All"
                                class="w-4 h-4 text-green-600 border-gray-300 rounded cursor-pointer focus:ring-green-500">
                        </th>


                        <!-- 1. Item No. Filter -->
                        <th class="whitespace-nowrap text-center border-b border-blue-900">
                            <div class="flex items-center justify-center gap-2">
                                <span class="tracking-wide font-sarabun text-xs text-white/90">Item No.</span>

                                <span
                                    class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                    onclick="toggleFilterDropdown('item_no', this)" data-col="item_no">
                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                </span>
                            </div>
                        </th>

                        <!-- 2. Item Description Filter -->
                        <th class="whitespace-nowrap text-center border-b border-blue-900">
                            <div class="flex items-center justify-center gap-2">
                                <span class="tracking-wide font-sarabun text-xs text-white/90">Item Description</span>

                                <span
                                    class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                    onclick="toggleFilterDropdown('item_description', this)" data-col="item_description">
                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                </span>
                            </div>
                        </th>

                        <!-- 3. Unit Price Filter -->
                        <th class="whitespace-nowrap text-center border-b border-blue-900">
                            <div class="flex items-center justify-center gap-2">
                                <span class="tracking-wide font-sarabun text-xs text-white/90">Unit Price</span>

                                <span
                                    class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                    onclick="toggleFilterDropdown('unit_price', this)" data-col="unit_price">
                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                </span>
                            </div>
                        </th>

                        <!-- 4. Unit Filter -->
                        <th class="whitespace-nowrap text-center border-b border-blue-900">
                            <div class="flex items-center justify-center gap-2">
                                <span class="tracking-wide font-sarabun text-xs text-white/90">Unit</span>

                                <span
                                    class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                    onclick="toggleFilterDropdown('unit', this)" data-col="unit">
                                    <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                </span>
                            </div>
                        </th>

                    </tr>
                </thead>
                <tbody id="modal-items-table-body" class="divide-y divide-gray-100">
                    <!-- Items rendered by JS -->
                </tbody>
            </table>
        </div>

        <!-- Modal Footer -->
        <div class="flex justify-end space-x-3 border-t px-6 py-4 bg-gray-50 rounded-b-xl shadow-inner">
            <button type="button" onclick="confirmSelection()"
                class="px-6 py-2 text-sm uppercase tracking-wider font-semibold text-white bg-green-500 rounded-full shadow-lg transition-all duration-300 ease-in-out hover:bg-green-600 active:bg-green-700 transform hover:scale-105">
                <i class="fas fa-check mr-1"></i> OK
            </button>

            <button type="button" onclick="closeModal('modal-xxl')"
                class="px-6 py-2 text-sm uppercase tracking-wider font-semibold text-gray-700 bg-gray-200 border border-gray-300 rounded-full shadow-md transition-all duration-300 ease-in-out hover:bg-gray-300 active:bg-gray-400">
                <i class="fas fa-times mr-1"></i> Cancel
            </button>
        </div>
    </div>

    <!-- Modal ตรวจสอบข้อมูล -->
    <div id="csvCheckModal"
        class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black bg-opacity-70 transition-opacity duration-300">
        <div class="bg-white w-full max-w-5xl rounded-2xl shadow-2xl flex flex-col relative transform modal-fade-enter">

            <!-- Header (Blue/Modern) -->
            <div class="relative flex items-center justify-between px-6 py-3 
            bg-gradient-to-r from-blue-700 to-indigo-600 
            rounded-t-2xl shadow-md">

                <!-- Title -->
                <h2 class="text-lg md:text-xl font-semibold 
               flex items-center gap-2 text-white tracking-wide">
                    <span class="flex items-center justify-center w-8 h-8 
                     bg-white/20 rounded-lg">
                        <i class="fas fa-clipboard-check text-sm text-white"></i>
                    </span>
                    ตรวจสอบข้อมูล
                </h2>

                <!-- Close Button -->
                <button onclick="closeCSVCheckModal()" class="flex items-center justify-center w-8 h-8 
               rounded-full bg-white/10 
               hover:bg-white/20 
               transition-all duration-200 hover:rotate-90">
                    <i class="fas fa-times text-sm text-white"></i>
                </button>

            </div>

            <!-- Content -->
            <div class="p-6 flex-grow">
                <div class="overflow-auto max-h-[70vh] rounded-xl ring-1 ring-gray-200 shadow-lg">
                    <table id="csvCheckTable" class="min-w-full text-sm divide-y divide-gray-200 relative">
                        <thead class="bg-blue-950 text-white sticky top-0 z-20">
                            <tr class="text-center">

                                <!-- 1. Item Code. Filter -->
                                <th class="w-[180px] px-4 py-3 whitespace-nowrap text-center border-b border-blue-900">
                                    <div class="flex items-center justify-center gap-2">
                                        <span class="tracking-wide font-sarabun text-xs text-white/90">Item Code.</span>

                                        <span
                                            class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                            onclick="toggleFilterDropdown('item_code', this)" data-col="item_code">
                                            <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                        </span>
                                    </div>
                                </th>

                                <!-- 2. Item Description Filter -->
                                <th class="w-[180px] px-4 py-3 whitespace-nowrap text-center border-b border-blue-900">
                                    <div class="flex items-center justify-center gap-2">
                                        <span class="tracking-wide font-sarabun text-xs text-white/90">Item
                                            Description</span>

                                        <span
                                            class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                            onclick="toggleFilterDropdown('item_description', this)"
                                            data-col="item_description">
                                            <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                        </span>
                                    </div>
                                </th>

                                <!-- 3. Unit Price -->
                                <th class="w-[180px] px-4 py-3 whitespace-nowrap text-center border-b border-blue-900">
                                    <div class="flex items-center justify-center gap-2">
                                        <span class="tracking-wide font-sarabun text-xs text-white/90">Unit Price</span>

                                        <span
                                            class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                            onclick="toggleFilterDropdown('unit_price', this)" data-col="unit_price">
                                            <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                        </span>
                                    </div>
                                </th>

                                <!-- 4. Unit Filter -->
                                <th class="w-[180px] px-4 py-3 whitespace-nowrap text-center border-b border-blue-900">
                                    <div class="flex items-center justify-center gap-2">
                                        <span class="tracking-wide font-sarabun text-xs text-white/90">Unit</span>

                                        <span
                                            class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                            onclick="toggleFilterDropdown('unit', this)" data-col="unit">
                                            <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                        </span>
                                    </div>
                                </th>

                                <!-- 5. Status Filter -->
                                <th class="w-[180px] px-4 py-3 whitespace-nowrap text-center border-b border-blue-900">
                                    <div class="flex items-center justify-center gap-2">
                                        <span class="tracking-wide font-sarabun text-xs text-white/90">Status</span>

                                        <span
                                            class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                            onclick="toggleFilterDropdown('status', this)" data-col="status">
                                            <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                        </span>
                                    </div>
                                </th>

                            </tr>
                        </thead>
                        <tbody id="csvCheckTableBody" class="bg-white divide-y divide-gray-100">
                            <tr class="text-center hover:bg-gray-50 transition-colors duration-150">
                                <td colspan="5" class="py-8 text-lg italic text-gray-500">
                                    กรุณาอัพโหลดไฟล์ CSV เพื่อตรวจสอบข้อมูล
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer (Action Buttons) - REDUCED SIZE HERE -->
            <div class="flex justify-end space-x-4 border-t px-6 py-4 bg-gray-50 rounded-b-2xl shadow-inner">
                <!-- นำเข้ารายการที่ถูกต้อง (Import Valid) - Reduced to px-4 py-2 text-sm -->
                <button onclick="importAndMergeFromCSV()"
                    class="px-4 py-2 text-sm uppercase tracking-wider font-bold text-white bg-green-500 rounded-full shadow-lg transition-all duration-300 ease-in-out hover:bg-green-600 active:bg-green-700 transform hover:scale-105">
                    <i class="fas fa-arrow-circle-down mr-2"></i> Import
                </button>
                <!-- ยกเลิก (Cancel) - Reduced to px-4 py-2 text-sm -->
                <button onclick="closeCSVCheckModal()"
                    class="px-4 py-2 text-sm uppercase tracking-wider font-bold text-gray-700 bg-gray-200 border border-gray-300 rounded-full shadow-md transition-all duration-300 ease-in-out hover:bg-gray-300 active:bg-gray-400">
                    <i class="fas fa-times-circle mr-2"></i> Cancel
                </button>
            </div>
        </div>
    </div>

</div>



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
    let activeColumn = null;
    let activeTable = null;

    // toggleFilterDropdown ให้รู้ว่ามาจาก table
    function toggleFilterDropdown(column, element) {

    const modal = document.getElementById('column-filter-modal');
    const content = document.getElementById('column-filter-content');

    // 🔥 ถ้าเปิดอยู่แล้ว ให้ปิด
    if (!modal.classList.contains('hidden') && activeColumn === column) {
        modal.classList.add('hidden');
        activeColumn = null;
        activeTable = null;
        return;
    }

    activeColumn = column;
    activeTable = element.closest('table');

    const rect = element.getBoundingClientRect();

    content.style.top = (rect.bottom + window.scrollY) + "px";
    content.style.left = (rect.left + window.scrollX - 150) + "px";

    generateFilterOptions();

    modal.classList.remove('hidden');
}

    // function ดึงข้อมูลจากตาราง
    function generateFilterOptions() {

    if (!activeTable || !activeColumn) return;

    const tbody = activeTable.querySelector('tbody');
    const rows = tbody.querySelectorAll('tr');

    const colIndex = getColumnIndex(activeTable, activeColumn);
    if (colIndex === -1) return;

    const values = new Set();

    rows.forEach(row => {

        if (row.style.display === "none") return;

        const cell = row.children[colIndex];

        if (cell) {
            const text = cell.innerText.trim();
            if (text !== "") values.add(text);
        }
    });

    renderCheckboxList(Array.from(values).sort());
}


//หา column index อัตโนมัติ
function getColumnIndex(table, column) {

    const headers = table.querySelectorAll('thead th');

    for (let i = 0; i < headers.length; i++) {

        const icon = headers[i].querySelector(`[data-col="${column}"]`);

        if (icon) return i;
    }

    return -1;
}

//ฟังชันสำหรับ render Checkbox List
function renderCheckboxList(values) {

    const container = document.getElementById('column-filter-checkbox-list');
    container.innerHTML = "";

    values.forEach(value => {

        const div = document.createElement('div');
        div.className = "flex items-center gap-2 py-1";

        div.innerHTML = `
            <input type="checkbox" value="${value}" class="filter-checkbox">
            <span>${value}</span>
        `;

        container.appendChild(div);
    });
}

    
</script>






<!-- ฟังชั่นสำใส่ Iput ครบแล้วแสดงปุ่ม Add Item -->
<script>
    // ฟังก์ชันเช็คค่า inputs
        const toggleAddItemButton = () => {
            const poNo = document.getElementById('po_no').value.trim();
            const poDate = document.getElementById('po_date').value;
            const customer = document.getElementById('customer').value.trim();
            const addBtn = document.getElementById('add-po-btn');

            if (poNo && poDate && customer) {
                addBtn.classList.remove('hidden');
            } else {
                addBtn.classList.add('hidden');
            }
        };

        // เรียกใช้ทุกครั้งที่ input เปลี่ยนค่า
        document.querySelectorAll('#po_no, #po_date, #customer').forEach(input => {
            input.addEventListener('input', toggleAddItemButton);
        });

        // เรียกครั้งแรกตอนโหลดหน้า
        window.addEventListener('DOMContentLoaded', toggleAddItemButton);

</script>



<script>
    const mockCustomers = @json($customers);
        const mockPurchaseOrders = @json($purchaseOrders);
        const mockItems = @json($items); // ดึงจาก item_master จริง
        
        // Global State
        let selectedItems = []; // Array of items currently in the main table
        let filters = {}; // { column_key: [value1, value2] }
        let currentFilterColumn = null;
        let currentFilterTableId = null;

        // --- UTILITY FUNCTIONS ---

        /**
         * Formats a number to a string with two decimal places and commas as thousands separators.
         * @param {number} num
         * @returns {string}
         */
        const formatNumber = (num) => {
            return parseFloat(num).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        };

        /**
         * Opens the specified modal.
         * @param {string} id
         */
        const openModal = (id) => {
            const modal = document.getElementById(id);
            if (modal) {
                // Pre-fill modal header info
                document.getElementById('modal_po_no').textContent = document.getElementById('po_no').value || 'N/A';
                document.getElementById('modal_po_date').textContent = document.getElementById('po_date').value || 'N/A';
                document.getElementById('modal_customer').textContent = document.getElementById('customer').value || 'N/A';

                // Ensure modal item table is rendered and filtered
                renderModalItemsTable();

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => {
                    modal.querySelector('.shadow-2xl').classList.remove('scale-95', 'opacity-0');
                    modal.querySelector('.shadow-2xl').classList.add('scale-100', 'opacity-100');
                }, 10);
            }
        };

        /**
         * Closes the specified modal.
         * @param {string} id
         */
        const closeModal = (id) => {
            const modal = document.getElementById(id);
            if (modal) {
                modal.querySelector('.shadow-2xl').classList.remove('scale-100', 'opacity-100');
                modal.querySelector('.shadow-2xl').classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                }, 300);
            }
        };


        // --- AUTOPILOT / DATA INITIALIZATION ---

        /**
         * Populates datalists with mock data.
         */
        const populateDatalists = () => {
            const poDatalist = document.getElementById('po_no_list');
            const customerDatalist = document.getElementById('customer_list');

            mockPurchaseOrders.forEach(po => {
                const option = document.createElement('option');
                option.value = po.po_no;
                option.setAttribute('data-id', po.id);
                poDatalist.appendChild(option);
            });

            mockCustomers.forEach(customer => {
                const option = document.createElement('option');
                option.value = customer.name;
                option.setAttribute('data-id', customer.id);
                customerDatalist.appendChild(option);
            });
        };

        /**
         * Initializes the application state and event listeners.
         */
        const initApp = () => {
            populateDatalists();
            renderModalItemsTable(); // Initial render of the modal table

            // Event listener for main inputs to update hidden fields
            document.querySelectorAll('#po_no, #po_date, #customer').forEach(input => {
                input.addEventListener('input', () => {
                    document.getElementById('hidden_po_no').value = document.getElementById('po_no').value;
                    document.getElementById('hidden_po_date').value = document.getElementById('po_date').value;

                    // Find customer ID based on name for the hidden field
                    const customerName = document.getElementById('customer').value;
                    const customer = mockCustomers.find(c => c.name === customerName);
                    document.getElementById('hidden_customer').value = customer ? customer.id : '';

                    // Show/hide buttons only if essential inputs are filled (optional)
                    if (document.getElementById('po_no').value && document.getElementById('po_date').value && document.getElementById('customer').value && selectedItems.length > 0) {
                        document.getElementById('save-cancel-buttons-container').classList.remove('hidden');
                        document.getElementById('grand-total-footer').classList.remove('hidden');
                    } else if (selectedItems.length === 0) {
                        document.getElementById('save-cancel-buttons-container').classList.add('hidden');
                        document.getElementById('grand-total-footer').classList.add('hidden');
                    }
                });
            });

            // Handle main form submission
            document.getElementById('saveForm').addEventListener('submit', handleFormSubmission);
            

            // Handle Cancel button
            document.getElementById('cancel-button').addEventListener('click', handleCancel);

            // XLSX Export
            document.getElementById('exportItemsToExcel').addEventListener('click', exportItemsToExcel);

        };


        // --- MAIN TABLE LOGIC (CUSTOMER-TABLE-DISPLAY) ---

        /**
         * Calculates the amount for a single row and updates the grand total.
         * @param {number} itemId
         */
        const updateItemAmount = (itemId) => {
            const item = selectedItems.find(i => i.id === itemId);
            if (!item) return;

            const row = document.getElementById(`item-row-${itemId}`);
            if (!row) return;

            const qtyInput = row.querySelector('input[name^="items"][name$="[qty]"]');
            const amountDisplay = row.querySelector('.amount-display');

            const qty = parseFloat(qtyInput.value) || 0;
            const amount = qty * item.unit_price;

            item.qty = qty;
            item.amount = amount;

            amountDisplay.textContent = formatNumber(amount);
            calculateGrandTotal();
        };

        /**
         * Calculates and updates the displayed Grand Total.
         */
        const calculateGrandTotal = () => {
            const total = selectedItems.reduce((sum, item) => sum + (item.amount || 0), 0);
            document.getElementById('grand-total-display').textContent = formatNumber(total);
            document.getElementById('hidden_total_amount').value = total.toFixed(2);

            // Show/Hide footer and buttons based on total
            const totalContainer = document.getElementById('grand-total-footer');
            const buttonsContainer = document.getElementById('save-cancel-buttons-container');
            const placeholder = document.getElementById('no-items-placeholder');
            const mainInputsFilled = document.getElementById('po_no').value && document.getElementById('customer').value;

            if (selectedItems.length > 0) {
                totalContainer.classList.remove('hidden');
                placeholder.classList.add('hidden');
                if (mainInputsFilled) {
                    buttonsContainer.classList.remove('hidden');
                }
            } else {
                totalContainer.classList.add('hidden');
                buttonsContainer.classList.add('hidden');
                placeholder.classList.remove('hidden');
            }
        };

        /**
         * Renders the item in the main display table.
         * @param {object} item
         */
        const renderMainTableRow = (item) => {
            const tbody = document.getElementById('customer-table-body');
            const newRow = document.createElement('tr');
            newRow.id = `item-row-${item.id}`;
            newRow.className = 'text-center hover:bg-gray-50';

            const formattedPrice = formatNumber(item.unit_price);
            const qty = item.qty || 1;
            item.amount = item.unit_price * qty; // Calculate initial amount

            newRow.innerHTML = `
                <td class="py-3 px-4 text-gray-800">${item.item_code}</td>
                <td class="py-3 px-4 text-left">${item.description}
                    <input type="hidden" name="items[${item.id}][item_id]" value="${item.id}">
                </td>
                <td class="py-3 px-4">${formattedPrice}</td>
                <td class="py-3 px-4">${item.unit}</td>
                <td class="py-3 px-4">
                    <input type="number" name="items[${item.id}][qty]" value="${qty}" min="1" step="any"
                        class="qty-input w-20 border rounded-lg px-2 py-1 text-center text-sm focus:ring focus:border-primary-green"
                        oninput="updateItemAmount(${item.id})">
                </td>
                <td class="py-3 px-4 font-semibold text-right text-green-700">
                    <span class="amount-display">${formatNumber(item.amount)}</span>
                </td>
                <td class="py-3 px-4">
                    <button type="button" onclick="removeItem(${item.id})"
                        class="text-red-500 hover:text-red-700 transition duration-150 p-1 rounded-full hover:bg-red-100">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(newRow);

            // Update amount after rendering
            updateItemAmount(item.id);
        };

        /**
         * Removes an item from the main table and state.
         * @param {number} itemId
         */
        const removeItem = (itemId) => {
            selectedItems = selectedItems.filter(item => item.id !== itemId);
            document.getElementById(`item-row-${itemId}`).remove();
            calculateGrandTotal();
        };


        // --- MODAL TABLE LOGIC (ITEM SELECTION) ---

        /**
         * Confirms selected items and moves them to the main table.
         */
        const confirmSelection = () => {
            const checkboxes = document.querySelectorAll('#modal-items-table .itemCheckbox:checked');
            const newSelections = [];

            checkboxes.forEach(checkbox => {
                const itemData = JSON.parse(checkbox.getAttribute('data-item'));
                // Only add if not already selected
                if (!selectedItems.some(i => i.id === itemData.id)) {
                    itemData.qty = 1; // Default qty
                    newSelections.push(itemData);
                }
            });

            selectedItems = [...selectedItems, ...newSelections];

            newSelections.forEach(item => renderMainTableRow(item));

            closeModal('modal-xxl');
            calculateGrandTotal(); // Recalculate and update visibility
        };

        /**
         * Renders the item rows in the modal table based on the current filters.
         */
        const renderModalItemsTable = () => {
            const tbody = document.getElementById('modal-items-table-body');
            tbody.innerHTML = ''; // Clear existing rows

            // Filter items based on current global filters
            const filteredItems = mockItems.filter(item => {
                for (const columnKey in filters) {
                    const selected = filters[columnKey];
                    if (!selected || selected.length === 0) continue;

                    let itemValue = String(item[columnKey]);
                    if (columnKey === 'unit_price') itemValue = parseFloat(itemValue).toFixed(2);

                    if (!selected.includes(itemValue)) return false;
                }
                return true;
            });


            filteredItems.forEach(item => {
                // Check if the item is already selected in the main table
                const isAlreadySelected = selectedItems.some(i => i.id === item.id);
                const itemJson = JSON.stringify(item);

                const row = document.createElement('tr');
                row.className = `text-center hover:bg-green-100 select-row ${isAlreadySelected ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : 'cursor-pointer'}`;
                row.setAttribute('data-item', itemJson);

                row.innerHTML = `
                    <td class="text-center py-2 px-4" data-column-key="item_id">
                        <input type="checkbox" class="itemCheckbox ..." value="${item.id}" data-item='${itemJson}' ${isAlreadySelected ? 'disabled' : ''}>
                    </td>
                    <td class="py-2 px-4" data-column-key="item_code">${item.item_code}</td>
                    <td class="py-2 px-4" data-column-key="description">${item.description}</td>
                    <td class="py-2 px-4" data-column-key="unit_price">${formatNumber(item.unit_price)}</td>
                    <td class="py-2 px-4" data-column-key="unit">${item.unit}</td>
                `;


                // Add row click listener for selection
                if (!isAlreadySelected) {
                    row.addEventListener('click', (e) => {
                        // Toggle checkbox state unless the click was on the checkbox itself
                        const checkbox = row.querySelector('.itemCheckbox');
                        if (e.target !== checkbox) {
                            checkbox.checked = !checkbox.checked;
                        }
                    });
                }

                tbody.appendChild(row);
            });

            // Update select all state
            updateSelectAllCheckboxState();
        };

        document.getElementById('selectAllFilter').addEventListener('click', () => {
            document.querySelectorAll('#column-filter-checkbox-list .filter-value-checkbox').forEach(cb => cb.checked = true);
        });

        document.getElementById('deselectAllFilter').addEventListener('click', () => {
            document.querySelectorAll('#column-filter-checkbox-list .filter-value-checkbox').forEach(cb => cb.checked = false);
        });


        // Select All Checkbox Logic
        document.getElementById('selectAllCheckbox').addEventListener('change', (e) => {
            const isChecked = e.target.checked;
            document.querySelectorAll('#modal-items-table .itemCheckbox:not(:disabled)').forEach(checkbox => {
                checkbox.checked = isChecked;
            });
        });

        // Update Select All Checkbox based on individual checkboxes
        const updateSelectAllCheckboxState = () => {
            const allCheckboxes = document.querySelectorAll('#modal-items-table .itemCheckbox:not(:disabled)');
            const checkedCheckboxes = document.querySelectorAll('#modal-items-table .itemCheckbox:checked:not(:disabled)');
            const selectAll = document.getElementById('selectAllCheckbox');

            if (allCheckboxes.length === 0) {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            } else if (checkedCheckboxes.length === allCheckboxes.length) {
                selectAll.checked = true;
                selectAll.indeterminate = false;
            } else if (checkedCheckboxes.length > 0) {
                selectAll.checked = false;
                selectAll.indeterminate = true;
            } else {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            }
        };

        document.getElementById('modal-items-table-body').addEventListener('change', (e) => {
            if (e.target.classList.contains('itemCheckbox')) {
                updateSelectAllCheckboxState();
            }
        });


        // --- FILTERING LOGIC ---

        /**
         * Opens the column filter modal and populates it with unique values.
         */
        const openColumnFilterModal = (button) => {
            const headerCell = button.closest('.filterable');
            const columnKey = headerCell.getAttribute('data-column');
            const columnName = button.getAttribute('data-column-name');
            const tableId = button.getAttribute('data-table-id');
            const modal = document.getElementById('column-filter-modal');
            const listContainer = document.getElementById('column-filter-checkbox-list');

            currentFilterColumn = columnKey;
            currentFilterTableId = tableId;

            // Update modal header
            document.getElementById('modal-column-name').textContent = columnName;
            document.getElementById('column-filter-search').value = '';
            listContainer.innerHTML = ''; // Clear old content

            // Unique values
    const uniqueValues = new Set();
    mockItems.forEach(item => {
        let value = String(item[columnKey]);
        if (columnKey === 'unit_price') {
            value = parseFloat(item[columnKey]).toFixed(2);
        }
        uniqueValues.add(value);
    });

    const sortedValues = Array.from(uniqueValues).sort();

    const selected = filters[columnKey] || [];

    sortedValues.forEach(value => {
        const id = `filter-${columnKey}-${value.replace(/[^\w]/g, '')}`;
        const label = document.createElement('label');
        label.className = "flex items-center space-x-2 py-1 px-2 hover:bg-gray-100 rounded-md";

        let displayValue = value;
        if (columnKey === 'unit_price') {
            displayValue = formatNumber(parseFloat(value));
        }

        label.innerHTML = `
            <input type="checkbox"
                id="${id}"
                value="${value}"
                class="filter-value-checkbox w-4 h-4"
                ${selected.includes(value) ? 'checked' : ''}>
            <span>${displayValue}</span>
        `;

        listContainer.appendChild(label);
    });

    // Position modal
    const rect = headerCell.getBoundingClientRect();
    modal.style.top = `${rect.bottom + window.scrollY}px`;
    modal.style.left = `${rect.left + window.scrollX}px`;

    modal.classList.remove('hidden');

    // Animation
    setTimeout(() => {
        modal.querySelector('.shadow-2xl').classList.remove('scale-95', 'opacity-0');
        modal.querySelector('.shadow-2xl').classList.add('scale-100', 'opacity-100');
    }, 10);
};

        /**
         * Closes the column filter modal.
         */
        const closeColumnFilterModal = () => {
            const modal = document.getElementById('column-filter-modal');
            modal.querySelector('.shadow-2xl').classList.remove('scale-100', 'opacity-100');
            modal.querySelector('.shadow-2xl').classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                currentFilterColumn = null;
                currentFilterTableId = null;
            }, 200);
        };

        /**
         * Applies the selected filter values to the current column and re-renders the table.
         */
        const applyColumnFilter = () => {
            if (!currentFilterColumn) return;

            const selectedValues = Array.from(document.querySelectorAll('#column-filter-checkbox-list .filter-value-checkbox:checked'))
                .map(cb => cb.value);

            filters[currentFilterColumn] = selectedValues;

            // Update icon
            const triggerButton = document.querySelector(`.table-filter-trigger[data-column-key="${currentFilterColumn}"]`);
            if (triggerButton) {
                const icon = triggerButton.querySelector('.filter-icon');
                    if (selectedValues.length > 0) {
                        icon.classList.remove('fa-sort', 'text-gray-500');
                        icon.classList.add('fa-filter', 'text-blue-500');
                    } else {
                        icon.classList.remove('fa-filter', 'text-blue-500');
                        icon.classList.add('fa-sort', 'text-gray-500');
                    }

            }

            renderModalItemsTable(); // render ใหม่
            closeColumnFilterModal();
        };


        /**
         * Clears filter for the currently open column.
         */
        const clearSingleColumnFilter = () => {
            if (currentFilterColumn) {
                filters[currentFilterColumn] = [];
                applyColumnFilter(); // This will re-render and close the modal
            }
        };

        /**
         * Search functionality for the filter values.
         */
        document.getElementById('column-filter-search').addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('#column-filter-checkbox-list label').forEach(label => {
                const labelText = label.textContent.toLowerCase();
                if (labelText.includes(searchTerm)) {
                    label.classList.remove('hidden');
                } else {
                    label.classList.add('hidden');
                }
            });
        });

        // Delegate listener for opening the filter modal
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.table-filter-trigger');
            if (btn) {
                openColumnFilterModal(btn);
            }
        });




        // --- FORM AND DATA HANDLING ---

        /**
         * Handles the form submission.
         * @param {Event} e
         */
        const handleFormSubmission = async (e) => {

    e.preventDefault();

    // ตรวจสอบว่ามีรายการหรือไม่
    if (selectedItems.length === 0) {
        Swal.fire({ icon: 'warning', title: 'รายการสินค้าว่างเปล่า', text: 'กรุณาเลือกรายการสินค้าอย่างน้อย 1 รายการก่อนบันทึก.' });
        return;
    }

    // --- 1) เติมค่า hidden fields จากค่าปัจจุบัน (สำคัญมาก) ---
    const poNoInput = document.getElementById('po_no');
    const poDateInput = document.getElementById('po_date');
    const customerInput = document.getElementById('customer');

    // ถ้าค่า visible มี ให้เขียนกลับไปที่ hidden (กรณีผู้ใช้ไม่ได้ trigger input event)
    document.getElementById('hidden_po_no').value = poNoInput ? poNoInput.value.trim() : '';
    document.getElementById('hidden_po_date').value = poDateInput ? poDateInput.value : '';
    document.getElementById('hidden_total_amount').value = document.getElementById('grand-total-display').textContent.replace(/,/g, '') || '0';

    // หา customer_id จากชื่อ (mockCustomers) — ถ้าคุณเก็บลูกค้าในฐานข้อมูลจริง ให้หาแบบนี้เช่นกัน
    const customerName = customerInput ? customerInput.value.trim() : '';
    const customerObj = mockCustomers.find(c => c.name === customerName);
    document.getElementById('hidden_customer').value = customerObj ? customerObj.id : '';

    // --- 2) ตรวจสอบความถูกต้องก่อนส่ง ---
    const existingPONos = mockPurchaseOrders.map(po => po.po_no);
    const po_no = document.getElementById('hidden_po_no').value;
    const po_date = document.getElementById('hidden_po_date').value;
    const customer_id = document.getElementById('hidden_customer').value;
    const total_amount = parseFloat(document.getElementById('hidden_total_amount').value) || 0;
    
    


    if (!po_no) {
        Swal.fire({ icon: 'warning', title: 'กรุณาระบุ PO No.', text: 'กรุณาระบุเลข PO ก่อนบันทึก' });
        return;
    }
    if (!po_date) {
        Swal.fire({ icon: 'warning', title: 'กรุณาระบุ PO Date', text: 'กรุณาเลือกวันที่ PO' });
        return;
    }
    if (!customerName) {
        Swal.fire({ icon: 'warning', title: 'กรุณาระบุชื่อลูกค้า' });
        return;
}
    if (total_amount <= 0) {
        Swal.fire({ icon: 'warning', title: 'ยอดรวมเป็น 0', text: 'ยอดรวมต้องมากกว่า 0' });
        return;
    }

    // --- 3) เตรียม items แบบที่ Controller ต้องการ (ชื่อ field ต้องตรงกับ DB) ---
    const itemsPayload = selectedItems.map(item => {
        // บังคับให้เป็นตัวเลขและใช้ชื่อฟิลด์ตามฐานข้อมูล (qty, unit_price, amount)
        const qty = Number(item.quantity || item.qty || 0);
        const unit_price = Number(item.unit_price || item.price || 0);
        const amount = Number(item.amount || (qty * unit_price) || 0);

        return {
            item_id: item.id,   // ถ้า selectedItems ใช้ property ชื่ออื่น ให้แก้เป็น property ที่ถูกต้อง
            qty,
            unit_price,
            amount
        };
    });

    const poData = {
        po_no,
        po_date,
        customer_name: customerName,
        total_amount,
        items: itemsPayload
    };

    console.log('== ข้อมูลที่จะส่ง ==');
    console.log(poData);

    try {
        const response = await fetch('{{ route("purchase-order.save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(poData)
        });

        // ถ้า status 419 หรือ 500 จะไม่ ok
        const result = await response.json().catch(() => null);

        if (!response.ok) {
            // แสดง error message ถ้ามีจาก server
            const serverMsg = result && result.message ? result.message : `HTTP ${response.status}`;
            console.error('Server error:', serverMsg, result);
            Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: `ไม่สามารถบันทึกข้อมูลได้: ${serverMsg}` });
            return;
        }

        if (result.status !== 'success') {
            const serverMsg = result.message || 'Unknown error';
            Swal.fire({ icon: 'error', title: 'บันทึกไม่สำเร็จ', text: serverMsg });
            return;
        }

        // สำเร็จ → redirect ไปหน้า show
        Swal.fire({
            icon: 'success',
            title: 'บันทึกสำเร็จ',
            text: 'กำลังไปที่หน้ารายละเอียด...'
        }).then(() => {
            window.location.href = "/purchase-order/show/" + result.po_id;
        });

    } catch (err) {
        console.error('Fetch exception', err);
        Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: 'ไม่สามารถบันทึกข้อมูลได้ (network/exception)' });
    }
};


        /**
         * Resets the entire form and state.
         */
        const handleCancel = () => {
            document.getElementById('saveForm').reset();
            selectedItems = [];
            filters = {};
            document.getElementById('customer-table-body').innerHTML = `
                <tr id="no-items-placeholder" class="text-center text-gray-500">
                    <td colspan="7" class="py-8 text-lg italic">
                        ยังไม่มีรายการสินค้าถูกเลือก. กรุณากด "Add Item" เพื่อเริ่มต้น.
                    </td>
                </tr>`;
            calculateGrandTotal();
            // Reset filter icons in modal header
            document.querySelectorAll('.filter-icon').forEach(icon => {
                icon.classList.remove('fa-filter', 'text-blue-500');
                icon.classList.add('fa-sort', 'text-gray-500');
            });

            
        };

        /**
         * Exports the current selected items to an Excel file.
         */
        const exportItemsToExcel = () => {
            if (selectedItems.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'ไม่มีข้อมูล!',
                    text: 'กรุณาเลือกรายการสินค้าก่อน Export.',
                });
                return;
            }

            const data = selectedItems.map(item => ({
                'PO No.': document.getElementById('po_no').value,
                'PO Date': document.getElementById('po_date').value,
                'Customer': document.getElementById('customer').value,
                'Item Code': item.item_code,
                'Description': item.description,
                'Unit Price': item.unit_price,
                'Quantity': item.qty,
                'Amount': item.amount,
                'Unit': item.unit,
            }));

            const ws = XLSX.utils.json_to_sheet(data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Purchase Order Details");

            const poNo = document.getElementById('po_no').value || 'NewPO';
            XLSX.writeFile(wb, `${poNo}_Items.xlsx`);
        };


        // Initialize the app when the page loads
        window.onload = initApp;

</script>




<script>
    let importedDataGlobal = [];

/* ==============================
   MAIN : ตรวจไฟล์ CSV / Excel
================================ */
function handleImportCheck() {
    const fileInput = document.getElementById('importCustomerFile');
    if (!fileInput.files.length) {
        alert('กรุณาเลือกไฟล์ CSV หรือ Excel ก่อน');
        return;
    }

    const file = fileInput.files[0];
    const ext = file.name.split('.').pop().toLowerCase();

    if (ext === 'csv') {
        readCSV(file);
    } else if (ext === 'xlsx' || ext === 'xls') {
        readExcel(file);
    } else {
        alert('รองรับเฉพาะ CSV / Excel เท่านั้น');
    }
}

/* ==============================
   READ CSV
================================ */
function readCSV(file) {
    Papa.parse(file, {
        header: true,
        skipEmptyLines: true,
        complete: function (results) {
            prepareData(results.data);
        }
    });
}

/* ==============================
   READ EXCEL
================================ */
function readExcel(file) {
    const reader = new FileReader();
    reader.onload = function (e) {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array' });
        const sheetName = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[sheetName];

        const jsonData = XLSX.utils.sheet_to_json(worksheet, {
            defval: ""
        });

        prepareData(jsonData);
    };
    reader.readAsArrayBuffer(file);
}

/* ==============================
   PREPARE DATA (CSV + Excel)
================================ */
function prepareData(rows) {
    importedDataGlobal = rows.map(row => ({
        item_code: getVal(row, ['item no.','item no','Item No','Item Code','item_code']),
        description: getVal(row, ['item description','Item Description','description']),
        unit_price: parseFloat(
            getVal(row, ['unit price','Unit Price','price','Price','unit_price'])
                .replace(/,/g,'')
        ) || 0,
        unit: getVal(row, ['unit','Unit','uom'])
    }));

    checkDuplicates(importedDataGlobal);
}

/* ==============================
   CHECK DUPLICATES (SERVER)
================================ */
function checkDuplicates(items) {
    const tbody = document.getElementById('csvCheckTableBody');
    tbody.innerHTML = '';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken) {
        Swal.fire({ icon:'error', title:'CSRF Token Missing' });
        return;
    }

    fetch('/check-items-duplicates', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ items })
    })
    .then(res => res.json())
    .then(res => {
        const duplicates = res.duplicates || [];
        tbody.innerHTML = '';

        importedDataGlobal.forEach(row => {
            const exists = duplicates.includes(row.item_code);
            const tr = document.createElement('tr');
            tr.className = "text-center hover:bg-red-50 transition-colors duration-150";
            tr.innerHTML = `
                <td class="py-2">${row.item_code}</td>
                <td class="py-2">${row.description}</td>
                <td class="py-2">${row.unit_price.toLocaleString()}</td>
                <td class="py-2">${row.unit}</td>
                <td class="py-2 font-semibold ${exists ? 'text-red-600' : 'text-green-600'}">
                    ${exists ? 'ซ้ำ' : 'นำเข้าได้'}
                </td>
            `;
            tbody.appendChild(tr);
        });

        importedDataGlobal = importedDataGlobal.filter(
            row => !duplicates.includes(row.item_code)
        );

        document.getElementById('csvCheckModal').classList.remove('hidden');
    })
    .catch(err => console.error(err));
}

/* ==============================
   IMPORT TO DB
================================ */
function importAndMergeFromCSV() {
    if (!importedDataGlobal.length) {
        Swal.fire({ icon:'info', title:'ไม่มีรายการใหม่สำหรับนำเข้า' });
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    fetch('/import-items', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ items: importedDataGlobal })
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            Swal.fire({ icon:'success', title:'บันทึกข้อมูลเรียบร้อย' });
        } else {
            Swal.fire({ icon:'error', title:'Import Failed', text: res.message });
        }
    })

    .catch(err => Swal.fire({ icon:'error', title:'Import Failed', text: err.message }));

    importedDataGlobal.forEach(item => {
        const localItem = { id: Date.now() + Math.random(), ...item };
        selectedItems.push(localItem);
        renderMainTableRow(localItem);
    });

    calculateGrandTotal();
    closeCSVCheckModal();
}

/* ==============================
   HELPER
================================ */
function getVal(row, keys) {
    const normalizedRow = {};
    for (let k in row) {
        normalizedRow[k.trim().toLowerCase()] = row[k];
    }

    for (let key of keys) {
        const k = key.trim().toLowerCase();
        if (normalizedRow[k] !== undefined) {
            return String(normalizedRow[k]).trim();
        }
    }
    return "";
}

function closeCSVCheckModal() {
    document.getElementById('csvCheckModal').classList.add('hidden');
}
</script>







@endsection