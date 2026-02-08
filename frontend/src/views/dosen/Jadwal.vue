<template>
  <div class="flex flex-col gap-6 animate-fade-in">
    <div
      class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4"
    >
      <div class="flex flex-col gap-1">
        <h1 class="text-3xl font-bold tracking-tight text-text-main">
          Jadwal Ujian & Seminar
        </h1>
        <p class="text-text-secondary text-base">
          Lihat jadwal ujian skripsi dan seminar proposal mahasiswa Anda.
        </p>
      </div>
      <div class="flex gap-2">
        <button
          class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors"
        >
          <span class="material-symbols-outlined text-[20px]">add</span>
          Request Jadwal
        </button>
      </div>
    </div>

    <!-- Tabs -->
    <div class="flex border-b border-border-light mb-2 gap-8 overflow-x-auto">
      <button
        class="flex flex-col items-center justify-center border-b-[3px] border-primary text-primary pb-3 pt-2 px-2 whitespace-nowrap font-bold text-sm"
      >
        Upcoming Exams (3)
      </button>
      <button
        class="flex flex-col items-center justify-center border-b-[3px] border-transparent text-text-secondary hover:text-text-main pb-3 pt-2 px-2 whitespace-nowrap transition-colors font-bold text-sm"
      >
        Exam History
      </button>
    </div>

    <!-- Table -->
    <div
      class="bg-surface-light border border-border-light rounded-xl overflow-hidden shadow-sm"
    >
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr
              class="bg-sidebar-light border-b border-border-light text-text-secondary"
            >
              <th
                class="px-6 py-4 text-xs font-semibold uppercase tracking-wider"
              >
                Mahasiswa
              </th>
              <th
                class="px-6 py-4 text-xs font-semibold uppercase tracking-wider"
              >
                Judul Skripsi
              </th>
              <th
                class="px-6 py-4 text-xs font-semibold uppercase tracking-wider"
              >
                Waktu
              </th>
              <th
                class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-center"
              >
                Status
              </th>
              <th
                class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-right"
              >
                Aksi
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <!-- Row 1 -->
            <tr class="group hover:bg-sidebar-light transition-colors">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="size-8 rounded-full bg-gray-200 dark:bg-gray-700 bg-center bg-cover"
                    style="
                      background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuC4E2z0-0vtJDUjHUFR6dXGsz8ZSAG254FQA2JORCupckx0QqIFod8hSgm0Gk7LqVgc-ljEWZbwY_Ku8eNx2E6m06QwNcF9FCw6b328UO6gSun3Z6zRoSCv4-BjHkyI7Jf0pqys1lflKVq-kIASFrqlgs_ztoSQFmzsbu34wiIutD6DwzuhbNkv2ENqk2CjFUK3Q3rx8FKAHuZ2yL9i6gq7QQskWvLf1i2UOdRUdu3By5EmfIYN_MgTdMOz__Z7Y4xZY1FFst8xwsgC&quot;);
                    "
                  ></div>
                  <div>
                    <p class="text-sm font-medium text-text-main">
                      Budi Santoso
                    </p>
                    <p class="text-xs text-text-secondary">NIM: 12345678</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 max-w-[300px]">
                <p class="text-sm text-text-main font-medium line-clamp-2">
                  Implementation of AI in Predicting Rainfall Patterns
                </p>
              </td>
              <td class="px-6 py-4">
                <div class="flex flex-col">
                  <span class="text-text-main text-sm font-medium"
                    >Oct 24, 2023</span
                  >
                  <span class="text-text-secondary text-xs"
                    >09:00 AM - 11:00 AM</span
                  >
                  <span
                    class="text-text-secondary text-xs mt-1 flex items-center gap-1"
                  >
                    <span class="material-symbols-outlined text-[14px]"
                      >location_on</span
                    >
                    Room 304, Build. A
                  </span>
                </div>
              </td>
              <td class="px-6 py-4 text-center">
                <span
                  class="inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-900/30 px-2.5 py-0.5 text-xs font-semibold text-blue-700 dark:text-blue-300"
                >
                  Upcoming
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <button
                  class="text-primary hover:text-blue-700 text-sm font-medium"
                >
                  Detail
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
