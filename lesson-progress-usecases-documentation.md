# Lesson Progress Use Cases Documentation

Dokumentasi ini merangkum seluruh use case pada modul **LessonProgress** di project LMS backend Laravel yang menggunakan pendekatan **Clean Architecture / DDD**.

Tujuan dokumen ini:

- menjadi pengingat struktur dan tanggung jawab setiap use case LessonProgress
- menjaga konsistensi business rule
- menjadi referensi saat mengerjakan modul berikutnya, terutama **Review**
- membantu developer lain memahami flow belajar student di dalam course

---

## Daftar Use Case LessonProgress

### Sudah dibuat

1. `StartLessonProgressUsecase`
2. `MarkLessonProgressAsCompletedUsecase`
3. `GetStudentLessonProgressByCourseUsecase`
4. `GetCourseProgressSummaryUsecase`
5. `GetLessonProgressDetailUsecase`
6. `UpdateLastAccessedLessonProgressUsecase`

### Ditunda

7. `ResetLessonProgressUsecase` _(hold dulu, dibuat jika nanti benar-benar dibutuhkan di tengah development)_

---

## Gambaran Umum Domain LessonProgress

`lesson_progress` digunakan untuk mencatat progress belajar **per student per lesson**.

Relasi utama:

- `lesson_progress.user_id -> users.id`
- `lesson_progress.lesson_id -> lessons.id`

Aturan penting:

- 1 student hanya boleh punya 1 record progress untuk 1 lesson
- status progress:
    - `not_started`
    - `in_progress`
    - `completed`

Catatan penting:

- jika sebuah lesson belum pernah disentuh student, maka lesson itu tetap harus tampil di response
- jika belum ada record di tabel `lesson_progress`, maka status default dianggap `not_started`

---

# Keterkaitan LessonProgress dengan Enrollment

Modul LessonProgress sangat bergantung pada modul Enrollment.

Use case Enrollment yang paling erat kaitannya:

- `CheckStudentEnrollmentAccessUsecase`
- `CompleteEnrollmentUsecase`

### Pola integrasi

1. student membuka atau mengubah progress lesson
2. sistem cek akses lewat `CheckStudentEnrollmentAccessUsecase`
3. lesson progress diubah
4. saat lesson diselesaikan, backend menghitung progress seluruh course
5. jika semua lesson dalam course selesai, backend otomatis menjalankan `CompleteEnrollmentUsecase`

### Catatan bisnis penting

- student hanya boleh mengakses lesson jika punya enrollment valid pada course terkait
- enrollment dengan status `active` dan `completed` tetap dianggap boleh mengakses course
- enrollment `cancelled` tidak boleh mengakses course
- ketika seluruh lesson pada course selesai, status enrollment otomatis berubah menjadi `completed`

---

# 1. StartLessonProgressUsecase

## Tujuan

Memulai progress lesson saat student mulai membuka atau mempelajari sebuah lesson.

## Kebutuhan bisnis

- hanya role `student` yang boleh memulai progress lesson
- lesson harus ada
- student harus punya akses ke course melalui enrollment
- jika progress belum ada:
    - buat record baru
    - status awal = `in_progress`
    - `last_accessed_at` diisi
- jika progress sudah ada:
    - jika status masih `not_started`, ubah jadi `in_progress`
    - jika sudah `in_progress` atau `completed`, cukup update `last_accessed_at`

## Input

- authenticated `student`
- `lessonId`

## Output

- object `LessonProgress`

## Dipakai untuk

- saat student membuka lesson pertama kali
- tombol “Mulai Belajar”
- menandai bahwa lesson sudah mulai dipelajari

---

# 2. MarkLessonProgressAsCompletedUsecase

## Tujuan

Menandai satu lesson sebagai selesai dipelajari oleh student.

## Kebutuhan bisnis

- hanya role `student` yang boleh menyelesaikan lesson
- lesson harus ada
- student harus punya akses ke course melalui enrollment
- jika progress belum ada:
    - boleh langsung dibuat sebagai `completed`
- jika progress sudah ada:
    - ubah status menjadi `completed`
- `completed_at` dan `last_accessed_at` harus diisi
- setelah lesson diselesaikan:
    - hitung total lesson dalam course
    - hitung total lesson yang sudah `completed`
    - jika semua lesson selesai, jalankan `CompleteEnrollmentUsecase`

## Input

- authenticated `student`
- `lessonId`

## Output

- object `LessonProgress`

## Dipakai untuk

- tombol “Selesaikan Lesson”
- checkpoint utama yang menghubungkan LessonProgress ke Enrollment completion

## Catatan penting

Use case ini adalah jembatan penting antara:

- progress lesson
- completion course
- eligibility untuk review di masa depan

---

# 3. GetStudentLessonProgressByCourseUsecase

## Tujuan

Mengambil seluruh daftar lesson dalam satu course beserta status progress milik student pada tiap lesson.

## Kebutuhan bisnis

- hanya role `student` yang boleh melihat progress miliknya
- student harus punya akses ke course melalui enrollment
- semua lesson dalam course harus tetap tampil
- jika sebuah lesson belum punya record progress, status default dianggap `not_started`

## Input

- authenticated `student`
- `courseId`

## Output

- `course_id`
- daftar `lessons`
- setiap lesson membawa data `progress`:
    - `status`
    - `completed_at`
    - `last_accessed_at`

## Dipakai untuk

- halaman belajar course
- sidebar daftar lesson
- menampilkan status tiap lesson:
    - belum mulai
    - sedang dipelajari
    - selesai

## Catatan

Use case ini adalah sumber data utama untuk UI yang menampilkan daftar lesson + progress student dalam satu course.

---

# 4. GetCourseProgressSummaryUsecase

## Tujuan

Mengambil ringkasan progress student pada satu course.

