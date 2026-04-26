# Analisis Kebutuhan Sistem LMS - Eduka

## 1. Pendahuluan

### 1.1 Latar Belakang

Perkembangan pembelajaran digital mendorong kebutuhan terhadap sistem yang mampu mengelola proses belajar secara terstruktur, mulai dari pengelolaan course, materi pembelajaran, enrollment student, progress belajar, hingga feedback terhadap course.

Dalam proses pembelajaran online, instructor membutuhkan sistem yang dapat membantu mereka membuat, mengatur, mempublikasikan, dan memantau performa course. Di sisi lain, student membutuhkan sistem yang memudahkan mereka menemukan course, melakukan enrollment atau pembelian course, mempelajari lesson secara bertahap, melihat progress belajar, menyelesaikan course, dan memberikan review setelah pembelajaran selesai.

Berdasarkan kebutuhan tersebut, dikembangkan sistem **Eduka**, yaitu Learning Management System berbasis web yang berfokus pada dua aktor utama: **Instructor** dan **Student**. Sistem ini dikembangkan sebagai capstone project Fullstack Web Development dengan backend API berbasis Laravel, pendekatan Clean Architecture, dan Domain-Driven Design. Sistem juga akan dipresentasikan melalui use case, flowchart, serta frontend yang diuji secara penuh dengan backend API.

---

### 1.2 Tujuan Sistem

Tujuan utama sistem Eduka adalah menyediakan platform pembelajaran online yang terstruktur, modular, dan siap dikembangkan menjadi produk LMS yang lebih lengkap.

Secara khusus, sistem ini bertujuan untuk:

1. Menyediakan sistem autentikasi dan otorisasi berbasis role.
2. Memungkinkan instructor membuat dan mengelola course.
3. Memungkinkan instructor mengelola category dan lesson pada course.
4. Memungkinkan student melihat daftar course yang tersedia.
5. Memungkinkan student melakukan enrollment atau pembelian course.
6. Mengintegrasikan proses pembelian course dengan payment gateway pihak ketiga.
7. Mencatat kepemilikan course melalui data enrollment.
8. Mengontrol akses student terhadap course berdasarkan enrollment valid.
9. Mencatat progress belajar student per lesson.
10. Menyelesaikan course secara otomatis ketika semua lesson selesai.
11. Memungkinkan student memberikan review dan rating setelah course selesai.
12. Menyediakan dashboard dasar untuk student dan instructor.
13. Menyediakan struktur backend yang modular agar mudah dikembangkan.
14. Mendukung integrasi frontend untuk kebutuhan testing fitur end-to-end.

---

### 1.3 Manfaat Sistem

#### Manfaat untuk Student

1. Student dapat menemukan dan mengikuti course secara online.
2. Student dapat mengakses course yang telah dibeli atau dienroll.
3. Student dapat belajar secara bertahap melalui lesson.
4. Student dapat melihat progress belajar dengan lebih jelas.
5. Student dapat mengetahui status course yang sedang berjalan atau sudah selesai.
6. Student dapat memberikan feedback melalui review dan rating.

#### Manfaat untuk Instructor

1. Instructor dapat membuat dan mengelola course.
2. Instructor dapat mengatur lesson dan urutan materi.
3. Instructor dapat mengelompokkan course berdasarkan category.
4. Instructor dapat melihat student yang melakukan enrollment.
5. Instructor dapat melihat review dari student.
6. Instructor dapat memantau performa course melalui dashboard.
7. Instructor dapat memperoleh monetisasi dari course berbayar.

#### Manfaat untuk Pengembangan Produk

1. Sistem memiliki struktur backend yang modular.
2. Sistem dapat dikembangkan untuk kebutuhan payment, dashboard, analytics, certificate, dan fitur lanjutan lainnya.
3. Sistem dapat dijadikan fondasi produk LMS komersial.
4. Sistem dapat dipresentasikan secara jelas melalui use case, flowchart, dan frontend testing.

---

## 2. Gambaran Umum Sistem

### 2.1 Deskripsi Sistem

**Eduka** adalah sistem Learning Management System berbasis web yang memungkinkan instructor membuat dan mengelola course, sementara student dapat membeli atau enroll ke course, mempelajari lesson, melihat progress belajar, menyelesaikan course, dan memberikan review.

