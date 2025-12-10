<script setup>
import { defineProps } from 'vue'

const props = defineProps({
  modelValue: Boolean,
  items: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['update:modelValue'])

const closeDialog = () => emit('update:modelValue', false)
</script>

<template>
  <VDialog
      v-model="props.modelValue"
      max-width="900"
      persistent
  >
    <!-- Dialog close btn -->
    <DialogCloseBtn @click="$emit('update:modelValue', false)" />

    <VCard>
      <VCardTitle>
        <span class="text-h6 font-weight-bold">Recommended Backlinks</span>
      </VCardTitle>

      <VCardText>
        <VTable>
          <thead>
          <tr>
<!--            <th>ID</th>-->
            <th>Website Name</th>
<!--            <th>Domain URL</th>-->
            <th>Platform</th>
            <th>DA</th>
            <th>Categories</th>
            <th>Country</th>
            <th>Profile URL</th>
            <th>Matches</th>
          </tr>
          </thead>

          <tbody>
          <tr v-for="item in props.items" :key="item.id">
<!--            <td>{{ item.id }}</td>-->
            <td>{{ item.website_name }}</td>
<!--            <td>{{ item.domain_url }}</td>-->
            <td>{{ item.platform_type }}</td>
            <td>{{ item.da }}</td>

            <!-- categories -->
            <td>
              <div class="flex flex-wrap gap-1">
                  <span
                      v-for="c in item.categories"
                      :key="c"
                      class="px-1 py-0.5 rounded bg-primary text-white text-xs"
                  >
                    {{ c }}
                  </span>
              </div>
            </td>

            <td>{{ item.country ?? '-' }}</td>

            <td>
              {{ item.profile_url ?? '-' }}
            </td>

            <td>
              <div v-if="item.match_summary">
                <div v-for="(val, key) in item.match_summary" :key="key">
                  {{ key }}: {{ val }}
                </div>
              </div>
            </td>
          </tr>
          </tbody>
        </VTable>
      </VCardText>

      <VCardActions>
        <VSpacer />
        <VBtn color="primary" @click="closeDialog">Close</VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

<style lang="scss"></style>