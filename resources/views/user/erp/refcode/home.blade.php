@extends('layouts.user')
@section('title', 'ERP Refcode')
@section('content')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <!-- sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">
    <!-- Load Font Awesome for Icons -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
            <div class="flex justify-end mr-3 ml-3">
                <button onclick="openModal()"
                    class="flex items-center bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow transition duration-200">

                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5 20h14v-2H5v2zm7-14l-5 5h3v4h4v-4h3l-5-5z"></path>
                    </svg>

                    Import
                </button>
            </div>



            <!-- Modal -->
            <div id="refcodeModal"
                class="fixed inset-0 bg-black bg-opacity-40 
           flex items-center justify-center z-50
           opacity-0 invisible transition-opacity duration-300">

                <div id="modalContent"
                    class="bg-white w-[600px] rounded-xl shadow-xl p-6 relative
               transform scale-95 transition-all duration-300">

                    <!-- ปุ่มปิด -->
                    <button onclick="closeModal()" class="absolute top-3 right-3 text-gray-500 hover:text-red-500">
                        ✕
                    </button>

                    <h2 class="text-lg font-medium font-sarabun text-gray-700 mb-4">
    Import Refcode
</h2>


                    <form action="{{ route('refcode.import') }}" method="POST" enctype="multipart/form-data" id="csvForm"
                        class="flex gap-2">
                        @csrf

                        <input type="file" name="xlsx_file_add" accept=".xlsx" required
                            class="border border-gray-300 rounded px-2 py-1 w-full">

                        <button type="submit" id="uploadButton"
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                            บันทึก
                        </button>
                    </form>

                    <!-- Loader -->
                    <div id="loadingSpinner" class="hidden mt-4 text-center text-sm text-gray-600">
                        กำลังบันทึกไฟล์... โปรดรอสักครู่
                    </div>
                </div>
            </div>

            <script>
                function openModal() {
                    const modal = document.getElementById('refcodeModal');
                    const content = document.getElementById('modalContent');

                    modal.classList.remove('invisible', 'opacity-0');
                    modal.classList.add('opacity-100');

                    setTimeout(() => {
                        content.classList.remove('scale-95');
                        content.classList.add('scale-100');
                    }, 50);
                }

                function closeModal() {
                    const modal = document.getElementById('refcodeModal');
                    const content = document.getElementById('modalContent');

                    content.classList.remove('scale-100');
                    content.classList.add('scale-95');

                    modal.classList.remove('opacity-100');
                    modal.classList.add('opacity-0');

                    setTimeout(() => {
                        modal.classList.add('invisible');
                    }, 300);
                }
            </script>
        @endif
    @endif




    
    <!-- ตาราง Showdata เก่าเปิดมาเจอเลย-->
    <div
        class="mt-2 ml-2 w-[98%] max-h-[600px] overflow-x-auto overflow-y-auto rounded-lg border border-gray-300 shadow-lg">
        <table class="w-full h-full border-collapse">
            
            <thead class="sticky top-0 bg-white shadow-md">
                <tr class="text-xs text-center h-[40px]">


                    <th class="bg-blue-950 text-neutral-50 px-2 py-1">
                        <div class="flex flex-col items-center">
                            <span>No</span>
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Project No.</span> 
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span class="now w">Project Name</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Ref. Code</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Project Type</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Construction Status</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Active (Y/N)</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Unit Type</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Owner</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Item.</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Currency</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Budget Contract</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Group</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Control Budget</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Control BOQ</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Project Contract</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>GL IC</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>A/C IC Control</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>A/C IC Secondary</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Sale</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Project Manager</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Engineer</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Project Director</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Section/Department Director</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Division Director</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Approve BG</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Signing No.</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Amount</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Proj. Budget</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Add By</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Add Date</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Edit By</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Edit Date</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Unit RE</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>runproject</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>hpre_event</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>proc2</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>proc3</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>proc4</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>proc5</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>pre_des_s</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>bank_code</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>pr_empno</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>projcenter</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>projno</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>projdate</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>projyear</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>totadd</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>areaqty</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>unitcode</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>unitstatus</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>revno</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>salecode</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>acvat</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>book2_no</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>book2</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>pre_thi</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>customer_code</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>plugin</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>type_code</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>area</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>allocate_status</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>projname2</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>SEC_empno</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>DIV_empno</span>
                            
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>proj_location</span>                 
                        </div>
                    </th>
     

                </tr>
            </thead>



            <tbody class="text-xs text-center bg-white" id="oldDataBody">
                @foreach ($importrefcode as $item)
                    <tr class="hover:bg-red-100 hover:text-red-600">
                        <td class="whitespace-nowrap">{{ $item->no }}</td>
                        <td class="whitespace-nowrap">{{ $item->project_no }}</td>
                        <td class="whitespace-nowrap">{{ $item->project_name }}</td>
                        <td class="whitespace-nowrap">{{ $item->ref_code }}</td>
                        <td class="whitespace-nowrap">{{ $item->project_type }}</td>
                        <td class="whitespace-nowrap">{{ $item->construction_status }}</td>
                        <td class="whitespace-nowrap">{{ $item->active_y_n }}</td>
                        <td class="whitespace-nowrap">{{ $item->unit_type }}</td>
                        <td class="whitespace-nowrap">{{ $item->owner }}</td>
                        <td class="whitespace-nowrap">{{ $item->item }}</td>

                        <td class="whitespace-nowrap">{{ $item->currency }}</td>
                        <td class="whitespace-nowrap">{{ $item->budget_contract }}</td>
                        <td class="whitespace-nowrap">{{ $item->group_group }}</td>
                        <td class="whitespace-nowrap">{{ $item->control_budget }}</td>
                        <td class="whitespace-nowrap">{{ $item->control_boq }}</td>
                        <td class="whitespace-nowrap">{{ $item->project_contract }}</td>
                        <td class="whitespace-nowrap">{{ $item->gl_ic }}</td>
                        <td class="whitespace-nowrap">{{ $item->ac_ic_control }}</td>
                        <td class="whitespace-nowrap">{{ $item->ac_ic_secondary }}</td>
                        <td class="whitespace-nowrap">{{ $item->sale }}</td>

                        <td class="whitespace-nowrap">{{ $item->project_manager }}</td>
                        <td class="whitespace-nowrap">{{ $item->engineer }}</td>
                        <td class="whitespace-nowrap">{{ $item->project_director }}</td>
                        <td class="whitespace-nowrap">{{ $item->section_director }}</td>
                        <td class="whitespace-nowrap">{{ $item->division_director }}</td>
                        <td class="whitespace-nowrap">{{ $item->approve_bg }}</td>
                        <td class="whitespace-nowrap">{{ $item->signing_no }}</td>
                        <td class="whitespace-nowrap">{{ $item->amount }}</td>
                        <td class="whitespace-nowrap">{{ $item->proj_budget }}</td>
                        <td class="whitespace-nowrap">{{ $item->add_by }}</td>
                       
                        <td class="whitespace-nowrap">{{ $item->add_date }}</td>
                        <td class="whitespace-nowrap">{{ $item->edit_by }}</td>
                        <td class="whitespace-nowrap">{{ $item->edit_date }}</td>
                        <td class="whitespace-nowrap">{{ $item->unit_re }}</td>
                        <td class="whitespace-nowrap">{{ $item->runproject }}</td>
                        <td class="whitespace-nowrap">{{ $item->hpre_event }}</td>
                        <td class="whitespace-nowrap">{{ $item->proc2 }}</td>
                        <td class="whitespace-nowrap">{{ $item->proc3 }}</td>
                        <td class="whitespace-nowrap">{{ $item->proc4 }}</td>
                        <td class="whitespace-nowrap">{{ $item->proc5 }}</td>
                        
                        <td class="whitespace-nowrap">{{ $item->pre_des_s }}</td>
                        <td class="whitespace-nowrap">{{ $item->bank_code }}</td>
                        <td class="whitespace-nowrap">{{ $item->pr_empno }}</td>
                        <td class="whitespace-nowrap">{{ $item->projcenter }}</td>
                        <td class="whitespace-nowrap">{{ $item->projno }}</td>
                        <td class="whitespace-nowrap">{{ $item->projdate }}</td>
                        <td class="whitespace-nowrap">{{ $item->projyear }}</td>
                        <td class="whitespace-nowrap">{{ $item->totadd }}</td>
                        <td class="whitespace-nowrap">{{ $item->areaqty }}</td>
                        <td class="whitespace-nowrap">{{ $item->unitcode }}</td>
                        <td class="whitespace-nowrap">{{ $item->unitstatus }}</td>
                        
                        <td class="whitespace-nowrap">{{ $item->revno }}</td>
                        <td class="whitespace-nowrap">{{ $item->salecode }}</td>
                        <td class="whitespace-nowrap">{{ $item->acvat }}</td>
                        <td class="whitespace-nowrap">{{ $item->book2_no }}</td>
                        <td class="whitespace-nowrap">{{ $item->book2 }}</td>
                        <td class="whitespace-nowrap">{{ $item->pre_thi }}</td>
                        <td class="whitespace-nowrap">{{ $item->customer_code }}</td>
                        <td class="whitespace-nowrap">{{ $item->plugin }}</td>
                        <td class="whitespace-nowrap">{{ $item->type_code }}</td>
                        <td class="whitespace-nowrap">{{ $item->area }}</td>
                        
                        <td class="whitespace-nowrap">{{ $item->allocate_status }}</td>
                        <td class="whitespace-nowrap">{{ $item->projname2 }}</td>
                        <td class="whitespace-nowrap">{{ $item->sec_empno }}</td>
                        <td class="whitespace-nowrap">{{ $item->div_empno }}</td>
                        <td class="whitespace-nowrap">{{ $item->proj_location }}</td>
                        

                    </tr>
                @endforeach

            </tbody>

        </table>
    </div>


    


@endsection
