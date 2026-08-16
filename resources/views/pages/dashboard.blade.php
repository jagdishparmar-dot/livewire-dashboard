<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Dashboard')] class extends Component
{
    public string $search = '';

    public string $status = 'all';

    public int $revenue = 48250;

    public int $orders = 186;

    public int $customers = 1240;

    public float $conversion = 3.8;

    public int $activeNow = 128;

    public array $labels = [];

    public array $revenuePoints = [];

    public array $orderPoints = [];

    public array $traffic = [
        'Direct' => 42,
        'Organic' => 31,
        'Referral' => 17,
        'Social' => 10,
    ];

    public array $sparklines = [];

    public array $feed = [];

    public function mount(): void
    {
        $now = now();

        for ($i = 23; $i >= 0; $i--) {
            $this->labels[] = $now->copy()->subSeconds($i * 8)->format('H:i:s');
            $this->revenuePoints[] = random_int(180, 420);
            $this->orderPoints[] = random_int(3, 14);
        }

        foreach (['revenue', 'orders', 'customers', 'conversion'] as $key) {
            $this->sparklines[$key] = collect(range(1, 16))
                ->map(fn () => random_int(8, 26))
                ->all();
        }

        $this->pushEvent('Live analytics connected', 'ok');
        $this->pushEvent('Checkout volume is trending up', 'ok');
    }

    public function tick(): void
    {
        $this->refreshStats();

        $this->labels[] = now()->format('H:i:s');
        array_shift($this->labels);

        $this->revenuePoints[] = max(140, end($this->revenuePoints) + random_int(-38, 52));
        array_shift($this->revenuePoints);

        $this->orderPoints[] = max(2, min(18, end($this->orderPoints) + random_int(-3, 4)));
        array_shift($this->orderPoints);

        foreach ($this->sparklines as $key => $points) {
            $points[] = max(6, min(28, end($points) + random_int(-4, 5)));
            array_shift($points);
            $this->sparklines[$key] = array_values($points);
        }

        $this->activeNow = max(42, min(260, $this->activeNow + random_int(-7, 9)));
        $this->jitterTraffic();

        if (random_int(1, 100) > 32) {
            $events = [
                ['Paid order #LW-'.random_int(1100, 1400).' just cleared', 'ok'],
                ['New customer signed up from organic search', 'ok'],
                ['3 visitors started checkout', 'info'],
                ['Referral spike from partner campaign', 'info'],
                ['A cart was recovered automatically', 'ok'],
                ['Pending payment waiting on customer', 'warn'],
            ];
            $event = $events[array_rand($events)];
            $this->pushEvent($event[0], $event[1]);
        }

        $this->dispatch('dashboard-tick',
            labels: $this->labels,
            revenue: $this->revenuePoints,
            orders: $this->orderPoints,
            traffic: $this->traffic,
            sparks: $this->sparklines,
        );
    }

    public function refreshStats(): void
    {
        $this->revenue += random_int(80, 640);
        $this->orders += random_int(1, 5);
        $this->customers += random_int(0, 3);
        $this->conversion = round(max(2.1, min(6.8, $this->conversion + (random_int(-3, 4) / 10))), 1);
    }

    public function with(): array
    {
        $orders = collect([
            ['id' => 'LW-1042', 'customer' => 'Ava Patel', 'product' => 'Pro plan', 'amount' => 129, 'status' => 'paid', 'date' => 'Aug 14'],
            ['id' => 'LW-1041', 'customer' => 'Noah Kim', 'product' => 'Starter kit', 'amount' => 49, 'status' => 'pending', 'date' => 'Aug 14'],
            ['id' => 'LW-1040', 'customer' => 'Mia Chen', 'product' => 'Team seats', 'amount' => 240, 'status' => 'paid', 'date' => 'Aug 13'],
            ['id' => 'LW-1039', 'customer' => 'Liam Brooks', 'product' => 'Add-on storage', 'amount' => 19, 'status' => 'refunded', 'date' => 'Aug 13'],
            ['id' => 'LW-1038', 'customer' => 'Sofia Alvarez', 'product' => 'Pro plan', 'amount' => 129, 'status' => 'paid', 'date' => 'Aug 12'],
            ['id' => 'LW-1037', 'customer' => 'Ethan Cole', 'product' => 'Consulting', 'amount' => 800, 'status' => 'pending', 'date' => 'Aug 12'],
            ['id' => 'LW-1036', 'customer' => 'Harper Singh', 'product' => 'Starter kit', 'amount' => 49, 'status' => 'paid', 'date' => 'Aug 11'],
            ['id' => 'LW-1035', 'customer' => 'Olivia Reed', 'product' => 'Team seats', 'amount' => 240, 'status' => 'cancelled', 'date' => 'Aug 11'],
        ]);

        $filtered = $orders
            ->when($this->status !== 'all', fn ($items) => $items->where('status', $this->status))
            ->filter(function ($order) {
                if ($this->search === '') {
                    return true;
                }

                $haystack = strtolower($order['id'].' '.$order['customer'].' '.$order['product']);

                return str_contains($haystack, strtolower($this->search));
            })
            ->values();

        $conversionAngle = (int) round(($this->conversion / 8) * 100);

        return [
            'recentOrders' => $filtered,
            'conversionAngle' => min(100, $conversionAngle),
            'projectUrl' => config('app.url'),
            'anonKey' => 'sb_publishable_lw_'.substr(hash('sha256', (string) config('app.key')), 0, 24),
            'serviceKey' => 'sb_secret_lw_'.substr(hash('sha256', 'service'.config('app.key')), 0, 24),
            'tableCount' => 8,
            'dbSize' => '24 MB',
        ];
    }

    protected function jitterTraffic(): void
    {
        $keys = array_keys($this->traffic);
        $from = $keys[array_rand($keys)];
        $to = $keys[array_rand($keys)];

        if ($from === $to || $this->traffic[$from] < 8) {
            return;
        }

        $this->traffic[$from]--;
        $this->traffic[$to]++;
    }

    protected function pushEvent(string $text, string $tone): void
    {
        array_unshift($this->feed, [
            'id' => (string) str()->ulid(),
            'text' => $text,
            'time' => now()->format('H:i:s'),
            'tone' => $tone,
        ]);

        $this->feed = array_slice($this->feed, 0, 7);
    }
};
?>

