<script setup lang="ts">
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
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import {Users, File, LayoutGrid, SquareUserRound, Clock, Eye } from 'lucide-vue-next';
import AppLogo from '../AppLogo.vue';
import { usePage} from '@inertiajs/vue3';

const page = usePage()
const mainNavItems: NavItem[] = [
    { title: 'Locator', href: '/locator', icon: LayoutGrid },
    { title: 'Create Application', href: '/loctr/applications/create', icon: SquareUserRound },
    { title: 'Pending Application', href: '/loctr/applications/pending', icon: Clock },
    { title: 'Approved Applications', href: '/loctr/applications/approved', icon: Eye },
    { title: 'My ATO', href: `/ato/viewer`, icon: File },
    { title: 'Vendor Requests', href: `/VendorVerify`, icon: Users },
    { title: 'My Vendors', href: `/MyVendors`, icon: Users },
    { title: 'My Service Providers', href: `/MyServiceProviders`, icon: Users },
     { title: 'ServiceProvider Requests', href: `/serviceProviderRequest`, icon: Users },
]

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
          
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
</template>
