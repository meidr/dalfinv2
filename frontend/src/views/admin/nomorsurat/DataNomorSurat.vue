<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-8 animate-fade-in-up">
    <div class="flex flex-col gap-1">
      <div class="flex items-center gap-2 text-sm text-text-secondary">
        <router-link to="/admin/dashboard" class="hover:text-primary transition-colors">
          Dashboard
        </router-link>
        <span>/</span>
        <span class="text-text-main font-medium">Nomor Surat</span>
      </div>
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Nomor Surat
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Atur template nomor surat dokumen resmi
      </p>
    </div>

    <div class="bg-surface-light border border-border-light rounded-xl shadow-sm overflow-hidden">
      <div class="p-5 border-b border-border-light flex items-center justify-between gap-4">
        <div>
          <h2 class="text-text-main text-lg font-semibold">Template Dokumen</h2>
          <p class="text-text-secondary text-sm mt-1">
            Jenis surat bersifat tetap dan hanya formatnya yang dapat diubah.
          </p>
        </div>
        <button
          @click="fetchData"
          class="inline-flex items-center gap-2 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:text-primary hover:bg-background-light transition-colors"
        >
          <span class="material-symbols-outlined text-[20px]">refresh</span>
          <span class="hidden sm:inline">Muat Ulang</span>
        </button>
      </div>

      <div v-if="loading" class="p-12 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div>
        <p class="text-text-secondary text-sm mt-3">Memuat data...</p>
      </div>

      <DataTableScroll v-else>
        <table class="w-full text-left text-sm">
          <thead class="bg-sidebar-light/50 text-text-secondary border-b border-border-light">
            <tr>
              <th class="px-6 py-4 font-semibold">Dokumen</th>
              <th class="px-6 py-4 font-semibold">Level</th>
              <th class="px-6 py-4 font-semibold">Digit</th>
              <th class="px-6 py-4 font-semibold">Template</th>
              <th class="px-6 py-4 text-right font-semibold">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-for="item in items" :key="item.id" class="hover:bg-sidebar-light/30 transition-colors">
              <td class="px-6 py-4">
                <div class="font-semibold text-text-main">{{ item.nama }}</div>
                <div class="text-xs text-text-secondary mt-1">{{ item.key }}</div>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex px-2.5 py-1 rounded-full bg-blue-50 text-primary dark:bg-blue-900/20 dark:text-blue-400 text-xs font-semibold capitalize">
                  {{ item.level }}
                </span>
              </td>
              <td class="px-6 py-4 text-text-secondary">{{ item.digit_urut }}</td>
              <td class="px-6 py-4">
                <code class="block max-w-xl whitespace-normal break-words text-text-main bg-background-light border border-border-light rounded-lg px-3 py-2 dark:bg-background">
                  {{ item.template }}
                </code>
              </td>
              <td class="px-6 py-4 text-right">
                <button
                  @click="openModal(item)"
                  class="inline-flex items-center gap-2 px-3 py-2 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                >
                  <span class="material-symbols-outlined text-[20px]">edit</span>
                  Edit
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </DataTableScroll>
    </div>

    <Transition name="modal-fade">
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-2xl">
          <div class="p-6 border-b border-border-light flex items-start justify-between gap-4">
            <div>
              <h2 class="text-xl font-bold text-text-main">Edit Template</h2>
              <p class="text-sm text-text-secondary mt-1">{{ form.nama }}</p>
            </div>
            <button
              @click="closeModal"
              class="p-2 text-text-secondary hover:text-text-main hover:bg-background-light rounded-lg transition-colors"
            >
              <span class="material-symbols-outlined text-[22px]">close</span>
            </button>
          </div>

          <form @submit.prevent="saveItem" class="p-6 space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-text-main mb-1">Level Penomoran</label>
                <select
                  v-model="form.level"
                  class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-background-light text-text-main dark:bg-background"
                >
                  <option value="prodi">Prodi</option>
                  <option value="fakultas">Fakultas</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-text-main mb-1">Digit Nomor Urut</label>
                <input
                  v-model.number="form.digit_urut"
                  type="number"
                  min="1"
                  max="10"
                  class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-background-light text-text-main dark:bg-background"
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-text-main mb-1">
                Template <span class="text-red-500">*</span>
              </label>
              <textarea
                ref="templateInput"
                v-model="form.template"
                rows="3"
                required
                class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-background-light text-text-main dark:bg-background font-mono text-sm resize-y"
              ></textarea>
              <div class="flex flex-wrap gap-2 mt-3">
                <button
                  v-for="token in tokens"
                  :key="token"
                  type="button"
                  @click="insertToken(token)"
                  class="px-3 py-1.5 rounded-lg border border-border-light text-xs font-semibold text-text-secondary hover:text-primary hover:border-primary/50 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                >
                  {{ token }}
                </button>
              </div>
            </div>

            <div class="rounded-lg border border-border-light bg-background-light dark:bg-background p-4">
              <div class="text-xs font-semibold uppercase tracking-wider text-text-secondary mb-2">
                Preview
              </div>
              <code class="block text-text-main whitespace-normal break-words">
                {{ preview }}
              </code>
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
  </div>
