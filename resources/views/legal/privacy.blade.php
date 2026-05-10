

@section('body')
    <x-legal-layout>
        <x-slot name="title">
            <h2 class="text-2xl font-bold uppercase text-dark tracking-wider font-heading sm:text-3xl">
                {{ __('Privacy Policy') }}
            </h2>
            <span class="text-sm leading-5 text-gray-500">{{ __('Last update: :date', ['date' => $legal->created_at->format('d, F Y')]) }}</span>
        </x-slot>

        @if($legal)
            <div class="prose prose-gray max-w-none">
                <!-- Privacy Policy Header -->
                <div class="bg-gray-50 rounded-lg p-6 mb-8">
                    <h3 class="text-lg font-semibold mb-3">{{ __('Our Commitment to Privacy') }}</h3>
                    <p class="text-gray-600">{{ __('This privacy policy explains how we collect, use, and protect your personal information in accordance with applicable privacy laws.') }}</p>
                </div>

                <!-- Main Content -->
                <div class="space-y-8">
                    {!! $legal->content !!}
                </div>

                <!-- Contact Information -->
                <div class="mt-12 border-t pt-8">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Contact Us') }}</h3>
                    <p class="text-gray-600">
                        {{ __('If you have any questions about this privacy policy or our data practices, please contact us.') }}
                    </p>
                </div>

                <!-- Last Updated Notice -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-500">
                        {{ __('This privacy policy was last updated on :date', ['date' => $legal->updated_at->format('F j, Y')]) }}
                    </p>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500">{{ __('Privacy policy content is currently not available.') }}</p>
            </div>
        @endif

    </x-legal-layout>

@endsection