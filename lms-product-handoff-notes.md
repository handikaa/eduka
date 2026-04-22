# LMS Product Handoff Notes

Dokumen ini dibuat sebagai **ringkasan konteks project LMS** agar saat membuka chat baru, konteks produk, implementasi backend yang sudah selesai, dan langkah pengembangan selanjutnya bisa langsung dipahami tanpa mengulang penjelasan dari awal.

Dokumen ini berfungsi sebagai:
- handoff context untuk diskusi lanjutan
- ringkasan kebutuhan produk LMS
- catatan modul backend yang sudah selesai
- ringkasan business rule penting
- daftar prioritas pengembangan berikutnya

---

# 1. Ringkasan Project

Project yang sedang dikembangkan adalah **Learning Management System (LMS)** berbasis backend Laravel dengan pendekatan:

- **Clean Architecture**
- **DDD (Domain-Driven Design)**

Project ini berasal dari studi kasus bootcamp dengan tujuan membangun LMS yang:
- mudah digunakan
- modular
- scalable
- berorientasi bisnis
- siap dikembangkan menjadi produk komersial

---

# 2. Studi Kasus Produk LMS

## Latar belakang
Kebutuhan pembelajaran digital terus meningkat untuk:
- individu
- institusi pendidikan
- perusahaan

LMS ini ditujukan sebagai platform pembelajaran online yang:
- user-friendly
- modular
- scalable
- dapat dikembangkan ke berbagai segmen pasar

## Tujuan produk
- menyediakan platform pembelajaran online yang mudah dipakai
- mendukung berbagai jenis konten
- menjadi fondasi produk komersial
- mendukung akuisisi user, retensi, dan monetisasi

## Persona utama
### General Learner
- usia 17–40 tahun
- pelajar, mahasiswa, fresh graduate, pekerja
- butuh:
  - akses materi kapan saja
  - progress belajar yang jelas
  - tampilan yang sederhana dan intuitif

### Instructor / Content Creator
- pengajar, mentor, trainer, praktisi
- butuh:
  - upload dan kelola materi dengan mudah
  - monitoring progress siswa
  - monetisasi course

---

# 3. Aktor Utama Sistem

Sistem LMS ini memiliki dua role utama:

## 3.1 Instructor
Fokus instructor:
- membuat course
- mengelola course
- mengelola lesson
- melihat enrollment student
- melihat review student
- monetisasi course

## 3.2 Student
Fokus student:
- membeli / enroll course
- belajar lesson
- melihat progress belajar
- menyelesaikan course
- memberikan review course

---

# 4. ERD dan Struktur Data Inti

Tabel utama yang sudah dirancang dalam ERD:

1. `users`
2. `courses`
3. `lessons`
4. `enrollments`
5. `lesson_progress`
6. `categories`
7. `course_categories`
8. `course_reviews`

## Hubungan inti
- instructor membuat banyak course
- course memiliki banyak lesson
- student memiliki banyak enrollment
- enrollment menghubungkan student dan course
- lesson_progress mencatat progress student per lesson
- course_reviews menyimpan rating dan komentar student terhadap course
- course_categories menghubungkan course dan category

---

# 5. Fitur Produk yang Sudah Diidentifikasi

## Dari sisi Instructor
- manage CRUD course
- memilih category untuk course
- publish / archive / draft course
- manage lesson
- mengatur urutan lesson
- melihat student yang membeli course
- melihat review course
- melihat performa course
- monetisasi course / pendapatan

## Dari sisi Student
- melihat course
- membeli / enroll course
- melihat course yang dimiliki
- belajar lesson
- melihat progress belajar
- menyelesaikan course
- memberi review course setelah selesai
- update / delete / restore review

---

# 6. Status Implementasi Backend Saat Ini

Backend yang sudah dikerjakan sampai saat ini berfokus pada:

- Authentication
- Course
- Category
- Lesson
- Enrollment
- LessonProgress
- CourseReview

---

# 7. Modul yang Sudah Diimplementasikan

## 7.1 Authentication
Sudah dibuat:
- register
- login
- logout
- profile
- auth berbasis Sanctum

---

## 7.2 Course
Sudah dibuat:
- create course
- read/list course
- detail course
- update course
- delete course
- restore course
- force delete course
- update course status
- create course dengan lessons
- relasi category
- relasi lesson

---

## 7.3 Category
Sudah dibuat:
- create category
- read/list category
- detail category
- update category
- delete category

---

## 7.4 Lesson
Sudah dibuat:
- create lesson
- update lesson
- delete lesson
- restore lesson
- force delete lesson
- position / urutan lesson sudah didukung
- lesson terhubung ke course

