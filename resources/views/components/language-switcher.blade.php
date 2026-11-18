@php
    $current = current_locale_meta();
    $locales = supported_locales();
@endphp

<div 
    x-data="{ 
        open: false,
        activeIndex: -1,
        localesCount: {{ count($locales) }},
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.activeIndex = -1;
            }
        },
        close() {
            this.open = false;
            this.activeIndex = -1;
        },
        focusNext() {
            if (!this.open) {
                this.open = true;
                this.activeIndex = 0;
            } else {
                this.activeIndex = (this.activeIndex + 1) % this.localesCount;
            }
            this.focusActive();
        },
        focusPrev() {
            if (!this.open) {
                this.open = true;
                this.activeIndex = this.localesCount - 1;
            } else {
                this.activeIndex = (this.activeIndex - 1 + this.localesCount) % this.localesCount;
            }
            this.focusActive();
        },
        focusActive() {
            this.$nextTick(() => {
                const items = this.$refs.menu.querySelectorAll('a[role=option]');
                if (items[this.activeIndex]) {
                    items[this.activeIndex].focus();
                }
            });
        }
    }" 
    @keydown.escape.window="close()"
    @click.outside="close()"
    class="relative inline-block text-left"
>
    <!-- Til almashtirgich tugmasi (Trigger) -->
    <button 
        type="button" 
        @click="toggle()"
        @keydown.arrow-down.prevent="focusNext()"
        @keydown.arrow-up.prevent="focusPrev()"
        :aria-expanded="open.toString()"
        aria-haspopup="listbox"
        aria-label="{{ __('common.select_language') }}"
        class="h-9 px-3 inline-flex items-center gap-1.5 rounded-lg border border-border bg-surface hover:bg-surface-2 text-content text-xs font-semibold shadow-2xs transition-colors focus:outline-none focus:ring-2 focus:ring-primary/20"
    >
        <span class="tracking-wide font-bold">{{ $current['short'] }}</span>
        <svg 
            class="w-3.5 h-3.5 text-muted transition-transform duration-200" 
            :class="open ? 'rotate-180 text-primary' : ''"
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor" 
            stroke-width="2.2"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Ochiluvchi ro'yxat (Dropdown Menu) -->
    <div 
        x-show="open" 
        x-ref="menu"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
        role="listbox"
        aria-label="{{ __('common.select_language') }}"
        @keydown.arrow-down.prevent="focusNext()"
        @keydown.arrow-up.prevent="focusPrev()"
        @keydown.tab="close()"
        style="display: none;"
        class="absolute right-0 mt-1.5 w-48 rounded-xl bg-surface border border-border shadow-lg py-1 z-50 focus:outline-none"
    >
        @foreach($locales as $code => $locale)
            @php
                $isActive = app()->getLocale() === $code;
            @endphp
            <a 
                href="{{ route('locale.switch', $code) }}"
                role="option"
                aria-selected="{{ $isActive ? 'true' : 'false' }}"
                class="flex items-center justify-between px-3.5 py-2 text-xs font-medium transition-colors focus:outline-none {{ $isActive ? 'bg-primary-soft text-primary font-semibold' : 'text-content hover:bg-surface-2 focus:bg-surface-2' }}"
            >
                <div class="flex items-center gap-2">
                    <span class="inline-block w-6 font-mono font-bold text-[11px] {{ $isActive ? 'text-primary' : 'text-muted' }}">{{ $locale['short'] }}</span>
                    <span>{{ $locale['name'] }}</span>
                </div>
                @if($isActive)
                    <svg class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                @endif
            </a>
        @endforeach
    </div>
</div>
