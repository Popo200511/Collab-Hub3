@extends('layouts.app')
@section('title', '98_TRUE Project Database')
@section('content')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <div>
        <h2 id="zoomText" class="text-center my-3 text-2xl font-bold"
            style="transform: scale(0.8); opacity: 0; transition: transform 0.5s ease-out, opacity 0.5s ease-out;">
            98_TRUE Project Database
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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">

    <div class="container-fluid  custom-container"> <!-- Add custom-container class -->
        <div class="row align-items-center h-100"> <!-- Add h-100 to make row take full height -->
            <div class="col-12 d-flex justify-content-end"> <!-- Add h-100 to the column -->

                <!-- ฟอร์มจะถูกซ่อนตอนแรก -->
                <div id="formContainer" class="container" style="display: none;">
                    <form action="/import" method="POST" enctype="multipart/form-data" id="csvForm"
                        class="d-flex flex-column flex-sm-row align-items-center gap-3 justify-content-start">
                        @csrf
                        <input type="file" class="form-control" name="csv_file_add" accept=".csv" required
                            style="width: 400px;">
                        <input type="submit" class="btn btn-success" name="preview_add"
                            value="แสดงข้อมูล SiteCode ที่ต้องการเพิ่ม" style="width: 250px; height: 37px;">
                    </form>
                </div>


                <div class="d-flex align-items-center"> <!-- Keep the search and export buttons together -->
                    <form class="d-flex ms-2">
                        <!-- Add margin-start to create space -->
                        <div data-aos="fade-right" data-aos-offset="300" data-aos-easing="ease-in-sine">
                            <input type="text"
                                class="form-control fixed-width-input border border-gray-300 px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                                name="search" id="search" placeholder="Search" aria-label="Search">
                        </div>

                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                AOS.init(); // เริ่มต้น AOS Animation
                            });
                        </script>

                        <div data-aos="fade-left" data-aos-anchor="#example-anchor" data-aos-offset="500"
                            data-aos-duration="500">
                            <button type="submit" class="btn btn-outline-success ms-2" id="exportButton"
                                style="margin-right: 30px;">
                                Export to Excel
                            </button>
                        </div>


                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                AOS.init(); // เริ่มต้น AOS Animation
                            });
                        </script>


                    </form>
                </div>
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
            width: 125px;
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

    <script>
        // ฟังก์ชันส่งออก export
        document.getElementById('exportButton').addEventListener('click', function() {
            var wb = XLSX.utils.book_new();
            var ws = XLSX.utils.table_to_sheet(document.getElementById('table'));
            XLSX.utils.book_append_sheet(wb, ws, 'x');
            XLSX.writeFile(wb, 'New_Site.xlsx');
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
            width: 98%;
            max-height: 500px;
            overflow-x: auto;
            overflow-y: auto;
        }


        .table-container table {
            height: 100%;
            border-collapse: collapse;
        }

        .table-container td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            white-space: nowrap;
        }

        .table-container th {
            //-เส้นขอบ colum
            padding: 8px;
            text-align: center;
            white-space: nowrap;
            position: sticky;
            top: 0px;
            text-align: center;
            background-color: #172554;
            /* เทียบเท่า bg-blue-950 */
            color: white;
            /* เพิ่มเพื่อให้ตัวหนังสืออ่านง่าย */


        }
    </style>

    <div data-aos="fade-up" data-aos-anchor-placement="top-bottom">
        <div class="table-container">
            <table class="table" id="table">
                <thead style="font-size: 12px; text-align:center">

                    <th scope="col">Refcode</th>
                    <th scope="col">Site Code</th>
                    <th scope="col">Site Name</th>
                    <th scope="col">Job Description</th>
                    <th scope="col">Project Code</th>
                    <th scope="col">Office Code</th>
                    <th scope="col">Customer Region</th>

                    @if (Auth::check() && Auth::user()->status == 4)
                        <th scope="col">Estimated Revenue</th>
                        <th scope="col">Estimated Service Cost</th>
                        <th scope="col">Estimated Material Cost</th>
                        <th scope="col">Estimated Gross Profit</th>
                        <th scope="col">Estimated Gross Profit Margin</th>
                    @endif

                    <th scope="col">Requester</th>
                    <!--
                        <th scope="col">Job Adding Status</th>
                        -->


                    <!--    </tr>  -->

                </thead>
                <tbody>
                    @foreach ($newjob as $item)
                        <tr style="font-size: 10px; text-align:center ">

                            <td>{{ $item->Refcode }}</td>

                            <td>{{ $item->Site_Code }}</td>
                            <td>{{ $item->Site_Name }}</td>
                            <td>{{ $item->Job_Description }}</td>
                            <td>{{ $item->Project_Code }}</td>
                            <td>{{ $item->Office_Code }}</td>

                            <td>{{ $item->Customer_Region }}</td>

                            @if (Auth::check() && Auth::user()->status == 4)
                            <td>{{ $item->Estimated_Revenue }}</td>
                            <td>{{ $item->Estimated_Service_Cost }}</td>
                            <td>{{ $item->Estimated_Material_Cost }}</td>
                            <td>{{ $item->Estimated_Gross_Profit }}</td>
                            <td>{{ $item->Estimated_Gross_ProfitMargin }}</td>
                            @endif

                            <td>{{ $item->Requester }}</td>
                            <!--
                                <td>{{ $item->Job_Adding_Status }}</td>
                                -->

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                AOS.init(); // เริ่มต้น AOS Animation
            });
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

    @endsection