<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login } from '@/routes';
/* @chisel-registration */
import { register } from '@/routes';
import AppFooter from '@/components/AppFooter.vue';
/* @end-chisel-registration */
</script>

<template>
    <Head title="Welcome to Your Buyer Portal | Adili Real Estate">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="min-h-screen bg-gray-50">
        <!-- ============================================ -->
        <!-- HEADER                                        -->
        <!-- ============================================ -->
        <header class="border-b border-gray-200 bg-white">
            <div
                class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8"
            >
                <img
                    src="/logo.png"
                    alt="Adili Real Estate"
                    class="h-12 w-auto"
                />

                <div class="flex items-center gap-3">
                    <template v-if="$page.props.auth.user">
                        <Link
                            href="/buyer/portal"
                            class="rounded-lg bg-sky-500 px-5 py-2.5 text-sm font-bold text-white shadow-md transition hover:bg-sky-600"
                        >
                            Go to My Portal
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                        >
                            Log in
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- ============================================ -->
        <!-- HERO                                          -->
        <!-- ============================================ -->
        <section
            class="relative overflow-hidden bg-gradient-to-br from-sky-50 via-white to-red-50 py-16 sm:py-24"
        >
            <!-- Background decorations -->
            <div class="absolute inset-0 overflow-hidden">
                <div
                    class="absolute -top-40 -right-40 h-80 w-80 rounded-full bg-sky-200/40 blur-3xl"
                ></div>
                <div
                    class="absolute -bottom-40 -left-40 h-80 w-80 rounded-full bg-red-200/40 blur-3xl"
                ></div>
            </div>

            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl text-center">
                    <div
                        class="mb-6 inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 shadow-sm ring-1 ring-gray-200"
                    >
                        <span
                            class="flex h-2 w-2 rounded-full bg-green-500"
                        ></span>
                        <span class="text-xs font-medium text-gray-700"
                            >Your Buyer Portal</span
                        >
                    </div>

                    <h1
                        class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl"
                    >
                        Welcome to your
                        <span class="text-sky-500">buyer portal</span>
                    </h1>

                    <p class="mx-auto mt-6 max-w-2xl text-lg text-gray-600">
                        This is your personal space at Adili Real Estate. Log in
                        to view your plot, track your installments, and manage
                        everything about your land purchase — all in one place.
                    </p>

                    <div
                        class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row"
                    >
                        <template v-if="$page.props.auth.user">
                            <Link
                                href="/buyer/portal"
                                class="w-full rounded-xl bg-sky-500 px-8 py-4 text-center text-base font-bold text-white shadow-lg transition hover:bg-sky-600 sm:w-auto"
                            >
                                Go to My Portal →
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                :href="login()"
                                class="w-full rounded-xl bg-sky-500 px-8 py-4 text-center text-base font-bold text-white shadow-lg transition hover:bg-sky-600 sm:w-auto"
                            >
                                Log in to My Portal →
                            </Link>
                        </template>
                    </div>

                    <p class="mt-6 text-sm text-gray-500">
                        Have an invite link? Check your email for the activation
                        link we sent you.
                    </p>
                </div>
            </div>
        </section>

        <!-- ============================================ -->
        <!-- WHAT YOU CAN DO                               -->
        <!-- ============================================ -->
        <section class="bg-white py-16 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl text-center">
                    <p
                        class="text-sm font-bold tracking-wider text-sky-500 uppercase"
                    >
                        Everything in one place
                    </p>
                    <h2
                        class="mt-2 text-3xl font-extrabold text-gray-900 sm:text-4xl"
                    >
                        What you can do in your portal
                    </h2>
                    <p class="mt-4 text-lg text-gray-600">
                        Your portal keeps you in control of your land purchase —
                        no more chasing updates on WhatsApp or email.
                    </p>
                </div>

                <div
                    class="mt-14 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <!-- Card 1 -->
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-6 transition hover:shadow-lg"
                    >
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-50 text-sky-500"
                        >
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-gray-900">
                            View Your Plot
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            See your plot number, size, location, and full
                            project details — always up to date.
                        </p>
                    </div>

                    <!-- Card 2 -->
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-6 transition hover:shadow-lg"
                    >
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 text-red-500"
                        >
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-gray-900">
                            Track Installments
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            See your payment schedule at a glance — what's paid,
                            what's next, and what's remaining.
                        </p>
                    </div>

                    <!-- Card 3 -->
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-6 transition hover:shadow-lg"
                    >
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-50 text-sky-500"
                        >
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-gray-900">
                            Payment History
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Every payment you've made is recorded and available
                            to view, with receipts attached.
                        </p>
                    </div>

                    <!-- Card 4 -->
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-6 transition hover:shadow-lg"
                    >
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-50 text-sky-500"
                        >
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-gray-900">
                            Secure & Private
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Your portal is private to you. Only you, and the
                            Adili team, can access your account.
                        </p>
                    </div>

                    <!-- Card 5 -->
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-6 transition hover:shadow-lg"
                    >
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 text-red-500"
                        >
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-gray-900">
                            Ready for Handover
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Once fully paid, your portal tracks the title deed
                            transfer all the way to completion.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================ -->
        <!-- HOW TO GET STARTED                            -->
        <!-- ============================================ -->
        <section class="bg-gray-50 py-16 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl text-center">
                    <p
                        class="text-sm font-bold tracking-wider text-sky-500 uppercase"
                    >
                        Getting started
                    </p>
                    <h2
                        class="mt-2 text-3xl font-extrabold text-gray-900 sm:text-4xl"
                    >
                        Access your portal in two steps
                    </h2>
                </div>

                <div
                    class="mx-auto mt-14 grid max-w-4xl grid-cols-1 gap-8 md:grid-cols-2"
                >
                    <!-- Step 1 -->
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm"
                    >
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-500 text-xl font-bold text-white"
                        >
                            1
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-gray-900">
                            Check your email
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            We sent you an activation link when you paid your
                            deposit. Look for an email from
                            <strong>Adili Real Estate</strong>.
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm"
                    >
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-500 text-xl font-bold text-white"
                        >
                            2
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-gray-900">
                            Create your account
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Click the link, set your password, and you're in. If
                            you've already activated, just log in.
                        </p>
                    </div>
                </div>

                <div class="mt-10 text-center">
                    <p class="text-sm text-gray-500">
                        Didn't receive the email? Contact us at
                        <a
                            href="mailto:info@adilirealestate.com"
                            class="font-medium text-sky-600 hover:underline"
                        >
                            info@adilirealestate.com
                        </a>
                        or call
                        <a
                            href="tel:+254720244744"
                            class="font-medium text-sky-600 hover:underline"
                        >
                            +254 720 244 744
                        </a>
                        .
                    </p>
                </div>
            </div>
        </section>

        <!-- ============================================ -->
        <!-- FINAL CTA                                     -->
        <!-- ============================================ -->
        <section class="bg-white py-16 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div
                    class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-500 via-sky-600 to-sky-700 px-6 py-14 text-center shadow-2xl sm:px-16 sm:py-16"
                >
                    <div
                        class="absolute -top-20 -right-20 h-60 w-60 rounded-full bg-white/10 blur-3xl"
                    ></div>
                    <div
                        class="absolute -bottom-20 -left-20 h-60 w-60 rounded-full bg-red-500/20 blur-3xl"
                    ></div>

                    <div class="relative mx-auto max-w-2xl">
                        <h2
                            class="text-3xl font-extrabold text-white sm:text-4xl"
                        >
                            Ready to check your portal?
                        </h2>
                        <p class="mt-4 text-lg text-white/90">
                            Everything about your plot, installments, and
                            referrals is waiting for you.
                        </p>

                        <div
                            class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row"
                        >
                            <template v-if="$page.props.auth.user">
                                <Link
                                    :href="dashboard()"
                                    class="w-full rounded-xl bg-white px-8 py-4 text-center text-base font-bold text-sky-600 shadow-lg transition hover:bg-gray-50 sm:w-auto"
                                >
                                    Go to My Portal
                                </Link>
                            </template>
                            <template v-else>
                                <Link
                                    :href="login()"
                                    class="w-full rounded-xl bg-white px-8 py-4 text-center text-base font-bold text-sky-600 shadow-lg transition hover:bg-gray-50 sm:w-auto"
                                >
                                    Log In
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <AppFooter />
    </div>
</template>