---

# 8. Modul Enrollment yang Sudah Selesai

## Use case yang sudah dibuat
1. `EnrollStudentUsecase`
2. `GetStudentEnrollmentsUsecase`
3. `GetCourseEnrollmentsUsecase`
4. `GetEnrollmentDetailUsecase`
5. `GetStudentEnrollmentByCourseUsecase`
6. `CheckStudentEnrollmentAccessUsecase`
7. `CompleteEnrollmentUsecase`
8. `CancelEnrollmentUsecase`

## Rule bisnis penting
- hanya student yang bisa enroll
- student tidak boleh enroll dua kali ke course yang sama
- course tunduk pada quota
- enrollment status:
  - `active`
  - `completed`
  - `cancelled`
- `completed` tetap dihitung dalam `enrolled_count`
- `cancelled` tidak dihitung dalam `enrolled_count`
- `CompleteEnrollmentUsecase` dipicu otomatis dari flow LessonProgress
- `CheckStudentEnrollmentAccessUsecase` menjadi fondasi validasi akses ke course

## Catatan desain penting
- `GetEnrollmentDetailUsecase` lebih cocok untuk detail enrollment generik / instructor view
- untuk student, sudah dibuat pendekatan terpisah untuk detail enrollment/course belajar

---

# 9. Modul LessonProgress yang Sudah Selesai

## Use case yang sudah dibuat
1. `StartLessonProgressUsecase`
2. `MarkLessonProgressAsCompletedUsecase`
3. `GetStudentLessonProgressByCourseUsecase`
4. `GetCourseProgressSummaryUsecase`
5. `GetLessonProgressDetailUsecase`
6. `UpdateLastAccessedLessonProgressUsecase`

## Ditunda
7. `ResetLessonProgressUsecase`

## Rule bisnis penting
- lesson progress hanya untuk student
- student harus punya akses melalui enrollment
- status progress:
  - `not_started`
  - `in_progress`
  - `completed`
- jika lesson belum punya record progress, default dianggap `not_started`
- `StartLessonProgressUsecase` membuat / mengubah progress menjadi `in_progress`
- `MarkLessonProgressAsCompletedUsecase` mengubah lesson menjadi `completed`
- saat semua lesson dalam course selesai, sistem otomatis menjalankan `CompleteEnrollmentUsecase`
- `UpdateLastAccessedLessonProgressUsecase` dipakai untuk kebutuhan FE seperti continue learning

## Hasil bisnis penting
Flow belajar student sudah hidup:
- student enroll
- student membuka detail course
- student start lesson
- student complete lesson
- semua lesson selesai
- enrollment otomatis menjadi `completed`

---

# 10. Modul CourseReview yang Sudah Selesai

## Use case yang sudah dibuat
1. `CreateCourseReviewUsecase`
2. `GetCourseReviewsUsecase`
3. `GetStudentCourseReviewUsecase`
4. `UpdateCourseReviewUsecase`
5. `DeleteCourseReviewUsecase`
6. `RestoreCourseReviewUsecase`

## Tidak dibuat saat ini
7. `GetCourseReviewDetailUsecase`

## Rule bisnis penting
- hanya student yang bisa membuat review
- student hanya bisa review jika enrollment pada course berstatus `completed`
- 1 student hanya boleh punya 1 review aktif per course
- review memakai soft delete custom:
  - `is_delete = true/false`
- review yang terhapus tidak ikut dihitung dalam:
  - `courses.rating_count`
  - `courses.rating_avg`
- setiap create/update/delete/restore review harus mengupdate summary rating course

## Hasil bisnis penting
Flow review sudah hidup:
- student menyelesaikan course
- student bisa memberi review
- student bisa update review
- student bisa delete review
- student bisa restore review

---

# 11. Dokumentasi yang Sudah Dibuat

Sudah dibuat file dokumentasi terpisah untuk:

- Enrollment use cases
- LessonProgress use cases
- CourseReview use cases
- LMS feature documentation

Dokumen-dokumen itu berisi:
- tujuan tiap use case
- rule bisnis
- hubungan antar modul
- flow bisnis utama

---

# 12. Business Rules Inti yang Harus Selalu Dijaga

Berikut beberapa rule terpenting yang harus selalu diingat dalam diskusi lanjutan:

## Enrollment
- student tidak boleh enroll dua kali
- enrollment mengontrol hak akses ke course
- enrollment selesai otomatis dari progress lesson

