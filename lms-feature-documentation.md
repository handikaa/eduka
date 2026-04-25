# LMS Dibimbing - Feature Documentation

Dokumentasi ini merangkum **fitur utama**, **aktor sistem**, **modul bisnis**, dan **use case penting** pada project **LMS Dibimbing** yang dibangun menggunakan **Laravel Backend**, **Clean Architecture**, dan **Domain-Driven Design (DDD)**.

Dokumen ini ditujukan untuk:
- membantu presentasi project
- menjelaskan gambaran fitur secara menyeluruh
- memudahkan developer lain memahami modul yang sudah dibangun
- menjadi referensi pengembangan lanjutan

---

# 1. Gambaran Umum Project

LMS Dibimbing adalah sistem Learning Management System yang berfokus pada dua aktor utama:

1. **Instructor**
2. **Student**

Sistem ini memungkinkan instructor membuat dan mengelola course beserta lesson, sementara student dapat membeli course, mempelajari lesson secara bertahap, melihat progress belajar, dan memberikan review setelah menyelesaikan course.

Arsitektur backend disusun menggunakan pendekatan:
- **Clean Architecture**
- **DDD (Domain-Driven Design)**

Dengan pendekatan ini, logika bisnis dipisahkan ke dalam:
- Domain
- Application Use Cases
- Infrastructure / Eloquent Repository
- HTTP Controller Layer

---

# 2. Aktor Utama Sistem

## 2.1 Instructor
Instructor adalah user yang berperan sebagai pembuat dan pengelola course.

### Fitur Instructor
- membuat course
- mengedit course
- menghapus course
- mengatur status course:
  - draft
  - published
  - archived
- menambahkan kategori pada course
- mengelola lesson di dalam course
- mengatur urutan lesson berdasarkan `position`
- melihat siapa saja student yang membeli course
- melihat review student pada course
- melihat performa course:
  - jumlah peserta
  - rating course
  - pendapatan dari course
- melakukan penarikan saldo hasil penjualan course

---

## 2.2 Student
Student adalah user yang berperan sebagai peserta course.

### Fitur Student
- melihat daftar course
- membeli / enroll ke course
- melihat course yang dimiliki
- membuka detail course yang dimiliki
- mempelajari lesson dalam course
- memulai lesson
- menyelesaikan lesson
- melihat progress belajar
- melihat ringkasan progress course
- memberikan review dan rating course
- mengubah review
- menghapus review
- melakukan topup saldo // hold untuk payment gateway
- melakukan tarik saldo digital

---

# 3. Struktur Modul Utama

Project LMS ini saat ini berfokus pada beberapa modul utama:

1. **Authentication**
2. **Course**
3. **Category**
4. **Lesson**
5. **Enrollment**
6. **LessonProgress**
7. **CourseReview**

---

# 4. Struktur Data dan Relasi Utama

## 4.1 Users
Semua akun disimpan dalam satu tabel `users`.

Role utama:
- `student`
- `instructor`

---

## 4.2 Courses
Tabel `courses` menyimpan data kelas yang dibuat instructor.

Setiap course memiliki:
- instructor
- kategori
- lessons
- enrollments
- reviews

Atribut penting:
- `price`
- `quota`
- `enrolled_count`
- `rating_avg`
- `rating_count`
- `status`

---

## 4.3 Lessons
Tabel `lessons` menyimpan materi di dalam course.

Setiap lesson:
- milik 1 course
- memiliki urutan belajar melalui `position`

---

## 4.4 Enrollments
Tabel `enrollments` menghubungkan student dan course.

Status enrollment:
- `active`
- `completed`
- `cancelled`

Fungsi utama:
- mencatat pembelian / kepemilikan course
- mengontrol akses student ke course

---

## 4.5 Lesson Progress
Tabel `lesson_progress` mencatat progress belajar student pada setiap lesson.

Status:
- `not_started`
- `in_progress`
- `completed`

Fungsi utama:
- melacak kemajuan belajar student per lesson
- menjadi dasar penyelesaian course

---

## 4.6 Course Reviews
Tabel `course_reviews` menyimpan rating dan komentar student terhadap course.

Fitur penting:
- 1 student maksimal 1 review aktif per course
- review menggunakan soft delete custom dengan field `is_delete`

---

# 5. Modul dan Fitur yang Sudah Dibangun

