<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { Check, ChevronsUpDown } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import OrganizationSwitchController from '@/actions/App/Http/Controllers/OrganizationSwitchController';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { Organization } from '@/types';

withDefaults(defineProps<{
    variant?: 'sidebar' | 'footer' | 'icon';
}>(), {
    variant: 'sidebar',
});

const page = usePage();
const currentOrganization = computed(() => page.props.auth.currentOrganization);
const organizations = computed(() => page.props.auth.organizations ?? []);
const hasMultipleOrgs = computed(() => organizations.value.length > 1);

const switching = ref(false);

function switchOrganization(organization: Organization) {
    if (organization.id === currentOrganization.value?.id) {
        return;
    }

    switching.value = true;
    router.visit(OrganizationSwitchController.url(organization), {
        method: 'put',
        preserveScroll: true,
        onFinish: () => {
            switching.value = false;
        },
    });
}

function getOrgInitials(name: string): string {
    return name
        .split(/\s+/)
        .slice(0, 2)
        .map((w) => w[0])
        .join('')
        .toUpperCase();
}
</script>

<template>
    <!-- Sidebar variant -->
    <template v-if="variant === 'sidebar'">
        <SidebarMenu>
            <SidebarMenuItem>
                <!-- Single org: link to dashboard -->
                <SidebarMenuButton v-if="!hasMultipleOrgs" size="lg" as-child>
                    <Link :href="dashboard()">
                        <div
                            class="flex aspect-square size-8 items-center justify-center rounded-md border text-sm font-medium"
                        >
                            {{ getOrgInitials(currentOrganization?.name ?? '') }}
                        </div>
                        <div class="ml-1 grid flex-1 text-left text-sm leading-tight">
                            <span class="truncate font-semibold">{{ currentOrganization?.name }}</span>
                        </div>
                    </Link>
                </SidebarMenuButton>

                <!-- Multiple orgs: dropdown switcher -->
                <DropdownMenu v-else>
                    <DropdownMenuTrigger as-child>
                        <SidebarMenuButton
                            size="lg"
                            class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                        >
                            <div
                                class="flex aspect-square size-8 items-center justify-center rounded-md border text-sm font-medium"
                            >
                                {{ getOrgInitials(currentOrganization?.name ?? '') }}
                            </div>
                            <div class="ml-1 grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-semibold">{{ currentOrganization?.name }}</span>
                            </div>
                            <ChevronsUpDown class="ml-auto size-4" />
                        </SidebarMenuButton>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent
                        class="w-(--reka-dropdown-menu-trigger-width) min-w-56 rounded-lg"
                        align="start"
                        :side-offset="4"
                    >
                        <DropdownMenuLabel class="text-xs text-muted-foreground">Organizations</DropdownMenuLabel>
                        <DropdownMenuItem
                            v-for="org in organizations"
                            :key="org.id"
                            class="cursor-pointer gap-2 p-2"
                            :disabled="switching"
                            @click="switchOrganization(org)"
                        >
                            <div
                                class="flex size-6 items-center justify-center rounded-sm border text-[0.625rem] font-medium"
                            >
                                {{ getOrgInitials(org.name) }}
                            </div>
                            <span class="truncate">{{ org.name }}</span>
                            <Check
                                v-if="org.id === currentOrganization?.id"
                                class="ml-auto size-4 shrink-0"
                            />
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </SidebarMenuItem>
        </SidebarMenu>
    </template>

    <!-- Footer variant: matches NavUser dropdown style -->
    <template v-else-if="variant === 'footer' && hasMultipleOrgs">
        <SidebarMenu>
            <SidebarMenuItem>
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <SidebarMenuButton
                            size="lg"
                            class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                        >
                            <div
                                class="flex aspect-square size-8 items-center justify-center rounded-md border text-sm font-medium"
                            >
                                {{ getOrgInitials(currentOrganization?.name ?? '') }}
                            </div>
                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-medium">{{ currentOrganization?.name }}</span>
                            </div>
                            <ChevronsUpDown class="ml-auto size-4" />
                        </SidebarMenuButton>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent
                        class="w-(--reka-dropdown-menu-trigger-width) min-w-56 rounded-lg"
                        side="top"
                        align="end"
                        :side-offset="4"
                    >
                        <DropdownMenuLabel class="text-xs text-muted-foreground">Switch organization</DropdownMenuLabel>
                        <DropdownMenuItem
                            v-for="org in organizations"
                            :key="org.id"
                            class="cursor-pointer gap-2 p-2"
                            :disabled="switching"
                            @click="switchOrganization(org)"
                        >
                            <div
                                class="flex size-6 items-center justify-center rounded-sm border text-[0.625rem] font-medium"
                            >
                                {{ getOrgInitials(org.name) }}
                            </div>
                            <span class="truncate">{{ org.name }}</span>
                            <Check
                                v-if="org.id === currentOrganization?.id"
                                class="ml-auto size-4 shrink-0"
                            />
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </SidebarMenuItem>
        </SidebarMenu>
    </template>

    <!-- Icon variant: compact icon button for header toolbar -->
    <template v-else-if="variant === 'icon' && hasMultipleOrgs">
        <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <Button
                    variant="ghost"
                    size="icon"
                    class="relative size-10 w-auto cursor-pointer rounded-full p-1 focus-within:ring-2 focus-within:ring-primary"
                >
                    <div
                        class="flex size-8 items-center justify-center rounded-full border text-sm font-medium"
                    >
                        {{ getOrgInitials(currentOrganization?.name ?? '') }}
                    </div>
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" class="w-64 rounded-lg">
                <DropdownMenuLabel class="text-xs text-muted-foreground">Switch organization</DropdownMenuLabel>
                <DropdownMenuItem
                    v-for="org in organizations"
                    :key="org.id"
                    class="cursor-pointer gap-2 p-2"
                    :disabled="switching"
                    @click="switchOrganization(org)"
                >
                    <div
                        class="flex size-6 items-center justify-center rounded-sm border text-[0.625rem] font-medium"
                    >
                        {{ getOrgInitials(org.name) }}
                    </div>
                    <span class="truncate">{{ org.name }}</span>
                    <Check
                        v-if="org.id === currentOrganization?.id"
                        class="ml-auto size-4 shrink-0"
                    />
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    </template>
</template>
