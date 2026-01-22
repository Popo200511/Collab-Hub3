@extends('layouts.Tailwind')

@section('title', 'SDA')

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

            <div class="bg-white p-4 rounded-xl shadow-md h-[600px]">
                <h2 class="text-2xl font-bold mb-4 text-blue-900">Request Added Job </h2>

                <div class="overflow-y-auto h-[500px]">
                    <table class="min-w-full border-collapse table-auto">
                        <thead class="bg-blue-950 text-white sticky top-0 z-10 ">
                            <tr>

                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Refcode</th>
                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Job <br> Adding Status</th>
                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Refcode on ERP</th>

                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Site Code</th>
                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Site Name</th>
                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Job <br> Description</th>
                                
                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Project Code</th>
                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Office Code</th>
                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Customer <br> Region</th>

                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Estimated <br> Revenue</th>
                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Estimated <br> Service Cost</th>
                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Estimated <br> Material Cost</th>

                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Estimated <br> Gross Profit</th>
                                <th class="py-2 px-4 border-b whitespace-nowrap text-center">Estimated <br> GrossProfit Margin</th>
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

                                    <td class="px-4 border-b whitespace-nowrap text-left">Ready/Not Ready</td>

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