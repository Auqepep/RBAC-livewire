<x-admin.layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dasbor Admin
        </h2>
    </x-slot>

    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <x-mary-stat
                    title="Total Pengguna"
                    description="Pengguna terdaftar"
                    value="{{ $stats['users'] ?? 0 }}"
                    icon="o-users"
                    color="text-primary" />

                <x-mary-stat
                    title="Grup Aktif"
                    description="Grup yang aktif"
                    value="{{ $stats['active_groups'] ?? 0 }}"
                    icon="o-building-office"
                    color="text-success" />

                <x-mary-stat
                    title="Total Role"
                    description="Role tersedia"
                    value="{{ $stats['roles'] ?? 0 }}"
                    icon="o-identification"
                    color="text-warning" />

                <x-mary-stat
                    title="Penugasan Grup"
                    description="Penugasan pengguna-grup"
                    value="{{ $stats['total_assignments'] ?? 0 }}"
                    icon="o-link"
                    color="text-info" />
            </div>

            <!-- Quick Actions and Management Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Quick Actions Card --}}
                <x-mary-card title="Aksi Cepat" subtitle="Tugas administratif umum">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-mary-button 
                            label="Buat Pengguna" 
                            icon="o-user-plus" 
                            class="btn-primary"
                            link="{{ route('admin.users.create') }}" />
                        
                        <x-mary-button 
                            label="Buat Grup" 
                            icon="o-plus-circle" 
                            class="btn-secondary"
                            link="{{ route('admin.groups.create') }}" />
                        
                        <x-mary-button 
                            label="Kelola Grup" 
                            icon="o-rectangle-group" 
                            class="btn-accent"
                            link="{{ route('admin.groups.index') }}" />
                    </div>
                    
                    <x-mary-alert class="mt-4" icon="o-information-circle">
                        <strong>Role Berpusat pada Grup:</strong> Role sekarang dikelola dalam grup. 
                        Untuk membuat atau mengelola role, pergi ke halaman manajemen grup tertentu.
                    </x-mary-alert>
                </x-mary-card>

                {{-- Additional Quick Links --}}
                <div class="space-y-6">
                    <x-mary-card title="Manajemen Pengguna" subtitle="Kelola pengguna sistem">
                        <div class="space-y-2">
                            <x-mary-button 
                                label="Lihat Semua Pengguna" 
                                icon="o-user-group" 
                                class="btn-outline btn-sm w-full"
                                link="{{ route('admin.users.index') }}" />
                                
                            <x-mary-button 
                                label="Direktori Pengguna" 
                                icon="o-book-open" 
                                class="btn-ghost btn-sm w-full"
                                link="{{ route('users.index') }}" />
                        </div>
                    </x-mary-card>

                    <x-mary-card title="Manajemen Grup" subtitle="Organisir pengguna ke dalam grup">
                        <div class="space-y-2">
                            <x-mary-button 
                                label="Semua Grup" 
                                icon="o-rectangle-group" 
                                class="btn-outline btn-sm w-full"
                                link="{{ route('admin.groups.index') }}" />
                        </div>
                    </x-mary-card>
                </div>
            </div>

            <!-- System Settings -->
            <x-mary-card title="Pengaturan Sistem" subtitle="Konfigurasi sistem">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">      
                         
                    <x-mary-button 
                        label="Log Sistem" 
                        icon="o-document-text" 
                        class="btn-ghost btn-sm disabled"
                        disabled />
                </div>
            </x-mary-card>
        </div>
    </div>
</x-admin.layout>
