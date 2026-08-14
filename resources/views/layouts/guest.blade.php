<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>LankaPOS Enterprise | Login</title>

        <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
            .bg-mesh {
                background-color: #f8fafc;
                background-image: 
                    radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.12) 0px, transparent 50%),
                    radial-gradient(at 100% 100%, rgba(20, 184, 166, 0.1) 0px, transparent 50%);
            }
        </style>
    </head>
    <body class="font-sans text-slate-800 antialiased bg-mesh min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-4xl grid grid-cols-1 md:grid-cols-12 gap-0 rounded-3xl bg-white border border-slate-200/80 shadow-2xl overflow-hidden">
            
            <!-- Left Side Branding Banner -->
            <div class="md:col-span-5 bg-gradient-to-br from-slate-900 via-slate-850 to-emerald-950 p-8 text-white flex flex-col justify-between relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl"></div>
                
                <div>
                    <div class="inline-flex items-center space-x-2 bg-emerald-500/10 border border-emerald-500/20 px-3 py-1.5 rounded-full mb-6">
                        <span class="text-lg">🇱🇰</span>
                        <span class="text-xs font-mono font-bold text-emerald-400 uppercase tracking-widest">Sri Lanka Retail POS</span>
                    </div>
                    
                    <h1 class="text-3xl font-extrabold tracking-tight text-white leading-snug">
                        Lanka<span class="text-emerald-400">POS</span><br>Enterprise
                    </h1>
                    <p class="text-xs text-slate-300 mt-3 leading-relaxed">
                        Next-generation multi-store retail management, thermal billing, and inventory system.
                    </p>
                </div>

                <div class="space-y-3 pt-8 border-t border-slate-800 text-xs text-slate-400">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span>Multi-Branch Inventory Sync</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span>80mm Thermal Cashier Receipts</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span>Real-time LKR Financial Analytics</span>
                    </div>
                </div>

                <div class="mt-8 text-[11px] text-slate-500">
                    &copy; {{ date('Y') }} LankaPOS. All rights reserved.
                </div>
            </div>

            <!-- Right Side Auth Form -->
            <div class="md:col-span-7 p-8 md:p-10 flex flex-col justify-center bg-white">
                {{ $slot }}
            </div>

        </div>
    </body>
</html>
