<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="relative">
                        <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.414V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                        </svg>
                        @if($this->getUnreadCount() > 0)
                            <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full animate-pulse">
                                {{ $this->getUnreadCount() }}
                            </span>
                        @endif
                    </div>
                    <span class="font-semibold">Notifikasi Real-time</span>
                </div>
                
                @if(count($notifications) > 0)
                    <button 
                        wire:click="clearAllNotifications"
                        class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                    >
                        Hapus Semua
                    </button>
                @endif
            </div>
        </x-slot>

        @if(count($notifications) > 0)
            <div class="space-y-3">
                @foreach($notifications as $notification)
                    <div 
                        wire:key="notification-{{ $notification['id'] }}"
                        class="flex items-start space-x-3 p-3 {{ $notification['read'] ? 'bg-gray-50 dark:bg-gray-800' : 'bg-blue-50 dark:bg-blue-900/20' }} rounded-lg border {{ $notification['read'] ? 'border-gray-200 dark:border-gray-700' : 'border-blue-200 dark:border-blue-700' }} transition-all duration-200"
                    >
                        <!-- Notification Icon -->
                        <div class="flex-shrink-0">
                            @switch($notification['type'])
                                @case('success')
                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    @break
                                @case('warning')
                                    <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    @break
                                @case('error')
                                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    @break
                                @default
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                            @endswitch
                        </div>

                        <!-- Notification Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $notification['title'] }}
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                                        {{ $notification['message'] }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                        {{ \Carbon\Carbon::parse($notification['timestamp'])->diffForHumans() }}
                                    </p>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex items-center space-x-1 ml-3">
                                    @if(!$notification['read'])
                                        <button 
                                            wire:click="markAsRead('{{ $notification['id'] }}')"
                                            class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
                                            title="Tandai sebagai dibaca"
                                        >
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    @endif
                                    
                                    <button 
                                        wire:click="removeNotification('{{ $notification['id'] }}')"
                                        class="p-1 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                                        title="Hapus notifikasi"
                                    >
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.414V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                    </svg>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-sm">
                    Tidak ada notifikasi saat ini
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                    Notifikasi real-time akan muncul di sini
                </p>
            </div>
        @endif
    </x-filament::section>

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            // Listen for notification events
            Livewire.on('show-notification', (notification) => {
                // Create toast notification
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 z-50 max-w-sm w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full opacity-0';
                
                const typeColors = {
                    'success': 'border-l-4 border-l-green-500',
                    'warning': 'border-l-4 border-l-yellow-500', 
                    'error': 'border-l-4 border-l-red-500',
                    'info': 'border-l-4 border-l-blue-500'
                };
                
                toast.className += ' ' + (typeColors[notification.type] || typeColors.info);
                
                toast.innerHTML = `
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">${notification.title}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">${notification.message}</p>
                            </div>
                            <button onclick="this.parentElement.parentElement.parentElement.remove()" class="ml-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(toast);
                
                // Animate in
                setTimeout(() => {
                    toast.classList.remove('translate-x-full', 'opacity-0');
                }, 100);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    toast.classList.add('translate-x-full', 'opacity-0');
                    setTimeout(() => {
                        if (toast.parentElement) {
                            toast.parentElement.removeChild(toast);
                        }
                    }, 300);
                }, 5000);
            });
        });
    </script>
    @endpush
</x-filament-widgets::widget>
