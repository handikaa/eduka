# Course Review Use Cases Documentation

Dokumentasi ini merangkum seluruh use case pada modul **CourseReview** di project LMS backend Laravel yang menggunakan pendekatan **Clean Architecture / DDD**.

Tujuan dokumen ini:
- menjadi pengingat struktur dan tanggung jawab setiap use case CourseReview
- menjaga konsistensi business rule
- membantu developer lain memahami flow review course
- menjadi referensi jika nanti modul review perlu dikembangkan lebih lanjut

---

## Daftar Use Case CourseReview

### Sudah dibuat
1. `CreateCourseReviewUsecase`
2. `GetCourseReviewsUsecase`
3. `GetStudentCourseReviewUsecase`
4. `UpdateCourseReviewUsecase`
5. `DeleteCourseReviewUsecase`
6. `RestoreCourseReviewUsecase`

### Tidak dibuat saat ini
7. `GetCourseReviewDetailUsecase` *(ditunda karena saat ini belum dibutuhkan oleh bisnis)*

---

## Gambaran Umum Domain CourseReview

`course_reviews` digunakan untuk menyimpan rating dan komentar student terhadap course.

Relasi utama:
- `course_reviews.course_id -> courses.id`
- `course_reviews.user_id -> users.id`

Atribut penting:
- `rating`
- `comment`
- `is_delete`

Catatan penting:
- modul ini menggunakan **soft delete custom** dengan field `is_delete`
- review dengan `is_delete = true` dianggap terhapus
- review yang terhapus:
  - tidak boleh tampil di list review aktif
  - tidak boleh dihitung di `rating_avg`
  - tidak boleh dihitung di `rating_count`

---

# Keterkaitan CourseReview dengan Enrollment dan LessonProgress

Modul CourseReview bergantung pada dua modul yang telah dibuat sebelumnya:

- **Enrollment**
- **LessonProgress**

### Rule bisnis utama
Student hanya boleh memberikan review jika:
- student benar-benar memiliki enrollment pada course
- dan enrollment tersebut sudah **completed**

Artinya:
- student harus membeli course
- student harus menyelesaikan proses belajar
- completion course sendiri dipicu dari seluruh lesson progress yang sudah selesai

### Implikasi
- CourseReview tidak berdiri sendiri
- validasi membuat review bergantung pada status enrollment
- status enrollment `completed` adalah fondasi utama agar review menjadi sah

---

# 1. CreateCourseReviewUsecase

## Tujuan
Membuat review baru untuk sebuah course oleh student.

## Kebutuhan bisnis
- hanya role `student` yang boleh membuat review
- course harus ada
- student harus punya enrollment pada course
- enrollment student harus berstatus `completed`
- student hanya boleh punya **1 review aktif** untuk 1 course
- rating harus valid (1 sampai 5)
- review baru dibuat dengan:
  - `is_delete = false`
- setelah review dibuat:
  - `courses.rating_count` dihitung ulang
  - `courses.rating_avg` dihitung ulang

## Input
- authenticated `student`
- `courseId`
- `rating`
- `comment`

## Output
- object `CourseReview`

## Dipakai untuk
- student memberikan review dan rating course yang telah selesai dipelajari

## Catatan penting
Ini adalah use case paling penting pada modul review, karena menjadi titik masuk utama fitur review.

---

# 2. GetCourseReviewsUsecase

## Tujuan
Mengambil daftar review aktif pada sebuah course.

## Kebutuhan bisnis
- course harus ada
- hanya review aktif (`is_delete = false`) yang boleh tampil
- hasil dapat dipaginasi

## Input
- `courseId`
- `perPage`
- `page`

## Output
- paginated list review aktif pada course

## Dipakai untuk
- halaman detail course
- menampilkan testimonial / review course
- instructor melihat review pada course miliknya

## Catatan
Use case ini tidak difokuskan ke role tertentu, karena daftar review course bisa dipakai oleh banyak konteks tampilan.

---

# 3. GetStudentCourseReviewUsecase

## Tujuan
Mengambil review aktif milik student yang sedang login pada satu course tertentu.

## Kebutuhan bisnis
- hanya role `student` yang boleh memakai use case ini
- course harus ada
- review yang dicari adalah review aktif student pada course itu
- jika tidak ada review aktif, hasil `null`

## Input
- authenticated `student`
- `courseId`

## Output
- `CourseReview | null`

## Dipakai untuk
- FE mengecek apakah student sudah pernah review course ini atau belum
- menentukan state UI:
  - tampilkan tombol **Beri Review**
  - atau tampilkan tombol **Edit Review**
- mengambil review milik student untuk form edit

## Catatan
Kondisi “belum ada review” bukan error, melainkan state bisnis normal.

---

# 4. UpdateCourseReviewUsecase

## Tujuan
Memperbarui review aktif milik student pada sebuah course.

## Kebutuhan bisnis
- hanya role `student` yang boleh memperbarui review
- course harus ada
- review aktif milik student pada course itu harus ada
- rating harus valid (1 sampai 5)
- setelah review diperbarui:
  - `courses.rating_count` dihitung ulang
  - `courses.rating_avg` dihitung ulang

## Input
- authenticated `student`
- `courseId`
- `rating`
- `comment`

## Output
- object `CourseReview`

## Dipakai untuk
- student mengubah rating atau komentar review yang sudah dibuat sebelumnya

## Catatan
Karena review aktif student sudah ada, use case ini tidak perlu lagi memvalidasi ulang enrollment completed.
Keberadaan review aktif sudah cukup menjadi bukti bahwa student sebelumnya lolos rule bisnis create review.

---

# 5. DeleteCourseReviewUsecase

