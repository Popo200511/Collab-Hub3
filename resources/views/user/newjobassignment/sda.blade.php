@extends('layouts.user')

@section('title', 'SDA')

@section('content')

<!-- Export To Excel -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<!--sweetalert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://unpkg.com/lucide@latest"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">


<!-- Load Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

<style>
    .swal-title,
    .swal-text {
        font-family: 'Sarabun', sans-serif;
    }
</style>
@endif



<div class="flex min-h-[calc(100vh-64px)]">

    <!-- Main Content -->
    <main class="flex-1 bg-gray-100 overflow-y-auto">

        <div class="bg-white p-4 rounded-xl shadow-md">
            <div class="flex items-center justify-between mb-1">
                <h2 class="text-2xl font-sarabun text-blue-900">Request Added Job </h2>

                <button type="button" id="exportPOToExcel" onclick="exportPOToExcel()" class="px-4 py-2 rounded-lg font-sarabun text-white
              bg-gradient-to-r from-green-600 to-green-500
              shadow-md hover:shadow-lg hover:scale-105 transition-all">
                    <i class="fas fa-file-excel mr-2"></i> Export visible Data
                </button>
            </div>

            <div class="overflow-y-auto h-[566px]">
                <table class="min-w-full border-collapse table-auto">
                    <thead class="bg-blue-950 text-white text-base sticky top-0 z-10">
                        <tr>

                            <th class="py-3 px-4 whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span
                                        class="tracking-wide font-sarabun text-base font-medium text-white/90">Refcode</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="0">
                                        <i class="fi fi-br-bars-filter text-base text-white"></i>
                                    </span>
                                </div>
                            </th>

                            <th class="py-3 px-4 whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span
                                        class="tracking-wide font-sarabun text-base font-medium text-white/90">Job<br>Adding
                                        Status</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="1">
                                        <i class="fi fi-br-bars-filter text-base text-white"></i>
                                    </span>
                                </div>
                            </th>

                            <th class="py-3 px-4 whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="tracking-wide font-sarabun text-base font-medium text-white/90">Refcode
                                        On ERP</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="2">
                                        <i class="fi fi-br-bars-filter text-base text-white"></i>
                                    </span>
                                </div>
                            </th>

                            <th class="py-3 px-4 whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="tracking-wide font-sarabun text-base font-medium text-white/90">Site
                                        Code</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="3">
                                        <i class="fi fi-br-bars-filter text-base text-white"></i>
                                    </span>
                                </div>
                            </th>


                            <th class="py-3 px-4 whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="tracking-wide font-sarabun text-base font-medium text-white/90">Site
                                        Name</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="4">
                                        <i class="fi fi-br-bars-filter text-base text-white"></i>
                                    </span>
                                </div>
                            </th>

                            <th class="py-3 px-4 whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="tracking-wide font-sarabun text-base font-medium text-white/90">Job
                                        <br> Description</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="5">
                                        <i class="fi fi-br-bars-filter text-base text-white"></i>
                                    </span>
                                </div>
                            </th>


                            <th class="py-3 px-4 whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="tracking-wide font-sarabun text-base font-medium text-white/90">Project
                                        Code</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="6">
                                        <i class="fi fi-br-bars-filter text-base text-white"></i>
                                    </span>
                                </div>
                            </th>


                            <th class="py-3 px-4 whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="tracking-wide font-sarabun text-base font-medium text-white/90">Office
                                        Code</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="7">
                                        <i class="fi fi-br-bars-filter text-base text-white"></i>
                                    </span>
                                </div>
                            </th>


                            <th class="py-3 px-4 whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span
                                        class="tracking-wide font-sarabun text-base font-medium text-white/90">Customer<br>Region</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="8">
                                        <i class="fi fi-br-bars-filter text-base text-white"></i>
                                    </span>
                                </div>
                            </th>

                            <th class="py-3 px-4 whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span
                                        class="tracking-wide font-sarabun text-base font-medium text-white/90">Estimated
                                        <br> Revenue</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="9">
                                        <i class="fi fi-br-bars-filter text-base text-white"></i>
                                    </span>
                                </div>
                            </th>

                            <th class="py-3 px-4 whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span
                                        class="tracking-wide font-sarabun text-base font-medium text-white/90">Estimated
                                        <br> Service Cost</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="10">
                                        <i class="fi fi-br-bars-filter text-base text-white"></i>
                                    </span>
                                </div>
                            </th>

                            <th class="py-3 px-4 whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span
                                        class="tracking-wide font-sarabun text-base font-medium text-white/90">Estimated
                                        <br> Material Cost</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="11">
                                        <i class="fi fi-br-bars-filter text-base text-white"></i>
                                    </span>
                                </div>
                            </th>

                            <th class="py-3 px-4 whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span
                                        class="tracking-wide font-sarabun text-base font-medium text-white/90">Estimated
                                        <br> Gross Profit</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="12">
                                        <i class="fi fi-br-bars-filter text-base text-white"></i>
                                    </span>
                                </div>
                            </th>

                            <th class="py-3 px-4 whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span
                                        class="tracking-wide font-sarabun text-base font-medium text-white/90">Estimated
                                        <br> GrossProfit Margin</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="13">
                                        <i class="fi fi-br-bars-filter text-base text-white"></i>
                                    </span>
                                </div>
                            </th>

                            <th class="py-3 px-4 whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span
                                        class="tracking-wide font-sarabun text-base font-medium text-white/90">Requester</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="14">
                                        <i class="fi fi-br-bars-filter text-base text-white"></i>
                                    </span>
                                </div>
                            </th>


                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($newjob as $item)

                        <tr class="hover:bg-red-100 transition-colors duration-200 text-xs ">
                            <td class="py-1 px-4 border-b whitespace-nowrap text-left">{{ $item->Refcode }}</td>

                            <td class="py-1 px-4 border-b whitespace-nowrap text-center">
                                @php
                                $isAuthorized = Auth::check() && Auth::user()->status == 4;
                                $statusColors = [
                                'Pending' => [
                                'bg' => 'bg-yellow-100',
                                'text' => 'text-yellow-800',
                                'dot' => 'bg-yellow-500',
                                'hover' => 'hover:bg-yellow-200',
                                ],
                                'Approved' => [
                                'bg' => 'bg-green-100',
                                'text' => 'text-green-800',
                                'dot' => 'bg-green-500',
                                'hover' => 'hover:bg-green-200',
                                ],
                                'Rejected' => [
                                'bg' => 'bg-red-100',
                                'text' => 'text-red-800',
                                'dot' => 'bg-red-500',
                                'hover' => 'hover:bg-red-200',
                                ],
                                ];
                                $color =
                                $statusColors[$item->Job_Adding_Status] ?? $statusColors['Pending'];
                                @endphp

                                @if ($isAuthorized && $item->Job_Adding_Status === 'Pending')
                                {{-- Pending → Dropdown --}}
                                <div class="relative inline-block">
                                    <button type="button"
                                        class="status-dropdown-btn {{ $color['bg'] }} {{ $color['text'] }} px-2 py-1 rounded-full font-semibold text-sm {{ $color['hover'] }} transition cursor-pointer flex items-center gap-2"
                                        onclick="toggleDropdown(this)">
                                        <span class="w-2 h-2 {{ $color['dot'] }} rounded-full"></span>
                                        {{ $item->Job_Adding_Status }}
                                    </button>
                                    <div
                                        class="status-dropdown absolute top-full left-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg min-w-max z-10 hidden">
                                        <form action="{{ route('update.job.status', $item->id) }}" method="POST"
                                            style="display: contents;">
                                            @csrf
                                            @method('PUT')
                                            @foreach (['Approved', 'Rejected'] as $status)
                                            @php $c = $statusColors[$status]; @endphp
                                            <button type="button" onclick="confirmStatusChange(this)"
                                                data-status="{{ $status }}"
                                                class="w-full px-4 py-2 text-left hover:bg-gray-100 {{ $c['text'] }} flex items-center gap-2 text-sm">
                                                <span class="w-2 h-2 {{ $c['dot'] }} rounded-full"></span>
                                                {{ $status }}
                                            </button>

                                            @endforeach
                                        </form>
                                    </div>
                                </div>
                                @else
                                {{-- Approved / Rejected หรือ ผู้ใช้งานทั่วไป → ปิด dropdown แต่ UI เหมือนกัน --}}
                                <span
                                    class="inline-flex items-center {{ $color['bg'] }} {{ $color['text'] }} text-sm font-semibold px-2 py-1 rounded-full">
                                    <span class="w-2 h-2 mr-1 {{ $color['dot'] }} rounded-full"></span>
                                    {{ $item->Job_Adding_Status }}
                                </span>
                                @endif
                            </td>

                            <td class="px-4 border-b whitespace-nowrap text-left">Ready/Not Ready</td>

                            <td class="py-1 px-4 border-b whitespace-nowrap text-left">{{ $item->Site_Code }}
                            </td>
                            <td class="py-1 px-4 border-b whitespace-nowrap text-left">{{ $item->Site_Name }}
                            </td>
                            <td class="py-1 px-4 border-b whitespace-nowrap text-left">
                                {{ $item->Job_Description }}</td>
                            <td class="py-1 px-4 border-b whitespace-nowrap text-left">{{ $item->Project_Code }}
                            </td>
                            <td class="py-1 px-4 border-b whitespace-nowrap text-left">{{ $item->Office_Code }}
                            </td>
                            <td class="py-1 px-4 border-b whitespace-nowrap text-left">
                                {{ $item->Customer_Region }}</td>
                            <td class="py-1 px-4 border-b whitespace-nowrap text-right">
                                {{ $item->Estimated_Revenue }}</td>
                            <td class="py-1 px-4 border-b whitespace-nowrap text-right">
                                {{ $item->Estimated_Service_Cost }}</td>
                            <td class="py-1 px-4 border-b whitespace-nowrap text-right">
                                {{ $item->Estimated_Material_Cost }}</td>
                            <td class="py-1 px-4 border-b whitespace-nowrap text-right">
                                {{ $item->Estimated_Gross_Profit }}</td>
                            <td class="py-1 px-4 border-b whitespace-nowrap text-center">
                                {{ $item->Estimated_Gross_ProfitMargin }}</td>
                            <td class="py-1 px-4 border-b whitespace-nowrap text-center">{{ $item->Requester }}
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

            <div id="listViewPagination"
                class="mt-4 flex flex-col lg:flex-row items-center justify-between space-y-4 lg:space-y-0 p-5 bg-white rounded-xl border border-gray-200 shadow-sm transition-all duration-300">

                <div class="flex items-center space-x-3 order-2 lg:order-1">
                    <label for="rowsPerPageList"
                        class="text-xs font-bold uppercase tracking-widest text-gray-400">แสดงรายการ:</label>
                    <div class="relative">
                        <select id="rowsPerPageList" onchange="changeRowsPerPage(this.value)"
                            class="block py-2 pl-4 pr-10 border border-gray-200 rounded-xl text-sm font-bold bg-gray-50 cursor-pointer appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                            <option value="5">5 รายการ</option>
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
                            class="w-10 h-10 rounded-xl bg-indigo-600 text-white font-bold text-sm shadow-md shadow-indigo-200">1</button>
                        <button
                            class="w-10 h-10 rounded-xl bg-white text-gray-600 font-semibold text-sm hover:bg-indigo-50 transition-all">2</button>
                        <button
                            class="w-10 h-10 rounded-xl bg-white text-gray-600 font-semibold text-sm hover:bg-indigo-50 transition-all">3</button>
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
                        class="text-sm font-medium text-gray-500 bg-gray-100 px-4 py-2 rounded-full">
                        แสดง <span class="text-indigo-600 font-bold">1-10</span> จากทั้งหมด <span
                            class="text-gray-900 font-bold">15</span> รายการ
                    </span>
                </div>
            </div>
        </div>
    </main>