Sistem ini dikembangkan dengan pendekatan backend API agar dapat diintegrasikan dengan frontend web. Backend dibangun menggunakan Laravel dengan pendekatan Clean Architecture dan Domain-Driven Design, sehingga logika bisnis dipisahkan ke dalam beberapa layer seperti HTTP Controller, Application Use Case, Domain, Repository Interface, dan Infrastructure Repository.

---

### 2.2 Bentuk Project yang Dipresentasikan

Project Eduka akan dipresentasikan dalam bentuk:

1. **Backend API**
   - Backend Laravel sebagai pusat business logic dan pengelolaan data.
   - API digunakan untuk mendukung fitur LMS seperti authentication, course, lesson, enrollment, progress, review, dashboard, dan payment.

2. **Use Case**
   - Use case digunakan untuk menjelaskan interaksi aktor dengan sistem.
   - Use case akan difokuskan pada role Instructor dan Student.

3. **Flowchart / Activity Diagram**
   - Diagram digunakan untuk menjelaskan alur proses utama.
   - Flow utama yang akan dijelaskan:
     - Instructor membuat dan mempublikasikan course.
     - Student membeli/enroll course dan belajar sampai selesai.
     - Student memberikan review setelah course selesai.

4. **Frontend Full Testing**
   - Frontend akan digunakan untuk menguji fitur yang sudah terhubung dengan backend.
   - Testing dilakukan untuk memastikan flow utama dapat berjalan secara end-to-end.

---

### 2.3 Aktor Sistem

Sistem Eduka memiliki dua aktor utama.

#### 2.3.1 Instructor

Instructor adalah user yang berperan sebagai pembuat dan pengelola course.

Tanggung jawab utama instructor:

1. Membuat course.
2. Mengedit course.
3. Menghapus course.
4. Mengatur status course.
5. Menambahkan category pada course.
6. Membuat dan mengelola lesson.
7. Mengatur urutan lesson.
8. Melihat student yang melakukan enrollment.
9. Melihat review student.
10. Melihat performa course melalui dashboard.
11. Memonetisasi course.

#### 2.3.2 Student

Student adalah user yang berperan sebagai peserta course.

Tanggung jawab utama student:

1. Melihat daftar course.
2. Membeli atau enroll ke course.
3. Melihat course yang dimiliki.
4. Membuka detail course.
5. Mempelajari lesson.
6. Memulai lesson.
7. Menyelesaikan lesson.
8. Melihat progress belajar.
9. Menyelesaikan course.
10. Memberikan review dan rating setelah course selesai.
11. Mengubah, menghapus, atau memulihkan review.

---

### 2.4 Persona Pengguna

#### General Learner / Student

Karakteristik:

1. Berusia sekitar 17-40 tahun.
2. Dapat berupa pelajar, mahasiswa, fresh graduate, atau pekerja.
3. Membutuhkan akses materi pembelajaran secara fleksibel.
4. Membutuhkan progress belajar yang jelas.
5. Membutuhkan tampilan sistem yang sederhana dan mudah digunakan.

Kebutuhan utama:

1. Melihat course yang tersedia.
2. Membeli atau enroll course.
3. Mengakses course yang dimiliki.
4. Belajar secara bertahap.
5. Melihat progress belajar.
6. Menyelesaikan course.
7. Memberikan review terhadap course.

#### Instructor / Content Creator

Karakteristik:

1. Dapat berupa pengajar, mentor, trainer, atau praktisi.
2. Membutuhkan sistem untuk mengelola course digital.
3. Membutuhkan cara untuk memantau student.
4. Membutuhkan fitur monetisasi course.

Kebutuhan utama:

1. Membuat course.
2. Mengelola lesson.
3. Mengatur category course.
4. Melihat student yang mengikuti course.
5. Melihat review dan rating.
6. Melihat performa course.
7. Menerima pendapatan dari course.

---

## 3. Analisis Masalah

### 3.1 Permasalahan yang Dihadapi

Beberapa permasalahan yang ingin diselesaikan oleh sistem Eduka adalah:

1. **Pengelolaan course belum terstruktur**
   - Instructor membutuhkan sistem untuk membuat, mengedit, menghapus, dan mempublikasikan course secara terorganisir.

2. **Materi pembelajaran perlu dipecah menjadi unit yang jelas**
   - Course perlu memiliki lesson agar proses belajar lebih terarah dan mudah diikuti oleh student.

3. **Student membutuhkan akses course yang jelas**
   - Student perlu mengetahui course mana yang sudah dimiliki dan dapat diakses.