## 5.1 Authentication Module
### Fitur
- register
- login
- logout
- profile

### Tujuan
Mengelola autentikasi user menggunakan Laravel Sanctum.

---

## 5.2 Course Module
### Fitur
- create course
- update course
- delete course
- restore course
- force delete course
- get list courses
- get detail course
- update status course
- attach category ke course
- create course beserta lessons sekaligus

### Nilai bisnis
Modul ini memungkinkan instructor mengelola kelas secara penuh.

---

## 5.3 Category Module
### Fitur
- CRUD category
- menghubungkan category dengan course

### Nilai bisnis
Membantu pengelompokan course agar lebih mudah difilter dan ditemukan.

---

## 5.4 Lesson Module
### Fitur
- create lesson
- update lesson
- delete lesson
- restore lesson
- force delete lesson
- pengaturan urutan lesson melalui `position`

### Nilai bisnis
Membagi course menjadi unit pembelajaran yang terstruktur.

---

# 6. Enrollment Module

Enrollment adalah modul yang mengatur kepemilikan dan akses student terhadap course.

## Tujuan bisnis
- student membeli course
- sistem mencatat kepesertaan student
- sistem mengontrol apakah student boleh mengakses course
- sistem melacak status penyelesaian course

## Use Cases Enrollment
1. `EnrollStudentUsecase`
2. `GetStudentEnrollmentsUsecase`
3. `GetCourseEnrollmentsUsecase`
4. `GetEnrollmentDetailUsecase`
5. `GetStudentEnrollmentByCourseUsecase`
6. `CheckStudentEnrollmentAccessUsecase`
7. `CompleteEnrollmentUsecase`
8. `CancelEnrollmentUsecase`

## Aturan bisnis penting
- hanya student yang bisa enroll course
- student tidak boleh enroll 2 kali pada course yang sama
- enrollment tunduk pada kuota course
- `completed` tetap dihitung dalam `enrolled_count`
- `cancelled` tidak dihitung dalam `enrolled_count`
- `CompleteEnrollmentUsecase` dipicu otomatis dari flow `LessonProgress`

## Nilai bisnis
Enrollment adalah pusat kepemilikan course dan fondasi hak akses belajar student.

---

# 7. LessonProgress Module

LessonProgress adalah modul yang mencatat aktivitas belajar student di dalam lesson.

## Tujuan bisnis
- mencatat apakah student sudah mulai belajar
- mencatat lesson mana yang sudah selesai
- menghitung progress course
- memicu completion course secara otomatis

## Use Cases LessonProgress
1. `StartLessonProgressUsecase`
2. `MarkLessonProgressAsCompletedUsecase`
3. `GetStudentLessonProgressByCourseUsecase`
4. `GetCourseProgressSummaryUsecase`
5. `GetLessonProgressDetailUsecase`
6. `UpdateLastAccessedLessonProgressUsecase`

### Ditunda
7. `ResetLessonProgressUsecase`

## Aturan bisnis penting
- hanya student yang bisa memiliki progress lesson
- student hanya bisa mengakses lesson jika punya enrollment valid
- jika lesson belum pernah disentuh, status default dianggap `not_started`
- jika seluruh lesson dalam course selesai, enrollment otomatis berubah menjadi `completed`

## Nilai bisnis
LessonProgress adalah mesin utama flow belajar student di LMS.

---

# 8. CourseReview Module

CourseReview adalah modul yang mengelola rating dan komentar student terhadap course.

## Tujuan bisnis
- memungkinkan student memberi feedback terhadap course
- membantu menampilkan kualitas course melalui rating
- menjadi dasar social proof di detail course

## Use Cases CourseReview
1. `CreateCourseReviewUsecase`
2. `GetCourseReviewsUsecase`
3. `GetStudentCourseReviewUsecase`
4. `UpdateCourseReviewUsecase`
5. `DeleteCourseReviewUsecase`
6. `RestoreCourseReviewUsecase`

### Ditunda
7. `GetCourseReviewDetailUsecase`

## Aturan bisnis penting
- hanya student yang boleh membuat review
- student hanya boleh review jika enrollment pada course sudah `completed`
- student hanya boleh punya 1 review aktif per course
- review menggunakan soft delete custom `is_delete`
- setiap create / update / delete / restore review harus mengupdate:
  - `courses.rating_count`
  - `courses.rating_avg`