</template>

<script setup>
import { computed, nextTick, onMounted, reactive, ref } from "vue";
import { useToast } from "vue-toastification";
import adminService from "../../../services/adminService";

const toast = useToast();
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);
const items = ref([]);
const tokens = ref([
  "{nomor_urut}",
  "{Fakultas_kode}",
  "{prodi_kode}",
  "{prodi_alias}",
  "{bulan}",
  "{tahun}",
]);
const templateInput = ref(null);
const currentId = ref(null);

const form = reactive({
  nama: "",
  level: "prodi",
  template: "",
  digit_urut: 3,
});

const preview = computed(() => {
  const nomor = String(5).padStart(Number(form.digit_urut) || 1, "0");
  return (form.template || "")
    .replaceAll("{nomor_urut}", nomor)
    .replaceAll("{Fakultas_kode}", "FT")
    .replaceAll("{prodi_kode}", "86208")
    .replaceAll("{prodi_alias}", "PAI")
    .replaceAll("{bulan}", "05")
    .replaceAll("{tahun}", "2026");
});

const fetchData = async () => {
  loading.value = true;
  try {
    const response = await adminService.getNomorSuratTemplates();
    if (response.success) {
      items.value = response.data || [];
      if (response.tokens?.length) tokens.value = response.tokens;
    }
  } catch (error) {
    toast.error(error.response?.data?.message || "Gagal memuat template nomor surat");
  } finally {
    loading.value = false;
  }
};

const openModal = (item) => {
  currentId.value = item.id;
  form.nama = item.nama;
  form.level = item.level || "prodi";
  form.template = item.template || "";
  form.digit_urut = item.digit_urut || 3;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const insertToken = async (token) => {
  const input = templateInput.value;
  if (!input) {
    form.template += token;
    return;
  }

  const start = input.selectionStart ?? form.template.length;
  const end = input.selectionEnd ?? form.template.length;
  form.template = `${form.template.slice(0, start)}${token}${form.template.slice(end)}`;
  await nextTick();
  input.focus();
  input.setSelectionRange(start + token.length, start + token.length);
};

const saveItem = async () => {
  saving.value = true;
  try {
    await adminService.updateNomorSuratTemplate(currentId.value, {
      level: form.level,
      template: form.template,
      digit_urut: form.digit_urut,
    });
    toast.success("Template nomor surat berhasil diperbarui");
    closeModal();
    await fetchData();
  } catch (error) {
    const message =
      error.response?.data?.errors?.template?.[0] ||
      error.response?.data?.message ||
      "Gagal menyimpan template nomor surat";
    toast.error(message);
  } finally {
    saving.value = false;
  }
};

onMounted(fetchData);
</script>
