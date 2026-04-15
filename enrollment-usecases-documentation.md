# Enrollment Use Cases Documentation

Dokumentasi ini merangkum seluruh use case pada modul **Enrollment** di project LMS backend Laravel yang menggunakan pendekatan **Clean Architecture / DDD**.

Tujuan dokumen ini:
- menjadi pengingat struktur dan tanggung jawab setiap use case Enrollment
- menjaga konsistensi business rule
- menjadi referensi saat mengerjakan modul berikutnya, terutama **LessonProgress** dan **Review**

---

## Daftar Use Case Enrollment

1. `EnrollStudentUsecase`
2. `GetStudentEnrollmentsUsecase`
3. `GetCourseEnrollmentsUsecase`
4. `GetEnrollmentDetailUsecase`
5. `GetStudentEnrollmentByCourseUsecase`
6. `CheckStudentEnrollmentAccessUsecase`
7. `CompleteEnrollmentUsecase`
8. `CancelEnrollmentUsecase`

---

## Gambaran Umum Domain Enrollment

Enrollment merepresentasikan hubungan antara **student** dan **course**.

Enrollment bukan hanya tabel pivot biasa, tetapi entity bisnis yang memiliki lifecycle status:

- `active`
- `completed`
- `cancelled`

Aturan bisnis utama:
- hanya user dengan role `student` yang dapat melakukan enrollment
- student tidak boleh enroll ke course yang sama dua kali
- enrollment tunduk pada kuota course
- `completed` **tetap dihitung** dalam `enrolled_count`
- `cancelled` **tidak dihitung** dalam `enrolled_count`

Catatan penting:
- `CompleteEnrollmentUsecase` **tidak dipanggil manual oleh frontend**
- `CompleteEnrollmentUsecase` akan **dipicu otomatis dari flow LessonProgress**
  ketika semua lesson pada course telah selesai dipelajari oleh student

---

# 1. EnrollStudentUsecase

## Tujuan
Mendaftarkan student ke sebuah course.

## Kebutuhan bisnis
- hanya role `student` yang dapat enroll
- student tidak boleh enroll 2 kali ke course yang sama
- course harus ada
- kuota course harus masih tersedia
- saat enrollment berhasil:
  - status awal = `active`
  - `enrolled_at` diisi
  - `enrolled_count` course bertambah

## Input
- authenticated `student`
- `courseId`

## Output
- object `Enrollment`

## Dipakai untuk
- proses student membeli / masuk ke course

---

# 2. GetStudentEnrollmentsUsecase

## Tujuan
Mengambil daftar semua enrollment milik student yang sedang login.

## Kebutuhan bisnis
- hanya role `student` yang dapat melihat daftar enrollment miliknya
- hasil dapat dipaginasi

## Input
- authenticated `student`
- `perPage`

## Output
- paginated enrollments milik student

## Dipakai untuk
- halaman **My Courses**
- daftar course yang sudah dibeli / diikuti student

---

# 3. GetCourseEnrollmentsUsecase

## Tujuan
Mengambil daftar enrollment pada sebuah course tertentu untuk instructor pemilik course.

## Kebutuhan bisnis
- hanya role `instructor` yang dapat melihat daftar enrollment course
- course harus ada
- instructor hanya boleh melihat enrollment dari course miliknya sendiri
- hasil dapat dipaginasi

## Input
- authenticated `instructor`
- `courseId`
- `perPage`

## Output
- paginated enrollments pada course tersebut

## Dipakai untuk
- instructor melihat student yang membeli / mengikuti course miliknya

---

# 4. GetEnrollmentDetailUsecase

## Tujuan
Mengambil detail satu enrollment tertentu.

## Kebutuhan bisnis
- enrollment harus ada
- detail enrollment hanya boleh dilihat oleh:
  - student pemilik enrollment
  - instructor pemilik course dari enrollment tersebut

## Input
- authenticated user
- `enrollmentId`

## Output
- object `Enrollment`

## Dipakai untuk
- halaman detail enrollment
- detail student pada course tertentu
- detail ownership course oleh student

## Catatan
Untuk versi dasar, use case ini fokus pada detail enrollment.
Jika nanti dibutuhkan, response dapat ditambah **progress summary**:
- total lesson
- completed lesson
- progress percentage

Namun **detail full lesson progress** sebaiknya tetap menjadi tanggung jawab modul `LessonProgress`.

---

# 5. GetStudentEnrollmentByCourseUsecase

## Tujuan
Mengambil enrollment milik student pada satu course tertentu.

## Kebutuhan bisnis
- hanya role `student` yang dapat mengakses
- jika enrollment tidak ditemukan, hasil `null`
- kondisi "belum enroll" **bukan error**

## Input
- authenticated `student`
- `courseId`

## Output
- `Enrollment | null`

## Dipakai untuk
- halaman detail course di sisi student
- menentukan state tombol di frontend:
  - `Beli Course`
  - `Lanjut Belajar`
  - `Sudah Selesai`
  - `Enrollment Dibatalkan`

## Catatan
Use case ini fokus pada **data retrieval**, bukan access decision.

---

# 6. CheckStudentEnrollmentAccessUsecase

## Tujuan
Mengecek apakah student memiliki hak akses ke sebuah course berdasarkan enrollment yang valid.

## Kebutuhan bisnis
- hanya role `student` yang dapat dicek aksesnya
- enrollment harus ada agar student memiliki akses
- status `active` -> boleh akses
- status `completed` -> tetap boleh akses
- status `cancelled` -> tidak boleh akses

## Input
- authenticated `student`
- `courseId`

## Output
Hasil check berbentuk data seperti:
- `has_access`
- `status`
- `enrollment`
- `reason`

