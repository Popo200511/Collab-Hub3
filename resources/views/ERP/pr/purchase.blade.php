@extends('layouts.Tailwind')
@section('title', 'ERP PR Home')
@section('content')

    <!-- แสดงข้อความสำเร็จ -->
    @if (session('success'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#22c55e'
                    /*
                    showConfirmButton: false,
                    timer: 3000, // ปิดอัตโนมัติใน 3 วินาที
                    timerProgressBar: true
                    */

                });
            });
        </script>
    @endif

    <!-- แสดงข้อความข้อผิดพลาด -->
    @if ($errors->has('message'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: 'error',
                    title: 'ข้อผิดพลาด!',
                    text: '{{ $errors->first('message') }}',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#d33'
                });
            });
        </script>
    @endif


    <div class="flex h-[calc(100vh-64px)] overflow-hidden">
        <!-- Aside Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <main class="flex-1 p-6 bg-gray-100 overflow-y-auto">

            <div class="bg-white p-4 rounded-md mb-6 shadow-md">
                <h1 class="text-2xl font-bold text-blue-900 mb-4">Import File Purchase</h1>

                <form id="myForm" action="{{ route('pr.purchase') }}" method="POST" enctype="multipart/form-data"
                    class="flex items-center gap-2">
                    @csrf

                    <!-- Input เลือกไฟล์ -->
                    <label for="small-file-input" class="sr-only">Choose file</label>

                    <input type="file" name="xlsx_file_add" id="small-file-input" accept=".xlsx" required
                        class="block w-[400px] border border-gray-200 shadow-sm rounded-lg text-sm 
                   focus:z-10 focus:border-blue-500 focus:ring-blue-500 
                   disabled:opacity-50 disabled:pointer-events-none 
                   file:bg-gray-50 file:border-0 file:me-4 file:py-2 file:px-4">

                    <!-- ปุ่มเช็คข้อมูล -->
                    <button type="submit" id="spin"
                        class="bg-indigo-500 text-white text-sm px-4 py-2 rounded-md font-semibold
                                hover:bg-indigo-600 hover:scale-105 transform transition duration-200 ease-in-out
                                flex items-center gap-2">

                        แสดงข้อมูลที่นำเข้า

                        <!-- Spinner -->
                        <svg id="spinner" class="hidden animate-spin h-5 w-5 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>

                    </button>

                    <script>
                        document.getElementById("myForm").addEventListener("submit", function(event) {

                            // ถ้าฟอร์มไม่ผ่าน required → หยุด & ไม่ให้ spinner ทำงาน
                            if (!this.checkValidity()) {
                                event.preventDefault(); // หยุดการ submit
                                return;
                            }

                            // ถ้าฟอร์ม valid → ให้ spinner แสดง
                            document.getElementById("spinner").classList.remove("hidden");

                            // ป้องกันการกดซ้ำ
                            document.getElementById("spin").disabled = true;
                        });
                    </script>

                </form>
            </div>

            <!-- Modal อัพเดทข้อมูลที่มีอยู่ในระบบ -->
            <div id="importModal"
                class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6 relative">
                    <h2 class="text-xl font-bold text-blue-900 mb-4">อัพเดทข้อมูล</h2>

                    <!-- ฟอร์มภายใน Modal -->
                    <form method="POST" enctype="multipart/form-data">
                        @csrf
                        <label class="block text-sm font-medium text-gray-700 mb-2">เลือกไฟล์ใหม่ (.xlsx)</label>
                        <input type="file" name="update_file" accept=".xlsx" required
                            class="block w-full border border-gray-300 rounded-md text-sm 
                       file:bg-gray-100 file:border-0 file:py-2 file:px-4 mb-4">

                        <div class="flex justify-end gap-2">
                            <!-- ปุ่มยกเลิก -->
                            <button type="button" id="closeModalBtn"
                                class="bg-red-500 text-white text-sm px-4 py-2 rounded-md 
                           hover:bg-red-700 transition">
                                ยกเลิก
                            </button>

                            <!-- ปุ่มยืนยัน -->
                            <button type="submit"
                                class="bg-green-600 text-white text-sm px-4 py-2 rounded-md font-semibold 
                           hover:bg-green-700 transition">
                                ยืนยันอัพเดท
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                const openModalBtn = document.getElementById('openModalBtn');
                const closeModalBtn = document.getElementById('closeModalBtn');
                const modal = document.getElementById('importModal');

                openModalBtn.addEventListener('click', () => {
                    modal.classList.remove('hidden');
                });

                closeModalBtn.addEventListener('click', () => {
                    modal.classList.add('hidden');
                });

                // ปิด modal เมื่อคลิกนอกกล่อง
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.classList.add('hidden');
                    }
                });
            </script>

            <!-- ตารางแสดงข้อมูล PR Records -->
            <div class="bg-white p-4 rounded-md shadow-md h-[450px]">
                <h2 class="text-2xl font-bold mb-4 text-blue-900">Purchase Records No. {{ $recordCount }}</h2>

                <div class="overflow-y-auto h-[350px]">
                    <table class="min-w-full border-collapse table-auto">
                        <thead class="bg-blue-100 sticky top-0 z-10">
                            <tr>
                                <th class="py-2 px-4 border-b text-left whitespace-nowrap">Document No.</th>
                                <th class="py-2 px-4 border-b text-left whitespace-nowrap">Ref. Code</th>
                                <th class="py-2 px-4 border-b text-left whitespace-nowrap">Approve Date/Time</th>
                                <th class="py-2 px-4 border-b text-left whitespace-nowrap">Attach File</th>
                                <th class="py-2 px-4 border-b text-left whitespace-nowrap">Ref. Code</th>
                                
                                <th class="py-2 px-4 border-b text-left whitespace-nowrap">Project. No / Dpt. Code</th>
                                <th class="py-2 px-4 border-b text-left whitespace-nowrap">Project Name / Dpt. Name</th>
                                <th class="py-2 px-4 border-b text-left whitespace-nowrap">Priority</th>
                                <th class="py-2 px-4 border-b text-left whitespace-nowrap">Type</th>
                                <th class="py-2 px-4 border-b text-left whitespace-nowrap">For</th>

                                <th class="py-2 px-4 border-b text-left whitespace-nowrap text-center">Remark</th>
                                <th class="py-2 px-4 border-b text-left whitespace-nowrap">Reference By</th>
                                <th class="py-2 px-4 border-b text-left whitespace-nowrap">Vendor</th>
                                <th class="py-2 px-4 border-b text-left whitespace-nowrap">Project Place</th>
                                <th class="py-2 px-4 border-b text-left whitespace-nowrap">Approve</th>

                                <th class="py-2 px-4 border-b text-left whitespace-nowrap">Ref. Petty Cash</th>
                                <th class="py-2 px-4 border-b text-left whitespace-nowrap">Ref. APO</th>
                                <th class="py-2 px-4 border-b text-left whitespace-nowrap">Print By</th>
                                <th class="py-2 px-4 border-b text-left whitespace-nowrap">Print Date</th>
                                <th class="py-2 px-4 border-b text-left whitespace-nowrap">Comment</th>

                                <th class="py-2 px-4 border-b text-left whitespace-nowrap">Open P/O</th>
                                <th class="py-2 px-4 border-b text-left whitespace-nowrap">Subc</th>
                                <th class="py-2 px-4 border-b text-left whitespace-nowrap">Subc. Code</th>
                                <th class="py-2 px-4 border-b text-left whitespace-nowrap">Add By</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($importpurchase as $item)
                                <tr class="odd:bg-white even:bg-gray-50">
                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->Document_No }}</td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->PR_Date }}</td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->Approve_Date_Time }}</td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->Attach_File }}</td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->Ref_Code }}</td>

                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->ProjectNo_DptCode }}</td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->ProjectName_DptName }}</td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->Priority }}</td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->Type_ }}</td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->For_ }}</td>

                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->Remark }}</td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->Reference_By }}</td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->Vendor }}</td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->Project_Place }}</td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->Approve }}</td>

                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->Ref_PettyCash }}</td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->Ref_APO }}</td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->Print_By }}</td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->Print_Date }}</td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->Comment_ }}</td>

                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->Open_P_O }}</td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->Subc_ }}</td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->Subc_Code }}</td>
                                    <td class="px-4 py-2 border-b whitespace-nowrap">{{ $item->AddBy }}</td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>



        </main>
    </div>

    <!-- Modal แสดงข้อมูล ที่ import เข้ามา -->
    @if (!empty($previewData) && (is_array($previewData) || is_object($previewData)))
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div
                class="relative w-full max-w-[50vm] mx-4 bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col h-[90vh]">

                <!-- Header -->
                <div class="flex justify-between items-center px-4 md:px-6 py-3 border-b bg-blue-800">
                    <h2 class="text-md md:text-lg text-white">
                        <h2>ตัวอย่างข้อมูล ({{ $rowCount }} รายการ) — แสดง 20 แถวแรก</h2>
                    </h2>
                    <a href="home" class="text-white hover:text-gray-200 transition text-xl">✖</a>
                </div>
                <!-- Body -->
                <div class="flex-1 overflow-y-auto p-2 min-h-[200px]">

                    <!-- ตารางแสดงข้อมูล -->
                    <div class="w-full h-full overflow-y-auto">
                        <table class="w-max md:w-full text-sm border-collapse">
                            <thead class="sticky top-0 bg-blue-50 shadow-sm ">
                                <tr class="text-sm text-center text-gray-700">
                                    <th class="px-2 py-3 border">No.</th>
                                    <th class="px-2 py-3 border">Document No.</th>
                                    <th class="px-2 py-3 border">PR. Date</th>
                                    <th class="px-2 py-3 border">Approve Date/Time</th>
                                    <th class="px-2 py-3 border">Attach File</th>
                                    <th class="px-2 py-3 border">Ref. Code</th>
                                    <th class="px-2 py-3 border">Project. No / Dpt. Code</th>
                                    <th class="px-2 py-3 border">Project Name / Dpt. Name</th>
                                    <th class="px-2 py-3 border">Priority</th>
                                    <th class="px-2 py-3 border">Type</th>
                                    <th class="px-2 py-3 border">For</th>
                                    <th class="px-2 py-3 border">Remark</th>
                                    <th class="px-2 py-3 border">Reference By</th>
                                    <th class="px-2 py-3 border">Vendor</th>
                                    <th class="px-2 py-3 border">Project Place</th>
                                    <th class="px-2 py-3 border">Approve</th>
                                    <th class="px-2 py-3 border">Ref. Petty Cash</th>
                                    <th class="px-2 py-3 border">Ref. APO</th>
                                    <th class="px-2 py-3 border">Print By</th>
                                    <th class="px-2 py-3 border">Print Date</th>

                                    <th class="px-2 py-3 border">Comment</th>
                                    <th class="px-2 py-3 border">Open P/O</th>
                                    <th class="px-2 py-3 border">Subc.</th>
                                    <th class="px-2 py-3 border">Subc. Code</th>
                                    <th class="px-2 py-3 border">Add By</th>


                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($previewData as $data)
                                    <tr class="hover:bg-gray-100 transition">

                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['No'] ?? '-' }}</td>
                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['Document_No'] ?? '-' }}</td>
                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['PR_Date'] ?? '-' }}</td>
                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['Approve_Date_Time'] ?? '-' }}</td>
                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['Attach_File'] ?? '-' }}</td>

                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['Ref_Code'] ?? '-' }}</td>
                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['ProjectNo_DptCode'] ?? '-' }}</td>
                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['ProjectName_DptName'] ?? '-' }}</td>
                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['Priority'] ?? '-' }}</td>
                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['Type_'] ?? '-' }}</td>

                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['For_'] ?? '-' }}</td>
                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['Remark'] ?? '-' }}</td>
                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['Reference_By'] ?? '-' }}</td>
                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['Vendor'] ?? '-' }}</td>
                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['Project_Place'] ?? '-' }}</td>

                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['Approve'] ?? '-' }}</td>
                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['Ref_PettyCash'] ?? '-' }}</td>
                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['Ref_APO'] ?? '-' }}</td>
                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['Print_By'] ?? '-' }}</td>
                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['Print_Date'] ?? '-' }}</td>

                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['Comment_'] ?? '-' }}</td>
                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['Open_P_O'] ?? '-' }}</td>
                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['Subc_'] ?? '-' }}</td>
                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['Subc_Code'] ?? '-' }}</td>
                                        <td class="px-2 py-1 border text-center whitespace-nowrap text-[14px]">
                                            {{ $data['AddBy'] ?? '-' }}</td>


                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end items-center px-6 py-2 border-t bg-gray-50">

                    <a href="home"
                        class="bg-red-500 text-white px-4 py-2 rounded-lg shadow hover:bg-red-600 transition">
                        ยกเลิก
                    </a>

                    <form action="{{ route('pr.savepurchase') }}" id="save" method="POST"
                        class="flex items-center gap-2 ml-2">
                        @csrf
                        <button type="submit"
                            class="bg-green-500 text-white px-4 py-2 rounded-lg shadow hover:bg-green-600 transition flex items-center gap-2">
                            ✅ บันทึกข้อมูล

                            <!-- Spinner -->
                            <svg id="spinnerSave" class="hidden animate-spin h-5 w-5 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                </path>
                            </svg>
                        </button>

                        <script>
                            document.getElementById("save").addEventListener("submit", function(event) {

                                // ถ้าฟอร์มไม่ผ่าน required → หยุด & ไม่ให้ spinner ทำงาน
                                if (!this.checkValidity()) {
                                    event.preventDefault(); // หยุดการ submit
                                    return;
                                }

                                // ถ้าฟอร์ม valid → ให้ spinner แสดง
                                document.getElementById("spinnerSave").classList.remove("hidden");

                                // ป้องกันการกดซ้ำ
                                document.getElementById("spin").disabled = true;
                            });
                        </script>

                    </form>

                </div>
            </div>
        </div>
    @else
        <!-- ไม่มีข้อมูลที่จะแสดง -->
    @endif



@endsection