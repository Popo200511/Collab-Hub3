@extends('layouts.user')
@section('title', 'GTN')
@section('content')

<!-- Tailwind -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Export to Excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<!-- search -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">



<div data-aos="fade-up" data-aos-anchor-placement="bottom-center">
    <h2 id="zoomText" class="text-center my-3 text-2xl font-bold 
               transform 
               scale-80 
               opacity-0 
               transition 
               duration-500 
               ease-out">
        New Site
    </h2>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
            AOS.init(); // เริ่มต้น AOS Animation

            // เพิ่ม Zoom-in เมื่อโหลดหน้า
            setTimeout(() => {
                let title = document.getElementById("zoomText");
                title.style.transform = "scale(1)";
                title.style.opacity = "1";
            }, 200);
        });
</script>






@if (session('success'))
<!-- Modal Popup -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-success">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="successModalLabel">สำเร็จ!</h5>
                <!--   <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button> -->
            </div>
            <div class="modal-body text-success">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        </div>
    </div>
</div>
@endif


@if (session('error'))
<!-- Modal Popup Error -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="errorModalLabel">เกิดข้อผิดพลาด!</h5>
                <!-- <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button> -->
            </div>
            <div class="modal-body text-danger">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            </div>
        </div>
    </div>
</div>
@endif

<script>
    // ให้แน่ใจว่า script ทำงานหลังจาก HTML โหลดเสร็จ
        document.addEventListener("DOMContentLoaded", function() {
            // ฟังก์ชันส่งออก Excel
            document.getElementById('exportButtonImport').addEventListener('click', function() {
                var wb = XLSX.utils.book_new();

                // สร้างตารางที่ต้องการ export (แค่หัวตาราง)
                var table = document.createElement('table');
                var thead = table.createTHead();
                var row = thead.insertRow();

                // สร้างหัวตาราง (columns)
                var th1 = row.insertCell();
                th1.innerText = "Refcode";
                var th2 = row.insertCell();
                th2.innerText = "Owner Old Ste";
                var th3 = row.insertCell();
                th3.innerText = "Site Code";
                var th4 = row.insertCell();
                th4.innerText = "Site NAME_T";
                var th5 = row.insertCell();
                th5.innerText = "Region";
                var th6 = row.insertCell();
                th6.innerText = "Province";
                var th7 = row.insertCell();
                th7.innerText = "Tower height";


                // แปลงตารางเป็น sheet และส่งออก
                var ws = XLSX.utils.table_to_sheet(table);
                XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
                XLSX.writeFile(wb, 'Template Import Refcode.csv');
            });
        });
</script>