</div>


<!-- ก้อน Filter ที่ใช้ทุกคอลั่ม -->
<div id="column-filter-modal" class="fixed inset-0 z-[100] hidden bg-transparent">
    <div id="column-filter-content" onclick="event.stopPropagation()"
        class="shadow-2xl bg-white rounded-xl flex flex-col w-[300px] absolute border border-gray-100">

        <!-- Search Input -->
        <div class="px-4 py-3 border-b border-gray-100">
            <div class="relative">
                <i data-lucide="search"
                    class="fa-solid fa-magnifying-glass w-4 h-4 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2"></i>
                <input type="text" id="column-filter-search" placeholder=""
                    class="pl-9 pr-3 w-full h-9 outline-none bg-gray-50 border border-gray-200 rounded-lg text-sm transition-all focus:border-blue-400 focus:bg-white"
                    oninput="handleSearch(this.value)">
            </div>
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
    /* -----------------------------------------------------
   ICON CONFIG
----------------------------------------------------- */
const ICONS = {
  normal: `<i class="fi fi-br-bars-filter text-white transition duration-150"></i>`,
  active: `<i class="fi fi-br-bars-filter text-white transition duration-150"></i>`,
  filter: `<i class="fi fi-br-bars-filter text-blue-400 transition duration-150"></i>`
};

