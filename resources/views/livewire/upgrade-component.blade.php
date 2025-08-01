<div>
    @if ($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" x-data>
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Upgrade File Retention</h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        @foreach ($retentionPlans as $plan)
                            <div 
                                class="border rounded-lg p-4 cursor-pointer transition-colors"
                                :class="{ 'border-indigo-500 bg-indigo-50': $wire.selectedPlan && $wire.selectedPlan['id'] === '{{ $plan['id'] }}' }"
                                wire:click="selectPlan('{{ $plan['id'] }}')"
                            >
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $plan['name'] }}</h4>
                                        <p class="text-sm text-gray-600">{{ $plan['description'] }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-lg font-bold text-indigo-600">${{ number_format($plan['price'], 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if ($selectedPlan)
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <p class="text-sm text-green-800">
                                    <strong>Selected:</strong> {{ $selectedPlan['name'] }} - ${{ number_format($selectedPlan['price'], 2) }}
                                </p>
                            </div>
                        @endif

                        <div class="flex space-x-3">
                            <button 
                                wire:click="closeModal"
                                class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                            >
                                Cancel
                            </button>
                            <button 
                                wire:click="checkout"
                                wire:loading.attr="disabled"
                                @if (!$selectedPlan) disabled @endif
                                class="flex-1 bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                            >
                                <span wire:loading.remove>Proceed to Payment</span>
                                <span wire:loading>Processing...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
