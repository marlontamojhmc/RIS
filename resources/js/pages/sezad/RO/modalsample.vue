<template>

    <Modal :show="showModal" @close="onClose" maxWidth="max-w-2xl">
        <Card
                class="relative z-10 max-h-[90vh] w-full overflow-y-auto border-2 border-amber-50"
            >
                <!-- Header -->
                <CardHeader>
                    <div class="flex w-full items-start justify-between">
                        <div>
                            <CardTitle>
                                {{ form.form_title }}
                            </CardTitle>

                            <CardDescription>
                                Status: {{ form.status }}
                            </CardDescription>
                        </div>
                    </div>
                </CardHeader>

                <!-- Content -->
                <CardContent class="space-y-1">
                    <div class="flex flex-row justify-between">
                        <!-- left -->
                        <div>
                            <p>
                                Application Date:
                                {{ formatDate(form.application_date) }}
                            </p>
                            <p>
                                Approved Date:
                                {{ formatDate(form.approved_date) }}
                            </p>
                            <p>Control #: {{ form.control_number }}</p>
                            <p>Locator: {{ form.locator_name }}</p>
                        </div>
                        <!-- right -->
                        <div v-if="form.approvers.length">
                            <h3 class="text-base font-semibold text-gray-800">
                                Approvers
                            </h3>

                            <ol class="space-y-1">
                                <li
                                    v-for="(a, i) in form.approvers"
                                    :key="i"
                                    class="flex items-center justify-between rounded-lg border bg-gray-50 px-1 py-1"
                                >
                                    <!-- Left Section -->
                                    <div class="flex flex-col">
                                        <span
                                            class="font-semibold text-gray-900"
                                        >
                                            {{ a.approver?.name }}
                                        </span>

                                        <span class="text-sm text-gray-500">
                                            {{ a.role }}
                                        </span>
                                    </div>

                                    <!-- Right Section (Status Badge) -->
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-medium capitalize"
                                        :class="
                                            statusClasses[
                                                normalizeStatus(a.status)
                                            ] || 'bg-gray-100 text-gray-600'
                                        "
                                    >
                                        {{ a.status }}
                                    </span>
                                </li>
                            </ol>
                        </div>
                    </div>

                    <div v-for="(items, index) in form.approvers" :key="index">
                        <div
                            v-if="
                                items.role == userRole.role &&
                                form.approvers[Number(userRole.sequence) - 1]
                                    .status == 'Approved'
                            "
                        >
                            <!-- Finance -->
                            <div
                                v-if="
                                    userRole.role == 'Finance' &&
                                    items.status != 'Approved'
                                "
                            >
                                <div class="flex gap-2">
                                    <button
                                        v-show="!openPayment"
                                        type="button"
                                        @click="openPaymentFooter(true)"
                                        class="rounded-md bg-primary px-4 py-2 text-primary-foreground transition hover:bg-primary/90"
                                    >
                                        Proceed Payment
                                    </button>

                                    <button
                                        v-show="openPayment"
                                        type="button"
                                        @click="openPaymentFooter(false)"
                                        class="rounded-md bg-destructive text-destructive-foreground transition hover:bg-destructive/90"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </div>
                            <!-- Other Signatoriesd -->
                            <div
                                v-show="
                                    userRole.role != 'Finance' &&
                                    items.role == userRole.role &&
                                    form.approvers[
                                        Number(userRole.sequence) - 1
                                    ].status == 'Approved' &&
                                    items.status == 'Pending'
                                "
                            >
                                <!-- {{ page }} -->
                                <button @click="onApprove">Approve</button>
                            </div>
                        </div>
                    </div>
                </CardContent>

                <!-- Footer -->
                <CardFooter v-show="openPayment" class="flex flex-row">
                    <div class="flex flex-col">
                        <div>
                            <Input
                                type="number"
                                name="text"
                                v-model="formPayment.is_number"
                                placeholder="Enter IS Number"
                            />
                        </div>
                        <div>
                            <Input
                                type="number"
                                name="amount"
                                v-model="formPayment.amount"
                                placeholder="Amount"
                            />
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <!-- <div>
                                <Calendar v-model="formPayment.payment_date" />
                            </div> -->
                        <div>
                            <button
                                class="rounded-md bg-primary px-4 py-2 text-white transition hover:bg-primary/90"
                                @click="submitPayment"
                            >
                                Accept Button
                            </button>
                        </div>
                    </div>
                </CardFooter>
            </Card>
        </Modal>

    </template>