/* -----------------------------------------------------
   GLOBAL STATE
----------------------------------------------------- */
let openFilterColumn = null;
let filters = {};                 // filters[col] = array OR null
let originalColumnValues = {};    // ค่าทั้งหมดในแต่ละคอลัมน์ (สำหรับ Checkbox list)

let allRows = [];       // ทุก tr ใน tbody (ต้นฉบับ)
let visibleRows = [];   // tr ที่ผ่าน filter (สำหรับ pagination)
let totalRows = 0;

let rowsPerPage = 10;
let currentPage = 1;

/* -----------------------------------------------------
   INITIAL LOAD
----------------------------------------------------- */
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".filter-icon").forEach(icon => {
        icon.addEventListener("click", e => {
            e.stopPropagation(); // ❗ สำคัญมาก ป้องกันเปิดซ้อน
            openColumnFilter(Number(icon.dataset.col));
        });
    });



    /* เก็บค่าต้นทางทั้งหมด (แบบ Excel) */
    const trs = Array.from(document.querySelectorAll("tbody tr"));
    const colCount = document.querySelectorAll("thead th").length;

    for (let i = 0; i < colCount; i++) originalColumnValues[i] = new Set();

    trs.forEach(row => {
        [...row.children].forEach((cell, col) => {
            originalColumnValues[col].add(cell.innerText.trim());
        });
    });

    allRows = trs;
    // ตอนเริ่มต้น visibleRows = allRows
    visibleRows = allRows.slice();
    totalRows = visibleRows.length;

    setupRowsPerPageOptions();
    renderPagination();
});

