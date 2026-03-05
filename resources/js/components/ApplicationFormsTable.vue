<script setup lang="ts">
import { Eye, Pencil, Trash2 } from 'lucide-vue-next';
interface UserRole {
    role: string;
    sequence: number | string;
    approver_group_id: number;
}

interface Application {
    id: number;
    control_number?: string;
    application?: {
        form_title?: string;
        form_number?: string;
        status?: string;
    };
}

const props = defineProps<{
    userRole: UserRole;
    applications: Application[];
}>();
const userRole = props.userRole;
const emit = defineEmits(['onOpenRecords', 'onOpenTimeline']);

const handleViewRecords = (app: any) => {
    emit('onOpenRecords', app);
};
const handleViewTime = (app: any) => {
    emit('onOpenTimeline', app);
};

// console.log(userRole);
</script>

<template>
    <!-- {{ userRole.sequence }} -->
    <div
        class="overflow-x-auto rounded-lg border border-gray-300 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-100"
    >
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-800">
                <tr class="text-center">
                    <th
                        class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200"
                    >
                        #
                    </th>
                    <th
                        class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200"
                    >
                        Form Title
                    </th>
                    <th
                        class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200"
                    >
                        Control Number
                    </th>
                    <th
                        class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200"
                    >
                        Form Number
                    </th>
                    <th
                        class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200"
                    >
                        Status
                    </th>
                    <th
                        class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200"
                    >
                        Actions
                    </th>
                </tr>
            </thead>

            <tbody
                v-if="applications.length"
                class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900"
            >
                <tr
                    v-for="(app, index) in applications"
                    :key="app.id"
                    :class="[
                        'text-center transition duration-500',
                        // app.application?.status === 'Approved'
                        //     ? 'bg-green-50'
                        //     : '',
                    ]"
                >
                    <td
                        class="px-4 py-2 align-middle text-sm dark:text-gray-100"
                    >
                        {{ index + 1 }}
                    </td>
                    <td
                        class="px-4 py-2 align-middle text-sm text-gray-800 dark:text-gray-100"
                    >
                        {{ app.application?.form_title ?? '-' }}
                    </td>
                    <td
                        class="px-4 py-2 align-middle text-sm text-gray-600 dark:text-gray-300"
                    >
                        {{ app.control_number ?? 'N/A' }}
                    </td>
                    <td
                        class="px-4 py-2 align-middle text-sm text-gray-600 dark:text-gray-300"
                    >
                        {{ app.application?.form_number ?? '—' }}
                    </td>
                    <td
                        class="px-4 py-2 align-middle text-sm text-gray-600 capitalize dark:text-gray-300"
                    >
                        {{ app.application?.status ?? 'N/A' }}
                    </td>
                    <td
                        class="flex items-center justify-center gap-2 px-4 py-2 text-sm"
                    >
                        <button
                            @click="handleViewRecords(app)"
                            class="rounded-full p-1 text-blue-600 transition hover:bg-blue-50 hover:text-blue-800 dark:text-blue-400 dark:hover:bg-blue-900/30 dark:hover:text-blue-300"
                            title="View"
                        >
                            <Eye class="h-5 w-5 dark:text-gray-100" />
                        </button>
                        <button
                            @click="handleViewTime(app)"
                            class="rounded-full p-1 text-blue-600 transition hover:bg-blue-50 hover:text-blue-800 dark:text-blue-400 dark:hover:bg-blue-900/30 dark:hover:text-blue-300"
                            title="View"
                        >
                            <Eye class="h-5 w-2 dark:text-gray-100" />
                        </button>
                        <button
                            v-show="userRole.role == 'Manager'"
                            @click="emit('edit', app)"
                            class="rounded-full p-1 text-green-600 transition hover:bg-green-50 hover:text-green-800 dark:text-green-400 dark:hover:bg-green-900/30 dark:hover:text-green-300"
                            title="Edit"
                        >
                            <Pencil class="h-5 w-5" />
                        </button>

                        <button
                            v-show="userRole.role == 'Manager'"
                            @click="emit('delete', app)"
                            class="rounded-full p-1 text-red-600 transition hover:bg-red-50 hover:text-red-800 dark:text-red-400 dark:hover:bg-red-900/30 dark:hover:text-red-300"
                            title="Delete"
                        >
                            <Trash2 class="h-5 w-5" />
                        </button>
                    </td>
                </tr>
            </tbody>

            <tbody v-else>
                <tr>
                    <td
                        colspan="6"
                        class="py-4 text-center text-gray-500 dark:text-gray-400"
                    >
                        No applications found.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
