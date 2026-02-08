<template>
  <div class="flex flex-col gap-6 animate-fade-in">
    <div
      class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4"
    >
      <div class="flex flex-col gap-1">
        <h1 class="text-3xl font-bold tracking-tight text-text-main">
          Mahasiswa Bimbingan
        </h1>
        <p class="text-text-secondary text-base">
          Kelola progres dan status skripsi mahasiswa bimbingan Anda.
        </p>
      </div>
      <div class="flex gap-2">
        <button
          class="flex items-center gap-2 bg-surface-light border border-border-light px-4 py-2 rounded-lg text-sm font-medium hover:bg-sidebar-light transition-colors text-text-main"
        >
          <span class="material-symbols-outlined text-[20px]">filter_list</span>
          Filter
        </button>
        <button
          class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors"
        >
          <span class="material-symbols-outlined text-[20px]">add</span>
          Tambah Mahasiswa
        </button>
      </div>
    </div>

    <!-- Student Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="i in 6"
        :key="i"
        class="bg-surface-light border border-border-light rounded-xl p-5 hover:border-primary/50 transition-colors shadow-sm cursor-pointer group"
        @click="
          $router.push({ name: 'DosenBimbinganProgress', params: { id: i } })
        "
      >
        <div class="flex items-start justify-between mb-4">
          <div class="flex items-center gap-3">
            <div
              class="size-10 rounded-full bg-gray-200 dark:bg-gray-700 bg-center bg-cover"
              style="
                background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuBYCuNlLBtqB5KfU2XCx2rhEuplT7TtbJUSVvOhYmIAfx2kppLQUH8zTYLXwTN9OvaxP5MYo9W9MFUrJCI_zdCG24bfYN8fwmdOq6q_ItllLLFI5du7S0eQqZ6jOjfWJ8xQYL-bKLQsn2QjWuqej244ODajAoJ7G-DHzNCOoOfCSnEmVnA1snb3cvEUAAyx7iLUvZxJldMICYLdeaP8GOEXVowgo3XeHvI38KfiEr6e5c8CGW2X1I8sxtImaSqIkjYGGQPz3kDOlsfi&quot;);
              "
            ></div>
            <div>
              <h3 class="font-bold text-text-main">Andi Pratama</h3>
              <p class="text-xs text-text-secondary">NIM: 12345678</p>
            </div>
          </div>
          <span
            class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400"
            >BAB 3</span
          >
        </div>
        <h4
          class="text-sm font-medium text-text-main line-clamp-2 mb-4 h-10 leading-snug"
        >
          Implementasi Algoritma Y untuk Optimasi Z pada Sistem X Berbasis Web
        </h4>
        <div
          class="flex items-center justify-between text-xs text-text-secondary border-t border-border-light pt-3"
        >
          <span class="flex items-center gap-1">
            <span class="material-symbols-outlined text-[14px]">schedule</span>
            Last update: 2h ago
          </span>
          <span class="group-hover:text-primary font-medium transition-colors"
            >Lihat Detail -></span
          >
        </div>
      </div>
    </div>
  </div>
</template>
