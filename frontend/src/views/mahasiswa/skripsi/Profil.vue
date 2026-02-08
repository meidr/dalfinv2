<template>
  <div class="flex flex-col gap-6 animate-fade-in">
    <div
      class="bg-surface-light rounded-xl p-6 shadow-sm border border-border-light"
    >
      <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">person</span>
        Identitas Mahasiswa
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
        <div class="flex flex-col">
          <span class="text-xs text-text-secondary uppercase font-semibold"
            >Nama Lengkap</span
          >
          <span class="font-medium">Budi Santoso</span>
        </div>
        <div class="flex flex-col">
          <span class="text-xs text-text-secondary uppercase font-semibold"
            >NIM</span
          >
          <span class="font-medium">12345678</span>
        </div>
        <div class="flex flex-col">
          <span class="text-xs text-text-secondary uppercase font-semibold"
            >Program Studi</span
          >
          <span class="font-medium">Teknik Informatika</span>
        </div>
        <div class="flex flex-col">
          <span class="text-xs text-text-secondary uppercase font-semibold"
            >Angkatan</span
          >
          <span class="font-medium">2020</span>
        </div>
      </div>
    </div>

    <div
      class="bg-surface-light rounded-xl p-6 shadow-sm border border-border-light"
    >
      <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">book</span>
        Data Skripsi
      </h3>
      <div class="flex flex-col gap-4">
        <div class="flex flex-col gap-1">
          <span class="text-xs text-text-secondary uppercase font-semibold"
            >Judul Skripsi</span
          >
          <h2 class="text-xl font-bold leading-snug">
            Analisis Penerapan Algoritma Machine Learning pada Sistem Prediksi
            Cuaca Berbasis IoT
          </h2>
        </div>
        <div class="flex flex-col gap-1">
          <span class="text-xs text-text-secondary uppercase font-semibold"
            >Abstrak</span
          >
          <p class="text-sm text-text-secondary leading-relaxed text-justify">
            Penelitian ini bertujuan untuk mengembangkan sistem prediksi cuaca
            yang akurat dengan memanfaatkan data dari perangkat Internet of
            Things (IoT) dan algoritma Machine Learning. Metode yang digunakan
            meliputi pengumpulan data suhu, kelembaban, dan tekanan udara secara
            real-time, kemudian diproses menggunakan model regresi...
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