<style>
    .dropdown-menu li {
        width: 200px;
        /* กำหนดความกว้างของแต่ละ li */
        margin: 5px auto;
        /* จัดให้ตรงกลาง */
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">

<div class="w-full">
    {{-- โครงสร้างหลัก: ทำให้เนื้อหาอยู่ตรงกลาง (ถ้าจำเป็น) และใช้ Flexbox จัดเรียงองค์ประกอบ --}}
    <div class="flex items-center justify-between h-full p-4">

        <div x-data="{ open: false }" class="relative inline-block text-left">

            {{-- ปุ่ม Dropdown (Smaller Modern Primary Button) --}}
            <button @click="open = !open" type="button" class="inline-flex items-center justify-center gap-x-1 
           bg-indigo-600 hover:bg-indigo-700 text-white 
           font-medium text-sm py-1.5 px-4 rounded-md 
           shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-300 
           transition duration-200 ease-in-out" data-aos="fade-right" data-aos-offset="100" data-aos-duration="800"
                data-aos-easing="ease-out">

                {{-- Icon: Bars (Font Awesome) --}}
                <i class="fa-solid fa-bars text-base"></i>
                Menu
            </button>

            {{-- Dropdown Content (Modern Minimalist Card) --}}
            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 transform"
                x-transition:enter-end="opacity-100 scale-100 transform"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 transform"
                x-transition:leave-end="opacity-0 scale-95 transform" class="absolute left-0 mt-3 w-64 
               bg-white rounded-xl shadow-2xl z-40 p-3 
               border border-gray-100 origin-top-left">

                {{-- Add RefCode --}}
                <a href="add" class="flex items-center space-x-3 w-full py-2 px-3 
                  text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 
                  rounded-lg transition duration-150 ease-in-out text-left">
                    {{-- Icon: Circle Plus (Font Awesome) --}}
                    <i class="fa-solid fa-circle-plus text-lg text-green-500 w-5"></i>
                    <span class="font-medium">Add RefCode</span>
                </a>

                {{-- Import RefCode --}}
                <a href="#" id="importFile" class="flex items-center space-x-3 w-full py-2 px-3 
                  text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 
                  rounded-lg transition duration-150 ease-in-out text-left">
                    {{-- Icon: File Import (Font Awesome) --}}
                    <i class="fa-solid fa-file-import text-lg text-yellow-600 w-5"></i>
                    <span class="font-medium">Import RefCode</span>
                </a>

                {{-- Divider --}}
                <div class="border-t border-gray-200 my-2"></div>

                {{-- Template Import Refcode Button (ปุ่ม Action ใน Dropdown) --}}
                <button type="submit" class="w-full flex items-center justify-center space-x-2 
                   bg-green-500 hover:bg-green-600 text-white 
                   font-medium py-2 px-4 rounded-lg 
                   transition duration-150 ease-in-out text-sm" id="exportButtonImport">
                    {{-- Icon: File Excel (Font Awesome) --}}
                    <i class="fa-solid fa-file-excel text-base"></i>
                    <span>Template Import Refcode</span>
                </button>
            </div>
        </div>

        {{-- 2. Form Import RefCode (ซ่อนอยู่) --}}
        <div id="formContainer" class="hidden">
            <form action="/import" method="POST" enctype="multipart/form-data" id="csvForm" class="flex flex-col md:flex-row items-stretch md:items-center gap-2 justify-start p-2 md:p-0 
               border border-gray-200 rounded-lg md:border-none">
                @csrf
                <input type="file" name="csv_file_add" accept=".csv" required class="block w-full md:w-60 lg:w-80 text-xs text-gray-900 
                   border border-gray-300 rounded-lg cursor-pointer bg-gray-50 
                   p-2 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 
                   file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <input type="submit" name="preview_add" value="แสดงข้อมูล SiteCode ที่ต้องการเพิ่ม" class="bg-green-600 hover:bg-green-700 text-white font-semibold 
                   py-1.5 px-3 rounded-lg shadow-md transition duration-150 cursor-pointer 
                   w-full md:w-auto text-xs h-auto">
            </form>
        </div>


        {{-- 3. Search and Export Group --}}
        <div class="flex items-center gap-3">

            {{-- Search Input Group --}}
            <form class="flex items-center gap-2" method="GET" action="/">

                <div data-aos="fade-right" data-aos-offset="300" data-aos-easing="ease-in-sine">
                    {{-- Search Input (พร้อม Icon Inset) --}}
                    <div class="relative">
                        <input type="text" name="search" id="search" placeholder="" aria-label="Search" class="border border-gray-300 pl-10 pr-4 py-1.5 rounded-lg 
                           focus:outline-none focus:ring-2 focus:ring-indigo-400 
                           w-32 sm:w-48 text-sm transition duration-200">

                        {{-- Icon: Magnifying Glass (Font Awesome) --}}
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                    </div>
                </div>

                {{-- Export Button --}}
                <div data-aos="fade-left" data-aos-anchor="#example-anchor" data-aos-offset="500"
                    data-aos-duration="500">
                    <button type="button" id="exportButton" class="flex items-center space-x-1 
                       bg-green-500 hover:bg-green-600 text-white 
                       font-medium py-1.5 px-3 rounded-lg 
                       transition duration-150 ease-in-out text-sm shadow-md">

                        {{-- Icon: File Excel (Font Awesome) --}}
                        <i class="fa-solid fa-file-excel text-base"></i>
                        <span>Export Excel</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
        width: 130px;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
            // ถ้ามี session('success') ให้เปิด Modal Success
            @if (session('success'))
                var successModal = new bootstrap.Modal(document.getElementById('successModal'), {
                    keyboard: false
                });
                successModal.show(); // แสดง Modal สำหรับ success

                // ปิด Modal หลังจาก 3 วินาที (3000ms)
                setTimeout(function() {
                    successModal.hide(); // ปิด Modal
                }, 3000);
            @endif

            // ถ้ามี session('error') ให้เปิด Modal Error
            @if (session('error'))
                var errorModal = new bootstrap.Modal(document.getElementById('errorModal'), {
                    keyboard: false
                });
                errorModal.show(); // แสดง Modal สำหรับ error

                // ปิด Modal หลังจาก 3 วินาที (3000ms)
                setTimeout(function() {
                    errorModal.hide(); // ปิด Modal
                }, 3000);
            @endif
        });
</script>

<script>
    document.getElementById('importFile').addEventListener('click', function(event) {
            event.preventDefault(); // ป้องกันลิงก์โหลดหน้าใหม่
            let formContainer = document.getElementById('formContainer');

            // แสดงฟอร์มถ้ายังไม่แสดง หรือซ่อนถ้ากดอีกครั้ง
            if (formContainer.style.display === 'none' || formContainer.style.display === '') {
                formContainer.style.display = 'block';
            } else {
                formContainer.style.display = 'none';
            }
        });
</script>

<script>
    //ฟังก์ชั่น search
        $(document).ready(function() {
            $('#search').on('keyup', function() {
                var query = $(this).val().toLowerCase(); // ทำให้ query เป็นตัวพิมพ์เล็กทั้งหมด
                $('#table tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(query) > -1);
                });
            });
        });
</script>


