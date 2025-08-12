<x-filament::widget>
    <x-filament::card>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            @foreach ($categoryStats as $stat)
                <div class="dark:bg-gray-900 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ $stat['name'] }}
                    </div>
                    <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                        Rp {{ number_format($stat['total'], 0, ',', '.') }}
                    </div>
                    <div class="mt-1 text-sm text-gray-400">
                        Total pengeluaran kategori
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::card>
</x-filament::widget>
