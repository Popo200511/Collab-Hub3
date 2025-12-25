{{-- resources/views/add-member.blade.php --}}
@extends('layouts.user')
@section('title', 'Add Member')

@push('styles')
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    :root {
        --primary: 175 70% 40%;
        --primary-foreground: 0 0% 100%;
        --background: 210 20% 98%;
        --foreground: 215 25% 15%;
        --card: 0 0% 100%;
        --muted: 210 15% 93%;
        --muted-foreground: 215 15% 50%;
        --accent: 175 60% 95%;
        --accent-foreground: 175 70% 30%;
        --border: 214 20% 90%;
        --destructive: 0 72% 51%;
        --ring: 175 70% 40%;
    }

    body {
        font-family: 'Sarabun', sans-serif;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-slide-up {
        animation: slideUp 0.5s ease-out forwards;
    }

    .shadow-card {
        box-shadow: 0 8px 32px -8px hsl(215 25% 15% / 0.12);
    }

    .shadow-soft {
        box-shadow: 0 4px 20px -4px hsl(215 25% 15% / 0.08);
    }

    .shadow-glow {
        box-shadow: 0 0 40px hsl(175 70% 40% / 0.15);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen flex items-center justify-center p-4" style="background: hsl(210 20% 98%);">
    <div class="w-full max-w-lg animate-slide-up">


        {{-- Success Alert --}}
        @if (session('success'))
        <div class="mb-6 p-4 rounded-xl border"
            style="background: hsl(142 70% 95%); border-color: hsl(142 70% 80%); color: hsl(142 70% 30%);">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        </div>
        @endif

        {{-- Form Card --}}
        <div class="rounded-2xl shadow-card border p-8"
            style="background: hsl(0 0% 100%); border-color: hsl(214 20% 90%);">

            <div class="flex items-center justify-between w-full p-1">
                <h3 class="text-lg font-extrabold tracking-tight">
                    <span class="text-xl text-slate-700 tracking-tight">Add Member</span>
                </h3>

                <button type="button" class="group relative inline-flex items-center gap-2 h-10 px-6 rounded-xl text-sm font-bold text-white transition-all duration-300 
               bg-gradient-to-r from-teal-500 to-emerald-600 
               hover:from-teal-600 hover:to-emerald-700 
               hover:shadow-[0_8px_20px_-6px_rgba(20,184,166,0.5)] 
               active:scale-95" id="openModalBtn">
                    <svg class="w-4 h-4 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span>Member Total</span>
                </button>
            </div>


            <div class=" fixed inset-0 z-50 hidden outline-none overflow-x-hidden overflow-y-auto" id="myModal">
                <div class="relative w-auto my-6 mx-auto max-w-4xl px-4">
                    <div class="relative bg-white rounded-2xl shadow-2xl flex flex-col w-full outline-none"
                        onclick="event.stopPropagation()">


                        <div class="flex items-center justify-between p-5 border-b border-gray-100">
                            <h2 class="text-xl font-bold text-gray-800" id="myModalLabel">Member Total</h2>
                            <button type="button"
                                class="closeModalBtn text-gray-400 hover:text-gray-600 transition-colors text-2xl">×</button>
                        </div>

                        <div class="relative p-6 flex-auto max-h-[70vh] overflow-y-auto">
                            <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-sm">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                                Id</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                                Name</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                                Email</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                                Status</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                                Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @foreach ($users as $index => $user)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-4 text-sm text-gray-600">{{ $index + 1 }}</td>
                                            <td class="px-4 py-4 text-sm font-medium text-gray-900">{{ $user->name }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                                            <td class="px-4 py-4 text-sm">
                                                <select class="status-dropdown bg-gray-50 border border-gray-200 text-gray-700 text-xs rounded-lg
           focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5" data-user-id="{{ $user->id }}"
                                                    data-current-status="{{ $user->status }}">

                                                    @foreach ($officecodes as $officeCode)
                                                    <option value="{{ $officeCode }}" {{ $officeCode==$user->status ?
                                                        'selected' : '' }}>
                                                        {{ $officeCode }}
                                                    </option>
                                                    @endforeach
                                                </select>


                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <form action="{{ route('user.delete', $user->id) }}" method="POST"
                                                    class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="bg-red-500 text-white text-xs px-2 py-1 rounded"
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


            <form method="POST" action="{{ route('register') }}" id="add-member-form" class="space-y-6">

                @csrf

                {{-- Name Field --}}
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-medium" style="color: hsl(215 25% 15%);">
                        ชื่อ-นามสกุล
                    </label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="กรอกชื่อ-นามสกุล"
                        required autocomplete="name" autofocus
                        class="flex h-11 w-full rounded-lg border px-4 py-2 text-base transition-all duration-200 outline-none @error('name') border-red-500 @enderror"
                        style="border-color: hsl(214 20% 90%); background: hsl(0 0% 100%);"
                        onfocus="this.style.borderColor='hsl(175 70% 40%)'; this.style.boxShadow='0 0 0 2px hsl(175 70% 40% / 0.2)';"
                        onblur="this.style.borderColor='hsl(214 20% 90%)'; this.style.boxShadow='none';">
                    @error('name')
                    <p class="text-sm" style="color: hsl(0 72% 51%);">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email Field --}}
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium" style="color: hsl(215 25% 15%);">
                        อีเมล
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        placeholder="your-os@gtn.co.th" required autocomplete="email"
                        class="flex h-11 w-full rounded-lg border px-4 py-2 text-base transition-all duration-200 outline-none @error('email') border-red-500 @enderror"
                        style="border-color: hsl(214 20% 90%); background: hsl(0 0% 100%);"
                        onfocus="this.style.borderColor='hsl(175 70% 40%)'; this.style.boxShadow='0 0 0 2px hsl(175 70% 40% / 0.2)';"
                        onblur="this.style.borderColor='hsl(214 20% 90%)'; this.style.boxShadow='none';">
                    @error('email')
                    <p class="text-sm" style="color: hsl(0 72% 51%);">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Field --}}
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium" style="color: hsl(215 25% 15%);">
                        รหัสผ่าน
                    </label>
                    <div class="relative">
                        <input id="password" type="password" name="password" placeholder="••••••••" required
                            autocomplete="new-password"
                            class="flex h-11 w-full rounded-lg border px-4 py-2 pr-10 text-base transition-all duration-200 outline-none @error('password') border-red-500 @enderror"
                            style="border-color: hsl(214 20% 90%); background: hsl(0 0% 100%);"
                            onfocus="this.style.borderColor='hsl(175 70% 40%)'; this.style.boxShadow='0 0 0 2px hsl(175 70% 40% / 0.2)';"
                            onblur="this.style.borderColor='hsl(214 20% 90%)'; this.style.boxShadow='none';">
                        <button type="button" onclick="togglePassword('password', this)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 transition-colors"
                            style="color: hsl(215 15% 50%);">
                            <svg class="w-4 h-4 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                            <svg class="w-4 h-4 eye-off-icon hidden" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21">
                                </path>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                    <p class="text-sm" style="color: hsl(0 72% 51%);">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password Field --}}
                <div class="space-y-2">
                    <label for="password-confirm" class="block text-sm font-medium" style="color: hsl(215 25% 15%);">
                        ยืนยันรหัสผ่าน
                    </label>
                    <div class="relative">
                        <input id="password-confirm" type="password" name="password_confirmation" placeholder="••••••••"
                            required autocomplete="new-password"
                            class="flex h-11 w-full rounded-lg border px-4 py-2 pr-10 text-base transition-all duration-200 outline-none"
                            style="border-color: hsl(214 20% 90%); background: hsl(0 0% 100%);"
                            onfocus="this.style.borderColor='hsl(175 70% 40%)'; this.style.boxShadow='0 0 0 2px hsl(175 70% 40% / 0.2)';"
                            onblur="this.style.borderColor='hsl(214 20% 90%)'; this.style.boxShadow='none';">
                        <button type="button" onclick="togglePassword('password-confirm', this)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 transition-colors"
                            style="color: hsl(215 15% 50%);">
                            <svg class="w-4 h-4 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                            <svg class="w-4 h-4 eye-off-icon hidden" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Status Field --}}
                <div class="space-y-2">
                    <label for="status" class="block text-sm font-medium" style="color: hsl(215 25% 15%);">
                        Office Code
                    </label>
                    <div class="relative">
                        <select id="status" name="status" required
                            class="flex h-11 w-full rounded-lg border px-4 py-2 text-base transition-all duration-200 outline-none appearance-none cursor-pointer"
                            style="border-color: hsl(214 20% 90%); background: hsl(0 0% 100%);"
                            onfocus="this.style.borderColor='hsl(175 70% 40%)'; this.style.boxShadow='0 0 0 2px hsl(175 70% 40% / 0.2)';"
                            onblur="this.style.borderColor='hsl(214 20% 90%)'; this.style.boxShadow='none';">
                            <option value="" disabled selected style="color: hsl(215 15% 50%);">เลือก Office Code
                            </option>
                            <option value="4">Admin</option>
                            @foreach ($officecodes as $code)
                            <option value="{{ $code }}">{{ $code }}</option>
                            @endforeach

                        </select>
                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"
                            style="color: hsl(215 15% 50%);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </div>

                    {{-- Status Legend --}}
                    <div class="mt-4 p-4 g-3 rounded-xl border"
                        style="background: hsl(210 15% 93% / 0.5); border-color: hsl(214 20% 90%);">
                        <p class="text-xs font-medium mb-3" style="color: hsl(215 15% 50%);">คำอธิบายสถานะ:</p>
                        <div class="grid grid-cols-3 gap-2 text-xs" style="color: hsl(215 15% 50%);">
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" style="color: hsl(175 70% 40%);" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4">
                                    </path>
                                </svg>
                                <span>Admin</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" style="color: hsl(175 70% 40%);" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
                                <span>01_BKK</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" style="color: hsl(175 70% 40%);" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                                <span>02_CMI</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" style="color: hsl(175 70% 40%);" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <span>03_KKN</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" style="color: hsl(175 70% 40%);" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <span>04_BUN</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" style="color: hsl(175 70% 40%);" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <span>05_HYI</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" style="color: hsl(175 70% 40%);" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <span>06_PLK</span>
                            </div>


                        </div>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3 pt-4">
                    <a href="/home"
                        class="flex-1 inline-flex items-center justify-center gap-2 h-11 px-5 rounded-lg border text-sm font-medium transition-all duration-200"
                        style="border-color: hsl(214 20% 90%); background: hsl(0 0% 100%); color: hsl(215 25% 25%);"
                        onmouseover="this.style.background='hsl(175 60% 95%)'; this.style.color='hsl(175 70% 30%)';"
                        onmouseout="this.style.background='hsl(0 0% 100%)'; this.style.color='hsl(215 25% 25%)';">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                        หน้าหลัก
                    </a>
                    <button type="button" onclick="confirmSubmission()"
                        class="flex-1 inline-flex items-center justify-center gap-2 h-11 px-5 rounded-lg text-sm font-medium transition-all duration-200 shadow-soft"
                        style="background: hsl(175 70% 40%); color: hsl(0 0% 100%);"
                        onmouseover="this.style.background='hsl(175 70% 35%)'; this.style.boxShadow='0 0 40px hsl(175 70% 40% / 0.3)';"
                        onmouseout="this.style.background='hsl(175 70% 40%)'; this.style.boxShadow='0 4px 20px -4px hsl(215 25% 15% / 0.08)';">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                            </path>
                        </svg>
                        เพิ่มสมาชิก
                    </button>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <p class="text-center text-xs mt-6" style="color: hsl(215 15% 50%);">
            ระบบจัดการสมาชิก • Member Management System
        </p>
    </div>
