@extends('layouts.user')
@section('title', 'ค้นหา Refcode')
@section('content')


<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" />

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        // ให้แน่ใจว่า script ทำงานหลังจาก HTML โหลดเสร็จ
        document.addEventListener("DOMContentLoaded", function() {
            // ฟังก์ชันส่งออก Excel
            document.getElementById('exportButton').addEventListener('click', function() {
                var wb = XLSX.utils.book_new();

                // สร้างตารางที่ต้องการ export (แค่หัวตาราง)
                var table = document.createElement('table');
                var thead = table.createTHead();
                var row = thead.insertRow();

                // สร้างหัวตาราง (columns)
                var th1 = row.insertCell();
                th1.innerText = "Refcode";
                var th2 = row.insertCell();
                th2.innerText = "Job ( Sitecode )";
                var th3 = row.insertCell();
                th3.innerText = "Office";
                var th4 = row.insertCell();
                th4.innerText = "Project";

                // แปลงตารางเป็น sheet และส่งออก
                var ws = XLSX.utils.table_to_sheet(table);
                XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
                XLSX.writeFile(wb, 'Template Refcode.xlsx');
            });
        });
    </script>

<h2 class="text-center mt-2 text-lg "data-aos="zoom-in-left">Search Refcode</h2>
<h4 class="text-center"data-aos="zoom-in-right">No. of Refcode : {{ $count }}</h4>

    @if (Auth::check())

        @if (in_array(Auth::user()->status, [4]))
            
            <div class="d-flex justify-content-between me-3 ms-3">

                <a href="{{ url('/refcode?export=true') }}" class="btn btn-outline-success"data-aos="fade-right">
                    Export Refcode
                </a>

                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#refcodeModal"data-aos="fade-left">
                    Import Refcode && Template
                </button>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="refcodeModal" tabindex="-1" aria-labelledby="refcodeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content p-4">
                        <div class="modal-header">
                            <h5 class="modal-title" id="refcodeModalLabel">Template Refcode & Import</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- ปุ่ม Export & ฟอร์มอยู่ภายใน Modal -->
                            <div class="flex justify-between items-center w-full px-4 mt-4">
                                <!-- ปุ่มสำหรับ Export Template -->
                                <button type="submit" class="btn btn-outline-success" id="exportButton">
                                    Template Refcode
                                </button>

                                <!-- ฟอร์มสำหรับแนบไฟล์ CSV -->
                                <div id="formContainer" class="container">
                                    <form action="importrefcode" method="POST" enctype="multipart/form-data" id="csvForm"
                                        class="flex flex-col sm:flex-row items-center gap-4 justify-center">
                                        @csrf
                                        <input type="file"
                                            class="w-full sm:w-[300px] h-[29px] text-xs border border-gray-500 rounded-md p-1"
                                            name="csv_file_add" accept=".csv" required>
                                        <input type="submit"
                                    class="bg-green-500 text-white text-xs px-4 py-2 rounded-md hover:bg-green-700 cursor-pointer"
                                            name="preview_add" value="แสดงข้อมูล Refcode ที่ต้องการเพิ่ม" id="uploadButton">
                                    </form>
                                </div>
                            </div>
                        </div>
 							
						<!-- Loader -->
                        <div id="loadingSpinner" class="hidden mt-3 text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">กำลังประมวลผล...</span>
                            </div>
                            <p class="text-sm text-gray-600">กำลังประมวลผลไฟล์ CSV โปรดรอสักครู่...</p>
                        </div>
						
                    </div>
                </div>
            </div>

			
			<script>
				// Loader import
                document.getElementById("csvForm").addEventListener("submit", function () {
                    document.getElementById("uploadButton").disabled = true; // ปิดปุ่ม
                    document.getElementById("loadingSpinner").classList.remove("hidden"); // แสดง Loader
                });
            </script>

            <script>
                // เมื่อกดปุ่ม ให้แสดงหรือซ่อนฟอร์ม
                document.getElementById('showFormButton').addEventListener('click', function() {
                    var formContainer = document.getElementById('formContainer');
                    // เช็คว่า form ซ่อนอยู่หรือไม่ ถ้าอยู่ให้แสดง ถ้าแสดงให้ซ่อน
                    if (formContainer.style.display === 'none' || formContainer.style.display === '') {
                        formContainer.style.display = 'block'; // แสดงฟอร์ม
                    } else {
                        formContainer.style.display = 'none'; // ซ่อนฟอร์ม
                    }
                });
            </script>
        @endif
    @endif


  <!-- แสดงข้อความสำเร็จ -->
    @if (session('success'))
        <div id="successAlert"
            class="alert alert-success bg-green-100 border border-green-500 text-green-800 px-2 py-2 rounded-md mt-2 w-full sm:w-auto sm:max-w-md mx-auto relative">
            <strong>สำเร็จ!</strong> {{ session('success') }}
            <button type="button" onclick="document.getElementById('successAlert').style.display='none'"
                class="absolute top-0 right-0 mt-1 mr-2 text-green-700 hover:text-green-900">
                &times;
            </button>
        </div>
    @endif


    <!-- แสดงข้อความข้อผิดพลาด -->
    @if ($errors->has('error'))
        <div id="errorAlert"
            class="alert alert-danger bg-red-100 border border-red-500 text-red-800 px-2 py-2 rounded-md mt-2 w-full sm:w-auto sm:max-w-md mx-auto relative">
            <strong>ข้อผิดพลาด!</strong> {{ $errors->first('error') }}
            <button type="button" onclick="document.getElementById('errorAlert').style.display='none'"
                class="absolute top-0 right-0 mt-1 mr-2 text-red-700 hover:text-red-900">
                &times;
            </button>
        </div>
    @endif




    @if (!empty($dataToSave) && (is_array($dataToSave) || is_object($dataToSave)))
    <div class="modal fade show d-block" id="refcodeModal" tabindex="-1" role="dialog"
        aria-labelledby="refcodeModalLabel" aria-hidden="true" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="modal-dialog modal-xl relative w-full sm:w-11/12 md:w-9/12 lg:w-8/12 xl:w-7/12" role="document">
            <div class="modal-content bg-white rounded-lg shadow-lg max-h-[650px]">
                <div class="modal-header flex justify-between items-center p-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold" id="refcodeModalLabel">ตรวจสอบข้อมูล Refcode</h2>
                    <a href="home" class="btn-close text-gray-600 hover:text-gray-900" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                        </svg>
                    </a>
                </div>
                <div class="modal-body p-4">
                    <div class="overflow-y-auto" style="max-height: 450px;">
                        <table class="table-auto w-full table-fixed border-collapse">
                            <thead class="sticky top-0 bg-white z-10">
                                <tr class="text-xs text-center">
                                    <th class="px-2 py-3 bg-blue-50 text-gray-700">Refcode</th>
                                    <th class="px-2 py-3 bg-blue-50 text-gray-700">Description</th>
                                    <th class="px-2 py-3 bg-blue-50 text-gray-700">Office</th>
                                    <th class="px-2 py-3 bg-blue-50 text-gray-700">Project</th>
                                    <th class="px-2 py-3 bg-blue-50 text-gray-700">Check Refcode</th>
                                </tr>
                            </thead>
                            <tbody class="text-xs text-center bg-white">
                                @foreach ($dataToSave as $row)
                                    <tr class="border-t border-gray-100 hover:bg-red-200">
                                        @foreach ($row as $key => $cell)
                                            <td class="px-2 py-2">{{ $cell }}</td>
                                        @endforeach
                                        <td class="px-2 py-2">
                                            @php
                                                $check = collect($refcode)->contains('refcode', $row['refcode']);
                                            @endphp
                                            @if ($check)
                                                <span class="text-red-500">refcode ซ้ำกัน</span>
                                            @else
                                                <span class="text-green-500">สามารถ Upload refcode ได้</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>                            
                        </table>
                    </div>
                </div>
                <div class="modal-footer flex  items-center p-4 bg-gray-50">
                    <form action="saverefcode" method="POST" class="flex items-center gap-4">
                        @csrf
                        <input type="hidden" name="data_add" value="{{ json_encode($dataToSave) }}">
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">เพิ่มข้อมูล</button>
                    </form>
                    <a href="home" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">ย้อนกลับ</a>
                </div>
            </div>
        </div>
    </div>
    @endif