## Dipakai untuk
- validasi akses pada modul `LessonProgress`
- validasi awal pada modul `Review`
- guard logic sebelum student membuka / mengubah progress lesson

## Catatan penting
Use case ini adalah **fondasi lintas modul**.
Use case ini bukan endpoint utama user, tetapi **helper application/business logic** yang akan dipakai ulang.

---

# 7. CompleteEnrollmentUsecase

## Tujuan
Mengubah enrollment student menjadi `completed`.

## Kebutuhan bisnis
- hanya role `student` yang dapat menyelesaikan enrollment miliknya
- enrollment harus ada
- enrollment tidak boleh sudah `completed`
- enrollment tidak boleh `cancelled`
- semua lesson dalam course harus sudah selesai dipelajari student
- saat enrollment berubah ke `completed`:
  - `completed_at` diisi
  - `enrolled_count` **tidak berubah**

## Input
- authenticated `student`
- `courseId`

## Output
- object `Enrollment` yang telah diperbarui

## Dipakai untuk
- menandai bahwa student telah menyelesaikan seluruh course

## Catatan penting
Use case ini **tidak dipanggil manual oleh frontend**.

Use case ini akan dipicu **otomatis dari flow LessonProgress**, tepatnya ketika:
- student menyelesaikan lesson
- backend mengecek bahwa semua lesson pada course telah completed
- jika semua selesai, maka backend menjalankan `CompleteEnrollmentUsecase`

### Keterkaitan dengan modul berikutnya
Use case ini memiliki hubungan sangat erat dengan:
- `LessonProgress`
- `Review`

Karena:
- enrollment baru bisa `completed` jika seluruh lesson selesai
- review hanya valid jika proses belajar telah selesai

---

# 8. CancelEnrollmentUsecase

## Tujuan
Membatalkan enrollment student pada sebuah course.

## Kebutuhan bisnis
- hanya role `student` yang dapat membatalkan enrollment miliknya
- enrollment harus ada
- enrollment tidak boleh sudah `cancelled`
- enrollment yang sudah `completed` tidak dapat dibatalkan
- jika enrollment dibatalkan dari status `active`:
  - status menjadi `cancelled`
  - `enrolled_count` course berkurang

## Input
- authenticated `student`
- `courseId`

## Output
- object `Enrollment` yang telah diperbarui

## Dipakai untuk
- kebutuhan pembatalan enrollment jika memang aturan bisnis mengizinkan
- kemungkinan kebutuhan internal/admin di masa depan

## Catatan
Untuk saat ini, use case ini lebih aman diposisikan sebagai logic internal.
Belum tentu perlu langsung dibuka sebagai endpoint publik untuk student,
kecuali nanti ada aturan bisnis yang jelas tentang:
- refund
- pembatalan course
- unenroll sebelum mulai belajar

---

# Hubungan Enrollment dengan LessonProgress dan Review

## Kaitan dengan LessonProgress
Use case Enrollment yang paling erat hubungannya dengan LessonProgress adalah:

- `CheckStudentEnrollmentAccessUsecase`
- `CompleteEnrollmentUsecase`

### Pola integrasi yang direncanakan
1. student membuka / mengupdate progress lesson
2. sistem cek akses dengan `CheckStudentEnrollmentAccessUsecase`
3. lesson progress diubah
4. setelah lesson selesai, backend menghitung total lesson vs completed lesson
5. jika semua lesson selesai, backend memanggil `CompleteEnrollmentUsecase`

## Kaitan dengan Review
Use case Enrollment yang paling erat hubungannya dengan Review adalah:

- `CheckStudentEnrollmentAccessUsecase`
- `GetStudentEnrollmentByCourseUsecase`
- `CompleteEnrollmentUsecase`

### Kemungkinan rule review nanti
- student harus memiliki enrollment pada course
- student harus menyelesaikan proses belajar
- status enrollment kemungkinan harus `completed`

---

# Ringkasan Fungsi Setiap Use Case

| Use Case | Fungsi Utama |
|---|---|
| `EnrollStudentUsecase` | membuat enrollment baru |
| `GetStudentEnrollmentsUsecase` | daftar enrollment milik student |
| `GetCourseEnrollmentsUsecase` | daftar student pada course milik instructor |
| `GetEnrollmentDetailUsecase` | detail satu enrollment |
| `GetStudentEnrollmentByCourseUsecase` | enrollment student pada satu course |
| `CheckStudentEnrollmentAccessUsecase` | cek akses student ke course |
| `CompleteEnrollmentUsecase` | menandai enrollment selesai |
| `CancelEnrollmentUsecase` | membatalkan enrollment |

---

# Rekomendasi Saat Masuk ke Modul Berikutnya

## Saat mengerjakan LessonProgress
Pastikan mengingat:
- `CheckStudentEnrollmentAccessUsecase` dipakai sebelum student mengakses / mengubah progress lesson
- `CompleteEnrollmentUsecase` dipicu otomatis ketika semua lesson pada course telah selesai

## Saat mengerjakan Review
Pastikan mengingat:
- review berkaitan dengan enrollment
- student minimal harus punya enrollment pada course
- besar kemungkinan student harus sudah `completed` sebelum boleh review

---

# Catatan Penutup

Modul Enrollment yang sudah dibuat sekarang sudah cukup lengkap sebagai fondasi bisnis untuk LMS.

Yang paling penting untuk diingat ke depan:
- Enrollment adalah pusat kepemilikan dan akses student terhadap course
- LessonProgress akan bergantung pada validasi dari Enrollment
- Review akan bergantung pada status completion dari Enrollment
- `CompleteEnrollmentUsecase` adalah jembatan penting antara modul Enrollment dan LessonProgress
