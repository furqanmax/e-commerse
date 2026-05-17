@extends('layouts.app')

@section('title', 'Delete Your PetKhazana Account')
@section('meta_description', 'Request deletion of your PetKhazana account and data. Learn about our data retention and account deletion process.')

@section('content')
<div class="py-12 bg-white dark:bg-zinc-900 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol role="list" class="flex items-center space-x-2 text-sm text-zinc-500 dark:text-zinc-400">
                <li>
                    <a href="{{ route('home') }}" class="hover:text-zinc-900 dark:hover:text-white transition-colors">Home</a>
                </li>
                <li>
                    <svg class="h-5 w-5 flex-shrink-0 text-zinc-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </li>
                <li>
                    <span class="text-zinc-900 dark:text-white font-medium">Delete Account</span>
                </li>
            </ol>
        </nav>

        <div class="bg-zinc-50 dark:bg-zinc-800 shadow-sm sm:rounded-lg overflow-hidden border border-zinc-200 dark:border-zinc-700">
            <div class="px-6 py-8 sm:p-10">
                
                <div class="mb-10 text-center border-b border-zinc-200 dark:border-zinc-700 pb-8">
                    <h1 class="text-3xl font-extrabold text-red-600 dark:text-red-500 tracking-tight sm:text-4xl mb-4">
                        Delete Your PetKhazana Account
                    </h1>
                    <p class="text-lg text-zinc-600 dark:text-zinc-400">
                        Instructions on how to request the deletion of your account and personal data.
                    </p>
                </div>

                <div class="prose prose-zinc dark:prose-invert max-w-none space-y-8 text-zinc-600 dark:text-zinc-300">
                    
                    <section>
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-3">
                            1. Introduction
                        </h2>
                        <p>We value your privacy and give you full control over your data. If you no longer wish to use PetKhazana, you have the right to request the deletion of your account and associated personal data.</p>
                    </section>

                    <section class="bg-indigo-50 dark:bg-indigo-900/20 p-6 rounded-lg border border-indigo-100 dark:border-indigo-800">
                        <h2 class="text-xl font-bold text-indigo-900 dark:text-indigo-200 mb-3">
                            2. Steps to Request Account Deletion
                        </h2>
                        <ol class="list-decimal pl-5 space-y-2 text-indigo-800 dark:text-indigo-300">
                            <li>Send an email to <strong>support@petkhazana.in</strong> from the email address associated with your PetKhazana account.</li>
                            <li>Use the subject line: <strong>"Account Deletion Request"</strong>.</li>
                            <li>Include your registered email address and username in the email body.</li>
                            <li>Our support team will process your request within <strong>7 business days</strong>.</li>
                        </ol>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-3">
                            3. Data That Will Be Deleted
                        </h2>
                        <p class="mb-3">Once your request is processed, the following data will be permanently deleted from our active databases:</p>
                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-4">
                            <li class="flex items-center gap-2">
                                <span class="text-red-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></span>
                                Profile information
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-red-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></span>
                                Pet details
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-red-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></span>
                                Uploaded images
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-red-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></span>
                                Saved addresses
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-red-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></span>
                                Shopping preferences
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-red-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></span>
                                Browsing activity history
                            </li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-3">
                            4. Data That May Be Retained
                        </h2>
                        <div class="bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 p-4 mb-4">
                            <p class="text-amber-800 dark:text-amber-200 font-medium">
                                <svg class="w-5 h-5 inline mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                Note: Some data may be retained for legal and security purposes.
                            </p>
                        </div>
                        <p class="mb-3">Even after your account is deleted, we are required by law to retain certain information, including:</p>
                        <ul class="list-disc pl-5 mt-2 space-y-1">
                            <li>Transaction records and completed order invoices</li>
                            <li>Data required for legal compliance and tax purposes</li>
                            <li>Customer support conversations and dispute resolution records</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-3">
                            5. Retention Period Information
                        </h2>
                        <p>Retained data is kept securely stored and is only accessible to authorized personnel. We keep this data strictly for the period mandated by local laws, after which it is permanently destroyed or anonymized.</p>
                    </section>
                    
                    <div class="mt-12 pt-8 border-t border-zinc-200 dark:border-zinc-700">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-4">
                            6. Contact Support Team
                        </h2>
                        <p class="mb-4">If you have any questions regarding our account deletion policy, please reach out to us:</p>
                        <ul class="space-y-2">
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <strong>Email:</strong> <a href="mailto:support@petkhazana.in" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 hover:underline">support@petkhazana.in</a>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                <strong>Website:</strong> <a href="http://petkhazana.in" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 hover:underline">http://petkhazana.in</a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
