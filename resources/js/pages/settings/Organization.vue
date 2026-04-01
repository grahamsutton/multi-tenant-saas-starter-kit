<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import OrganizationController from '@/actions/App/Http/Controllers/Settings/OrganizationController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit } from '@/routes/organization';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Organization settings',
                href: edit(),
            },
        ],
    },
});

const page = usePage();
const organization = computed(() => page.props.auth.currentOrganization);
</script>

<template>
    <Head title="Organization settings" />

    <h1 class="sr-only">Organization settings</h1>

    <div class="flex flex-col space-y-6">
        <Heading
            variant="small"
            title="Organization information"
            description="Update your organization's name"
        />

        <Form
            v-bind="OrganizationController.update.form()"
            class="space-y-6"
            v-slot="{ errors, processing, recentlySuccessful }"
        >
            <div class="grid gap-2">
                <Label for="name">Organization name</Label>
                <Input
                    id="name"
                    class="mt-1 block w-full"
                    name="name"
                    :default-value="organization?.name"
                    required
                    autocomplete="organization"
                    placeholder="Organization name"
                />
                <InputError class="mt-2" :message="errors.name" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save</Button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-show="recentlySuccessful"
                        class="text-sm text-neutral-600"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </Form>
    </div>
</template>
