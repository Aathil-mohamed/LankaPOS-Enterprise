<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-900 leading-tight">
                    Product Catalog & Inventory
                </h2>
                <p class="text-xs text-slate-500 mt-1">Manage items, prices, and stock allocations per retail branch</p>
            </div>
            <button x-data x-on:click="$dispatch('open-modal', 'add-product-modal')" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md transition flex items-center space-x-2 text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Add New Product</span>
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

            <!-- Store Filter Bar -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center justify-between">
                <form method="GET" action="{{ route('products.index') }}" class="flex items-center space-x-3 flex-1 max-w-md">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Filter By Store Branch:</label>
                    <select name="warehouse_id" onchange="this.form.submit()" class="w-full bg-slate-50 border-slate-200 text-slate-800 rounded-xl text-xs py-2 px-3 focus:ring-emerald-500">
                        <option value="">All Stores & Warehouses</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ $selectedWarehouseId == $wh->id ? 'selected' : '' }}>
                                {{ $wh->name }} ({{ $wh->code }})
                            </option>
                        @endforeach
                    </select>
                </form>
                @if($selectedWarehouseId)
                    <a href="{{ route('products.index') }}" class="text-xs text-rose-600 hover:text-rose-700 font-bold bg-rose-50 px-3 py-1.5 rounded-xl border border-rose-200">Clear Filter</a>
                @endif
            </div>

            <!-- Product Table -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                                <th class="py-3 px-3">Product Name</th>
                                <th class="py-3 px-3">SKU</th>
                                <th class="py-3 px-3">Category</th>
                                <th class="py-3 px-3">Cost Price</th>
                                <th class="py-3 px-3">Selling Price</th>
                                <th class="py-3 px-3">Store Stock Breakdown</th>
                                <th class="py-3 px-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse($products as $p)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-4 px-3 font-bold text-slate-900 max-w-[220px]">
                                        <div class="truncate">{{ $p->name }}</div>
                                    </td>
                                    <td class="py-4 px-3 font-mono text-emerald-700 font-bold text-xs whitespace-nowrap">{{ $p->sku }}</td>
                                    <td class="py-4 px-3 whitespace-nowrap">
                                        <span class="text-[11px] px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-bold border border-emerald-200">
                                            {{ $p->category?->name ?? 'General' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-3 font-mono text-slate-500 whitespace-nowrap">Rs. {{ number_format($p->cost_price, 2) }}</td>
                                    <td class="py-4 px-3 font-mono font-extrabold text-slate-900 whitespace-nowrap">Rs. {{ number_format($p->selling_price, 2) }}</td>
                                    <td class="py-4 px-3">
                                        <div class="space-y-1">
                                            @forelse($p->inventories as $inv)
                                                <div class="flex items-center space-x-2 text-xs">
                                                    <span class="text-slate-500 font-mono">{{ $inv->warehouse->name }}:</span>
                                                    <span class="font-bold font-mono text-emerald-700">{{ $inv->quantity }} pcs</span>
                                                </div>
                                            @empty
                                                <span class="text-xs text-rose-500 font-mono">0 pcs (No Stock)</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="py-4 px-3 text-right whitespace-nowrap space-x-1">
                                        <button x-data x-on:click="$dispatch('open-modal', 'update-stock-modal-{{ $p->id }}')" class="text-xs text-emerald-700 hover:text-emerald-800 font-bold bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200 transition">Edit Branch Stock</button>
                                        <form method="POST" action="{{ route('products.destroy', $p->id) }}" class="inline" onsubmit="return confirm('Delete product?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-rose-600 hover:text-rose-700 font-bold bg-rose-50 px-3 py-1.5 rounded-xl border border-rose-200 transition">Delete</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Per-Product Stock Modal -->
                                <x-modal name="update-stock-modal-{{ $p->id }}" focusable>
                                    <form method="POST" action="{{ route('products.update-stock', $p->id) }}" class="p-6 bg-white text-slate-800 border border-slate-200 rounded-2xl space-y-4">
                                        @csrf
                                        <h3 class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3">Adjust Stock for: {{ $p->name }}</h3>
                                        <div>
                                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Select Store Branch *</label>
                                            <select name="warehouse_id" required class="w-full bg-slate-50 border-slate-200 text-slate-800 rounded-xl text-xs py-2.5 px-3 focus:ring-emerald-500">
                                                @foreach($warehouses as $w)
                                                    <option value="{{ $w->id }}">{{ $w->name }} ({{ $w->code }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">New Stock Quantity *</label>
                                            <input type="number" name="quantity" min="0" required class="w-full bg-slate-50 border-slate-200 text-slate-800 rounded-xl text-xs font-mono py-2.5 px-3 focus:ring-emerald-500">
                                        </div>
                                        <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                                            <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl text-xs hover:bg-slate-200">Cancel</button>
                                            <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl text-xs shadow-md">Save Stock Level</button>
                                        </div>
                                    </form>
                                </x-modal>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-400 text-xs">No products created yet. Click "Add New Product" above.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            </div>

        </div>
    </div>

    <!-- Add Product Modal -->
    <x-modal name="add-product-modal" focusable>
        <form method="POST" action="{{ route('products.store') }}" class="p-6 bg-white text-slate-800 border border-slate-200 rounded-2xl space-y-4">
            @csrf
            <h3 class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3">Add New Inventory Product</h3>

            <div>
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Product Name *</label>
                <input type="text" name="name" required class="w-full bg-slate-50 border-slate-200 text-slate-800 rounded-xl text-xs py-2.5 px-3 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">SKU Code *</label>
                    <input type="text" name="sku" placeholder="E.g. FOOD-NEST-001" required class="w-full bg-slate-50 border-slate-200 text-slate-800 rounded-xl text-xs font-mono py-2.5 px-3 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Barcode</label>
                    <input type="text" name="barcode" class="w-full bg-slate-50 border-slate-200 text-slate-800 rounded-xl text-xs font-mono py-2.5 px-3 focus:ring-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Category *</label>
                    <select name="category_id" required class="w-full bg-slate-50 border-slate-200 text-slate-800 rounded-xl text-xs py-2.5 px-3 focus:ring-emerald-500">
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Brand</label>
                    <select name="brand_id" class="w-full bg-slate-50 border-slate-200 text-slate-800 rounded-xl text-xs py-2.5 px-3 focus:ring-emerald-500">
                        <option value="">None</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Unit *</label>
                    <select name="unit_id" required class="w-full bg-slate-50 border-slate-200 text-slate-800 rounded-xl text-xs py-2.5 px-3 focus:ring-emerald-500">
                        @foreach($units as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->short_name }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Cost Price (Rs.) *</label>
                    <input type="number" step="0.01" name="cost_price" required class="w-full bg-slate-50 border-slate-200 text-slate-800 rounded-xl text-xs font-mono py-2.5 px-3 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Selling Price (Rs.) *</label>
                    <input type="number" step="0.01" name="selling_price" required class="w-full bg-slate-50 border-slate-200 text-slate-800 rounded-xl text-xs font-mono py-2.5 px-3 focus:ring-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Initial Stock (Qty)</label>
                    <input type="number" name="initial_stock" value="10" class="w-full bg-slate-50 border-slate-200 text-slate-800 rounded-xl text-xs font-mono py-2.5 px-3 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Store Warehouse</label>
                    <select name="warehouse_id" class="w-full bg-slate-50 border-slate-200 text-slate-800 rounded-xl text-xs py-2.5 px-3 focus:ring-emerald-500">
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl text-xs hover:bg-slate-200">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl text-xs shadow-md">Save Product</button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