4. **Sistem perlu mengontrol hak akses student**
   - Tidak semua student boleh mengakses semua course. Akses perlu berdasarkan enrollment atau pembelian yang valid.

5. **Progress belajar perlu dicatat**
   - Student membutuhkan informasi lesson mana yang belum dimulai, sedang berjalan, atau sudah selesai.

6. **Course completion perlu dihitung otomatis**
   - Sistem perlu menentukan kapan student telah menyelesaikan course berdasarkan lesson yang diselesaikan.

7. **Feedback course perlu dikelola**
   - Review dan rating diperlukan untuk menunjukkan kualitas course dan membantu calon student lain dalam memilih course.

8. **Instructor membutuhkan informasi performa course**
   - Instructor perlu mengetahui jumlah student, rating, review, dan potensi pendapatan dari course.

9. **Pembelian course membutuhkan integrasi payment**
   - Untuk mendukung monetisasi, sistem perlu dapat diintegrasikan dengan payment gateway pihak ketiga.

10. **Project perlu mudah dipresentasikan**
   - Sebagai capstone project, sistem perlu didukung oleh analisis kebutuhan, use case, flowchart, dan dokumentasi fitur yang jelas.

---

### 3.2 Solusi yang Ditawarkan

Eduka menawarkan solusi berupa sistem LMS berbasis web dengan fitur utama:

1. Autentikasi user dan pengelolaan role.
2. Manajemen course oleh instructor.
3. Manajemen category untuk pengelompokan course.
4. Manajemen lesson sebagai unit pembelajaran.
5. Enrollment sebagai dasar kepemilikan dan akses course.
6. Integrasi payment gateway pihak ketiga untuk pembelian course.
7. Lesson progress untuk mencatat aktivitas belajar student.
8. Course completion otomatis berdasarkan penyelesaian lesson.
9. Course review sebagai media feedback student.
10. Dashboard dasar untuk student dan instructor.
11. Backend API modular yang dapat diintegrasikan dengan frontend.

---

## 4. Ruang Lingkup Sistem

### 4.1 Scope MVP

Fitur yang masuk ke dalam MVP Eduka adalah:

1. Authentication
2. Role-based access untuk Student dan Instructor
3. Course Management
4. Category Management
5. Lesson Management
6. Enrollment
7. Payment Gateway Integration
8. Lesson Progress
9. Course Review
10. Basic Dashboard
11. Frontend integration testing dengan backend API

---

### 4.2 Modul Utama Sistem

#### 4.2.1 Authentication

Modul authentication digunakan untuk mengelola akses user ke sistem.

Fitur utama:

1. Register.
2. Login.
3. Logout.
4. Profile.
5. Auth berbasis token.

#### 4.2.2 Course

Modul course digunakan oleh instructor untuk mengelola kelas.

Fitur utama:

1. Create course.
2. Update course.
3. Delete course.
4. Restore course.
5. Force delete course.
6. Get list courses.
7. Get detail course.
8. Update status course.
9. Attach category ke course.
10. Create course beserta lessons.

#### 4.2.3 Category

Modul category digunakan untuk mengelompokkan course.

Fitur utama:

1. Create category.
2. Get list categories.
3. Get detail category.
4. Update category.
5. Delete category.
6. Menghubungkan category dengan course.

#### 4.2.4 Lesson

Modul lesson digunakan untuk mengelola materi pembelajaran dalam course.

Fitur utama:

1. Create lesson.
2. Update lesson.
3. Delete lesson.
4. Restore lesson.
5. Force delete lesson.
6. Mengatur urutan lesson melalui position.
7. Menghubungkan lesson dengan course.

#### 4.2.5 Enrollment

Modul enrollment digunakan untuk mencatat kepemilikan course dan mengatur akses student.

Fitur utama:

1. Student enroll ke course.
2. Student melihat daftar enrollment miliknya.
3. Instructor melihat enrollment pada course miliknya.
4. Melihat detail enrollment.
5. Mengecek akses student terhadap course.
6. Menyelesaikan enrollment.
7. Membatalkan enrollment.
8. Menjadi dasar hak akses belajar student.

#### 4.2.6 Payment Gateway Integration

Modul payment digunakan untuk mendukung pembelian course berbayar.

Fitur utama:

1. Student melakukan pembelian course.
2. Sistem membuat transaksi pembayaran.
3. Sistem mengirim data transaksi ke payment gateway pihak ketiga.
4. Sistem menerima status pembayaran.
5. Sistem membuat atau mengaktifkan enrollment setelah pembayaran berhasil.
6. Sistem menangani pembayaran gagal, pending, atau expired.
7. Sistem mendukung pencatatan pendapatan instructor.

