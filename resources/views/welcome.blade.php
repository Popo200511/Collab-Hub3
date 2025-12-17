@extends('layouts.Tailwind')
@section('title', 'หน้าแรกของเว็บไซต์')

@section('content')

<style>
    /* * **CSS ที่ได้รับการปรับปรุง:**
     * ลบคลาส .bgImage และ .bgImage::after ออก และใช้ Tailwind's Utility Classes แทน
     * ปรับปรุงแอนิเมชันให้ดูทันสมัยขึ้น
    */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        /* ใช้ Font ที่ทันสมัยกว่า sans-serif ทั่วไป เช่น Inter หรือคุณอาจใช้ฟอนต์ไทยที่คุณชอบ */
        font-family: 'Inter', sans-serif; 
    }

    /* Import Font: Inter (ใช้ในตัวอย่างก่อนหน้านี้) */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

    /* กำหนดคอนเทนเนอร์หลักให้เต็มพื้นที่ */
    .hero-container {
        /* ตรวจสอบว่า layout หลักของคุณได้กำหนดความสูงขั้นต่ำให้ body/html แล้ว */
        min-height: 100vh;
        position: relative;
    }

    /* คลาสภาพพื้นหลังและ Overlay */
    .hero-bg {
        background-image: url("https://img.pikbest.com/wp/202347/pastel-green-background-3d-rendering-of-a-winner-s-podium-on-matching_9767186.jpg!bw700");
        background-size: cover; /* เปลี่ยนจาก 100% เป็น cover เพื่อให้ครอบคลุมเต็มพื้นที่ */
        background-position: center bottom;
        background-repeat: no-repeat;
    }

    /* การจัดเนื้อหาให้อยู่ตรงกลางด้วย Flex */
    .content-area {
        position: relative;
        z-index: 10;
        /* ใช้ flex justify-center items-center ใน HTML แทนการใช้ transform: translate() ใน CSS */
    }

    .content h2 {
        font-size: 50px;
        color: #fff; /* เปลี่ยนเป็นสีขาวเพื่อให้ตัดกับ overlay */
        margin-bottom: 20px;
        opacity: 0;
        animation: slideTop 1s ease forwards;
        animation-delay: .5s; /* หน่วงเวลาเล็กน้อย */
    }

    .content h2 span {
        color: #6ee7b7; /* สีเขียวนีออน/มิ้นท์อ่อนๆ (Tailwind's emerald-300) */
        font-weight: 800;
    }

    .subtitle {
        color: #d1d5db; /* สีเทาอ่อน */
        font-size: 1.5rem;
        margin-top: 1rem;
        opacity: 0;
        animation: slideTop 1s ease forwards;
        animation-delay: 1s; /* หน่วงเวลามากกว่า H2 */
    }


    /* กำหนดแอนิเมชัน slideTop */
    @keyframes slideTop {
        0% {
            transform: translateY(50px); /* เริ่มจากด้านล่าง (น้อยกว่าเดิมเพื่อให้ดูนุ่มนวล) */
            opacity: 0; 
        }
        100% {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>

{{-- โครงสร้าง HTML ที่ปรับปรุงใหม่ --}}
<div class="hero-container relative flex items-center justify-center">

    {{-- 1. พื้นหลังภาพและ Overlay (ใช้ Tailwind Utilities) --}}
    <div class="hero-bg absolute inset-0">
        {{-- Overlay: ให้ภาพพื้นหลังเข้มขึ้นและมีสีทับเล็กน้อย --}}
        <div class="absolute inset-0 bg-gray-900 opacity-60"></div> 
        {{-- Gradient Overlay: เพิ่มความสวยงามที่ด้านบนและล่าง --}}
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/50 via-transparent to-gray-900/10"></div>
    </div>
    
    {{-- 2. เนื้อหาหลัก (Content) --}}
    <div class="content-area w-full max-w-4xl px-4 py-20 text-center">
        <div class="content">
            <h2 class="text-6xl md:text-8xl font-extrabold tracking-tight">
                Welcome To <span>GTN</span>
            </h2>
            <p class="subtitle font-light max-w-2xl mx-auto">
                Discover a new era of digital excellence and seamless solutions designed just for you.
            </p>

            {{-- ปุ่ม CTA (Call to Action) --}}
            <div class="mt-10 opacity-0" style="animation: slideTop 1s ease forwards; animation-delay: 1.5s;">
                {{-- ปุ่มหลัก: Get Started Now --}}
                <a href="#" class="inline-block px-8 py-3 text-lg font-semibold text-gray-900 bg-emerald-300 rounded-full shadow-lg transition duration-300 transform hover:scale-105 hover:bg-emerald-400">
                    Get Started Now
                </a>
                
                {{-- ปุ่มรอง: LOGIN (เพิ่มลิงก์ไปหน้า Login) --}}
                <a href="{{ route('login') }}" class="inline-block ml-4 px-8 py-3 text-lg font-semibold text-white border-2 border-emerald-300 rounded-full transition duration-300 transform hover:bg-emerald-300 hover:text-gray-900">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </a>
            </div>
        </div>
    </div>
</div>

@endsection