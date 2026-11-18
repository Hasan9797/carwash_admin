@php
    $themes = config('themes.available', []);
    $defaultTheme = config('themes.default', 'light');
    $authTheme = auth()->check() ? (auth()->user()->theme ?? null) : null;
    $initialTheme = $authTheme ?? $defaultTheme;
    $initialName = __($themes[$initialTheme]['name'] ?? 'theme.light');

    $config = [
        'current' => $initialTheme,
        'themes' => collect($themes)->mapWithKeys(fn($t, $k) => [$k => __($t['name'])])->all(),
        'isAuthenticated' => auth()->check(),
        'updateUrl' => route('theme.update'),
    ];

    $renderIcon = function(string $icon, string $class = 'w-4 h-4'): string {
        return match ($icon) {
            'sun' => '<svg class="' . $class . '" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">'
                . '<circle cx="12" cy="12" r="4"/>'
                . '<path stroke-linecap="round" stroke-linejoin="round" d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>'
                . '</svg>',
            'moon' => '<svg class="' . $class . '" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">'
                . '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3a6 6 0 009 9 9 9 0 11-9-9z"/>'
                . '</svg>',
            'coffee' => '<svg class="' . $class . '" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">'
                . '<path stroke-linecap="round" stroke-linejoin="round" d="M17 8h1a4 4 0 110 8h-1"/>'
                . '<path stroke-linecap="round" stroke-linejoin="round" d="M3 8h14v9a4 4 0 01-4 4H7a4 4 0 01-4-4Z"/>'
                . '<line stroke-linecap="round" stroke-linejoin="round" x1="6" x2="6" y1="2" y2="4"/>'
                . '<line stroke-linecap="round" stroke-linejoin="round" x1="10" x2="10" y1="2" y2="4"/>'
                . '<line stroke-linecap="round" stroke-linejoin="round" x1="14" x2="14" y1="2" y2="4"/>'
                . '</svg>',
            'cloud' => '<svg class="' . $class . '" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">'
                . '<path stroke-linecap="round" stroke-linejoin="round" d="M17.5 19H9a7 7 0 116.71-9h1.79a4.5 4.5 0 110 9z"/>'
                . '</svg>',
            default => '<svg class="' . $class . '" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">'
                . '<circle cx="12" cy="12" r="9"/>'
                . '</svg>',
        };
    };
@endphp

<div 
    x-data="themeSwitcher(@js($config))" 
    @keydown.escape.window="close()"
    @click.outside="close()"
    class="relative inline-block text-left"
>
    <!-- Tema almashtirgich tugmasi (Trigger) -->
    <button 
        type="button" 
        @click="toggle()"
        @keydown.arrow-down.prevent="focusNext()"
        @keydown.arrow-up.prevent="focusPrev()"
        :aria-expanded="open.toString()"
        aria-haspopup="menu"
        aria-label="{{ __('theme.choose') }}"
        class="h-9 px-2.5 sm:px-3 inline-flex items-center gap-2 rounded-lg border border-border bg-surface hover:bg-surface-2 text-content text-xs font-semibold shadow-2xs transition-colors focus:outline-none focus:ring-2 focus:ring-primary/20 cursor-pointer"
    >
        <!-- Joriy tema ikonkasi (18px, yumshoq almashuv bilan) -->
        <div class="relative w-[18px] h-[18px] shrink-0 flex items-center justify-center">
            @foreach($themes as $key => $t)
                <span 
                    x-show="currentTheme === '{{ $key }}'" 
                    x-transition:enter="transition-all ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-75 rotate-45"
                    x-transition:enter-end="opacity-100 scale-100 rotate-0"
                    x-transition:leave="transition-all ease-in duration-150 absolute"
                    x-transition:leave-start="opacity-100 scale-100 rotate-0"
                    x-transition:leave-end="opacity-0 scale-75 -rotate-45"
                    class="flex items-center justify-center text-primary"
                    style="{{ $initialTheme !== $key ? 'display: none;' : '' }}"
                >
                    {!! $renderIcon($t['icon'] ?? '', 'w-[18px] h-[18px]') !!}
                </span>
            @endforeach
        </div>

        <!-- Joriy tema nomi (mobilda faqat ikonka va chevron ko'rinadi) -->
        <span class="hidden sm:inline font-medium" x-text="currentName">{{ $initialName }}</span>

        <!-- Kichik chevron -->
        <svg 
            class="w-3.5 h-3.5 text-muted transition-transform duration-200 shrink-0" 
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
        role="menu"
        aria-label="{{ __('theme.choose') }}"
        @keydown.arrow-down.prevent="focusNext()"
        @keydown.arrow-up.prevent="focusPrev()"
        @keydown.tab="close()"
        style="display: none;"
        class="absolute right-0 mt-1.5 w-56 rounded-xl bg-surface border border-border shadow-lg p-1.5 z-50 focus:outline-none"
    >
        @foreach($themes as $key => $t)
            @php
                $name = __($t['name']);
                $swatchBg = $t['swatch_bg'] ?? '#ffffff';
                $swatchIcon = $t['swatch_icon'] ?? '#f59e0b';
            @endphp
            <button 
                type="button"
                role="menuitem"
                :aria-selected="currentTheme === '{{ $key }}' ? 'true' : 'false'"
                @click="selectTheme('{{ $key }}')"
                class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-lg transition-colors focus:outline-none cursor-pointer"
                :class="currentTheme === '{{ $key }}' ? 'bg-primary-soft/60 text-primary font-semibold' : 'text-content hover:bg-surface-2 focus:bg-surface-2'"
            >
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Dumaloq swatch (28px) markazda 15px ikonkasi bilan -->
                    <div 
                        class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 transition-all duration-150 {{ $key === 'light' ? 'border-[1.5px] border-border' : 'border border-black/10 dark:border-white/10' }}"
                        :class="currentTheme === '{{ $key }}' ? 'ring-2 ring-primary ring-offset-2 ring-offset-surface' : ''"
                        style="background-color: {{ $swatchBg }}; color: {{ $swatchIcon }};"
                    >
                        {!! $renderIcon($t['icon'] ?? '', 'w-[15px] h-[15px]') !!}
                    </div>
                    <span class="truncate">{{ $name }}</span>
                </div>

                <!-- Tanlangan tema uchun galochka (check) ikonkasi -->
                <svg 
                    x-show="currentTheme === '{{ $key }}'" 
                    class="w-4 h-4 text-primary shrink-0 transition-opacity duration-150" 
                    style="{{ $initialTheme !== $key ? 'display: none;' : '' }}"
                    fill="none" 
                    viewBox="0 0 24 24" 
                    stroke="currentColor" 
                    stroke-width="2.5"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </button>
        @endforeach
    </div>
</div>
