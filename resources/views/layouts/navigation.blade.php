<nav x-data="{ open: false }" class="bg-white/90 backdrop-blur-md border-b border-slate-200 sticky top-0 z-40 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center space-x-6">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ auth()->user()?->can('view dashboard') ? route('dashboard') : route('pos.index') }}" class="flex items-center space-x-2">
                        <div class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center shadow-md font-black text-white text-lg">
                            🇱🇰
                        </div>
                        <span class="text-slate-900 font-extrabold text-lg tracking-tight">
                            Lanka<span class="text-emerald-600">POS</span>
                        </span>
                        <span class="text-[10px] uppercase tracking-widest px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-bold border border-emerald-200">
                            Enterprise
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:flex">
                    @can('view dashboard')
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-slate-600 hover:text-slate-900 font-medium">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    @endcan
                    @can('manage products')
                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')" class="text-slate-600 hover:text-slate-900 font-medium">
                        {{ __('Products & Inventory') }}
                    </x-nav-link>
                    @endcan
                    @can('manage settings')
                    <x-nav-link :href="route('stores.index')" :active="request()->routeIs('stores.*')" class="text-slate-600 hover:text-slate-900 font-medium">
                        {{ __('Stores & Branches') }}
                    </x-nav-link>
                    @endcan
                    @can('access pos')
                    <x-nav-link :href="route('pos.index')" :active="request()->routeIs('pos.index')" class="text-slate-600 hover:text-slate-900 font-medium">
                        {{ __('POS Terminal') }}
                    </x-nav-link>
                    @endcan
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3.5 py-1.5 border border-slate-200 text-xs font-semibold rounded-xl text-slate-700 bg-slate-100 hover:bg-slate-200 focus:outline-none transition shadow-sm">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></div>
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4 text-slate-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1.01 0 011.414 0L10 10.586l3.293-3.293a1.01 1.414 1.414l-4 4a1.01 0 01-1.414 0l-4-4a1.01 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile Settings') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>
