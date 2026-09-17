<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { email } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Forgot password',
        description: 'Enter your email to receive a password reset link',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Forgot Password - Adili Real Estate" />

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
                <h2 class="text-xl font-bold text-gray-800">Forgot your password?</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Enter your email and we'll send you a link to reset it.
                </p>
            </div>

            <!-- Status Message -->
            <div
                v-if="status"
                class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-center text-sm font-medium text-green-700"
            >
                {{ status }}
            </div>

            <Form v-bind="email.form()" v-slot="{ errors, processing }">
                <div class="grid gap-2">
                    <Label for="email" class="text-sm font-medium text-gray-700">
                        Email address
                    </Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="off"
                        autofocus
                        placeholder="you@example.com"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 placeholder-gray-400 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="my-6">
                    <Button
                        class="w-full rounded-lg bg-sky-500 px-4 py-3 text-base font-bold text-white shadow-md transition hover:bg-sky-600 disabled:opacity-50"
                        :disabled="processing"
                        data-test="email-password-reset-link-button"
                    >
                        <Spinner v-if="processing" class="mr-2" />
                        {{ processing ? 'Sending...' : 'Email reset link' }}
                    </Button>
                </div>
            </Form>

            <div class="text-center text-sm text-gray-600">
                <span>Remembered it? </span>
                <TextLink
                    :href="login()"
                    class="font-medium text-sky-600 hover:text-sky-700 hover:underline"
                >
                    Back to login
                </TextLink>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-xs text-gray-400">
                &copy; {{ new Date().getFullYear() }} Adili Real Estate. All rights reserved.
            </p>
        </div>
    </div>
</template>