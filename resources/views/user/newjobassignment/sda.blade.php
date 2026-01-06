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



<div class="flex flex-col lg:flex-row min-h-[calc(100vh-60px)] overflow-hidden">
    <!-- Main Content -->
    <main class="flex-1 bg-gray-100 overflow-y-auto">

        <div class="flex justify-between items-center bg-white p-4 rounded-xl mb-6 shadow-md ">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 w-full items-stretch">

                <!-- Summary -->
                <div class="bg-white p-2 rounded-xl shadow-md min-h-[60px]">
                    <h3 class="text-sm font-sarabun text-gray-500 mb-2">Added Job Total</h3>
                    <div class="text-4xl font-bold text-blue-600 text-center">5</div>
                    <div class="text-sm text-gray-500 mt-1 text-center">
                        Completed: <span class="font-sarabun"></span>
                    </div>
                </div>

                <!-- Reject -->
                <div class="bg-white p-2 rounded-xl shadow-md min-h-[60px]">
                    <h3 class="text-sm font-sarabun text-gray-500 mb-2">Reject</h3>
                    <div class="text-4xl font-bold text-red-600 text-center">10</div>
                </div>

                <!-- Pending -->
                <div class="bg-white p-2 rounded-xl shadow-md min-h-[60px]">
                    <h3 class="text-sm font-sarabun text-gray-500 mb-2 ">Pending</h3>
                    <div class="text-4xl font-bold text-orange-400 text-center">10</div>
                </div>

                <!-- Approved -->
                <div class="bg-white p-2 rounded-xl shadow-md min-h-[60px]">
                    <h3 class="text-sm font-sarabun text-gray-500 mb-2 ">Approved</h3>
                    <div class="text-4xl font-bold text-green-600 text-center">10</div>
                </div>

            </div>


        </div>


        <div class="bg-white p-4 rounded-xl shadow-md">
            <div class="flex items-center justify-between mb-1">
                <h2 class="text-2xl font-sarabun text-blue-900">Request Added Job </h2>

                <button type="button" id="exportPOToExcel" onclick="exportPOToExcel()" class="px-3 py-1.5 rounded-md font-sarabun text-sm text-white
                bg-gradient-to-r from-green-600 to-green-500
                shadow hover:shadow-md hover:scale-[1.02] transition-all">
                    <i class="fas fa-file-excel mr-1 text-sm"></i>
                    Export visible Data
                </button>

            </div>

            <div class="relative overflow-x-auto mt-2 h-[395px] font-sarabun">
                <table class="min-w-max table-fixed border-separate border-spacing-0
                                [--th-h:20px]
                                [--th-w:20px]
                                [--th-px:6px]
                                [--th-py:2px]

                                [--col-1:110px] [--col-2:130px] [--col-3:130px]
                                [--col-4:130px] [--col-5:130px] [--col-6:140px]">

                    <thead class="bg-blue-950 text-white font-sarabun text-base sticky top-0 z-[200]">
                        <tr>

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
                                    <span class="tracking-wide font-sarabun text-xs text-white/90">Job<br>Adding
                                        Status</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="1">
                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                    </span>
                                </div>
                            </th>

                            <th
                                class=" whitespace-nowrap text-center border-b border-blue-900 group sticky top-0 left-[calc(var(--col-1)+var(--col-2))] z-[130] bg-blue-950 w-[var(--col-3)]">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="tracking-wide font-sarabun text-xs text-white/90">Refcode
                                        On ERP</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="2">
                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                    </span>
                                </div>
                            </th>

                            <th class="whitespace-nowrap text-center border-b border-blue-900 group sticky top-0
                            left-[calc(var(--col-1)+var(--col-2)+var(--col-3))] z-[120] bg-blue-950 w-[var(--col-4)]">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="tracking-wide font-sarabun text-xs text-white/90">Site
                                        Code</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="3">
                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                    </span>
                                </div>
                            </th>


                            <th
                                class=" whitespace-nowrap text-center border-b border-blue-900 group sticky top-0
                            left-[calc(var(--col-1)+var(--col-2)+var(--col-3)+var(--col-4))] z-[110] bg-blue-950 w-[var(--col-5)]">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="tracking-wide font-sarabun text-xs text-white/90">Site
                                        Name</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="4">
                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                    </span>
                                </div>
                            </th>

                            <th
                                class=" whitespace-nowrap text-center border-b border-blue-900 group sticky top-0
                        left-[calc(var(--col-1)+var(--col-2)+var(--col-3)+var(--col-4)+var(--col-5))] z-[100] bg-blue-950 w-[var(--col-6)]">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="tracking-wide font-sarabun text-xs text-white/90">Job
                                        <br> Description</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="5">
                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                    </span>
                                </div>
                            </th>


                            <th class=" whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="tracking-wide font-sarabun text-xs text-white/90">Project
                                        Code</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="6">
                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                    </span>
                                </div>
                            </th>


                            <th class=" whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="tracking-wide font-sarabun text-xs text-white/90">Office
                                        Code</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="7">
                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                    </span>
                                </div>
                            </th>


                            <th class=" whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span
                                        class="tracking-wide font-sarabun text-xs text-white/90">Customer<br>Region</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="8">
                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                    </span>
                                </div>
                            </th>

                            <th class=" whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="tracking-wide font-sarabun text-xs text-white/90">Estimated
                                        <br> Revenue</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="9">
                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                    </span>
                                </div>
                            </th>

                            <th class=" whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="tracking-wide font-sarabun text-xs text-white/90">Estimated
                                        <br> Service Cost</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="10">
                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                    </span>
                                </div>
                            </th>

                            <th class=" whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="tracking-wide font-sarabun text-xs text-white/90">Estimated
                                        <br> Material Cost</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="11">
                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                    </span>
                                </div>
                            </th>

                            <th class=" whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="tracking-wide font-sarabun text-xs text-white/90">Estimated
                                        <br> Gross Profit</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="12">
                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                    </span>
                                </div>
                            </th>

                            <th class=" whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="tracking-wide font-sarabun text-xs text-white/90">Estimated
                                        <br> GrossProfit Margin</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="13">
                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                    </span>
                                </div>
                            </th>

                            <th class=" whitespace-nowrap text-center border-b border-blue-900 group">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="tracking-wide font-sarabun text-xs text-white/90">Requester</span>

                                    <span
                                        class="filter-icon cursor-pointer inline-flex items-center opacity-60 group-hover:opacity-100 transition-opacity"
                                        data-col="14">
                                        <i class="fi fi-br-bars-filter text-xs text-white"></i>
                                    </span>
                                </div>
                            </th>


                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($newjob as $item)

                        <tr class="hover:bg-red-100 group transition-colors font-sarabun duration-200 text-[10px] ">
                            <td
                                class=" py-1 px-3 border-b whitespace-nowrap text-left sticky left-0 z-[70] bg-white group-hover:bg-red-100 transition">
                                {{ $item->Refcode }}
                            </td>

                            <td
                                class="sticky left-[var(--col-1)] z-[60] py-1 px-1 border-b whitespace-nowrap bg-white text-center group-hover:bg-red-100 transition">
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
                                $color = $statusColors[$item->Job_Adding_Status] ?? $statusColors['Pending'];
                                @endphp

                                @if ($isAuthorized && $item->Job_Adding_Status === 'Pending')
                                <button type="button" class="status-btn
                                        min-w-[80px] justify-center
                                        {{ $color['bg'] }} {{ $color['text'] }}
                                        px-2 py-1 rounded-full font-sarabun text-xs
                                        {{ $color['hover'] }} inline-flex items-center gap-2" 
                                        onclick="openStatusDropdown(this, '{{ route('update.job.status', $item->id) }}')">
                                    <span class="w-2 h-2 {{ $color['dot'] }} rounded-full"></span>
                                    {{ $item->Job_Adding_Status }}
                                </button>
                                @else
                                <span class="inline-flex items-center {{ $color['bg'] }} {{ $color['text'] }}
                     text-xs font-sarabun px-2 py-1 rounded-full">
                                    <span class="w-2 h-2 mr-1 {{ $color['dot'] }} rounded-full"></span>
                                    {{ $item->Job_Adding_Status }}
                                </span>
                                @endif
                            </td>


                            <td
                                class="py-1 px-1 border-b whitespace-nowrap text-left sticky left-[calc(var(--col-1)+var(--col-2))] z-[60] bg-white group-hover:bg-red-100 transition">
                                Ready/Not Ready</td>

                            <td
                                class="py-1 px-1 border-b whitespace-nowrap text-left sticky left-[calc(var(--col-1)+var(--col-2)+var(--col-3))] z-[55] bg-white group-hover:bg-red-100 transition">
                                {{ $item->Site_Code }}
                            </td>

                            <td class="py-1 px-1 border-b whitespace-nowrap text-left sticky left-[calc(var(--col-1)+var(--col-2)+var(--col-3)+var(--col-4))]
 z-[60] bg-white group-hover:bg-red-100 transition">
                                {{ $item->Site_Name }}
                            </td>

                            <td class="py-1 px-1 border-b whitespace-nowrap text-left sticky left-[calc(var(--col-1)+var(--col-2)+var(--col-3)+var(--col-4)+var(--col-5))]
 z-[50] bg-white group-hover:bg-red-100 transition">
                                {{ $item->Job_Description }}</td>

                            <td class="py-1 px-1 border-b whitespace-nowrap text-left">{{ $item->Project_Code }}
                            </td>
                            <td class="py-1 px-1 border-b whitespace-nowrap text-left">{{ $item->Office_Code }}
                            </td>
                            <td class="py-1 px-1 border-b whitespace-nowrap text-left">
                                {{ $item->Customer_Region }}</td>
                            <td class="py-1 px-1 border-b whitespace-nowrap text-right">
                                {{ $item->Estimated_Revenue }}</td>
                            <td class="py-1 px-1 border-b whitespace-nowrap text-right">
                                {{ $item->Estimated_Service_Cost }}</td>
                            <td class="py-1 px-1 border-b whitespace-nowrap text-right">
                                {{ $item->Estimated_Material_Cost }}</td>
                            <td class="py-1 px-1 border-b whitespace-nowrap text-right">
                                {{ $item->Estimated_Gross_Profit }}</td>
                            <td class="py-1 px-1 border-b whitespace-nowrap text-center">
                                {{ $item->Estimated_Gross_ProfitMargin }}</td>
                            <td class="py-1 px-1 border-b whitespace-nowrap text-center">{{ $item->Requester }}
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
                        class="text-xs font-sarabun text-gray-500 bg-gray-100 px-4 py-2 rounded-full">
                        แสดง <span class="text-indigo-600 font-sarabun">1-10</span> จากทั้งหมด <span
                            class="text-gray-900 font-sarabun">15</span> รายการ
                    </span>
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