<style>
    .input-group {
        position: relative;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        width: 25%;
    }

    .table-container {
        width: 101%;
        height: 578px;
        max-height: 578px;
        overflow-x: auto;
        overflow-y: auto;
    }

    .row-hover td {
        background-color: #f5c7c7;
    }





    .table-container table {
        border-collapse: collapse;
    }

    .table-container td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .table-container th {
        /*เส้นขอบ colum */
        padding: 8px;
        text-align: center;
        white-space: nowrap;
        position: sticky;
        top: 0px;
        text-align: center;
        overflow: hidden;
        text-overflow: ellipsis;

    }

    .table-container th:nth-child(1),
    .table-container td:nth-child(1) {
        position: sticky;
        left: 0px;
        width: 50px;
        min-width: 50px;

    }

    .table-container th:nth-child(2),
    .table-container td:nth-child(2) {
        position: sticky;
        left: 50px;
        width: 100px;
        min-width: 100px;
    }

    .table-container th:nth-child(3),
    .table-container td:nth-child(3) {
        position: sticky;
        left: 150px;
        width: 100px;
        min-width: 100px;
    }

    .table-container th:nth-child(4),
    .table-container td:nth-child(4) {
        position: sticky;
        left: 250px;
        width: 115px;
        min-width: 115px;
    }

    .table-container th:nth-child(5),
    .table-container td:nth-child(5) {
        position: sticky;
        left: 365px;
        width: 120px;
        min-width: 120px;
    }

    .table-container td:nth-child(1),
    .table-container td:nth-child(2),
    .table-container td:nth-child(3),
    .table-container td:nth-child(4),
    .table-container td:nth-child(5) {}

    .table-container th:nth-child(1),
    .table-container th:nth-child(2),
    .table-container th:nth-child(3),
    .table-container th:nth-child(4),
    .table-container th:nth-child(5) {
        z-index: 5;
        background: #d2cbc8;
    }

    .status-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 60px;
        /* ระยะห่างระหว่างแต่ละ <h5> */
    }

    .indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-left: 5px;
        /* เพิ่มระยะห่างระหว่างข้อความและตัวบ่งชี้ */
    }

    .has-data {
        background-color: rgb(0, 156, 0);
    }

    .no-data {
        background-color: rgb(255, 0, 0);
    }
</style>

