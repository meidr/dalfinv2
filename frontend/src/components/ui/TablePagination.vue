<template>
  <div
    v-if="visible"
    class="table-pagination flex flex-col gap-3 border-t border-border-light px-4 py-4 lg:flex-row lg:items-center lg:justify-between"
  >
    <p class="text-sm text-text-secondary">
      Menampilkan
      <span class="font-medium text-text-main">{{ from }}</span>
      -
      <span class="font-medium text-text-main">{{ to }}</span>
      dari
      <span class="font-medium text-text-main">{{ total }}</span>
      {{ itemLabel }}
    </p>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
      <label
        v-if="showPerPage"
        class="flex items-center gap-2 text-sm text-text-secondary"
      >
        <span>Show by</span>
        <select
          :value="perPage"
          :disabled="disabled"
          class="h-9 rounded-lg border border-border-light bg-surface-light px-3 text-sm font-medium text-text-main outline-none transition-colors focus:border-primary focus:ring-2 focus:ring-primary/20 disabled:cursor-not-allowed disabled:opacity-50"
          @change="emitPerPageChange"
        >
          <option v-for="option in perPageOptions" :key="option" :value="option">
            {{ option }}
          </option>
        </select>
      </label>

      <div class="flex flex-wrap items-center gap-1">
        <button
          type="button"
          class="table-pagination__button"
          :disabled="disabled || currentPage <= 1"
          @click="emitPageChange(1)"
        >
          First
        </button>
        <button
          type="button"
          class="table-pagination__button"
          :disabled="disabled || currentPage <= 1"
          @click="emitPageChange(currentPage - 1)"
        >
          Prev
        </button>

        <template v-for="page in pages" :key="page.key">
          <span v-if="page.ellipsis" class="px-2 text-sm text-text-secondary">
            ...
          </span>
          <button
            v-else
            type="button"
            class="table-pagination__page"
            :class="page.value === currentPage ? 'is-active' : ''"
            :disabled="disabled"
            @click="emitPageChange(page.value)"
          >
            {{ page.value }}
          </button>
        </template>

        <button
          type="button"
          class="table-pagination__button"
          :disabled="disabled || currentPage >= lastPage"
          @click="emitPageChange(currentPage + 1)"
        >
          Next
        </button>
        <button
          type="button"
          class="table-pagination__button"
          :disabled="disabled || currentPage >= lastPage"
          @click="emitPageChange(lastPage)"
        >
          End
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  pagination: {
    type: Object,
    required: true,
  },
  perPageOptions: {
    type: Array,
    default: () => [10, 15, 20, 25, 50, 100],
  },
  showPerPage: {
    type: Boolean,
    default: true,
  },
  itemLabel: {
    type: String,
    default: "data",
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  maxVisiblePages: {
    type: Number,
    default: 5,
  },
});

const emit = defineEmits(["page-change", "per-page-change"]);

const currentPage = computed(() =>
  Math.max(1, Number(props.pagination?.current_page || 1)),
);

const lastPage = computed(() =>
  Math.max(1, Number(props.pagination?.last_page || 1)),
);

const perPage = computed(() =>
  Number(props.pagination?.per_page || props.perPageOptions[0] || 10),
);

const total = computed(() => Number(props.pagination?.total || 0));

const from = computed(() => {
  if (props.pagination?.from != null) return Number(props.pagination.from) || 0;
  if (!total.value) return 0;
  return (currentPage.value - 1) * perPage.value + 1;
});

const to = computed(() => {
  if (props.pagination?.to != null) return Number(props.pagination.to) || 0;
  if (!total.value) return 0;
  return Math.min(currentPage.value * perPage.value, total.value);
});

const visible = computed(() => total.value > 0 || lastPage.value > 1);

const pages = computed(() => {
  const maxPages = Math.max(3, props.maxVisiblePages);
  const totalPages = lastPage.value;
  const current = currentPage.value;

  if (totalPages <= maxPages + 2) {
    return Array.from({ length: totalPages }, (_, index) => ({
      key: `page-${index + 1}`,
      value: index + 1,
    }));
  }

  let start = Math.max(1, current - Math.floor(maxPages / 2));
  let end = Math.min(totalPages, start + maxPages - 1);
  start = Math.max(1, end - maxPages + 1);

  const result = [];
  const pushPage = (value) => result.push({ key: `page-${value}`, value });
  const pushEllipsis = (key) => result.push({ key, ellipsis: true });

  if (start > 1) pushPage(1);
  if (start > 2) pushEllipsis("start-ellipsis");

  for (let page = start; page <= end; page += 1) {
    pushPage(page);
  }

  if (end < totalPages - 1) pushEllipsis("end-ellipsis");
  if (end < totalPages) pushPage(totalPages);

  return result;
});

const emitPageChange = (page) => {
  const target = Math.min(Math.max(Number(page), 1), lastPage.value);
  if (target === currentPage.value || props.disabled) return;
  emit("page-change", target);
};

const emitPerPageChange = (event) => {
  const value = Number(event.target.value);
  if (!value || value === perPage.value || props.disabled) return;
  emit("per-page-change", value);
};
</script>
