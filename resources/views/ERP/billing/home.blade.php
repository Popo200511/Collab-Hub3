@extends('layouts.Tailwind')
@section('title', 'ERP Billing Home')
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
                    text: '{{ $errors->first('error') }}',
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
                <h1 class="text-2xl font-bold text-blue-900 mb-4">Import File Billing</h1>

                <form action="{{ route('billing.home') }}" method="POST" enctype="multipart/form-data"
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
                    <button type="submit"
                        class="bg-indigo-500 text-white text-sm px-4 py-2 rounded-md 
                        hover:bg-indigo-600 hover:scale-105 transform transition duration-200 ease-in-out">
                        แสดงข้อมูลที่ Import
                    </button>
                </form>
            </div>

            <div class="bg-white p-4 rounded-md shadow-md h-[450px]">
                <!-- ส่วนหัวและฟอร์มค้นหา -->
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-blue-900">Billing Records No. </h2>

                </div>

                <!-- ตารางหลัก -->
                <div class="overflow-y-auto h-[350px]"> 
                    <table class="min-w-full border-collapse text-center">
                        <thead class="bg-blue-100 sticky top-0">
                            <tr>
                                <th class="py-2 px-4 border-b">No</th>
                                <th class="py-2 px-4 border-b">Refcode</th>
                                <th class="py-2 px-4 border-b">DocumentNo</th>
                                <th class="py-2 px-4 border-b">Subcontractor</th>
                                <th class="py-2 px-4 border-b">Bill Date</th>
                            </tr>
                        </thead>
                        <tbody>
                           @foreach ($billing as $bill)
                                <tr>
                                    <td class="py-1 px-4 border-b">{{ $bill->id }}</td>
                                    <td class="py-1 px-4 border-b">{{ $bill->refCode }}</td>
                                    <td class="py-1 px-4 border-b">{{ $bill->documentNo }}</td>
                                    <td class="py-1 px-4 border-b">{{ $bill->subcontractor }}</td>
                                    <td class="py-1 px-4 border-b">
                                        {{ \Carbon\Carbon::parse($bill->billDate)->format('d-m-Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>


        </main>
    </div>

    <!-- Modal แสดงข้อมูล ที่ import เข้ามา -->
    @if (!empty($dataToSave) && (is_array($dataToSave) || is_object($dataToSave)))
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div
                class="relative w-full max-w-3xl mx-auto bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col h-[70vh]">

                <!-- Header -->
                <div class="flex justify-between items-center px-6 py-3 border-b bg-blue-800 ">
                    <h2 class="text-lg md:text-md text-white">
                        ตรวจสอบข้อมูล Refcode (ตัวอย่าง {{ $countDataToSave }} รายการ)
                    </h2>
                    <a href="home" class="text-white hover:text-gray-200 transition">✖</a>
                </div>
                <!-- Body -->
                <div class="flex-1 overflow-y-auto p-2">
                    <div class="border rounded-lg overflow-hidden">
                        <table class="w-full text-sm border-collapse">
                            <thead class="sticky top-0 bg-blue-50 shadow-sm">
                                <tr class="text-sm text-center text-gray-700">
                                    <th class="px-2 py-3 border">Refcode</th>
                                    <th class="px-2 py-3 border">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($dataToSave as $data)
                                    <tr class="hover:bg-gray-100 transition">

                                        <td class="px-2 py-1 border text-center text-[14px]">
                                            {{ $data['refCode'] ?? '-' }}
                                        </td>

                                        <td class="px-2 py-1 border text-center text-[14px]">
                                            @php
                                                $check = collect($count)->contains($data['refCode'] ?? '');
                                            @endphp

                                            @if ($check)
                                                <span class="text-red-500 font-bold">❌</span>
                                            @else
                                                <span class="text-green-500 font-bold">✅</span>
                                            @endif

                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end items-center px-6 py-2 border-t bg-gray-50">

                    <a href="home" class="bg-red-500 text-white px-4 py-2 rounded-lg shadow hover:bg-red-600 transition">
                        ยกเลิก
                    </a>

                    <form action="{{ route('billing.savebilling') }}" method="POST" class="flex items-center gap-2 ml-2">
                        @csrf
                        <input type="hidden" name="dataToSave" value="{{ json_encode($dataToSave) }}">
                        <button type="submit"
                            class="bg-green-500 text-white px-4 py-2 rounded-lg shadow hover:bg-green-600 transition">
                            ✅ บันทึกข้อมูล
                        </button>
                    </form>

                </div>
            </div>
        </div>
    @else
        <!-- ไม่มีข้อมูลที่จะแสดง -->
    @endif

@endsection