Catatan:
Payment masuk ke dalam MVP, tetapi integrasi teknis dilakukan melalui payment gateway pihak ketiga.

#### 4.2.7 Lesson Progress

Modul lesson progress digunakan untuk mencatat aktivitas belajar student.

Fitur utama:

1. Student memulai lesson.
2. Student menyelesaikan lesson.
3. Sistem mencatat status progress lesson.
4. Sistem menampilkan progress student pada course.
5. Sistem menampilkan ringkasan progress course.
6. Sistem memperbarui last accessed lesson.
7. Sistem menyelesaikan enrollment otomatis jika semua lesson selesai.

#### 4.2.8 Course Review

Modul course review digunakan untuk mengelola rating dan komentar student.

Fitur utama:

1. Student membuat review.
2. Student melihat review pada course.
3. Student melihat review miliknya.
4. Student mengubah review.
5. Student menghapus review.
6. Student memulihkan review.
7. Sistem menghitung ulang rating course.

#### 4.2.9 Basic Dashboard

Dashboard digunakan untuk memberikan ringkasan informasi kepada student dan instructor.

Student Dashboard:

1. Total enrolled courses.
2. Course aktif.
3. Course completed.
4. Progress terbaru.
5. Continue learning.

Instructor Dashboard:

1. Total course dibuat.
2. Total enrollment.
3. Total review.
4. Total rating.
5. Total pendapatan atau monetisasi course.

Catatan:
Backend dashboard sudah dibuat atau sedang disiapkan dari sisi API. UI dashboard dan integrasi frontend masih menjadi bagian tambahan MVP yang perlu diselesaikan.

---

### 4.3 Batasan Sistem

Batasan sistem pada tahap MVP:

1. Sistem berfokus pada role Student dan Instructor.
2. Admin panel belum menjadi fokus utama.
3. Payment gateway menggunakan layanan pihak ketiga, bukan sistem pembayaran internal penuh.
4. Certificate belum masuk ke MVP utama.
5. Forum diskusi belum masuk ke MVP utama.
6. Quiz dan assignment belum masuk ke MVP utama.
7. Advanced analytics belum masuk ke MVP utama.
8. Video streaming management belum menjadi fokus utama.
9. Frontend difokuskan untuk testing fitur utama yang terhubung dengan backend.

---

## 5. Kebutuhan Pengguna

### 5.1 Kebutuhan Instructor

Instructor membutuhkan sistem yang dapat:

1. Mengelola akun dan login ke sistem.
2. Membuat course baru.
3. Mengubah data course.
4. Menghapus course.
5. Mengatur status course menjadi draft, published, atau archived.
6. Menghubungkan course dengan category.
7. Membuat lesson di dalam course.
8. Mengubah lesson.
9. Menghapus lesson.
10. Mengatur urutan lesson.
11. Melihat daftar student yang membeli atau enroll ke course.
12. Melihat review dan rating dari student.
13. Melihat ringkasan performa course.
14. Melihat pendapatan dari course.
15. Mengelola course agar siap dipublikasikan.

---

### 5.2 Kebutuhan Student

Student membutuhkan sistem yang dapat:

1. Melakukan register dan login.
2. Melihat daftar course yang tersedia.
3. Melihat detail course.
4. Membeli atau enroll course.
5. Melakukan pembayaran course melalui payment gateway.
6. Melihat course yang sudah dimiliki.
7. Membuka detail course yang sudah dimiliki.
8. Melihat daftar lesson.
9. Memulai lesson.
10. Menyelesaikan lesson.
11. Melihat progress belajar.
12. Melanjutkan lesson terakhir yang diakses.
13. Menyelesaikan course.
14. Memberikan review setelah course selesai.
15. Mengubah review.
16. Menghapus review.
17. Memulihkan review.
18. Melihat ringkasan dashboard belajar.

---

## 6. Kebutuhan Fungsional

### 6.1 Authentication dan User Role

| Kode | Kebutuhan Fungsional |
|---|---|
| FR-01 | Sistem harus menyediakan fitur register user. |
| FR-02 | Sistem harus menyediakan fitur login user. |
| FR-03 | Sistem harus menyediakan fitur logout user. |
| FR-04 | Sistem harus menyediakan fitur melihat profile user. |
| FR-05 | Sistem harus membedakan user berdasarkan role Student dan Instructor. |
| FR-06 | Sistem harus membatasi akses fitur berdasarkan role user. |

