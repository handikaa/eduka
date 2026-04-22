# Dashboard Use Cases Documentation

Dokumentasi ini merangkum seluruh use case pada modul **Dashboard** di project LMS backend Laravel yang menggunakan pendekatan **Clean Architecture / DDD**.

Tujuan dokumen ini:
- menjadi pengingat struktur dan tanggung jawab setiap use case Dashboard
- menjaga konsistensi business rule
- membantu developer lain memahami fungsi dashboard untuk tiap role
- menjadi referensi saat menyiapkan presentasi produk LMS

---

## Daftar Use Case Dashboard

### Sudah dibuat
1. `GetStudentDashboardSummaryUsecase`
2. `GetInstructorDashboardSummaryUsecase`
3. `GetStudentContinueLearningUsecase`
4. `GetInstructorCoursePerformanceUsecase`

---

## Gambaran Umum Domain Dashboard

Modul Dashboard tidak menjadi source of truth utama, tetapi berfungsi sebagai **lapisan ringkasan** dari modul-modul yang sudah ada, terutama:

- `Enrollment`
- `LessonProgress`
- `Course`
- `CourseReview`

Tujuan Dashboard:
- menampilkan data penting secara ringkas
- mempercepat pemahaman user terhadap kondisinya saat ini
- mendukung kebutuhan MVP untuk student dan instructor
- membantu presentasi produk dengan data yang lebih business-oriented

---

# Keterkaitan Dashboard dengan Modul Lain

## Keterkaitan dengan Enrollment
Dashboard menggunakan data enrollment untuk:
- menghitung jumlah course milik student
- menghitung jumlah course aktif dan selesai
- menghitung total enrollment pada course milik instructor

## Keterkaitan dengan LessonProgress
Dashboard menggunakan lesson progress untuk:
- menentukan continue learning student
- menentukan lesson terakhir yang diakses

## Keterkaitan dengan Course
Dashboard menggunakan course untuk:
- menghitung jumlah course milik instructor
- memisahkan course berdasarkan status
- menampilkan performa tiap course milik instructor

## Keterkaitan dengan CourseReview
Dashboard menggunakan review untuk:
- menghitung jumlah review aktif
- menghitung average rating instructor

---

# 1. GetStudentDashboardSummaryUsecase

## Tujuan
Mengambil ringkasan dashboard untuk student.

## Kebutuhan bisnis
- hanya role `student` yang boleh mengakses dashboard student
- dashboard menampilkan gambaran singkat kondisi belajar student
- data summary diambil dari:
  - enrollment
  - lesson progress

## Input
- authenticated `student`

## Output
- `total_enrolled_courses`
- `total_active_courses`
- `total_completed_courses`
- `continue_learning`

## `continue_learning` berisi
- `course_id`
- `course_title`
- `course_slug`
- `lesson_id`
- `lesson_title`
- `lesson_type`
- `last_accessed_at`

## Dipakai untuk
- halaman dashboard student
- kartu summary belajar
- shortcut menuju lesson terakhir yang dipelajari

## Catatan penting
Use case ini adalah ringkasan global dashboard student.
Untuk kebutuhan widget yang lebih spesifik, digunakan `GetStudentContinueLearningUsecase`.

---

# 2. GetInstructorDashboardSummaryUsecase

## Tujuan
Mengambil ringkasan dashboard untuk instructor.

## Kebutuhan bisnis
- hanya role `instructor` yang boleh mengakses dashboard instructor
- dashboard menampilkan performa global instructor dari seluruh course miliknya
- data summary diambil dari:
  - course
  - enrollment
  - review

## Input
- authenticated `instructor`

## Output
- `total_courses`
- `total_published_courses`
- `total_draft_courses`
- `total_archived_courses`
- `total_enrollments`
- `total_reviews`
- `average_rating`

## Dipakai untuk
- halaman dashboard instructor
- ringkasan performa konten instructor
- melihat kondisi bisnis course secara cepat

## Catatan penting
Untuk versi MVP, use case ini belum memasukkan revenue karena fitur monetisasi penuh belum menjadi fokus utama implementasi backend saat ini.