## LessonProgress
- lesson progress hanya untuk student yang punya enrollment valid
- progress per lesson unik per student
- semua lesson selesai -> enrollment completed

## CourseReview
- review hanya boleh jika course sudah selesai
- review aktif hanya satu per student per course
- soft delete review harus memengaruhi rating summary course

---

# 13. Fitur Produk yang Masih Perlu Dirapikan atau Didokumentasikan

Walaupun backend inti sudah cukup matang, masih ada beberapa hal yang perlu dirapikan agar project siap dipresentasikan dengan lebih baik.

## Yang perlu dibuat / dirapikan dari sisi dokumentasi
1. Analisis Kebutuhan LMS
2. Use Case Summary per Role
3. 3 flowchart utama
4. README versi produk/presentasi
5. daftar fitur yang sudah jadi vs roadmap

## Yang perlu dipertimbangkan dari sisi fitur produk
### Basic Dashboard Summary
Karena studi kasus MVP menyebut **Basic Dashboard**, ini menjadi kandidat terbaik untuk fitur backend tambahan agar project lebih siap dipresentasikan.

Contoh dashboard summary:

#### Student Dashboard
- total enrolled courses
- course aktif
- course completed
- progress terbaru / continue learning

#### Instructor Dashboard
- total course dibuat
- total enrollment
- total review
- total rating
- total pendapatan / monetisasi (jika dipakai)

---

# 14. Langkah Pengembangan Selanjutnya yang Direkomendasikan

Langkah paling praktis berikutnya yang disarankan:

1. **Buat Analisis Kebutuhan LMS**
2. **Buat Use Case Summary per Role**
3. **Buat 3 flowchart utama**
4. **Tambahkan Basic Dashboard summary sebagai fitur/roadmap**
5. **Rapikan README presentasi versi produk**
6. **Siapkan fitur yang sudah jadi vs roadmap**

---

# 15. 3 Flow Utama yang Perlu Dijelaskan di Chat Berikutnya

## Flow 1 — Instructor membuat course
- login
- create course
- assign category
- create lessons
- set lesson position
- publish course

## Flow 2 — Student belajar sampai selesai
- login
- enroll course
- buka detail course
- start lesson
- complete lesson
- semua lesson selesai
- enrollment completed

## Flow 3 — Student memberi review
- course completed
- cek review existing
- create / update / delete / restore review

---

# 16. Kebutuhan Chat Berikutnya

Chat berikutnya disarankan fokus pada sisi **product & analysis**, bukan teknikal backend detail.

Topik yang cocok untuk chat berikutnya:
- Analisis Kebutuhan LMS
- Use Case Summary per Role
- Flowchart utama
- Gap analysis terhadap case study bootcamp
- Dashboard summary MVP
- README presentasi versi produk
- roadmap fitur industri

---

# 17. Ringkasan Singkat untuk Dibawa ke Chat Baru

Gunakan ringkasan ini jika ingin cepat memberi konteks ke chat baru:

> Saya sedang membangun backend LMS dengan Laravel menggunakan Clean Architecture + DDD.  
> Role utama ada dua: instructor dan student.  
> Backend yang sudah selesai mencakup Auth, Course, Category, Lesson, Enrollment, LessonProgress, dan CourseReview.  
>  
> Enrollment sudah mendukung enroll, list enrollment student/instructor, access checking, complete enrollment otomatis, dan cancel enrollment.  
> LessonProgress sudah mendukung start lesson, complete lesson, list progress by course, summary progress, detail progress lesson, dan update last accessed.  
> CourseReview sudah mendukung create, list, get student review, update, delete, dan restore review dengan soft delete custom `is_delete`.  
>  
> Rule penting:
> - student tidak boleh enroll dua kali
> - akses lesson bergantung pada enrollment valid
> - semua lesson selesai -> enrollment completed
> - review hanya boleh jika enrollment completed
> - rating summary course selalu sinkron dari review aktif
>  
> Saya ingin lanjut ke sisi product analysis dan dokumentasi agar project siap dipresentasikan:
> - analisis kebutuhan
> - use case per role
> - flowchart utama
> - dashboard summary MVP
> - README presentasi versi produk
> - fitur yang sudah jadi vs roadmap

---

# 18. Penutup

Project LMS ini sudah memiliki backend yang kuat di sisi business flow inti.

Yang paling penting untuk diingat:
- fokus chat berikutnya bukan lagi membangun use case backend inti
- fokus berikutnya adalah **mengubah implementasi yang sudah ada menjadi narasi produk yang matang**
- titik lanjut terbaik adalah dokumen analisis kebutuhan dan kesiapan presentasi
