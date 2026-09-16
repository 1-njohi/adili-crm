<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
/* @chisel-registration */
import { register } from '@/routes';
/* @end-chisel-registration */
import { store } from '@/routes/login';
import { request } from '@/routes/password';
/* @chisel-passkeys */
/* @end-chisel-passkeys */

defineOptions({
    layout: {
        title: 'Welcome back',
        description: 'Log in to manage your projects, agents, and sales',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Log in - Adili Real Estate" />

    <!-- Full-page centered container -->
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 px-4 py-12">
        <!-- Branding -->
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-teal-600 text-white text-2xl font-bold mb-4">
                A
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Adili Real Estate</h1>
            <p class="text-sm text-gray-500 mt-1">Your Property Portal</p>
        </div>

        <!-- Login Card -->
        <div class="w-full max-w-md bg-white rounded-2xl shadow-lg ring-1 ring-gray-200 p-6 sm:p-8">
            <!-- Header -->
            <div class="mb-6 text-center">
                <h2 class="text-xl font-bold text-gray-800">Welcome back</h2>
                <p class="mt-1 text-sm text-gray-500">Log in to manage your projects, agents, and sales</p>
            </div>

            <!-- Status Message -->
            <div
                v-if="status"
                class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-center text-sm font-medium text-green-700"
            >
                {{ status }}
            </div>

            <Form
                v-bind="store.form()"
                :reset-on-success="['password']"
                v-slot="{ errors, processing }"
                class="flex flex-col gap-5"
            >
                <!-- Email -->
                <div class="grid gap-2">
                    <Label for="email" class="text-sm font-medium text-gray-700">
                        Email address
                    </Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        placeholder="you@adilirealestate.com"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 placeholder-gray-400 focus:border-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-200"
                    />
                    <InputError :message="errors.email" />
                </div>

                <!-- Password -->
                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password" class="text-sm font-medium text-gray-700">
                            Password
                        </Label>
                        <TextLink
                            v-if="canResetPassword"
                            :href="request()"
                            class="text-sm text-teal-600 hover:text-teal-700 hover:underline"
                            :tabindex="5"
                        >
                            Forgot password?
                        </TextLink>
                    </div>
                    <PasswordInput
                        id="password"
                        name="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        placeholder="Enter your password"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 placeholder-gray-400 focus:border-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-200"
                    />
                    <InputError :message="errors.password" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center gap-3 text-sm text-gray-600 cursor-pointer">
                        <Checkbox
                            id="remember"
                            name="remember"
                            :tabindex="3"
                            class="rounded border-gray-300 text-teal-600 focus:ring-teal-500"
                        />
                        <span>Remember me for 30 days</span>
                    </Label>
                </div>

                <!-- Login Button -->
                <Button
                    type="submit"
                    class="mt-2 w-full rounded-lg bg-teal-600 px-4 py-3 text-base font-bold text-white shadow-md transition hover:bg-teal-700 disabled:opacity-50"
                    :tabindex="4"
                    :disabled="processing"
                    data-test="login-button"
                >
                    <Spinner v-if="processing" class="mr-2" />
                    {{ processing ? 'Logging in...' : 'Log in to Dashboard' }}
                </Button>
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