/* -----------------------------------------------------
   FILTER
----------------------------------------------------- */
function openColumnFilter(colIndex) {
    if (openFilterColumn === colIndex) {
        closeColumnFilterModal(false);
        return;
    }

    openFilterColumn = colIndex;
    loadFilterValues(colIndex);

    document.querySelectorAll(".filter-icon").forEach(x => {
        x.classList.toggle("filter-active", Number(x.dataset.col) === colIndex);
    });

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

    // --- ต้องแสดงค่าทั้งหมดในคอลัมน์นี้เสมอ (เหมือน Excel) ---
    const values = [...originalColumnValues[colIndex]].sort((a,b) =>
        a.localeCompare(b, undefined, {numeric:true})
    );

    const selected = filters[colIndex] || null;

    values.forEach(v => {
        list.innerHTML += `
            <label class="flex items-center space-x-2 py-1">
                <input type="checkbox" class="filter-checkbox" value="${v}"
                    ${(selected === null || selected.includes(v)) ? "checked" : ""}>
                <span>${v}</span>
            </label>
        `;
    });
}



function handleSearch(text) {
    const list = document.getElementById("column-filter-checkbox-list");
    const keyword = text.toLowerCase().trim();

    const items = list.querySelectorAll("label");

    items.forEach(label => {
        const value = label.querySelector("span").innerText.toLowerCase();
        label.style.display = (value.includes(keyword)) ? "" : "none";
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
    sortTable(openFilterColumn, 'asc');
}


function sortZA() {
    sortTable(openFilterColumn, 'desc');
}




function applyColumnFilter() {
    const col = openFilterColumn;

    const checkboxes = document.querySelectorAll(".filter-checkbox");
    const selected = [...checkboxes]
        .filter(cb => cb.checked)
        .map(cb => cb.value);

    const total = checkboxes.length;

    // 🔑 ถ้าเลือกทั้งหมด หรือไม่ได้เลือกอะไรเลย = ไม่มี filter
    if (selected.length === 0 || selected.length === total) {
        filters[col] = null;
    } else {
        filters[col] = selected;
    }

    applyAllFilters();
    closeColumnFilterModal(false);
}





function applyAllFilters() {
    // เริ่มจาก allRows เสมอ แล้วคัดเฉพาะที่ผ่าน filter
    visibleRows = allRows.filter(row => {
        for (let colKey in filters) {
            const allowed = filters[colKey];
            if (!allowed) continue;
            const colIndex = Number(colKey);
            const value = (row.children[colIndex] && row.children[colIndex].innerText) ? row.children[colIndex].innerText.trim() : "";
            if (!allowed.includes(value)) return false;
        }
        return true;
    });

    totalRows = visibleRows.length;

    // reset pagination
    currentPage = 1;
    renderPagination();

    // update icons (in case filters cleared)
    document.querySelectorAll(".filter-icon").forEach(x => {
        const idx = x.dataset.col;
        x.classList.toggle("filter-active", !!filters[idx]);
    });

}

function closeColumnFilterModal(resetIcon = true) {
    document.getElementById("column-filter-modal").classList.add("hidden");

    if (resetIcon && openFilterColumn != null) {
        const icon = document.querySelector(`.filter-icon[data-col="${openFilterColumn}"]`);
        icon.innerHTML = filters[openFilterColumn] ? ICONS.filter : ICONS.normal;
    }


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

    const presets = [5, 10, 20, 50, 100];

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

    select.value = rowsPerPage;
}

function renderPagination() {
    // ป้องกัน totalPages = 0
    const totalPages = Math.max(1, Math.ceil(totalRows / rowsPerPage));

    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    /* ซ่อนทั้งหมดก่อน */
    allRows.forEach(r => r.style.display = "none");

    /* คำนวณขอบเขตและแสดงเฉพาะ visibleRows ในช่วงหน้า */
    if (totalRows === 0) {
        document.getElementById("paginationSummaryList").innerText = `แสดง 0-0 จากทั้งหมด 0 รายการ`;
    } else {
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        visibleRows.slice(start, end).forEach(r => r.style.display = "");
        document.getElementById("paginationSummaryList").innerText =
            `แสดง ${start + 1}-${Math.min(end, totalRows)} จากทั้งหมด ${totalRows} รายการ`;
    }

    /* ปุ่ม pagination */
    document.getElementById("prevPageBtnList").disabled = currentPage === 1;
    document.getElementById("nextPageBtnList").disabled = currentPage >= totalPages;

    const pageContainer = document.getElementById("pageNumbersList");
    if (pageContainer) {
        pageContainer.innerHTML = "";
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement("button");
            btn.textContent = i;
            btn.className =
                "px-3 py-1 rounded-lg text-sm font-semibold transition " +
                (i === currentPage
                    ? "bg-indigo-600 text-white"
                    : "bg-white border border-gray-300 text-indigo-600 hover:bg-indigo-100");
            btn.onclick = () => goToPage(i);
            pageContainer.appendChild(btn);
        }
    }
}

function goToPage(page) {
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
    function sortTable(colIndex, direction = 'asc') {
    // copy rows เพื่อจัดเรียง
    let sorted = [...allRows];

    sorted.sort((a, b) => {
        const v1 = a.children[colIndex]?.innerText.trim().toLowerCase() ?? "";
        const v2 = b.children[colIndex]?.innerText.trim().toLowerCase() ?? "";

        return direction === 'asc'
            ? v1.localeCompare(v2, undefined, { numeric: true })
            : v2.localeCompare(v1, undefined, { numeric: true });
    });

    // update allRows
    allRows = sorted;

    // เมื่อ sort แล้ว ต้องนำ filter มาคัดอีกครั้ง
    applyAllFilters();
}

</script>










<script>
    function toggleDropdown(btn) {
        const dropdown = btn.nextElementSibling;
            dropdown.classList.toggle('hidden');
                document.querySelectorAll('.status-dropdown').forEach(d => {
                    if (d !== dropdown) d.classList.add('hidden');
                        });
                    }
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.relative')) {
            document.querySelectorAll('.status-dropdown').forEach(d => d.classList.add('hidden'));
            }
    });
