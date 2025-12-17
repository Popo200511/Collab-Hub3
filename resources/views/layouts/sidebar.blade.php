<aside class="w-56 bg-blue-800 text-white flex flex-col p-4 space-y-4">
    <h2 class="text-2xl font-semibold mb-4">ERP</h2>

    <!-- Billing -->
    <a href="{{ route('billing.home') }}"
        class="flex items-center gap-2 px-3 py-2 rounded-md transition
       @if (Request::is('billing*')) bg-white text-blue-800 shadow-md @else hover:bg-white hover:text-blue-800 @endif">
        <i class="fa-solid fa-warehouse"></i>
        <span class="text-xl font-bold">Billing</span>
    </a>

    <!-- PR Dropdown -->
    <div x-data="{ open: false }" class="flex flex-col">
        <!-- ปุ่ม PR -->
        <button type="button" @click="open = !open"
            class="flex items-center justify-between gap-2 px-3 py-2 rounded-md transition w-full
               hover:bg-white hover:text-blue-800">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-file-invoice text-white"></i>
                <span class="text-xl font-bold">PR</span>
            </div>
            <i :class="open ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'" class="text-white"></i>
        </button>

        <!-- เมนูย่อย -->
        <div x-show="open || window.location.pathname.startsWith('/pr')" x-transition
            class="flex flex-col pl-8 mt-2 space-y-1">

            <!-- PR -->
            <a href="{{ route('pr.home') }}"
                class="px-3 py-1 rounded-md transition font-semibold {{ Route::currentRouteName() === 'pr.home' ? 'bg-white text-blue-800' : 'text-blue hover:bg-white hover:text-blue-800' }}">
                PR
            </a>

            <!-- Purchase -->
            <a href="{{ route('pr.purchase') }}"
                class="px-3 py-1 rounded-md transition font-semibold {{ Route::currentRouteName() === 'pr.purchase' ? 'bg-white text-blue-800' : 'text-blue hover:bg-white hover:text-blue-800' }}">
                Purchase
            </a>

        </div>
    </div>



    <!-- WO -->
    <a href="{{ route('wo.home') }}"
        class="flex items-center gap-2 px-3 py-2 rounded-md transition
       @if (Request::is('wo*')) bg-white text-blue-800 shadow-md @else hover:bg-white hover:text-blue-800 @endif">
        <i class="fa-solid fa-clock-rotate-left"></i>
        <span class="text-xl font-bold">WO</span>
    </a>

    <!-- SubcInvoice -->
    <a href="{{ route('subcinvoice.home') }}"
        class="flex items-center gap-2 px-3 py-2 rounded-md transition
       @if (Request::is('subcinvoice*')) bg-white text-blue-800 shadow-md @else hover:bg-white hover:text-blue-800 @endif">
        <i class="fa-solid fa-clock-rotate-left"></i>
        <span class="text-xl font-bold">SubC Invoice</span>
    </a>
</aside>

<script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.12.0/cdn.min.js" defer></script>