const fs = require('fs');
const execSync = require('child_process').execSync;

const files = [
"frontend/src/views/admin/mentorsempro/DataMentorSempro.vue",
"frontend/src/views/admin/nomorsurat/DataNomorSurat.vue",
"frontend/src/views/admin/pembimbing/DataPembimbing.vue",
"frontend/src/views/admin/pengguna/DataPengguna.vue",
"frontend/src/views/admin/profil/Profile.vue",
"frontend/src/views/admin/seminar/DataSeminar.vue",
"frontend/src/views/admin/seminar/DetailSeminar.vue",
"frontend/src/views/admin/seminarhasil/DataSeminarHasil.vue",
"frontend/src/views/admin/seminarhasil/DetailSeminarHasil.vue",
"frontend/src/views/admin/skripsi/DataSkripsi.vue",
"frontend/src/views/admin/skripsi/DetailSkripsi.vue",
"frontend/src/views/admin/skripsi/VerificationSkripsi.vue",
"frontend/src/views/admin/sktugas/DataSKTugas.vue",
"frontend/src/views/admin/skyudisium/DataSKYudisium.vue",
"frontend/src/views/admin/superadmin/OtoritasJabatan.vue",
"frontend/src/views/admin/superadmin/OtoritasTandaTangan.vue",
"frontend/src/views/admin/ujian/DataUjian.vue",
"frontend/src/views/dosen/Dashboard.vue",
"frontend/src/views/dosen/Jadwal.vue",
"frontend/src/views/mahasiswa/DaftarSkripsi.vue",
"frontend/src/views/mahasiswa/Dashboard.vue"
];

for (let file of files) {
    try {
        execSync(`git checkout ${file}`, {cwd: 'c:\\laragon\\www\\dalfin'});
        console.log('Restored', file);
    } catch (e) {
        console.log('Failed to restore (maybe new file)', file);
    }
}
