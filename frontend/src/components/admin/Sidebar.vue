<template>
  <aside
    :class="[
      'flex flex-col border-r border-border-light bg-sidebar-light shrink-0 overflow-y-auto h-full fixed lg:static z-20 transition-all duration-300 scrollbar-elegant',
      isSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
      isCollapsed ? 'w-20' : 'w-72',
    ]"
  >
    <div class="flex flex-col gap-4 p-4 min-h-full">
      <div
        :class="[
          'flex items-center gap-3 px-2 py-4 transition-all',
          isCollapsed ? 'justify-center' : '',
        ]"
      >
        <div
          class="bg-center bg-no-repeat bg-cover rounded-full size-10 aspect-square shadow-sm shrink-0"
          data-alt="University Logo"
          style="
            background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuCgBfrJ6gLv5MJ7k8FdXthRiv-DjdZjoh11_Q5GcfeRcHUj34lwbcA9lJxjB77qnn-6HAtzKQf3srfXdjw0GB5omKY9g0ErqAxTVENQB_cDMbhMtegaZcX9jwWS7_mlq_Kv52jrHsVmzP-8gY9eZeweWwyA5r773ACDkhLoy-MMWl-egpXdyxDw-0t_NMfxKlDlcUinaHvgi0vrWpnvzw8oKwv0b3hRXC3u_F1ymAYEugEHXBuQcDCnLYSOFgyZLci2eE3TaajTzSrw&quot;);
          "
        ></div>
        <div
          v-show="!isCollapsed"
          class="flex flex-col overflow-hidden whitespace-nowrap transition-all duration-300"
        >
          <h1
            class="text-text-main text-lg font-bold leading-none tracking-tight"
          >
            SIS Admin
          </h1>
          <p class="text-text-secondary text-xs font-normal mt-1">
            Thesis Management
          </p>
        </div>
        <button
          v-show="!isCollapsed"
          @click="toggleCollapse"
          class="ml-auto text-text-secondary hover:text-primary transition-colors p-1 rounded-md hover:bg-sidebar-light lg:block hidden"
        >
          <span class="material-symbols-outlined text-[20px]">first_page</span>
        </button>
      </div>

      <div v-show="isCollapsed" class="flex justify-center lg:block hidden">
        <button
          @click="toggleCollapse"
          class="text-text-secondary hover:text-primary transition-colors p-1 rounded-md hover:bg-sidebar-light"
        >
          <span class="material-symbols-outlined text-[20px]">last_page</span>
        </button>
      </div>
      <nav class="flex flex-col gap-1 mt-2">
        <router-link
          to="/admin/dashboard"
          :class="[
            'flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary text-white shadow-md shadow-primary/20 hover:opacity-90 transition-all font-medium group cursor-pointer',
            isCollapsed ? 'justify-center' : '',
          ]"
        >
          <span class="material-symbols-outlined text-[22px]">dashboard</span>
          <p
            v-show="!isCollapsed"
            class="text-sm font-semibold whitespace-nowrap"
          >
            Dashboard
          </p>
        </router-link>
        <div class="flex flex-col gap-1 mt-4">
          <div
            :class="[
              'flex items-center gap-3 px-3 py-2 text-text-secondary',
              isCollapsed ? 'justify-center' : '',
            ]"
          >
            <p
              v-show="!isCollapsed"
              class="text-[10px] font-bold uppercase tracking-widest whitespace-nowrap"
            >
              Manajemen Skripsi
            </p>
            <span
              v-show="isCollapsed"
              class="w-4 h-0.5 bg-text-secondary/20 rounded"
            ></span>
          </div>
          <div class="flex flex-col gap-0.5">
            <router-link
              to="/admin/skripsi"
              active-class="bg-blue-50 text-primary dark:bg-blue-900/20 dark:text-blue-400"
              :class="[
                'flex items-center gap-3 px-3 py-2 rounded-lg text-text-secondary hover:text-primary hover:bg-surface-light transition-all group cursor-pointer',
                isCollapsed ? 'justify-center' : '',
              ]"
            >
              <span
                class="material-symbols-outlined text-[20px] group-hover:scale-110 transition-transform"
                >folder_open</span
              >
              <p
                v-show="!isCollapsed"
                class="text-sm font-medium whitespace-nowrap"
              >
                Data Skripsi
              </p>
            </router-link>
            <router-link
              to="/admin/pembimbing"
              active-class="bg-blue-50 text-primary dark:bg-blue-900/20 dark:text-blue-400"
              :class="[
                'flex items-center gap-3 px-3 py-2 rounded-lg text-text-secondary hover:text-primary hover:bg-surface-light transition-all group cursor-pointer',
                isCollapsed ? 'justify-center' : '',
              ]"
            >
              <span
                class="material-symbols-outlined text-[20px] group-hover:scale-110 transition-transform"
                >supervisor_account</span
              >
              <p
                v-show="!isCollapsed"
                class="text-sm font-medium whitespace-nowrap"
              >
                Pembimbing
              </p>
            </router-link>
            <router-link
              to="/admin/sktugas"
              active-class="bg-blue-50 text-primary dark:bg-blue-900/20 dark:text-blue-400"
              :class="[
                'flex items-center gap-3 px-3 py-2 rounded-lg text-text-secondary hover:text-primary hover:bg-surface-light transition-all group cursor-pointer',
                isCollapsed ? 'justify-center' : '',
              ]"
            >
              <span
                class="material-symbols-outlined text-[20px] group-hover:scale-110 transition-transform"
                >assignment_turned_in</span
              >
              <p
                v-show="!isCollapsed"
                class="text-sm font-medium whitespace-nowrap"
              >
                SK Tugas
              </p>
            </router-link>
            <router-link
              to="/admin/seminar"
              active-class="bg-blue-50 text-primary dark:bg-blue-900/20 dark:text-blue-400"
              :class="[
                'flex items-center gap-3 px-3 py-2 rounded-lg text-text-secondary hover:text-primary hover:bg-surface-light transition-all group cursor-pointer',
                isCollapsed ? 'justify-center' : '',
              ]"
            >
              <span
                class="material-symbols-outlined text-[20px] group-hover:scale-110 transition-transform"
                >present_to_all</span
              >
              <p
                v-show="!isCollapsed"
                class="text-sm font-medium whitespace-nowrap"
              >
                Seminar Proposal
              </p>
            </router-link>
            <router-link
              to="/admin/bimbingan"
              active-class="bg-blue-50 text-primary dark:bg-blue-900/20 dark:text-blue-400"
              :class="[
                'flex items-center gap-3 px-3 py-2 rounded-lg text-text-secondary hover:text-primary hover:bg-surface-light transition-all group cursor-pointer',
                isCollapsed ? 'justify-center' : '',
              ]"
            >
              <span
                class="material-symbols-outlined text-[20px] group-hover:scale-110 transition-transform"
                >cast_for_education</span
              >
              <p
                v-show="!isCollapsed"
                class="text-sm font-medium whitespace-nowrap"
              >
                Bimbingan
              </p>
            </router-link>
            <router-link
              to="/admin/notabimbingan"
              active-class="bg-blue-50 text-primary dark:bg-blue-900/20 dark:text-blue-400"
              :class="[
                'flex items-center gap-3 px-3 py-2 rounded-lg text-text-secondary hover:text-primary hover:bg-surface-light transition-all group cursor-pointer',
                isCollapsed ? 'justify-center' : '',
              ]"
            >
              <span
                class="material-symbols-outlined text-[20px] group-hover:scale-110 transition-transform"
                >sticky_note_2</span
              >
              <p
                v-show="!isCollapsed"
                class="text-sm font-medium whitespace-nowrap"
              >
                Nota Bimbingan
              </p>
            </router-link>
            <router-link
              to="/admin/ujian"
              active-class="bg-blue-50 text-primary dark:bg-blue-900/20 dark:text-blue-400"
              :class="[
                'flex items-center gap-3 px-3 py-2 rounded-lg text-text-secondary hover:text-primary hover:bg-surface-light transition-all group cursor-pointer',
                isCollapsed ? 'justify-center' : '',
              ]"
            >
              <span
                class="material-symbols-outlined text-[20px] group-hover:scale-110 transition-transform"
                >history_edu</span
              >
              <p
                v-show="!isCollapsed"
                class="text-sm font-medium whitespace-nowrap"
              >
                Ujian Skripsi
              </p>
            </router-link>
            <router-link
              to="/admin/beritaacara"
              active-class="bg-blue-50 text-primary dark:bg-blue-900/20 dark:text-blue-400"
              :class="[
                'flex items-center gap-3 px-3 py-2 rounded-lg text-text-secondary hover:text-primary hover:bg-surface-light transition-all group cursor-pointer',
                isCollapsed ? 'justify-center' : '',
              ]"
            >
              <span
                class="material-symbols-outlined text-[20px] group-hover:scale-110 transition-transform"
                >description</span
              >
              <p
                v-show="!isCollapsed"
                class="text-sm font-medium whitespace-nowrap"
              >
                Berita Acara
              </p>
            </router-link>
            <router-link
              to="/admin/skyudisium"
              active-class="bg-blue-50 text-primary dark:bg-blue-900/20 dark:text-blue-400"
              :class="[
                'flex items-center gap-3 px-3 py-2 rounded-lg text-text-secondary hover:text-primary hover:bg-surface-light transition-all group cursor-pointer',
                isCollapsed ? 'justify-center' : '',
              ]"
            >
              <span
                class="material-symbols-outlined text-[20px] group-hover:scale-110 transition-transform"
                >workspace_premium</span
              >
              <p
                v-show="!isCollapsed"
                class="text-sm font-medium whitespace-nowrap"
              >
                SK Yudisium
              </p>
            </router-link>
          </div>
        </div>
        <div class="mt-4 flex flex-col gap-1">
          <!-- Administration Section -->
          <div class="px-4 mt-2 mb-2">
            <p
              v-show="!isCollapsed"
              class="text-[10px] font-bold text-text-secondary uppercase tracking-wider transition-opacity duration-300"
            >
              Administrasi
            </p>
            <div
              v-show="isCollapsed"
              class="h-px w-full bg-border-light my-2"
            ></div>
          </div>

          <!-- Master Data Dropdown -->
          <div class="flex flex-col gap-1">
            <button
              @click="toggleMasterData"
              :class="[
                'flex items-center gap-3 px-3 py-2 rounded-lg text-text-secondary hover:text-primary hover:bg-surface-light transition-all group w-full text-left',
                isCollapsed ? 'justify-center' : '',
                isMasterDataOpen ? 'bg-surface-light text-primary' : '',
              ]"
            >
              <span class="material-symbols-outlined text-[20px]"
                >database</span
              >
              <p
                v-show="!isCollapsed"
                class="text-sm font-medium whitespace-nowrap flex-1"
              >
                Master Data
              </p>
              <span
                v-show="!isCollapsed"
                class="material-symbols-outlined text-[18px] transition-transform duration-300"
                :class="isMasterDataOpen ? 'rotate-180' : ''"
                >expand_more</span
              >
            </button>
            <!-- Dropdown Items -->
            <div
              v-show="isMasterDataOpen && !isCollapsed"
              class="flex flex-col gap-1 pl-10 pr-2 overflow-hidden transition-all duration-300 ease-in-out"
            >
              <router-link
                to="/admin/master/mahasiswa"
                active-class="text-primary bg-blue-50 dark:bg-blue-900/20"
                class="flex items-center gap-2 px-3 py-2 rounded-lg text-text-secondary hover:text-primary hover:bg-surface-light transition-all text-sm"
              >
                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                Mahasiswa
              </router-link>
              <router-link
                to="/admin/master/dosen"
                active-class="text-primary bg-blue-50 dark:bg-blue-900/20"
                class="flex items-center gap-2 px-3 py-2 rounded-lg text-text-secondary hover:text-primary hover:bg-surface-light transition-all text-sm"
              >
                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                Dosen
              </router-link>
            </div>
          </div>

          <router-link
            to="/admin/pengguna"
            active-class="bg-blue-50 text-primary dark:bg-blue-900/20 dark:text-blue-400"
            :class="[
              'flex items-center gap-3 px-3 py-2 rounded-lg text-text-secondary hover:text-primary hover:bg-surface-light transition-all group cursor-pointer',
              isCollapsed ? 'justify-center' : '',
            ]"
          >
            <span class="material-symbols-outlined text-[20px]">group</span>
            <p
              v-show="!isCollapsed"
              class="text-sm font-medium whitespace-nowrap"
            >
              Manajemen User
            </p>
          </router-link>

          <router-link
            to="/admin/profil"
            active-class="bg-blue-50 text-primary dark:bg-blue-900/20 dark:text-blue-400"
            :class="[
              'flex items-center gap-3 px-3 py-2 rounded-lg text-text-secondary hover:text-primary hover:bg-surface-light transition-all group cursor-pointer',
              isCollapsed ? 'justify-center' : '',
            ]"
          >
            <span class="material-symbols-outlined text-[20px]">person</span>
            <p
              v-show="!isCollapsed"
              class="text-sm font-medium whitespace-nowrap"
            >
              Profil Saya
            </p>
          </router-link>
        </div>
      </nav>
      <div
        class="mt-auto flex flex-col gap-1 pt-4 border-t border-border-light"
      >
        <button
          :class="[
            'flex items-center gap-3 px-3 py-2.5 rounded-lg text-red-600 hover:bg-red-50 transition-colors w-full text-left group',
            isCollapsed ? 'justify-center' : '',
          ]"
        >
          <span
            class="material-symbols-outlined group-hover:translate-x-1 transition-transform"
            >logout</span
          >
          <p
            v-show="!isCollapsed"
            class="text-sm font-semibold whitespace-nowrap"
          >
            Log Out
          </p>
        </button>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
  isSidebarOpen: {
    type: Boolean,
    default: false,
  },
});

const isCollapsed = ref(false);
const isMasterDataOpen = ref(false);

const toggleCollapse = () => {
  isCollapsed.value = !isCollapsed.value;
  if (isCollapsed.value) {
    isMasterDataOpen.value = false;
  }
};

const toggleMasterData = () => {
  if (isCollapsed.value) {
    isCollapsed.value = false;
    setTimeout(() => {
      isMasterDataOpen.value = true;
    }, 150);
  } else {
    isMasterDataOpen.value = !isMasterDataOpen.value;
  }
};
</script>
