<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/password/confirm';
/* @chisel-passkeys */
import {
    index as confirmOptions,
    store as confirmStore,
} from '@/actions/Laravel/Passkeys/Http/Controllers/PasskeyConfirmationController';
import PasskeyVerify from '@/components/PasskeyVerify.vue';
/* @end-chisel-passkeys */

defineOptions({
    layout: {
        title: 'Confirm password',
        description:
            'This is a secure area of the application. Please confirm your password before continuing.',
    },
});
</script>

<template>
    <Head title="Confirm Password - Adili Real Estate" />

    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 px-4 py-12">
        <!-- Branding -->
        <div class="mb-8 text-center">
            <img
                src="/logo.png"
                alt="Adili Real Estate"
                class="h-20 w-auto mx-auto mb-4"
            />
            <p class="text-sm text-gray-500 italic">Defined by Trust</p>
        </div>

        <!-- Card -->
        <div class="w-full max-w-md bg-white rounded-2xl shadow-lg ring-1 ring-gray-200 p-6 sm:p-8">
            <!-- Header -->
            <div class="mb-6 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-red-50 text-red-500 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Confirm your password</h2>
                <p class="mt-1 text-sm text-gray-500">
                    For your security, please confirm your password to continue.
                </p>
            </div>

            <!-- @chisel-passkeys -->
            <div class="mb-6">
                <PasskeyVerify
                    :routes="{
                        options: confirmOptions(),
                        submit: confirmStore(),
                    }"
                    label="Confirm with passkey"
                    loading-label="Confirming..."
                    separator="Or confirm with password"
                />
            </div>
            <!-- @end-chisel-passkeys -->

            <Form
                v-bind="store.form()"
                reset-on-success
                v-slot="{ errors, processing }"
            >
                <div class="grid gap-5">
                    <div class="grid gap-2">
                        <Label for="password" class="text-sm font-medium text-gray-700">
                            Password
                        </Label>
                        <PasswordInput
                            id="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            autofocus
                            placeholder="Enter your password"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 placeholder-gray-400 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                        />
                        <InputError :message="errors.password" />
                    </div>

                    <Button
                        type="submit"
                        class="mt-2 w-full rounded-lg bg-sky-500 px-4 py-3 text-base font-bold text-white shadow-md transition hover:bg-sky-600 disabled:opacity-50"
                        :disabled="processing"
                        data-test="confirm-password-button"
                    >
                        <Spinner v-if="processing" class="mr-2" />
                        {{ processing ? 'Confirming...' : 'Confirm Password' }}
                    </Button>
                </div>
            </Form>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-xs text-gray-400">
                &copy; {{ new Date().getFullYear() }} Adili Real Estate. All rights reserved.
            </p>
        </div>
    </div>
</template>