## Kebutuhan bisnis

- hanya role `student` yang boleh melihat summary progress miliknya
- student harus punya akses ke course melalui enrollment
- summary dihitung dari total lesson dan progress student pada lesson-lesson itu

## Input

- authenticated `student`
- `courseId`

## Output

- `course_id`
- `total_lessons`
- `completed_lessons`
- `in_progress_lessons`
- `not_started_lessons`
- `progress_percentage`

## Dipakai untuk

- progress bar di halaman course
- kartu progress di halaman “My Courses”
- ringkasan progres belajar student

## Catatan

Use case ini berguna saat FE tidak butuh seluruh detail lesson, tetapi hanya butuh angka ringkas untuk ditampilkan.

---

# 5. GetLessonProgressDetailUsecase

## Tujuan

Mengambil detail satu lesson beserta progress student pada lesson tersebut.

## Kebutuhan bisnis

- hanya role `student` yang boleh melihat detail progress lesson miliknya
- lesson harus ada
- student harus punya akses ke course dari lesson tersebut
- jika progress belum ada, status default dianggap `not_started`

## Input

- authenticated `student`
- `lessonId`

## Output

- `course_id`
- detail `lesson`
- detail `progress`:
    - `status`
    - `completed_at`
    - `last_accessed_at`

## Dipakai untuk

- halaman detail lesson
- saat student klik satu lesson dari daftar lesson
- menampilkan status lesson sebelum student menekan tombol start/complete

## Catatan

Use case ini adalah versi detail per lesson, sedangkan `GetStudentLessonProgressByCourseUsecase` adalah versi list untuk seluruh lesson dalam course.

---

# 6. UpdateLastAccessedLessonProgressUsecase

## Tujuan

Memperbarui `last_accessed_at` pada lesson progress saat student membuka atau kembali ke lesson.

## Kebutuhan bisnis

- hanya role `student` yang boleh memperbarui progress miliknya
- lesson harus ada
- student harus punya akses ke course melalui enrollment
- jika progress belum ada:
    - buat record baru
    - status awal = `in_progress`
    - isi `last_accessed_at`
- jika progress sudah ada:
    - hanya update `last_accessed_at`
    - jangan menurunkan status `completed`
    - jangan mengubah `completed_at`

## Input

- authenticated `student`
- `lessonId`

## Output

- object `LessonProgress`

## Dipakai untuk

- menyimpan lesson terakhir yang dibuka
- fitur “lanjutkan belajar”
- tracking aktivitas belajar tanpa harus menandai lesson selesai

## Catatan

Use case ini berguna terutama untuk kebutuhan frontend dan UX learning flow.

---

# 7. ResetLessonProgressUsecase _(hold)_

## Status

Belum dibuat, ditunda sementara.

## Alasan ditunda

Saat ini belum menjadi bagian dari flow belajar inti.

## Kemungkinan fungsi di masa depan

- reset progress lesson menjadi `not_started`
- ulang lesson
- ulang course
- menghapus state belajar tertentu

## Kapan bisa dipertimbangkan

- jika nanti ada kebutuhan “ulang dari awal”
- jika product flow meminta student bisa reset progress secara manual
- jika admin butuh tool internal untuk reset progress

---

# Flow Bisnis LessonProgress

## Flow utama student

1. student membuka halaman detail course miliknya
2. student melihat daftar lesson + progress
3. student klik satu lesson
4. backend mengembalikan detail lesson + progress
5. student mulai lesson
6. sistem membuat / mengubah progress menjadi `in_progress`
7. student menyelesaikan lesson
8. sistem mengubah progress menjadi `completed`
9. sistem cek apakah semua lesson dalam course sudah selesai
10. jika semua selesai, enrollment otomatis menjadi `completed`

---

# Ringkasan Fungsi Setiap Use Case

| Use Case                                  | Fungsi Utama                                                                    |
| ----------------------------------------- | ------------------------------------------------------------------------------- |
| `StartLessonProgressUsecase`              | memulai progress lesson                                                         |
| `MarkLessonProgressAsCompletedUsecase`    | menyelesaikan lesson dan memicu completion enrollment jika semua lesson selesai |
| `GetStudentLessonProgressByCourseUsecase` | daftar semua lesson + progress student dalam satu course                        |
| `GetCourseProgressSummaryUsecase`         | ringkasan progress student pada course                                          |
| `GetLessonProgressDetailUsecase`          | detail satu lesson + progress student                                           |
| `UpdateLastAccessedLessonProgressUsecase` | update lesson terakhir yang diakses                                             |
| `ResetLessonProgressUsecase`              | reset progress lesson _(ditunda)_                                               |

---

# Rekomendasi Saat Masuk ke Modul Review

Saat mengerjakan modul Review nanti, hal yang perlu diingat dari LessonProgress adalah:

- review sangat mungkin mensyaratkan student sudah menyelesaikan proses belajar
- status `completed` pada enrollment dipicu dari completion seluruh lesson
- jadi validasi review akan bergantung pada:
    - enrollment
    - completion course
    - progress lesson yang sudah selesai

Kemungkinan aturan review:

- student harus punya enrollment pada course
- student harus sudah menyelesaikan seluruh lesson
- enrollment kemungkinan harus berstatus `completed`

---

# Catatan Penutup

Modul LessonProgress yang sudah dibuat sekarang sudah cukup kuat untuk mendukung flow belajar inti student.

Yang paling penting untuk diingat:

- LessonProgress tidak berdiri sendiri, tetapi sangat bergantung pada Enrollment
- Enrollment memberi hak akses ke course
- LessonProgress mencatat aktivitas belajar per lesson
- Completion semua lesson akan memicu completion enrollment
- Completion enrollment akan menjadi fondasi penting untuk modul Review
