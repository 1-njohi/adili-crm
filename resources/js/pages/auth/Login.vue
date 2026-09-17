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
        description: 'Log in to view your installments and payments',
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
            <img
                src="/logo.png"
                alt="Adili Real Estate"
                class="h-20 w-auto mx-auto mb-4"
            />
            <p class="text-sm text-gray-500 italic">Defined by Trust</p>
        </div>

        <!-- Login Card -->
        <div class="w-full max-w-md bg-white rounded-2xl shadow-lg ring-1 ring-gray-200 p-6 sm:p-8">
            <!-- Header -->
            <div class="mb-6 text-center">
                <h2 class="text-xl font-bold text-gray-800">Welcome back</h2>
                <p class="mt-1 text-sm text-gray-500">Log in to view your plot, payments, and referrals</p>
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
                        placeholder="you@example.com"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 placeholder-gray-400 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
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
                            class="text-sm text-sky-600 hover:text-sky-700 hover:underline"
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
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 placeholder-gray-400 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
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
                            class="rounded border-gray-300 text-sky-500 focus:ring-sky-500"
                        />
                        <span>Remember me for 30 days</span>
                    </Label>
                </div>

                <!-- Login Button -->
                <Button
                    type="submit"
                    class="mt-2 w-full rounded-lg bg-sky-500 px-4 py-3 text-base font-bold text-white shadow-md transition hover:bg-sky-600 disabled:opacity-50"
                    :tabindex="4"
                    :disabled="processing"
                    data-test="login-button"
                >
                    <Spinner v-if="processing" class="mr-2" />
                    {{ processing ? 'Logging in...' : 'Log in to My Portal' }}
                </Button>

                <!-- Divider + Register (if available) -->
                <!-- @chisel-registration -->
                <div class="relative my-2">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs uppercase">
                        <span class="bg-white px-3 text-gray-400">or</span>
                    </div>
                </div>

                <div class="text-center text-sm text-gray-600">
                    New to Adili?
                    <TextLink
                        :href="register()"
                        class="font-medium text-sky-600 hover:text-sky-700 hover:underline"
                    >
                        Create your buyer account
                    </TextLink>
                </div>
                <!-- @end-chisel-registration -->
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