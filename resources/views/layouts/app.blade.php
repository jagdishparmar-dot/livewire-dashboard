<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light" style="color-scheme: light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Dashboard' }} · {{ config('app.name') }}</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body
        class="min-h-screen bg-white antialiased"
        x-data="{
            panel: null,
            togglePanel(name) {
                this.panel = this.panel === name ? null : name
            },
        }"
        @keydown.escape.window="panel = null"
    >
        <flux:sidebar sticky collapsible="mobile" class="border-r border-zinc-200 bg-zinc-50">
            <flux:sidebar.header>
                <flux:sidebar.brand
                    :href="route('dashboard')"
                    :name="config('app.name')"
                    wire:navigate
                >
                    <x-slot:logo class="bg-brand text-studio">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M13.6 2.2L3.2 14.6h7.2L9.2 21.8l11.6-13.8h-7.4l.2-5.8z" />
                        </svg>
                    </x-slot:logo>
                </flux:sidebar.brand>

                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:modal.trigger name="connect">
                <flux:button variant="primary" icon="link" class="w-full">
                    Connect
                </flux:button>
            </flux:modal.trigger>

            <flux:sidebar.nav>
                <flux:sidebar.item
                    icon="home"
                    :href="route('dashboard')"
                    :current="request()->routeIs('dashboard')"
                    wire:navigate
                >
                    Home
                </flux:sidebar.item>

                <flux:sidebar.group heading="Database" class="grid">
                    <flux:sidebar.item
                        icon="table-cells"
                        :href="route('table-editor')"
                        :current="request()->routeIs('table-editor')"
                        wire:navigate
                    >
                        Table Editor
                    </flux:sidebar.item>
                    <flux:sidebar.item
                        icon="command-line"
                        :href="route('sql-editor')"
                        :current="request()->routeIs('sql-editor')"
                        wire:navigate
                    >
                        SQL Editor
                    </flux:sidebar.item>
                    <flux:sidebar.item
                        icon="circle-stack"
                        :href="route('database')"
                        :current="request()->routeIs('database')"
                        wire:navigate
                    >
                        Database
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group heading="Platform" class="grid">
                    <flux:sidebar.item
                        icon="lock-closed"
                        :href="route('authentication')"
                        :current="request()->routeIs('authentication')"
                        wire:navigate
                    >
                        Authentication
                    </flux:sidebar.item>
                    <flux:sidebar.item
                        icon="archive-box"
                        :href="route('storage')"
                        :current="request()->routeIs('storage')"
                        wire:navigate
                    >
                        Storage
                    </flux:sidebar.item>
                    <flux:sidebar.item
                        icon="bolt"
                        :href="route('edge-functions')"
                        :current="request()->routeIs('edge-functions')"
                        wire:navigate
                    >
                        Edge Functions
                    </flux:sidebar.item>
                    <flux:sidebar.item
                        icon="signal"
                        :href="route('realtime')"
                        :current="request()->routeIs('realtime')"
                        wire:navigate
                    >
                        Realtime
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group heading="Observability" class="grid">
                    <flux:sidebar.item
                        icon="chart-bar"
                        :href="route('reports')"
                        :current="request()->routeIs('reports')"
                        wire:navigate
                    >
                        Reports
                    </flux:sidebar.item>
                    <flux:sidebar.item
                        icon="document-text"
                        :href="route('logs')"
                        :current="request()->routeIs('logs')"
                        wire:navigate
                    >
                        Logs
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:sidebar.spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item
                    icon="user"
                    :href="route('profile')"
                    :current="request()->routeIs('profile')"
                    wire:navigate
                >
                    Profile
                </flux:sidebar.item>
                <flux:sidebar.item
                    icon="cog-6-tooth"
                    :href="route('project-settings')"
                    :current="request()->routeIs('project-settings')"
                    wire:navigate
                >
                    Project Settings
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <x-user-menu sidebar class="max-lg:hidden" />
        </flux:sidebar>

        <flux:header class="border-b border-zinc-200 bg-white">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <div class="hidden min-w-0 items-center gap-1 text-sm text-zinc-500 sm:flex">
                <span>livewire-prod</span>
                <flux:icon name="chevron-right" variant="micro" class="text-zinc-400" />
                <span class="font-medium text-zinc-900">{{ $title ?? 'Home' }}</span>
            </div>
            <flux:heading size="sm" class="truncate sm:hidden">{{ $title ?? 'Home' }}</flux:heading>

            <flux:spacer />

            <div class="hidden md:block">
                <flux:input icon="magnifying-glass" placeholder="Search..." kbd="⌘K" class="w-56" />
            </div>

            <flux:button variant="ghost" class="hidden sm:inline-flex">Feedback</flux:button>

            <flux:button variant="subtle" icon="book-open" class="max-md:hidden" />
            <flux:button variant="subtle" icon="bell" />

            <x-user-menu :named="false" class="lg:hidden" />
        </flux:header>

        <flux:main>
            {{ $slot }}

            <flux:modal name="connect" class="md:w-96">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Project API</flux:heading>
                        <flux:text class="mt-2">Use these values in your client apps.</flux:text>
                    </div>

                    <flux:input
                        label="Project URL"
                        :value="config('app.url')"
                        copyable
                        readonly
                    />

                    <flux:input
                        label="anon public"
                        value="sb_publishable_lw_{{ substr(hash('sha256', (string) config('app.key')), 0, 20) }}"
                        copyable
                        readonly
                    />

                    <flux:input
                        label="Database"
                        value="sqlite://database/database.sqlite"
                        readonly
                    />
                </div>
            </flux:modal>
        </flux:main>

        <flux:aside class="hidden border-l border-zinc-200 bg-white xl:flex">
            <div class="flex w-11 shrink-0 flex-col items-center gap-1 py-2">
                <flux:button
                    variant="subtle"
                    icon="command-line"
                    x-bind:class="panel === 'sql' && 'bg-zinc-100 text-zinc-900'"
                    @click="togglePanel('sql')"
                    title="SQL Editor"
                />
                <flux:button
                    variant="subtle"
                    icon="sparkles"
                    x-bind:class="panel === 'advisor' && 'bg-zinc-100 text-zinc-900'"
                    @click="togglePanel('advisor')"
                    title="Advisor"
                />
                <flux:button
                    variant="subtle"
                    icon="cpu-chip"
                    x-bind:class="panel === 'ai' && 'bg-zinc-100 text-zinc-900'"
                    @click="togglePanel('ai')"
                    title="Assistant"
                />
                <flux:button
                    variant="subtle"
                    icon="question-mark-circle"
                    x-bind:class="panel === 'help' && 'bg-zinc-100 text-zinc-900'"
                    @click="togglePanel('help')"
                    title="Help"
                />
            </div>

            <section
                x-show="panel"
                x-cloak
                class="flex w-80 shrink-0 flex-col border-l border-zinc-200 bg-white"
            >
                <div class="flex h-12 items-center justify-between border-b border-zinc-200 px-3">
                    <p class="text-sm font-medium text-zinc-900" x-text="{ sql: 'SQL Editor', advisor: 'Advisor', ai: 'Assistant', help: 'Support' }[panel]"></p>
                    <flux:button variant="subtle" icon="x-mark" size="sm" @click="panel = null" />
                </div>

                <div class="flex-1 overflow-auto p-6" x-show="panel === 'sql'">
                    <pre class="rounded-md bg-zinc-900 p-3 font-mono text-xs leading-relaxed text-zinc-200">select id, customer, product, amount, status
