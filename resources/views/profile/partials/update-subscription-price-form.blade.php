<section>
    <header>
        <h2>{{ __("Obuna narxi") }}</h2>
        <p>{{ __("O'quvchilar shu narxni to'lab, sizning barcha bo'lim va kitoblaringizdan 1 oy davomida bepul foydalana oladi. Alohida bo'lim yoki kitob sotib olish ham baribir mumkin bo'lib qoladi.") }}</p>
    </header>

    <form method="post" action="{{ route('profile.subscription-price') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="subscription_price" :value="__("Oylik obuna narxi (so'm)")" />
            <x-text-input id="subscription_price" name="subscription_price" type="number" min="0" step="1000"
                class="mt-1 block w-full" :value="old('subscription_price', $user->subscription_price)" required />
            <x-input-error class="mt-2" :messages="$errors->get('subscription_price')" />
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">0 kiritsangiz, obuna taklifi o'quvchilarga ko'rsatilmaydi.</p>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'subscription-price-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
