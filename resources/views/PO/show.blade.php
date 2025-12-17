@extends('layouts.Tailwind')

@section('title', 'PO หรือ Purchase Order ใบสั่งซื้อ')


<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- Font: Inter -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">

<!-- Load Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


<!-- Export To Excel -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

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

<div id="app" class="mx-auto bg-white shadow-2xl rounded-2xl p-6 md:p-10 h-[900px]">

    <!-- Header -->
    <header class="mb-8 border-b-2 border-indigo-100 pb-4 flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-header">จัดการใบสั่งซื้อ (Purchase Order)</h1>
            <p class="text-gray-500 mt-1">รายการทั้งหมดและรายละเอียด PO ที่ถูกบันทึกในระบบ</p>
        </div>
        <!-- ไปที่หน้า สร้าง PO -->
        <button onclick="window.location='{{ route('purchase-orders.index') }}'"
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-xl shadow-lg shadow-indigo-300">
            สร้าง PO ใหม่
        </button>


    </header>




    <!-- 1. PO List View (Index) -->
    <div id="listView" class="view-section ">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">ประวัติการสั่งซื้อ</h2>

            <button type="button" id="exportPOToExcel" onclick="exportPOToExcel()"
                class="rounded-xl bg-green-500 px-6 py-2 text-sm font-bold text-white shadow-lg hover:bg-green-600 transition transform hover:scale-105">
                <i class="fas fa-file-excel mr-2"></i> Export visible Data
            </button>
        </div>



        <div id="tableContainer" class="overflow-x-auto rounded-xl shadow-xl border border-gray-200">
            <!-- กำหนดความสูง max-height ให้ scroll ทำงาน -->
            <div class="max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 table-fixed">
                    <thead class="bg-indigo-600 text-white sticky top-0">
                        <tr>

                            <!-- 1. PO No. Filter -->
                            <th
                                class="w-1/5 px-4 py-2 text-left text-xs font-extrabold uppercase tracking-wider rounded-tl-xl relative">
                                <div class="relative inline-flex items-center space-x-2" data-column="po_no">
                                    <span>PO No.</span>
                                    <button data-column="po_no" onclick="toggleFilterDropdown('po_no')"
                                        class="filter-btn-po_no text-white hover:text-yellow-300 p-1 rounded-full transition duration-150">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </button>
                                </div>
                            </th>

                            <!-- 2. Customer Name Filter -->
                            <th
                                class="w-2/5 px-4 py-2 text-left text-xs font-extrabold uppercase tracking-wider relative">
                                <div class="relative inline-flex items-center space-x-2" data-column="customer_name">
                                    <span>Customer Name</span>
                                    <button data-column="customer_name" onclick="toggleFilterDropdown('customer_name')"
                                        class="filter-btn-customer_name text-white hover:text-yellow-300 p-1 rounded-full transition duration-150">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </button>
                                </div>
                            </th>

                            <!-- 3. PO Date Filter -->
                            <th
                                class="w-1/5 px-4 py-2 text-left text-xs font-extrabold uppercase tracking-wider relative">
                                <div class="relative inline-flex items-center space-x-2" data-column="po_date">
                                    <span>PO Date</span>
                                    <button data-column="po_date" onclick="toggleFilterDropdown('po_date')"
                                        class="filter-btn-po_date text-white hover:text-yellow-300 p-1 rounded-full transition duration-150">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </button>
                                </div>
                            </th>

                            <!-- 4. Total Amount Filter -->
                            <th
                                class="w-1/5 px-4 py-2 text-right text-xs font-extrabold uppercase tracking-wider relative">
                                <div class="relative inline-flex items-center space-x-2" data-column="total_amount">
                                    <span>Total Amount</span>
                                    <button data-column="total_amount" onclick="toggleFilterDropdown('total_amount')"
                                        class="filter-btn-total_amount text-white hover:text-yellow-300 p-1 rounded-full transition duration-150">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </button>
                                </div>
                            </th>

                            <!-- 5. Balanced Amount Filter -->
                            <th
                                class="w-1/5 px-4 py-2 text-right text-xs font-extrabold uppercase tracking-wider rounded-tr-xl relative">
                                <div class="relative inline-flex items-center space-x-2" data-column="balanced_amount">
                                    <span>Balanced Amount</span>
                                    <button data-column="balanced_amount"
                                        onclick="toggleFilterDropdown('balanced_amount')"
                                        class="text-white hover:text-yellow-300 p-1 rounded-full transition duration-150">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </button>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="po-list-body" class="divide-y divide-gray-200">
                        <!-- Rows จะถูก inject ที่นี่ -->
                    </tbody>
                </table>

            </div>

            <p id="noPoMessage" class="hidden px-6 py-8 text-center text-gray-500 font-medium">
                ไม่พบรายการใบสั่งซื้อในระบบ
            </p>

            <div id="listViewPagination"
                class="mt-4 flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0 p-4 bg-gray-50 rounded-b-xl border border-t-0 border-gray-200">
                <!-- Row Count Dropdown -->
                <div class="flex items-center space-x-3">
                    <label for="rowsPerPage" class="text-sm font-medium text-gray-600">แสดงรายการ:</label>
                    <select id="rowsPerPageList" onchange="changeRowsPerPage(this.value)"
                        class="py-2 px-4 border border-gray-300 rounded-lg text-sm font-semibold bg-white cursor-pointer shadow-sm transition duration-150 hover:border-indigo-400 focus:ring-indigo-500 focus:border-indigo-500 appearance-none">
                        <option value="5">5 แถว</option>
                        <option value="10" selected>10 แถว</option>
                        <option value="20">20 แถว</option>
                    </select>
                </div>

                <!-- Pagination -->
                <nav class="flex items-center space-x-1" aria-label="Pagination">
                    <button id="prevPageBtnList" onclick="goToPage(currentPage - 1)"
                        class="pagination-btn p-3 rounded-full text-indigo-600 hover:bg-indigo-100 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                        < </button>

                            <div id="pageNumbersList" class="flex space-x-1">
                                <!-- Page buttons -->
                            </div>

                            <button id="nextPageBtnList" onclick="goToPage(currentPage + 1)"
                                class="pagination-btn p-3 rounded-full text-indigo-600 hover:bg-indigo-100 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                                >
                            </button>
                </nav>

                <!-- Status/Summary Text -->
                <span id="paginationSummaryList" class="text-sm text-gray-600">
                    แสดง 1-10 จากทั้งหมด 15 รายการ
                </span>
            </div>
        </div>

    </div>



    <!-- 2. PO Detail View (Show) -->
    <div id="showView" class="view-section hidden">

        <div class="flex items-center justify-between mb-6">
            <!-- ปุ่มกลับไปหน้าประวัติ -->
            <button onclick="setView('list')"
                class="text-indigo-600 hover:text-indigo-800 flex items-center transition-colors font-medium p-2 -ml-2 rounded-lg hover:bg-indigo-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                กลับไปหน้าประวัติ
            </button>

            <!-- ปุ่ม Export to Excel -->
            <button type="button" id="exportPOandItemToExcel" onclick="exportPOandItemToExcel()"
                class="rounded-xl bg-green-500 px-6 py-2 text-sm font-bold text-white shadow-lg hover:bg-green-600 transition transform hover:scale-105">
                <i class="fas fa-file-excel mr-2"></i> Export visible Data
            </button>
        </div>



        <!-- PO Header Detail Card -->
        <div class="bg-white p-8 rounded-xl border border-indigo-200 shadow-xl mb-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">PO NO</p>
                    <p id="detailPoNo" class="text-3xl font-extrabold text-indigo-700 mt-1">{{ $po->po_no }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-sm font-medium text-gray-500 uppercase">Customer</p>
                    <p id="detailCustomer" class="text-2xl font-bold text-gray-800 mt-1">{{ $po->customer_name
                        }}
                    </p>
                    </p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-indigo-100 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">PO Date</p>
                    <p id="detailPoDate" class="text-lg font-semibold text-gray-700">{{ $po->po_date }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase">สถานะ</p>
                    <span id="detailStatus"
                        class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 shadow-inner">
                        <svg class="w-2 h-2 mr-1.5" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3" />
                        </svg>
                        อยู่ระหว่างดำเนินการ (Open)
                    </span>
                </div>
            </div>

        </div>


        <!-- PO Items Detail Table -->
        <h3 class="text-xl font-bold text-gray-800 mb-4 border-l-4 border-green-500 pl-3">รายการสินค้า/บริการ
        </h3>
        <div class="overflow-x-auto rounded-xl shadow-lg border border-gray-200">
            <!-- กำหนดความสูงสูงสุดและให้ scroll-y ทำงานเมื่อเกิน -->
            <div class="max-h-[350px] overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 table-striped">
                    <thead class="bg-green-600 text-white sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider rounded-tl-xl">
                                Item Code
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">
                                Description
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider">
                                Unit Price
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider">QTY</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider">Used QTY
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider">Used Amount
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider">Balanced QTY
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider rounded-tr-xl">
                                Balanced Amount
                            </th>
                        </tr>
                    </thead>
                    <tbody id="po-items-detail-body" class="divide-y divide-gray-100">
                        <!-- JS จะเป็นผู้สร้างข้อมูลตรงนี้ 100% -->
                    </tbody>

                    <tfoot class="bg-gray-100 border-t-4 border-gray-300 sticky bottom-0 z-10">
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-right text-lg font-bold text-gray-800">
                                รวมยอด PO สุทธิ:
                            </td>
                            <td id="detailTotalOriginalAmount"
                                class="px-6 py-4 text-right text-xl font-extrabold text-green-700">
                            </td>
                            <td colspan="3" class="px-6 py-4 text-right text-lg font-bold text-gray-800">
                                ยอดคงเหลือรวม:
                            </td>
                            <td id="detailTotalBalancedAmount"
                                class="px-6 py-4 text-right text-xl font-extrabold text-blue-700">
                                {{ number_format($po->total_amount, 2) }}
                            </td>
                        </tr>
                    </tfoot>

                </table>
            </div>
        </div>



        <!-- Action Buttons (Update/Delete) -->
        <div class="mt-8 flex justify-end space-x-4">
            <button onclick="showTemporaryMessage('ฟังก์ชันแก้ไข PO ยังไม่เปิดใช้งาน', 'bg-yellow-500')"
                class="bg-yellow-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-yellow-600 transition duration-300 shadow-lg shadow-yellow-200 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                    <path fill-rule="evenodd"
                        d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                        clip-rule="evenodd" />
                </svg>
                แก้ไข PO
            </button>
            <button onclick="showTemporaryMessage('ฟังก์ชันลบ PO ยังไม่เปิดใช้งาน', 'bg-red-600')"
                class="bg-red-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-red-700 transition duration-300 shadow-lg shadow-red-200 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 6h6v10H7V6z"
                        clip-rule="evenodd" />
                </svg>
                ลบ PO
            </button>
        </div>
    </div>


</div>

<!-- Modal for Column Filter -->
<div id="column-filter-modal" class="fixed inset-0 hidden bg-transparent z-50" onclick="closeColumnFilterModal()">
    <div id="column-filter-content" onclick="event.stopPropagation()"
        class="shadow-2xl bg-white rounded-xl flex flex-col w-[300px] absolute border border-gray-100">

        <div class="px-4 pt-4 pb-2 border-b border-gray-100 text-sm font-semibold text-gray-600">
            <span id="modal-column-name" class="font-extrabold text-gray-800">Column</span>
        </div>

        <div class="px-4 py-3 border-b border-gray-100">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="column-filter-search" placeholder="Search values..."
                    class="pl-9 pr-3 w-full h-9 outline-none bg-gray-50 border border-gray-200 rounded-lg text-sm transition-all focus:border-blue-400 focus:bg-white"
                    oninput="handleSearch(this.value)">
            </div>
        </div>

        <div class="px-4 pt-3 pb-2 border-b border-gray-100">
            <div class="flex justify-between space-x-2 mb-3">
                <button type="button" onclick="selectAll()"
                    class="w-1/2 text-xs text-center bg-green-300 hover:bg-green-400 text-gray-800 rounded py-1">Select All</button>
                <button type="button" onclick="deselectAll()"
                    class="w-1/2 text-xs text-center bg-red-300 hover:bg-red-400 text-gray-800 rounded py-1">Deselect All</button>
            </div>
        </div>

        <div id="column-filter-checkbox-list" class="overflow-y-auto px-4 py-2 text-sm max-h-60 flex-grow"></div>

        <div class="flex justify-end space-x-2 border-t px-4 py-3 bg-gray-50 rounded-b-xl">
            <button type="button" onclick="applyColumnFilter()"
                class="bg-blue-600 text-white px-4 py-2 text-xs rounded-lg font-semibold hover:bg-blue-700 transition shadow-md">Apply</button>
            <button type="button" onclick="closeColumnFilterModal()"
                class="bg-white border border-gray-300 text-gray-700 px-4 py-2 text-xs rounded-lg font-semibold hover:bg-gray-100 transition shadow-sm">Cancel</button>
        </div>
    </div>
</div>




<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
let currentColumn = null;
let allValues = [];
let filterState = {};           // filtered / closed / open
let filterStateValues = {};     // เก็บค่า checkbox ที่เลือกแต่ละ column
let tempSelected = []; // เก็บค่าที่ติ๊กก่อน Apply


// เปิด modal
function toggleFilterDropdown(column) {
    currentColumn = column;
    loadColumnValues(column);
    showColumnFilterModal(column, allValues);

    const btn = document.querySelector(`button[data-column="${column}"]`);
    btn.innerHTML = `<i class="fas fa-chevron-up"></i>`;
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
function handleSearch(val) {
    const checkboxes = document.querySelectorAll('#column-filter-checkbox-list div');
    checkboxes.forEach(div => {
        const label = div.querySelector('label').textContent.toLowerCase();
        div.style.display = label.includes(val.toLowerCase()) ? 'flex' : 'none';
    });
}

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
        "Item Code": row.cells[0].textContent.trim(),
        "Description": row.cells[1].textContent.trim(),
        "Unit Price": row.cells[2].textContent.trim(),
        "QTY": row.cells[3].textContent.trim(),
        "Amount": row.cells[4].textContent.trim(),
        "Used QTY": row.cells[5].textContent.trim(),
        "Used Amount": row.cells[6].textContent.trim(),
        "Balanced QTY": row.cells[7].textContent.trim(),
        "Balanced Amount": row.cells[8].textContent.trim()
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
                    <td class="px-4 py-3 text-right">${formatCurrency(item.balanced_amount)}</td>
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

                row.innerHTML = `
                    <td data-column="po_no" class="px-2 py-2">${po.po_no}</td>
                    <td data-column="customer_name" class="px-2 py-2">${po.customer_name}</td>
                    <td data-column="po_date" class="px-2 py-2">${po.po_date}</td>
                    <td data-column="total_amount" class="px-2 py-2 text-right">${po.total_amount.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                    <td data-column="balanced_amount" class="px-2 py-2 text-right">${po.total_amount.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                `;
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