</script>








<!-- ฟังชันสำหรับ Export -->
<script>
    function exportPOToExcel() {
    const table = document.querySelector("table");
    const tbody = table.querySelector("tbody");
    const visibleRows = Array.from(tbody.querySelectorAll("tr"))
        .filter(row => row.style.display !== "none");

    if (visibleRows.length === 0) {
        alert("ไม่มีข้อมูลในหน้านี้เพื่อทำการ Export");
        return;
    }

    // เตรียม JSON data
    const exportData = [];

    visibleRows.forEach(row => {
        const cells = row.querySelectorAll("td");

        exportData.push({
            "Refcode": cells[0].innerText.trim(),
            "Job Adding Status": cells[1].innerText.trim(),
            "Refcode on ERP": cells[2].innerText.trim(),
            "Site Code": cells[3].innerText.trim(),
            "Site Name": cells[4].innerText.trim(),
            "Job Description": cells[5].innerText.trim(),
            "Project Code": cells[6].innerText.trim(),
            "Office Code": cells[7].innerText.trim(),
            "Customer Region": cells[8].innerText.trim(),
            "Estimated Revenue": cells[9].innerText.trim(),
            "Estimated Service Cost": cells[10].innerText.trim(),
            "Estimated Material Cost": cells[11].innerText.trim(),
            "Estimated Gross Profit": cells[12].innerText.trim(),
            "Gross Profit Margin": cells[13].innerText.trim(),
            "Requester": cells[14].innerText.trim(),
        });
    });

    // สร้าง Workbook
    const ws = XLSX.utils.json_to_sheet(exportData);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Visible Data");

    // ดาวน์โหลดไฟล์
    XLSX.writeFile(wb, "Visible_Table_Data.xlsx");
}

</script>


<script>
function confirmStatusChange(button) {
    const status = button.dataset.status;
    const form = button.closest('form');

    let config = {
        Approved: {
            icon: 'question',
            title: 'ยืนยันการอนุมัติ?',
            text: 'คุณต้องการอนุมัติรายการนี้ใช่หรือไม่',
            confirmColor: '#22c55e'
        },
        Rejected: {
            icon: 'warning',
            title: 'ยืนยันการปฏิเสธ?',
            text: 'คุณต้องการปฏิเสธรายการนี้ใช่หรือไม่',
            confirmColor: '#ef4444'
        }
    };

    Swal.fire({
        icon: config[status].icon,
        title: config[status].title,
        text: config[status].text,
        showCancelButton: true,
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: config[status].confirmColor,
        cancelButtonColor: '#9ca3af',
        customClass: {
            title: 'swal-title',
            popup: 'font-sarabun'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // สร้าง hidden input แล้ว submit
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'Job_Adding_Status';
            input.value = status;
            form.appendChild(input);

            form.submit();
        }
    });
}
</script>

@endsection