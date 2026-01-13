<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import NavMainOSAC from '@/layouts/Osac/NavMainOSAC.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard,usersDashboard,sezadDashboard,bddDashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid,SquareUserRound, Clock, Eye } from 'lucide-vue-next';
import AppLogo from '../AppLogo.vue';


const mainNavItems: NavItem[] = [
    {
        title: 'Osac',
        href: '/osac',
        icon: LayoutGrid,
        children: [
      {
        title: "Pending Applications",
        href: "/applications/all",
      },
      {
        title: "Apply for Locator",
        href:"/osac/apply",
      },{
        title:"Assigned to Me",
        href:"/osac",
      }
    ],
    },
    
];

const footerNavItems: NavItem[] = [
];
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
            <NavMainOSAC :items="mainNavItems" />
           
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
             <NavUser />
        </SidebarFooter>
    </Sidebar>
   

</template>
