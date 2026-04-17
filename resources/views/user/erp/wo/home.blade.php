@extends('layouts.user')
@section('title', 'ERP WO Home')
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
            Wo <span class="text-blue-600">No. of Wo : {{ $count }}</span>
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
                            Maincode
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Docno
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Docdate
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Docapp_dt
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Quono
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Quodate
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Period_beg
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Period_end
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Acct_no
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Cust_name
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Pre_event
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Pre_des
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Jobcode
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Jobname
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Dpt_code
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Dpt_name
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Vattype
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Vatper
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Refcode
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Pre_event2
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Data_ty
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Bus_code
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Docapp
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Adduser
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Contract_Amount
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Amount
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Advance
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Vat_amt
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            WT_Amount
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Retention01_amt
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Net_amount
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Description
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Refprno
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Revno
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Buyer_name
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Send_date_mail
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Email_format
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Reply_status
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Reply_remark
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Reply_dt
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            User_name
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            Contract_type
                        </div>
                    </th>

                    <th class="bg-blue-950 text-neutral-50 px-2 py-1 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                            <span>Contract_type_name</span>
                        </div>
                    </th>

                </tr>
            </thead>



            <tbody class="text-xs text-center bg-white" id="oldDataBody">
                @foreach ($wo as $item)
                    <tr class="divide-x divide-gray-100 border border-gray-200 hover:bg-red-100 hover:text-red-600">

                        <td class="whitespace-nowrap">{{ $item->maincode }}</td>
                        <td class="whitespace-nowrap">{{ $item->docno }}</td>
                        
                        <td class="whitespace-nowrap">{{ \Carbon\Carbon::parse($item->docdate)->format('d/m/Y') }}</td>  <!--d/m/y -->
                        <td class="whitespace-nowrap">{{ \Carbon\Carbon::parse($item->docapp_dt)->format('d/m/Y') }}</td>
                        <td class="whitespace-nowrap">{{ $item->quono }}</td>
                        <td class="whitespace-nowrap">{{ \Carbon\Carbon::parse($item->quodate)->format('d/m/Y') }}</td>
                        <td class="whitespace-nowrap">{{ \Carbon\Carbon::parse($item->period_beg)->format('d/m/Y') }}</td>
                        <td class="whitespace-nowrap">{{ \Carbon\Carbon::parse($item->period_end)->format('d/m/Y') }}</td>
                        <td class="whitespace-nowrap">{{ $item->acct_no }}</td>
                        <td class="whitespace-nowrap">{{ $item->cust_name }}</td>

                        <td class="whitespace-nowrap">{{ $item->pre_event }}</td>
                        <td class="whitespace-nowrap">{{ $item->pre_des }}</td>
                        <td class="whitespace-nowrap">{{ $item->jobcode }}</td>
                        <td class="whitespace-nowrap">{{ $item->jobname }}</td>
                        <td class="whitespace-nowrap">{{ $item->dpt_code }}</td>
                        <td class="whitespace-nowrap">{{ $item->dpt_name }}</td>
                        <td class="whitespace-nowrap">{{ $item->vattype }}</td>
                        <td class="whitespace-nowrap">{{ $item->vatper }}</td>
                        <td class="whitespace-nowrap">{{ $item->refcode }}</td>
                        <td class="whitespace-nowrap">{{ $item->pre_event2 }}</td>

                        <td class="whitespace-nowrap">{{ $item->data_ty }}</td>
                        <td class="whitespace-nowrap">{{ $item->bus_code }}</td>
                        <td class="whitespace-nowrap">{{ $item->docapp }}</td>
                        <td class="whitespace-nowrap">{{ $item->adduser }}</td>
                        <td class="whitespace-nowrap">{{ $item->Contract_Amount }}</td>

                                        


                        <td class="whitespace-nowrap text-right">{{ number_format($item->Amount, 2) }}</td>
                        <td class="whitespace-nowrap">{{ $item->Advance }}</td>
                        <td class="whitespace-nowrap text-right">{{ number_format($item->Vat_amt, 2) }}</td>
                        <td class="whitespace-nowrap text-right">{{ number_format($item->WT_Amount, 2) }}</td>
                        <td class="whitespace-nowrap">{{ $item->retention01_amt }}</td>

                        <td class="whitespace-nowrap text-right">{{ number_format($item->Net_amount, 2) }}</td>
                        <td class="whitespace-nowrap">{{ $item->description }}</td>
                        <td class="whitespace-nowrap">{{ $item->refprno }}</td>
                        <td class="whitespace-nowrap">{{ $item->revno }}</td>
                        <td class="whitespace-nowrap">{{ $item->buyer_name }}</td>
                        <td class="whitespace-nowrap">{{ $item->send_date_mail }}</td>
                        <td class="whitespace-nowrap">{{ $item->email_format }}</td>
                        <td class="whitespace-nowrap">{{ $item->reply_status }}</td>
                        <td class="whitespace-nowrap">{{ $item->reply_remark }}</td>
                        <td class="whitespace-nowrap">{{ $item->reply_dt }}</td>
                        <td class="whitespace-nowrap">{{ $item->user_name }}</td>

                        <td class="whitespace-nowrap">{{ $item->contract_type }}</td>
                        <td class="whitespace-nowrap">{{ $item->contract_type_name }}</td>


                    </tr>
                @endforeach


            </tbody>

        </table>
    </div>





@endsection
