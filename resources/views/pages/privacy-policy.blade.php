@extends('layouts.app')

@section('title', 'Privacy Policy - PetKhazana')
@section('meta_description', 'Privacy Policy for PetKhazana. Read about what information we collect, how we use it, and your rights.')

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
                    <span class="text-zinc-900 dark:text-white font-medium">Privacy Policy</span>
                </li>
            </ol>
        </nav>

        <div class="bg-zinc-50 dark:bg-zinc-800 shadow-sm sm:rounded-lg overflow-hidden border border-zinc-200 dark:border-zinc-700">
            <div class="px-6 py-8 sm:p-10">
                
                <div class="mb-10 text-center border-b border-zinc-200 dark:border-zinc-700 pb-8">
                    <h1 class="text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight sm:text-4xl mb-4">
                        Privacy Policy
                    </h1>
                    <p class="text-lg text-zinc-600 dark:text-zinc-400">
                        Effective date: {{ date('F d, Y') }}
                    </p>
                </div>

                <div class="prose prose-zinc dark:prose-invert max-w-none space-y-8 text-zinc-600 dark:text-zinc-300">
                    
                    <section>
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-sm">1</span> 
                            Introduction
                        </h2>
                        <p class="mb-4">
                            Welcome to PetKhazana. We respect your privacy and are committed to protecting your personal data. This Privacy Policy will inform you as to how we look after your personal data when you visit our website (http://petkhazana.in) and use our application.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-sm">2</span> 
                            Information We Collect
                        </h2>
                        <p class="mb-4">
                            We may collect, use, store and transfer different kinds of personal data about you, including:
                        </p>
                        <ul class="list-disc pl-5 mt-2 space-y-1">
                            <li>Identity Data (first name, last name, username)</li>
                            <li>Contact Data (email address, telephone numbers, delivery address)</li>
                            <li>Profile Data (purchases made by you, your interests, preferences, and feedback)</li>
                            <li>Pet Data (details about your pets you provide for better product recommendations)</li>
                            <li>Technical Data (IP address, login data, browser type and version)</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-sm">3</span> 
                            How We Use Information
                        </h2>
                        <p class="mb-4">We will only use your personal data when the law allows us to. Most commonly, we use your data to:</p>
                        <ul class="list-disc pl-5 mt-2 space-y-1">
                            <li>Process and deliver your order, including managing payments and fees.</li>
                            <li>Manage our relationship with you, such as notifying you about changes to our terms or privacy policy.</li>
                            <li>Provide recommendations and personalized content based on your profile and pet details.</li>
                            <li>Improve our website, application, products/services, marketing, and customer relationships.</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-sm">4</span> 
                            Sharing of Information
                        </h2>
                        <p>We may share your personal data with third parties for the purposes set out in section 3. This includes service providers acting as processors who provide IT and system administration services, delivery companies, and payment gateways.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-sm">5</span> 
                            Data Security
                        </h2>
                        <p>We have put in place appropriate security measures to prevent your personal data from being accidentally lost, used, or accessed in an unauthorized way, altered, or disclosed. We limit access to your personal data to those employees, agents, contractors, and other third parties who have a business need to know.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-sm">6</span> 
                            Data Retention
                        </h2>
                        <p>We will only retain your personal data for as long as reasonably necessary to fulfill the purposes we collected it for, including for the purposes of satisfying any legal, regulatory, tax, accounting, or reporting requirements.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-sm">7</span> 
                            Account Deletion
                        </h2>
                        <p>You have the right to request the deletion of your account and associated personal data. Upon such a request, we will process your deletion subject to legal retention obligations.</p>
                        <div class="mt-4 p-4 bg-zinc-100 dark:bg-zinc-700/50 rounded-md border border-zinc-200 dark:border-zinc-700">
                            <p class="font-medium">Want to delete your account?</p>
                            <a href="{{ route('delete-account') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 font-semibold mt-2 inline-block transition-colors">
                                Go to Delete Account Page &rarr;
                            </a>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-sm">8</span> 
                            User Rights
                        </h2>
                        <p>Under certain circumstances, you have rights under data protection laws concerning your personal data, including the right to request access, correction, erasure, restriction, transfer, or to object to processing.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-sm">9</span> 
                            Third-Party Services
                        </h2>
                        <p>Our application may include links to third-party websites, plug-ins, and applications. Clicking on those links or enabling those connections may allow third parties to collect or share data about you. We do not control these third-party websites and are not responsible for their privacy statements.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-sm">10</span> 
                            Children's Privacy
                        </h2>
                        <p>Our website and application are not intended for children under 13 years of age. We do not knowingly collect personal data relating to children.</p>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-sm">11</span> 
                            Changes to Privacy Policy
                        </h2>
                        <p>We keep our privacy policy under regular review. Any changes we make to our privacy policy in the future will be posted on this page and, where appropriate, notified to you by email.</p>
                    </section>
                    
                    <div class="mt-12 pt-8 border-t border-zinc-200 dark:border-zinc-700">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-4">
                            12. Contact Information
                        </h2>
                        <p class="mb-4">If you have any questions about this privacy policy or our privacy practices, please contact us:</p>
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
