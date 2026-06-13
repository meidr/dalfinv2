const fs = require('fs');
const filePath = 'frontend/src/views/admin/skripsi/DataSkripsi.vue';
let content = fs.readFileSync(filePath, 'utf8');

// Normalize everything to \n for easier matching
content = content.replace(/\r\n/g, '\n');

// 1. Bulk Buttons
content = content.replace(
  '<button\n            @click="openAddModal"',
  `<Transition name="fade">
            <div v-if="selectedItems.length > 0" class="flex items-center gap-2">
              <span class="text-sm text-text-secondary font-medium whitespace-nowrap mr-2">
                {{ selectedItems.length }} dipilih
              </span>
              <button
                @click="bulkApproveStatus"
                class="inline-flex items-center gap-1.5 px-3 py-2.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors whitespace-nowrap shadow-sm shadow-green-500/20"
              >
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                Setujui Terpilih
              </button>
              <button
                @click="bulkRejectStatus"
                class="inline-flex items-center gap-1.5 px-3 py-2.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors whitespace-nowrap shadow-sm shadow-red-500/20"
              >
                <span class="material-symbols-outlined text-[18px]">cancel</span>
                Tolak Terpilih
              </button>
            </div>
          </Transition>
          <button
            @click="openAddModal"`
);

// 2. Checkbox Header
content = content.replace(
  `            <tr>\n              <th\n                class="px-6 py-4 cursor-pointer hover:text-primary transition-colors select-none group"\n                @click="handleSort('mahasiswa_nama')"`,
  `            <tr>
              <th class="px-4 py-4 w-10">
                <input
                  v-if="hasPengajuanItems"
                  type="checkbox"
                  :checked="isAllSelected"
                  :indeterminate="isIndeterminate"
                  @change="toggleSelectAll"
                  class="size-4 rounded border-border-light text-primary focus:ring-primary cursor-pointer accent-primary"
                />
              </th>
              <th
                class="px-6 py-4 cursor-pointer hover:text-primary transition-colors select-none group"
                @click="handleSort('mahasiswa_nama')"`
);

// 3. Colspan 8 -> 9
content = content.replace(
  `colspan="8"`,
  `colspan="9"`
);

// 4. Row Checkbox
content = content.replace(
  `<tr\n              v-for="item in skripsiList"\n              :key="item.id"\n              class="group hover:bg-sidebar-light/30 transition-colors"\n            >\n              <td class="px-6 py-4">`,
  `<tr
              v-for="item in skripsiList"
              :key="item.id"
              class="group hover:bg-sidebar-light/30 transition-colors"
              :class="{ 'bg-primary/5': selectedItems.includes(item.id) }"
            >
              <td class="px-4 py-4">
                <input
                  v-if="item.status === 'pengajuan'"
                  type="checkbox"
                  :checked="selectedItems.includes(item.id)"
                  @change="toggleSelect(item.id)"
                  class="size-4 rounded border-border-light text-primary focus:ring-primary cursor-pointer accent-primary"
                />
              </td>
              <td class="px-6 py-4">`
);

// 5. Actions
content = content.replace(
  `<button\n                    @click="viewDetail(item.id)"\n                    class="p-2 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"\n                    title="Lihat Detail"\n                  >`,
  `<button
                    v-if="item.status === 'pengajuan'"
                    @click="updateStatusDirect(item, 'disetujui')"
                    class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors"
                    title="Setujui"
                  >
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                  </button>
                  <button
                    v-if="item.status === 'pengajuan'"
                    @click="updateStatusDirect(item, 'ditolak')"
                    class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                    title="Tolak"
                  >
                    <span class="material-symbols-outlined text-[20px]">cancel</span>
                  </button>
                  <button
                    @click="viewDetail(item.id)"
                    class="p-2 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                    title="Lihat Detail"
                  >`
);

