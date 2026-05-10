@extends('layouts.store')

@section('body')
    <x-legal-layout>
        <x-slot name="title">
            <h2 class="text-2xl font-bold uppercase text-gray-900 dark:text-white tracking-wider font-heading sm:text-3xl text-center mb-2">
                {{ __('Refund Policy') }}
            </h2>
            <span class="text-sm leading-5 text-gray-500 dark:text-gray-400 text-center block">
                {{ __('Last update: :date', ['date' => $legal?->created_at->format('d, F Y') ?? now()->format('d, F Y')]) }}
            </span>
        </x-slot>

        @if($legal)
            {!! $legal->content !!}
        @else
            <div class="text-center py-12">
                <p class="text-gray-500 dark:text-gray-400">{{ __('Refund policy content not available.') }}</p>
            </div>
        @endif
    </x-legal-layout>
@endsection