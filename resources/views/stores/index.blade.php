<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-900 leading-tight">
                    Store & Branch Management
                </h2>
                <p class="text-xs text-slate-500 mt-1">Manage multiple retail outlets, supermarkets, and warehouse locations across Sri Lanka</p>
            </div>
            <button x-data x-on:click="$dispatch('open-modal', 'add-store-modal')" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md transition flex items-center space-x-2 text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Add New Store / Branch</span>
            </button>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen text-slate-800">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm font-semibold shadow-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Stores Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($stores as $st)
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between space-y-4 hover:shadow-md transition">
                        <div>
                            <div class="flex justify-between items-start">
                                <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-bold border border-emerald-200 font-mono">
                                    {{ $st->code }}
                                </span>
                                <span class="text-xs font-bold text-emerald-600 flex items-center space-x-1">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Active Branch</span>
                                </span>
                            </div>
                            <h3 class="font-extrabold text-slate-900 text-lg mt-3">{{ $st->name }}</h3>
                            <p class="text-xs text-slate-500 mt-2 flex items-center space-x-1">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span>{{ $st->address ?? 'Colombo, Sri Lanka' }}</span>
                            </p>
                            <p class="text-xs text-slate-500 mt-1 flex items-center space-x-1">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h32a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"></path></svg>
                                <span>{{ $st->phone ?? '+94 11 234 5678' }}</span>
                            </p>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex justify-between items-center text-xs text-slate-500">
                            <span>Status: Operational</span>
                            <a href="{{ route('pos.index') }}" class="text-emerald-600 font-bold hover:underline">Open POS &rarr;</a>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    <!-- Add Store Modal -->
    <x-modal name="add-store-modal" focusable>
        <form method="POST" action="{{ route('stores.store') }}" class="p-6 bg-white text-slate-800 border border-slate-200 rounded-2xl space-y-4">
            @csrf
            <h3 class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3">Add New Store / Retail Branch</h3>

            <div>
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Store / Branch Name *</label>
                <input type="text" name="name" placeholder="E.g. Kandy Supermarket Branch" required class="w-full bg-slate-50 border-slate-200 text-slate-800 rounded-xl text-xs py-2.5 px-3 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Branch Code *</label>
                    <input type="text" name="code" placeholder="E.g. STORE-KDY-002" required class="w-full bg-slate-50 border-slate-200 text-slate-800 rounded-xl text-xs font-mono py-2.5 px-3 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Phone Number</label>
                    <input type="text" name="phone" placeholder="+94 81 223 4567" class="w-full bg-slate-50 border-slate-200 text-slate-800 rounded-xl text-xs font-mono py-2.5 px-3 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Store Email</label>
                <input type="email" name="email" placeholder="kandy@lankapos.lk" class="w-full bg-slate-50 border-slate-200 text-slate-800 rounded-xl text-xs py-2.5 px-3 focus:ring-emerald-500">
            </div>

            <div>
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Branch Address</label>
                <textarea name="address" rows="2" placeholder="No. 45, Peradeniya Road, Kandy" class="w-full bg-slate-50 border-slate-200 text-slate-800 rounded-xl text-xs py-2.5 px-3 focus:ring-emerald-500"></textarea>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl text-xs hover:bg-slate-200">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl text-xs shadow-md">Create Branch</button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