## Tujuan
Melakukan soft delete review aktif milik student pada sebuah course.

## Kebutuhan bisnis
- hanya role `student` yang boleh menghapus review
- course harus ada
- review aktif milik student pada course itu harus ada
- delete dilakukan dengan:
  - `is_delete = true`
- setelah delete:
  - `courses.rating_count` dihitung ulang
  - `courses.rating_avg` dihitung ulang

## Input
- authenticated `student`
- `courseId`

## Output
- object `CourseReview` yang sudah berubah menjadi soft deleted

## Dipakai untuk
- student menghapus review miliknya

## Catatan
Delete di modul ini bukan hard delete.
Data review tetap ada di database, tetapi ditandai sebagai terhapus.

---

# 6. RestoreCourseReviewUsecase

## Tujuan
Mengembalikan review student yang sebelumnya telah di-soft-delete.

## Kebutuhan bisnis
- hanya role `student` yang boleh restore review miliknya
- course harus ada
- review yang dicari harus review milik student pada course itu dengan `is_delete = true`
- restore dilakukan dengan:
  - `is_delete = false`
- setelah restore:
  - `courses.rating_count` dihitung ulang
  - `courses.rating_avg` dihitung ulang

## Input
- authenticated `student`
- `courseId`

## Output
- object `CourseReview` yang telah di-restore

## Dipakai untuk
- student mengaktifkan kembali review yang sebelumnya dihapus

## Catatan
Restore ini berguna karena sistem review menggunakan soft delete custom, bukan delete permanen.

---

# 7. GetCourseReviewDetailUsecase *(ditunda)*

## Status
Tidak dibuat saat ini.

## Alasan tidak dibuat
Dari kebutuhan bisnis yang Anda tentukan, detail review tunggal belum dibutuhkan.
Daftar review course, review milik student, create, update, delete, dan restore sudah cukup untuk flow review saat ini.

## Kapan bisa dipertimbangkan
- jika nanti instructor perlu membuka detail satu review tertentu
- jika ada kebutuhan admin/internal
- jika frontend suatu saat membutuhkan halaman detail review tunggal

---

# Rule Bisnis Penting pada CourseReview

## 1. Student harus menyelesaikan course sebelum bisa review
Ini adalah rule paling penting.

Implikasinya:
- Create review harus validasi enrollment
- status enrollment wajib `completed`

## 2. Satu student satu review aktif per course
Implikasinya:
- student tidak boleh membuat lebih dari satu review aktif
- jika review sudah pernah dihapus, maka lebih tepat di-restore daripada membuat review aktif ganda

## 3. Soft delete custom pakai `is_delete`
Implikasinya:
- semua query list aktif harus filter `is_delete = false`
- semua perhitungan rating course harus hanya memakai review aktif
- review yang terhapus tetap ada di database dan bisa di-restore

## 4. Rating summary di tabel `courses` harus selalu sinkron
Setiap kali:
- create
- update
- delete
- restore

maka field berikut pada `courses` harus diperbarui:
- `rating_count`
- `rating_avg`

Karena `course_reviews` adalah source of truth, sedangkan `courses.rating_count` dan `courses.rating_avg` adalah ringkasan performa untuk frontend.

---

# Flow Bisnis CourseReview

## Flow utama student
1. student menyelesaikan seluruh lesson pada course
2. enrollment otomatis berubah menjadi `completed`
3. student membuka halaman course
4. FE mengecek apakah student sudah punya review aktif untuk course itu
5. jika belum ada review:
   - tampilkan form **Beri Review**
6. student mengirim rating dan komentar
7. review dibuat
8. `rating_count` dan `rating_avg` course dihitung ulang
9. jika student ingin mengubah review:
   - gunakan update review
10. jika student ingin menghapus review:
   - gunakan soft delete review
11. jika student ingin mengembalikan review:
   - gunakan restore review

---

# Ringkasan Fungsi Setiap Use Case

| Use Case | Fungsi Utama |
|---|---|
| `CreateCourseReviewUsecase` | membuat review baru |
| `GetCourseReviewsUsecase` | daftar review aktif pada course |
| `GetStudentCourseReviewUsecase` | review aktif milik student pada course |
| `UpdateCourseReviewUsecase` | memperbarui review aktif student |
| `DeleteCourseReviewUsecase` | soft delete review student |
| `RestoreCourseReviewUsecase` | restore review student yang terhapus |
| `GetCourseReviewDetailUsecase` | detail satu review *(ditunda)* |

---

# Catatan Teknis Repository

Repository `CourseReview` perlu sadar bahwa review aktif dan review terhapus adalah dua hal berbeda.

Method yang penting:
- `findActiveByStudentAndCourse(...)`
- `findDeletedByStudentAndCourse(...)`
- `getActiveByCourseId(...)`
- `countActiveByCourseId(...)`
- `getActiveAverageRatingByCourseId(...)`

Semua perhitungan summary rating harus hanya memakai review aktif.

---

# Catatan Penutup

Modul CourseReview yang sudah dibuat sekarang sudah cukup lengkap untuk kebutuhan bisnis utama:

- student bisa membuat review
- course bisa menampilkan review aktif
- FE bisa tahu apakah student sudah review atau belum
- student bisa mengubah review
- student bisa menghapus review secara soft delete
- student bisa mengembalikan review yang dihapus

Yang paling penting untuk diingat:
- CourseReview bergantung pada Enrollment completion
- LessonProgress completion menjadi fondasi agar enrollment bisa `completed`
- Review aktif memengaruhi `courses.rating_count` dan `courses.rating_avg`
- Soft delete custom `is_delete` harus selalu diperhatikan di query dan perhitungan rating