<div id="statusDropdown" class="fixed bg-white border border-gray-200 rounded-lg shadow-lg
            min-w-[160px] z-[999] hidden">
    <form id="statusForm" method="POST">
        @csrf
        @method('PUT')

        <button type="button" data-status="Approved" class="w-full px-4 py-2 text-left hover:bg-gray-100
                   text-green-700 flex items-center gap-2 text-xs" onclick="confirmStatusChange(this)">
            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
            Approved
        </button>

        <button type="button" data-status="Rejected" class="w-full px-4 py-2 text-left hover:bg-gray-100
                   text-red-700 flex items-center gap-2 text-xs" onclick="confirmStatusChange(this)">
            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
            Rejected
        </button>
    </form>
</div>







<!-- ฟังชั่น Filter  -->
<script>
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

const ICONS = {
  normal: `<i class="fi fi-br-bars-filter text-xs text-gray-300 transition duration-150"></i>`,
  filter: `<i class="fi fi-br-bars-filter text-xs text-blue-500 transition duration-150"></i>`,
  sortAsc: `<i class="fa-solid fa-arrow-down-a-z text-xs text-indigo-500 transition duration-150"></i>`,
  sortDesc: `<i class="fa-solid fa-arrow-down-z-a text-xs text-indigo-500 transition duration-150"></i>`
};





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
        Object.keys(filters).length === 0
            ? allRows
            : visibleRows;

    const values = [...new Set(
        sourceRows.map(r =>
            r.children[colIndex]?.innerText.trim() ?? ""
        )
    )].sort((a, b) =>
        a.localeCompare(b, undefined, { numeric: true })
    );

    // ✅ ถ้าไม่เคย filter → ยังไม่ติ๊กอะไรเลย
    const selected = filters[colIndex] ?? [];

    values.forEach(v => {
        list.innerHTML += `
            <label
                class="filter-item flex items-center space-x-2 py-1 px-2 rounded cursor-pointer
                    hover:bg-red-100 transition"
            >
                <input type="checkbox" class="filter-checkbox" value="${v}">
                <span>${v}</span>
            </label>
        `;
    });
