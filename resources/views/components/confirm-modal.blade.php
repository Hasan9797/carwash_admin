<div 
    x-data="{
        open: false,
        actionUrl: '',
        title: '{{ addslashes(__('common.confirm_delete_title')) }}',
        message: '{{ addslashes(__('common.confirm_delete_message')) }}',
        show(detail) {
            this.actionUrl = detail.action || '';
            this.title = detail.title || this.title;
            this.message = detail.message || this.message;
            this.open = true;
        },
        close() {
            this.open = false;
        }
    }"
    @open-delete-modal.window="show($event.detail)"
    @keydown.escape.window="close()"
    x-cloak
>
    <!-- Modal Backdrop -->
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;"
    >
        <!-- Modal Card -->
        <div 
            x-show="open" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.away="close()"
            class="bg-surface rounded-2xl shadow-xl max-w-md w-full p-6 text-left border border-border text-content"
        >
            <div class="flex items-center gap-3.5 mb-4">
                <div class="w-10 h-10 rounded-full bg-danger-soft text-danger flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-content" x-text="title"></h3>
                    <p class="text-xs text-muted mt-0.5">{{ __('common.confirm_delete_warning') }}</p>
                </div>
            </div>

            <p class="text-sm text-muted mb-6" x-text="message"></p>

            <form :action="actionUrl" method="POST" class="flex items-center justify-end gap-3">
                @csrf
                @method('DELETE')
                <x-button type="button" variant="secondary" @click="close()">
                    {{ __('common.cancel') }}
                </x-button>
                <x-button type="submit" variant="danger">
                    {{ __('common.confirm_delete_btn') }}
                </x-button>
            </form>
        </div>
    </div>
</div>
