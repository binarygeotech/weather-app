<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Weather Companion') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="bg-gray-200 dark:bg-gray-800 bg-opacity-25 grid grid-cols-1 gap-6 lg:gap-8 p-6 lg:p-8">
                    <x-subscription-form :cities="$cities" :countries="$countries" />
                </div>
            </div>
        </div>
    </div>
</div>