<!-- ตาราง -->
    <div class="mt-2 overflow-y-auto" style="height: 620px;">
        <table class="table-auto w-full mt-2 border-collapse" data-aos="zoom-in" >
            <thead class="sticky top-0 bg-white shadow-md">
                <tr class="text-xs text-center">
                    <th class="bg-blue-950 text-neutral-50 px-2 py-1">
                        <div class="flex flex-col items-center">
                            <span>Refcode</span>
                            <input
                                class="filter-input mt-1 w-[200px] h-[20px] p-2 text-xs border border-gray-300 rounded-md text-gray-950"
                                type="text" id="search-refcode" placeholder="">
                        </div>
                    </th>
                    <th class="bg-blue-950 text-neutral-50 px-2 py-1">
                        <div class="flex flex-col items-center">
                            <span>SiteCode</span>
                            <input
                                class="filter-input mt-1 w-[200px] h-[20px] p-2 text-xs border border-gray-300 rounded-md text-gray-950"
                                type="text" id="search-sitecode" placeholder="">

                        </div>
                    </th>
                    <th class="bg-blue-950 text-neutral-50 px-2 py-1">
                        <div class="flex flex-col items-center">
                            <span>Office</span>
                            <input
                                class="filter-input mt-1 w-[200px] h-[20px] p-2 text-xs border border-gray-300 rounded-md text-gray-950"
                                type="text" id="search-office" placeholder="">

                        </div>
                    </th>
                    <th class="bg-blue-950 text-neutral-50 px-2 py-1">
                        <div class="flex flex-col items-center">
                            <span>Project</span>
                            <input
                                class="filter-input mt-1 w-[200px] h-[20px] p-2 text-xs border border-gray-300 rounded-md text-gray-950"
                                type="text" id="search-project" placeholder="">

                        </div>
                    </th>

                </tr>
            </thead>

            <tbody class="text-xs text-center bg-white">
                @foreach ($refcode as $item)
                    <tr class="hover:bg-red-100 hover:text-red-600">
                        <td>{{ $item->refcode }}</td>
                        <td>{{ $item->sitecode }}</td>
                        <td>{{ $item->office }}</td>
                        <td>{{ $item->project }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            $(".filter-input").on("keypress", function(e) {
                if (e.which === 13) { // ตรวจจับปุ่ม Enter
                    var refcode = $("#search-refcode").val().trim();
                    var sitecode = $("#search-sitecode").val().trim();
                    var office = $("#search-office").val().trim();
                    var project = $("#search-project").val().trim();
    
                    // เช็คว่าทุกช่องว่างหรือไม่
                    if (refcode === "" && sitecode === "" && office === "" && project === "") {
                        loadInitialData(); // โหลดข้อมูลทั้งหมดกลับมา
                        return;
                    }
    
                    var searchData = { refcode, sitecode, office, project };
    
                    $.ajax({
                        url: '{{ route('searchRefcode') }}',
                        method: 'GET',
                        data: searchData,
                        success: function(response) {
                            $("tbody").empty(); // ล้างตารางเก่า
                            $.each(response, function(index, item) {
                                $("tbody").append(`
                                    <tr class="hover:bg-red-100 hover:text-red-600">
                                        <td>${item.refcode}</td>
                                        <td>${item.sitecode}</td>
                                        <td>${item.office}</td>
                                        <td>${item.project}</td>
                                    </tr>
                                `);
                            });
                        }
                    });
                }
            });
    
            // ฟังก์ชันโหลดข้อมูลทั้งหมด
            function loadInitialData() {
                $.ajax({
                    url: '{{ route('searchRefcode') }}',
                    method: 'GET',
                    success: function(response) {
                        $("tbody").empty();
                        $.each(response, function(index, item) {
                            $("tbody").append(`
                                <tr class="hover:bg-red-100 hover:text-red-600">
                                    <td>${item.refcode}</td>
                                    <td>${item.sitecode}</td>
                                    <td>${item.office}</td>
                                    <td>${item.project}</td>
                                </tr>
                            `);
                        });
                    }
                });
            }
    
            // โหลดข้อมูลทั้งหมดเมื่อหน้าเว็บโหลดเสร็จ
            loadInitialData();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init();
      </script>



@endsection