<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 bg-slate-50 text-slate-800 min-h-screen" x-data="posSystem()">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Left Side: Catalog & Search -->
            <div class="lg:col-span-7 flex flex-col space-y-5">
                
                <!-- Filter & Control Header Card -->
                <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-sm flex flex-wrap gap-4 items-center justify-between">
                    <div class="flex-1 min-w-[180px]">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Store Branch</label>
                        <select x-model="selectedWarehouse" class="w-full bg-slate-50 border-slate-200 text-slate-800 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 py-2.5 px-3">
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }} ({{ $wh->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex-1 min-w-[180px]">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Product Category</label>
                        <select x-model="selectedCategory" class="w-full bg-slate-50 border-slate-200 text-slate-800 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 py-2.5 px-3">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Product Search Bar -->
                <div class="relative">
                    <input type="text" x-model="searchQuery" placeholder="Search by Product Name, SKU, or Scan Barcode..." 
                        class="w-full pl-11 pr-4 py-3.5 bg-white border border-slate-200 text-slate-900 rounded-2xl focus:ring-2 focus:ring-emerald-500 text-sm shadow-sm transition placeholder:text-slate-400">
                    <svg class="w-5 h-5 text-emerald-600 absolute left-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 overflow-y-auto max-h-[580px] pr-1">
                    <template x-for="product in filteredProducts()" :key="product.id">
                        <div @click="addToCart(product)" 
                            class="bg-white hover:bg-slate-50/80 p-4 rounded-2xl border border-slate-200 hover:border-emerald-500 transition-all duration-200 cursor-pointer flex flex-col justify-between hover:shadow-md hover:-translate-y-0.5 group">
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wide bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200" x-text="product.category ? product.category.name : 'General'"></span>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-mono font-bold" 
                                        :class="getStock(product) > 5 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'"
                                        x-text="getStock(product) + ' in stock'"></span>
                                </div>
                                <h4 class="font-bold text-slate-900 group-hover:text-emerald-600 text-sm line-clamp-2 transition-colors mt-1" x-text="product.name"></h4>
                                <p class="text-[11px] text-slate-400 mt-1 font-mono" x-text="'SKU: ' + product.sku"></p>
                            </div>
                            <div class="mt-4 flex justify-between items-center pt-3 border-t border-slate-100">
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase block font-semibold">Price</span>
                                    <span class="text-base font-extrabold text-slate-900 font-mono" x-text="'Rs. ' + parseFloat(product.selling_price).toLocaleString('en-US', {minimumFractionDigits: 2})"></span>
                                </div>
                                <span class="w-8 h-8 rounded-xl bg-slate-100 group-hover:bg-emerald-600 text-slate-600 group-hover:text-white flex items-center justify-center transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Right Side: POS Shopping Cart & Order Summary -->
            <div class="lg:col-span-5 bg-white rounded-2xl p-5 border border-slate-200 flex flex-col justify-between h-full min-h-[600px] shadow-sm">
                <div>
                    <!-- Header / Customer selector -->
                    <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex-1 mr-3">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Select Customer</label>
                            <select x-model="selectedCustomer" class="w-full bg-slate-50 border-slate-200 text-slate-800 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 py-2 px-3">
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone }})</option>
                                @endforeach
                            </select>
                        </div>
                        <button @click="clearCart()" class="text-xs text-rose-600 hover:text-rose-700 font-bold px-3 py-1.5 bg-rose-50 hover:bg-rose-100 rounded-xl border border-rose-200 transition">
                            Clear Cart
                        </button>
                    </div>

                    <!-- Cart Item Table Header -->
                    <div class="flex text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2 px-1">
                        <div class="flex-1">Item Details</div>
                        <div class="w-24 text-center">Qty</div>
                        <div class="w-24 text-right">Subtotal</div>
                    </div>

                    <!-- Cart Items List -->
                    <div class="overflow-y-auto max-h-[300px] divide-y divide-slate-100 my-1 pr-1">
                        <template x-for="(item, index) in cart" :key="item.product_id">
                            <div class="py-3 flex items-center justify-between">
                                <div class="flex-1 pr-2">
                                    <div class="text-xs font-bold text-slate-900" x-text="item.name"></div>
                                    <div class="text-[11px] text-slate-500 font-mono" x-text="'Rs. ' + item.unit_price.toFixed(2) + ' / unit'"></div>
                                </div>
                                <div class="w-24 flex items-center justify-center space-x-1.5">
                                    <button @click="updateQty(index, -1)" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 flex items-center justify-center font-extrabold text-xs transition">-</button>
                                    <span class="text-xs font-mono font-extrabold text-slate-900 w-6 text-center" x-text="item.quantity"></span>
                                    <button @click="updateQty(index, 1)" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 flex items-center justify-center font-extrabold text-xs transition">+</button>
                                </div>
                                <div class="w-24 text-right font-mono font-extrabold text-xs text-slate-900" x-text="'Rs. ' + (item.quantity * item.unit_price).toLocaleString('en-US', {minimumFractionDigits: 2})"></div>
                            </div>
                        </template>
                        <template x-if="cart.length === 0">
                            <div class="py-16 text-center text-slate-400 text-xs">
                                <svg class="w-12 h-12 mx-auto mb-2 opacity-40 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                Shopping cart is empty.<br>Click any item from the catalog to add.
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Totals & Payment Section -->
                <div class="border-t border-slate-100 pt-4 space-y-4">
                    <div class="space-y-1.5 text-xs">
                        <div class="flex justify-between text-slate-500">
                            <span>Subtotal</span>
                            <span class="text-slate-800 font-mono font-bold" x-text="'Rs. ' + getSubtotal().toLocaleString('en-US', {minimumFractionDigits: 2})"></span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Tax (10%)</span>
                            <span class="text-slate-800 font-mono font-bold" x-text="'Rs. ' + getTax().toLocaleString('en-US', {minimumFractionDigits: 2})"></span>
                        </div>
                        <div class="flex justify-between text-base font-extrabold text-slate-900 pt-2 border-t border-slate-100">
                            <span>Grand Total (LKR)</span>
                            <span class="text-emerald-600 font-mono text-lg" x-text="'Rs. ' + getGrandTotal().toLocaleString('en-US', {minimumFractionDigits: 2})"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Payment Method</label>
                            <select x-model="paymentMethod" class="w-full bg-slate-50 border-slate-200 text-slate-800 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 py-2 px-3">
                                <option value="cash">Cash</option>
                                <option value="card">Credit / Debit Card</option>
                                <option value="digital">Digital Wallet</option>
                                <option value="bank_transfer">Bank Transfer (LKR)</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Paid Amount (LKR)</label>
                            <input type="number" step="0.01" x-model="paidAmount" class="w-full bg-slate-50 border-slate-200 text-slate-900 rounded-xl text-xs font-mono font-bold py-2 px-3 focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <button @click="processCheckout()" :disabled="cart.length === 0 || loading"
                        class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-extrabold rounded-xl shadow-md transition-all duration-200 flex items-center justify-center space-x-2 text-sm tracking-wide">
                        <template x-if="!loading">
                            <span class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <span>Complete Checkout Order</span>
                            </span>
                        </template>
                        <template x-if="loading">
                            <span>Processing Transaction...</span>
                        </template>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <script>
        function posSystem() {
            return {
                products: @json($products),
                selectedWarehouse: '{{ $warehouses->first()?->id ?? 1 }}',
                selectedCategory: '',
                selectedCustomer: '{{ $customers->first()?->id ?? "" }}',
                searchQuery: '',
                paymentMethod: 'cash',
                paidAmount: 0,
                cart: [],
                loading: false,

                filteredProducts() {
                    return this.products.filter(p => {
                        const matchesCategory = !this.selectedCategory || p.category_id == this.selectedCategory;
                        const matchesSearch = !this.searchQuery || 
                            p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                            p.sku.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            (p.barcode && p.barcode.includes(this.searchQuery));
                        return matchesCategory && matchesSearch;
                    });
                },

                getStock(product) {
                    if (!product.inventories || product.inventories.length === 0) return 0;
                    const whInv = product.inventories.find(i => i.warehouse_id == this.selectedWarehouse);
                    return whInv ? whInv.quantity : 0;
                },

                addToCart(product) {
                    const currentStock = this.getStock(product);
                    const existing = this.cart.find(i => i.product_id === product.id);

                    if (existing) {
                        if (existing.quantity + 1 > currentStock) {
                            alert('Cannot add more than available warehouse stock (' + currentStock + ')');
                            return;
                        }
                        existing.quantity++;
                    } else {
                        if (currentStock < 1) {
                            alert('Item is out of stock in this warehouse');
                            return;
                        }
                        this.cart.push({
                            product_id: product.id,
                            name: product.name,
                            unit_price: parseFloat(product.selling_price),
                            tax_rate: parseFloat(product.tax_rate || 0),
                            quantity: 1,
                            discount: 0,
                        });
                    }
                    this.paidAmount = this.getGrandTotal();
                },

                updateQty(index, delta) {
                    const item = this.cart[index];
                    const product = this.products.find(p => p.id === item.product_id);
                    const stock = this.getStock(product);

                    if (delta > 0 && item.quantity + 1 > stock) {
                        alert('Max available stock reached (' + stock + ')');
                        return;
                    }

                    item.quantity += delta;
                    if (item.quantity <= 0) {
                        this.cart.splice(index, 1);
                    }
                    this.paidAmount = this.getGrandTotal();
                },

                removeItem(index) {
                    this.cart.splice(index, 1);
                    this.paidAmount = this.getGrandTotal();
                },

                getSubtotal() {
                    return this.cart.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
                },

                getTax() {
                    return this.cart.reduce((sum, item) => sum + ((item.quantity * item.unit_price) * (item.tax_rate / 100)), 0);
                },

                getGrandTotal() {
                    return this.getSubtotal() + this.getTax();
                },

                async processCheckout() {
                    if (this.cart.length === 0) return;
                    this.loading = true;

                    try {
                        const response = await fetch('{{ route("pos.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                warehouse_id: this.selectedWarehouse,
                                customer_id: this.selectedCustomer,
                                payment_method: this.paymentMethod,
                                paid_amount: parseFloat(this.paidAmount || this.getGrandTotal()),
                                items: this.cart.map(i => ({
                                    product_id: i.product_id,
                                    quantity: i.quantity,
                                    discount: 0
                                }))
                            })
                        });

                        const res = await response.json();
                        if (res.success) {
                            // Open receipt in popup modal/window
                            const printWindow = window.open(res.receipt_url + '?autoprint=1', '_blank', 'width=400,height=600');
                            if (printWindow) {
                                printWindow.focus();
                            }
                            this.cart = [];
                            this.paidAmount = 0;
                            setTimeout(() => { window.location.reload(); }, 1000);
                        } else {
                            alert('Checkout Error: ' + res.message);
                        }
                    } catch (err) {
                        alert('System error occurred during payment processing.');
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
