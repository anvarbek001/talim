<section>
    <header>
        <h2>{{ __("Ko'rinish rejimi") }}</h2>
        <p>{{ __("Tungi yoki kunduzgi rejimni tanlang.") }}</p>
    </header>

    <label class="theme-switch-row">
        <span class="theme-switch-label"><i class="bi bi-moon-stars-fill"></i> Tungi rejim</span>
        <span class="theme-switch">
            <input type="checkbox" id="settingsThemeToggle">
            <span class="theme-switch-track"></span>
        </span>
    </label>

    <style>
        .theme-switch-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
        }

        .theme-switch-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: .88rem;
            font-weight: 600;
        }

        .theme-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .theme-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .theme-switch-track {
            position: absolute;
            inset: 0;
            background: var(--line);
            border-radius: 20px;
            transition: background .2s;
        }

        .theme-switch-track::before {
            content: "";
            position: absolute;
            width: 18px;
            height: 18px;
            left: 3px;
            top: 3px;
            background: #fff;
            border-radius: 50%;
            transition: transform .2s;
        }

        .theme-switch input:checked+.theme-switch-track {
            background: var(--primary);
        }

        .theme-switch input:checked+.theme-switch-track::before {
            transform: translateX(20px);
        }
    </style>

    <script>
        (function () {
            const root = document.documentElement;
            const checkbox = document.getElementById('settingsThemeToggle');

            checkbox.checked = root.getAttribute('data-theme') === 'dark';

            checkbox.addEventListener('change', () => {
                const next = checkbox.checked ? 'dark' : 'light';
                root.setAttribute('data-theme', next);
                localStorage.setItem('darsqil-theme', next);
            });
        })();
    </script>
</section>
