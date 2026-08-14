<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-900 leading-tight">
                    Executive Dashboard
                </h2>
                <p class="text-xs text-slate-500 mt-1">Real-time overview of sales revenue, operational expenses, and store stock alerts</p>
            </div>
            <a href="{{ route('pos.index') }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md transition flex items-center space-x-2 text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path></svg>
                <span>Launch POS Terminal</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-screen text-slate-800">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Executive Summary Analytics Widgets -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Today's Sales</span>
                        <span class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl border border-emerald-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-2xl font-black text-emerald-600 font-mono">Rs. {{ number_format($todaySales, 2) }}</h3>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Monthly Revenue</span>
                        <span class="p-2.5 bg-blue-50 text-blue-600 rounded-xl border border-blue-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-2xl font-black text-blue-600 font-mono">Rs. {{ number_format($monthlySales, 2) }}</h3>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Monthly Expenses</span>
                        <span class="p-2.5 bg-rose-50 text-rose-600 rounded-xl border border-rose-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-2xl font-black text-rose-600 font-mono">Rs. {{ number_format($totalExpenses, 2) }}</h3>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Products</span>
                        <span class="p-2.5 bg-purple-50 text-purple-600 rounded-xl border border-purple-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-2xl font-black text-purple-600 font-mono">{{ $totalProducts }} SKU</h3>
                    </div>
                </div>
            </div>

            <!-- Content Grid: Recent Transactions & Low Stock Alerts -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Recent Sales Table -->
                <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-extrabold text-slate-900 text-lg">Recent POS Transactions</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                                    <th class="py-3 px-2">Invoice #</th>
                                    <th class="py-3 px-2">Customer</th>
                                    <th class="py-3 px-2">Grand Total</th>
                                    <th class="py-3 px-2">Status</th>
                                    <th class="py-3 px-2">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @forelse($recentSales as $sale)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3.5 px-2 font-mono font-bold text-emerald-700">{{ $sale->invoice_number }}</td>
                                        <td class="py-3.5 px-2 text-slate-700 font-medium">{{ $sale->customer?->name ?? 'Walk-in Customer' }}</td>
                                        <td class="py-3.5 px-2 font-mono font-extrabold text-slate-900">Rs. {{ number_format($sale->grand_total, 2) }}</td>
                                        <td class="py-3.5 px-2">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                {{ $sale->status }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-2 text-xs text-slate-500 font-mono">{{ $sale->created_at->format('M d, H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-slate-400 text-xs">No sales processed yet today.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Low Stock Alert Box -->
                <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h3 class="font-extrabold text-slate-900 text-lg mb-4 flex items-center space-x-2 text-rose-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>Low Stock Warnings</span>
                    </h3>

                    <div class="space-y-3">
                        @forelse($lowStockProducts as $inv)
                            <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl flex items-center justify-between">
                                <div>
                                    <h5 class="text-xs font-bold text-rose-900">{{ $inv->product->name }}</h5>
                                    <span class="text-[11px] text-rose-600 font-mono">SKU: {{ $inv->product->sku }}</span>
                                </div>
                                <span class="px-2.5 py-1 bg-rose-600 text-white font-mono font-extrabold text-xs rounded-lg shadow-sm">
                                    {{ $inv->quantity }} left
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 text-center py-8">All products have sufficient stock levels.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