</div>

{{-- Confirmation Modal --}}
<div id="confirmModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4"
    style="background: rgba(0,0,0,0.5);">
    <div class="rounded-2xl shadow-card p-6 w-full max-w-md"
        style="background: hsl(0 0% 100%); animation: slideUp 0.3s ease-out;">
        <h3 class="text-lg font-semibold mb-2" style="color: hsl(215 25% 15%);">ยืนยันการเพิ่มสมาชิก</h3>
        <p class="text-sm mb-6" style="color: hsl(215 15% 50%);">คุณต้องการเพิ่มสมาชิกใหม่ใช่หรือไม่?</p>
        <div class="flex gap-3">
            <button type="button" onclick="closeModal()"
                class="flex-1 h-10 px-4 rounded-lg border text-sm font-medium transition-all"
                style="border-color: hsl(214 20% 90%); background: hsl(0 0% 100%);">
                ยกเลิก
            </button>
            <button type="button" onclick="submitForm()"
                class="flex-1 h-10 px-4 rounded-lg text-sm font-medium transition-all"
                style="background: hsl(175 70% 40%); color: white;">
                ยืนยัน
            </button>
        </div>
    </div>
</div>




</div>




<script>
    function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    const eyeIcon = button.querySelector('.eye-icon');
    const eyeOffIcon = button.querySelector('.eye-off-icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        eyeIcon.classList.add('hidden');
        eyeOffIcon.classList.remove('hidden');
    } else {
        input.type = 'password';
        eyeIcon.classList.remove('hidden');
        eyeOffIcon.classList.add('hidden');
    }
}

