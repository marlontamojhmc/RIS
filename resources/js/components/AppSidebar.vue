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

import { usersDashboard, sezadDashboard } from '@/routes';
import { Link, usePage } from '@inertiajs/vue3';
import type { PageProps } from '@inertiajs/core';
import AppLogo from './AppLogo.vue';
import type { NavItem } from '@/types';

import {
    Users,
    UserRoundPen,
    BookOpen,
    Folder,
    LayoutGrid,
    SquareUserRound,
    Clock,
    Eye,
} from 'lucide-vue-next';

import { ref, onMounted } from 'vue';
import * as Ably from 'ably';

/* ===============================
   TYPES
   =============================== */
interface Permissions {
    isAdmin: boolean
    isLocator: boolean
    sezadManager: boolean
    accreditationSpsnbe: boolean
    accreditationCeoc: boolean
    accreditationTfbosta: boolean
    accreditationVme: boolean
    accreditationProvitional: boolean
}

/* ===============================
   PAGE & PERMISSIONS
   =============================== */
const page = usePage<PageProps>();

const propsAny = page.props as unknown as {
    auth?: { user?: any }
    permissions?: Partial<Permissions>
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

const user = propsAny.auth?.user ?? null;

/* ===============================
   ROLE CHECKS
   =============================== */
const BDD =
    user?.details &&
    user.details.role_id === 2 &&
    user.details.permission_id === 2 &&
    user.details.department_id === 5 &&
    user.details.division_id === null &&
    user.details.user_function_id === 6;

const vendor =
    user?.details &&
    user.details.permission_id === 2 &&
    user.details.role_id === 7 &&
    user.details.department_id === null &&
    user.details.user_function_id === null;

const ServiceProvider =
    user?.details &&
    user.details.role_id === 4 &&
    user.details.permission_id === 2 &&
    user.details.department_id === null &&
    user.details.user_function_id === null;
// permissions.isLocator= true; 
// console.log(propsAny.permissions);
/**Marlon you can remove later */

const osac =
      user?.details &&
    user.details.role_id === 4 &&
    user.details.permission_id === 2 &&
    user.details.department_id === null &&
    user.details.user_function_id === null;
/* ===============================
   MAIN NAVIGATION
   =============================== */
const mainNavItems: NavItem[] = [];

/* ADMIN */
if (permissions.isAdmin) {
    mainNavItems.push(
        { title: 'Dashboard', href: '/dashboard', icon: LayoutGrid },
        { title: 'Users', href: usersDashboard(), icon: SquareUserRound },
        { title: 'SEZAD', href: sezadDashboard(), icon: Folder },
        { title: 'BDD Created Users', href: 'bdd', icon: BookOpen },

        { title: 'Locator', href: '/locator', icon: LayoutGrid },
        { title: 'Create Application', href: '/loctr/applications/create', icon: SquareUserRound },
        { title: 'Pending Application', href: '/loctr/applications/pending', icon: Clock },
        { title: 'Approved Applications', href: '/loctr/applications/approved', icon: Eye },
    );
}

/* SEZAD MANAGER */
else if (permissions.sezadManager) {
    mainNavItems.push({
        title: 'SEZAD Dashboard',
        href: '/sezad',
        icon: LayoutGrid,
    });
}

/* ACCREDITATION TYPES */
else if (permissions.accreditationSpsnbe) {
    mainNavItems.push({
        title: 'Service Provider / Supplier',
        href: '/dashboard',
        icon: LayoutGrid,
    });
}
else if (permissions.accreditationCeoc) {
    mainNavItems.push({
        title: 'Commercial Event Operator',
        href: '/dashboard',
        icon: LayoutGrid,
    });
}
else if (permissions.accreditationTfbosta) {
    mainNavItems.push({
        title: 'Trade Fairs & Bazaars',
        href: '/dashboard',
        icon: LayoutGrid,
    });
}
else if (permissions.accreditationVme) {
    mainNavItems.push({
        title: 'Vendor & Micro Entrepreneurs',
        href: '/dashboard',
        icon: LayoutGrid,
    });
}
else if (permissions.accreditationProvitional) {
    mainNavItems.push({
        title: 'Provisional Grant',
        href: '/dashboard',
        icon: LayoutGrid,
    });
}

/* LOCATOR */
else if (permissions.isLocator) {
    mainNavItems.push(
        { title: 'Create Application', href: '/loctr/applications/create', icon: LayoutGrid },
        { title: 'Apply For ATO', href: '/loctr/applications/create', icon: LayoutGrid },
        { title: 'Vendor Requests', href: '/VendorVerify', icon: LayoutGrid },
        { title: 'My Vendors', href: '/MyVendors', icon: LayoutGrid },
        {title: "My Service Providers",href: '/MyServiceProviders', icon:Users},
        { title: 'Service Provider Requests', href: '/serviceProviderRequest', icon: Users },
      
    );
}

/* VENDOR */
else if (vendor) {
    mainNavItems.push(
        { title: 'Vendor Dashboard', href: '/', icon: LayoutGrid },
        { title: 'Apply for Accreditation', href: '/', icon: Eye },
        {title: 'Apply for Permit', href:'/loctr/applications/create', icon: LayoutGrid},
    );
}

/* SERVICE PROVIDER */
else if (ServiceProvider) {
    mainNavItems.push(
        { title: 'Service Provider', href: '/', icon: LayoutGrid },
        { title: 'Accreditation', href: '/', icon: LayoutGrid },
        {title: 'Apply for Permit', href:'/loctr/applications/create', icon: LayoutGrid},
    );
}

/* ===============================
   FOOTER NAV
   =============================== */
const footerNavItems: NavItem[] = [];

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
