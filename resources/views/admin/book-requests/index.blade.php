@extends('layouts.app')

@section('title', 'Book requests')

@section('content')
    <div class="mb-6 flex items-center justify-between gap-4">
        <h1 class="text-2xl font-bold text-slate-900">Book Requests</h1>
        <a href="{{ route('admin.dashboard') }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Back to admin</a>
    </div>

    <div class="glass overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-clean w-full min-w-[860px] text-left text-sm">
                <thead class="border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3">User</th>
                    <th class="px-4 py-3">Book</th>
                    <th class="px-4 py-3">Availability</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Requested At</th>
                    <th class="px-4 py-3 text-end">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($pendingRequests as $requestItem)
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-800">{{ $requestItem->user?->name }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-900">{{ Str::limit($requestItem->book?->title, 40) }}</p>
                            <p class="text-xs text-slate-500">{{ $requestItem->book?->author }}</p>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $requestItem->book?->available_copies }}/{{ $requestItem->book?->total_copies }}</td>
                        <td class="px-4 py-3"><span class="rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">Pending</span></td>
                        <td class="px-4 py-3 text-slate-500">{{ $requestItem->created_at->diffForHumans() }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <form action="{{ route('admin.book-requests.approve', $requestItem) }}" method="post">
                                    @csrf
                                    <button type="submit" class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700 hover:bg-emerald-100">Approve</button>
                                </form>
                                <form action="{{ route('admin.book-requests.reject', $requestItem) }}" method="post">
                                    @csrf
                                    <button type="submit" class="rounded-full border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-medium text-rose-700 hover:bg-rose-100">Reject</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">No pending requests.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $pendingRequests->links('vendor.pagination.dark-glass') }}</div>
@endsection
