<x-app-layout>
    <div class="max-w-[1180px] mx-auto sm:px-7 px-4 py-8">
        <!-- Page Head -->
        <div class="mb-8">
            <h1 class="text-[30px] font-serif font-medium tracking-[-0.01em] text-ink mb-1">Dashboard Pesanan</h1>
            <p class="text-muted text-sm">Pantau dan kelola seluruh pesanan katering harian dari berbagai kota.</p>
        </div>

        <!-- Stat Strip -->
        <div class="flex flex-col sm:flex-row border border-border rounded-lg mb-8 bg-main overflow-hidden">
            <div class="flex-1 p-6 border-b sm:border-b-0 sm:border-r border-border">
                <p class="text-[12.5px] text-muted mb-1">Total Pesanan</p>
                <p class="text-[26px] font-serif font-medium text-ink">{{ number_format($stats['totalOrders']) }}</p>
            </div>
            <div class="flex-1 p-6 border-b sm:border-b-0 sm:border-r border-border">
                <p class="text-[12.5px] text-muted mb-1">Omzet Berjalan</p>
                <p class="text-[26px] font-serif font-medium text-accent-warm">Rp {{ number_format($stats['totalRevenue'], 0, ',', '.') }}</p>
            </div>
            <div class="flex-1 p-6 border-b sm:border-b-0 sm:border-r border-border">
                <p class="text-[12.5px] text-muted mb-1">Menunggu Diproses</p>
                <p class="text-[26px] font-serif font-medium text-ink">{{ number_format($stats['pendingCount']) }}</p>
            </div>
            <div class="flex-1 p-6">
                <p class="text-[12.5px] text-muted mb-1">Rata-rata per Pesanan</p>
                <p class="text-[26px] font-serif font-medium text-ink">Rp {{ number_format($stats['averageOrder'], 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Toolbar -->
        <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-faint" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ $search }}" class="block w-full pl-10 pr-3 py-2 border border-border rounded-md text-sm bg-main text-ink placeholder-faint focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-colors" placeholder="Cari pelanggan, kota, atau ID order...">
            </div>
            <div class="flex gap-4 sm:w-auto w-full">
                <select name="status" onchange="this.form.submit()" class="block w-full sm:w-40 border border-border rounded-md py-2 pl-3 pr-10 text-sm bg-main text-ink focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-colors">
                    <option value="">Semua Status</option>
                    @foreach (['pending' => 'Pending', 'diproses' => 'Diproses', 'dikirim' => 'Dikirim', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $val => $label)
                        <option value="{{ $val }}" @selected($status === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="city_id" onchange="this.form.submit()" class="block w-full sm:w-44 border border-border rounded-md py-2 pl-3 pr-10 text-sm bg-main text-ink focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-colors">
                    <option value="">Semua Kota</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}" @selected($cityId === $city->id)>{{ $city->name }}</option>
                    @endforeach
                </select>
                @if ($search || $status || $cityId)
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-3 py-2 border border-border rounded-md text-sm text-muted hover:bg-panel transition-colors whitespace-nowrap">Reset</a>
                @endif
            </div>
        </form>

        <!-- Main Table -->
        <div class="bg-main border border-border rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead>
                    <tr class="bg-panel">
                        <th scope="col" class="px-6 py-3 text-left text-[12.5px] font-medium text-muted uppercase tracking-wider">ID Order</th>
                        <th scope="col" class="px-6 py-3 text-left text-[12.5px] font-medium text-muted uppercase tracking-wider">Pelanggan</th>
                        <th scope="col" class="px-6 py-3 text-left text-[12.5px] font-medium text-muted uppercase tracking-wider">Pesanan</th>
                        <th scope="col" class="px-6 py-3 text-right text-[12.5px] font-medium text-muted uppercase tracking-wider">Total</th>
                        <th scope="col" class="px-6 py-3 text-left text-[12.5px] font-medium text-muted uppercase tracking-wider">Pengiriman & Pembayaran</th>
                        <th scope="col" class="px-6 py-3 text-left text-[12.5px] font-medium text-muted uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-main divide-y divide-border text-[13px]">
                    @forelse ($orders as $order)
                        @php
                            $items = $order->orderItems;
                            $firstItem = $items->first();
                            $restItems = $items->slice(1);
                            $total = $items->sum('subtotal');

                            $statusMap = [
                                'pending'    => ['bg' => 'bg-status-pending-bg',  'text' => 'text-status-pending-text',  'dot' => 'bg-status-pending-text',  'label' => 'Pending'],
                                'diproses'   => ['bg' => 'bg-status-process-bg',  'text' => 'text-status-process-text',  'dot' => 'bg-status-process-text',  'label' => 'Diproses'],
                                'dikirim'    => ['bg' => 'bg-status-ship-bg',     'text' => 'text-status-ship-text',     'dot' => 'bg-status-ship-text',     'label' => 'Dikirim'],
                                'selesai'    => ['bg' => 'bg-status-done-bg',     'text' => 'text-status-done-text',     'dot' => 'bg-status-done-text',     'label' => 'Selesai'],
                                'dibatalkan' => ['bg' => 'bg-status-cancel-bg',   'text' => 'text-status-cancel-text',   'dot' => 'bg-status-cancel-text',   'label' => 'Dibatalkan'],
                            ];
                            $s = $statusMap[$order->status] ?? $statusMap['pending'];
                        @endphp
                        <tr class="hover:bg-panel transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-faint tabular-nums">#{{ $order->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-ink">{{ $order->customer->name }}</div>
                                <div class="text-[12.5px] text-muted">{{ $order->customer->city->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($firstItem)
                                    <div x-data="{ expanded: false }">
                                        <div class="text-ink">{{ $firstItem->qty }}x {{ $firstItem->menu->name }} <span class="text-faint">({{ $firstItem->menu->category->name }})</span></div>
                                        @if ($restItems->count() > 0)
                                            <div x-show="expanded" x-collapse>
                                                @foreach ($restItems as $item)
                                                    <div class="text-ink mt-1 pt-1 border-t border-border/50">{{ $item->qty }}x {{ $item->menu->name }} <span class="text-faint">({{ $item->menu->category->name }})</span></div>
                                                @endforeach
                                            </div>
                                            <button @click="expanded = !expanded" class="text-accent text-[12.5px] font-medium hover:underline focus:outline-none mt-1">
                                                <span x-show="!expanded">+{{ $restItems->count() }} lainnya</span>
                                                <span x-show="expanded">Sembunyikan</span>
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-faint">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-ink font-medium tabular-nums">
                                Rp {{ number_format($total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-ink">{{ $order->courier->name }}</div>
                                <div class="text-[12.5px] text-muted">{{ $order->paymentMethod->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[12px] font-medium {{ $s['bg'] }} {{ $s['text'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $s['dot'] }}"></span>
                                    {{ $s['label'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-muted">
                                <div class="text-faint mb-2">
                                    <svg class="mx-auto h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                </div>
                                Tidak ada pesanan yang cocok dengan filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm">
            <div class="text-muted">
                Menampilkan {{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() }} pesanan
            </div>
            <div>
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
