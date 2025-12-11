<script setup>
import { defineProps, computed } from 'vue'

const props = defineProps({
  modelValue: Boolean,
  items: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['update:modelValue'])

const closeDialog = () => emit('update:modelValue', false)

const hasResults = computed(() => props.items && props.items.length > 0)

const getMatchColor = (value) => {
  return value > 0 ? 'success' : 'error'
}

const getTotalMatches = (item) => {
  if (!item.match_summary) return 0
  return Object.values(item.match_summary).reduce((sum, val) => sum + val, 0)
}

const getMatchPercentage = (item) => {
  const total = getTotalMatches(item)
  return Math.round((total / 4) * 100) // 4 criteria max
}
</script>

<template>
  <VDialog
      v-model="props.modelValue"
      max-width="1200"
      persistent
      scrollable
  >
    <!-- Dialog close btn -->
    <DialogCloseBtn @click="$emit('update:modelValue', false)" />

    <VCard>
      <VCardTitle class="d-flex align-center justify-space-between">
        <span class="text-h6 font-weight-bold">Recommended Backlinks</span>
        <VChip v-if="hasResults" color="primary" size="small">
          {{ props.items.length }} Recommendations
        </VChip>
      </VCardTitle>

      <VCardText>
        <!-- No Results State -->
        <div v-if="!hasResults" class="text-center py-8">
          <VIcon icon="tabler-link-off" size="64" color="grey-lighten-1" class="mb-4"/>
          <div class="text-h6 mb-2">No Recommendations Found</div>
          <p class="text-body-2 text-grey">
            We couldn't find any backlinks matching your domain's criteria.<br>
            Try adjusting your domain settings or check back later for new backlinks.
          </p>
        </div>

        <!-- Results Table -->
        <VTable v-else density="comfortable">
          <thead>
          <tr>
            <th class="text-center">Match Score</th>
            <th>Website Name</th>
            <!-- <th>Platform</th> -->
            <!-- <th class="text-center">DA</th> -->
            <!-- <th>Categories</th> -->
            <!-- <th class="text-center">Country</th> -->
            <th>Profile URL</th>
            <th>Match Details</th>
          </tr>
          </thead>

          <tbody>
          <tr v-for="item in props.items" :key="item.id">
            <!-- Match Score Column -->
            <td class="text-center">
              <VChip
                  :color="getMatchPercentage(item) > 75 ? 'success' : getMatchPercentage(item) > 50 ? 'warning' : 'info'"
                  size="small"
              >
                {{ getTotalMatches(item) }}/4
              </VChip>
              <div class="text-caption text-grey mt-1">
                {{ getMatchPercentage(item) }}%
              </div>
            </td>

            <td>
              <div class="font-weight-medium">{{ item.website_name }}</div>
              <div class="text-caption text-grey">{{ item.domain_url }}</div>
            </td>

            <!-- Platform Column - Commented -->
            <!-- <td>
              <VChip  v-if="item.platform_type" size="small" variant="tonal">
                {{ item.platform_type }}
              </VChip>
              <span v-else class="text-grey">-</span>
            </td> -->

            <!-- DA Column - Commented -->
            <!-- <td class="text-center">
              <VChip v-if="item.da" size="small" color="primary" variant="tonal">
                {{ item.da }}
              </VChip>
              <span v-else class="text-grey">-</span>
            </td> -->

            <!-- Categories Column - Commented -->
            <!-- <td>
              <div class="d-flex flex-wrap ga-1 align-center">
                <VChip
                    v-for="c in (item.categories || []).slice(0, 2)"
                    :key="c"
                    size="x-small"
                    color="secondary"
                    variant="tonal"
                >
                  {{ c }}
                </VChip>

                <VMenu v-if="item.categories && item.categories.length > 2">
                  <template v-slot:activator="{ props: menuProps }">
                    <VChip
                        v-bind="menuProps"
                        size="x-small"
                        color="secondary"
                        variant="outlined"
                        class="cursor-pointer"
                    >
                      +{{ item.categories.length - 2 }} more
                      <VIcon end size="small">mdi-chevron-down</VIcon>
                    </VChip>
                  </template>

                  <VList density="compact" max-width="250">
                    <VListItem
                        v-for="cat in item.categories"
                        :key="cat"
                    >
                      <VListItemTitle>
                        <VChip size="small" color="secondary" variant="tonal">
                          {{ cat }}
                        </VChip>
                      </VListItemTitle>
                    </VListItem>
                  </VList>
                </VMenu>
                <span v-else class="text-grey">-</span>
              </div>
            </td> -->

            <!-- Country Column - Commented -->
            <!-- <td class="text-center">
              <VChip
                  v-if="item.country"
                  size="small"
                  variant="tonal"
              >
                {{ item.country }}
              </VChip>
              <span v-else class="text-grey">-</span>
            </td> -->

            <!-- Profile URL Column -->
            <td>
              <div v-if="item.profile_url">
                <div class="text-caption text-medium-emphasis mb-1">{{ item.profile_url }}</div>
                <a
                    :href="item.profile_url"
                    target="_blank"
                    class="text-decoration-none"
                >
                  <VBtn size="x-small" variant="tonal" color="primary">
                    Visit
                    <VIcon end size="small">mdi-open-in-new</VIcon>
                  </VBtn>
                </a>
              </div>
              <span v-else class="text-grey">-</span>
            </td>

            <!-- Match Details Column -->
            <td>
              <div v-if="item.match_summary" class="d-flex flex-column ga-1 my-1">
                <VChip
                    v-for="(val, key) in item.match_summary"
                    :key="key"
                    size="x-small"
                    :color="getMatchColor(val)"
                    variant="flat"
                >
                  {{ key.replace('_', ' ') }}: {{ val > 0 ? '✓' : '✗' }}
                </VChip>
              </div>
            </td>
          </tr>
          </tbody>
        </VTable>

        <!-- Match Legend -->
        <div v-if="hasResults" class="mt-4 pa-3 bg-grey-lighten-4 rounded">
          <div class="text-caption font-weight-bold mb-2">Match Criteria:</div>
          <div class="d-flex flex-wrap ga-2">
            <VChip size="x-small" color="success">
              Categories Match
            </VChip>
            <VChip size="x-small" color="success">
              Platform Match
            </VChip>
            <VChip size="x-small" color="success">
              Country Match
            </VChip>
            <VChip size="x-small" color="success">
              DA Within ±10
            </VChip>
          </div>
        </div>
      </VCardText>

      <VCardActions>
        <VSpacer />
        <VBtn color="primary" variant="flat" @click="closeDialog">Close</VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

<style lang="scss" scoped>
.text-caption {
  font-size: 0.75rem;
}

.cursor-pointer {
  cursor: pointer;
}
</style>