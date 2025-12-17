@extends('layouts.app')
@section('title', 'UPDATE')
@section('content')

<!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>-->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">

    <style>
        .form-control {
            border-color: #2f82c3;
            font-size: 12px;
        }

        .col-md-12 {
            width: 100%;
            max-width: 300px;
            max-height: 300px;
        }

        .col-md-3 {
            width: 100%;
            max-width: 1000px;
            max-height: 300px;
        }

        .col-md-6 {
            width: 100%;
            max-width: 750px;
            max-height: 300px;
        }

        .col-md-7 {
            width: 100%;
            max-width: 900px;
            max-height: 40px;

        }

        .custom-divider {
            margin-top: 30px;
        }

        h4 {
            margin-top: 2px;
            font-size: 18px;
            font-weight: 600;
            color: red
        }

        label {
            font-size: 13px;
        }


        .indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-left: 5px;
            /* เพิ่มระยะห่างระหว่างข้อความและตัวบ่งชี้ */

        }

        .status-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 60px;
            /* ระยะห่างระหว่างแต่ละ <h5> */
        }

        .has-data {
            background-color: rgb(0, 156, 0);
        }

        .no-data {
            background-color: rgb(255, 0, 0);
        }

        .status-row h5 {
            font-size: 13px;
            /* ปรับขนาดฟอนต์ที่นี่ */
            margin: 0;
            /* ลบระยะห่างบนและล่างของ <h5> */
        }

        /* ปรับขนาด placeholder */
        ::placeholder {
            font-size: 11px;
            /* ขนาดของ font */
            color: #999;
            /* สีของ placeholder */
        }



        /* Collap */

        #accordionExample {
            margin: 20px auto 0 auto;
            /* ขยับห่างจากบน */
            padding-right: 50px;

        }

        .accordion-button {
            background-color: transparent;
            /* เปลี่ยนพื้นหลังเป็นโปร่งใส */
            color: black;
            /* เปลี่ยนสีข้อความ */

        }

        .accordion-button:not(.collapsed) {
            background-color: transparent;
            /* เมื่อตัว accordion เปิดอยู่ ให้ยังคงสีเดิม */
            color: black;
            /* สีข้อความยังคงเหมือนเดิม */
        }

        .accordion-button:focus {
            box-shadow: none;
            /* เอาเอฟเฟกต์เงาออกเมื่อถูกคลิก */
            background-color: #007bff;
            /* กำหนดสีพื้นหลังเมื่อถูกกด */
            color: white;
            /* กำหนดสีตัวอักษร */
        }

        .accordion-button:active {
            background-color: #0056b3;
            /* สีพื้นหลังเมื่อถูกคลิกค้าง */
            color: white;
            /* สีตัวอักษร */
        }

        .hidden {
            display: none;
        }

        .fade-in {
            opacity: 0;
            transform: translateY(-10px);
            animation: fadeIn 0.5s forwards;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* เปลี่ยนสี Modal ให้ดูเด่น */
        .modal-content {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border: 2px solid #28a745;
            /* สีเขียว */
        }

        .modal-header {
            background-color: #28a745;
            /* สีเขียว */
            color: white;
        }

        .modal-footer button:hover {
            background-color: #218838;
            /* เปลี่ยนสีปุ่มเมื่อ hover */
        }

        .modal-body i {
            font-size: 30px;
            margin-right: 10px;
        }
    </style>

    @if (session('success'))
        <!-- Modal Popup -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content border-success">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="successModalLabel">สำเร็จ!</h5>
                      <!--  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                    </div>
                    <div class="modal-body text-success">
                        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                    </div>
                </div>
            </div>
        </div>
    @endif


    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            {{ session('error') }}
        </div>
    @endif


    <div data-aos="fade-up" data-aos-anchor-placement="bottom-center">
        <h2 id="zoomText" class="text-center my-3 text-2xl font-bold"
            style="transform: scale(0.8); opacity: 0; transition: transform 0.5s ease-out, opacity 0.5s ease-out;">
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

    <div class="container input-group mb-3 input-group-sm  py-2 " data-aos="fade-up">

        <!-- Main -->
        <form id="updateForm" class="row g-3 custom-form" autocomplete="off" method="POST"
            action="{{ route('update', $blog->id) }}">
            @csrf
			@method('PUT')

            <div class="col-md-12 d-flex align-items-center ">
                <label for="RefCode" class="me-4" style="width: 100px;">RefCode</label>
                <div class="d-flex flex-column ">
                    <input type="text" name="RefCode" class="form-control" value="{{ $blog->RefCode }}">
                </div>
            </div>

			<!--
            <div class="col-md-12 d-flex align-items-center  ">
                <label for="PlanType" class="me-4" style="width: 100px;">PlanType</label>
                <div class="d-flex flex-column ">
                    <input type="text" name="PlanType" class="form-control" value="{{ $blog->PlanType }}">
                </div>
            </div>
			-->

            <div class="col-md-12 d-flex align-items-center ">
                <label for="Region_id" class="me-4" style="width: 40px;">Region</label>
                <div class="d-flex flex-column ">
                    <select name="Region_id" id="Region_id" class="form-control">
                        @foreach ($areas as $area)
                            <option value="{{ $area->Region_id }}"
                                {{ $area->Region_id == old('Region_id', $blog->Region_id) ? 'selected' : '' }}>
                                {{ $area->Region_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-12 d-flex align-items-center ">
                <label for="Province" class="me-4" style="width: 100px;">Province</label>
                <div class="d-flex flex-column ">
                    <input type="text" name="Province" class="form-control" value="{{ $blog->Province }}">
                </div>
            </div>

            <div class="col-md-12 d-flex align-items-center ">
                <label for="Province" class="me-4" style="width: 100px;">OwnerOldSte</label>
                <div class="d-flex flex-column ">
                    <input type="text" name="OwnerOldSte" class="form-control" value="{{ $blog->OwnerOldSte }}">
                </div>
            </div>

            <div class="col-md-12 d-flex align-items-center ">
                <label for="SiteCode" class="me-4" style="width: 100px;">SiteCode</label>
                <div class="d-flex flex-column ">
                    <input type="text" name="SiteCode" class="form-control" value="{{ $blog->SiteCode }}">
                    @error('SiteCode')
                        <span class="text text-danger ">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="col-md-12 d-flex align-items-center ">
                <label for="SiteNAME_T" class="me-4" style="width: 100px;">SiteNAME_T</label>
                <div class="d-flex flex-column ">
                    <input type="text" name="SiteNAME_T" class="form-control" value="{{ $blog->SiteNAME_T }}">
                </div>
            </div>

			<!--
            <div class="col-md-12 d-flex align-items-center ">
                <label for="SiteType" class="me-4" style="width: 100px;">SiteType</label>
                <div class="d-flex flex-column ">
                    <input type="text" name="SiteType" class="form-control" value="{{ $blog->SiteType }}">
                </div>
            </div>
			-->

            <div class="col-md-12 d-flex align-items-center ">
                <label for="Towerheight" class="me-4" style="width: 100px;">Towerheight</label>
                <div class="d-flex flex-column ">
                    <input type="text" name="Towerheight" class="form-control" value="{{ $blog->Towerheight }}">
                </div>
            </div>

            


            <div class="accordion py-2" id="accordionExample">
                <div class="accordion-item">


                    <!-- STATUS 1 -->

                    @if (Auth::check())

                        @if (in_array(Auth::user()->status, [1, 4]))

                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                    INVOICE STATUS
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">

                                    <div class="container input-group mb-3 input-group-sm  py-3 row g-3 custom-form">

                                        <div class="status-row">
                                            <h4>INVOICE STATUS</h4>

                                            <div class="mb-1">
                                                <h5 class="fw-bold text-primary ">Civil Design</h5>
                                            </div>

                                            <h5>Amount 1
                                                @if (!empty($blog->Amount1_IN))
                                                    <span class="indicator has-data" title="Data Present"></span>
                                                @else
                                                    <span class="indicator no-data" title="No Data"></span>
                                                @endif
                                            </h5>

                                            <h5>Amount 2
                                                @if (!empty($blog->Amount2_IN))
                                                    <span class="indicator has-data" title="Data Present"></span>
                                                @else
                                                    <span class="indicator no-data" title="No Data"></span>
                                                @endif
                                            </h5>

                                            <div class="mb-1">
                                                <h5 class="fw-bold text-primary">Civil Construction</h5>
                                            </div>

                                            <h5>Amount 1
                                                @if (!empty($blog->Amount1_CC))
                                                    <span class="indicator has-data" title="Data Present"></span>
                                                @else
                                                    <span class="indicator no-data" title="No Data"></span>
                                                @endif
                                            </h5>

                                            <h5>Amount 2
                                                @if (!empty($blog->Amount2_CC))
                                                    <span class="indicator has-data" title="Data Present"></span>
                                                @else
                                                    <span class="indicator no-data" title="No Data"></span>
                                                @endif
                                            </h5>

                                        </div>

                                        <!-- บรรทัด 1 -->
                                        <div class="col-md-12 d-flex align-items-center ">
                                            <label for="Quotation_IN" class="me-4"
                                                style="width: 100px;">Quotation</label>
                                            <div class="d-flex flex-column ">
                                                <input type="text" name="Quotation_IN" class="form-control"
                                                    value="{{ $blog->Quotation_IN }}">
                                            </div>
                                        </div>

                                        <div class="col-md-12 d-flex align-items-center ">
                                            <label for="PO_No_IN" class="me-4" style="width: 100px;">PO No.</label>
                                            <div class="d-flex flex-column ">
                                                <input type="text" name="PO_No_IN" class="form-control"
                                                    value="{{ $blog->PO_No_IN }}">
                                            </div>
                                        </div>

                                        <div class="col-md-12 d-flex align-items-center ">
                                            <label for="PO_Amount_IN" class="me-4" style="width: 100px;">PO
                                                Amount</label>
                                            <div class="d-flex flex-column  ">
                                                <input type="number" name="PO_Amount_IN" class="form-control"
                                                    placeholder="กรุณากรอกตัวเลข" value="{{ $blog->PO_Amount_IN }}">
                                            </div>
                                        </div>

                                        <div class="col-md-12 d-flex align-items-center ">
                                            <label for="Banlace_IN" class="me-4"
                                                style="width: 100px;">Balance_Invoice</label>
                                            <div class="d-flex flex-column">
                                                @php

                                                    // แปลงค่าเป็นตัวเลขก่อนการคำนวณ
                                                    $amount1_IN = floatval($blog->Amount1_IN);
                                                    $amount2_IN = floatval($blog->Amount2_IN);
                                                    $amount1_CC = floatval($blog->Amount1_CC);
                                                    $amount2_CC = floatval($blog->Amount2_CC);

                                                    $po_Amount = floatval($blog->PO_Amount_IN);

                                                    // คำนวณผลรวม
                                                    $total = $amount1_IN + $amount2_IN + $amount1_CC + $amount2_CC;
                                                    $banlace = $po_Amount;
                                                    $difference = $banlace - $total; // ไม่ให้ค่า difference ติดลบ

                                                @endphp

                                                @if (empty($banlace))
                                                @elseif ($total > $banlace)
                                                    <input type="number" name="Banlace_IN" class="form-control"
                                                        value="{{ number_format($difference, 2, '.', '') }}" readonly
                                                        style="color: red">
                                                @else
                                                    <!-- ถ้า $total เท่ากับ $banlace ให้แสดง difference เป็นทศนิยม 2 ตำแหน่ง -->
                                                    <input type="number" name="Banlace_IN" class="form-control"
                                                        value="{{ number_format($difference, 2, '.', '') }}" readonly
                                                        style="color: red">
                                                @endif

                                            </div>
                                        </div>


                                        <!-- บรรทัด 2 -->
                                        <div class="col-md-6 d-flex align-items-center flex-wrap ">

                                            <!-- ย้ายออกมานอก d-flex หรือใช้ w-100 -->
                                            <div class="w-100 mb-2 fw-bold text-primary">
                                                Civil Design
                                            </div>
											
											<div class="d-flex align-items-center mb-3 w-100">                                                
                                                <label class="me-4" style="width: 100px;">PO Amount</label>
                                                <div class="d-flex flex-column">
                                                    <input type="number" name="Design_Amount" class="form-control"
                                                        style="width: 160px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Design_Amount }}">
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center mb-3 ">

                                                <label for="Invoice1_IN" class="me-4" style="width: 100px;">Invoice 1
                                                </label>
                                                <div class="flex-grow-1 position-relative">
                                                    <input type="text" id="Invoice1_IN" name="Invoice1_IN"
                                                        class="form-control" style="width: 160px;"
                                                        value="{{ old('Invoice1_IN', $blog->Invoice1_IN) }}">
                                                </div>

                                            </div>

                                            <div class="d-flex align-items-center mb-3 ms-3">
                                                <label for="Amount1_IN" class="me-4" style="width: 100px;">Amount
                                                    1</label>
                                                <div class="d-flex flex-column">
                                                    <input type="number" name="Amount1_IN" class="form-control"
                                                        style="width: 160px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Amount1_IN }}">
                                                </div>
                                            </div>

                                            @if (!empty($blog->Amount1_IN))
                                                <div style="margin-top: -20px; margin-left: 8px;">
                                                    <img src="/checkmark.png" width="18" height="18"
                                                        alt="checkmark">
                                                </div>
                                            @else
                                            @endif

                                        </div>

                                        <div class="col-md-6 d-flex align-items-center flex-wrap mt-0 ">
                                            <div class="d-flex align-items-center mb-3 ">

                                                <label for="Invoice2_IN" class="me-4" style="width: 100px;">Invoice 2
                                                </label>
                                                <div class="flex-grow-1 position-relative">
                                                    <input type="text" id="Invoice2_IN" name="Invoice2_IN"
                                                        class="form-control" style="width: 160px;"
                                                        value="{{ old('Invoice2_IN', $blog->Invoice2_IN) }}">
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center mb-3 ms-3">
                                                <label for="Amount2_IN" class="me-4" style="width: 100px;">Amount 2
                                                </label>
                                                <div class="d-flex flex-column  ">
                                                    <input type="number" name="Amount2_IN" class="form-control"
                                                        style="width: 160px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Amount2_IN }}">

                                                </div>
                                            </div>

                                            @if (!empty($blog->Amount2_IN))
                                                <div style="margin-top: -20px; margin-left: 8px;">
                                                    <img src="/checkmark.png" width="18" height="18"
                                                        alt="checkmark">
                                                </div>
                                            @else
                                            @endif
                                        </div>


                                        <!-- บรรทัด 2 -->
                                        <div class="col-md-6 d-flex align-items-center flex-wrap ">

                                            <!-- ย้ายออกมานอก d-flex หรือใช้ w-100 -->
                                            <div class="w-100 mb-2 fw-bold text-primary">
                                                Civil Construction
                                            </div>
											
											<div class="d-flex align-items-center mb-3 w-100">                                                
                                                <label class="me-4" style="width: 100px;">PO Amount</label>
                                                <div class="d-flex flex-column">
                                                    <input type="number" name="Construction_Amount" class="form-control"
                                                        style="width: 160px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Construction_Amount }}">
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center mb-3 ">

                                                <label for="Invoice1_CC" class="me-4" style="width: 100px;">Invoice 1
                                                </label>
                                                <div class="flex-grow-1 position-relative">
                                                    <input type="text" id="Invoice1_CC" name="Invoice1_CC"
                                                        class="form-control" style="width: 160px;"
                                                        value="{{ old('Invoice1_CC', $blog->Invoice1_CC) }}">
                                                </div>

                                            </div>

                                            <div class="d-flex align-items-center mb-3 ms-3">
                                                <label for="Amount1_CC" class="me-4" style="width: 100px;">Amount
                                                    1</label>
                                                <div class="d-flex flex-column">
                                                    <input type="number" name="Amount1_CC" class="form-control"
                                                        style="width: 160px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Amount1_CC }}">
                                                </div>
                                            </div>
                                            @if (!empty($blog->Amount1_CC))
                                                <div style="margin-top: -20px; margin-left: 8px;">
                                                    <img src="/checkmark.png" width="18" height="18"
                                                        alt="checkmark">
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-md-6 d-flex align-items-center flex-wrap mt-0 ">
                                            <div class="d-flex align-items-center mb-3 ">

                                                <label for="Invoice2_CC" class="me-4" style="width: 100px;">Invoice 2
                                                </label>
                                                <div class="flex-grow-1 position-relative">
                                                    <input type="text" id="Invoice2_CC" name="Invoice2_CC"
                                                        class="form-control" style="width: 160px;"
                                                        value="{{ old('Invoice2_CC', $blog->Invoice2_CC) }}">
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center mb-3 ms-3">
                                                <label for="Amount2_CC" class="me-4" style="width: 100px;">Amount 2
                                                </label>
                                                <div class="d-flex flex-column">
                                                    <input type="number" name="Amount2_CC" class="form-control"
                                                        style="width: 160px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Amount2_CC }}">
                                                </div>
                                            </div>
                                            @if (!empty($blog->Amount2_CC))
                                                <div style="margin-top: -20px; margin-left: 8px;">
                                                    <img src="/checkmark.png" width="18" height="18"
                                                        alt="checkmark">
                                                </div>
                                            @endif

                                        </div>
                                        
                                    </div>
                                </div>
                            </div>

                            <!-- SAQ -->

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        SAQ
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">


                                        <div class="container input-group mb-3 input-group-sm  py-3 row g-3 custom-form">

                                            <div class="status-row">

                                                <h4>SAQ</h4>

                                                <h5>Accept_1st
                                                    @if (!empty($blog->Accept_1st_SAQ))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_2nd
                                                    @if (!empty($blog->Accept_2nd_SAQ))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_3rd
                                                    @if (!empty($blog->Accept_3rd_SAQ))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_4th
                                                    @if (!empty($blog->Accept_4th_SAQ))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>
                                            </div>

                                            <!-- บรรทัด 1 -->
                                            <div class="col-md-6 d-flex align-items-center flex-wrap ">
                                                <div class="d-flex align-items-center mb-3 ms-2">

                                                    <label for="AssignedSubCSurveySAQ" class="me-4"
                                                        style="width: 150px;">AssignedSubCSurveySAQ</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="AssignedSubCSurveySAQ"
                                                            name="AssignedSubCSurveySAQ"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 180px;"
                                                            value="{{ old('AssignedSubCSurveySAQ', $blog->AssignedSubCSurveySAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="SubName_SAQ" class="me-4"
                                                        style="width: 100px;">SubName_SAQ</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" name="SubName_SAQ" class="form-control"
                                                            style="width: 200px;" value="{{ $blog->SubName_SAQ }}">
                                                    </div>
                                                </div>
                                            </div>



                                            <!-- บรรทัด 2 -->

                                            <div class="col-md-6 d-flex align-items-center flex-wrap mt-0 ">

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="PlanSurveySAQ" class="me-4"
                                                        style="width: 100px;">PlanSurveySAQ</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="PlanSurveySAQ" name="PlanSurveySAQ"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 160px;"
                                                            value="{{ old('PlanSurveySAQ', $blog->PlanSurveySAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="ActualSurveySAQ" class="me-4"
                                                        style="width: 100px;">ActualSurveySAQ</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ActualSurveySAQ" name="ActualSurveySAQ"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 160px;"
                                                            value="{{ old('ActualSurveySAQ', $blog->ActualSurveySAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 3 -->

                                            <div class="col-md-3 d-flex align-items-center flex-wrap mt-0">

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Quo_No_SAQ" class="me-4"
                                                        style="width: 100px;">Quo_No_SAQ</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="Quo_No_SAQ" class="form-control"
                                                            style="width: 160px;" value="{{ $blog->Quo_No_SAQ }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="PR_Price_SAQ" class="me-4"
                                                        style="width: 100px;">PR_Price_SAQ</label>
                                                    <div class="d-flex flex-column  ">
                                                        <input type="text" name="PR_Price_SAQ" class="form-control"
                                                            style="width: 160px;" placeholder="กรุณากรอกตัวเลข"
                                                            value="{{ $blog->PR_Price_SAQ }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Accept_PR_Date_SAQ" class="me-4"
                                                        style="width: 120px;">Accept_PR_Date_SAQ</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Accept_PR_Date_SAQ"
                                                            name="Accept_PR_Date_SAQ" style="width: 160px;"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('Accept_PR_Date_SAQ', $blog->Accept_PR_Date_SAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 4 -->

                                            <div class="col-md-3 d-flex align-items-center flex-wrap mt-0">

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="WO_No_SAQ" class="me-4"
                                                        style="width: 100px;">WO_No_SAQ</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="WO_No_SAQ" class="form-control"
                                                            style="width: 160px;" value="{{ $blog->WO_No_SAQ }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="WO_Price_SAQ" class="me-4"
                                                        style="width: 100px;">WO_Price_SAQ</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="number" name="WO_Price_SAQ" class="form-control"
                                                            style="width: 160px;" placeholder="กรุณากรอกตัวเลข"
                                                            value="{{ $blog->WO_Price_SAQ }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Banlace_SAQ" class="me-4"
                                                        style="width: 100px;">Balance_SAQ</label>
                                                    <div class="d-flex flex-column">
                                                        @php

                                                            // แปลงค่าเป็นตัวเลขก่อนการคำนวณ
                                                            $accept_1st_saq = floatval($blog->Accept_1st_SAQ);
                                                            $accept_2nd_saq = floatval($blog->Accept_2nd_SAQ);
                                                            $accept_3rd_saq = floatval($blog->Accept_3rd_SAQ);
                                                            $accept_4th_saq = floatval($blog->Accept_4th_SAQ);
                                                            $wo_price_saq = floatval($blog->WO_Price_SAQ);

                                                            // คำนวณผลรวม
                                                            $total =
                                                                $accept_1st_saq +
                                                                $accept_2nd_saq +
                                                                $accept_3rd_saq +
                                                                $accept_4th_saq;
                                                            $banlace = $blog->WO_Price_SAQ;
                                                            $difference = $banlace - $total; // ไม่ให้ค่า difference ติดลบ
                                                        @endphp


                                                        @if (empty($banlace))
                                                        @elseif ($total > $banlace)
                                                            <input type="number" name="Banlace_SAQ" class="form-control"
                                                                value="{{ number_format($difference, 2, '.', '') }}"
                                                                readonly style="color: red">
                                                        @else
                                                            <!-- ถ้า $total เท่ากับ $banlace ให้แสดง difference เป็นทศนิยม 2 ตำแหน่ง -->
                                                            <input type="number" name="Banlace_SAQ" class="form-control"
                                                                value="{{ number_format($difference, 2, '.', '') }}"
                                                                readonly style="color: red">
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>


                                            <!-- บรรทัด 5 -->

                                            <div class="col-md-7 d-flex align-items-center mt-4 ms-2">
                                                <label for="Accept_1st_SAQ" class="me-4"
                                                    style="width: 120px;">Accept_1st_SAQ</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_1st_SAQ" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_1st_SAQ }}">
                                                </div>


                                                <div class="form-group mb-4 ms-2 ">
                                                    <label for="Mail_1st_SAQ" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="position-relative">
                                                        <input type="text" id="Mail_1st_SAQ" name="Mail_1st_SAQ"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_1st_SAQ', $blog->Mail_1st_SAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_1st_SAQ" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_1st_SAQ" name="ERP_1st_SAQ"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_1st_SAQ', $blog->ERP_1st_SAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
													
                                                </div>
												
												@if (!empty($blog->Accept_1st_SAQ))
                                                    <div style="margin-top: -10px; margin-left: 5px;">
                                                        <img src="/checkmark.png" width="18" height="18"
                                                            alt="checkmark">
                                                    </div>
                                                @endif
												
                                            </div>

                                            <!-- บรรทัด 6 -->

                                            <div class="col-md-7 d-flex align-items-center  ms-2">
                                                <label for="Accept_2nd_SAQ" class="me-4"
                                                    style="width: 120px;">Accept_2nd_SAQ</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_2nd_SAQ" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_2nd_SAQ }}">

                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_2nd_SAQ" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_2nd_SAQ" name="Mail_2nd_SAQ"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_2nd_SAQ', $blog->Mail_2nd_SAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_2nd_SAQ" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_2nd_SAQ" name="ERP_2nd_SAQ"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_2nd_SAQ', $blog->ERP_2nd_SAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_2nd_SAQ))
                                                    <div style="margin-top: -10px; margin-left: 5px;">
                                                        <img src="/checkmark.png" width="18" height="18"
                                                            alt="checkmark">
                                                    </div>
                                                @endif
												
                                            </div>

                                            <!-- บรรทัด 7 -->

                                            <div class="col-md-7 d-flex align-items-center  ms-2">
                                                <label for="Accept_3rd_SAQ" class="me-4"
                                                    style="width: 120px;">Accept_3rd_SAQ</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_3rd_SAQ" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_3rd_SAQ }}">
                                                </div>


                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_3rd_SAQ" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_3rd_SAQ" name="Mail_3rd_SAQ"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_3rd_SAQ', $blog->Mail_3rd_SAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_3rd_SAQ" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_3rd_SAQ" name="ERP_3rd_SAQ"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_3rd_SAQ', $blog->ERP_3rd_SAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												@if (!empty($blog->Accept_3rd_SAQ))
                                                    <div style="margin-top: -10px; margin-left: 5px;">
                                                        <img src="/checkmark.png" width="18" height="18"
                                                            alt="checkmark">
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- บรรทัด 8 -->

                                            <div class="col-md-7 d-flex align-items-center ms-2">
                                                <label for="Accept_4th_SAQ" class="me-4"
                                                    style="width: 120px;">Accept_4th_SAQ</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_4th_SAQ" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_4th_SAQ }}">
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_4th_SAQ" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_4th_SAQ" name="Mail_4th_SAQ"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_4th_SAQ', $blog->Mail_4th_SAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_4th_SAQ" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_4th_SAQ" name="ERP_4th_SAQ"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_4th_SAQ', $blog->ERP_4th_SAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_4th_SAQ))
                                                <div style="margin-top: -10px; margin-left: 5px;">
                                                    <img src="/checkmark.png" width="18" height="18"
                                                        alt="checkmark">
                                                </div>    
                                                @endif
												
                                            </div>
											
                                        </div>


                                    </div>
                                </div>
                            </div>

                            <!-- CR -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseThree" aria-expanded="false"
                                        aria-controls="collapseThree">
                                        CR
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse"
                                    aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">


                                        <div class="container input-group mb-3 input-group-sm  py-3 row g-3 custom-form">

                                            <div class="status-row">

                                                <h4>CR</h4>

                                                <h5>Accept_1st
                                                    @if (!empty($blog->Accept_1st_CR))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_2nd
                                                    @if (!empty($blog->Accept_2nd_CR))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_3rd
                                                    @if (!empty($blog->Accept_3rd_CR))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_4th
                                                    @if (!empty($blog->Accept_4th_CR))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>
                                            </div>

                                            <!-- บรรทัด 1 -->
                                            <div class="col-md-6 d-flex align-items-center flex-wrap ">
                                                <div class="d-flex align-items-center mb-3 ms-2">

                                                    <label for="AssignedSubCCR" class="me-4"
                                                        style="width: 120px;">AssignedSubCCR</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="AssignedSubCCR" name="AssignedSubCCR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 160px;"
                                                            value="{{ old('AssignedSubCCR', $blog->AssignedSubCCR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="SubName_CR" class="me-4"
                                                        style="width: 100px;">SubName_CR</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="SubName_CR" class="form-control"
                                                            style="width: 200px;" value="{{ $blog->SubName_CR }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 2 -->

                                            <div class="col-md-6 d-flex align-items-center flex-wrap mt-0 ">

                                                <div div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="PlanCR" class="me-4"
                                                        style="width: 120px;">PlanCR</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="PlanCR" name="PlanCR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 160px;"
                                                            value="{{ old('PlanCR', $blog->PlanCR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="ActualCR" class="me-4"
                                                        style="width: 100px;">ActualCR</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ActualCR" name="ActualCR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 160px;"
                                                            value="{{ old('ActualCR', $blog->ActualCR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 3 -->

                                            <div class="col-md-3 d-flex align-items-center flex-wrap mt-0">

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Quo_No_CR" class="me-4"
                                                        style="width: 120px;">Quo_No_CR</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="Quo_No_CR" class="form-control"
                                                            style="width: 160px;" value="{{ $blog->Quo_No_CR }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="PR_Price_CR" class="me-4"
                                                        style="width: 100px;">PR_Price_CR</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="number" name="PR_Price_CR" class="form-control"
                                                            style="width: 160px;" placeholder="กรุณากรอกตัวเลข"
                                                            value="{{ $blog->PR_Price_CR }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Accept_PR_Date_CR" class="me-4"
                                                        style="width: 120px;">Accept_PR_Date_CR</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Accept_PR_Date_CR"
                                                            name="Accept_PR_Date_CR" class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 160px;"
                                                            value="{{ old('Accept_PR_Date_CR', $blog->Accept_PR_Date_CR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 4 -->

                                            <div class="col-md-3 d-flex align-items-center flex-wrap mt-0">

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="WO_No_CR" class="me-4"
                                                        style="width: 120px;">WO_No_CR</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="WO_No_CR" class="form-control"
                                                            style="width: 160px;" value="{{ $blog->WO_No_CR }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="WO_Price_CR" class="me-4"
                                                        style="width: 100px;">WO_Price_CR</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="number" name="WO_Price_CR" class="form-control"
                                                            style="width: 160px;" placeholder="กรุณากรอกตัวเลข"
                                                            value="{{ $blog->WO_Price_CR }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Banlace_CR" class="me-4"
                                                        style="width: 80px;">Balance_CR</label>
                                                    <div class="d-flex flex-column">
                                                        @php

                                                            // แปลงค่าเป็นตัวเลขก่อนการคำนวณ
                                                            $accept_1st_cr = floatval($blog->Accept_1st_CR);
                                                            $accept_2nd_cr = floatval($blog->Accept_2nd_CR);
                                                            $accept_3rd_cr = floatval($blog->Accept_3rd_CR);
                                                            $accept_4th_cr = floatval($blog->Accept_4th_CR);
                                                            $wo_price_cr = floatval($blog->WO_Price_CR);

                                                            // คำนวณผลรวม
                                                            $total =
                                                                $accept_1st_cr +
                                                                $accept_2nd_cr +
                                                                $accept_3rd_cr +
                                                                $accept_4th_cr;
                                                            $banlace = $blog->WO_Price_CR;
                                                            $difference = $banlace - $total; // ไม่ให้ค่า difference ติดลบ
                                                        @endphp



                                                        @if (empty($banlace))
                                                        @elseif ($total > $banlace)
                                                            <input type="number" name="Banlace_CR" class="form-control"
                                                                value="{{ number_format($difference, 2, '.', '') }}"
                                                                readonly style="color: red">
                                                        @else
                                                            <!-- ถ้า $total เท่ากับ $banlace ให้แสดง "0" -->
                                                            <input type="number" name="Banlace_CR" class="form-control"
                                                                value="{{ number_format($difference, 2, '.', '') }}"
                                                                readonly style="color: red">
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 4 -->

                                            <div class="col-md-7 d-flex align-items-center mt-4 ms-2">
                                                <label for="Accept_1st_CR" class="me-4"
                                                    style="width: 120px;">Accept_1st_CR</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_1st_CR" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_1st_CR }}">
                                                </div>


                                                <div class="form-group mb-4 ms-2 ">
                                                    <label for="Mail_1st_CR" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="position-relative">
                                                        <input type="text" id="Mail_1st_CR" name="Mail_1st_CR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_1st_CR', $blog->Mail_1st_CR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_1st_CR" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_1st_CR" name="ERP_1st_CR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_1st_CR', $blog->ERP_1st_CR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_1st_CR))
                                                <div style="margin-top: -10px; margin-left: 5px;">
                                                    <img src="/checkmark.png" width="18" height="18"
                                                        alt="checkmark">
                                                </div>    
                                                @endif
                                            </div>

                                            <!-- บรรทัด 5 -->

                                            <div class="col-md-7 d-flex align-items-center ms-2">
                                                <label for="Accept_2nd_CR" class="me-4"
                                                    style="width: 120px;">Accept_2nd_CR</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_2nd_CR" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_2nd_CR }}">
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_2nd_CR" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_2nd_CR" name="Mail_2nd_CR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_2nd_CR', $blog->Mail_2nd_CR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_2nd_CR" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_2nd_CR" name="ERP_2nd_CR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_2nd_CR', $blog->ERP_2nd_CR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_2nd_CR))
                                                <div style="margin-top: -10px; margin-left: 5px;">
                                                    <img src="/checkmark.png" width="18" height="18"
                                                        alt="checkmark">
                                                </div>  
                                                @endif
												
                                            </div>

                                            <!-- บรรทัด 6 -->

                                            <div class="col-md-7 d-flex align-items-center  ms-2">
                                                <label for="Accept_3rd_CR" class="me-4"
                                                    style="width: 120px;">Accept_3rd_CR</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_3rd_CR" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_3rd_CR }}">
                                                </div>


                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_3rd_CR" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_3rd_CR" name="Mail_3rd_CR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_3rd_CR', $blog->Mail_3rd_CR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_3rd_CR" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_3rd_CR" name="ERP_3rd_CR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_3rd_CR', $blog->ERP_3rd_CR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_3rd_CR))
                                                <div style="margin-top: -10px; margin-left: 5px;">
                                                    <img src="/checkmark.png" width="18" height="18"
                                                        alt="checkmark">
                                                </div>  
                                                @endif
												
                                            </div>

                                            <!-- บรรทัด 7 -->

                                            <div class="col-md-7 d-flex align-items-center ms-2">
                                                <label for="Accept_4th_CR" class="me-4"
                                                    style="width: 120px;">Accept_4th_CR</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_4th_CR" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_4th_CR }}">
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_4th_CR" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_4th_CR" name="Mail_4th_CR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_4th_CR', $blog->Mail_4th_CR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_4th_CR" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_4th_CR" name="ERP_4th_CR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_4th_CR', $blog->ERP_4th_CR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_4th_CR))
                                                <div style="margin-top: -10px; margin-left: 5px;">
                                                    <img src="/checkmark.png" width="18" height="18"
                                                        alt="checkmark">
                                                </div>  
                                                @endif
												
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>




                            <!-- TSSR -->

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingFour">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseFour" aria-expanded="false"
                                        aria-controls="collapseFour">
                                        TSSR
                                    </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">


                                        <div class="container input-group mb-3 input-group-sm  py-3 row g-3 custom-form">

                                            <div class="status-row">

                                                <h4>TSSR</h4>

                                                <h5>Accept_1st
                                                    @if (!empty($blog->Accept_1st_TSSR))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_2nd
                                                    @if (!empty($blog->Accept_2nd_TSSR))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_3rd
                                                    @if (!empty($blog->Accept_3rd_TSSR))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_4th
                                                    @if (!empty($blog->Accept_4th_TSSR))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>
                                            </div>

                                            <!-- บรรทัด 1 -->

                                            <div class="col-md-6 d-flex align-items-center flex-wrap ">

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="AssignedSubCTSSR" class="me-4"
                                                        style="width: 120px;">AssignedSubCTSSR</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="AssignedSubCTSSR"
                                                            name="AssignedSubCTSSR" class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 160px;"
                                                            value="{{ old('AssignedSubCTSSR', $blog->AssignedSubCTSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="SubName_TSSR" class="me-4"
                                                        style="width: 100px;">SubName_TSSR</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="SubName_TSSR" class="form-control"
                                                            style="width: 200px;" value="{{ $blog->SubName_TSSR }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 2 -->

                                            <div class="col-md-6 d-flex align-items-center flex-wrap mt-0 ">

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="PlanTSSR" class="me-4"
                                                        style="width: 120px;">PlanTSSR</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="PlanTSSR" name="PlanTSSR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 160px;"
                                                            value="{{ old('PlanTSSR', $blog->PlanTSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="ActualTSSR" class="me-4"
                                                        style="width: 100px;">ActualTSSR</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ActualTSSR" name="ActualTSSR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 160px;"
                                                            value="{{ old('ActualTSSR', $blog->ActualTSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 3 -->

                                            <div class="col-md-3 d-flex align-items-center flex-wrap mt-0">

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Quo_No_TSSR" class="me-4"
                                                        style="width: 120px;">Quo_No_TSSR</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="Quo_No_TSSR" class="form-control"
                                                            style="width: 160px;" value="{{ $blog->Quo_No_TSSR }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="PR_Price_TSSR" class="me-4"
                                                        style="width: 100px;">PR_Price_TSSR</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="number" name="PR_Price_TSSR" 
															class="form-control"
                                                            style="width: 160px;" placeholder="กรุณากรอกตัวเลข"
                                                            value="{{ $blog->PR_Price_TSSR }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Accept_PR_Date_TSSR" class="me-4"
                                                        style="width: 140px;">Accept_PR_Date_TSSR</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Accept_PR_Date_TSSR"
                                                            name="Accept_PR_Date_TSSR" style="width: 160px;"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('Accept_PR_Date_TSSR', $blog->Accept_PR_Date_TSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 4 -->

                                            <div class="col-md-3 d-flex align-items-center flex-wrap mt-0">
                                                <div class="d-flex align-items-center mb-3 ms-2">

                                                    <label for="WO_No_TSSR" class="me-4"
                                                        style="width: 120px;">WO_No_TSSR</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="WO_No_TSSR" class="form-control"
                                                            style="width: 160px;" value="{{ $blog->WO_No_TSSR }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="WO_Price_TSSR" class="me-4"
                                                        style="width: 100px;">WO_Price_TSSR</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="number" name="WO_Price_TSSR"
                                                            class="form-control" style="width: 160px;"
                                                            placeholder="กรุณากรอกตัวเลข"
                                                            value="{{ $blog->WO_Price_TSSR }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Banlace_TSSR" class="me-4"
                                                        style="width: 80px;">Banlace_TSSR</label>
                                                    <div class="d-flex flex-column">
                                                        @php

                                                            // แปลงค่าเป็นตัวเลขก่อนการคำนวณ
                                                            $accept_1st_TSSR = floatval($blog->Accept_1st_TSSR);
                                                            $accept_2nd_TSSR = floatval($blog->Accept_2nd_TSSR);
                                                            $accept_3rd_TSSR = floatval($blog->Accept_3rd_TSSR);
                                                            $accept_4th_TSSR = floatval($blog->Accept_4th_TSSR);
                                                            $wo_price_TSSR = floatval($blog->WO_Price_TSSR);

                                                            // คำนวณผลรวม
                                                            $total =
                                                                $accept_1st_TSSR +
                                                                $accept_2nd_TSSR +
                                                                $accept_3rd_TSSR +
                                                                $accept_4th_TSSR;
                                                            $banlace = $blog->WO_Price_TSSR;
                                                            $difference = $banlace - $total; // ไม่ให้ค่า difference ติดลบ
                                                        @endphp


                                                        @if (empty($banlace))
                                                        @else
                                                            @if ($total > $banlace)
                                                                <input type="number" name="Banlace_TSSR"
                                                                    class="form-control"
                                                                    value="{{ number_format($difference, 2, '.', '') }}"
                                                                    readonly style="color: red">
                                                            @else
                                                                <input type="number" name="Banlace_TSSR"
                                                                    class="form-control"
                                                                    value="{{ number_format($difference, 2, '.', '') }}"
                                                                    readonly style="color: red">
                                                            @endif
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 4 -->

                                            <div class="col-md-7 d-flex align-items-center mt-4  ms-2">
                                                <label for="Accept_1st_TSSR" class="me-4"
                                                    style="width: 120px;">Accept_1st_TSSR</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_1st_TSSR" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_1st_TSSR }}">
                                                </div>


                                                <div class="form-group mb-4 ms-2 ">
                                                    <label for="Mail_1st_TSSR" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="position-relative">
                                                        <input type="text" id="Mail_1st_TSSR" name="Mail_1st_TSSR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_1st_TSSR', $blog->Mail_1st_TSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_1st_TSSR" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_1st_TSSR" name="ERP_1st_TSSR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_1st_TSSR', $blog->ERP_1st_TSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_1st_TSSR))
                                                <div style="margin-top: -10px; margin-left: 5px;">
                                                    <img src="/checkmark.png" width="18" height="18"
                                                        alt="checkmark">
                                                </div>    
                                                @endif
												
                                            </div>

                                            <!-- บรรทัด 5 -->

                                            <div class="col-md-7 d-flex align-items-center  ms-2">
                                                <label for="Accept_2nd_TSSR" class="me-4"
                                                    style="width: 120px;">Accept_2nd_TSSR</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_2nd_TSSR" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_2nd_TSSR }}">
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_2nd_TSSR" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_2nd_TSSR" name="Mail_2nd_TSSR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_2nd_TSSR', $blog->Mail_2nd_TSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_2nd_TSSR" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_2nd_TSSR" name="ERP_2nd_TSSR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_2nd_TSSR', $blog->ERP_2nd_TSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												 @if (!empty($blog->Accept_2nd_TSSR))
                                                <div style="margin-top: -10px; margin-left: 5px;">
                                                    <img src="/checkmark.png" width="18" height="18"
                                                        alt="checkmark">
                                                </div>    
                                                @endif
												
                                            </div>

                                            <!-- บรรทัด 6 -->

                                            <div class="col-md-7 d-flex align-items-center  ms-2">
                                                <label for="Accept_3rd_TSSR" class="me-4"
                                                    style="width: 120px;">Accept_3rd_TSSR</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_3rd_TSSR" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_3rd_TSSR }}">
                                                </div>


                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_3rd_TSSR" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_3rd_TSSR" name="Mail_3rd_TSSR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_3rd_TSSR', $blog->Mail_3rd_TSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_3rd_TSSR" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_3rd_TSSR" name="ERP_3rd_TSSR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_3rd_TSSR', $blog->ERP_3rd_TSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_3rd_TSSR))
                                                <div style="margin-top: -10px; margin-left: 5px;">
                                                    <img src="/checkmark.png" width="18" height="18"
                                                        alt="checkmark">
                                                </div>    
                                                @endif
												
                                            </div>

                                            <!-- บรรทัด 7 -->

                                            <div class="col-md-7 d-flex align-items-center ms-2">
                                                <label for="Accept_4th_TSSR" class="me-4"
                                                    style="width: 120px;">Accept_4th_TSSR</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_4th_TSSR" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_4th_TSSR }}">
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_4th_TSSR" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_4th_TSSR" name="Mail_4th_TSSR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_4th_TSSR', $blog->Mail_4th_TSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_4th_TSSR" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_4th_TSSR" name="ERP_4th_TSSR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_4th_TSSR', $blog->ERP_4th_TSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												 @if (!empty($blog->Accept_4th_TSSR))
                                                <div style="margin-top: -10px; margin-left: 5px;">
                                                    <img src="/checkmark.png" width="18" height="18"
                                                        alt="checkmark">
                                                </div>    
                                                @endif
												
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>



                            <!-- CivilWork -->

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingfive">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapsefive" aria-expanded="false"
                                        aria-controls="collapsefive">
                                        CivilWork
                                    </button>
                                </h2>
                                <div id="collapsefive" class="accordion-collapse collapse"
                                    aria-labelledby="headingfive" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">


                                        <div class="container input-group mb-3 input-group-sm  py-3 row g-3 custom-form">

                                            <div class="status-row">

                                                <h4>CivilWork</h4>

                                                <h5>Accept_1st
                                                    @if (!empty($blog->Accept_1st_CivilWork))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_2nd
                                                    @if (!empty($blog->Accept_2nd_CivilWork))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_3rd
                                                    @if (!empty($blog->Accept_3rd_CivilWork))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_4th
                                                    @if (!empty($blog->Accept_4th_CivilWork))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>
                                            </div>




                                            <!-- บรรทัด 1 -->

                                            <div class="col-md-6 d-flex align-items-center flex-wrap ">
                                                <!-- เพิ่ม flex-wrap เพื่อให้แต่ละบรรทัดอยู่แยกกัน -->
                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <!-- เพิ่ม margin-bottom เพื่อสร้างระยะห่างระหว่างแถว -->
                                                    <label for="AssignSubCivilfoundation" class="me-4"
                                                        style="width: 100px;">PlanCivilFoundation</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="AssignSubCivilfoundation"
                                                            style="width: 180px;" name="AssignSubCivilfoundation"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('AssignSubCivilfoundation', $blog->AssignSubCivilfoundation) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <!-- เพิ่ม margin-bottom เพื่อสร้างระยะห่างระหว่างแถว -->
                                                    <label for="PlanCivilWorkFoundation" class="me-4"
                                                        style="width: 150px;">ActualCivilWorkFoundation</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="PlanCivilWorkFoundation"
                                                            style="width: 180px;" name="PlanCivilWorkFoundation"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('PlanCivilWorkFoundation', $blog->PlanCivilWorkFoundation) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 2 -->

                                            <div class="col-md-6 d-flex align-items-center flex-wrap mt-0">

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <!-- ลด margin-bottom และใช้ margin-top ติดลบ -->
                                                    <label for="ActualCivilWorkTower" class="me-4"
                                                        style="width: 100px;">PlanCivilWorkTower</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ActualCivilWorkTower"
                                                            style="width: 180px;" name="ActualCivilWorkTower"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('ActualCivilWorkTower', $blog->ActualCivilWorkTower) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>


                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="AssignCivilWorkTower" class="me-4"
                                                        style="width: 150px;">ActualCivilWorkTower</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="AssignCivilWorkTower"
                                                            style="width: 180px;" name="AssignCivilWorkTower"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('AssignCivilWorkTower', $blog->AssignCivilWorkTower) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 3 -->

                                            <div class="col-md-6 d-flex align-items-center flex-wrap mt-0">
                                                <!-- ใช้ mt-n3 เพื่อขยับขึ้น -->
                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <!-- ลด margin-bottom และใช้ margin-top ติดลบ -->

                                                    <label for="PlanInstallationRectifier" class="me-4"
                                                        style="width: 130px;">PlanInstallationRectifier</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="PlanInstallationRectifier"
                                                            style="width: 150px;" name="PlanInstallationRectifier"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('PlanInstallationRectifier', $blog->PlanInstallationRectifier) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="ActualInstallationRectifier" class="me-4"
                                                        style="width: 150px;">ActualInstallationRectifier</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ActualInstallationRectifier"
                                                            style="width: 180px;" name="ActualInstallationRectifier"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('ActualInstallationRectifier', $blog->ActualInstallationRectifier) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 4 -->

                                            <div class="col-md-6 d-flex align-items-center flex-wrap mt-0">
                                                <!-- ใช้ mt-n3 เพื่อขยับขึ้น -->
                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <!-- ลด margin-bottom และใช้ margin-top ติดลบ -->
                                                    <label for="PlanACPower" class="me-4"
                                                        style="width: 100px;">PlanACPower</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="PlanACPower" name="PlanACPower"
                                                            style="width: 180px;" class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('PlanACPower', $blog->PlanACPower) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>


                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="ActualACPower" class="me-4"
                                                        style="width: 150px;">ActualACPower</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ActualACPower" name="ActualACPower"
                                                            style="width: 180px;" class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('ActualACPower', $blog->ActualACPower) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 5 -->

                                            <div class="col-md-6 d-flex align-items-center flex-wrap mt-0">
                                                <div class="d-flex align-items-center mb-3 ms-2">

                                                    <label for="PlanACMeter" class="me-4"
                                                        style="width: 100px;">PlanACMeter</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="PlanACMeter" name="PlanACMeter"
                                                            style="width: 180px;" class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('PlanACMeter', $blog->PlanACMeter) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="ActualACMeter" class="me-4"
                                                        style="width: 150px;">ActualACMeter</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ActualACMeter" name="ActualACMeter"
                                                            style="width: 180px;" class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('ActualACMeter', $blog->ActualACMeter) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 6 -->
                                            <div class="col-md-3 d-flex align-items-center flex-wrap mt-0">
                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="PAT" class="me-1"
                                                        style="width: 50px;">PAT</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="PAT" class="form-control"
                                                            value="{{ $blog->PAT }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="DefPAT" class="me-1"
                                                        style="width: 50px;">DefPAT</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="DefPAT" class="form-control"
                                                            value="{{ $blog->DefPAT }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="FAT" class="me-1"
                                                        style="width: 50px;">FAT</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="FAT" class="form-control"
                                                            value="{{ $blog->FAT }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="custom-divider">



                                            <!-- บรรทัด 1 -->

                                            <div class="col-md-6 d-flex align-items-center flex-wrap ">
                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Assigned_CivilWork" class="me-4"
                                                        style="width: 100px;">Assigned_CivilWork</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Assigned_CivilWork"
                                                            name="Assigned_CivilWork" style="width: 180px;"
                                                            class="form-control datepicker"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('Assigned_CivilWork', $blog->Assigned_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="SubName_CivilWork" class="me-4"
                                                        style="width: 120px;">SubName_CivilWork</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="SubName_CivilWork"
                                                            class="form-control" style="width: 200px;"
                                                            value="{{ $blog->SubName_CivilWork }}">
                                                    </div>
                                                </div>
                                            </div>


                                            <!-- บรรทัด 2 -->
                                            <div class="col-md-6 d-flex align-items-center flex-wrap mt-0">
                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Plan_CivilWork" class="me-4"
                                                        style="width: 100px;">Plan_CivilWork</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Plan_CivilWork"
                                                            name="Plan_CivilWork" style="width: 180px;"
                                                            class="form-control datepicker"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('Plan_CivilWork', $blog->Plan_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Actual_CivilWork" class="me-4"
                                                        style="width: 120px;">Actual_CivilWork</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Actual_CivilWork"
                                                            name="Actual_CivilWork" style="width: 160px;"
                                                            class="form-control datepicker"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('Actual_CivilWork', $blog->Actual_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 3 -->

                                            <div class="col-md-3 d-flex align-items-center flex-wrap mt-0">

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Quo_No_CivilWork" class="me-4"
                                                        style="width: 100px;">Quo_No_CivilWork</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="Quo_No_CivilWork"
                                                            class="form-control" style="width: 180px;"
                                                            value="{{ $blog->Quo_No_CivilWork }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="PR_Price_CivilWork" class="me-4"
                                                        style="width: 120px;">PR_Price_CivilWork</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="number" name="PR_Price_CivilWork"
                                                            class="form-control" placeholder="กรุณากรอกตัวเลข"
                                                            value="{{ $blog->PR_Price_CivilWork }}">
                                                    </div>
                                                </div>


                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Accept_PR_Date_CivilWork" class="me-4"
                                                        style="width: 150px;">Accept_PR_Date_CivilWork</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Accept_PR_Date_CivilWork"
                                                            name="Accept_PR_Date_CivilWork"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 150px;"
                                                            value="{{ old('Accept_PR_Date_CivilWork', $blog->Accept_PR_Date_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 4 -->

                                            <div class="col-md-3 d-flex align-items-center flex-wrap mt-0">

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="WO_No_CivilWork" class="me-4"
                                                        style="width: 100px;">WO_No_CivilWork</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="WO_No_CivilWork"
                                                            class="form-control" style="width: 180px;"
                                                            value="{{ $blog->WO_No_CivilWork }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="WO_Price_CivilWork" class="me-4"
                                                        style="width: 120px;">WO_Price_CivilWork</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="number" name="WO_Price_CivilWork"
                                                            class="form-control" placeholder="กรุณากรอกตัวเลข"
                                                            value="{{ $blog->WO_Price_CivilWork }}">
                                                        @error('WO_Price_CivilWork')
                                                            <span class="text text-danger ">{{$message}}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Banlace_CivilWork" class="me-4"
                                                        style="width: 100px;">Balance_CivilWork</label>
                                                    <div class="d-flex flex-column">
                                                        @php

                                                            // แปลงค่าเป็นตัวเลขก่อนการคำนวณ
                                                            $accept_1st_CivilWork = floatval(
                                                                $blog->Accept_1st_CivilWork,
                                                            );
                                                            $accept_2nd_CivilWork = floatval(
                                                                $blog->Accept_2nd_CivilWork,
                                                            );
                                                            $accept_3rd_CivilWork = floatval(
                                                                $blog->Accept_3rd_CivilWork,
                                                            );
                                                            $accept_4th_CivilWork = floatval(
                                                                $blog->Accept_4th_CivilWork,
                                                            );
                                                            $wo_price_CivilWork = floatval($blog->WO_Price_CivilWork);

                                                            // คำนวณผลรวม
                                                            $total =
                                                                $accept_1st_CivilWork +
                                                                $accept_2nd_CivilWork +
                                                                $accept_3rd_CivilWork +
                                                                $accept_4th_CivilWork;
                                                            $banlace = $blog->WO_Price_CivilWork;
                                                            $difference = $banlace - $total; // ไม่ให้ค่า difference ติดลบ
                                                        @endphp


                                                        @if (empty($banlace))
                                                        @elseif ($total > $banlace)
                                                            <input type="number" name="Banlace_CivilWork"
                                                                class="form-control"
                                                                value="{{ number_format($difference, 2, '.', '') }}"
                                                                readonly style="color: red">
                                                        @else
                                                            <!-- ถ้า $total เท่ากับ $banlace ให้แสดง "0" -->
                                                            <input type="number" name="Banlace_CivilWork"
                                                                class="form-control"
                                                                value="{{ number_format($difference, 2, '.', '') }}"
                                                                readonly style="color: red">
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>


                                            <!-- บรรทัด 5 -->

                                            <div class="col-md-7 d-flex align-items-center mt-4  ms-2">
                                                <label for="Accept_1st_CivilWork" class="me-4"
                                                    style="width: 120px;">Accept_1st_CivilWork</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_1st_CivilWork"
                                                        class="form-control" style="width: 140px;"
                                                        placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_1st_CivilWork }}">
                                                </div>


                                                <div class="form-group mb-4 ms-2 ">
                                                    <label for="Mail_1st_CivilWork" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="position-relative">
                                                        <input type="text" id="Mail_1st_CivilWork"
                                                            name="Mail_1st_CivilWork"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_1st_CivilWork', $blog->Mail_1st_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_1st_CivilWork" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_1st_CivilWork"
                                                            name="ERP_1st_CivilWork"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_1st_CivilWork', $blog->ERP_1st_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_1st_CivilWork))
                                                <div style="margin-top: -10px; margin-left: 5px;">
                                                    <img src="/checkmark.png" width="18" height="18"
                                                        alt="checkmark">
                                                </div>  
                                                @endif
												
                                            </div>

                                            <!-- บรรทัด 6 -->

                                            <div class="col-md-7 d-flex align-items-center  ms-2">
                                                <label for="Accept_2nd_CivilWork" class="me-4"
                                                    style="width: 120px;">Accept_2nd_CivilWork</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_2nd_CivilWork"
                                                        class="form-control" style="width: 140px;"
                                                        placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_2nd_CivilWork }}">
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_2nd_CivilWork" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_2nd_CivilWork"
                                                            name="Mail_2nd_CivilWork"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_2nd_CivilWork', $blog->Mail_2nd_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_2nd_CivilWork" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_2nd_CivilWork"
                                                            name="ERP_2nd_CivilWork"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_2nd_CivilWork', $blog->ERP_2nd_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												 @if (!empty($blog->Accept_2nd_CivilWork))
                                                    <div style="margin-top: -10px; margin-left: 5px;">
                                                    <img src="/checkmark.png" width="18" height="18"
                                                        alt="checkmark">
                                                </div>    
                                                @endif
												
                                            </div>

                                            <!-- บรรทัด 7 -->

                                            <div class="col-md-7 d-flex align-items-center  ms-2">
                                                <label for="Accept_3rd_CivilWork" class="me-4"
                                                    style="width: 120px;">Accept_3rd_CivilWork</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_3rd_CivilWork"
                                                        class="form-control" style="width: 140px;"
                                                        placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_3rd_CivilWork }}">
                                                </div>


                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_3rd_CivilWork" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_3rd_CivilWork"
                                                            name="Mail_3rd_CivilWork"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_3rd_CivilWork', $blog->Mail_3rd_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_3rd_CivilWork" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_3rd_CivilWork"
                                                            name="ERP_3rd_CivilWork"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_3rd_CivilWork', $blog->ERP_3rd_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_3rd_CivilWork))
                                                    <div style="margin-top: -10px; margin-left: 5px;">
                                                    <img src="/checkmark.png" width="18" height="18"
                                                        alt="checkmark">
                                                </div>    
                                                @endif
												
                                            </div>

                                            <!-- บรรทัด 8 -->

                                            <div class="col-md-7 d-flex align-items-center ms-2">
                                                <label for="Accept_4th_CivilWork" class="me-4"
                                                    style="width: 120px;">Accept_4th_CivilWork</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_4th_CivilWork"
                                                        class="form-control" style="width: 140px;"
                                                        placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_4th_CivilWork }}">
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_4th_CivilWork" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_4th_CivilWork"
                                                            name="Mail_4th_CivilWork"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_4th_CivilWork', $blog->Mail_4th_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_4th_CivilWork" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_4th_CivilWork"
                                                            name="ERP_4th_CivilWork"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_4th_CivilWork', $blog->ERP_4th_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_4th_CivilWork))
                                                    <div style="margin-top: -10px; margin-left: 5px;">
                                                    <img src="/checkmark.png" width="18" height="18"
                                                        alt="checkmark">
                                                </div>    
                                                @endif
												
                                            </div>


                                        </div>


                                    </div>
                                </div>
                            </div>
                        @endif

                    @endif


                    <!-- STATUS 2 -->

                    <!-- SAQ -->

                    @if (Auth::check())

                        @if (in_array(Auth::user()->status, [2]))


                            <!-- INVOICE -->

                            <input type="hidden" name="Quotation_IN"
                                value="{{ old('Quotation_IN', $blog->Quotation_IN) }}">
                            <input type="hidden" name="PO_No_IN" value="{{ old('PO_No_IN', $blog->PO_No_IN) }}">
                            <input type="hidden" name="PO_Amount_IN"
                                value="{{ old('PO_Amount_IN', $blog->PO_Amount_IN) }}">
                            <input type="hidden" name="Invoice1_IN"
                                value="{{ old('Invoice1_IN', $blog->Invoice1_IN) }}">
                            <input type="hidden" name="Amount1_IN"
                                value="{{ old('Amount1_IN', $blog->Amount1_IN) }}">
                            <input type="hidden" name="Invoice2_IN"
                                value="{{ old('Invoice2_IN', $blog->Invoice2_IN) }}">
                            <input type="hidden" name="Amount2_IN"
                                value="{{ old('Amount2_IN', $blog->Amount2_IN) }}">


                            <!-- TSSR -->
                            <input type="hidden" name="AssignedSubCTSSR"
                                value="{{ old('AssignedSubCTSSR', $blog->AssignedSubCTSSR) }}">
                            <input type="hidden" name="PlanTSSR" value="{{ old('PlanTSSR', $blog->PlanTSSR) }}">
                            <input type="hidden" name="ActualTSSR"
                                value="{{ old('ActualTSSR', $blog->ActualTSSR) }}">
                            <input type="hidden" name="SubName_TSSR"
                                value="{{ old('SubName_TSSR', $blog->SubName_TSSR) }}">
                            <input type="hidden" name="Quo_No_TSSR"
                                value="{{ old('Quo_No_TSSR', $blog->Quo_No_TSSR) }}">
                            <input type="hidden" name="PR_Price_TSSR"
                                value="{{ old('PR_Price_TSSR', $blog->PR_Price_TSSR) }}">
                            <input type="hidden" name="Accept_PR_Date_TSSR"
                                value="{{ old('Accept_PR_Date_TSSR', $blog->Accept_PR_Date_TSSR) }}">
                            <input type="hidden" name="WO_No_TSSR"
                                value="{{ old('WO_No_TSSR', $blog->WO_No_TSSR) }}">
                            <input type="hidden" name="WO_Price_TSSR"
                                value="{{ old('WO_Price_TSSR', $blog->WO_Price_TSSR) }}">


                            <input type="hidden" name="Accept_1st_TSSR"
                                value="{{ old('Accept_1st_TSSR', $blog->Accept_1st_TSSR) }}">
                            <input type="hidden" name="Mail_1st_TSSR"
                                value="{{ old('Mail_1st_TSSR', $blog->Mail_1st_TSSR) }}">
                            <input type="hidden" name="ERP_1st_TSSR"
                                value="{{ old('ERP_1st_TSSR', $blog->ERP_1st_TSSR) }}">

                            <input type="hidden" name="Accept_2nd_TSSR"
                                value="{{ old('Accept_2nd_TSSR', $blog->Accept_2nd_TSSR) }}">
                            <input type="hidden" name="Mail_2nd_TSSR"
                                value="{{ old('Mail_2nd_TSSR', $blog->Mail_2nd_TSSR) }}">
                            <input type="hidden" name="ERP_2nd_TSSR"
                                value="{{ old('ERP_2nd_TSSR', $blog->ERP_2nd_TSSR) }}">

                            <input type="hidden" name="Accept_3rd_TSSR"
                                value="{{ old('Accept_3rd_TSSR', $blog->Accept_3rd_TSSR) }}">
                            <input type="hidden" name="Mail_3rd_TSSR"
                                value="{{ old('Mail_3rd_TSSR', $blog->Mail_3rd_TSSR) }}">
                            <input type="hidden" name="ERP_3rd_TSSR"
                                value="{{ old('ERP_3rd_TSSR', $blog->ERP_3rd_TSSR) }}">

                            <input type="hidden" name="Accept_4th_TSSR"
                                value="{{ old('Accept_4th_TSSR', $blog->Accept_4th_TSSR) }}">
                            <input type="hidden" name="ERP_4th_TSSR"
                                value="{{ old('ERP_4th_TSSR', $blog->ERP_4th_TSSR) }}">
                            <input type="hidden" name="Banlace_TSSR"
                                value="{{ old('Banlace_TSSR', $blog->Banlace_TSSR) }}">


                            <!-- CIVIL WORK -->

                            <input type="hidden" name="AssignSubCivilfoundation"
                                value="{{ old('AssignSubCivilfoundation', $blog->AssignSubCivilfoundation) }}">
                            <input type="hidden" name="PlanCivilWorkFoundation"
                                value="{{ old('PlanCivilWorkFoundation', $blog->PlanCivilWorkFoundation) }}">
                            <input type="hidden" name="ActualCivilWorkTower"
                                value="{{ old('ActualCivilWorkTower', $blog->ActualCivilWorkTower) }}">
                            <input type="hidden" name="AssignCivilWorkTower"
                                value="{{ old('AssignCivilWorkTower', $blog->AssignCivilWorkTower) }}">
                            <input type="hidden" name="PlanInstallationRectifier"
                                value="{{ old('PlanInstallationRectifier', $blog->PlanInstallationRectifier) }}">
                            <input type="hidden" name="ActualInstallationRectifier"
                                value="{{ old('ActualInstallationRectifier', $blog->ActualInstallationRectifier) }}">
                            <input type="hidden" name="PlanACPower"
                                value="{{ old('PlanACPower', $blog->PlanACPower) }}">
                            <input type="hidden" name="ActualACPower"
                                value="{{ old('ActualACPower', $blog->ActualACPower) }}">
                            <input type="hidden" name="PlanACMeter"
                                value="{{ old('PlanACMeter', $blog->PlanACMeter) }}">

                            <input type="hidden" name="ActualACMeter"
                                value="{{ old('ActualACMeter', $blog->ActualACMeter) }}">
                            <input type="hidden" name="PAT" value="{{ old('PAT', $blog->PAT) }}">
                            <input type="hidden" name="DefPAT" value="{{ old('DefPAT', $blog->DefPAT) }}">
                            <input type="hidden" name="FAT" value="{{ old('FAT', $blog->FAT) }}">

                            <input type="hidden" name="Assigned_CivilWork"
                                value="{{ old('Assigned_CivilWork', $blog->Assigned_CivilWork) }}">
                            <input type="hidden" name="Plan_CivilWork"
                                value="{{ old('Plan_CivilWork', $blog->Plan_CivilWork) }}">
                            <input type="hidden" name="Actual_CivilWork"
                                value="{{ old('Actual_CivilWork', $blog->Actual_CivilWork) }}">
                            <input type="hidden" name="SubName_CivilWork"
                                value="{{ old('SubName_CivilWork', $blog->SubName_CivilWork) }}">
                            <input type="hidden" name="Quo_No_CivilWork"
                                value="{{ old('Quo_No_CivilWork', $blog->Quo_No_CivilWork) }}">
                            <input type="hidden" name="PR_Price_CivilWork"
                                value="{{ old('PR_Price_CivilWork', $blog->PR_Price_CivilWork) }}">
                            <input type="hidden" name="Accept_PR_Date_CivilWork"
                                value="{{ old('Accept_PR_Date_CivilWork', $blog->Accept_PR_Date_CivilWork) }}">
                            <input type="hidden" name="WO_No_CivilWork"
                                value="{{ old('WO_No_CivilWork', $blog->WO_No_CivilWork) }}">
                            <input type="hidden" name="WO_Price_CivilWork"
                                value="{{ old('WO_Price_CivilWork', $blog->WO_Price_CivilWork) }}">

                            <input type="hidden" name="Accept_1st_CivilWork"
                                value="{{ old('Accept_1st_CivilWork', $blog->Accept_1st_CivilWork) }}">
                            <input type="hidden" name="Mail_1st_CivilWork"
                                value="{{ old('Mail_1st_CivilWork', $blog->Mail_1st_CivilWork) }}">
                            <input type="hidden" name="ERP_1st_CivilWork"
                                value="{{ old('ERP_1st_CivilWork', $blog->ERP_1st_CivilWork) }}">

                            <input type="hidden" name="Accept_2nd_CivilWork"
                                value="{{ old('Accept_2nd_CivilWork', $blog->Accept_2nd_CivilWork) }}">
                            <input type="hidden" name="Mail_2nd_CivilWork"
                                value="{{ old('Mail_2nd_CivilWork', $blog->Mail_2nd_CivilWork) }}">
                            <input type="hidden" name="ERP_2nd_CivilWork"
                                value="{{ old('ERP_2nd_CivilWork', $blog->ERP_2nd_CivilWork) }}">


                            <input type="hidden" name="Accept_3rd_CivilWork"
                                value="{{ old('Accept_3rd_CivilWork', $blog->Accept_3rd_CivilWork) }}">
                            <input type="hidden" name="Mail_3rd_CivilWork"
                                value="{{ old('Mail_3rd_CivilWork', $blog->Mail_3rd_CivilWork) }}">
                            <input type="hidden" name="ERP_3rd_CivilWork"
                                value="{{ old('ERP_3rd_CivilWork', $blog->ERP_3rd_CivilWork) }}">


                            <input type="hidden" name="Accept_4th_CivilWork"
                                value="{{ old('Accept_4th_CivilWork', $blog->Accept_4th_CivilWork) }}">
                            <input type="hidden" name="Mail_4th_CivilWork"
                                value="{{ old('Mail_4th_CivilWork', $blog->Mail_4th_CivilWork) }}">
                            <input type="hidden" name="ERP_4th_CivilWork"
                                value="{{ old('ERP_4th_CivilWork', $blog->ERP_4th_CivilWork) }}">



                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseTwo" aria-expanded="false"
                                        aria-controls="collapseTwo">
                                        SAQ
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">


                                        <div class="container input-group mb-3 input-group-sm  py-3 row g-3 custom-form">

                                            <div class="status-row">

                                                <h4>SAQ</h4>

                                                <h5>Accept_1st
                                                    @if (!empty($blog->Accept_1st_SAQ))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_2nd
                                                    @if (!empty($blog->Accept_2nd_SAQ))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_3rd
                                                    @if (!empty($blog->Accept_3rd_SAQ))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_4th
                                                    @if (!empty($blog->Accept_4th_SAQ))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>
                                            </div>

                                            <!-- บรรทัด 1 -->
                                            <div class="col-md-6 d-flex align-items-center flex-wrap ">
                                                <div class="d-flex align-items-center mb-3 ms-2">

                                                    <label for="AssignedSubCSurveySAQ" class="me-4"
                                                        style="width: 150px;">AssignedSubCSurveySAQ</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="AssignedSubCSurveySAQ"
                                                            name="AssignedSubCSurveySAQ"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 180px;"
                                                            value="{{ old('AssignedSubCSurveySAQ', $blog->AssignedSubCSurveySAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="SubName_SAQ" class="me-4"
                                                        style="width: 100px;">SubName_SAQ</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" name="SubName_SAQ" class="form-control"
                                                            style="width: 200px;" value="{{ $blog->SubName_SAQ }}">
                                                    </div>
                                                </div>
                                            </div>



                                            <!-- บรรทัด 2 -->

                                            <div class="col-md-6 d-flex align-items-center flex-wrap mt-0 ">

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="PlanSurveySAQ" class="me-4"
                                                        style="width: 100px;">PlanSurveySAQ</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="PlanSurveySAQ" name="PlanSurveySAQ"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 160px;"
                                                            value="{{ old('PlanSurveySAQ', $blog->PlanSurveySAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="ActualSurveySAQ" class="me-4"
                                                        style="width: 100px;">ActualSurveySAQ</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ActualSurveySAQ"
                                                            name="ActualSurveySAQ" class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 160px;"
                                                            value="{{ old('ActualSurveySAQ', $blog->ActualSurveySAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 3 -->

                                            <div class="col-md-3 d-flex align-items-center flex-wrap mt-0">

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Quo_No_SAQ" class="me-4"
                                                        style="width: 100px;">Quo_No_SAQ</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="Quo_No_SAQ" class="form-control"
                                                            style="width: 160px;" value="{{ $blog->Quo_No_SAQ }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="PR_Price_SAQ" class="me-4"
                                                        style="width: 100px;">PR_Price_SAQ</label>
                                                    <div class="d-flex flex-column  ">
                                                        <input type="text" name="PR_Price_SAQ" class="form-control"
                                                            style="width: 160px;" placeholder="กรุณากรอกตัวเลข"
                                                            value="{{ $blog->PR_Price_SAQ }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Accept_PR_Date_SAQ" class="me-4"
                                                        style="width: 120px;">Accept_PR_Date_SAQ</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Accept_PR_Date_SAQ"
                                                            name="Accept_PR_Date_SAQ" style="width: 160px;"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('Accept_PR_Date_SAQ', $blog->Accept_PR_Date_SAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 4 -->

                                            <div class="col-md-3 d-flex align-items-center flex-wrap mt-0">

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="WO_No_SAQ" class="me-4"
                                                        style="width: 100px;">WO_No_SAQ</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="WO_No_SAQ" class="form-control"
                                                            style="width: 160px;" value="{{ $blog->WO_No_SAQ }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="WO_Price_SAQ" class="me-4"
                                                        style="width: 100px;">WO_Price_SAQ</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="number" name="WO_Price_SAQ" class="form-control"
                                                            style="width: 160px;" placeholder="กรุณากรอกตัวเลข"
                                                            value="{{ $blog->WO_Price_SAQ }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Banlace_SAQ" class="me-4"
                                                        style="width: 100px;">Balance_SAQ</label>
                                                    <div class="d-flex flex-column">
                                                        @php

                                                            // แปลงค่าเป็นตัวเลขก่อนการคำนวณ
                                                            $accept_1st_saq = floatval($blog->Accept_1st_SAQ);
                                                            $accept_2nd_saq = floatval($blog->Accept_2nd_SAQ);
                                                            $accept_3rd_saq = floatval($blog->Accept_3rd_SAQ);
                                                            $accept_4th_saq = floatval($blog->Accept_4th_SAQ);
                                                            $wo_price_saq = floatval($blog->WO_Price_SAQ);

                                                            // คำนวณผลรวม
                                                            $total =
                                                                $accept_1st_saq +
                                                                $accept_2nd_saq +
                                                                $accept_3rd_saq +
                                                                $accept_4th_saq;
                                                            $banlace = $blog->WO_Price_SAQ;
                                                            $difference = $banlace - $total; // ไม่ให้ค่า difference ติดลบ
                                                        @endphp


                                                        @if (empty($banlace))
                                                        @elseif ($total > $banlace)
                                                            <input type="number" name="Banlace_SAQ"
                                                                class="form-control"
                                                                value="{{ number_format($difference, 2, '.', '') }}"
                                                                readonly style="color: red">
                                                        @else
                                                            <!-- ถ้า $total เท่ากับ $banlace ให้แสดง difference เป็นทศนิยม 2 ตำแหน่ง -->
                                                            <input type="number" name="Banlace_SAQ"
                                                                class="form-control"
                                                                value="{{ number_format($difference, 2, '.', '') }}"
                                                                readonly style="color: red">
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>


                                            <!-- บรรทัด 5 -->

                                            <div class="col-md-7 d-flex align-items-center mt-4 ms-2">
                                                <label for="Accept_1st_SAQ" class="me-4"
                                                    style="width: 120px;">Accept_1st_SAQ</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_1st_SAQ" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_1st_SAQ }}">
                                                </div>


                                                <div class="form-group mb-4 ms-2 ">
                                                    <label for="Mail_1st_SAQ" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="position-relative">
                                                        <input type="text" id="Mail_1st_SAQ" name="Mail_1st_SAQ"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_1st_SAQ', $blog->Mail_1st_SAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_1st_SAQ" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_1st_SAQ" name="ERP_1st_SAQ"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_1st_SAQ', $blog->ERP_1st_SAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_1st_SAQ))
                                                    <div style="margin-top: -10px; margin-left: 5px;">
                                                        <img src="/checkmark.png" width="18" height="18"
                                                            alt="checkmark">
                                                    </div>
                                                @endif
												
                                            </div>

                                            <!-- บรรทัด 6 -->

                                            <div class="col-md-7 d-flex align-items-center  ms-2">
                                                <label for="Accept_2nd_SAQ" class="me-4"
                                                    style="width: 120px;">Accept_2nd_SAQ</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_2nd_SAQ" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_2nd_SAQ }}">


                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_2nd_SAQ" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_2nd_SAQ" name="Mail_2nd_SAQ"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_2nd_SAQ', $blog->Mail_2nd_SAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_2nd_SAQ" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_2nd_SAQ" name="ERP_2nd_SAQ"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_2nd_SAQ', $blog->ERP_2nd_SAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_2nd_SAQ))
                                                    <div style="margin-top: -10px; margin-left: 5px;">
                                                        <img src="/checkmark.png" width="18" height="18"
                                                            alt="checkmark">
                                                    </div>
                                                @endif
												
                                            </div>

                                            <!-- บรรทัด 7 -->

                                            <div class="col-md-7 d-flex align-items-center  ms-2">
                                                <label for="Accept_3rd_SAQ" class="me-4"
                                                    style="width: 120px;">Accept_3rd_SAQ</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_3rd_SAQ" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_3rd_SAQ }}">
                                                </div>


                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_3rd_SAQ" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_3rd_SAQ" name="Mail_3rd_SAQ"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_3rd_SAQ', $blog->Mail_3rd_SAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_3rd_SAQ" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_3rd_SAQ" name="ERP_3rd_SAQ"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_3rd_SAQ', $blog->ERP_3rd_SAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_3rd_SAQ))
                                                    <div style="margin-top: -10px; margin-left: 5px;">
                                                        <img src="/checkmark.png" width="18" height="18"
                                                            alt="checkmark">
                                                    </div>
                                                @endif
												
                                            </div>

                                            <!-- บรรทัด 8 -->

                                            <div class="col-md-7 d-flex align-items-center ms-2">
                                                <label for="Accept_4th_SAQ" class="me-4"
                                                    style="width: 120px;">Accept_4th_SAQ</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_4th_SAQ" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_4th_SAQ }}">
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_4th_SAQ" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_4th_SAQ" name="Mail_4th_SAQ"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_4th_SAQ', $blog->Mail_4th_SAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_4th_SAQ" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_4th_SAQ" name="ERP_4th_SAQ"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_4th_SAQ', $blog->ERP_4th_SAQ) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_4th_SAQ))
                                                    <div style="margin-top: -10px; margin-left: 5px;">
                                                        <img src="/checkmark.png" width="18" height="18"
                                                            alt="checkmark">
                                                    </div>
                                                @endif
												
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>



                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseThree" aria-expanded="false"
                                        aria-controls="collapseThree">
                                        CR
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse"
                                    aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">


                                        <div class="container input-group mb-3 input-group-sm  py-3 row g-3 custom-form">

                                            <div class="status-row">

                                                <h4>CR</h4>

                                                <h5>Accept_1st
                                                    @if (!empty($blog->Accept_1st_CR))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_2nd
                                                    @if (!empty($blog->Accept_2nd_CR))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_3rd
                                                    @if (!empty($blog->Accept_3rd_CR))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_4th
                                                    @if (!empty($blog->Accept_4th_CR))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>
                                            </div>

                                            <!-- บรรทัด 1 -->
                                            <div class="col-md-6 d-flex align-items-center flex-wrap ">
                                                <div class="d-flex align-items-center mb-3 ms-2">

                                                    <label for="AssignedSubCCR" class="me-4"
                                                        style="width: 120px;">AssignedSubCCR</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="AssignedSubCCR"
                                                            name="AssignedSubCCR" class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 160px;"
                                                            value="{{ old('AssignedSubCCR', $blog->AssignedSubCCR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="SubName_CR" class="me-4"
                                                        style="width: 100px;">SubName_CR</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="SubName_CR" class="form-control"
                                                            style="width: 200px;" value="{{ $blog->SubName_CR }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 2 -->

                                            <div class="col-md-6 d-flex align-items-center flex-wrap mt-0 ">

                                                <div div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="PlanCR" class="me-4"
                                                        style="width: 120px;">PlanCR</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="PlanCR" name="PlanCR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 160px;"
                                                            value="{{ old('PlanCR', $blog->PlanCR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="ActualCR" class="me-4"
                                                        style="width: 100px;">ActualCR</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ActualCR" name="ActualCR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 160px;"
                                                            value="{{ old('ActualCR', $blog->ActualCR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 3 -->

                                            <div class="col-md-3 d-flex align-items-center flex-wrap mt-0">

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Quo_No_CR" class="me-4"
                                                        style="width: 120px;">Quo_No_CR</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="Quo_No_CR" class="form-control"
                                                            style="width: 160px;" value="{{ $blog->Quo_No_CR }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="PR_Price_CR" class="me-4"
                                                        style="width: 100px;">PR_Price_CR</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="number" name="PR_Price_CR" class="form-control"
                                                            style="width: 160px;" placeholder="กรุณากรอกตัวเลข"
                                                            value="{{ $blog->PR_Price_CR }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Accept_PR_Date_CR" class="me-4"
                                                        style="width: 120px;">Accept_PR_Date_CR</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Accept_PR_Date_CR"
                                                            name="Accept_PR_Date_CR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 160px;"
                                                            value="{{ old('Accept_PR_Date_CR', $blog->Accept_PR_Date_CR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 4 -->

                                            <div class="col-md-3 d-flex align-items-center flex-wrap mt-0">

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="WO_No_CR" class="me-4"
                                                        style="width: 120px;">WO_No_CR</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="WO_No_CR" class="form-control"
                                                            style="width: 160px;" value="{{ $blog->WO_No_CR }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="WO_Price_CR" class="me-4"
                                                        style="width: 100px;">WO_Price_CR</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="number" name="WO_Price_CR" class="form-control"
                                                            style="width: 160px;" placeholder="กรุณากรอกตัวเลข"
                                                            value="{{ $blog->WO_Price_CR }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Banlace_CR" class="me-4"
                                                        style="width: 80px;">Balance_CR</label>
                                                    <div class="d-flex flex-column">
                                                        @php

                                                            // แปลงค่าเป็นตัวเลขก่อนการคำนวณ
                                                            $accept_1st_cr = floatval($blog->Accept_1st_CR);
                                                            $accept_2nd_cr = floatval($blog->Accept_2nd_CR);
                                                            $accept_3rd_cr = floatval($blog->Accept_3rd_CR);
                                                            $accept_4th_cr = floatval($blog->Accept_4th_CR);
                                                            $wo_price_cr = floatval($blog->WO_Price_CR);

                                                            // คำนวณผลรวม
                                                            $total =
                                                                $accept_1st_cr +
                                                                $accept_2nd_cr +
                                                                $accept_3rd_cr +
                                                                $accept_4th_cr;
                                                            $banlace = $blog->WO_Price_CR;
                                                            $difference = $banlace - $total; // ไม่ให้ค่า difference ติดลบ
                                                        @endphp



                                                        @if (empty($banlace))
                                                        @elseif ($total > $banlace)
                                                            <input type="number" name="Banlace_CR"
                                                                class="form-control"
                                                                value="{{ number_format($difference, 2, '.', '') }}"
                                                                readonly style="color: red">
                                                        @else
                                                            <!-- ถ้า $total เท่ากับ $banlace ให้แสดง "0" -->
                                                            <input type="number" name="Banlace_CR"
                                                                class="form-control"
                                                                value="{{ number_format($difference, 2, '.', '') }}"
                                                                readonly style="color: red">
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 4 -->

                                            <div class="col-md-7 d-flex align-items-center mt-4 ms-2">
                                                <label for="Accept_1st_CR" class="me-4"
                                                    style="width: 120px;">Accept_1st_CR</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_1st_CR" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_1st_CR }}">
                                                </div>


                                                <div class="form-group mb-4 ms-2 ">
                                                    <label for="Mail_1st_CR" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="position-relative">
                                                        <input type="text" id="Mail_1st_CR" name="Mail_1st_CR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_1st_CR', $blog->Mail_1st_CR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_1st_CR" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_1st_CR" name="ERP_1st_CR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_1st_CR', $blog->ERP_1st_CR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_1st_CR))
                                                    <div style="margin-top: -10px; margin-left: 5px;">
                                                        <img src="/checkmark.png" width="18" height="18"
                                                            alt="checkmark">
                                                    </div>
                                                @endif
												
                                            </div>

                                            <!-- บรรทัด 5 -->

                                            <div class="col-md-7 d-flex align-items-center ms-2">
                                                <label for="Accept_2nd_CR" class="me-4"
                                                    style="width: 120px;">Accept_2nd_CR</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_2nd_CR" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_2nd_CR }}">
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_2nd_CR" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_2nd_CR" name="Mail_2nd_CR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_2nd_CR', $blog->Mail_2nd_CR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_2nd_CR" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_2nd_CR" name="ERP_2nd_CR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_2nd_CR', $blog->ERP_2nd_CR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_2nd_CR))
                                                    <div style="margin-top: -10px; margin-left: 5px;">
                                                        <img src="/checkmark.png" width="18" height="18"
                                                            alt="checkmark">
                                                    </div>
                                                @endif
												
                                            </div>

                                            <!-- บรรทัด 6 -->

                                            <div class="col-md-7 d-flex align-items-center  ms-2">
                                                <label for="Accept_3rd_CR" class="me-4"
                                                    style="width: 120px;">Accept_3rd_CR</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_3rd_CR" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_3rd_CR }}">
                                                </div>


                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_3rd_CR" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_3rd_CR" name="Mail_3rd_CR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_3rd_CR', $blog->Mail_3rd_CR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_3rd_CR" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_3rd_CR" name="ERP_3rd_CR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_3rd_CR', $blog->ERP_3rd_CR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_3rd_CR))
                                                    <div style="margin-top: -10px; margin-left: 5px;">
                                                        <img src="/checkmark.png" width="18" height="18"
                                                            alt="checkmark">
                                                    </div>
                                                @endif
												
                                            </div>

                                            <!-- บรรทัด 7 -->

                                            <div class="col-md-7 d-flex align-items-center ms-2">
                                                <label for="Accept_4th_CR" class="me-4"
                                                    style="width: 120px;">Accept_4th_CR</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_4th_CR" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_4th_CR }}">
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_4th_CR" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_4th_CR" name="Mail_4th_CR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_4th_CR', $blog->Mail_4th_CR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_4th_CR" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_4th_CR" name="ERP_4th_CR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_4th_CR', $blog->ERP_4th_CR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_4th_CR))
                                                    <div style="margin-top: -10px; margin-left: 5px;">
                                                        <img src="/checkmark.png" width="18" height="18"
                                                            alt="checkmark">
                                                    </div>
                                                @endif
												
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>


                        @endif

                    @endif


                    <!-- STATUS 3 -->

                    @if (Auth::check())

                        @if (in_array(Auth::user()->status, [3]))

                            <!-- INVOICE -->

                            <input type="hidden" name="Quotation_IN"
                                value="{{ old('Quotation_IN', $blog->Quotation_IN) }}">
                            <input type="hidden" name="PO_No_IN" value="{{ old('PO_No_IN', $blog->PO_No_IN) }}">
                            <input type="hidden" name="PO_Amount_IN"
                                value="{{ old('PO_Amount_IN', $blog->PO_Amount_IN) }}">
                            <input type="hidden" name="Invoice1_IN"
                                value="{{ old('Invoice1_IN', $blog->Invoice1_IN) }}">
                            <input type="hidden" name="Amount1_IN"
                                value="{{ old('Amount1_IN', $blog->Amount1_IN) }}">
                            <input type="hidden" name="Invoice2_IN"
                                value="{{ old('Invoice2_IN', $blog->Invoice2_IN) }}">
                            <input type="hidden" name="Amount2_IN"
                                value="{{ old('Amount2_IN', $blog->Amount2_IN) }}">


                            <!-- SAQ -->
                            <input type="hidden" name="AssignedSubCSurveySAQ"
                                value="{{ old('AssignedSubCSurveySAQ', $blog->AssignedSubCSurveySAQ) }}">
                            <input type="hidden" name="SubName_SAQ"
                                value="{{ old('SubName_SAQ', $blog->SubName_SAQ) }}">
                            <input type="hidden" name="PlanSurveySAQ"
                                value="{{ old('PlanSurveySAQ', $blog->PlanSurveySAQ) }}">
                            <input type="hidden" name="ActualSurveySAQ"
                                value="{{ old('ActualSurveySAQ', $blog->ActualSurveySAQ) }}">
                            <input type="hidden" name="Quo_No_SAQ"
                                value="{{ old('Quo_No_SAQ', $blog->Quo_No_SAQ) }}">
                            <input type="hidden" name="PR_Price_SAQ"
                                value="{{ old('PR_Price_SAQ', $blog->PR_Price_SAQ) }}">
                            <input type="hidden" name="Accept_PR_Date_SAQ"
                                value="{{ old('Accept_PR_Date_SAQ', $blog->Accept_PR_Date_SAQ) }}">
                            <input type="hidden" name="WO_No_SAQ" value="{{ old('WO_No_SAQ', $blog->WO_No_SAQ) }}">
                            <input type="hidden" name="WO_Price_SAQ"
                                value="{{ old('WO_Price_SAQ', $blog->WO_Price_SAQ) }}">


                            <input type="hidden" name="Accept_1st_SAQ"
                                value="{{ old('Accept_1st_SAQ', $blog->Accept_1st_SAQ) }}">
                            <input type="hidden" name="Mail_1st_SAQ"
                                value="{{ old('Mail_1st_SAQ', $blog->Mail_1st_SAQ) }}">
                            <input type="hidden" name="ERP_1st_SAQ"
                                value="{{ old('ERP_1st_SAQ', $blog->ERP_1st_SAQ) }}">

                            <input type="hidden" name="Accept_2nd_SAQ"
                                value="{{ old('Accept_2nd_SAQ', $blog->Accept_2nd_SAQ) }}">
                            <input type="hidden" name="Mail_2nd_SAQ"
                                value="{{ old('Mail_2nd_SAQ', $blog->Mail_2nd_SAQ) }}">
                            <input type="hidden" name="ERP_2nd_SAQ"
                                value="{{ old('ERP_2nd_SAQ', $blog->ERP_2nd_SAQ) }}">

                            <input type="hidden" name="Accept_3rd_SAQ"
                                value="{{ old('Accept_3rd_SAQ', $blog->Accept_3rd_SAQ) }}">
                            <input type="hidden" name="Mail_3rd_SAQ"
                                value="{{ old('Mail_3rd_SAQ', $blog->Mail_3rd_SAQ) }}">
                            <input type="hidden" name="ERP_3rd_SAQ"
                                value="{{ old('ERP_3rd_SAQ', $blog->ERP_3rd_SAQ) }}">

                            <input type="hidden" name="Accept_4th_SAQ"
                                value="{{ old('Accept_4th_SAQ', $blog->Accept_4th_SAQ) }}">
                            <input type="hidden" name="Mail_4th_SAQ"
                                value="{{ old('Mail_4th_SAQ', $blog->Mail_4th_SAQ) }}">
                            <input type="hidden" name="ERP_4th_SAQ"
                                value="{{ old('ERP_4th_SAQ', $blog->ERP_4th_SAQ) }}">


                            <!-- CR -->

                            <input type="hidden" name="AssignedSubCCR"
                                value="{{ old('AssignedSubCCR', $blog->AssignedSubCCR) }}">
                            <input type="hidden" name="SubName_CR"
                                value="{{ old('SubName_CR', $blog->SubName_CR) }}">
                            <input type="hidden" name="PlanCR" value="{{ old('PlanCR', $blog->PlanCR) }}">
                            <input type="hidden" name="ActualCR" value="{{ old('ActualCR', $blog->ActualCR) }}">
                            <input type="hidden" name="Quo_No_CR" value="{{ old('Quo_No_CR', $blog->Quo_No_CR) }}">
                            <input type="hidden" name="PR_Price_CR"
                                value="{{ old('PR_Price_CR', $blog->PR_Price_CR) }}">
                            <input type="hidden" name="Accept_PR_Date_CR"
                                value="{{ old('Accept_PR_Date_CR', $blog->Accept_PR_Date_CR) }}">
                            <input type="hidden" name="WO_No_CR" value="{{ old('WO_No_CR', $blog->WO_No_CR) }}">
                            <input type="hidden" name="WO_Price_CR"
                                value="{{ old('WO_Price_CR', $blog->WO_Price_CR) }}">

                            <input type="hidden" name="Accept_1st_CR"
                                value="{{ old('Accept_1st_CR', $blog->Accept_1st_CR) }}">
                            <input type="hidden" name="Mail_1st_CR"
                                value="{{ old('Mail_1st_CR', $blog->Mail_1st_CR) }}">
                            <input type="hidden" name="ERP_1st_CR"
                                value="{{ old('ERP_1st_CR', $blog->ERP_1st_CR) }}">

                            <input type="hidden" name="Accept_2nd_CR"
                                value="{{ old('Accept_2nd_CR', $blog->Accept_2nd_CR) }}">
                            <input type="hidden" name="Mail_2nd_CR"
                                value="{{ old('Mail_2nd_CR', $blog->Mail_2nd_CR) }}">
                            <input type="hidden" name="ERP_2nd_CR"
                                value="{{ old('ERP_2nd_CR', $blog->ERP_2nd_CR) }}">

                            <input type="hidden" name="Accept_3rd_CR"
                                value="{{ old('Accept_3rd_CR', $blog->Accept_3rd_CR) }}">
                            <input type="hidden" name="Mail_3rd_CR"
                                value="{{ old('Mail_3rd_CR', $blog->Mail_3rd_CR) }}">
                            <input type="hidden" name="ERP_3rd_CR"
                                value="{{ old('ERP_3rd_CR', $blog->ERP_3rd_CR) }}">

                            <input type="hidden" name="Accept_4th_CR"
                                value="{{ old('Accept_4th_CR', $blog->Accept_4th_CR) }}">
                            <input type="hidden" name="Mail_4th_CR"
                                value="{{ old('Mail_4th_CR', $blog->Mail_4th_CR) }}">
                            <input type="hidden" name="ERP_4th_CR"
                                value="{{ old('ERP_4th_CR', $blog->ERP_4th_CR) }}">





                            <!-- TSSR -->

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingFour">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseFour" aria-expanded="false"
                                        aria-controls="collapseFour">
                                        TSSR
                                    </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse"
                                    aria-labelledby="headingFour" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">


                                        <div class="container input-group mb-3 input-group-sm  py-3 row g-3 custom-form">

                                            <div class="status-row">

                                                <h4>TSSR</h4>

                                                <h5>Accept_1st
                                                    @if (!empty($blog->Accept_1st_TSSR))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_2nd
                                                    @if (!empty($blog->Accept_2nd_TSSR))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_3rd
                                                    @if (!empty($blog->Accept_3rd_TSSR))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_4th
                                                    @if (!empty($blog->Accept_4th_TSSR))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>
                                            </div>

                                            <!-- บรรทัด 1 -->

                                            <div class="col-md-6 d-flex align-items-center flex-wrap ">

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="AssignedSubCTSSR" class="me-4"
                                                        style="width: 120px;">AssignedSubCTSSR</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="AssignedSubCTSSR"
                                                            name="AssignedSubCTSSR" class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 160px;"
                                                            value="{{ old('AssignedSubCTSSR', $blog->AssignedSubCTSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="SubName_TSSR" class="me-4"
                                                        style="width: 100px;">SubName_TSSR</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="SubName_TSSR" class="form-control"
                                                            style="width: 200px;" value="{{ $blog->SubName_TSSR }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 2 -->

                                            <div class="col-md-6 d-flex align-items-center flex-wrap mt-0 ">

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="PlanTSSR" class="me-4"
                                                        style="width: 120px;">PlanTSSR</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="PlanTSSR" name="PlanTSSR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 160px;"
                                                            value="{{ old('PlanTSSR', $blog->PlanTSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="ActualTSSR" class="me-4"
                                                        style="width: 100px;">ActualTSSR</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ActualTSSR" name="ActualTSSR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 160px;"
                                                            value="{{ old('ActualTSSR', $blog->ActualTSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 3 -->

                                            <div class="col-md-3 d-flex align-items-center flex-wrap mt-0">

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Quo_No_TSSR" class="me-4"
                                                        style="width: 120px;">Quo_No_TSSR</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="Quo_No_TSSR" class="form-control"
                                                            style="width: 160px;" value="{{ $blog->Quo_No_TSSR }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="PR_Price_TSSR" class="me-4"
                                                        style="width: 100px;">PR_Price_TSSR</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="number" name="PR_Price_TSSR"
                                                            class="form-control" style="width: 160px;"
                                                            placeholder="กรุณากรอกตัวเลข"
                                                            value="{{ $blog->PR_Price_TSSR }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Accept_PR_Date_TSSR" class="me-4"
                                                        style="width: 140px;">Accept_PR_Date_TSSR</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Accept_PR_Date_TSSR"
                                                            name="Accept_PR_Date_TSSR" style="width: 160px;"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('Accept_PR_Date_TSSR', $blog->Accept_PR_Date_TSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 4 -->

                                            <div class="col-md-3 d-flex align-items-center flex-wrap mt-0">
                                                <div class="d-flex align-items-center mb-3 ms-2">

                                                    <label for="WO_No_TSSR" class="me-4"
                                                        style="width: 120px;">WO_No_TSSR</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="WO_No_TSSR" class="form-control"
                                                            style="width: 160px;" value="{{ $blog->WO_No_TSSR }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="WO_Price_TSSR" class="me-4"
                                                        style="width: 100px;">WO_Price_TSSR</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="number" name="WO_Price_TSSR"
                                                            class="form-control" style="width: 160px;"
                                                            placeholder="กรุณากรอกตัวเลข"
                                                            value="{{ $blog->WO_Price_TSSR }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Banlace_TSSR" class="me-4"
                                                        style="width: 80px;">Banlace_TSSR</label>
                                                    <div class="d-flex flex-column">
                                                        @php

                                                            // แปลงค่าเป็นตัวเลขก่อนการคำนวณ
                                                            $accept_1st_TSSR = floatval($blog->Accept_1st_TSSR);
                                                            $accept_2nd_TSSR = floatval($blog->Accept_2nd_TSSR);
                                                            $accept_3rd_TSSR = floatval($blog->Accept_3rd_TSSR);
                                                            $accept_4th_TSSR = floatval($blog->Accept_4th_TSSR);
                                                            $wo_price_TSSR = floatval($blog->WO_Price_TSSR);

                                                            // คำนวณผลรวม
                                                            $total =
                                                                $accept_1st_TSSR +
                                                                $accept_2nd_TSSR +
                                                                $accept_3rd_TSSR +
                                                                $accept_4th_TSSR;
                                                            $banlace = $blog->WO_Price_TSSR;
                                                            $difference = $banlace - $total; // ไม่ให้ค่า difference ติดลบ
                                                        @endphp


                                                        @if (empty($banlace))
                                                        @else
                                                            @if ($total > $banlace)
                                                                <input type="number" name="Banlace_TSSR"
                                                                    class="form-control"
                                                                    value="{{ number_format($difference, 2, '.', '') }}"
                                                                    readonly style="color: red">
                                                            @else
                                                                <input type="number" name="Banlace_TSSR"
                                                                    class="form-control"
                                                                    value="{{ number_format($difference, 2, '.', '') }}"
                                                                    readonly style="color: red">
                                                            @endif
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 4 -->

                                            <div class="col-md-7 d-flex align-items-center mt-4  ms-2">
                                                <label for="Accept_1st_TSSR" class="me-4"
                                                    style="width: 120px;">Accept_1st_TSSR</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_1st_TSSR" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_1st_TSSR }}">
                                                </div>


                                                <div class="form-group mb-4 ms-2 ">
                                                    <label for="Mail_1st_TSSR" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="position-relative">
                                                        <input type="text" id="Mail_1st_TSSR" name="Mail_1st_TSSR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_1st_TSSR', $blog->Mail_1st_TSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_1st_TSSR" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_1st_TSSR" name="ERP_1st_TSSR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_1st_TSSR', $blog->ERP_1st_TSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_1st_TSSR))
                                                    <div style="margin-top: -10px; margin-left: 5px;">
                                                        <img src="/checkmark.png" width="18" height="18"
                                                            alt="checkmark">
                                                    </div>
                                                @endif
												
                                            </div>

                                            <!-- บรรทัด 5 -->

                                            <div class="col-md-7 d-flex align-items-center  ms-2">
                                                <label for="Accept_2nd_TSSR" class="me-4"
                                                    style="width: 120px;">Accept_2nd_TSSR</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_2nd_TSSR" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_2nd_TSSR }}">
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_2nd_TSSR" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_2nd_TSSR" name="Mail_2nd_TSSR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_2nd_TSSR', $blog->Mail_2nd_TSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_2nd_TSSR" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_2nd_TSSR" name="ERP_2nd_TSSR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_2nd_TSSR', $blog->ERP_2nd_TSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_2nd_TSSR))
                                                    <div style="margin-top: -10px; margin-left: 5px;">
                                                        <img src="/checkmark.png" width="18" height="18"
                                                            alt="checkmark">
                                                    </div>
                                                @endif
												
                                            </div>

                                            <!-- บรรทัด 6 -->

                                            <div class="col-md-7 d-flex align-items-center  ms-2">
                                                <label for="Accept_3rd_TSSR" class="me-4"
                                                    style="width: 120px;">Accept_3rd_TSSR</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_3rd_TSSR" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_3rd_TSSR }}">
                                                </div>


                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_3rd_TSSR" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_3rd_TSSR" name="Mail_3rd_TSSR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_3rd_TSSR', $blog->Mail_3rd_TSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_3rd_TSSR" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_3rd_TSSR" name="ERP_3rd_TSSR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_3rd_TSSR', $blog->ERP_3rd_TSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_3rd_TSSR))
                                                    <div style="margin-top: -10px; margin-left: 5px;">
                                                        <img src="/checkmark.png" width="18" height="18"
                                                            alt="checkmark">
                                                    </div>
                                                @endif
												
                                            </div>

                                            <!-- บรรทัด 7 -->

                                            <div class="col-md-7 d-flex align-items-center ms-2">
                                                <label for="Accept_4th_TSSR" class="me-4"
                                                    style="width: 120px;">Accept_4th_TSSR</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_4th_TSSR" class="form-control"
                                                        style="width: 140px;" placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_4th_TSSR }}">
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_4th_TSSR" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_4th_TSSR" name="Mail_4th_TSSR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_4th_TSSR', $blog->Mail_4th_TSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_4th_TSSR" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_4th_TSSR" name="ERP_4th_TSSR"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_4th_TSSR', $blog->ERP_4th_TSSR) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_4th_TSSR))
                                                    <div style="margin-top: -10px; margin-left: 5px;">
                                                        <img src="/checkmark.png" width="18" height="18"
                                                            alt="checkmark">
                                                    </div>
                                                @endif
												
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>



                            <!-- CivilWork -->

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingfive">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapsefive" aria-expanded="false"
                                        aria-controls="collapsefive">
                                        CivilWork
                                    </button>
                                </h2>
                                <div id="collapsefive" class="accordion-collapse collapse"
                                    aria-labelledby="headingfive" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">


                                        <div class="container input-group mb-3 input-group-sm  py-3 row g-3 custom-form">

                                            <div class="status-row">

                                                <h4>CivilWork</h4>

                                                <h5>Accept_1st
                                                    @if (!empty($blog->Accept_1st_CivilWork))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_2nd
                                                    @if (!empty($blog->Accept_2nd_CivilWork))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_3rd
                                                    @if (!empty($blog->Accept_3rd_CivilWork))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>

                                                <h5>Accept_4th
                                                    @if (!empty($blog->Accept_4th_CivilWork))
                                                        <span class="indicator has-data" title="Data Present"></span>
                                                    @else
                                                        <span class="indicator no-data" title="No Data"></span>
                                                    @endif
                                                </h5>
                                            </div>




                                            <!-- บรรทัด 1 -->

                                            <div class="col-md-6 d-flex align-items-center flex-wrap ">
                                                <!-- เพิ่ม flex-wrap เพื่อให้แต่ละบรรทัดอยู่แยกกัน -->
                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <!-- เพิ่ม margin-bottom เพื่อสร้างระยะห่างระหว่างแถว -->
                                                    <label for="AssignSubCivilfoundation" class="me-4"
                                                        style="width: 100px;">PlanCivilFoundation</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="AssignSubCivilfoundation"
                                                            style="width: 180px;" name="AssignSubCivilfoundation"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('AssignSubCivilfoundation', $blog->AssignSubCivilfoundation) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <!-- เพิ่ม margin-bottom เพื่อสร้างระยะห่างระหว่างแถว -->
                                                    <label for="PlanCivilWorkFoundation" class="me-4"
                                                        style="width: 150px;">ActualCivilWorkFoundation</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="PlanCivilWorkFoundation"
                                                            style="width: 180px;" name="PlanCivilWorkFoundation"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('PlanCivilWorkFoundation', $blog->PlanCivilWorkFoundation) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 2 -->

                                            <div class="col-md-6 d-flex align-items-center flex-wrap mt-0">

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <!-- ลด margin-bottom และใช้ margin-top ติดลบ -->
                                                    <label for="ActualCivilWorkTower" class="me-4"
                                                        style="width: 100px;">PlanCivilWorkTower</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ActualCivilWorkTower"
                                                            style="width: 180px;" name="ActualCivilWorkTower"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('ActualCivilWorkTower', $blog->ActualCivilWorkTower) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>


                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="AssignCivilWorkTower" class="me-4"
                                                        style="width: 150px;">ActualCivilWorkTower</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="AssignCivilWorkTower"
                                                            style="width: 180px;" name="AssignCivilWorkTower"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('AssignCivilWorkTower', $blog->AssignCivilWorkTower) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 3 -->

                                            <div class="col-md-6 d-flex align-items-center flex-wrap mt-0">
                                                <!-- ใช้ mt-n3 เพื่อขยับขึ้น -->
                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <!-- ลด margin-bottom และใช้ margin-top ติดลบ -->

                                                    <label for="PlanInstallationRectifier" class="me-4"
                                                        style="width: 130px;">PlanInstallationRectifier</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="PlanInstallationRectifier"
                                                            style="width: 150px;" name="PlanInstallationRectifier"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('PlanInstallationRectifier', $blog->PlanInstallationRectifier) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="ActualInstallationRectifier" class="me-4"
                                                        style="width: 150px;">ActualInstallationRectifier</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ActualInstallationRectifier"
                                                            style="width: 180px;" name="ActualInstallationRectifier"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('ActualInstallationRectifier', $blog->ActualInstallationRectifier) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 4 -->

                                            <div class="col-md-6 d-flex align-items-center flex-wrap mt-0">
                                                <!-- ใช้ mt-n3 เพื่อขยับขึ้น -->
                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <!-- ลด margin-bottom และใช้ margin-top ติดลบ -->
                                                    <label for="PlanACPower" class="me-4"
                                                        style="width: 100px;">PlanACPower</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="PlanACPower" name="PlanACPower"
                                                            style="width: 180px;" class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('PlanACPower', $blog->PlanACPower) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>


                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="ActualACPower" class="me-4"
                                                        style="width: 150px;">ActualACPower</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ActualACPower" name="ActualACPower"
                                                            style="width: 180px;" class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('ActualACPower', $blog->ActualACPower) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 5 -->

                                            <div class="col-md-6 d-flex align-items-center flex-wrap mt-0">
                                                <div class="d-flex align-items-center mb-3 ms-2">

                                                    <label for="PlanACMeter" class="me-4"
                                                        style="width: 100px;">PlanACMeter</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="PlanACMeter" name="PlanACMeter"
                                                            style="width: 180px;" class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('PlanACMeter', $blog->PlanACMeter) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="ActualACMeter" class="me-4"
                                                        style="width: 150px;">ActualACMeter</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ActualACMeter" name="ActualACMeter"
                                                            style="width: 180px;" class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('ActualACMeter', $blog->ActualACMeter) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 6 -->
                                            <div class="col-md-3 d-flex align-items-center flex-wrap mt-0">
                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="PAT" class="me-1"
                                                        style="width: 50px;">PAT</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="PAT" class="form-control"
                                                            value="{{ $blog->PAT }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="DefPAT" class="me-1"
                                                        style="width: 50px;">DefPAT</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="DefPAT" class="form-control"
                                                            value="{{ $blog->DefPAT }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="FAT" class="me-1"
                                                        style="width: 50px;">FAT</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="FAT" class="form-control"
                                                            value="{{ $blog->FAT }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="custom-divider">



                                            <!-- บรรทัด 1 -->

                                            <div class="col-md-6 d-flex align-items-center flex-wrap ">
                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Assigned_CivilWork" class="me-4"
                                                        style="width: 100px;">Assigned_CivilWork</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Assigned_CivilWork"
                                                            name="Assigned_CivilWork" style="width: 180px;"
                                                            class="form-control datepicker"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('Assigned_CivilWork', $blog->Assigned_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="SubName_CivilWork" class="me-4"
                                                        style="width: 120px;">SubName_CivilWork</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="SubName_CivilWork"
                                                            class="form-control" style="width: 200px;"
                                                            value="{{ $blog->SubName_CivilWork }}">
                                                    </div>
                                                </div>
                                            </div>


                                            <!-- บรรทัด 2 -->
                                            <div class="col-md-6 d-flex align-items-center flex-wrap mt-0">
                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Plan_CivilWork" class="me-4"
                                                        style="width: 100px;">Plan_CivilWork</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Plan_CivilWork"
                                                            name="Plan_CivilWork" style="width: 180px;"
                                                            class="form-control datepicker"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('Plan_CivilWork', $blog->Plan_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Actual_CivilWork" class="me-4"
                                                        style="width: 120px;">Actual_CivilWork</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Actual_CivilWork"
                                                            name="Actual_CivilWork" style="width: 160px;"
                                                            class="form-control datepicker"
                                                            placeholder="วันที่-เดือน-ปี"
                                                            value="{{ old('Actual_CivilWork', $blog->Actual_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 3 -->

                                            <div class="col-md-3 d-flex align-items-center flex-wrap mt-0">

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Quo_No_CivilWork" class="me-4"
                                                        style="width: 100px;">Quo_No_CivilWork</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="Quo_No_CivilWork"
                                                            class="form-control" style="width: 180px;"
                                                            value="{{ $blog->Quo_No_CivilWork }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="PR_Price_CivilWork" class="me-4"
                                                        style="width: 120px;">PR_Price_CivilWork</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="number" name="PR_Price_CivilWork"
                                                            class="form-control" placeholder="กรุณากรอกตัวเลข"
                                                            value="{{ $blog->PR_Price_CivilWork }}">
                                                    </div>
                                                </div>


                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Accept_PR_Date_CivilWork" class="me-4"
                                                        style="width: 150px;">Accept_PR_Date_CivilWork</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Accept_PR_Date_CivilWork"
                                                            name="Accept_PR_Date_CivilWork"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 150px;"
                                                            value="{{ old('Accept_PR_Date_CivilWork', $blog->Accept_PR_Date_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- บรรทัด 4 -->

                                            <div class="col-md-3 d-flex align-items-center flex-wrap mt-0">

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="WO_No_CivilWork" class="me-4"
                                                        style="width: 100px;">WO_No_CivilWork</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="text" name="WO_No_CivilWork"
                                                            class="form-control" style="width: 180px;"
                                                            value="{{ $blog->WO_No_CivilWork }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="WO_Price_CivilWork" class="me-4"
                                                        style="width: 120px;">WO_Price_CivilWork</label>
                                                    <div class="d-flex flex-column ">
                                                        <input type="number" name="WO_Price_CivilWork"
                                                            class="form-control" placeholder="กรุณากรอกตัวเลข"
                                                            value="{{ $blog->WO_Price_CivilWork }}">
                                                        @error('WO_Price_CivilWork')
                                                            <span class="text text-danger ">{{$message}}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-3 ms-2">
                                                    <label for="Banlace_CivilWork" class="me-4"
                                                        style="width: 100px;">Balance_CivilWork</label>
                                                    <div class="d-flex flex-column">
                                                        @php

                                                            // แปลงค่าเป็นตัวเลขก่อนการคำนวณ
                                                            $accept_1st_CivilWork = floatval(
                                                                $blog->Accept_1st_CivilWork,
                                                            );
                                                            $accept_2nd_CivilWork = floatval(
                                                                $blog->Accept_2nd_CivilWork,
                                                            );
                                                            $accept_3rd_CivilWork = floatval(
                                                                $blog->Accept_3rd_CivilWork,
                                                            );
                                                            $accept_4th_CivilWork = floatval(
                                                                $blog->Accept_4th_CivilWork,
                                                            );
                                                            $wo_price_CivilWork = floatval($blog->WO_Price_CivilWork);

                                                            // คำนวณผลรวม
                                                            $total =
                                                                $accept_1st_CivilWork +
                                                                $accept_2nd_CivilWork +
                                                                $accept_3rd_CivilWork +
                                                                $accept_4th_CivilWork;
                                                            $banlace = $blog->WO_Price_CivilWork;
                                                            $difference = $banlace - $total; // ไม่ให้ค่า difference ติดลบ
                                                        @endphp


                                                        @if (empty($banlace))
                                                        @elseif ($total > $banlace)
                                                            <input type="number" name="Banlace_CivilWork"
                                                                class="form-control"
                                                                value="{{ number_format($difference, 2, '.', '') }}"
                                                                readonly style="color: red">
                                                        @else
                                                            <!-- ถ้า $total เท่ากับ $banlace ให้แสดง "0" -->
                                                            <input type="number" name="Banlace_CivilWork"
                                                                class="form-control"
                                                                value="{{ number_format($difference, 2, '.', '') }}"
                                                                readonly style="color: red">
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>


                                            <!-- บรรทัด 5 -->

                                            <div class="col-md-7 d-flex align-items-center mt-4  ms-2">
                                                <label for="Accept_1st_CivilWork" class="me-4"
                                                    style="width: 120px;">Accept_1st_CivilWork</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_1st_CivilWork"
                                                        class="form-control" style="width: 140px;"
                                                        placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_1st_CivilWork }}">
                                                </div>


                                                <div class="form-group mb-4 ms-2 ">
                                                    <label for="Mail_1st_CivilWork" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="position-relative">
                                                        <input type="text" id="Mail_1st_CivilWork"
                                                            name="Mail_1st_CivilWork"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_1st_CivilWork', $blog->Mail_1st_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_1st_CivilWork" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_1st_CivilWork"
                                                            name="ERP_1st_CivilWork"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_1st_CivilWork', $blog->ERP_1st_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_1st_CivilWork))
                                                    <div style="margin-top: -10px; margin-left: 5px;">
                                                        <img src="/checkmark.png" width="18" height="18"
                                                            alt="checkmark">
                                                    </div>
                                                @endif
												
                                            </div>

                                            <!-- บรรทัด 6 -->

                                            <div class="col-md-7 d-flex align-items-center  ms-2">
                                                <label for="Accept_2nd_CivilWork" class="me-4"
                                                    style="width: 120px;">Accept_2nd_CivilWork</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_2nd_CivilWork"
                                                        class="form-control" style="width: 140px;"
                                                        placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_2nd_CivilWork }}">
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_2nd_CivilWork" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_2nd_CivilWork"
                                                            name="Mail_2nd_CivilWork"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_2nd_CivilWork', $blog->Mail_2nd_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_2nd_CivilWork" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_2nd_CivilWork"
                                                            name="ERP_2nd_CivilWork"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_2nd_CivilWork', $blog->ERP_2nd_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_2nd_CivilWork))
                                                    <div style="margin-top: -10px; margin-left: 5px;">
                                                        <img src="/checkmark.png" width="18" height="18"
                                                            alt="checkmark">
                                                    </div>
                                                @endif
												
                                            </div>

                                            <!-- บรรทัด 7 -->

                                            <div class="col-md-7 d-flex align-items-center  ms-2">
                                                <label for="Accept_3rd_CivilWork" class="me-4"
                                                    style="width: 120px;">Accept_3rd_CivilWork</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_3rd_CivilWork"
                                                        class="form-control" style="width: 140px;"
                                                        placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_3rd_CivilWork }}">
                                                </div>


                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_3rd_CivilWork" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_3rd_CivilWork"
                                                            name="Mail_3rd_CivilWork"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_3rd_CivilWork', $blog->Mail_3rd_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_3rd_CivilWork" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_3rd_CivilWork"
                                                            name="ERP_3rd_CivilWork"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_3rd_CivilWork', $blog->ERP_3rd_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_3rd_CivilWork))
                                                    <div style="margin-top: -10px; margin-left: 5px;">
                                                        <img src="/checkmark.png" width="18" height="18"
                                                            alt="checkmark">
                                                    </div>
                                                @endif
												
                                            </div>

                                            <!-- บรรทัด 8 -->

                                            <div class="col-md-7 d-flex align-items-center ms-2">
                                                <label for="Accept_4th_CivilWork" class="me-4"
                                                    style="width: 120px;">Accept_4th_CivilWork</label>
                                                <div class="d-flex flex-column ">
                                                    <input type="number" name="Accept_4th_CivilWork"
                                                        class="form-control" style="width: 140px;"
                                                        placeholder="กรุณากรอกตัวเลข"
                                                        value="{{ $blog->Accept_4th_CivilWork }}">
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="Mail_4th_CivilWork" class="me-4"
                                                        style="width: 120px;">Mail</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="Mail_4th_CivilWork"
                                                            name="Mail_4th_CivilWork"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('Mail_4th_CivilWork', $blog->Mail_4th_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 ms-2">
                                                    <label for="ERP_4th_CivilWork" class="me-4"
                                                        style="width: 120px;">ERP</label>
                                                    <div class="flex-grow-1 position-relative">
                                                        <input type="text" id="ERP_4th_CivilWork"
                                                            name="ERP_4th_CivilWork"
                                                            class="form-control datepicker pe-5"
                                                            placeholder="วันที่-เดือน-ปี" style="width: 140px;"
                                                            value="{{ old('ERP_4th_CivilWork', $blog->ERP_4th_CivilWork) }}">
                                                        <i
                                                            class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                                    </div>
                                                </div>
												
												@if (!empty($blog->Accept_4th_CivilWork))
                                                    <div style="margin-top: -10px; margin-left: 5px;">
                                                        <img src="/checkmark.png" width="18" height="18"
                                                            alt="checkmark">
                                                    </div>
                                                @endif
												
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    @endif


                    <!-- Additional -->

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingsix">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapsesix" aria-expanded="false" aria-controls="collapsesix">
                                ADDITIONAL
                            </button>
                        </h2>

                        <div id="collapsesix" class="accordion-collapse collapse" aria-labelledby="headingsix"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <div class="container input-group mb-3 input-group-sm  py-3 row g-3 custom-form">
                                    <div class="status-row">
                                        <h4>ADDITIONAL</h4>
                                    </div>

                                    <!-- บรรทัด 1 -->

                                    <div class="col-md-3 d-flex align-items-center flex-wrap mt-2">

                                        <div class="d-flex align-items-center mb-3 ms-2">
                                            <label for="pile_supplier"
                                                class="me-1
                                                style="width:100px;>Plie Supplier</label>
                                            <div class="d-flex flex-column ">
                                                <input type="text" name="pile_supplier" class="form-control"
                                                    style="width: 160px;" value="{{ $blog->pile_supplier }}">
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center mb-3 ms-2">
                                            <label for="price" class="ms-2" style="width: 50px;">Price</label>
                                            <div class="d-flex flex-column  ">
                                                <input type="number" name="price" class="form-control"
                                                    style="width: 120px;" placeholder="กรุณากรอกตัวเลข"
                                                    value="{{ $blog->price }}">
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center mb-3 ms-2">
                                            <label for="pile_supplier_accept_date" class="me-1 ms-2"
                                                style="width: 150px;">Plie Supplier Accept Date</label>
                                            <div class="flex-grow-1 position-relative">
                                                <input type="text" id="pile_supplier_accept_date"
                                                    name="pile_supplier_accept_date" style="width: 140px;"
                                                    class="form-control datepicker pe-5" placeholder="วันที่-เดือน-ปี"
                                                    value="{{ old('pile_supplier_accept_date', $blog->pile_supplier_accept_date) }}">
                                                <!--DATE -->
                                                <i
                                                    class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center mb-3 ms-2">
                                            <label for="wo_no" class="me-2 ms-2" style="width: 50px;">WO
                                                No.</label>
                                            <div class="d-flex flex-column ">
                                                <input type="text" name="wo_no" class="form-control"
                                                    style="width: 160px;" value="{{ $blog->wo_no }}">
                                            </div>
                                        </div>

                                    </div>



                                    <!-- บรรทัด 2 -->

                                    <div class="col-md-3 d-flex align-items-center flex-wrap mt-2">

                                        <div class="d-flex align-items-center mb-3 ms-2">
                                            <label for="accept_1" class="me-4" style="width: 100px;">Accept
                                                1</label>

                                            <div class="flex-grow-1 position-relative">
                                                <input type="text" id="accept_1" name="accept_1"
                                                    style="width: 160px;" class="form-control datepicker pe-5"
                                                    placeholder="วันที่-เดือน-ปี"
                                                    value="{{ old('accept_1', $blog->accept_1) }}"> <!--DATE -->
                                                <i
                                                    class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center mb-3 ms-2" style="gap: 3rem;">
                                            <label for="accept_2" class="me-2 " style="width: 100px;">Accept 2
                                            </label>

                                            <div class="flex-grow-1 position-relative">
                                                <input type="text" id="accept_2" name="accept_2"
                                                    style="width: 160px;" class="form-control datepicker pe-5"
                                                    placeholder="วันที่-เดือน-ปี"
                                                    value="{{ old('accept_2', $blog->accept_2) }}"> <!--DATE -->
                                                <i
                                                    class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center mb-3 " style="gap:17px;">
                                            <label for="accept_3" class="me-2 ms-2" style="width: 120px;">Accept 3
                                            </label>
                                            <div class="flex-grow-1 position-relative">
                                                <input type="text" id="accept_3" name="accept_3"
                                                    style="width: 150px;" class="form-control datepicker pe-5"
                                                    placeholder="วันที่-เดือน-ปี"
                                                    value="{{ old('accept_3', $blog->accept_3) }}">
                                                <i
                                                    class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- บรรทัด 3 -->

                                    <div class="col-md-3 d-flex align-items-center flex-wrap mt-2">

                                        <div class="d-flex align-items-center mb-3 ms-2">
                                            <label for="sub_extra_work" class="me-4" style="width: 100px;">Sub Extra
                                                Work</label>
                                            <div class="d-flex flex-column ">
                                                <input type="text" name="sub_extra_work" class="form-control"
                                                    style="width: 160px;" value="{{ $blog->sub_extra_work }}">
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center mb-3 ms-2">
                                            <label for="sub_extra_work_price" class="me-2" style="width: 150px;">Sub
                                                Extra Work Price </label>

                                            <div class="d-flex flex-column  ">
                                                <input type="number" name="sub_extra_work_price" class="form-control"
                                                    style="width: 160px;" placeholder="กรุณากรอกตัวเลข"
                                                    value="{{ $blog->sub_extra_work_price }}">
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center mb-3 ms-2">
                                            <label for="extra_work_accept_date" class="me-1"
                                                style="width: 140px;">Sub Extra Work Date </label>


                                            <div class="flex-grow-1 position-relative">
                                                <input type="text" id="extra_work_accept_date"
                                                    name="extra_work_accept_date" style="width: 150px;"
                                                    class="form-control datepicker pe-5" placeholder="วันที่-เดือน-ปี"
                                                    value="{{ old('extra_work_accept_date', $blog->extra_work_accept_date) }}">
                                                <!--DATE -->
                                                <i
                                                    class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                            </div>
                                        </div>

                                    </div>


                                    <!-- บรรทัด 4 -->
                                    <div class="col-md-3 d-flex align-items-center flex-wrap mt-2">

                                        <div class="d-flex align-items-center mb-3 ms-2">
                                            <label for="build_permit" class="me-1" style="width: 120px;">Build
                                                Permit Price</label>
                                            <div class="d-flex flex-column  ">
                                                <input type="number" name="build_permit" class="form-control"
                                                    style="width: 160px;" placeholder="กรุณากรอกตัวเลข"
                                                    value="{{ $blog->build_permit }}">
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center mb-3 ms-2 " style="gap: 2rem">
                                            <label for="payment_to" class="me-1" style="width: 120px;">Payment
                                                to</label>
                                            <div class="d-flex flex-column ">
                                                <input type="text" name="payment_to" class="form-control"
                                                    value="{{ $blog->payment_to }}">
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center mb-3 ms-2">
                                            <label for="payment_date" class="me-2 ms-1" style="width: 131px;">Payment
                                                Date</label>

                                            <div class="flex-grow-1 position-relative">
                                                <input type="text" id="payment_date" name="payment_date"
                                                    style="width: 150px;" class="form-control datepicker pe-5"
                                                    placeholder="วันที่-เดือน-ปี"
                                                    value="{{ old('payment_date', $blog->payment_date) }}">
                                                <i
                                                    class="bi bi-calendar position-absolute top-50 end-0 translate-middle-y pe-2"></i>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>


                </div>


                <div class="container text-center mb-3">
                    <!-- Loader (ซ่อนตอนแรก) -->
                    <div id="loadingSpinner" class="hidden mt-3 text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">กำลังอัปเดตข้อมูล...</span>
                        </div>
                        <p class="text-sm text-gray-600">โปรดรอสักครู่ ...</p>
                    </div>

                    <!-- ปุ่มกดอัปเดต -->
                    <input type="submit" id="updateBtn" class="btn btn-success my-3" value="อัปเดต">
                    <a href="/blog" id="cancelBtn" class="btn btn-danger">หน้าแรก</a>
                </div>


                <!-- Date -->
                <script>
                    $(document).ready(function() {
                        $('.datepicker').datepicker({
                            format: 'dd-mm-yyyy',
                            autoclose: true,
                            todayHighlight: true,
                            clearBtn: true,
                            startDate: '01-01-2000', // เริ่มวันที่ 1 มกราคม ค.ศ. 2000
                            endDate: '31-12-2100' // สิ้นสุดวันที่ 31 ธันวาคม ค.ศ. 2100
                        });
                    });
                </script>

                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        // เปิด modal เมื่อโหลดหน้า
                        var myModal = new bootstrap.Modal(document.getElementById('successModal'), {
                            keyboard: false
                        });
                        myModal.show(); // แสดง Modal
                    });
                </script>


                <!-- JavaScript -->
                <script>
                    document.getElementById("updateForm").addEventListener("submit", function(event) {
                        let confirmUpdate = confirm("ยืนยันการอัปเดตหรือไม่?");
                        if (!confirmUpdate) {
                            event.preventDefault(); // ยกเลิกการส่งฟอร์ม ถ้าผู้ใช้กด Cancel
                            return;
                        }

                        // ซ่อนปุ่มอัปเดตและปุ่มยกเลิก
                        document.getElementById("updateBtn").style.display = 'none';
                        document.getElementById("cancelBtn").style.display = 'none';

                        // แสดง Loader
                        document.getElementById("loadingSpinner").classList.remove("hidden");

                    });
                </script>

                <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
                
				<script>
                    document.querySelectorAll('input[placeholder="กรุณากรอกตัวเลข"]').forEach(function(element) {
                        // เพิ่ม step="0.01" และ min="0" ไม่ให้กรอกค่าติดลบ
                        element.setAttribute('step', '0.01');
                        element.setAttribute('min', '0');

                        element.addEventListener('blur', function(e) {
                            let value = e.target.value;
                            if (value) {
                                value = parseFloat(value);
                                if (value < 0) {
                                    // ถ้าไม่ต้องการให้มีค่าติดลบเลย
                                    e.target.value = '';
                                    alert("ห้ามกรอกค่าติดลบ");
                                } else {
                                    e.target.value = value.toFixed(2); // ปัดเศษทศนิยม 2 ตำแหน่ง
                                }
                            }
                        });
                    });
                </script>


        </form>
    </div>

@endsection