---

### 6.2 Course Management

| Kode | Kebutuhan Fungsional |
|---|---|
| FR-07 | Instructor dapat membuat course baru. |
| FR-08 | Instructor dapat mengubah data course miliknya. |
| FR-09 | Instructor dapat menghapus course miliknya. |
| FR-10 | Instructor dapat melakukan restore course yang dihapus. |
| FR-11 | Instructor dapat melakukan force delete course jika diperlukan. |
| FR-12 | Instructor dapat melihat daftar course miliknya. |
| FR-13 | Student dapat melihat daftar course yang tersedia. |
| FR-14 | User dapat melihat detail course. |
| FR-15 | Instructor dapat mengubah status course menjadi draft, published, atau archived. |
| FR-16 | Sistem hanya menampilkan course published kepada student pada daftar course publik. |

---

### 6.3 Category Management

| Kode | Kebutuhan Fungsional |
|---|---|
| FR-17 | Instructor dapat membuat category. |
| FR-18 | Instructor dapat melihat daftar category. |
| FR-19 | Instructor dapat melihat detail category. |
| FR-20 | Instructor dapat mengubah category. |
| FR-21 | Instructor dapat menghapus category. |
| FR-22 | Instructor dapat menghubungkan category dengan course. |
| FR-23 | Sistem dapat menggunakan category untuk membantu pengelompokan course. |

---

### 6.4 Lesson Management

| Kode | Kebutuhan Fungsional |
|---|---|
| FR-24 | Instructor dapat membuat lesson pada course miliknya. |
| FR-25 | Instructor dapat mengubah lesson pada course miliknya. |
| FR-26 | Instructor dapat menghapus lesson pada course miliknya. |
| FR-27 | Instructor dapat melakukan restore lesson. |
| FR-28 | Instructor dapat melakukan force delete lesson. |
| FR-29 | Instructor dapat mengatur urutan lesson menggunakan position. |
| FR-30 | Sistem harus menampilkan lesson berdasarkan urutan position. |
| FR-31 | Student dapat melihat daftar lesson pada course yang dimiliki. |

---

### 6.5 Enrollment

| Kode | Kebutuhan Fungsional |
|---|---|
| FR-32 | Student dapat melakukan enrollment ke course. |
| FR-33 | Sistem harus mencegah student melakukan enrollment lebih dari satu kali pada course yang sama. |
| FR-34 | Sistem harus memeriksa quota course sebelum enrollment dibuat. |
| FR-35 | Sistem harus mencatat status enrollment. |
| FR-36 | Sistem harus mendukung status enrollment active, completed, dan cancelled. |
| FR-37 | Student dapat melihat daftar course yang sudah dienroll. |
| FR-38 | Instructor dapat melihat student yang enroll pada course miliknya. |
| FR-39 | Sistem harus menyediakan pengecekan akses student terhadap course. |
| FR-40 | Sistem harus dapat membatalkan enrollment. |
| FR-41 | Sistem harus dapat menyelesaikan enrollment ketika seluruh lesson selesai. |

---

### 6.6 Payment Gateway

| Kode | Kebutuhan Fungsional |
|---|---|
| FR-42 | Student dapat melakukan pembelian course berbayar. |
| FR-43 | Sistem harus membuat transaksi pembayaran untuk course berbayar. |
| FR-44 | Sistem harus mengirim detail transaksi ke payment gateway pihak ketiga. |
| FR-45 | Sistem harus menerima callback atau status pembayaran dari payment gateway. |
| FR-46 | Sistem harus mengaktifkan enrollment jika pembayaran berhasil. |
| FR-47 | Sistem tidak boleh memberikan akses course jika pembayaran belum berhasil, kecuali course gratis atau aturan khusus ditentukan. |
| FR-48 | Sistem harus menyimpan status pembayaran seperti pending, paid, failed, expired, atau cancelled. |
| FR-49 | Sistem harus mendukung pencatatan pendapatan instructor dari course berbayar. |

---

### 6.7 Lesson Progress

