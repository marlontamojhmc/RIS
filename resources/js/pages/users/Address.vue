<script setup lang="ts">
import { ref, watch } from 'vue'

// --------------------
// Types
// --------------------
interface PSGCEntry {
  code: string
  name: string
  oldName?: string
  regionCode?: string
  provinceCode?: string
  municipalityCode?: string
  cityCode?: string
}

// --------------------
// State
// --------------------
const regions = ref<PSGCEntry[]>([])
const provinces = ref<PSGCEntry[]>([])
const municipalities = ref<PSGCEntry[]>([])
const barangays = ref<PSGCEntry[]>([])

const selectedRegion = ref<string>("")
const selectedProvince = ref<string>("")
const selectedMunicipality = ref<string>("")
const selectedBarangay = ref<string>("")

// --------------------
// API Fetch Helpers
// --------------------
async function fetchRegions(): Promise<void> {
  regions.value = await fetch('https://psgc.cloud/api/regions').then(r => r.json())
}

async function fetchProvinces(regionCode: string): Promise<void> {
  provinces.value = await fetch(`https://psgc.cloud/api/regions/${regionCode}/provinces`).then(r => r.json())
}

async function fetchMunicipalities(provinceCode: string): Promise<void> {
  municipalities.value = await fetch(`https://psgc.cloud/api/provinces/${provinceCode}/municipalities`).then(r => r.json())
}

async function fetchBarangays(municipalityCode: string): Promise<void> {
  barangays.value = await fetch(`https://psgc.cloud/api/municipalities/${municipalityCode}/barangays`).then(r => r.json())
}

// --------------------
// Watchers
// --------------------
watch(selectedRegion, (val) => {
  if (val) {
    fetchProvinces(val)
    provinces.value = []
    municipalities.value = []
    barangays.value = []
    selectedProvince.value = ""
    selectedMunicipality.value = ""
    selectedBarangay.value = ""
  }
})

watch(selectedProvince, (val) => {
  if (val) {
    fetchMunicipalities(val)
    municipalities.value = []
    barangays.value = []
    selectedMunicipality.value = ""
    selectedBarangay.value = ""
  }
})

watch(selectedMunicipality, (val) => {
  if (val) {
    fetchBarangays(val)
    barangays.value = []
    selectedBarangay.value = ""
  }
})

// --------------------
// Init
// --------------------
fetchRegions()
</script>

<template>
  <div class="space-y-4">
    <div>
      <label class="block">Region:</label>
      <select v-model="selectedRegion" class="border p-2 rounded w-full">
        <option value="">Select Region</option>
        <option v-for="r in regions" :key="r.code" :value="r.code">{{ r.name }}</option>
      </select>
    </div>

    <div>
      <label class="block">Province:</label>
      <select v-model="selectedProvince" class="border p-2 rounded w-full" :disabled="!selectedRegion">
        <option value="">Select Province</option>
        <option v-for="p in provinces" :key="p.code" :value="p.code">{{ p.name }}</option>
      </select>
    </div>

    <div>
      <label class="block">Municipality:</label>
      <select v-model="selectedMunicipality" class="border p-2 rounded w-full" :disabled="!selectedProvince">
        <option value="">Select Municipality</option>
        <option v-for="m in municipalities" :key="m.code" :value="m.code">{{ m.name }}</option>
      </select>
    </div>

    <div>
      <label class="block">Barangay:</label>
      <select v-model="selectedBarangay" class="border p-2 rounded w-full" :disabled="!selectedMunicipality">
        <option value="">Select Barangay</option>
        <option v-for="b in barangays" :key="b.code" :value="b.code">{{ b.name }}</option>
      </select>
    </div>
  </div>
</template>
