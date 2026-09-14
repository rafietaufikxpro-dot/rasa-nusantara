<x-app-layout>
    <div class="max-w-[1180px] mx-auto sm:px-7 px-4 py-8">
        <!-- Page Head -->
        <div class="mb-8">
            <h1 class="text-[30px] font-serif font-medium tracking-[-0.01em] text-ink mb-1">Persetujuan Akun</h1>
            <p class="text-muted text-sm">Daftar akun yang menunggu verifikasi dari admin.</p>
        </div>

        <!-- Flash Message -->
        @if (session('status'))
            <div class="mb-6 px-4 py-3 rounded-md bg-status-process-bg text-status-process-text text-sm font-medium">
                {{ session('status') }}
            </div>
        @endif

        <!-- Table -->
        <div class="bg-main border border-border rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead>
                    <tr class="bg-panel">
                        <th scope="col" class="px-6 py-3 text-left text-[12.5px] font-medium text-muted uppercase tracking-wider">Nama</th>
                        <th scope="col" class="px-6 py-3 text-left text-[12.5px] font-medium text-muted uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-[12.5px] font-medium text-muted uppercase tracking-wider">Mendaftar</th>
                        <th scope="col" class="px-6 py-3 text-left text-[12.5px] font-medium text-muted uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-main divide-y divide-border text-[13px]">
                    @forelse ($pendingUsers as $user)
                        <tr class="hover:bg-panel transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-ink">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-muted">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-muted">{{ $user->created_at->diffForHumans() }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.approvals.approve', $user) }}">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium text-white bg-accent hover:bg-opacity-90 transition-colors focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-1 focus:ring-offset-main"
                                        onclick="return confirm('Setujui akun {{ addslashes($user->name) }}?')">
                                        Setujui
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-muted">
                                <div class="text-faint mb-1">
                                    <svg class="mx-auto h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                Tidak ada akun yang menunggu persetujuan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