<div data-aos="fade-up" data-aos-anchor-placement="top-bottom">
    <div class="table-container overflow-x-auto">
        <table class="table-fixed w-max divide-y divide-gray-200 border border-gray-300" id="table">
            <thead class="bg-gray-50">
                <tr class="text-center text-[12px] h-10">
                    <th scope="col" class="bg-[#d2cbc8]"></th>

                    <th scope=" col" class="bg-[#d2cbc8] text-black ">RefCode</th>
                    <th scope="col" class="bg-[#d2cbc8] text-black">Owner Old Ste</th>
                    <th scope="col" class="bg-[#d2cbc8] text-black">Site Code</th>
                    <th scope="col" class="bg-[#d2cbc8] text-black">Site NAME_T</th>
                    <!--   <th scope="col">Plan Type</th>  -->
                    <th scope="col" class="bg-[#99FFCC] text-black">Region</th>
                    <th scope="col" class="bg-[#99FFCC] text-black">Province</th>
                    <!--   <th scope="col">Site Type</th>  -->
                    <th scope="col" class="bg-[#99FFCC] text-black p-2">Tower height</th>



                    <!-- INVOICE -->
                    <th scope="col" class="bg-[#eaff01] text-black p-2">Quotation_IN</th>
                    <th scope="col" class="bg-[#eaff01] text-black p-2">PO_No_IN</th>

                    <th scope="col" class="bg-[#eaff01] text-black p-2">PO_Amount_IN</th>
                    <th scope="col" class="bg-[#ff0000] text-black p-2">Banlace_IN</th>

                    <th scope="col" class="bg-[#eaff01] text-black p-2">Design_Amount</th>
                    <th scope="col" class="bg-[#eaff01] text-black p-2">Invoice1_IN</th>
                    <th scope="col" class="bg-[#eaff01] text-black p-2">Amount1_IN</th>
                    <th scope="col" class="bg-[#eaff01] text-black p-2">Invoice2_IN</th>
                    <th scope="col" class="bg-[#eaff01] text-black p-2">Amount2_IN</th>

                    <th scope="col" class="bg-[#eaff01] text-black p-2">Construction_Amount</th>
                    <th scope="col" class="bg-[#eaff01] text-black p-2">Invoice1_IN</th>
                    <th scope="col" class="bg-[#eaff01] text-black p-2">Amount1_IN</th>
                    <th scope="col" class="bg-[#eaff01] text-black p-2">Invoice2_IN</th>
                    <th scope="col" class="bg-[#eaff01] text-black p-2">Amount2_IN</th>


                    <!-- SAQ -->
                    <th scope="col" class="bg-[#D1E9F6] text-black p-2">Assigned SubC Survey SAQ</th>
                    <th scope="col" class="bg-[#D1E9F6] text-black p-2">SubName SAQ</th>

                    <th scope="col" class="bg-[#D1E9F6] text-black p-2">Plan Survey SAQ</th>
                    <th scope="col" class="bg-[#D1E9F6] text-black p-2">Actual Survey SAQ</th>

                    <th scope="col" class="bg-[#D1E9F6] text-black p-2">Quo No SAQ</th>
                    <th scope="col" class="bg-[#D1E9F6] text-black p-2">PR Price SAQ</th>
                    <th scope="col" class="bg-[#D1E9F6] text-black p-2">Accept PR Date SAQ</th>

                    <th scope="col" class="bg-[#D1E9F6] text-black p-2">WO No SAQ</th>
                    <th scope="col" class="bg-[#D1E9F6] text-black p-2">WO Price SAQ</th>
                    <th scope="col" class="bg-[#ff0000] text-black p-2">Banlace SAQ</th>

                    <th scope="col" class="bg-[#D1E9F6] text-black p-2">Accept 1st SAQ</th>
                    <th scope="col" class="bg-[#D1E9F6] text-black p-2">Mail</th>
                    <th scope="col" class="bg-[#D1E9F6] text-black p-2">ERP</th>

                    <th scope="col" class="bg-[#D1E9F6] text-black p-2">Accept 2st SAQ</th>
                    <th scope="col" class="bg-[#D1E9F6] text-black p-2">Mail</th>
                    <th scope="col" class="bg-[#D1E9F6] text-black p-2">ERP</th>

                    <th scope="col" class="bg-[#D1E9F6] text-black p-2">Accept 3st SAQ</th>
                    <th scope="col" class="bg-[#D1E9F6] text-black p-2">Mail</th>
                    <th scope="col" class="bg-[#D1E9F6] text-black p-2">ERP</th>
                    <th scope="col" class="bg-[#D1E9F6] text-black p-2">Accept 4st SAQ</th>
                    <th scope="col" class="bg-[#D1E9F6] text-black p-2">Mail</th>
                    <th scope="col" class="bg-[#D1E9F6] text-black p-2">ERP</th>

                    <!-- CR -->
                    <th scope="col" class="bg-[#29b6f6] text-black p-2">Assigned SubC CR</th>
                    <th scope="col" class="bg-[#29b6f6] text-black p-2">SubName CR</th>

                    <th scope="col" class="bg-[#29b6f6] text-black p-2">Plan CR</th>
                    <th scope="col" class="bg-[#29b6f6] text-black p-2">Actual CR</th>

                    <th scope="col" class="bg-[#29b6f6] text-black p-2">Quo No CR</th>
                    <th scope="col" class="bg-[#29b6f6] text-black p-2">PR Price CR</th>
                    <th scope="col" class="bg-[#29b6f6] text-black p-2">Accept PR Date CR</th>

                    <th scope="col" class="bg-[#29b6f6] text-black p-2">WO No CR</th>
                    <th scope="col" class="bg-[#29b6f6] text-black p-2">WO Price CR</th>
                    <th scope="col" class="bg-[#ff0000] text-black p-2">Banlace CR</th>


                    <th scope="col" class="bg-[#29b6f6] text-black p-2">Accept 1st CR</th>
                    <th scope="col" class="bg-[#29b6f6] text-black p-2">Mail</th>
                    <th scope="col" class="bg-[#29b6f6] text-black p-2">ERP</th>

                    <th scope="col" class="bg-[#29b6f6] text-black p-2">Accept 2st CR</th>
                    <th scope="col" class="bg-[#29b6f6] text-black p-2">Mail</th>
                    <th scope="col" class="bg-[#29b6f6] text-black p-2">ERP</th>

                    <th scope="col" class="bg-[#29b6f6] text-black p-2">Accept 3st CR</th>
                    <th scope="col" class="bg-[#29b6f6] text-black p-2">Mail</th>
                    <th scope="col" class="bg-[#29b6f6] text-black p-2">ERP</th>

                    <th scope="col" class="bg-[#29b6f6] text-black p-2">Accept 4st CR</th>
                    <th scope="col" class="bg-[#29b6f6] text-black p-2">Mail</th>
                    <th scope="col" class="bg-[#29b6f6] text-black p-2">ERP</th>

                    <!-- TSSR -->
                    <th scope="col" class="bg-[#fff176] text-black p-2">Assigned SubC TSSR</th>
                    <th scope="col" class="bg-[#fff176] text-black p-2">SubName TSSR</th>

                    <th scope="col" class="bg-[#fff176] text-black p-2">Plan TSSR</th>
                    <th scope="col" class="bg-[#fff176] text-black p-2">Actual TSSR</th>

                    <th scope="col" class="bg-[#fff176] text-black p-2">Quo No TSSR</th>
                    <th scope="col" class="bg-[#fff176] text-black p-2">PR Price TSSR</th>
                    <th scope="col" class="bg-[#fff176] text-black p-2">Accept PR Date TSSR</th>

                    <th scope="col" class="bg-[#fff176] text-black p-2">WO No TSSR</th>
                    <th scope="col" class="bg-[#fff176] text-black p-2">WO Price TSSR</th>
                    <th scope="col" class="bg-[#ff0000] text-black p-2">Banlace TSSR</th>


                    <th scope="col" class="bg-[#fff176] text-black p-2">Accept 1st TSSR</th>
                    <th scope="col" class="bg-[#fff176] text-black p-2">Mail</th>
                    <th scope="col" class="bg-[#fff176] text-black p-2">ERP</th>

                    <th scope="col" class="bg-[#fff176] text-black p-2">Accept 2st TSSR</th>
                    <th scope="col" class="bg-[#fff176] text-black p-2">Mail</th>
                    <th scope="col" class="bg-[#fff176] text-black p-2">ERP</th>

                    <th scope="col" class="bg-[#fff176] text-black p-2">Accept 3st TSSR</th>
                    <th scope="col" class="bg-[#fff176] text-black p-2">Mail</th>
                    <th scope="col" class="bg-[#fff176] text-black p-2">ERP</th>

                    <th scope="col" class="bg-[#fff176] text-black p-2">Accept 4st TSSR</th>
                    <th scope="col" class="bg-[#fff176] text-black p-2">Mail</th>
                    <th scope="col" class="bg-[#fff176] text-black p-2">ERP</th>



                    <!-- CivilWork -->
                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Plan Civil FoundationAssign</th>
                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Actual Civil WorkFoundation</th>

                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Plan Civil WorkTower</th>
                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Actual Civil WorkTower</th>

                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Plan Installation Rectifier</th>
                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Actual Installation Rectifier</th>

                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Plan AC Power</th>
                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Actual AC Power</th>

                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Plan AC Meter</th>
                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Actual AC Meter</th>

                    <th scope="col" class="bg-[#00DFA2] text-black p-2">PAT</th>
                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Def.PAT</th>
                    <th scope="col" class="bg-[#00DFA2] text-black p-2">FAT</th>

                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Assigned CivilWork</th>
                    <th scope="col" class="bg-[#00DFA2] text-black p-2">SubName CivilWork</th>

                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Plan CivilWork</th>
                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Actual CivilWork</th>

                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Quo No CivilWork</th>
                    <th scope="col" class="bg-[#00DFA2] text-black p-2">PR Price CivilWork</th>
                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Accept PR Date CivilWork</th>

                    <th scope="col" class="bg-[#00DFA2] text-black p-2">WO No CivilWork</th>
                    <th scope="col" class="bg-[#00DFA2] text-black p-2">WO Price CivilWork</th>
                    <th scope="col" class="bg-[#ff0000] text-black p-2">Banlace CivilWork</th>


                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Accept 1st CivilWork</th>
                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Mail</th>
                    <th scope="col" class="bg-[#00DFA2] text-black p-2">ERP</th>

                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Accept 2st CivilWork</th>
                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Mail</th>
                    <th scope="col" class="bg-[#00DFA2] text-black p-2">ERP</th>

                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Accept 3st CivilWork</th>
                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Mail</th>
                    <th scope="col" class="bg-[#00DFA2] text-black p-2">ERP</th>

                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Accept 4st CivilWork</th>
                    <th scope="col" class="bg-[#00DFA2] text-black p-2">Mail</th>
                    <th scope="col" class="bg-[#00DFA2] text-black p-2">ERP</th>

                    <!-- ADDITIONAL -->
                    <!-- <th scope="col" style="background-color: #ddd">Additional</th> -->
                    <th scope="col" class="bg-[#ddd] text-black p-2">Pile Supplier</th>
                    <th scope="col" class="bg-[#ddd] text-black p-2">Price</th>
                    <th scope="col" class="bg-[#ddd] text-black p-2">Pile Supplier Accept Date</th>
                    <th scope="col" class="bg-[#ddd] text-black p-2">WO No.</th>
                    <th scope="col" class="bg-[#ddd] text-black p-2">Accept 1</th>
                    <th scope="col" class="bg-[#ddd] text-black p-2">Accept 2</th>
                    <th scope="col" class="bg-[#ddd] text-black p-2">Accept 3</th>
                    <th scope="col" class="bg-[#ddd] text-black p-2">Sub Extra Work </th>
                    <th scope="col" class="bg-[#ddd] text-black p-2">Sub Extra Work Price</th>
                    <th scope="col" class="bg-[#ddd] text-black p-2">Extra work Accept Date</th>
                    <th scope="col" class="bg-[#ddd] text-black p-2">Build Permit Price</th>
                    <th scope="col" class="bg-[#ddd] text-black p-2">Payment To</th>
                    <th scope="col" class="bg-[#ddd] text-black p-2">Payment Date</th>


                </tr>

            </thead>
            <tbody>
                @foreach ($data as $item)
                <tr style="font-size: 10px; text-align:center " onmouseover="this.classList.add('row-hover')"
                    onmouseout="this.classList.remove('row-hover')">

                    <td class="bg-[#fff]"> <a href=" {{ route('edit', $item->id) }}"><i class="fa-solid fa-pencil fa-lg"
                                style="color: #74C0FC;"></i>
                    </td>


                    <td class="bg-[#fff]">{{ $item->RefCode }}</td>

                    <td class="bg-[#fff]">{{ $item->OwnerOldSte }}</td>
                    <td class="bg-[#fff]">{{ $item->SiteCode }}</td>
                    <td class="bg-[#fff]">{{ $item->SiteNAME_T }}</td>
                    <!--    <td>{{ $item->PlanType }}</td>  -->

                    <td class="bg-[#fff]">
                        @foreach ($areas as $region)
                        @if ($region->Region_id == $item->Region_id)
                        {{ $region->Region_name }}
                        @endif
                        @endforeach
                    </td>

                    <td class="bg-[#fff]">{{ $item->Province }}</td>
                    <!--  <td>{{ $item->SiteType }}</td> -->

                    <td class="bg-[#fff]">{{ $item->Towerheight }}</td>



                    <!-- INVOICE -->
                    <td class="bg-[#fff]">{{ $item->Quotation_IN }}</td>
                    <td class="bg-[#fff]">{{ $item->PO_No_IN }}</td>

                    <td class="bg-[#fff]">{{ $item->PO_Amount_IN }}</td>

                    <td class="bg-[#fff]">
                        @php
                        $wo = $item->PO_Amount_IN;
                        $banlace_IN = $item->Banlace_IN;
                        @endphp
                        @if (empty($wo))
                        @elseif ($banlace_IN == 0)
                        <span style="color: green;">{{ number_format($banlace_IN, 2, '.', ',') }}</span>
                        <!-- แสดง Banlace_SAQ เป็นสีเขียวเมื่อเป็น 0 -->
                        @else
                        {{ $item->Banlace_IN }}
                        @endif
                    </td>

                    <!-- CD -->
                    <td class="bg-[#fff]">{{ $item->Design_Amount }}</td>
                    <td class="bg-[#fff]">{{ $item->Invoice1_IN }}</td>

                    <td class="bg-[#fff]">
                        {{ $item->Amount1_IN }}
                        @if (empty($item->Amount1_IN))
                        <span class="indicator no-data" title="No Data"></span>
                        @endif
                    </td>

                    <td class="bg-[#fff]">{{ $item->Invoice2_IN }}</td>

                    <td class="bg-[#fff]">
                        {{ $item->Amount2_IN }}
                        @if (empty($item->Amount2_IN))
                        <span class="indicator no-data" title="No Data"></span>
                        @endif
                    </td>

                    <!-- CC -->

                    <td class="bg-[#fff]">{{ $item->Construction_Amount }}</td>
                    <td class="bg-[#fff]">{{ $item->Invoice1_CC }}</td>

                    <td class="bg-[#fff]">
                        {{ $item->Amount1_CC }}
                        @if (empty($item->Amount1_CC))
                        <span class="indicator no-data" title="No Data"></span>
                        @endif
                    </td class="bg-[#fff]">

                    <td class="bg-[#fff]">{{ $item->Invoice2_CC }}</td>

                    <td class="bg-[#fff]">
                        {{ $item->Amount2_CC }}
                        @if (empty($item->Amount2_CC))
                        <span class="indicator no-data" title="No Data"></span>
                        @endif
                    </td>





                    <!-- SAQ  -->
                    <td class="bg-[#fff]">{{ $item->AssignedSubCSurveySAQ }}</td>
                    <td class="bg-[#fff]">{{ $item->SubName_SAQ }}</td>

                    <td class="bg-[#fff]">{{ $item->PlanSurveySAQ }}</td>
                    <td class="bg-[#fff]">{{ $item->ActualSurveySAQ }}</td>

                    <td class="bg-[#fff]">{{ $item->Quo_No_SAQ }}</td>
                    <td class="bg-[#fff]">{{ $item->PR_Price_SAQ }}</td>
                    <td class="bg-[#fff]">{{ $item->Accept_PR_Date_SAQ }}</td>
                    <td class="bg-[#fff]">{{ $item->WO_No_SAQ }}</td>
                    <td class="bg-[#fff]">{{ $item->WO_Price_SAQ }}</td>

                    <td class="bg-[#fff]">
                        @php
                        $wo = $item->WO_Price_SAQ;
                        $banlace_SAQ = $item->Banlace_SAQ;
                        @endphp
                        @if (empty($wo))
                        @elseif ($banlace_SAQ == 0)
                        <span style="color: green;">{{ number_format($banlace_SAQ, 2, '.', ',') }}</span>
                        <!-- แสดง Banlace_SAQ เป็นสีเขียวเมื่อเป็น 0 -->
                        @else
                        {{ $item->Banlace_SAQ }}
                        @endif
                    </td>

                    <td class="bg-[#fff]">
                        {{ $item->Accept_1st_SAQ }}
                        @if (empty($item->Accept_1st_SAQ))
                        <span class="indicator no-data" title="No Data"></span>
                        @endif
                    </td>
                    <td class="bg-[#fff]">{{ $item->Mail_1st_SAQ }}</td>
                    <td class="bg-[#fff]">{{ $item->ERP_1st_SAQ }}</td>

                    <td class="bg-[#fff]">
                        {{ $item->Accept_2nd_SAQ }}
                        @if (empty($item->Accept_2nd_SAQ))
                        <span class="indicator no-data" title="No Data"></span>
                        @endif
                    </td>
                    <td class="bg-[#fff]">{{ $item->Mail_2nd_SAQ }}</td>
                    <td class="bg-[#fff]">{{ $item->ERP_2nd_SAQ }}</td>

                    <td class="bg-[#fff]">
                        {{ $item->Accept_3rd_SAQ }}
                        @if (empty($item->Accept_3rd_SAQ))
                        <span class="indicator no-data" title="No Data"></span>
                        @endif
                    </td>
                    <td class="bg-[#fff]">{{ $item->Mail_3rd_SAQ }}</td>
                    <td class="bg-[#fff]">{{ $item->ERP_3rd_SAQ }}</td>

                    <td class="bg-[#fff]">
                        {{ $item->Accept_4th_SAQ }}
                        @if (empty($item->Accept_4th_SAQ))
                        <span class="indicator no-data" title="No Data"></span>
                        @endif
                    </td>
                    <td class="bg-[#fff]">{{ $item->Mail_4th_SAQ }}</td>
                    <td class="bg-[#fff]">{{ $item->ERP_4th_SAQ }}</td>




                    <!-- CR  -->
                    <td class="bg-[#fff]">{{ $item->AssignedSubCCR }}</td>
                    <td class="bg-[#fff]">{{ $item->SubName_CR }}</td>

                    <td class="bg-[#fff]">{{ $item->PlanCR }}</td>
                    <td class="bg-[#fff]">{{ $item->ActualCR }}</td>

                    <td class="bg-[#fff]">{{ $item->Quo_No_CR }}</td>
                    <td class="bg-[#fff]">{{ $item->PR_Price_CR }}</td>
                    <td class="bg-[#fff]">{{ $item->Accept_PR_Date_CR }}</td>

                    <td class="bg-[#fff]">{{ $item->WO_No_CR }}</td>
                    <td class="bg-[#fff]">{{ $item->WO_Price_CR }}</td>

                    <td class="bg-[#fff]">
                        @php
                        $wo = $item->WO_Price_CR;
                        $banlace_CR = $item->Banlace_CR; // ดึงค่า Banlace_SAQ
                        @endphp
                        @if (empty($wo))
                        @elseif ($banlace_CR == 0)
                        <span style="color: green;">{{ number_format($banlace_CR, 2, '.', ',') }}</span>
                        <!-- แสดง Banlace_SAQ เป็นสีเขียวเมื่อเป็น 0 -->
                        @else
                        {{ $item->Banlace_CR }}
                        @endif
                    </td>


                    <td class="bg-[#fff]">
                        {{ $item->Accept_1st_CR }}
                        @if (empty($item->Accept_1st_CR))
                        <span class="indicator no-data" title="No Data"></span>
                        @endif
                    </td>
                    <td class="bg-[#fff]">{{ $item->Mail_1st_CR }}</td>
                    <td class="bg-[#fff]">{{ $item->ERP_1st_CR }}</td>

                    <td class="bg-[#fff]">
                        {{ $item->Accept_2nd_CR }}
                        @if (empty($item->Accept_2nd_CR))
                        <span class="indicator no-data" title="No Data"></span>
                        @endif
                    </td>
                    <td class="bg-[#fff]">{{ $item->Mail_2nd_CR }}</td>
                    <td class="bg-[#fff]">{{ $item->ERP_2nd_CR }}</td>

                    <td class="bg-[#fff]">
                        {{ $item->Accept_3rd_CR }}
                        @if (empty($item->Accept_3rd_CR))
                        <span class="indicator no-data" title="No Data"></span>
                        @endif
                    </td>
                    <td class="bg-[#fff]">{{ $item->Mail_3rd_CR }}</td>
                    <td class="bg-[#fff]">{{ $item->ERP_3rd_CR }}</td>

                    <td class="bg-[#fff]">
                        {{ $item->Accept_4th_CR }}
                        @if (empty($item->Accept_4th_CR))
                        <span class="indicator no-data" title="No Data"></span>
                        @endif
                    </td>
                    <td class="bg-[#fff]">{{ $item->Mail_4th_CR }}</td>
                    <td class="bg-[#fff]">{{ $item->ERP_4th_CR }}</td>


                    <!-- TSSR  -->
                    <td class="bg-[#fff]">{{ $item->AssignedSubCTSSR }}</td>
                    <td class="bg-[#fff]">{{ $item->SubName_TSSR }}</td>

                    <td class="bg-[#fff]">{{ $item->PlanTSSR }}</td>
                    <td class="bg-[#fff]">{{ $item->ActualTSSR }}</td>

                    <td class="bg-[#fff]">{{ $item->Quo_No_TSSR }}</td>
                    <td class="bg-[#fff]">{{ $item->PR_Price_TSSR }}</td>
                    <td class="bg-[#fff]">{{ $item->Accept_PR_Date_TSSR }}</td>

                    <td class="bg-[#fff]">{{ $item->WO_No_TSSR }}</td>
                    <td class="bg-[#fff]">{{ $item->WO_Price_TSSR }}</td>

                    <td class="bg-[#fff]">
                        @php
                        $wo = $item->WO_Price_TSSR;
                        $banlace_TSSR = $item->Banlace_TSSR;
                        @endphp
                        @if (empty($wo))
                        @elseif ($banlace_TSSR == 0)
                        <span style="color: green;">{{ number_format($banlace_TSSR, 2, '.', ',') }}</span>
                        <!-- แสดง Banlace_SAQ เป็นสีเขียวเมื่อเป็น 0 -->
                        @else
                        {{ $item->Banlace_TSSR }}
                        @endif
                    </td>

                    <td class="bg-[#fff]">
                        {{ $item->Accept_1st_TSSR }}
                        @if (empty($item->Accept_1st_TSSR))
                        <span class="indicator no-data" title="No Data"></span>
                        @endif
                    </td>
                    <td class="bg-[#fff]">{{ $item->Mail_1st_TSSR }}</td>
                    <td class="bg-[#fff]">{{ $item->ERP_1st_TSSR }}</td>


                    <td class="bg-[#fff]">
                        {{ $item->Accept_2nd_TSSR }}
                        @if (empty($item->Accept_2nd_TSSR))
                        <span class="indicator no-data" title="No Data"></span>
                        @endif
                    </td>
                    <td class="bg-[#fff]">{{ $item->Mail_2nd_TSSR }}</td>
                    <td class="bg-[#fff]">{{ $item->ERP_2nd_TSSR }}</td>


                    <td class="bg-[#fff]">
                        {{ $item->Accept_3rd_TSSR }}
                        @if (empty($item->Accept_3rd_TSSR))
                        <span class="indicator no-data" title="No Data"></span>
                        @endif
                    </td>
                    <td class="bg-[#fff]">{{ $item->Mail_3rd_TSSR }}</td>
                    <td class="bg-[#fff]">{{ $item->ERP_3rd_TSSR }}</td>


                    <td class="bg-[#fff]">
                        {{ $item->Accept_4th_TSSR }}
                        @if (empty($item->Accept_4th_TSSR))
                        <span class="indicator no-data" title="No Data"></span>
                        @endif
                    </td>
                    <td class="bg-[#fff]">{{ $item->Mail_4th_TSSR }}</td>
                    <td class="bg-[#fff]">{{ $item->ERP_4th_TSSR }}</td>



                    <!-- CivilWork  -->
                    <td class="bg-[#fff]">{{ $item->AssignSubCivilfoundation }}</td>
                    <td class="bg-[#fff]">{{ $item->PlanCivilWorkFoundation }}</td>

                    <td class="bg-[#fff]">{{ $item->ActualCivilWorkTower }}</td>
                    <td class="bg-[#fff]">{{ $item->AssignCivilWorkTower }}</td>

                    <td class="bg-[#fff]">{{ $item->PlanInstallationRectifier }}</td>
                    <td class="bg-[#fff]">{{ $item->ActualInstallationRectifier }}</td>

                    <td class="bg-[#fff]">{{ $item->PlanACPower }}</td>
                    <td class="bg-[#fff]">{{ $item->ActualACPower }}</td>

                    <td class="bg-[#fff]">{{ $item->PlanACMeter }}</td>
                    <td class="bg-[#fff]">{{ $item->ActualACMeter }}</td>

                    <td class="bg-[#fff]">{{ $item->PAT }}</td>
                    <td class="bg-[#fff]">{{ $item->DefPAT }}</td>
                    <td class="bg-[#fff]">{{ $item->FAT }}</td>

                    <td class="bg-[#fff]">{{ $item->Assigned_CivilWork }}</td>
                    <td class="bg-[#fff]">{{ $item->SubName_CivilWork }}</td>

                    <td class="bg-[#fff]">{{ $item->Plan_CivilWork }}</td>
                    <td class="bg-[#fff]">{{ $item->Actual_CivilWork }}</td>

                    <td class="bg-[#fff]">{{ $item->Quo_No_CivilWork }}</td>
                    <td class="bg-[#fff]">{{ $item->PR_Price_CivilWork }}</td>
                    <td class="bg-[#fff]">{{ $item->Accept_PR_Date_CivilWork }}</td>

                    <td class="bg-[#fff]">{{ $item->WO_No_CivilWork }}</td>
                    <td class="bg-[#fff]">{{ $item->WO_Price_CivilWork }}</td>

                    <td class="bg-[#fff]">
                        @php
                        $wo = $item->WO_Price_CivilWork;
                        $banlace_CivilWork = $item->Banlace_CivilWork;
                        @endphp
                        @if (empty($wo))
                        @elseif ($banlace_CivilWork == 0)
                        <span style="color: green;">{{ number_format($banlace_CivilWork, 2, '.', ',') }}</span>
                        <!-- แสดง Banlace_SAQ เป็นสีเขียวเมื่อเป็น 0 -->
                        @else
                        {{ $banlace_CivilWork }}
                        @endif
                    </td>

                    <td class="bg-[#fff]">
                        {{ $item->Accept_1st_CivilWork }}
                        @if (empty($item->Accept_1st_CivilWork))
                        <span class="indicator no-data" title="No Data"></span>
                        @endif
                    </td>
                    <td class="bg-[#fff]">{{ $item->Mail_1st_CivilWork }}</td>
                    <td class="bg-[#fff]">{{ $item->ERP_1st_CivilWork }}</td>

                    <td class="bg-[#fff]">
                        {{ $item->Accept_2nd_CivilWork }}
                        @if (empty($item->Accept_2nd_CivilWork))
                        <span class="indicator no-data" title="No Data"></span>
                        @endif
                    </td>
                    <td class="bg-[#fff]">{{ $item->Mail_2nd_CivilWork }}</td>
                    <td class="bg-[#fff]">{{ $item->ERP_2nd_CivilWork }}</td>

                    <td class="bg-[#fff]">
                        {{ $item->Accept_3rd_CivilWork }}
                        @if (empty($item->Accept_3rd_CivilWork))
                        <span class="indicator no-data" title="No Data"></span>
                        @endif
                    </td>
                    <td class="bg-[#fff]">{{ $item->Mail_3rd_CivilWork }}</td>
                    <td class="bg-[#fff]">{{ $item->ERP_3rd_CivilWork }}</td>

                    <td class="bg-[#fff]">
                        {{ $item->Accept_4th_CivilWork }}
                        @if (empty($item->Accept_4th_CivilWork))
                        <span class="indicator no-data" title="No Data"></span>
                        @endif
                    </td>
                    <td class="bg-[#fff]">{{ $item->Mail_4th_CivilWork }}</td>
                    <td class="bg-[#fff]">{{ $item->ERP_4th_CivilWork }}</td>

                    <!-- ADDITIONAL -->
                    <!--   <td>{{ $item->id_add }}</td>   -->
                    <td class="bg-[#fff]">{{ $item->pile_supplier }}</td>
                    <td class="bg-[#fff]">{{ $item->price }}</td>
                    <td class="bg-[#fff]">{{ $item->pile_supplier_accept_date }}</td>
                    <td class="bg-[#fff]">{{ $item->wo_no }}</td>
                    <td class="bg-[#fff]">{{ $item->accept_1 }}</td>
                    <td class="bg-[#fff]">{{ $item->accept_2 }}</td>
                    <td class="bg-[#fff]">{{ $item->accept_3 }}</td>
                    <td class="bg-[#fff]">{{ $item->sub_extra_work }}</td>
                    <td class="bg-[#fff]">{{ $item->sub_extra_work_price }}</td>
                    <td class="bg-[#fff]">{{ $item->extra_work_accept_date }}</td>
                    <td class="bg-[#fff]">{{ $item->build_permit }}</td>
                    <td class="bg-[#fff]">{{ $item->payment_to }}</td>
                    <td class="bg-[#fff]">{{ $item->payment_date }}</td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        // ฟังก์ชันส่งออก export
        document.getElementById('exportButton').addEventListener('click', function() {
            var wb = XLSX.utils.book_new();
            var ws = XLSX.utils.table_to_sheet(document.getElementById('table'));
            XLSX.utils.book_append_sheet(wb, ws, 'x');
            XLSX.writeFile(wb, 'New_Site.xlsx');
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
                AOS.init(); // เริ่มต้น AOS Animation
            });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

    @endsection