| Kode | Kebutuhan Fungsional |
|---|---|
| FR-50 | Student dapat memulai lesson pada course yang sudah dienroll. |
| FR-51 | Student dapat menyelesaikan lesson. |
| FR-52 | Sistem harus mencatat progress lesson per student. |
| FR-53 | Sistem harus mendukung status progress not_started, in_progress, dan completed. |
| FR-54 | Sistem harus menganggap lesson yang belum memiliki progress sebagai not_started. |
| FR-55 | Sistem harus menampilkan progress student pada course tertentu. |
| FR-56 | Sistem harus menampilkan ringkasan progress course. |
| FR-57 | Sistem harus memperbarui last accessed lesson. |
| FR-58 | Sistem harus otomatis menyelesaikan enrollment ketika semua lesson dalam course selesai. |

---

### 6.8 Course Review

| Kode | Kebutuhan Fungsional |
|---|---|
| FR-59 | Student dapat membuat review pada course yang sudah selesai. |
| FR-60 | Sistem harus mencegah student membuat review jika enrollment belum completed. |
| FR-61 | Sistem harus mencegah student memiliki lebih dari satu review aktif pada course yang sama. |
| FR-62 | Student dapat melihat review pada course. |
| FR-63 | Student dapat melihat review miliknya pada course tertentu. |
| FR-64 | Student dapat mengubah review miliknya. |
| FR-65 | Student dapat menghapus review miliknya. |
| FR-66 | Student dapat memulihkan review miliknya. |
| FR-67 | Sistem harus menghitung ulang rating_count dan rating_avg saat review dibuat, diubah, dihapus, atau dipulihkan. |

---

### 6.9 Dashboard

| Kode | Kebutuhan Fungsional |
|---|---|
| FR-68 | Sistem harus menyediakan dashboard student. |
| FR-69 | Dashboard student harus menampilkan total enrolled courses. |
| FR-70 | Dashboard student harus menampilkan jumlah course active. |
| FR-71 | Dashboard student harus menampilkan jumlah course completed. |
| FR-72 | Dashboard student harus menampilkan progress terbaru atau continue learning. |
| FR-73 | Sistem harus menyediakan dashboard instructor. |
| FR-74 | Dashboard instructor harus menampilkan total course yang dibuat. |
| FR-75 | Dashboard instructor harus menampilkan total enrollment. |
| FR-76 | Dashboard instructor harus menampilkan total review. |
| FR-77 | Dashboard instructor harus menampilkan total rating. |
| FR-78 | Dashboard instructor harus menampilkan total pendapatan atau monetisasi course. |

---

### 6.10 Frontend Integration Testing

| Kode | Kebutuhan Fungsional |
|---|---|
| FR-79 | Frontend harus dapat melakukan login dan menyimpan token autentikasi. |
| FR-80 | Frontend harus dapat menampilkan daftar course dari backend API. |
| FR-81 | Frontend harus dapat menampilkan detail course. |
| FR-82 | Frontend harus dapat melakukan flow enrollment atau pembelian course. |
| FR-83 | Frontend harus dapat menampilkan course milik student. |
| FR-84 | Frontend harus dapat menjalankan start lesson dan complete lesson. |
| FR-85 | Frontend harus dapat menampilkan progress belajar student. |
| FR-86 | Frontend harus dapat menjalankan create, update, delete, dan restore review. |
| FR-87 | Frontend harus dapat menampilkan dashboard dasar berdasarkan data dari backend. |

---

## 7. Kebutuhan Non-Fungsional

| Kode | Kebutuhan Non-Fungsional |
|---|---|
| NFR-01 | Sistem harus menggunakan autentikasi berbasis token untuk endpoint private. |
| NFR-02 | Sistem harus membatasi akses fitur berdasarkan role Student dan Instructor. |
| NFR-03 | Sistem harus menjaga konsistensi data antara course, enrollment, lesson progress, review, payment, dan dashboard. |
| NFR-04 | Sistem harus memiliki response API yang konsisten agar mudah digunakan frontend. |
| NFR-05 | Sistem harus memiliki struktur kode modular agar mudah dikembangkan. |
| NFR-06 | Sistem harus memisahkan business logic dari controller. |
| NFR-07 | Sistem harus menggunakan repository interface untuk memisahkan domain dan infrastructure. |
| NFR-08 | Sistem harus dapat dikembangkan untuk fitur lanjutan seperti certificate, admin panel, analytics, quiz, dan discussion forum. |
| NFR-09 | Sistem harus dapat menangani validasi input dan error response dengan jelas. |
| NFR-10 | Sistem harus menjaga keamanan data user dan transaksi pembayaran. |
| NFR-11 | Sistem harus dapat diintegrasikan dengan payment gateway pihak ketiga. |
| NFR-12 | Sistem harus dapat digunakan oleh frontend untuk testing end-to-end fitur utama. |
| NFR-13 | Sistem harus menjaga performa query untuk data dashboard, list course, enrollment, dan progress. |
| NFR-14 | Sistem harus menjaga integritas rating course ketika review berubah. |
| NFR-15 | Sistem harus mudah dipahami melalui dokumentasi use case, flowchart, dan README presentasi. |

