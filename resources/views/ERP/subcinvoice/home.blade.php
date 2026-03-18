@extends('layouts.Tailwind')

@section('title', 'ERP WO Home')

@section('content')
    <div class="flex h-[calc(100vh-64px)] overflow-hidden">
        <!-- Aside Sidebar -->
        @include('layouts.sidebar')


        <!-- Main Content -->
        <main class="flex-1 p-6 bg-gray-100 overflow-y-auto">

            <div class="bg-white p-4 rounded-md mb-6 shadow-md">
                <h1 class="text-2xl font-bold text-blue-900 mb-4">Import File SubC Invoice</h1>

                <form action="{{ route('billing.home') }}" method="POST" enctype="multipart/form-data"
                    class="flex items-center gap-2">
                    @csrf

                    <!-- Input เลือกไฟล์ -->
                    <label for="small-file-input" class="sr-only">Choose file</label>

                    <input type="file" name="file" id="small-file-input" accept=".xlsx" required
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
                <h2 class="text-2xl font-bold mb-4 text-blue-900">SubC Invoice Records</h2>
                <table class="min-w-full border-collapse">
                    <thead class="bg-blue-100 sticky top-0">
                        <tr>
                            <th class="py-2 px-4 border-b">No.</th>
                            <th class="py-2 px-4 border-b">Test</th>
                        </tr>
                    </thead>
                </table>

                <!-- Scrollable tbody -->
                <div class="overflow-y-auto h-[300px]">
                    <table class="min-w-full border-collapse">
                        <tbody>
                            @foreach ($subc as $item)
                                <tr>
                                    <td class="py-2 px-4 border-b">{{ $item->id }}</td>
                                    <td class="py-2 px-4 border-b">{{ $item->test }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>

        </main>
    </div>

@endsection