from orders
where status = 'paid'
order by created_at desc
limit 20;</pre>
                    <flux:button variant="primary" class="mt-3">Run</flux:button>
                </div>

                <div class="flex-1 space-y-4 overflow-auto p-6" x-show="panel === 'advisor'">
                    <flux:callout variant="success" icon="check-circle">
                        <flux:callout.heading>Project is healthy</flux:callout.heading>
                        <flux:callout.text>No security or performance issues detected.</flux:callout.text>
                    </flux:callout>
                    <div class="rounded-md border border-zinc-200 p-3">
                        <p class="text-sm font-medium text-zinc-900">Index suggestion</p>
                        <p class="mt-1 text-xs text-zinc-500">Consider an index on <code class="rounded bg-zinc-100 px-1">orders.status</code> for filter queries.</p>
                    </div>
                </div>

                <div class="flex flex-1 flex-col p-6" x-show="panel === 'ai'">
                    <p class="text-sm text-zinc-500">Ask about schema, queries, or this project's metrics.</p>
                    <flux:input placeholder="Ask Assistant..." class="mt-auto" />
                </div>

                <div class="space-y-3 p-6 text-sm text-zinc-600" x-show="panel === 'help'">
                    <p>Livewire 4 dashboard modeled after Supabase Studio.</p>
                    <p>Use Connect for API keys, Home for project health, and Table Editor for live order rows.</p>
                </div>
            </section>
        </flux:aside>

        @livewireScripts
        @fluxScripts
    </body>
</html>
