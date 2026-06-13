<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-8 animate-fade-in-up">
    <!-- Header -->
    <div class="flex flex-col gap-1">
      <div class="flex items-center gap-2 text-sm text-text-secondary">
        <router-link
          to="/admin/dashboard"
          class="hover:text-primary transition-colors"
          >Dashboard</router-link
        >
        <span>/</span>
        <span class="text-text-main font-medium">Otoritas Tanda Tangan</span>
      </div>
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Otoritas Tanda Tangan
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Kelola tanda tangan digital pejabat untuk dokumen resmi
      </p>
    </div>

    <!-- Toolbar & Table -->
    <div
      class="flex flex-col bg-surface-light border border-border-light rounded-xl shadow-sm"
    >
      <div
        class="p-5 border-b border-border-light flex flex-col md:flex-row gap-4 items-center justify-between"
      >
        <div class="relative w-full md:max-w-md">
          <div
            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
          >
            <span class="material-symbols-outlined text-text-secondary"
              >search</span
            >
          </div>
          <input
            v-model="searchQuery"
            @input="debouncedSearch"
            type="text"
            placeholder="Cari nama dosen..."
            class="block w-full pl-10 pr-3 py-2.5 border border-border-light rounded-lg bg-background-light text-text-main placeholder-text-secondary focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm dark:bg-background"
          />
        </div>
        <button
          @click="openModal()"
          class="flex items-center gap-2 px-4 py-2.5 bg-primary hover:bg-blue-600 text-white rounded-lg font-medium text-sm shadow-sm shadow-blue-500/20 transition-all whitespace-nowrap"
        >
          <span class="material-symbols-outlined text-[20px]">add</span>
          Tambah Tanda Tangan
        </button>
      </div>

      <div v-if="loading" class="p-12 text-center">
        <div
          class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"
        ></div>
        <p class="text-text-secondary text-sm mt-3">Memuat data...</p>
      </div>

      <div v-else>
        <!-- Card Grid View -->
        <div
          v-if="items.length === 0"
          class="p-12 text-center text-text-secondary"
        >
          Tidak ada data tanda tangan
        </div>
        <div
          v-else
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-5"
        >
          <div
            v-for="item in items"
            :key="item.id"
            class="group bg-background-light dark:bg-background border border-border-light rounded-xl p-5 hover:shadow-md transition-all"
          >
            <!-- Dosen Info -->
            <div class="flex items-center gap-3 mb-4">
              <div
                class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold"
                :class="getAvatarColor(item.dosen?.nama)"
              >
                {{ getInitials(item.dosen?.nama) }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-text-main font-semibold text-sm truncate">
                  {{ getDosenName(item.dosen) }}
                </p>
                <p class="text-text-secondary text-xs">
                  NIP: {{ item.dosen?.nip || "-" }}
                </p>
              </div>
            </div>
            <!-- Signature Preview -->
            <div
              class="bg-white dark:bg-surface-light border border-border-light rounded-lg p-3 mb-4 flex items-center justify-center min-h-[100px]"
            >
              <img
                :src="item.ttd_url"
                alt="Tanda Tangan"
                class="max-h-[80px] max-w-full object-contain"
                @error="onImageError"
              />
            </div>
            <!-- Actions -->
            <div class="flex gap-2">
              <button
                @click="openModal(item)"
                class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg border border-border-light text-text-secondary hover:text-primary hover:border-primary/30 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
              >
                <span class="material-symbols-outlined text-[16px]">edit</span>
                Edit
              </button>
              <button
                @click="confirmDelete(item)"
                class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg border border-border-light text-text-secondary hover:text-red-500 hover:border-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
              >
                <span class="material-symbols-outlined text-[16px]"
                  >delete</span
                >
                Hapus
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <Teleport to="body">
    <Transition name="modal-fade">
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
        >
          <div class="p-6 border-b border-border-light">
            <h2 class="text-xl font-bold text-text-main">
              {{ isEditing ? "Edit Tanda Tangan" : "Tambah Tanda Tangan" }}
            </h2>
          </div>
          <form @submit.prevent="saveItem" class="p-6 space-y-5">
            <!-- Dosen Search -->
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Dosen <span class="text-red-500">*</span></label
              >
              <div class="relative">
                <input
                  v-model="dosenSearch"
                  @input="debouncedDosenSearch"
                  @focus="showDosenDropdown = true"
                  :disabled="isEditing"
                  placeholder="Cari nama/NIP dosen..."
                  class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-background-light text-text-main dark:bg-background disabled:opacity-60"
                />
                <div
                  v-if="selectedDosen && !isEditing"
                  class="absolute right-3 top-1/2 -translate-y-1/2"
                >
                  <button
                    type="button"
                    @click="clearDosen"
                    class="text-text-secondary hover:text-red-500"
                  >
                    <span class="material-symbols-outlined text-[18px]"
                      >close</span
                    >
                  </button>
                </div>
              </div>
              <div
                v-if="showDosenDropdown && dosenOptions.length > 0"
                class="mt-1 max-h-40 overflow-y-auto bg-white dark:bg-surface-light border border-border-light rounded-lg shadow-lg z-10 relative"
              >
                <button
                  v-for="d in dosenOptions"
                  :key="d.id"
                  type="button"
                  @click="selectDosen(d)"
                  class="w-full px-3 py-2 text-left text-sm hover:bg-sidebar-light/50 transition-colors"
                >
                  <p class="text-text-main font-medium">
                    {{ getDosenName(d) }}
                  </p>
                  <p class="text-text-secondary text-xs">
                    NIP: {{ d.nip || "-" }}
                  </p>
                </button>
              </div>
            </div>

            <!-- Input Mode Toggle -->
            <div>
              <label class="block text-sm font-medium text-text-main mb-2"
                >Metode Input</label
              >
              <div class="flex p-1 bg-sidebar-light rounded-lg w-fit">
                <button
                  type="button"
                  @click="inputMode = 'upload'"
                  :class="[
                    'px-4 py-2 text-xs font-medium rounded-md transition-all',
                    inputMode === 'upload'
                      ? 'bg-background-light text-primary shadow-sm dark:bg-surface'
                      : 'text-text-secondary hover:text-text-main',
                  ]"
                >
                  <span
                    class="material-symbols-outlined text-[16px] mr-1 align-middle"
                    >upload_file</span
                  >
                  Upload File
                </button>
                <button
                  type="button"
                  @click="inputMode = 'draw'"
                  :class="[
                    'px-4 py-2 text-xs font-medium rounded-md transition-all',
                    inputMode === 'draw'
                      ? 'bg-background-light text-primary shadow-sm dark:bg-surface'
                      : 'text-text-secondary hover:text-text-main',
                  ]"
                >
                  <span
                    class="material-symbols-outlined text-[16px] mr-1 align-middle"
                    >draw</span
                  >
                  Gambar
                </button>
              </div>
            </div>

            <!-- Upload Mode -->
            <div v-if="inputMode === 'upload'">
              <label class="block text-sm font-medium text-text-main mb-2"
                >Upload Tanda Tangan</label
              >
              <div
                class="border-2 border-dashed border-border-light rounded-lg p-6 text-center hover:border-primary/50 transition-colors cursor-pointer"
                @click="$refs.fileInput.click()"
                @dragover.prevent
                @drop.prevent="handleDrop"
              >
                <input
                  ref="fileInput"
                  type="file"
                  accept="image/png,image/jpeg,image/jpg"
                  class="hidden"
                  @change="handleFileSelect"
                />
                <span
                  class="material-symbols-outlined text-3xl text-text-secondary mb-2 block"
                  >cloud_upload</span
                >
                <p
                  v-if="!uploadFile && !previewUrl"
                  class="text-sm text-text-secondary"
                >
                  Klik atau drag & drop file gambar (PNG/JPG)
                </p>
                <div
                  v-else-if="previewUrl"
                  class="flex flex-col items-center gap-2"
                >
                  <img
                    :src="previewUrl"
                    alt="Preview"
                    class="max-h-[80px] object-contain"
                  />
                  <p class="text-xs text-primary font-medium">
                    {{ uploadFile?.name || "Tanda tangan saat ini" }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Draw Mode -->
            <div v-if="inputMode === 'draw'">
              <label class="block text-sm font-medium text-text-main mb-2"
                >Gambar Tanda Tangan</label
              >
              <div
                class="border border-border-light rounded-lg overflow-hidden bg-white"
              >
                <canvas
                  ref="signatureCanvas"
                  :width="canvasWidth"
                  :height="canvasHeight"
                  class="w-full cursor-crosshair touch-none"
                  @mousedown="startDraw"
                  @mousemove="draw"
                  @mouseup="endDraw"
                  @mouseleave="endDraw"
                  @touchstart.prevent="startDrawTouch"
                  @touchmove.prevent="drawTouch"
                  @touchend="endDraw"
                >
                </canvas>
              </div>
              <div class="flex items-center gap-3 mt-2">
                <button
                  type="button"
                  @click="clearCanvas"
                  class="flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-text-secondary hover:text-red-500 border border-border-light rounded-lg hover:border-red-300 transition-colors"
                >
                  <span class="material-symbols-outlined text-[14px]"
                    >delete_sweep</span
                  >
                  Hapus Gambar
                </button>
                <div class="flex items-center gap-2 ml-auto">
                  <label class="text-xs text-text-secondary">Ketebalan:</label>
                  <input
                    type="range"
                    v-model.number="penWidth"
                    min="1"
                    max="6"
                    class="w-20 accent-primary"
                  />
                  <span class="text-xs text-text-secondary font-mono w-4">{{
                    penWidth
                  }}</span>
                </div>
              </div>
            </div>

            <!-- Current Signature Preview (editing) -->
            <div
              v-if="isEditing && editingItem?.ttd_url"
              class="p-3 bg-sidebar-light/50 rounded-lg"
            >
              <p class="text-xs text-text-secondary mb-2">
                Tanda tangan saat ini:
              </p>
              <img
                :src="editingItem.ttd_url"
                alt="Current"
                class="max-h-[60px] object-contain"
              />
            </div>

            <div class="flex gap-3 pt-2">
              <button
                type="button"
                @click="closeModal"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="saving"
                class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50"
              >
                {{ saving ? "Menyimpan..." : "Simpan" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, watch } from "vue";
import { useToast } from "vue-toastification";
import Swal from "sweetalert2";
import adminService from "../../../services/adminService";

const toast = useToast();
const loading = ref(false);
const saving = ref(false);
const items = ref([]);
const showModal = ref(false);
const isEditing = ref(false);
const editingItem = ref(null);
const searchQuery = ref("");

// ========= Input Mode =========
const inputMode = ref("upload"); // 'upload' | 'draw'

// ========= Upload =========
const fileInput = ref(null);
const uploadFile = ref(null);
const previewUrl = ref("");

// ========= Draw Canvas =========
const signatureCanvas = ref(null);
const canvasWidth = 440;
const canvasHeight = 180;
const penWidth = ref(2);
let isDrawing = false;
let ctx = null;

// ========= Dosen Search =========
const dosenSearch = ref("");
const dosenOptions = ref([]);
const selectedDosen = ref(null);
const showDosenDropdown = ref(false);
const dosenId = ref("");
let dosenSearchTimeout = null;
let searchTimeout = null;

// ========= Fetch =========
const fetchData = async () => {
  loading.value = true;
  try {
    const response = await adminService.getTandaTangan({
      search: searchQuery.value,
    });
    if (response.success) items.value = response.data;
  } catch (e) {
    toast.error("Gagal memuat data");
  } finally {
    loading.value = false;
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(fetchData, 300);
};

// ========= Dosen Search =========
const searchDosenApi = async () => {
  if (!dosenSearch.value || dosenSearch.value.length < 2) {
    dosenOptions.value = [];
    return;
  }
  try {
    const response = await adminService.getDosen({
      search: dosenSearch.value,
      per_page: 10,
    });
    dosenOptions.value = response.data?.data || response.data || [];
  } catch (e) {
    console.error(e);
  }
};

const debouncedDosenSearch = () => {
  clearTimeout(dosenSearchTimeout);
  dosenSearchTimeout = setTimeout(searchDosenApi, 300);
};

const selectDosen = (d) => {
  selectedDosen.value = d;
  dosenId.value = d.id;
  dosenSearch.value = getDosenName(d);
  showDosenDropdown.value = false;
  dosenOptions.value = [];
};

const clearDosen = () => {
  selectedDosen.value = null;
  dosenId.value = "";
  dosenSearch.value = "";
};

// ========= File Handling =========
const handleFileSelect = (e) => {
  const file = e.target.files[0];
  if (file) {
    uploadFile.value = file;
    previewUrl.value = URL.createObjectURL(file);
  }
};

const handleDrop = (e) => {
  const file = e.dataTransfer.files[0];
  if (file && file.type.startsWith("image/")) {
    uploadFile.value = file;
    previewUrl.value = URL.createObjectURL(file);
  }
};

// ========= Canvas Drawing =========
const initCanvas = () => {
  nextTick(() => {
    const canvas = signatureCanvas.value;
    if (!canvas) return;
    ctx = canvas.getContext("2d");
    ctx.strokeStyle = "#000000";
    ctx.lineCap = "round";
    ctx.lineJoin = "round";
    ctx.lineWidth = penWidth.value;
    // Clear to white
    ctx.fillStyle = "#ffffff";
    ctx.fillRect(0, 0, canvasWidth, canvasHeight);
  });
};

const getCanvasPos = (e) => {
  const canvas = signatureCanvas.value;
  const rect = canvas.getBoundingClientRect();
  const scaleX = canvas.width / rect.width;
  const scaleY = canvas.height / rect.height;
  return {
    x: (e.clientX - rect.left) * scaleX,
    y: (e.clientY - rect.top) * scaleY,
  };
};

const startDraw = (e) => {
  isDrawing = true;
  ctx.lineWidth = penWidth.value;
  const pos = getCanvasPos(e);
  ctx.beginPath();
  ctx.moveTo(pos.x, pos.y);
};

const draw = (e) => {
  if (!isDrawing) return;
  const pos = getCanvasPos(e);
  ctx.lineTo(pos.x, pos.y);
  ctx.stroke();
};

const endDraw = () => {
  isDrawing = false;
};

// Touch events
const startDrawTouch = (e) => {
  const touch = e.touches[0];
  startDraw({ clientX: touch.clientX, clientY: touch.clientY });
};

const drawTouch = (e) => {
  const touch = e.touches[0];
  draw({ clientX: touch.clientX, clientY: touch.clientY });
};

const clearCanvas = () => {
  if (!ctx) return;
  ctx.fillStyle = "#ffffff";
  ctx.fillRect(0, 0, canvasWidth, canvasHeight);
};

const getCanvasBase64 = () => {
  const canvas = signatureCanvas.value;
  if (!canvas) return null;
  return canvas.toDataURL("image/png");
};

const isCanvasBlank = () => {
  const canvas = signatureCanvas.value;
  if (!canvas) return true;
  const blank = document.createElement("canvas");
  blank.width = canvas.width;
  blank.height = canvas.height;
  const blankCtx = blank.getContext("2d");
  blankCtx.fillStyle = "#ffffff";
  blankCtx.fillRect(0, 0, blank.width, blank.height);
  return canvas.toDataURL() === blank.toDataURL();
};

// ========= Modal =========
const openModal = (item = null) => {
  if (item) {
    isEditing.value = true;
    editingItem.value = item;
    dosenId.value = item.dosen_id;
    selectedDosen.value = item.dosen;
    dosenSearch.value = getDosenName(item.dosen);
  } else {
    isEditing.value = false;
    editingItem.value = null;
    clearDosen();
  }
  inputMode.value = "upload";
  uploadFile.value = null;
  previewUrl.value = "";
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  uploadFile.value = null;
  previewUrl.value = "";
};

// Watch for input mode to init canvas
watch(inputMode, (val) => {
  if (val === "draw") initCanvas();
});

// ========= Save =========
const saveItem = async () => {
  if (!dosenId.value) {
    toast.error("Pilih dosen terlebih dahulu");
    return;
  }

  // Validate: must have either upload or drawn something
  if (inputMode.value === "upload" && !uploadFile.value && !isEditing.value) {
    toast.error("Harap upload file tanda tangan");
    return;
  }
  if (inputMode.value === "draw" && isCanvasBlank()) {
    toast.error("Harap gambar tanda tangan terlebih dahulu");
    return;
  }

  saving.value = true;
  try {
    const formData = new FormData();
    formData.append("dosen_id", dosenId.value);

    if (inputMode.value === "upload" && uploadFile.value) {
      formData.append("ttd_file", uploadFile.value);
    } else if (inputMode.value === "draw" && !isCanvasBlank()) {
      formData.append("ttd_base64", getCanvasBase64());
    }

    if (isEditing.value) {
      await adminService.updateTandaTangan(editingItem.value.id, formData);
      toast.success("Tanda tangan berhasil diperbarui");
    } else {
      await adminService.createTandaTangan(formData);
      toast.success("Tanda tangan berhasil disimpan");
    }
    closeModal();
    fetchData();
  } catch (e) {
    toast.error(e.response?.data?.message || "Gagal menyimpan");
  } finally {
    saving.value = false;
  }
};

// ========= Delete =========
const confirmDelete = async (item) => {
  const result = await Swal.fire({
    title: "Hapus Tanda Tangan?",
    text: `Tanda tangan ${getDosenName(item.dosen)} akan dihapus`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Ya, Hapus!",
    cancelButtonText: "Batal",
  });
  if (result.isConfirmed) {
    try {
      await adminService.deleteTandaTangan(item.id);
      toast.success("Tanda tangan berhasil dihapus");
      fetchData();
    } catch (e) {
      toast.error(e.response?.data?.message || "Gagal menghapus");
    }
  }
};

// ========= Helpers =========
const getDosenName = (dosen) => {
  if (!dosen) return "-";
  const parts = [];
  if (dosen.gelar_depan) parts.push(dosen.gelar_depan);
  if (dosen.nama) parts.push(dosen.nama);
  if (dosen.gelar_belakang) parts.push(dosen.gelar_belakang);
  return parts.join(" ") || dosen.nama || "-";
};

const getInitials = (name) => {
  if (!name) return "?";
  return name
    .split(" ")
    .map((n) => n[0])
    .join("")
    .substring(0, 2)
    .toUpperCase();
};

const getAvatarColor = (name) => {
  const colors = [
    "bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400",
    "bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400",
    "bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400",
    "bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400",
  ];
  if (!name) return colors[0];
  return colors[name.charCodeAt(0) % colors.length];
};

const onImageError = (e) => {
  e.target.src =
    'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="60"><text x="50%" y="50%" text-anchor="middle" dy=".3em" fill="%23999" font-size="12">No Image</text></svg>';
};

// ========= Init =========
onMounted(fetchData);
</script>
