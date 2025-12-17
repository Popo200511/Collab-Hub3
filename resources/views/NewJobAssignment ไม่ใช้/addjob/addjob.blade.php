@extends('layouts.Tailwind')

<script>
    const notifBtn = document.getElementById('notification-button');
    const notifDropdown = document.getElementById('notification-dropdown');

    notifBtn.addEventListener('click', () => {
        notifDropdown.classList.toggle('hidden');
    });

    document.addEventListener('click', function(event) {
        if (!notifBtn.contains(event.target) && !notifDropdown.contains(event.target)) {
            notifDropdown.classList.add('hidden');
        }
    });
</script>



@section('title', 'NEW JOB ASSIGNMENT')

@section('content')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
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
    
    <div class="flex h-[calc(100vh-64px)] overflow-hidden">
        <!-- Aside Sidebar -->

        <!-- Main Content -->
        <main class="flex-1 p-6 bg-gray-100 overflow-y-auto">

            <div class="flex justify-between items-center bg-white p-4 rounded-xl mb-6 shadow-md">

                <!-- กลุ่มปุ่มด้านซ้าย -->
                <div class="flex space-x-2">

                    <button id="openModalBtn"
                        class="rounded-md bg-blue-950 py-2 px-4 text-white hover:bg-slate-700 font-bold shadow-md transition">
                        Add New Job
                    </button>

                    <button id="openModalBtn2"
                        class="rounded-md bg-blue-950 py-2 px-4 text-white hover:bg-slate-700 font-bold shadow-md transition">
                        Import New Job
                    </button>
                </div>


                <a href="/export-wo"
                    class="bg-green-600 text-white px-4 py-2 rounded-md font-bold hover:bg-green-700 transition">
                    Export Visible Data
                </a>

            </div>

            <!-- Modal Import New Job -->
            <div id="importModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 z-50">

                <!-- Modal Content -->
                <div
                    class="bg-white rounded-lg shadow-lg w-11/12 max-w-lg p-6 relative transform scale-95 transition-all duration-300">

                    <!-- Header -->
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold text-slate-800">Import New Job</h2>
                        <button id="closeImportModal"
                            class="text-slate-500 hover:text-slate-700 text-2xl font-bold">&times;</button>
                    </div>

                    <!-- Download Template -->
                    <a href="{{ asset('templates/Add_job_template.xlsx') }}" download
                        class="mb-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-md font-semibold hover:bg-blue-700 transition">
                        Download Import Template
                    </a>


                    <!-- Form -->
                    <form method="POST" action="{{ route('addjob.importnewjob') }}" enctype="multipart/form-data"
                        name="xlsx_file_add">
                        @csrf

                        <label class="block mb-2 font-semibold text-slate-700"> Upload File <span
                                class="text-red-600">(.xlsx)</span> </label>

                        <input type="file" name="xlsx_file_add" accept=".xlsx"
                            class="w-full border border-gray-300 rounded-md p-2 mb-4 focus:ring focus:ring-blue-300"
                            required>

                        <!-- Buttons -->
                        <div class="flex justify-end space-x-2">
                            <button type="button" id="cancelImportBtn"
                                class="rounded-md border py-2 px-4 font-semibold text-slate-600 hover:bg-slate-100">
                                Cancel
                            </button>
                            <button type="submit"
                                class="rounded-md bg-green-600 text-white font-semibold py-2 px-4 hover:bg-green-700 shadow-md">
                                Import
                            </button>
                        </div>
                    </form>

                </div>
            </div>

            <!-- Modal แสดงข้อมูล Import -->

            <!-- Modal แสดงข้อมูล ที่ import เข้ามา -->
            @if (!empty($dataToSave) && (is_array($dataToSave) || is_object($dataToSave)))
                <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                    <div
                        class="relative w-full max-w-[70vw] mx-4 bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col h-[70vh]">

                        <!-- Header -->
                        <div class="flex justify-between items-center px-4 md:px-6 py-3 border-b bg-blue-950">
                            <h2 class="text-md md:text-lg text-white">
                                <h2 class="text-white" style="font-family: 'Sarabun', sans-serif;">
                                    จำนวนข้อมูลที่ import เข้า {{ count($dataToSave) }} รายการ
                                </h2>

                            </h2>
                            <a href="addjob" class="text-white hover:text-gray-200 transition text-xl">✖</a>
                        </div>
                        <!-- Body -->
                        <div class="flex-1 overflow-y-auto p-2 min-h-[200px]">

                            <!-- ตารางแสดงข้อมูล -->
                            <div class="w-full h-full overflow-y-auto">
                                <table class="w-max md:w-full text-sm border-collapse">
                                    <thead class="sticky top-0 bg-blue-50 shadow-sm ">
                                        <tr class="text-sm text-center text-gray-700">
                                            <th class="px-2 py-3 border">Site Code</th>
                                            <th class="px-2 py-3 border">Site Name</th>
                                            <th class="px-2 py-3 border">Job Description</th>
                                            <th class="px-2 py-3 border">Project Code</th>
                                            <th class="px-2 py-3 border">Office Code</th>
                                            <th class="px-2 py-3 border">Customer Region</th>
                                            <th class="px-2 py-3 border">Estimated Revenue</th>
                                            <th class="px-2 py-3 border">Estimated Service Cost</th>
                                            <th class="px-2 py-3 border">Estimated Material Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                        @foreach ($dataToSave as $data)
                                            <tr class="hover:bg-gray-100 transition">

                                                <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                                    {{ $data['Site_Code'] ?? '-' }}</td>
                                                <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                                    {{ $data['Site_Name'] ?? '-' }}</td>
                                                <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                                    {{ $data['Job_Description'] ?? '-' }}</td>
                                                <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                                    {{ $data['Project_Code'] ?? '-' }}</td>
                                                <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                                    {{ $data['Office_Code'] ?? '-' }}</td>
                                                <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                                    {{ $data['Customer_Region'] ?? '-' }}</td>
                                                <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                                    {{ $data['Estimated_Revenue'] ?? '-' }}</td>
                                                <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                                    {{ $data['Estimated_Service_Cost'] ?? '-' }}</td>
                                                <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                                    {{ $data['Estimated_Material_Cost'] ?? '-' }}</td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="flex justify-end items-center px-6 py-2 border-t bg-gray-50">

                            <a href="addjob"
                                class="bg-red-500 text-white px-4 py-2 rounded-lg shadow hover:bg-red-600 transition">
                                ยกเลิก
                            </a>

                            <form action="{{ route('addjob.saveimportnewjob') }}" id="save" method="POST"
                                class="flex items-center gap-2 ml-2">
                                @csrf

                                @foreach ($dataToSave as $index => $data)
                                    <input type="hidden" name="dataToSave[{{ $index }}][Site_Code]"
                                        value="{{ $data['Site_Code'] ?? '' }}">
                                    <input type="hidden" name="dataToSave[{{ $index }}][Site_Name]"
                                        value="{{ $data['Site_Name'] ?? '' }}">
                                    <input type="hidden" name="dataToSave[{{ $index }}][Job_Description]"
                                        value="{{ $data['Job_Description'] ?? '' }}">
                                    <input type="hidden" name="dataToSave[{{ $index }}][Project_Code]"
                                        value="{{ $data['Project_Code'] ?? '' }}">
                                    <input type="hidden" name="dataToSave[{{ $index }}][Office_Code]"
                                        value="{{ $data['Office_Code'] ?? '' }}">
                                    <input type="hidden" name="dataToSave[{{ $index }}][Customer_Region]"
                                        value="{{ $data['Customer_Region'] ?? '' }}">
                                    <input type="hidden" name="dataToSave[{{ $index }}][Estimated_Revenue]"
                                        value="{{ $data['Estimated_Revenue'] ?? '' }}">
                                    <input type="hidden" name="dataToSave[{{ $index }}][Estimated_Service_Cost]"
                                        value="{{ $data['Estimated_Service_Cost'] ?? '' }}">
                                    <input type="hidden" name="dataToSave[{{ $index }}][Estimated_Material_Cost]"
                                        value="{{ $data['Estimated_Material_Cost'] ?? '' }}">
                                    <input type="hidden" name="dataToSave[{{ $index }}][Estimated_Gross_Profit]"
                                        value="{{ $data['Estimated_Gross_Profit'] ?? '' }}">
                                    <input type="hidden"
                                        name="dataToSave[{{ $index }}][Estimated_Gross_ProfitMargin]"
                                        value="{{ $data['Estimated_Gross_ProfitMargin'] ?? '' }}">
                                    <input type="hidden" name="dataToSave[{{ $index }}][Job_Adding_Status]"
                                        value="{{ $data['Job_Adding_Status'] ?? '' }}">
                                    <input type="hidden" name="dataToSave[{{ $index }}][Refcode]"
                                        value="{{ $data['Refcode'] ?? '' }}">
                                @endforeach

                                <button type="submit" id="spin"
                                    class="bg-green-500 text-white px-4 py-2 rounded-lg shadow hover:bg-green-600 transition flex items-center gap-2">
                                    บันทึกข้อมูล

                                    <svg id="spinnerSave" class="hidden animate-spin h-5 w-5 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                        </path>
                                    </svg>
                                </button>
                            </form>


                        </div>
                    </div>
                </div>
            @else
                <!-- ไม่มีข้อมูลที่จะแสดง -->
            @endif

            <script>
                const openImportBtn = document.getElementById('openModalBtn2');
                const importModal = document.getElementById('importModal');
                const importModalContent = importModal.querySelector('div');
                const closeImportBtn = document.getElementById('closeImportModal');
                const cancelImportBtn = document.getElementById('cancelImportBtn');

                function openImportModal() {
                    importModal.classList.remove('pointer-events-none', 'opacity-0');
                    importModal.classList.add('opacity-100');
                    importModalContent.classList.remove('scale-95');
                    importModalContent.classList.add('scale-100');
                }

                function closeImportModal() {
                    importModal.classList.remove('opacity-100');
                    importModal.classList.add('opacity-0');
                    importModalContent.classList.remove('scale-100');
                    importModalContent.classList.add('scale-95');
                    setTimeout(() => importModal.classList.add('pointer-events-none'), 300);
                }

                openImportBtn.addEventListener('click', openImportModal);
                closeImportBtn.addEventListener('click', closeImportModal);
                cancelImportBtn.addEventListener('click', closeImportModal);

                importModal.addEventListener('click', (e) => {
                    if (e.target === importModal) closeImportModal();
                });
            </script>


            <!-- Modal Add New Job -->
            <div id="modalLg"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 z-50">
                <!-- Modal Content -->
                <div
                    class="bg-white rounded-lg shadow-lg w-11/12 max-w-4xl p-6 relative transform scale-95 transition-all duration-300 overflow-y-auto max-h-[95vh]">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold text-slate-800">Job Details</h2>
                        <button id="closeModalBtn"
                            class="text-slate-500 hover:text-slate-700 text-2xl font-bold">&times;</button>
                    </div>

                    <!-- Form -->
                    <form action="{{ route('addjob.savenewjob') }}" method="POST" class="space-y-4"
                        autocomplete="off">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div>
                                <label class="block mb-1 font-semibold">Site Code<span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="site_code" class="border rounded-md w-full p-1" required>
                            </div>
                            <div>
                                <label class="block mb-1 font-semibold">Site Name</label>
                                <input type="text" name="site_name" class="border rounded-md w-full p-1">
                            </div>
                            <div>
                                <label class="block mb-1 font-semibold">Job Description <span
                                        class="text-red-500">*</span>
                                </label>
                                <input type="text" name="job_description" class="border rounded-md w-full p-1"
                                    required>
                            </div>

                            <div>
                                <label class="block mb-1 font-semibold">
                                    Project Code <span class="text-red-500">*</span>
                                </label>

                                <select name="project_code" class="border rounded-md w-full p-1" required>
                                    <option value="" disabled selected>-- Select Project Code --</option>

                                    @foreach ($projectCodes as $code)
                                        <option value="{{ $code->projectCode }}">
                                            {{ $code->projectCode }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>


                            <div>
                                <label class="block mb-1 font-semibold">
                                    Office Code<span class="text-red-500">*</span>
                                </label>

                                <select name="office_code" class="border rounded-md w-full p-1" required>
                                    <option value="" disabled selected>-- Select Office Code --</option>

                                    @foreach ($officeCodes as $code)
                                        <option value="{{ $code->officeCode }}">
                                            {{ $code->officeCode }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>


                            <div>
                                <label class="block mb-1 font-semibold">Customer Region</label>
                                <input type="text" name="customer_region" class="border rounded-md w-full p-1">
                            </div>

                            <div>
                                <label class="block mb-1 font-semibold">Estimated Revenue <span
                                        class="text-red-500">*</span></label>
                                <input type="text" id="estimated_revenue" name="estimated_revenue"
                                    class="border rounded-md w-full p-1" required>
                            </div>

                            <div>
                                <label class="block mb-1 font-semibold">Estimated Service Cost <span
                                        class="text-red-500">*</span></label>
                                <input type="text" id="estimated_service_cost" name="estimated_service_cost"
                                    class="border rounded-md w-full p-1" required>
                            </div>

                            <div>
                                <label class="block mb-1 font-semibold">Estimated Material Cost <span
                                        class="text-red-500">*</span></label>
                                <input type="text" id="estimated_material_cost" name="estimated_material_cost"
                                    class="border rounded-md w-full p-1" required>
                            </div>

                            <div>
                                <label class="block mb-1 font-semibold">Estimated Gross Profit</label>
                                <input type="text" id="estimated_gross_profit" name="estimated_gross_profit" readonly
                                    class="border rounded-md w-full p-1 font-semibold">
                            </div>

                            <div>
                                <label class="block mb-1 font-semibold  whitespace-nowrap">Estimated Gross Profit
                                    Margin</label>
                                <input type="text" id="estimated_gross_profit_margin"
                                    name="estimated_gross_profit_margin" readonly
                                    class="border rounded-md w-full p-1 font-semibold">
                            </div>

                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end space-x-2 mt-4">

                            @php
                                $fields = [
                                    'site_code',
                                    'job_description',
                                    'project_code',
                                    'office_code',
                                    'estimated_revenue',
                                    'estimated_service_cost',
                                    'estimated_material_cost',
                                ];
                            @endphp

                            @foreach ($fields as $field)
                                @error($field)
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            @endforeach


                            <button type="button" id="cancelBtn"
                                class="rounded-md border py-2 px-4 font-semibold text-slate-600 hover:bg-slate-100">
                                Cancel
                            </button>
                            <button type="submit"
                                class="rounded-md bg-green-600 text-white py-2 px-4 font-semibold hover:bg-green-700 shadow-md">
                                Save
                            </button>
                        </div>

                    </form>
                </div>
            </div>


            <script>
                // เลือก element
                const openAddBtn = document.getElementById('openModalBtn'); // ปุ่มเปิด modal
                const addModal = document.getElementById('modalLg');
                const addModalContent = addModal.querySelector('div'); // div ภายใน modal
                const closeAddBtn = document.getElementById('closeModalBtn');
                const cancelAddBtn = document.getElementById('cancelBtn');

                const revenueInput = document.getElementById('estimated_revenue');
                const serviceCostInput = document.getElementById('estimated_service_cost');
                const materialCostInput = document.getElementById('estimated_material_cost');
                const grossProfitInput = document.getElementById('estimated_gross_profit');
                const grossProfitMarginInput = document.getElementById('estimated_gross_profit_margin');

                const modalInputs = [revenueInput, serviceCostInput, materialCostInput, grossProfitInput, grossProfitMarginInput];

                // ฟังก์ชันคำนวณ Gross Profit / Margin + ฟอร์แมต comma + 2 ทศนิยม
                function calculateGross(format = true) {
                    const revenue = parseFloat(revenueInput.value.replace(/,/g, '')) || 0;
                    const serviceCost = parseFloat(serviceCostInput.value.replace(/,/g, '')) || 0;
                    const materialCost = parseFloat(materialCostInput.value.replace(/,/g, '')) || 0;

                    const grossProfit = (revenue - serviceCost) - materialCost;
                    const grossProfitMargin = revenue ? (grossProfit / revenue) * 100 : 0;

                    const formatNumber = (num) => num.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                    if (format && (revenue || serviceCost || materialCost)) {
                        revenueInput.value = formatNumber(revenue);
                        serviceCostInput.value = formatNumber(serviceCost);
                        materialCostInput.value = formatNumber(materialCost);
                        grossProfitInput.value = formatNumber(grossProfit);
                        grossProfitMarginInput.value = formatNumber(grossProfitMargin) + '%';
                    } else {
                        grossProfitInput.value = (revenue || serviceCost || materialCost) ? grossProfit.toFixed(2) : '';
                        grossProfitMarginInput.value = (revenue || serviceCost || materialCost) ? grossProfitMargin.toFixed(2) +
                            '%' : '';
                    }
                }


                // เปิด modal + รีเซ็ต input
                function openAddModal() {
                    modalInputs.forEach(input => input.value = '');
                    addModal.classList.remove('pointer-events-none', 'opacity-0');
                    addModal.classList.add('opacity-100');
                    addModalContent.classList.remove('scale-95');
                    addModalContent.classList.add('scale-100');
                }

                // ปิด modal
                function closeAddModal() {
                    addModal.classList.remove('opacity-100');
                    addModal.classList.add('opacity-0');
                    addModalContent.classList.remove('scale-100');
                    addModalContent.classList.add('scale-95');
                    setTimeout(() => addModal.classList.add('pointer-events-none'), 300);
                }

                // Event listener
                if (openAddBtn) openAddBtn.addEventListener('click', openAddModal);
                closeAddBtn.addEventListener('click', closeAddModal);
                cancelAddBtn.addEventListener('click', closeAddModal);
                addModal.addEventListener('click', (e) => {
                    if (e.target === addModal) closeAddModal();
                });

                // ดัก input: ไม่ให้กรอก - หรือ e, คำนวณ realtime, ฟอร์แมต
                [revenueInput, serviceCostInput, materialCostInput].forEach(input => {
                    input.addEventListener('keydown', (e) => {
                        if (e.key === '-' || e.key === 'e') e.preventDefault();
                    });

                    input.addEventListener('input', () => {
                        // ลบตัวอักษรที่ไม่ใช่ตัวเลขหรือจุดทศนิยม
                        input.value = input.value.replace(/[^0-9.]/g, '');
                        calculateGross(false); // ส่ง false = ยังไม่ฟอร์แมต
                    });

                    // ฟอร์แมตตอนออกจาก input
                    input.addEventListener('blur', () => {
                        calculateGross(true); // ส่ง true = ฟอร์แมต comma + 2 ทศนิยม
                    });
                });
            </script>

            <!-- เปิด modal ถ้ามี error จากการ validate -->
            @if ($errors->any())
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const addModal = document.getElementById('modalLg');
                        const addModalContent = addModal.querySelector('div');

                        addModal.classList.remove('pointer-events-none', 'opacity-0');
                        addModal.classList.add('opacity-100');

                        addModalContent.classList.remove('scale-95');
                        addModalContent.classList.add('scale-100');
                    });
                </script>
            @endif


            <div class="bg-white p-4 rounded-xl shadow-md h-[500px]">
                <h2 class="text-2xl font-bold mb-4 text-blue-900">Added Job Records</h2>

                <div class="overflow-y-auto h-[400px]">
                    <table class="min-w-full border-collapse table-auto">
                        <thead class="bg-blue-950 text-white sticky top-0 z-10 ">
                            <tr>
                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Refcode</th>
                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Job <br> Adding Status</th>
                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Refcode On ERP</th>


                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Site Code</th>
                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Site Name</th>
                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Job <br> Description</th>

                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Project Code</th>
                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Office Code</th>
                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Customer <br> Region</th>

                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Estimated <br> Revenue</th>
                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Estimated <br> Service Cost
                                </th>
                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Estimated <br> Material Cost
                                </th>

                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Estimated <br> Gross Profit
                                </th>
                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Estimated <br> GrossProfit
                                    Margin</th>
                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Requester</th>
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
                                                    <form action="{{ route('update.job.status', $item->id) }}"
                                                        method="POST" style="display: contents;">
                                                        @csrf
                                                        @method('PUT')
                                                        @foreach (['Approved', 'Rejected'] as $status)
                                                            @php $c = $statusColors[$status]; @endphp
                                                            <button type="submit" name="Job_Adding_Status"
                                                                value="{{ $status }}"
                                                                class="w-full px-4 py-2 text-left hover:bg-gray-100 {{ $c['text'] }} flex items-center gap-2 text-sm">
                                                                <span
                                                                    class="w-2 h-2 {{ $c['dot'] }} rounded-full"></span>
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

                                    <td>Ready/Not Ready</td>

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
            </div>


        </main>
    </div>

@endsection