## Nilai bisnis
CourseReview membantu meningkatkan kualitas course dan memberikan feedback nyata dari student.

---

# 9. Flow Bisnis Utama End-to-End

## 9.1 Flow Instructor
1. instructor login
2. instructor membuat course
3. instructor menambahkan category ke course
4. instructor membuat lesson di dalam course
5. instructor mengatur urutan lesson
6. instructor publish course
7. instructor melihat siapa saja student yang membeli course
8. instructor melihat review dari student
9. instructor melihat performa course dan pendapatan

---

## 9.2 Flow Student
1. student login
2. student melihat daftar course
3. student membeli / enroll ke course
4. student membuka detail course yang dimiliki
5. student melihat daftar lesson dan progress
6. student mulai lesson
7. student menyelesaikan lesson
8. sistem menghitung progress course
9. jika semua lesson selesai:
   - enrollment otomatis berubah menjadi `completed`
10. student dapat memberikan review
11. student dapat mengubah, menghapus, atau restore review

---

# 10. Hubungan Antar Modul

## Enrollment -> LessonProgress
- enrollment memberi hak akses ke course
- `CheckStudentEnrollmentAccessUsecase` dipakai di LessonProgress
- `CompleteEnrollmentUsecase` dipicu otomatis ketika semua lesson selesai

## Enrollment -> CourseReview
- review hanya boleh jika student punya enrollment valid
- status enrollment harus `completed`

## LessonProgress -> CourseReview
- lesson progress menentukan apakah course sudah selesai
- completion course menjadi syarat membuat review

---

# 11. Kelebihan Implementasi

## 11.1 Clean Architecture
Project disusun dengan pemisahan lapisan:
- controller untuk HTTP layer
- application use case untuk business flow
- domain untuk rule bisnis
- repository interface untuk abstraksi data
- eloquent repository untuk implementasi database

## 11.2 Domain Rule yang Jelas
Business rule utama sudah didefinisikan, misalnya:
- student tidak boleh enroll 2 kali
- completion course terjadi otomatis
- review hanya setelah course selesai
- soft delete review tidak menghilangkan source of truth

## 11.3 Siap Dikembangkan
Modul yang sekarang sudah dibuat cukup modular sehingga mudah dikembangkan untuk:
- payment
- instructor dashboard
- analytics
- certificate
- wishlist
- admin panel
- reset lesson progress

---

# 12. Fitur yang Sudah Siap untuk Dipresentasikan

Jika dipresentasikan, poin fitur utama yang sudah kuat adalah:

## Dari sisi Instructor
- CRUD course
- CRUD category
- CRUD lesson
- pengaturan urutan lesson
- status course: draft / published / archived
- melihat enrollment student
- melihat review student

## Dari sisi Student
- enroll course
- melihat course yang dimiliki
- melihat detail course milik student
- memulai lesson
- menyelesaikan lesson
- melihat progress belajar
- melihat summary progress course
- memberi review setelah course selesai
- update, delete, restore review

---

# 13. Saran Struktur Presentasi

Agar presentasi project lebih rapi, urutannya bisa seperti ini:

1. **Problem & tujuan LMS**
2. **Aktor sistem: Instructor dan Student**
3. **ERD dan relasi data utama**
4. **Arsitektur backend: Clean Architecture + DDD**
5. **Modul utama**
   - Auth
   - Course
   - Category
   - Lesson
   - Enrollment
   - LessonProgress
   - CourseReview
6. **Flow bisnis student**
7. **Flow bisnis instructor**
8. **Highlight business rules**
9. **API/use case yang sudah dibangun**
10. **Potensi pengembangan ke depan**

---

# 14. Kesimpulan

LMS Dibimbing yang Anda bangun bukan hanya CRUD API biasa, tetapi sudah memiliki alur bisnis yang cukup lengkap untuk sistem pembelajaran digital:

- instructor dapat mengelola course dan lesson
- student dapat membeli course dan belajar bertahap
- progress belajar tercatat per lesson
- completion course terjadi otomatis
- review hanya bisa dilakukan setelah course selesai
- rating course selalu sinkron dengan review aktif

Secara keseluruhan, sistem ini sudah menunjukkan implementasi backend LMS yang cukup matang, modular, dan siap dipresentasikan sebagai project yang memiliki:
- struktur domain yang jelas
- use case yang spesifik
- business rule yang kuat
- flow end-to-end yang realistis
