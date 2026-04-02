<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/checkout';

defineOptions({
    layout: {
        title: 'Choose your plan',
        description:
            'Select a plan to get started. You can change your plan at any time.',
    },
});

type Plan = {
    id: string;
    name: string;
    slug: string;
    stripe_monthly_price_id: string | null;
    stripe_annual_price_id: string | null;
    monthly_price: number;
    annual_price: number;
    sort_order: number;
    is_active: boolean;
    trial_days: number;
};

defineProps<{
    plans: Plan[];
}>();

const interval = ref<'monthly' | 'annual'>('monthly');
const submittingPlan = ref<string | null>(null);

const features: Record<string, string[]> = {
    starter: [
        'Up to 5 team members',
        'Basic reporting',
        'Email support',
        '1 GB storage',
    ],
    professional: [
        'Up to 25 team members',
        'Advanced reporting',
        'Priority support',
        '10 GB storage',
        'Custom integrations',
    ],
    enterprise: [
        'Unlimited team members',
        'Enterprise reporting',
        'Dedicated support',
        'Unlimited storage',
        'Custom integrations',
        'SSO & advanced security',
    ],
};

function formatPrice(cents: number): string {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0,
    }).format(cents / 100);
}

function priceForInterval(plan: Plan): number {
    return interval.value === 'monthly' ? plan.monthly_price : plan.annual_price;
}

function selectPlan(plan: Plan) {
    submittingPlan.value = plan.slug;

    router.post(
        store.url(),
        {
            plan: plan.slug,
            interval: interval.value,
        },
        {
            onFinish: () => {
                submittingPlan.value = null;
            },
        },
    );
}
</script>

<template>
    <Head title="Choose your plan" />

    <div class="flex flex-col gap-8">
        <!-- Interval toggle -->
        <div class="flex items-center justify-center gap-1 rounded-full border p-1 self-center">
            <button
                class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                :class="
                    interval === 'monthly'
                        ? 'bg-primary text-primary-foreground'
                        : 'text-muted-foreground hover:text-foreground'
                "
                @click="interval = 'monthly'"
            >
                Monthly
            </button>
            <button
                class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                :class="
                    interval === 'annual'
                        ? 'bg-primary text-primary-foreground'
                        : 'text-muted-foreground hover:text-foreground'
                "
                @click="interval = 'annual'"
            >
                Annual
            </button>
        </div>

        <!-- Plan cards -->
        <div class="grid gap-6 md:grid-cols-3">
            <Card
                v-for="plan in plans"
                :key="plan.id"
                :class="[
                    'relative flex flex-col transition-shadow',
                    plan.slug === 'professional'
                        ? 'border-primary shadow-md'
                        : '',
                ]"
            >
                <CardHeader>
                    <div class="flex items-center gap-2">
                        <CardTitle class="text-lg">{{ plan.name }}</CardTitle>
                        <Badge
                            v-if="plan.trial_days > 0"
                            variant="secondary"
                        >
                            {{ plan.trial_days }}-day free trial
                        </Badge>
                    </div>
                    <CardDescription>
                        <template v-if="plan.slug === 'starter'">
                            Everything you need to get started.
                        </template>
                        <template v-else-if="plan.slug === 'professional'">
                            For growing teams that need more.
                        </template>
                        <template v-else-if="plan.slug === 'enterprise'">
                            Advanced features for large organizations.
                        </template>
                    </CardDescription>
                </CardHeader>

                <CardContent class="flex flex-1 flex-col gap-6">
                    <div>
                        <span class="text-3xl font-semibold tracking-tight">
                            {{ formatPrice(priceForInterval(plan)) }}
                        </span>
                        <span class="text-sm text-muted-foreground">
                            /{{ interval === 'monthly' ? 'mo' : 'yr' }}
                        </span>
                    </div>

                    <ul class="space-y-2 text-sm">
                        <li
                            v-for="feature in features[plan.slug] ?? []"
                            :key="feature"
                            class="flex items-start gap-2 text-muted-foreground"
                        >
                            <svg
                                class="mt-0.5 h-4 w-4 shrink-0 text-foreground"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                            {{ feature }}
                        </li>
                    </ul>
                </CardContent>

                <CardFooter>
                    <Button
                        class="w-full"
                        :variant="
                            plan.slug === 'professional'
                                ? 'default'
                                : 'outline'
                        "
                        :disabled="submittingPlan !== null"
                        @click="selectPlan(plan)"
                    >
                        <Spinner v-if="submittingPlan === plan.slug" />
                        <template v-else>
                            {{
                                plan.trial_days > 0
                                    ? `Start ${plan.trial_days}-day free trial`
                                    : `Get started`
                            }}
                        </template>
                    </Button>
                </CardFooter>
            </Card>
        </div>
    </div>
</template>
