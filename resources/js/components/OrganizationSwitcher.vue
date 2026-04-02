<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { ChevronsUpDown, Check, Building2, Plus } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import OrganizationSwitchController from '@/actions/App/Http/Controllers/OrganizationSwitchController';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from '@/components/ui/sidebar';
import type { Organization } from '@/types';

type Props = {
    variant?: 'sidebar' | 'header';
};

const props = withDefaults(defineProps<Props>(), {
    variant: 'sidebar',
});

const page = usePage();
const currentOrganization = computed(() => page.props.auth.currentOrganization);
const organizations = computed(() => page.props.auth.organizations ?? []);

const switching = ref(false);

function switchOrganization(organization: Organization) {
    if (organization.id === currentOrganization.value?.id) return;

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
    <!-- Sidebar variant: uses SidebarMenu components -->
    <template v-if="variant === 'sidebar'">
        <SidebarMenu>
            <SidebarMenuItem>
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <SidebarMenuButton
                            size="lg"
                            class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                        >
                            <div
                                class="flex aspect-square size-8 items-center justify-center rounded-md border text-xs font-medium"
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

    <!-- Header variant: plain dropdown button -->
    <template v-else>
        <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <button
                    class="inline-flex items-center gap-2 rounded-md px-3 py-1.5 text-sm font-medium transition-colors hover:bg-accent focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                >
                    <div
                        class="flex size-6 items-center justify-center rounded-sm border text-[0.625rem] font-medium"
                    >
                        {{ getOrgInitials(currentOrganization?.name ?? '') }}
                    </div>
                    <span class="hidden truncate sm:inline">{{ currentOrganization?.name }}</span>
                    <ChevronsUpDown class="size-4 opacity-50" />
                </button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="start" class="w-64 rounded-lg">
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
    </template>
</template>