;
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
        } 
        else if (value.includes(keyword)) {
            // ✅ match → แสดง + ติ๊ก
            label.style.display = "";
            checkbox.checked = true;
        } 
        else {
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
            const value = row.children[colIndex]?.innerText.trim() ?? "";

            if (!allowed.includes(value)) return false;
        }
        return true;
    });

    totalRows = visibleRows.length;

    // 🔑 ถ้ามี sort อยู่ → sort ใหม่
    if (sortState.col !== null && sortState.direction !== null) {
        sortTable(sortState.col, sortState.direction);
        return; // sortTable จะ renderPagination ให้แล้ว
    }

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
    let sortState = {
        col: null,
        direction: null // 'asc' | 'desc'
    };

function sortTable(colIndex, direction) {
    if (colIndex == null) return;

    sortState.col = colIndex;
    sortState.direction = direction;

    const tbody = document.querySelector("tbody");

    visibleRows.sort((a, b) => {
        let v1 = a.children[colIndex]?.innerText.trim() ?? "";
        let v2 = b.children[colIndex]?.innerText.trim() ?? "";

        const n1 = parseFloat(v1.replace(/,/g, ""));
        const n2 = parseFloat(v2.replace(/,/g, ""));

        if (!isNaN(n1) && !isNaN(n2)) {
            return direction === 'asc' ? n1 - n2 : n2 - n1;
        }

        return direction === 'asc'
            ? v1.localeCompare(v2, undefined, { numeric: true })
            : v2.localeCompare(v1, undefined, { numeric: true });
    });

    visibleRows.forEach(tr => tbody.appendChild(tr));

    currentPage = 1;
    renderPagination();

    // ✅ update sort icons
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

    iconWrap.innerHTML = isFiltered
        ? ICONS.filter
        : ICONS.normal;
}

