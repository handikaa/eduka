mkdir -p app/Domain/User/{Entities,Repositories,Exceptions} \
 app/Application/User/{DTOs,UseCases,Services} \
 app/Infrastructure/Persistence/Eloquent/{Models,Repositories} \
 app/Http/{Controllers/Api,Requests,Resources,Responses} && \

cat > app/Domain/README.md <<'EOF'

# Domain Layer

Layer ini berisi aturan bisnis inti.
Domain tidak boleh bergantung pada framework, database, atau HTTP.

Isi umum:

- Entities: representasi object bisnis
- Repositories: kontrak/interface akses data
- Exceptions: error yang terkait aturan bisnis

Contoh:

- UserEntity
- UserRepositoryInterface
- EmailAlreadyUsedException
  EOF

cat > app/Domain/User/Entities/README.md <<'EOF'

# Domain/User/Entities

Berisi entity domain untuk User.

Tugas:

- merepresentasikan object bisnis User
- tidak bergantung ke Eloquent atau Request Laravel

Contoh:

- UserEntity.php
  EOF

cat > app/Domain/User/Repositories/README.md <<'EOF'

# Domain/User/Repositories

Berisi interface repository untuk kebutuhan domain User.

Tugas:

- mendefinisikan kontrak akses data
- bukan implementasi database

Contoh:

- UserRepositoryInterface.php
  EOF

cat > app/Domain/User/Exceptions/README.md <<'EOF'

# Domain/User/Exceptions

Berisi exception yang terkait aturan bisnis User.

Tugas:

- mewakili pelanggaran business rule

Contoh:

- EmailAlreadyUsedException.php
- UserNotAllowedToDeleteException.php
  EOF

cat > app/Application/README.md <<'EOF'

# Application Layer

Layer ini berisi alur use case aplikasi.
Application mengatur bagaimana bisnis dijalankan, tetapi bukan detail teknisnya.

Isi umum:

- DTOs: object kirim data antar layer
- UseCases: alur fitur utama
- Services: service pendukung aplikasi

Contoh:

- CreateUserDto
- CreateUserUseCase
- PasswordHasherService
  EOF

cat > app/Application/User/DTOs/README.md <<'EOF'

# Application/User/DTOs

Berisi DTO untuk User.

Tugas:

- membawa data dari HTTP ke UseCase
- membuat parameter lebih rapi dan type-safe

Contoh:

- CreateUserDto.php
- UpdateUserDto.php
  EOF

cat > app/Application/User/UseCases/README.md <<'EOF'

# Application/User/UseCases

Berisi use case utama untuk fitur User.

Tugas:

- menjalankan alur bisnis per kebutuhan fitur
- memakai repository interface dari Domain

Contoh:

- CreateUserUseCase.php
- GetUserProfileUseCase.php
- UpdateUserUseCase.php
  EOF

cat > app/Application/User/Services/README.md <<'EOF'

# Application/User/Services

Berisi service pendukung untuk User.

Tugas:

- helper logic yang dipakai oleh use case
- bukan controller dan bukan model database

Contoh:

- PasswordHasherService.php
- UserTokenService.php
  EOF

cat > app/Infrastructure/README.md <<'EOF'

# Infrastructure Layer

Layer ini berisi implementasi teknis.
Infrastructure menangani detail framework, database, dan adapter.

Isi umum:

- Models: Eloquent model
- Repositories: implementasi repository interface

Contoh:

- User model Eloquent
- EloquentUserRepository
  EOF

cat > app/Infrastructure/Persistence/Eloquent/Models/README.md <<'EOF'

# Infrastructure/Persistence/Eloquent/Models

Berisi model Eloquent Laravel.

Tugas:

- representasi tabel database
- relasi antar tabel
- fillable, casts, scope teknis

Contoh:

- User.php
  EOF

cat > app/Infrastructure/Persistence/Eloquent/Repositories/README.md <<'EOF'

# Infrastructure/Persistence/Eloquent/Repositories

Berisi implementasi repository menggunakan Eloquent.

Tugas:

- menghubungkan Domain/Application dengan database
- mengimplementasikan interface repository dari Domain

Contoh:

- EloquentUserRepository.php
  EOF

cat > app/Http/README.md <<'EOF'

# Http Layer

Layer ini menangani komunikasi API/HTTP.
Http hanya menerima request, memanggil use case, lalu mengembalikan response.

Isi umum:

- Controllers/Api: endpoint controller
- Requests: validasi request
- Resources: transformasi output
- Responses: response wrapper/helper

Contoh:

- AuthController
- LoginRequest
- UserResource
- ApiResponse
  EOF

cat > app/Http/Controllers/Api/README.md <<'EOF'

# Http/Controllers/Api

Berisi controller API.

Tugas:

- menerima request
- memanggil use case
- mengembalikan response

Catatan:

- controller harus tipis
- jangan menaruh business logic besar di sini
  EOF

cat > app/Http/Requests/README.md <<'EOF'

# Http/Requests

Berisi Form Request untuk validasi input.

Tugas:

- validasi request dari client
- memisahkan validasi dari controller

Contoh:

- StoreUserRequest.php
- LoginRequest.php
  EOF

cat > app/Http/Resources/README.md <<'EOF'

# Http/Resources

Berisi Resource untuk membentuk output JSON.

Tugas:

- mengontrol field yang dikirim ke FE
- menjaga response tetap rapi dan konsisten

Contoh:

- UserResource.php
- AuthResource.php
  EOF

cat > app/Http/Responses/README.md <<'EOF'

# Http/Responses

Berisi helper/wrapper response API.

Tugas:

- standarisasi response success/error
- menjaga format JSON konsisten

Contoh format:
{
"success": true,
"message": "Success",
"data": {}
}
EOF

cat > app/ARCHITECTURE_NOTES.md <<'EOF'

# Laravel Clean Architecture / DDD Notes

Struktur yang digunakan:

app/
├── Domain/
│ └── User/
│ ├── Entities/
│ ├── Repositories/
│ └── Exceptions/
├── Application/
│ └── User/
│ ├── DTOs/
│ ├── UseCases/
│ └── Services/
├── Infrastructure/
│ └── Persistence/
│ └── Eloquent/
│ ├── Models/
│ └── Repositories/
└── Http/
├── Controllers/Api/
├── Requests/
├── Resources/
└── Responses/

Alur business flow backend:
Request -> Controller -> DTO -> UseCase -> Repository Interface -> Eloquent Repository -> Model -> Resource -> ApiResponse

Prinsip utama:

- Domain: aturan bisnis inti
- Application: alur use case
- Infrastructure: implementasi teknis
- Http: request/response API

Aturan penting:

- Controller harus tipis
- Validasi di Http/Requests
- Response konsisten di Http/Responses
- Output ke FE lewat Http/Resources
- Query database lewat repository implementation
- Business rule jangan ditaruh di controller
  EOF

echo "Struktur clean architecture berhasil dibuat."
