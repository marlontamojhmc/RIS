<script setup lang="ts">
import VendorAppSidebarLayout from '@/layouts/vendor/VendorAppSidebarLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Separator } from '@/components/ui/separator'

interface Accreditation {
  id: number
  user_id: number
  form_number: string
  date: string
  type: 'new' | 'renewal'
  business_name: string
  frequency: string
  address: string
  email: string
  contact: string
  representative: string
  privacy_consent: number
  created_at: string
  updated_at: string
}

interface Approver {
  id: number
  approver_group_id: number
  approver_id: number
  application_form_id: number
  sequence: number
  status: 'Pending' | 'Approved' | 'Rejected'
  role: string
  acted_at: string | null
  remark: string | null
  created_at: string
  updated_at: string
}

const props = defineProps<{
  accreditation: Accreditation
  approvers: Approver[]
}>()
const { accreditation, approvers } = props
</script>

<template>
  <VendorAppSidebarLayout>
    <div class="max-w-6xl mx-auto p-6">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column: Accreditation Details -->
        <Card>
          <CardHeader>
            <CardTitle class="text-xl">Accreditation Details</CardTitle>
          </CardHeader>

          <CardContent class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-muted-foreground">Form Number</p>
                <p class="font-medium">{{ accreditation.form_number }}</p>
              </div>

              <div>
                <p class="text-sm text-muted-foreground">Type</p>
                <p class="font-medium capitalize">{{ accreditation.type }}</p>
              </div>

              <div>
                <p class="text-sm text-muted-foreground">Business Name</p>
                <p class="font-medium">{{ accreditation.business_name }}</p>
              </div>

              <div>
                <p class="text-sm text-muted-foreground">Frequency</p>
                <p class="font-medium">{{ accreditation.frequency }}</p>
              </div>

              <div>
                <p class="text-sm text-muted-foreground">Date</p>
                <p class="font-medium">{{ accreditation.date }}</p>
              </div>

              <div>
                <p class="text-sm text-muted-foreground">Representative</p>
                <p class="font-medium">{{ accreditation.representative }}</p>
              </div>
            </div>

            <Separator />

            <div class="space-y-2">
              <div>
                <p class="text-sm text-muted-foreground">Address</p>
                <p class="font-medium">{{ accreditation.address }}</p>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <p class="text-sm text-muted-foreground">Email</p>
                  <p class="font-medium">{{ accreditation.email }}</p>
                </div>

                <div>
                  <p class="text-sm text-muted-foreground">Contact</p>
                  <p class="font-medium">{{ accreditation.contact }}</p>
                </div>
              </div>
            </div>

            <Separator />

            <div class="flex items-center justify-between">
              <p class="text-sm text-muted-foreground">
                Privacy Consent:
                <span class="font-medium text-foreground">
                  {{ accreditation.privacy_consent ? 'Yes' : 'No' }}
                </span>
              </p>

              <Button
                variant="outline"
                @click="$inertia.visit('/loctr/applications/pending')"
              >
                Back to List
              </Button>
            </div>
          </CardContent>
        </Card>

        <!-- Right Column: Approval Flow (1 per row) -->
        <Card>
          <CardHeader>
            <CardTitle class="text-xl">Approval Flow</CardTitle>
          </CardHeader>

          <CardContent class="space-y-3">
            <div
              v-for="approver in approvers"
              :key="approver.id"
              class="flex items-center justify-between rounded-lg border p-4"
            >
              <div>
                <p class="font-medium">{{ approver.role }}</p>
                <p class="text-sm text-muted-foreground">Sequence {{ approver.sequence }}</p>
              </div>

              <div class="text-right">
                <p
                  class="text-sm font-semibold"
                  :class="{
                    'text-yellow-600': approver.status === 'Pending',
                    'text-green-600': approver.status === 'Approved',
                    'text-red-600': approver.status === 'Rejected',
                  }"
                >
                  {{ approver.status }}
                </p>
                <p v-if="approver.acted_at" class="text-xs text-muted-foreground">
                  {{ approver.acted_at }}
                </p>
              </div>
            </div>

            <div v-if="!approvers.length" class="text-sm text-muted-foreground">
              No approvers assigned.
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </VendorAppSidebarLayout>
</template>