function confirmSubmission() {
    const modal = document.getElementById('confirmModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal() {
    const modal = document.getElementById('confirmModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function submitForm() {
    document.getElementById('add-member-form').submit();
}

// Close modal on backdrop click
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>



<script>
    document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('myModal');
    const openBtn = document.getElementById('openModalBtn');

    // ✅ เปิด modal
    openBtn.addEventListener('click', () => {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    });

    // ✅ ปุ่มปิด (×)
    document.querySelectorAll('.closeModalBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        });
    });

    // ✅ คลิก backdrop ปิด
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    });
});

</script>



<script>
$(document).on('change', '.status-dropdown', function () {
    let select = $(this);
    let userId = select.data('user-id');            // ✅ แก้
    let newStatus = select.val();
    let oldStatus = select.data('current-status'); // ✅ แก้

    Swal.fire({
        title: 'ยืนยันการเปลี่ยนสถานะ?',
        text: 'คุณต้องการเปลี่ยนสถานะใช่หรือไม่',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {

        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('update.job.status', '') }}/" + userId,
                method: "PUT",
                data: {
                    _token: "{{ csrf_token() }}",
                    status: newStatus
                },
                success: function () {
                    Swal.fire('สำเร็จ', 'เปลี่ยนสถานะเรียบร้อย', 'success');

                    // ✅ update ค่าเดิมใหม่
                    select.data('current-status', newStatus);
                }
            });
        } else {
            // ❗ revert ค่าเดิม
            select.val(oldStatus);
        }
    });
});



</script>








@endsection