export default function registerThemeSwitcher(Alpine) {
    Alpine.data('themeSwitcher', (config = {}) => ({
        open: false,
        currentTheme: config.current || 'light',
        currentName: config.themes?.[config.current] || '',
        activeIndex: -1,
        themesCount: Object.keys(config.themes || {}).length,
        isAuthenticated: !!config.isAuthenticated,
        updateUrl: config.updateUrl || '',
        themesMap: config.themes || {},

        init() {
            const docTheme = document.documentElement.getAttribute('data-theme') || this.currentTheme;
            if (this.themesMap[docTheme]) {
                this.currentTheme = docTheme;
                this.currentName = this.themesMap[docTheme];
            }
            window.addEventListener('theme-changed', (e) => {
                const t = e.detail?.theme;
                if (t && this.themesMap[t]) {
                    this.currentTheme = t;
                    this.currentName = this.themesMap[t];
                }
            });
        },
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
        selectTheme(key) {
            this.currentTheme = key;
            this.currentName = this.themesMap[key] || key;
            document.documentElement.setAttribute('data-theme', key);
            try {
                localStorage.setItem('theme', key);
            } catch (e) {}

            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: key } }));

            if (this.isAuthenticated && this.updateUrl) {
                const token = document.querySelector('meta[name=csrf-token]')?.content;
                fetch(this.updateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token || ''
                    },
                    body: JSON.stringify({ theme: key })
                }).catch(() => {});
            }

            this.close();
        },
        focusNext() {
            if (!this.open) {
                this.open = true;
                this.activeIndex = 0;
            } else {
                this.activeIndex = (this.activeIndex + 1) % this.themesCount;
            }
            this.focusActive();
        },
        focusPrev() {
            if (!this.open) {
                this.open = true;
                this.activeIndex = this.themesCount - 1;
            } else {
                this.activeIndex = (this.activeIndex - 1 + this.themesCount) % this.themesCount;
            }
            this.focusActive();
        },
        focusActive() {
            this.$nextTick(() => {
                const items = this.$refs.menu?.querySelectorAll('button[role=menuitem]');
                if (items && items[this.activeIndex]) {
                    items[this.activeIndex].focus();
                }
            });
        }
    }));
}
