@extends('layouts.Tailwind')
@section('title', 'ERP search Refcode')
@section('content')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">

    <h2 class="text-center mt-2 text-lg">Search Refcode</h2>
    <h4 class="text-center">No. of Refcode : {{ $recordCount }} </h4>

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
    @if ($errors->has('error'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: 'error',
                    title: 'ข้อผิดพลาด!',
                    text: '{{ $errors->first('error') }}',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#d33'
                });
            });
        </script>
    @endif

    @if (Auth::check())

        @if (in_array(Auth::user()->status, ['Admin']))
            <div class="d-flex justify-content-end me-3 ms-3">
                <!-- import Refcode -->

                <!-- <a href="{{ route('exportRefcode') }}" class="btn btn-outline-success">
            Export Refcode
        </a> -->
                <button
                    class="flex items-center bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg shadow-md transition duration-200"
                    data-bs-toggle="modal" data-bs-target="#refcodeModal">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5 20h14v-2H5v2zm7-14l-5 5h3v4h4v-4h3l-5-5z" />
                    </svg>
                    Import
                </button>
            </div>

            <!-- Modal import -->
            <div class="modal fade" id="refcodeModal" tabindex="-1" aria-labelledby="refcodeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content rounded-lg shadow-lg">
                        <div class="modal-header bg-blue-950 text-neutral-50 px-4 py-1 ">
                            <h5 class="modal-title" id="refcodeModalLabel">Import Refcode</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- ปุ่ม Export & ฟอร์มอยู่ภายใน Modal -->
                            <div class="flex justify-between items-center w-full px-2 mt-2">

                                <!-- ฟอร์มสำหรับแนบไฟล์ Xlsx -->
                                <div id="formContainer" class="container">
                                    <form action="{{ route('refcode.import') }}" method="POST"
                                        enctype="multipart/form-data" id="csvForm"
                                        class="flex flex-col sm:flex-row items-center gap-2 justify-center">
                                        @csrf
                                        <input type="file"
                                            class="w-full sm:w-[300px] h-[29px] text-xs border border-gray-500 rounded-md p-1"
                                            name="xlsx_file_add" accept=".xlsx" required>
                                        <input type="submit"
                                            class="bg-green-500 text-white text-xs px-4 py-2 rounded-md hover:bg-green-700 cursor-pointer"
                                            style="font-family: 'Sarabun', sans-serif;" name="preview_add"
                                            value="บันทึก Refcode ที่ต้องการเพิ่ม" id="uploadButton">
                                    </form>
                                </div>
                            </div>
                        </div>


                        <!-- Loader -->
                        <div id="loadingSpinner" class="hidden mt-3 text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">กำลังประมวลผล...</span>
                            </div>
                            <p class="text-sm text-gray-600" style="font-family: 'Sarabun', sans-serif;">กำลังบันทึกไฟล์
                                xlsx โปรดรอสักครู่...</p>
                        </div>

                    </div>
                </div>
            </div>

            <script>
                // Loader import
                document.getElementById("csvForm").addEventListener("submit", function() {
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

    <!-- ตาราง Showdata เก่าเปิดมาเจอเลย-->
    <div class="mt-2 w-[100%] max-h-[550px] overflow-x-auto overflow-y-auto">
        <table class="w-full h-full border-collapse">
            <thead class="sticky top-0 bg-white shadow-md">
                <tr class="text-xs text-center">
                    <th class="bg-blue-950 text-neutral-50 px-2 py-1">
                        <div class="flex flex-col items-center">
                            <span>Project Name</span>
                            <input
                                class="filter-input mt-1 w-[200px] h-[20px] p-2 text-xs border border-gray-300 rounded-md text-gray-950"
                                type="text" id="project_name" placeholder="">
                        </div>
                    </th>
                    <th class="bg-blue-950 text-neutral-50 px-2 py-1">
                        <div class="flex flex-col items-center">
                            <span>Ref. Code</span>
                            <input
                                class="filter-input mt-1 w-[200px] h-[20px] p-2 text-xs border border-gray-300 rounded-md text-gray-950"
                                type="text" id="ref_code" placeholder="">
                        </div>
                    </th>
                    <th class="bg-blue-950 text-neutral-50 px-2 py-1">
                        <div class="flex flex-col items-center">
                            <span>Group</span>
                            <input
                                class="filter-input mt-1 w-[200px] h-[20px] p-2 text-xs border border-gray-300 rounded-md text-gray-950"
                                type="text" id="group_group" placeholder="">
                        </div>
                    </th>
                    <!--
                            <th class="bg-blue-950 text-neutral-50 px-2 py-1">
                                <div class="flex flex-col items-center">
                                    <span>Project</span>
                                    <input
                                        class="filter-input mt-1 w-[200px] h-[20px] p-2 text-xs border border-gray-300 rounded-md text-gray-950"
                                        type="text" id="search-project" placeholder="">
                                </div>
                            </th>
                        -->
                </tr>
            </thead>

            <tbody class="text-xs text-center bg-white" id="oldDataBody">
                @foreach ($importrefcode as $item)
                    <tr class="hover:bg-red-100 hover:text-red-600">
                        <td>{{ $item->project_name }}</td>
                        <td>{{ $item->ref_code }}</td>
                        <td>{{ $item->group_group }}</td>
                        <td></td>
                        <!--    <td>{{ $item->project_type }}</td>  -->
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>



    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script>
        // JS ค้นหาข้อมูลเก่าเท่านั้น
        $(document).ready(function() {
            $(".filter-input").on("keypress", function(e) {
                if (e.which === 13) {
                    var project_name = $("#project_name").val().trim();
                    var ref_code = $("#ref_code").val().trim();
                    var group_group = $("#group_group").val().trim();

                    $.ajax({
                        url: '{{ route('searchRefcode') }}',
                        method: 'GET',
                        data: {
                            project_name,
                            ref_code,
                            group_group
                        },
                        success: function(response) {
                            $("#oldDataBody").empty();
                            $.each(response, function(i, item) {
                                $("#oldDataBody").append(`
                            <tr>
                                <td>${item.project_name}</td>
                                <td>${item.ref_code}</td>
                                <td>${item.group_group}</td>
                            </tr>
                        `);
                            });
                        }
                    });
                }
            });
        });
    </script>


@endsection
