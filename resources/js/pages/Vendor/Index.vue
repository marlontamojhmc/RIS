<script setup lang="ts">
import { Menu, MenuButton, MenuItems, MenuItem, Transition } from '@headlessui/vue';
import LocatorAppSidebarLayout from '@/layouts/locator/LocatorAppSidebarLayout.vue';
import VendorAppSidebarLayout from '@/layouts/vendor/VendorAppSidebarLayout.vue';
</script>
<template>
  <VendorAppSidebarLayout>
  <div class="grid grid-cols-2 gap-4 w-full mt-4">

    <!-- Left Column -->
    <Menu as="div" class="relative w-full text-left">
      <MenuButton as="button" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition">
        Accreditation
      </MenuButton>
      <Transition
        enter="transition duration-100 ease-out"
        enter-from="transform scale-95 opacity-0"
        enter-to="transform scale-100 opacity-100"
        leave="transition duration-75 ease-in"
        leave-from="transform scale-100 opacity-100"
        leave-to="transform scale-95 opacity-0"
      >
        <MenuItems class="absolute mt-2 w-full rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
          <MenuItem as="a" href="#" class="text-center block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
            New Accreditation
          </MenuItem>
          <MenuItem as="a" href="#" class=" text-center block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
            Pending Approvals
          </MenuItem>
        </MenuItems>
      </Transition>
    </Menu>

    <!-- Right Column -->
    <Menu as="div" class="relative w-full text-left">
      <MenuButton as="button" class="w-full bg-gray-500 text-white py-2 rounded hover:bg-gray-600 transition">
        Re-Accreditation
      </MenuButton>
      <Transition
        enter="transition duration-100 ease-out"
        enter-from="transform scale-95 opacity-0"
        enter-to="transform scale-100 opacity-100"
        leave="transition duration-75 ease-in"
        leave-from="transform scale-100 opacity-100"
        leave-to="transform scale-95 opacity-0"
      >
        <MenuItems class="absolute mt-2 w-full rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
          <MenuItem as="a" href="#" class="block text-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
            Re-Accreditation Requests
          </MenuItem>
          <MenuItem as="a" href="#" class="block text-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
            History
          </MenuItem>
        </MenuItems>
      </Transition>
    </Menu>

  </div>
  </VendorAppSidebarLayout>
</template>


