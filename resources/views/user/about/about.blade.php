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

    <style>
            .swal-title,
            .swal-text {
                font-family: 'Sarabun', sans-serif;
            }
        </style>

    <div class="flex h-[calc(100vh-64px)] overflow-hidden">
        <!-- Sidebar -->
        @include('layouts.user')


        <!-- Main Content -->
        <main class="flex-1 p-6 bg-gray-100 overflow-y-auto">

            <div class="bg-white p-4 rounded-md mb-6 shadow-md">
                <h1 class="text-2xl font-bold text-blue-900 mb-4"  style="font-family: 'Sarabun', sans-serif;">ข่าวสารบริษัท</h1>
            </div>

            <div class="bg-white p-4 rounded-md shadow-md h-[450px]">
                <!-- ส่วนหัวและฟอร์มค้นหา -->
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-blue-900" style="font-family: 'Sarabun', sans-serif;">รูปภาพ </h2>

                </div>

              
            </div>


        </main>
        
    </div>
        

    </div>


@endsection