<div
    wire:poll.2s.visible="tick"
    x-data="dashboardCharts({
        labels: @js($labels),
        revenue: @js($revenuePoints),
        orders: @js($orderPoints),
        traffic: @js($traffic),
        sparks: @js($sparklines),
    })"
>
    <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <flux:heading size="xl" level="1">livewire-prod</flux:heading>
                <flux:badge color="lime" size="sm">
                    <span class="live-dot h-1.5 w-1.5 rounded-full bg-brand"></span>
                    Project is healthy
                </flux:badge>
            </div>
            <flux:text class="mt-2">Local · PHP {{ PHP_MAJOR_VERSION }}.{{ PHP_MINOR_VERSION }} · SQLite · metrics refresh every 2s</flux:text>
        </div>
        <div class="flex items-center gap-3">
            <flux:button wire:click="tick">Refresh</flux:button>
            <flux:button variant="primary" icon="link" @click="$flux.modal('connect').show()">
                Connect
            </flux:button>
        </div>
    </div>

    <div class="mb-6 grid gap-6 lg:grid-cols-3">
        <flux:card>
            <flux:heading>Client libraries</flux:heading>
            <flux:text class="mt-1">Start querying this project from your app.</flux:text>
            <div class="mt-4 flex flex-wrap gap-2">
                <flux:badge>Laravel</flux:badge>
                <flux:badge>Livewire</flux:badge>
                <flux:badge>REST</flux:badge>
                <flux:badge>PHP</flux:badge>
            </div>
        </flux:card>
        <flux:card class="lg:col-span-2">
            <flux:heading>Project API</flux:heading>
            <flux:text class="mt-1">Keys for this local environment</flux:text>
            <div class="mt-4 space-y-3">
                <div class="flex items-center gap-4">
                    <span class="w-28 shrink-0 text-sm text-zinc-500">URL</span>
                    <code class="min-w-0 flex-1 truncate rounded-lg bg-zinc-100 px-3 py-2 font-mono text-sm text-zinc-800">{{ $projectUrl }}</code>
                </div>
                <div class="flex items-center gap-4">
                    <span class="w-28 shrink-0 text-sm text-zinc-500">anon</span>
                    <code class="min-w-0 flex-1 truncate rounded-lg bg-zinc-100 px-3 py-2 font-mono text-sm text-zinc-800">{{ $anonKey }}</code>
                </div>
                <div class="flex items-center gap-4">
                    <span class="w-28 shrink-0 text-sm text-zinc-500">service_role</span>
                    <code class="min-w-0 flex-1 truncate rounded-lg bg-zinc-100 px-3 py-2 font-mono text-sm text-zinc-800">{{ $serviceKey }}</code>
                </div>
            </div>
        </flux:card>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
        <flux:card>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <flux:text>Database</flux:text>
                    <p class="mt-1 text-2xl font-semibold tabular-nums text-zinc-900">${{ number_format($revenue) }}</p>
                    <flux:text class="mt-1">{{ $dbSize }} · {{ $tableCount }} tables</flux:text>
                </div>
                <div wire:ignore class="h-12 w-32 shrink-0">
                    <canvas x-ref="sparkRevenue"></canvas>
                </div>
            </div>
        </flux:card>
        <flux:card>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <flux:text>Auth</flux:text>
                    <p class="mt-1 text-2xl font-semibold tabular-nums text-zinc-900">{{ number_format($customers) }}</p>
                    <flux:text class="mt-1">Registered users</flux:text>
                </div>
                <div wire:ignore class="h-12 w-32 shrink-0">
                    <canvas x-ref="sparkCustomers"></canvas>
                </div>
            </div>
        </flux:card>
        <flux:card>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <flux:text>Storage</flux:text>
                    <p class="mt-1 text-2xl font-semibold tabular-nums text-zinc-900">{{ number_format($orders) }}</p>
                    <flux:text class="mt-1">Objects this period</flux:text>
                </div>
                <div wire:ignore class="h-12 w-32 shrink-0">
                    <canvas x-ref="sparkOrders"></canvas>
                </div>
            </div>
        </flux:card>
        <flux:card>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <flux:text>Realtime</flux:text>
                    <p class="mt-1 text-2xl font-semibold tabular-nums text-zinc-900">{{ $activeNow }}</p>
                    <flux:text class="mt-1">{{ $conversion }}% conversion</flux:text>
                </div>
                <div wire:ignore class="h-12 w-32 shrink-0">
                    <canvas x-ref="sparkConversion"></canvas>
                </div>
            </div>
        </flux:card>
    </div>

    <div class="mb-6 grid gap-6 xl:grid-cols-3">
        <flux:card class="p-0 xl:col-span-2">
            <div class="flex items-center justify-between border-b border-zinc-200 px-6 py-4">
                <div>
                    <flux:heading>API requests</flux:heading>
                    <flux:text class="mt-1">Incoming volume over the last 3 minutes</flux:text>
                </div>
                <span class="font-mono text-sm text-zinc-400">{{ now()->format('H:i:s') }}</span>
            </div>
            <div wire:ignore class="h-64 px-4 pb-4 pt-3">
                <canvas x-ref="revenue"></canvas>
            </div>
        </flux:card>

        <div class="grid gap-6">
            <flux:card>
                <div class="mb-4 flex items-center justify-between">
                    <flux:heading>Active now</flux:heading>
                    <span class="tabular-nums text-2xl font-semibold text-zinc-900">{{ $activeNow }}</span>
                </div>
                <div class="mb-3 h-2 overflow-hidden rounded-full bg-zinc-100">
                    <div class="h-full rounded-full bg-brand transition-all duration-700" style="width: {{ min(100, $activeNow / 2.6) }}%"></div>
                </div>
                <div class="flex items-center justify-between text-sm text-zinc-500">
                    <span>Connected clients</span>
                    <span class="font-medium text-emerald-700">realtime</span>
                </div>
            </flux:card>

            <flux:card>
                <div class="mb-4 flex items-center justify-between">
                    <flux:heading>Quota</flux:heading>
                    <flux:text>Goal 8%</flux:text>
                </div>
                <div class="flex items-center gap-4">
                    <div class="relative h-20 w-20 shrink-0">
                        <svg viewBox="0 0 36 36" class="h-20 w-20 -rotate-90">
                            <circle cx="18" cy="18" r="15.5" fill="none" stroke="#e5e5e5" stroke-width="3.5" pathLength="100"></circle>
                            <circle cx="18" cy="18" r="15.5" fill="none" stroke="#3ecf8e" stroke-width="3.5" stroke-linecap="round" pathLength="100"
                                stroke-dasharray="{{ $conversionAngle }} 100"
                                class="transition-all duration-700"></circle>
                        </svg>
                        <span class="absolute inset-0 grid place-items-center text-sm font-semibold text-zinc-900">{{ $conversion }}%</span>
                    </div>
                    <flux:text>Live checkout conversion against the 8% project target.</flux:text>
                </div>
            </flux:card>
        </div>
    </div>

    <div class="mb-6 grid gap-6 xl:grid-cols-3">
        <flux:card class="p-0">
            <div class="border-b border-zinc-200 px-6 py-4">
                <flux:heading>Database queries</flux:heading>
                <flux:text class="mt-1">Latest interval highlighted</flux:text>
            </div>
            <div wire:ignore class="h-56 px-4 pb-4 pt-3">
                <canvas x-ref="orders"></canvas>
            </div>
        </flux:card>

        <flux:card class="p-0">
            <div class="border-b border-zinc-200 px-6 py-4">
                <flux:heading>Traffic mix</flux:heading>
                <flux:text class="mt-1">Share shifts as sessions arrive</flux:text>
            </div>
            <div wire:ignore class="h-56 px-4 pb-3 pt-3">
                <canvas x-ref="traffic"></canvas>
            </div>
        </flux:card>

        <flux:card class="p-0">
            <div class="flex items-center justify-between border-b border-zinc-200 px-6 py-4">
                <div>
                    <flux:heading>Logs</flux:heading>
                    <flux:text class="mt-1">Newest events first</flux:text>
                </div>
                <span class="relative flex h-2.5 w-2.5">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-brand opacity-60"></span>
                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-brand"></span>
                </span>
            </div>
            <ul class="divide-y divide-zinc-100">
                @foreach ($feed as $item)
                    <li wire:key="{{ $item['id'] }}" class="feed-item flex items-start gap-3 px-6 py-3">
                        <span @class([
                            'mt-2 h-2 w-2 shrink-0 rounded-full',
                            'bg-brand' => $item['tone'] === 'ok',
                            'bg-sky-500' => $item['tone'] === 'info',
                            'bg-amber-500' => $item['tone'] === 'warn',
                        ])></span>
                        <span class="min-w-0 flex-1 text-sm text-zinc-700">{{ $item['text'] }}</span>
                        <span class="shrink-0 font-mono text-xs text-zinc-400">{{ $item['time'] }}</span>
                    </li>
                @endforeach
            </ul>
        </flux:card>
    </div>

    <flux:card class="mb-6">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <flux:heading>Channel heat</flux:heading>
                <flux:text class="mt-1">Bars ease as traffic redistributes</flux:text>
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($traffic as $channel => $share)
                <div wire:key="channel-{{ $channel }}">
                    <div class="mb-2 flex items-center justify-between text-sm">
                        <span class="text-zinc-600">{{ $channel }}</span>
                        <span class="tabular-nums font-medium text-zinc-900">{{ $share }}%</span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-zinc-100">
                        <div class="channel-bar h-full rounded-full bg-brand transition-all duration-700" style="width: {{ $share }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </flux:card>

    <flux:card class="overflow-hidden p-0">
        <div class="flex flex-col gap-4 border-b border-zinc-200 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading>Table Editor · public.orders</flux:heading>
                <flux:text class="mt-1">{{ $recentOrders->count() }} rows · schema cache</flux:text>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                <flux:input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Filter rows..."
                    class="sm:w-56"
                />
                <x-select
                    wire:model.live="status"
                    class="sm:w-44"
                    placeholder="All statuses"
                    :options="[
                        'all' => 'All statuses',
                        'paid' => 'Paid',
                        'pending' => 'Pending',
                        'refunded' => 'Refunded',
                        'cancelled' => 'Cancelled',
                    ]"
                />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse text-left text-sm">
                <thead class="bg-zinc-50 text-xs font-medium text-zinc-500">
                    <tr class="border-b border-zinc-200">
                        <th class="w-10 border-r border-zinc-200 px-4 py-3 font-medium"></th>
                        <th class="border-r border-zinc-200 px-4 py-3 font-medium">id <span class="font-normal text-zinc-400">text</span></th>
                        <th class="border-r border-zinc-200 px-4 py-3 font-medium">customer <span class="font-normal text-zinc-400">text</span></th>
                        <th class="border-r border-zinc-200 px-4 py-3 font-medium">product <span class="font-normal text-zinc-400">text</span></th>
                        <th class="border-r border-zinc-200 px-4 py-3 font-medium">amount <span class="font-normal text-zinc-400">int8</span></th>
                        <th class="border-r border-zinc-200 px-4 py-3 font-medium">status <span class="font-normal text-zinc-400">enum</span></th>
                        <th class="px-4 py-3 font-medium">created_at <span class="font-normal text-zinc-400">date</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentOrders as $order)
                        <tr wire:key="{{ $order['id'] }}" class="border-b border-zinc-100 hover:bg-zinc-50">
                            <td class="border-r border-zinc-100 px-4 py-3 text-zinc-400">
                                <flux:checkbox />
                            </td>
                            <td class="border-r border-zinc-100 px-4 py-3 font-mono text-sm text-zinc-900">{{ $order['id'] }}</td>
                            <td class="border-r border-zinc-100 px-4 py-3">{{ $order['customer'] }}</td>
                            <td class="border-r border-zinc-100 px-4 py-3 text-zinc-600">{{ $order['product'] }}</td>
                            <td class="border-r border-zinc-100 px-4 py-3 font-mono tabular-nums">{{ $order['amount'] }}</td>
                            <td class="border-r border-zinc-100 px-4 py-3">
                                @php
                                    $colors = [
                                        'paid' => 'lime',
                                        'pending' => 'amber',
                                        'refunded' => 'zinc',
                                        'cancelled' => 'red',
                                    ];
                                @endphp
                                <flux:badge size="sm" :color="$colors[$order['status']]">
                                    {{ $order['status'] }}
                                </flux:badge>
                            </td>
                            <td class="px-4 py-3 text-zinc-500">{{ $order['date'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-zinc-500">
                                No rows match your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </flux:card>
</div>