---

## 8. Business Rules

### 8.1 Business Rules Authentication

1. User harus login untuk mengakses fitur private.
2. User memiliki role yang menentukan hak akses.
3. Fitur instructor hanya boleh diakses oleh instructor.
4. Fitur student hanya boleh diakses oleh student.

---

### 8.2 Business Rules Course

1. Course dibuat oleh instructor.
2. Instructor hanya boleh mengelola course miliknya.
3. Course memiliki status draft, published, atau archived.
4. Student hanya dapat melihat atau mengakses course yang tersedia sesuai aturan sistem.
5. Course dapat memiliki banyak lesson.
6. Course dapat memiliki banyak category.
7. Course memiliki quota untuk membatasi jumlah enrollment.
8. Course menyimpan rating_avg dan rating_count sebagai ringkasan review aktif.

---

### 8.3 Business Rules Lesson

1. Lesson harus terhubung dengan course.
2. Lesson memiliki urutan berdasarkan position.
3. Urutan lesson digunakan untuk menampilkan materi secara terstruktur.
4. Lesson yang dihapus dapat direstore jika menggunakan soft delete.
5. Student hanya dapat mengakses lesson jika memiliki enrollment valid pada course tersebut.

---

### 8.4 Business Rules Enrollment

1. Hanya student yang dapat melakukan enrollment.
2. Student tidak boleh enroll lebih dari satu kali pada course yang sama.
3. Enrollment tunduk pada quota course.
4. Enrollment digunakan sebagai dasar kepemilikan course.
5. Enrollment digunakan sebagai dasar validasi akses belajar.
6. Enrollment memiliki status active, completed, dan cancelled.
7. Enrollment active berarti student masih mengikuti course.
8. Enrollment completed berarti student telah menyelesaikan course.
9. Enrollment cancelled berarti enrollment dibatalkan.
10. Enrollment completed tetap dihitung dalam enrolled_count.
11. Enrollment cancelled tidak dihitung dalam enrolled_count.
12. Enrollment dapat berubah menjadi completed secara otomatis dari lesson progress.

---

### 8.5 Business Rules Payment

1. Course berbayar harus melalui proses payment sebelum student mendapatkan akses penuh.
2. Sistem harus membuat transaksi pembayaran sebelum mengarahkan student ke payment gateway.
3. Payment gateway pihak ketiga menjadi pihak yang memproses pembayaran.
4. Sistem hanya boleh mengaktifkan enrollment setelah pembayaran berhasil.
5. Jika pembayaran pending, akses course belum diberikan.
6. Jika pembayaran failed, expired, atau cancelled, enrollment tidak boleh aktif.
7. Sistem harus menyimpan status pembayaran untuk kebutuhan audit dan tracking.
8. Pendapatan instructor dihitung berdasarkan course yang berhasil dibeli.
9. Payment tidak boleh membuat duplicate enrollment untuk student dan course yang sama.

---

### 8.6 Business Rules Lesson Progress

1. Lesson progress hanya berlaku untuk student.
2. Student harus memiliki enrollment valid untuk membuat atau mengubah progress.
3. Progress lesson bersifat unik untuk kombinasi student dan lesson.
4. Jika progress belum ada, status default dianggap not_started.
5. Student dapat memulai lesson sehingga status menjadi in_progress.
6. Student dapat menyelesaikan lesson sehingga status menjadi completed.
7. Sistem harus menghitung progress course berdasarkan jumlah lesson yang completed.
8. Jika seluruh lesson dalam course completed, sistem otomatis mengubah enrollment menjadi completed.
9. Last accessed lesson digunakan untuk mendukung fitur continue learning.

---

### 8.7 Business Rules Course Review

