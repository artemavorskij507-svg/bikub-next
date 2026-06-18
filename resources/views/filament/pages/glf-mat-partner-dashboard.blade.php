<x-filament-panels::page>
    <!-- Stats Overview -->
    <div class="grid gap-4 mb-6">
        @livewire(\App\Filament\Widgets\GLFMaTStatsOverview::class)
    </div>

    <!-- Quick Actions Section -->
    <div class="bg-white dark:bg-slate-800 rounded-lg p-6 mb-6 shadow">
        <h2 class="text-lg font-semibold mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('filament.admin.resources.orders.index', ['tableFilters[status]' => 'pending']) }}"
               class="block p-4 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition text-center">
                <div class="text-2xl font-bold text-blue-600">📋</div>
                <div class="font-medium mt-2">Pending Orders</div>
                <div class="text-sm text-slate-500">Review & confirm</div>
            </a>

            <a href="{{ route('filament.admin.resources.orders.index', ['tableFilters[scenario.title]' => 'Ready%20food%20delivery']) }}"
               class="block p-4 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition text-center">
                <div class="text-2xl font-bold text-green-600">🚚</div>
                <div class="font-medium mt-2">Delivery Orders</div>
                <div class="text-sm text-slate-500">Assign & track</div>
            </a>

            <a href="{{ route('filament.admin.resources.orders.index', ['tableFilters[scenario.title]' => 'Table%20reservation']) }}"
               class="block p-4 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition text-center">
                <div class="text-2xl font-bold text-purple-600">🍽️</div>
                <div class="font-medium mt-2">Reservations</div>
                <div class="text-sm text-slate-500">Booking requests</div>
            </a>

            <a href="{{ route('filament.admin.resources.orders.index', ['tableFilters[status]' => 'completed']) }}"
               class="block p-4 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition text-center">
                <div class="text-2xl font-bold text-teal-600">✅</div>
                <div class="font-medium mt-2">Completed</div>
                <div class="text-sm text-slate-500">Today's success</div>
            </a>
        </div>
    </div>

    <!-- Manual Confirmation Notice -->
    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4 mb-6">
        <div class="flex items-start gap-3">
            <span class="text-xl">ℹ️</span>
            <div>
                <h3 class="font-semibold text-amber-900 dark:text-amber-100">Manual Confirmation Required</h3>
                <p class="text-sm text-amber-800 dark:text-amber-200 mt-1">
                    All customer orders require manual confirmation from your staff. Click "Confirm Order" to approve delivery or table booking.
                    Once confirmed, BiKuBe workers will be assigned automatically through our dispatch system.
                </p>
            </div>
        </div>
    </div>

    <!-- Payment Status Notice -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
        <div class="flex items-start gap-3">
            <span class="text-xl">💳</span>
            <div>
                <h3 class="font-semibold text-blue-900 dark:text-blue-100">Payment Readiness</h3>
                <p class="text-sm text-blue-800 dark:text-blue-200 mt-1">
                    Online payment is currently <strong>manual only</strong>. Customers can pay cash at delivery or arrange payment directly with your restaurant.
                    Payment provider integration (Vipps) is available upon request.
                </p>
            </div>
        </div>
    </div>

    <!-- Orders Quick List -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold">Today's Orders (Last 10)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-700">
                        <th class="px-6 py-3 text-left text-sm font-semibold">Order #</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Type</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Customer</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Phone</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(\App\Models\Order::latest('created_at')->limit(10)->get() as $order)
                        <tr class="border-b border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                            <td class="px-6 py-4 font-mono text-sm">{{ $order->order_number }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if(str_contains($order->service_scenario_key, 'delivery'))
                                    <span class="inline-block px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-100 rounded text-xs font-semibold">Delivery</span>
                                @elseif(str_contains($order->service_scenario_key, 'booking'))
                                    <span class="inline-block px-2 py-1 bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-100 rounded text-xs font-semibold">Booking</span>
                                @else
                                    <span class="inline-block px-2 py-1 bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 rounded text-xs font-semibold">{{ $order->scenario->title }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $order->customer_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm">{{ $order->customer_phone ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm">
                                @php
                                    $statusColors = [
                                        'pending' => 'amber',
                                        'confirmed' => 'blue',
                                        'assigned' => 'cyan',
                                        'completed' => 'green',
                                        'rejected' => 'red',
                                    ];
                                    $color = $statusColors[$order->status] ?? 'gray';
                                @endphp
                                <span class="inline-block px-2 py-1 bg-{{ $color }}-100 dark:bg-{{ $color }}-900 text-{{ $color }}-800 dark:text-{{ $color }}-100 rounded text-xs font-semibold">
                                    {{ str($order->status)->replace('_', ' ')->title() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('filament.admin.resources.orders.view', $order) }}"
                                   class="text-blue-600 dark:text-blue-400 hover:underline">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-slate-500 dark:text-slate-400">
                                No orders yet. Customers can submit delivery or booking requests from the public page.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Documentation Links -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-slate-50 dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
            <h3 class="font-semibold mb-2">📚 Documentation</h3>
            <ul class="text-sm space-y-1 text-slate-600 dark:text-slate-400">
                <li>• <a href="/docs/CMPAAA-141_EXECUTIVE_SUMMARY.md" class="text-blue-600 dark:text-blue-400 hover:underline">Executive Summary</a></li>
                <li>• <a href="/docs/CMPAAA-141_GLF_MAT_PRODUCT_CONTRACT.md" class="text-blue-600 dark:text-blue-400 hover:underline">Product Contract</a></li>
                <li>• <a href="/docs/CMPAAA-141_GLF_MAT_PHASE_2_WIRING.md" class="text-blue-600 dark:text-blue-400 hover:underline">Phase 2 Wiring</a></li>
            </ul>
        </div>

        <div class="bg-slate-50 dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
            <h3 class="font-semibold mb-2">🔗 Quick Links</h3>
            <ul class="text-sm space-y-1 text-slate-600 dark:text-slate-400">
                <li>• <a href="{{ route('filament.admin.resources.orders.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">All Orders</a></li>
                <li>• <a href="/services/food" class="text-blue-600 dark:text-blue-400 hover:underline">Public Food Page</a></li>
                <li>• <a href="/worker/orders" class="text-blue-600 dark:text-blue-400 hover:underline">Worker Deliveries</a></li>
            </ul>
        </div>
    </div>
</x-filament-panels::page>
