<script setup lang="ts">
/* ===============================
   IMPORTS
   =============================== */
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';

import { sezadDashboard, usersDashboard } from '@/routes';
import type { NavItem } from '@/types';
import type { PageProps } from '@inertiajs/core';
import { Link, usePage } from '@inertiajs/vue3';
import AppLogo from './AppLogo.vue';

import {
    BookOpen,
    Clock,
    Eye,
    Folder,
    LayoutGrid,
    SquareUserRound,
    Users,
} from 'lucide-vue-next';

import * as Ably from 'ably';
import { onMounted, ref } from 'vue';

/* ===============================
   TYPES
   =============================== */
interface Permissions {
    isAdmin: boolean;
    isLocator: boolean;
    sezadManager: boolean;
    accreditationSpsnbe: boolean;
    accreditationCeoc: boolean;
    accreditationTfbosta: boolean;
    accreditationVme: boolean;
    accreditationProvitional: boolean;
}

/* ===============================
   PAGE & USER
   =============================== */
const page = usePage<PageProps>();
const propsAny = page.props as unknown as {
    auth?: { user?: any };
    permissions?: Partial<Permissions>;
};

const permissions: Permissions = {
    isAdmin: !!propsAny.permissions?.isAdmin,
    isLocator: !!propsAny.permissions?.isLocator,
    sezadManager: !!propsAny.permissions?.sezadManager,
    accreditationSpsnbe: !!propsAny.permissions?.accreditationSpsnbe,
    accreditationCeoc: !!propsAny.permissions?.accreditationCeoc,
    accreditationTfbosta: !!propsAny.permissions?.accreditationTfbosta,
    accreditationVme: !!propsAny.permissions?.accreditationVme,
    accreditationProvitional: !!propsAny.permissions?.accreditationProvitional,
};

const user = propsAny.auth?.user ?? {};
const userDetails = user.details ?? {};

/* ===============================
   ROLE CHECKS
   =============================== */
const roles = {
    BDD:
        userDetails.role_id === 2 &&
        userDetails.permission_id === 2 &&
        userDetails.department_id === 5 &&
        userDetails.division_id === null &&
        userDetails.user_function_id === 6,

    Vendor:
        userDetails.role_id === 7 &&
        userDetails.permission_id === 2 &&
        userDetails.department_id === null &&
        userDetails.user_function_id === null,

    ServiceProvider:
        userDetails.role_id === 4 &&
        userDetails.permission_id === 2 &&
        userDetails.department_id === null &&
        userDetails.user_function_id === null,

    OSAC:
        user?.details.user_function_id === 4 &&
        user?.details.role_id === 2 &&
        user?.details.department_id === 12 &&
        user?.details.business_type_id === null,

    Locator:
        user?.userRole === 'locator' && userDetails?.user_function_id === 4,

    Cco:
        userDetails.role_id === 2 &&
        userDetails.permission_id === 2 &&
        userDetails.user_function_id === null &&
        userDetails.department_id === 12,

    finance: user?.details.department_id === 10 && user?.details.role_id === 2,

    registerOfficer:
        user?.details.business_type_id === null &&
        user?.details.department_id === 12 &&
        user?.details.user_function_id === 12 &&
        user?.details.position_id === 60,
    sezadManager:
        user?.details.department_id === 2 &&
        user?.details.role_id === 2 &&
        user?.details.user_function_id === 5,
};
/* ===============================
   NAVIGATION ITEMS
   =============================== */
const mainNavItems: NavItem[] = [];
const footerNavItems: NavItem[] = [];

