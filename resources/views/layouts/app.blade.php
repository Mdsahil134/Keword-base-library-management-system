@php
    use App\Models\Book;
    use App\Models\Category;
    use App\Models\User;
    $__dashBooks = Book::query()->count();
    $__dashCategories = Category::query()->count();
    $__dashUsers = User::query()->count();
    $__navCategories = Category::query()->orderBy('name')->limit(12)->pluck('name');
    $__minimalChrome = request()->routeIs('login', 'register', 'password.request', 'password.reset', 'password.confirm', 'verification.notice');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="activity-time-url" content="{{ auth()->check() ? route('activity.time') : '' }}">
    <title>@yield('title', config('app.name')) — Library</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="dashboard-body font-sans antialiased h-full text-black">
<div id="sidebar-overlay" class="fixed inset-0 z-40 hidden bg-slate-900/40 md:hidden"></div>

<aside id="app-sidebar"
       class="fixed left-0 top-0 z-50 flex h-full w-64 -translate-x-full flex-col border-r border-slate-200 bg-gray-100 text-slate-900 transition-all duration-300 md:translate-x-0">
    <div class="flex h-20 items-center gap-3 border-b border-slate-200 px-4">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-base font-bold text-white">
            @auth{{ Str::substr(auth()->user()->name, 0, 1) }}@else L @endauth
        </span>
        <div class="min-w-0">
            <p class="truncate text-sm font-semibold tracking-tight text-slate-900">@auth{{ auth()->user()->name }}@else Library Hub @endauth</p>
            <p class="truncate text-xs text-[var(--text-muted)]">@auth{{ auth()->user()->email }}@else Explorer @endauth</p>
        </div>
    </div>
    <nav class="flex-1 space-y-1 overflow-y-auto p-3">
        <a href="{{ route('home') }}"
           class="flex items-center gap-3 rounded-xl border px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('home') ? 'nav-side-active' : 'nav-side-idle' }}">
            <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
            Library home
        </a>
        @auth
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 rounded-xl border px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'nav-side-active' : 'nav-side-idle' }}">
                <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6z"/></svg>
                My dashboard
            </a>
        @endauth
        <a href="{{ route('search') }}"
           class="flex items-center gap-3 rounded-xl border px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('search') ? 'nav-side-active' : 'nav-side-idle' }}">
            <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            Search
        </a>
        @auth
            @if(auth()->user()->is_admin)
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 rounded-xl border px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.*') ? 'nav-side-active' : 'nav-side-idle' }}">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.125 1.125 0 11-2.25 0 1.125 1.125 0 012.25 0zm0 0a1.125 1.125 0 11-2.25 0 1.125 1.125 0 012.25 0zm0 0h9.75m-9.75 0a1.125 1.125 0 11-2.25 0 1.125 1.125 0 012.25 0z"/></svg>
                    Admin
                </a>
                <div class="ml-3 space-y-1 border-l border-slate-200 pl-3">
                    <a href="{{ route('admin.books.index') }}"
                       class="block rounded-lg px-3 py-2 text-xs font-medium transition {{ request()->routeIs('admin.books.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}">
                        Manage books
                    </a>
                    <a href="{{ route('admin.categories.index') }}"
                       class="block rounded-lg px-3 py-2 text-xs font-medium transition {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}">
                        Manage categories
                    </a>
                    <a href="{{ route('admin.book-requests.index') }}"
                       class="block rounded-lg px-3 py-2 text-xs font-medium transition {{ request()->routeIs('admin.book-requests.*') ? 'bg-amber-50 text-amber-700' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}">
                        Issue requests
                    </a>
                </div>
            @endif
            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 rounded-xl border border-transparent px-3 py-2.5 text-sm font-medium text-slate-500 transition hover:border-slate-200 hover:bg-slate-100 hover:text-slate-900">
                <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>
                Profile
            </a>
        @else
            <a href="{{ route('login') }}"
               class="flex items-center gap-3 rounded-xl border border-transparent px-3 py-2.5 text-sm font-medium text-slate-500 transition hover:border-slate-200 hover:bg-slate-100 hover:text-slate-900">
                Log in
            </a>
            <a href="{{ route('register') }}"
               class="flex items-center gap-3 rounded-xl border border-transparent px-3 py-2.5 text-sm font-medium text-slate-500 transition hover:border-slate-200 hover:bg-slate-100 hover:text-slate-900">
                Register
            </a>
        @endauth
    </nav>
    <div class="border-t border-slate-200 p-4">
        @auth
            <form action="{{ route('logout') }}" method="post" class="mb-3">
                @csrf
                <button type="submit" class="w-full rounded-xl border border-slate-200 bg-white py-2 text-xs font-medium text-slate-700 hover:bg-slate-100">Log out</button>
            </form>
        @endauth
        <p class="text-xs text-[var(--text-muted)]">Minimal library workspace</p>
    </div>
</aside>