---

# 3. GetStudentContinueLearningUsecase

## Tujuan
Mengambil data course dan lesson terakhir yang paling relevan untuk dilanjutkan oleh student.

## Kebutuhan bisnis
- hanya role `student` yang boleh mengakses
- jika student belum pernah membuka lesson apa pun, hasilnya `null`
- data diambil dari `lesson_progress.last_accessed_at` paling terbaru

## Input
- authenticated `student`

## Output
- `course`
  - `id`
  - `title`
  - `slug`
  - `thumbnail_url`
  - `status`
- `lesson`
  - `id`
  - `title`
  - `type`
  - `position`
- `progress`
  - `status`
  - `completed_at`
  - `last_accessed_at`

## Dipakai untuk
- widget “Continue Learning”
- tombol “Lanjutkan Belajar”
- shortcut menuju lesson terakhir yang diakses

## Catatan penting
Kondisi `null` bukan error.
Itu berarti student belum memiliki lesson yang pernah diakses dan belum ada data yang bisa dilanjutkan.

---

# 4. GetInstructorCoursePerformanceUsecase

## Tujuan
Mengambil performa setiap course milik instructor.

## Kebutuhan bisnis
- hanya role `instructor` yang boleh mengakses
- performa dihitung per course
- data diambil dari tabel `courses` dan relasi `lessons`

## Input
- authenticated `instructor`

## Output per course
- `course_id`
- `title`
- `slug`
- `status`
- `price`
- `enrolled_count`
- `rating_count`
- `rating_avg`
- `total_lessons`
- `created_at`

## Dipakai untuk
- halaman detail performa instructor
- daftar performa semua course milik instructor
- membantu instructor melihat course mana yang:
  - paling banyak student
  - punya rating baik
  - masih draft / archived / published

## Catatan penting
Use case ini adalah versi lebih detail dari dashboard instructor.
Jika `GetInstructorDashboardSummaryUsecase` memberi angka total, maka use case ini memberi breakdown performa per course.

---

# Flow Bisnis Dashboard

## Flow Dashboard Student
1. student login
2. student membuka dashboard
3. backend mengambil:
   - total course yang dimiliki
   - total course aktif
   - total course selesai
   - continue learning
4. frontend menampilkan summary belajar student

## Flow Dashboard Instructor
1. instructor login
2. instructor membuka dashboard
3. backend mengambil:
   - total course
   - jumlah course per status
   - total enrollment
   - total review
   - average rating
4. jika instructor membuka detail performa course:
   - backend mengambil daftar performa tiap course
5. frontend menampilkan summary bisnis course instructor

---

# Ringkasan Fungsi Setiap Use Case

| Use Case | Fungsi Utama |
|---|---|
| `GetStudentDashboardSummaryUsecase` | ringkasan dashboard student |
| `GetInstructorDashboardSummaryUsecase` | ringkasan dashboard instructor |
| `GetStudentContinueLearningUsecase` | data lesson terakhir untuk continue learning |
| `GetInstructorCoursePerformanceUsecase` | performa tiap course milik instructor |

---

# Nilai Bisnis Modul Dashboard

Modul Dashboard penting karena:
- membuat LMS terasa lebih siap dipakai sebagai produk
- memperjelas value sistem saat presentasi
- menampilkan ringkasan data yang sebelumnya tersebar di berbagai modul
- membantu student memahami progress belajar
- membantu instructor memahami performa course

---

# Catatan Penutup

Modul Dashboard yang sudah dibuat saat ini sudah cukup kuat untuk kebutuhan **MVP dashboard**:

## Dashboard Student
- jumlah course dimiliki
- jumlah course aktif
- jumlah course selesai
- continue learning

## Dashboard Instructor
- jumlah course
- jumlah course per status
- total enrollment
- total review
- average rating
- performa tiap course

Dengan ini, dashboard LMS sudah cukup siap untuk dijelaskan dalam presentasi produk maupun pengembangan lanjutan.