// Role/Permission-based nav config
const navConfig: Record<string, NavItem[]> = {
    admin: [
        { title: 'Dashboard', href: '/dashboard', icon: LayoutGrid },
        { title: 'Users', href: usersDashboard(), icon: SquareUserRound },
        { title: 'SEZAD', href: sezadDashboard(), icon: Folder },
        { title: 'BDD Created Users', href: '/bdd', icon: BookOpen },
        { title: 'Locator', href: '/locator', icon: LayoutGrid },
        {
            title: 'Create Application',
            href: '/loctr/applications/create',
            icon: SquareUserRound,
        },
        {
            title: 'Pending Application',
            href: '/loctr/applications/pending',
            icon: Clock,
        },
        {
            title: 'Approved Applications',
            href: '/loctr/applications/approved',
            icon: Eye,
        },
    ],
    sezadManager: [
        { title: 'SEZAD Dashboard', href: '/sezad', icon: LayoutGrid },
    ],
    accreditationSpsnbe: [
        {
            title: 'Service Provider / Supplier',
            href: '/dashboard',
            icon: LayoutGrid,
        },
    ],
    accreditationCeoc: [
        {
            title: 'Commercial Event Operator',
            href: '/dashboard',
            icon: LayoutGrid,
        },
    ],
    accreditationTfbosta: [
        {
            title: 'Trade Fairs & Bazaars',
            href: '/dashboard',
            icon: LayoutGrid,
        },
    ],
    accreditationVme: [
        {
            title: 'Vendor & Micro Entrepreneurs',
            href: '/dashboard',
            icon: LayoutGrid,
        },
    ],
    accreditationProvitional: [
        { title: 'Provisional Grant', href: '/dashboard', icon: LayoutGrid },
    ],
    locator: [
        {
            title: 'Create Application',
            href: '/loctr/applications/create',
            icon: LayoutGrid,
        },
        {
            title: 'Apply For ATO',
            href: '/loctr/applications/create',
            icon: LayoutGrid,
        },
        { title: 'Vendor Requests', href: '/VendorVerify', icon: LayoutGrid },
        { title: 'My Vendors', href: '/MyVendors', icon: LayoutGrid },
        {
            title: 'My Service Providers',
            href: '/MyServiceProviders',
            icon: Users,
        },
        {
            title: 'Service Provider Requests',
            href: '/serviceProviderRequest',
            icon: Users,
        },
    ],
    vendor: [
        { title: 'Vendor Dashboard', href: '/', icon: LayoutGrid },
        { title: 'Apply for Accreditation', href: '/', icon: Eye },
        {
            title: 'Apply for Permit',
            href: '/loctr/applications/create',
            icon: LayoutGrid,
        },
    ],
    serviceProvider: [
        { title: 'Service Provider', href: '/', icon: LayoutGrid },
        { title: 'Accreditation', href: '/', icon: LayoutGrid },
        {
            title: 'Apply for Permit',
            href: '/loctr/applications/create',
            icon: LayoutGrid,
        },
    ],
    osac: [
        { title: 'OSAC Dashboard', href: '/osac/dashboard', icon: LayoutGrid },
        {
            title: 'Permits',
            href: '/',
            icon: LayoutGrid,
            children: [
                { title: 'Permit List', href: '/sezad/osac', icon: '' },
                { title: 'Approved', href: '/', icon: '' },
                { title: 'Pending', href: '/', icon: '' },
            ],
        },
    ],
    cco: [
        { title: 'Custom Officer', href: '/', icon: LayoutGrid },
        {
            title: 'Permits',
            href: '/',
            icon: LayoutGrid,
            children: [
                { title: 'Permit List', href: '/sezad/cco', icon: '' },
                { title: 'Approved', href: '/', icon: '' },
                { title: 'Pending ', href: '/', icon: '' },
            ],
        },
    ],
    finance: [
        {
            title: 'Permits',
            href: '/',
            icon: LayoutGrid,
            children: [
                { title: 'Approved', href: '/', icon: '' },
                { title: 'Pending', href: '/', icon: '' },
            ],
        },
    ],
    registerOfficer: [
        {
            title: 'Permits',
            href: '/',
            icon: LayoutGrid,
            children: [
                { title: 'Permit List', href: '/sezad/ro', icon: '' },
                { title: 'Approved', href: '/', icon: '' },
                { title: 'Pending ', href: '/', icon: '' },
            ],
        },
        {
            title: 'Accreditations',
            href: '/',
            icon: LayoutGrid,
            children: [
                { title: 'Accreditation List', href: '', icon: '' },
                { title: 'Approved', href: '/', icon: '' },
                { title: 'Pending', href: '/', icon: '' },
            ],
        },
        {
            title: 'ATO',
            href: '/',
            icon: LayoutGrid,
            children: [
                { title: 'ATO list', href: '/', icon: '' },
                { title: 'Approved', href: '/', icon: '' },
                { title: 'Pending', href: '/', icon: '' },
            ],
        },
    ],
    sezadManager: [
        {
            title: 'Permits',
            href: '/',
            icon: LayoutGrid,
            children: [
                { title: 'Approved', href: '/', icon: '' },
                { title: 'Pending', href: '/', icon: '' },
            ],
        },
        {
            title: 'Accreditations',
            href: '/',
            icon: LayoutGrid,
            children: [
                { title: 'Approved', href: '/', icon: '' },
                { title: 'Pending', href: '/', icon: '' },
            ],
        },
        {
            title: 'ATO',
            href: '/',
            icon: LayoutGrid,
            children: [
                { title: 'Approved', href: '/', icon: '' },
                { title: 'Pending', href: '/', icon: '' },
            ],
        },
    ],
};