1. Hanya student yang dapat membuat review.
2. Student hanya dapat membuat review jika enrollment pada course sudah completed.
3. Student hanya boleh memiliki satu review aktif pada satu course.
4. Review dapat diubah oleh pemilik review.
5. Review dapat dihapus oleh pemilik review.
6. Review menggunakan soft delete custom melalui field is_delete.
7. Review yang dihapus tidak ikut dihitung dalam rating_count dan rating_avg.
8. Review yang dipulihkan kembali ikut dihitung dalam rating_count dan rating_avg.
9. Setiap create, update, delete, dan restore review harus memperbarui summary rating course.

---

### 8.8 Business Rules Dashboard

1. Dashboard student hanya menampilkan data milik student yang sedang login.
2. Dashboard instructor hanya menampilkan data dari course milik instructor yang sedang login.
3. Data dashboard student dihitung dari enrollment dan lesson progress.
4. Data dashboard instructor dihitung dari course, enrollment, review, rating, dan payment.
5. Dashboard harus menampilkan data ringkas dan mudah dipahami.
6. Dashboard tidak boleh menampilkan data user lain yang tidak berhubungan.

---

## 9. Asumsi Sistem

Asumsi yang digunakan dalam analisis kebutuhan ini:

1. Sistem memiliki dua role utama: Student dan Instructor.
2. User hanya memiliki satu role utama dalam sistem.
3. Backend API menjadi sumber utama business logic.
4. Frontend digunakan untuk menguji fitur secara end-to-end.
5. Payment gateway yang digunakan adalah layanan pihak ketiga.
6. Enrollment menjadi representasi kepemilikan course.
7. Course dapat bersifat berbayar.
8. Dashboard backend sudah dibuat atau sedang dipersiapkan, sedangkan UI dan integrasi frontend dashboard masih perlu dilanjutkan.
9. Admin panel belum menjadi prioritas MVP.
10. Certificate, quiz, forum diskusi, dan advanced analytics masuk ke pengembangan lanjutan.
11. Review hanya dapat dibuat setelah student menyelesaikan course.
12. Rating course dihitung dari review aktif.
13. Sistem dirancang agar dapat dikembangkan menjadi produk LMS komersial.

---

## 10. Status Implementasi Berdasarkan Analisis

| Area | Status |
|---|---|
| Authentication | Backend sudah dibuat |
| Course | Backend sudah dibuat |
| Category | Backend sudah dibuat |
| Lesson | Backend sudah dibuat |
| Enrollment | Backend sudah dibuat |
| Lesson Progress | Backend sudah dibuat |
| Course Review | Backend sudah dibuat |
| Basic Dashboard | Backend sudah dibuat / tambahan MVP, UI dan integrasi FE belum selesai |
| Payment Gateway | Masuk MVP, akan diintegrasikan dengan pihak ketiga |
| Frontend Integration Testing | Masuk kebutuhan presentasi |
| Use Case Documentation | Perlu dibuat setelah analisis kebutuhan |
| Flowchart / Activity Diagram | Perlu dibuat setelah use case summary |
| README Presentasi | Perlu dirapikan setelah diagram dan fitur selesai dipetakan |

---

## 11. Prioritas Lanjutan Setelah Analisis Kebutuhan

Setelah dokumen analisis kebutuhan selesai, prioritas berikutnya adalah:

1. Membuat Use Case Summary per Role.
2. Membuat Use Case Diagram.
3. Membuat Activity Diagram untuk tiga flow utama.
4. Merapikan README presentasi.
5. Menyusun fitur selesai vs roadmap.

Tiga flow utama yang perlu divisualisasikan:

1. Instructor membuat course sampai publish.
2. Student membeli/enroll course dan belajar sampai selesai.
3. Student memberikan review setelah course selesai.

---

## 12. Kesimpulan

Eduka adalah sistem LMS yang dirancang untuk mendukung proses pembelajaran online antara instructor dan student. Sistem ini tidak hanya berfokus pada CRUD data, tetapi juga mencakup alur bisnis utama LMS seperti course management, enrollment, payment, lesson progress, course completion otomatis, review, rating, dan dashboard.

Dari sisi backend, sistem sudah memiliki fondasi yang kuat melalui implementasi Authentication, Course, Category, Lesson, Enrollment, LessonProgress, dan CourseReview. Dengan tambahan payment gateway, dashboard, use case, flowchart, dan frontend integration testing, Eduka dapat dipresentasikan sebagai capstone project yang memiliki alur produk, business rule, dan struktur teknis yang jelas.

Dokumen analisis kebutuhan ini menjadi dasar untuk penyusunan use case summary, use case diagram, activity diagram, README presentasi, dan pemetaan fitur selesai vs roadmap.