function updateAllColumnIcons() {
    document.querySelectorAll(".filter-icon").forEach(icon => {
        const col = Number(icon.dataset.col);

        // 1. sort มาก่อน
        if (sortState.col === col) {
            icon.innerHTML =
                sortState.direction === "asc"
                    ? ICONS.sortAsc
                    : ICONS.sortDesc;
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






<script>
    const dropdown = document.getElementById('statusDropdown');
    const form = document.getElementById('statusForm');

    function openStatusDropdown(btn, actionUrl) {

        // 🔁 ถ้ากดปุ่มเดิมซ้ำ → ปิด
        if (dropdown.classList.contains('hidden') === false && activeStatusBtn === btn) {
            dropdown.classList.add('hidden');
            activeStatusBtn = null;
            return;
        }

        const rect = btn.getBoundingClientRect();

        dropdown.style.top  = rect.bottom + 6 + 'px';
        dropdown.style.left = rect.left + 'px';

        form.action = actionUrl;

        dropdown.classList.remove('hidden');
        activeStatusBtn = btn;
    }


    // ✅ CONFIRM ก่อน submit
    function confirmStatusChange(button) {
        const status = button.dataset.status;

        const config = {
            Approved: {
                icon: 'question',
                title: 'ยืนยันการอนุมัติ?',
                text: 'คุณต้องการอนุมัติรายการนี้ใช่หรือไม่',
                color: '#22c55e'
            },
            Rejected: {
                icon: 'warning',
                title: 'ยืนยันการปฏิเสธ?',
                text: 'คุณต้องการปฏิเสธรายการนี้ใช่หรือไม่',
                color: '#ef4444'
            }
        };

        Swal.fire({
            icon: config[status].icon,
            title: config[status].title,
            text: config[status].text,
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: config[status].color,
            cancelButtonColor: '#9ca3af',
            customClass: {
                popup: 'font-sarabun'
            }
        }).then(result => {
            if (result.isConfirmed) {

                // 🔥 ลบ input เก่าก่อน (กัน submit ซ้ำ)
                const old = form.querySelector('input[name="Job_Adding_Status"]');
                if (old) old.remove();

                // 🔥 ใส่ค่าใหม่
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'Job_Adding_Status';
                input.value = status;

                form.appendChild(input);
                form.submit();
            }
        });
    }

    // คลิกนอก → ปิด dropdown
    document.addEventListener('click', e => {
        if (
            !e.target.closest('#statusDropdown') &&
            !e.target.closest('.status-btn')
        ) {
            dropdown.classList.add('hidden');
        }
    });

    // scroll / resize → ปิด
    window.addEventListener('scroll', () => dropdown.classList.add('hidden'), true);
    window.addEventListener('resize', () => dropdown.classList.add('hidden'));
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


@endsection