// 6. Logic injection
const logicString = `
const selectedItems = ref([]);

const hasPengajuanItems = computed(() => {
  return skripsiList.value.some(item => item.status === 'pengajuan');
});

const isAllSelected = computed(() => {
  const pengajuanItems = skripsiList.value.filter(item => item.status === 'pengajuan');
  return pengajuanItems.length > 0 && pengajuanItems.every(item => selectedItems.value.includes(item.id));
});

const isIndeterminate = computed(() => {
  return selectedItems.value.length > 0 && !isAllSelected.value;
});

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    selectedItems.value = [];
  } else {
    selectedItems.value = skripsiList.value.filter(item => item.status === 'pengajuan').map(item => item.id);
  }
};

const toggleSelect = (id) => {
  const idx = selectedItems.value.indexOf(id);
  if (idx >= 0) {
    selectedItems.value.splice(idx, 1);
  } else {
    selectedItems.value.push(id);
  }
};

const updateStatusDirect = async (item, newStatus) => {
  if (!confirm(\`Apakah Anda yakin ingin \${newStatus === 'disetujui' ? 'menyetujui' : 'menolak'} pengajuan ini?\`)) return;
  try {
    loading.value = true;
    const formData = new FormData();
    formData.append("_method", "PUT");
    formData.append("status", newStatus);
    formData.append("alasan", \`Status diubah menjadi \${newStatus} secara langsung\`);
    const response = await adminService.updateSkripsi(item.id, formData);
    if (response && response.message) {
      alert(response.message);
    }
    fetchSkripsi();
  } catch (error) {
    console.error("Failed to update status:", error);
    alert("Gagal mengupdate status: " + (error.response?.data?.message || error.message));
    loading.value = false;
  }
};

const bulkApproveStatus = async () => {
  if (!confirm(\`Setujui \${selectedItems.value.length} pengajuan skripsi terpilih?\`)) return;
  try {
    loading.value = true;
    for (const id of selectedItems.value) {
      const formData = new FormData();
      formData.append("_method", "PUT");
      formData.append("status", "disetujui");
      formData.append("alasan", "Pengajuan disetujui secara massal");
      await adminService.updateSkripsi(id, formData);
    }
    selectedItems.value = [];
    fetchSkripsi();
  } catch (error) {
    console.error("Failed to bulk update status:", error);
    alert("Gagal menyetujui pengajuan secara massal.");
    loading.value = false;
  }
};

const bulkRejectStatus = async () => {
  if (!confirm(\`Tolak \${selectedItems.value.length} pengajuan skripsi terpilih?\`)) return;
  try {
    loading.value = true;
    for (const id of selectedItems.value) {
      const formData = new FormData();
      formData.append("_method", "PUT");
      formData.append("status", "ditolak");
      formData.append("alasan", "Pengajuan ditolak secara massal");
      await adminService.updateSkripsi(id, formData);
    }
    selectedItems.value = [];
    fetchSkripsi();
  } catch (error) {
    console.error("Failed to bulk update status:", error);
    alert("Gagal menolak pengajuan secara massal.");
    loading.value = false;
  }
};
`;

content = content.replace(
  'const skripsiList = ref([]);',
  'const skripsiList = ref([]);' + logicString
);

// 7. Labels
content = content.replace(
  `const getStatusClass = (status) => {\n  const classes = {\n    proposal: "bg-yellow-50 text-yellow-600 border border-yellow-100",`,
  `const getStatusClass = (status) => {\n  const classes = {\n    pengajuan: "bg-gray-50 text-gray-600 border border-gray-100",\n    disetujui: "bg-green-100 text-green-700 border border-green-200",\n    ditolak: "bg-red-100 text-red-700 border border-red-200",\n    proposal: "bg-yellow-50 text-yellow-600 border border-yellow-100",`
);

content = content.replace(
  `const getStatusDot = (status) => {\n  const dots = {\n    proposal: "bg-yellow-600",`,
  `const getStatusDot = (status) => {\n  const dots = {\n    pengajuan: "bg-gray-500",\n    disetujui: "bg-green-600",\n    ditolak: "bg-red-600",\n    proposal: "bg-yellow-600",`
);

content = content.replace(
  `const getStatusLabel = (status) => {\n  const labels = {\n    proposal: "Proposal",`,
  `const getStatusLabel = (status) => {\n  const labels = {\n    pengajuan: "Pengajuan",\n    disetujui: "Disetujui",\n    ditolak: "Ditolak",\n    proposal: "Proposal",`
);

fs.writeFileSync(filePath, content, 'utf8');
console.log('Done modifying DataSkripsi.vue correctly!');
