@extends('layouts.Tailwind')

@section('title', 'ADD MEMBER')

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

        <!-- Sidebar -->
        @include('layouts.user')

        <!-- Main Content -->
        <main class="flex-1 p-6 bg-gray-100 overflow-y-auto">
            <div class="container my-4">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">

                            <div class="card-header d-flex justify-content-between align-items-center">
                                Add Member
                                <button class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                                    Member Total
                                </button>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h2 class="modal-title" id="myModalLabel">Member Total</h2>
                                            <button type="button" class="close" data-dismiss="modal">

                                            </button>
                                        </div>

                                        <!-- ปรับตรงนี้ -->
                                        <div class="modal-body" style="max-height:70vh; overflow-y:auto;">

                                            <div class="user-list">
                                                <table class="table table-bordered">
                                                    <thead class="bg-blue-500 text-white">
                                                        <tr>
                                                            <th>Id</th>
                                                            <th>Name</th>
                                                            <th>Email</th>
                                                            <th style="width: 80px">Status</th>
                                                            <th style="width: 80px">Action</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($users as $index => $user)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ $user->name }}</td>
                                                                <td>{{ $user->email }}</td>

                                                                <td>

                                                                    <select class="status-dropdown"
                                                                        data-user-id="{{ $user->id }}">
                                                                        <option value="{{ $user->status }}" selected>
                                                                            {{ $user->status }}</option>
                                                                        @foreach ($officecodes as $officeCode)
                                                                            @if ($officeCode !== $user->status)
                                                                                <option value="{{ $officeCode }}">
                                                                                    {{ $officeCode }}</option>
                                                                            @endif
                                                                        @endforeach
                                                                        <option value="Admin">Admin</option>
                                                                    </select>

                                                                </td>

                                                                <td class="text-center">
                                                                    <form action="{{ route('user.delete', $user->id) }}"
                                                                        method="POST"
                                                                        onsubmit="return confirm('คุณแน่ใจที่จะลบผู้ใช้นี้?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button
                                                                            class="bg-red-500 text-white text-xs px-2 py-1 rounded"
                                                                            type="submit">
                                                                            ลบ
                                                                        </button>
                                                                    </form>
                                                                </td>

                                                            </tr>
                                                        @endforeach
                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>


                            <script>
                                $(document).ready(function() {

                                    $('.status-dropdown').on('change', function() {

                                        let dropdown = $(this);
                                        let newStatus = dropdown.val();
                                        let userId = dropdown.data('user-id');
                                        let oldStatus = dropdown.data('status');

                                        if (!confirm('คุณต้องการเปลี่ยนสถานะของผู้ใช้นี้?')) {
                                            dropdown.val(oldStatus);
                                            return;
                                        }

                                        $.ajax({
                                            url: "/update-status/" + userId,
                                            method: "PUT",
                                            data: {
                                                _token: "{{ csrf_token() }}",
                                                status: newStatus
                                            },
                                            success: function(response) {
                                                alert("สถานะผู้ใช้ถูกอัปเดตเรียบร้อยแล้ว");
                                                dropdown.data('status', newStatus); // อัปเดตค่าปัจจุบัน
                                            },
                                            error: function() {
                                                alert("เกิดข้อผิดพลาดในการอัปเดตสถานะ");
                                                dropdown.val(oldStatus); // คืนค่าเดิม
                                            }
                                        });

                                    });
                                });
                            </script>



                            <div class="card-body">
                                <form method="POST" action="{{ route('sda.register') }}" id="add-member-form">
                                    @csrf

                                    <div class="row mb-3">
                                        <label for="name"
                                            class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                                        <div class="col-md-6">
                                            <input id="name" type="text"
                                                class="form-control @error('name') is-invalid @enderror" name="name"
                                                value="{{ old('name') }}" required autocomplete="name" autofocus>

                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="email"
                                            class="col-md-4 col-form-label text-md-end">{{ __('Email
                                                                                                                                Address') }}</label>

                                        <div class="col-md-6">
                                            <input id="email" type="email"
                                                class="form-control @error('email') is-invalid @enderror" name="email"
                                                value="{{ old('email') }}" required autocomplete="email">

                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="password"
                                            class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                                        <div class="col-md-6">
                                            <input id="password" type="password"
                                                class="form-control @error('password') is-invalid @enderror" name="password"
                                                required autocomplete="new-password">

                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="password-confirm"
                                            class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                                        <div class="col-md-6">
                                            <input id="password-confirm" type="password" class="form-control"
                                                name="password_confirmation" required autocomplete="new-password">
                                        </div>
                                    </div>

                                    <!-- STATUS -->
                                    <div class="row mb-3">
                                        <label for="status" class="col-md-4 col-form-label text-md-end">
                                            Office Code
                                        </label>

                                        <div class="col-md-6">
                                            <select id="status" name="status" class="form-control" required>
                                                <option value="" disabled selected>เลือก Office Code</option>
                                                <option value="Admin">Admin</option>
                                                @foreach ($officecodes as $code)
                                                    <option value="{{ $code }}">{{ $code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>



                                    <!--    <div class="row mb-3">
                                                                            <label for="Option" class="col-md-4 col-form-label text-md-end">Option</label>
                                                                            

                                                                            <div class="col-md-6">
                                                                                <input id="Option" type="Option" class="form-control @error('password') is-invalid @enderror" name="Option" required autocomplete="Option">

                                                                                @error('password')
        <span class="invalid-feedback" role="alert">
                                                                                                                                        <strong>{{ $message }}</strong>
                                                                                                                                    </span>
    @enderror
                                                                            </div>
                                                                        </div> -->

                                    <div class="row mb-0">
                                        <div class="col-md-6 offset-md-4">
                                            <button type="button" class="btn btn-success" onclick="confirmSubmission()">
                                                Add Member
                                            </button>

                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function confirmSubmission() {
                    if (confirm("ต้องการเพิ่มสมาชิก ")) {
                        document.getElementById('add-member-form').submit();
                    }
                }


                /* เปลี่ยน status */
                document.querySelectorAll('.status-dropdown').forEach(select => {
                    const currentValue = select.value;

                    select.addEventListener('mousedown', () => {
                        // ซ่อนค่าเดิมจาก dropdown list
                        Array.from(select.options).forEach(option => {
                            if (option.value === currentValue) option.style.display = 'none';
                        });
                    });

                    select.addEventListener('change', () => {
                        // ถ้าเลือกค่าใหม่ ให้ค่าเดิมกลับมาใน select
                        Array.from(select.options).forEach(option => option.style.display = 'block');
                    });
                });
            </script>


        </main>
    </div>

@endsection
