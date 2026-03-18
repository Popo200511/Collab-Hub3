@extends('layouts.user')
@section('title', 'ERP PR')
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
        Payment <span class="text-blue-600">No. of Purchase : {{ $recordCount }}</span>
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
                        <span>PR/MR No.</span>
                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Date</span>
                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Delivery Date</span>

                    </div>
                </th>
                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Ref. Code</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Project/Department</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Job</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>PO Type</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>For</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Remark</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Amount</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Requestor by</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1">
                    <div class="flex flex-col items-center">
                        <span>Vendors</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Approve</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Approve Date</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Status</span>

                    </div>
                </th>


                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Pending</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Ref. BOQ</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Submit</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Submit by</span>

                    </div>
                </th>





                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Submit Date</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Ref. Petty Cash</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Ref. AP</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Add User</span>

                    </div>
                </th>


                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Add Date</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Edit User</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                    <div class="flex flex-col items-center">
                        <span>Edit Date</span>

                    </div>
                </th>

                <th class="bg-blue-950 text-neutral-50 px-2 py-1">
                    <div class="flex flex-col items-center">
                        <span> </span>

                    </div>
                </th>

            </tr>
        </thead>



        <tbody class="text-xs text-center bg-white" id="oldDataBody">
            @foreach ($importpurchase as $item)
            <tr class="divide-x divide-gray-100 border border-gray-200 hover:bg-red-100 hover:text-red-600">
                <td class="whitespace-nowrap">{{ $item->PR_MR_No }}</td>
                <td class="whitespace-nowrap">{{ $item->Date }}</td>
                <td class="whitespace-nowrap">{{ $item->Delivery_Date }}</td>
                <td class="whitespace-nowrap">{{ $item->Ref_Code }}</td>
                <td class="whitespace-nowrap">{{ $item->Project_Department }}</td>
                <td class="whitespace-nowrap">{{ $item->Job }}</td>
                <td class="whitespace-nowrap">{{ $item->PO_Type }}</td>
                <td class="whitespace-nowrap">{{ $item->For }}</td>
                <td class="whitespace-nowrap">{{ $item->Remark }}</td>
                <td class="whitespace-nowrap text-right">{{ ($item->Amount) }}</td>
                <td class="whitespace-nowrap">{{ $item->Requestor_by }}</td>
                <td class="whitespace-nowrap">{{ $item->Vendors }}</td>
                <td class="whitespace-nowrap">{{ $item->Approve }}</td>
                <td class="whitespace-nowrap">{{ $item->Approve_Date }}</td>
                <td class="whitespace-nowrap">{{ $item->Status }}</td>
                <td class="whitespace-nowrap">{{ $item->Pending }}</td>
                <td class="whitespace-nowrap">{{ $item->Ref_BOQ }}</td>
                <td class="whitespace-nowrap">{{ $item->Submit }}</td>
                <td class="whitespace-nowrap">{{ $item->Submit_by }}</td>
                <td class="whitespace-nowrap">{{ $item->Submit_Date }}</td>
                <td class="whitespace-nowrap">{{ $item->Ref_Petty_Cash }}</td>
                <td class="whitespace-nowrap">{{ $item->Ref_AP }}</td>
                <td class="whitespace-nowrap">{{ $item->Add_User }}</td>
                <td class="whitespace-nowrap">{{ $item->Add_Date }}</td>
                <td class="whitespace-nowrap">{{ $item->Edit_User }}</td>
                <td class="whitespace-nowrap">{{ $item->Edit_Date }}</td>
                <td class="whitespace-nowrap">{{ $item->Other }}</td>

            </tr>
            @endforeach


        </tbody>

    </table>
</div>





@endsection