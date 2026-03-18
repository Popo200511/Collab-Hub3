@extends('layouts.user')
@section('title', 'ERP Billing')
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


<div class="text-center mt-6 mb-4">
    <h2 class="text-2xl font-sarabun font-extrabold text-gray-900 italic uppercase">
        Billing <span class="text-blue-600">No. of Purchase : {{ $count }}</span>
    </h2>
</div>


<style>
    .swal-title,
    .swal-text {
        font-family: 'Sarabun', sans-serif;
    }
</style>


<!-- ตาราง Showdata เก่าเปิดมาเจอเลย-->
<div
    class="mt-2 ml-2 w-[98%] max-h-[650px] overflow-x-auto overflow-y-auto rounded-lg border border-gray-300 shadow-lg">
    <table class="w-full h-full border-collapse">

        <thead class="sticky top-0 bg-white shadow-md">
            <tr class="text-xs text-center h-[40px]">


                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>No</span>
                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>DataType</span>
                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Document No</span>

                    </div>
                </th>
                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Subcontractor</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Type</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>BillNo</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>BillDate</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>PeriodNo</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>DueDate</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>VoucherNo</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Vendor</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1">
                    <div class="flex flex-col items-center">
                        <span>BranchNo</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Invoice</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Currency</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Amount</span>

                    </div>
                </th>


                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>LessAmt</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>NetAmount</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>RefCode</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>ProjectDepartment</span>

                    </div>
                </th>





                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Job</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Submit</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>SubmitBy</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>SubmitDate</span>

                    </div>
                </th>


                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Sign</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Remark</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>AddUser</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1">
                    <div class="flex flex-col items-center">
                        <span>AddDate </span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>EditUser</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>EditDate</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Gl</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>WriteOffStatus</span>

                    </div>
                </th>



            </tr>
        </thead>



        <tbody class="text-xs text-center bg-white" id="oldDataBody">
            @foreach ($billing as $item)
            <tr class="border-b border-gray-200 divide-x divide-gray-100 hover:bg-red-100 hover:text-red-600">
                <td class="whitespace-nowrap">{{ $item->no }}</td>
                <td class="whitespace-nowrap">{{ $item->dataType }}</td>
                <td class="whitespace-nowrap">{{ $item->documentNo }}</td>
                <td class="whitespace-nowrap">{{ $item->subcontractor }}</td>
                <td class="whitespace-nowrap">{{ $item->type }}</td>
                <td class="whitespace-nowrap">{{ $item->billNo }}</td>
                <td class="whitespace-nowrap">{{ $item->billDate }}</td>
                <td class="whitespace-nowrap">{{ $item->periodNo }}</td>
                <td class="whitespace-nowrap">{{ $item->dueDate }}</td>
                <td class="whitespace-nowrap">{{ $item->voucherNo }}</td>
                <td class="whitespace-nowrap">{{ $item->vendor }}</td>
                <td class="whitespace-nowrap">{{ $item->branchNo }}</td>
                <td class="whitespace-nowrap">{{ $item->invoice }}</td>
                <td class="whitespace-nowrap">{{ $item->currency }}</td>
                <td class="whitespace-nowrap text-right">{{ number_format($item->netAmount, 2) }}</td>
                <!-- แสดงผล NetAmount ด้วยการจัดรูปแบบตัวเลขให้มีทศนิยม 2 ตำแหน่ง    -->
                <td class="whitespace-nowrap">{{ $item->lessAmt }}</td>
                <td class="whitespace-nowrap text-right">{{ number_format($item->amount, 2) }}</td>
                <!-- แสดงผล Amount ด้วยการจัดรูปแบบตัวเลขให้มีทศนิยม 2 ตำแหน่ง -->
                <td class="whitespace-nowrap">{{ $item->refCode }}</td>
                <td class="whitespace-nowrap">{{ $item->projectDepartment }}</td>
                <td class="whitespace-nowrap">{{ $item->job }}</td>
                <td class="whitespace-nowrap">{{ $item->submit }}</td>
                <td class="whitespace-nowrap">{{ $item->submitBy }}</td>
                <td class="whitespace-nowrap">{{ $item->submitDate }}</td>
                <td class="whitespace-nowrap">{{ $item->sign }}</td>
                <td class="whitespace-nowrap">{{ $item->remark }}</td>
                <td class="whitespace-nowrap">{{ $item->addUser }}</td>
                <td class="whitespace-nowrap">{{ $item->addDate }}</td>
                <td class="whitespace-nowrap">{{ $item->editUser }}</td>

                <td class="whitespace-nowrap">{{ $item->editDate }}</td>
                <td class="whitespace-nowrap">{{ $item->gl }}</td>
                <td class="whitespace-nowrap">{{ $item->writeOffStatus }}</td>

            </tr>
            @endforeach


        </tbody>

    </table>
</div>





@endsection