// Push nav items based on roles & permissions
if (permissions.isAdmin) mainNavItems.push(...navConfig.admin);
else if (permissions.sezadManager) mainNavItems.push(...navConfig.sezadManager);
else if (permissions.accreditationSpsnbe)
    mainNavItems.push(...navConfig.accreditationSpsnbe);
else if (permissions.accreditationCeoc)
    mainNavItems.push(...navConfig.accreditationCeoc);
else if (permissions.accreditationTfbosta)
    mainNavItems.push(...navConfig.accreditationTfbosta);
else if (permissions.accreditationVme)
    mainNavItems.push(...navConfig.accreditationVme);
else if (permissions.accreditationProvitional)
    mainNavItems.push(...navConfig.accreditationProvitional);
else if (roles.Locator) mainNavItems.push(...navConfig.locator);
else if (roles.Vendor) mainNavItems.push(...navConfig.vendor);
else if (roles.ServiceProvider) mainNavItems.push(...navConfig.serviceProvider);
else if (roles.OSAC) mainNavItems.push(...navConfig.osac);
else if (roles.Cco) mainNavItems.push(...navConfig.cco);
else if (roles.finance) mainNavItems.push(...navConfig.finance);
else if (roles.sezadManager) mainNavItems.push(...navConfig.sezadManager);
else if (roles.registerOfficer) mainNavItems.push(...navConfig.registerOfficer);
/* ===============================
   NOTIFICATIONS
   =============================== */
const hasNotification = ref(false);

onMounted(() => {
    hasNotification.value = localStorage.getItem('hasNotification') === 'true';

    const ablyKey = import.meta.env.VITE_ABLY_KEY;
    if (!ablyKey) return;

    const client = new Ably.Realtime({ key: ablyKey });
    const channel = client.channels.get('notifications');

    channel.subscribe(() => {
        hasNotification.value = true;
        localStorage.setItem('hasNotification', 'true');
    });
});

function clearNotification() {
    hasNotification.value = false;
    localStorage.setItem('hasNotification', 'false');
}
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <!-- HEADER -->
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/">
                            <AppLogo class="h-8 w-auto" />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <!-- CONTENT -->
        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <!-- FOOTER -->
        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser
                :user="user"
                :has-notification="hasNotification"
                @clear-notification="clearNotification"
            />
        </SidebarFooter>
    </Sidebar>

    <slot />
</template>