<div id="app-shell" class="flex min-h-screen flex-col overflow-x-hidden transition-all duration-300 md:ml-64">
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 px-4 py-3 backdrop-blur">
        <div class="flex w-full max-w-none flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
            <div class="flex items-center gap-3">
                <button type="button" id="sidebar-toggle" class="inline-flex rounded-xl border border-slate-200 bg-white p-2 text-slate-700 hover:bg-slate-100" aria-label="Toggle sidebar">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" stroke-linecap="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                </button>
                <form id="nav-search-form" action="{{ route('search') }}" method="get" class="search-glow flex w-full min-w-0 flex-1 items-center gap-2 rounded-full border border-slate-200 bg-white px-1 py-1 pl-4 sm:max-w-4xl">
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search the catalogue…" autocomplete="off"
                           class="min-w-0 flex-1 border-0 bg-transparent text-sm text-slate-800 placeholder-[var(--text-placeholder)] outline-none focus:ring-0"/>
                    <span id="nav-search-spinner" class="hidden shrink-0 pr-2" aria-hidden="true"><span class="spinner inline-block"></span></span>
                    <button type="submit" class="btn-glow shrink-0 rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white">
                        Go
                    </button>
                </form>
            </div>
            <div class="flex items-center justify-end gap-2 sm:ml-auto">
                <button type="button" class="rounded-xl border border-slate-200 bg-white p-2.5 text-slate-600" aria-label="Notifications">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9a6 6 0 10-12 0v.05-.7a8.967 8.967 0 01-2.31 6.022 23.848 23.848 0 005.454 1.31m5.713 0a24.255 24.255 0 01-5.713 0m5.713 0a3 3 0 11-5.713 0"/></svg>
                </button>
                <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white py-1.5 pl-2 pr-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-blue-500 text-sm font-semibold text-white">
                        @auth{{ Str::substr(auth()->user()->name, 0, 1) }}@else ? @endauth
                    </span>
                    <div class="hidden text-left text-xs sm:block">
                        @auth
                            <p class="font-medium text-[var(--text-primary)]">{{ Str::limit(auth()->user()->name, 18) }}</p>
                            <p class="text-[var(--text-muted)]">{{ Str::limit(auth()->user()->email, 22) }}</p>
                        @else
                            <p class="font-medium text-slate-700">Guest</p>
                            <a href="{{ route('login') }}" class="text-sky-400 hover:text-sky-300">Sign in</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main id="main-content" class="page-enter w-full max-w-none flex-1 px-6 py-6">
        @isset($header)
            <div class="mb-6 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-[var(--text-primary)]">
                {{ $header }}
            </div>
        @endisset
        @if(session('status'))
            <div class="mb-6 flex items-center justify-between gap-4 rounded-2xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-emerald-700">
                <span>{{ session('status') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 rounded-2xl border border-rose-300 bg-rose-50 px-4 py-3 text-rose-700">
                <ul class="list-inside list-disc space-y-1 text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        @unless($__minimalChrome)
            <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
                <div class="stat-card glass flex flex-col gap-1 p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium uppercase tracking-wider text-[var(--text-muted)]">Books</span>
                        <svg class="h-5 w-5 text-indigo-500/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                    </div>
                    <p class="text-3xl font-bold tabular-nums text-[var(--text-primary)]">{{ $__dashBooks }}</p>
                    <p class="text-xs text-[var(--text-muted)]">Total in catalogue</p>
                </div>
                <div class="stat-card glass flex flex-col gap-1 p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium uppercase tracking-wider text-[var(--text-muted)]">Categories</span>
                        <svg class="h-5 w-5 text-indigo-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/></svg>
                    </div>
                    <p class="text-3xl font-bold tabular-nums text-[var(--text-primary)]">{{ $__dashCategories }}</p>
                    <p class="text-xs text-[var(--text-muted)]">Taxonomy labels</p>
                </div>
                <div class="stat-card glass flex flex-col gap-1 p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium uppercase tracking-wider text-[var(--text-muted)]">Users</span>
                        <svg class="h-5 w-5 text-violet-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z"/></svg>
                    </div>
                    <p class="text-3xl font-bold tabular-nums text-[var(--text-primary)]">{{ $__dashUsers }}</p>
                    <p class="text-xs text-[var(--text-muted)]">Accounts</p>
                </div>
                <div class="stat-card glass flex flex-col gap-1 p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium uppercase tracking-wider text-[var(--text-muted)]">@yield('stat_four_label', 'Searches today')</span>
                        <svg class="h-5 w-5 text-cyan-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6"/></svg>
                    </div>
                    <p class="text-3xl font-bold tabular-nums text-indigo-600">
                        @hasSection('stat_four_value')
                            @yield('stat_four_value')
                        @else
                            @auth
                                <span>{{ auth()->user()->total_searches }}</span>
                            @else
                                <span id="stat-searches-today">0</span>
                            @endauth
                        @endif
                    </p>
                    <p class="text-xs text-[var(--text-muted)]">
                        @hasSection('stat_four_hint')
                            @yield('stat_four_hint')
                        @else
                            @auth
                                Your total catalogue searches
                            @else
                                On this device · session
                            @endauth
                        @endif
                    </p>
                </div>
            </div>
        @endunless

        @isset($slot)
            {{ $slot }}
        @else
            @yield('content')
        @endisset
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
@stack('scripts')
</body>
</html>
