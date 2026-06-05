<template>
  <nav v-if="lastPage > 1">
    <ul class="pagination justify-content-end">

      <li class="page-item" :class="{ disabled: currentPage === 1 }">
        <button class="page-link" @click="$emit('change', currentPage - 1)">
          Previous
        </button>
      </li>

      <li
          v-for="page in lastPage"
          :key="page"
          class="page-item"
          :class="{ active: currentPage === page }">

        <button
            class="page-link"
            @click="$emit('change', page)">
          {{ page }}
        </button>

      </li>

      <li class="page-item" :class="{ disabled: currentPage === lastPage }">
        <button class="page-link" @click="$emit('change', currentPage + 1)">
          Next
        </button>
      </li>

    </ul>
  </nav>
</template>

<script setup>
defineProps({
  currentPage: Number,
  lastPage: Number
})

defineEmits(['change'])
</script>