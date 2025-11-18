<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // <-- Pastikan ini ada
use App\Models\CoreEmployee;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids; // <-- Pastikan HasUuids juga ada di sini

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'core_users';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id'; // <-- Definisikan primary key secara eksplisit

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Definisikan relasi many-to-many ke Role.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'assoc_user_roles', 'user_id', 'role_id');
    }

    /**
 * Helper untuk mengambil data kelas yang diampu oleh Wali Kelas (Guru).
 * @param string|null $academicYear Tahun ajaran (cth: "2025/2026"). Jika null, akan ambil yang pertama ditemukan.
 * @return \App\Models\RefClass|null
 */
public function getWaliKelasClass($academicYear = null)
    {
        // 1. Pastikan user adalah guru
        if (!$this->roles->contains('code', 'guru')) {
            return null;
        }

        // 2. Cek apakah kolom class_id di core_users sudah diisi
        if (empty($this->class_id)) {
            // Jika kosong, berarti guru ini tidak ditugaskan sebagai wali kelas
            return null;
        }

        // 3. Tentukan tahun ajaran (Sama seperti sebelumnya, ini perlu dibuat dinamis nanti)
        $academicYear = $academicYear ?? '2025/2026'; // SEMENTARA

        // 4. Ambil kelas berdasarkan class_id dari user
        $class = RefClass::find($this->class_id);

        // 5. Validasi: Cek apakah kelas yang ditemukan sesuai tahun ajaran
        // Ini penting agar wali kelas tahun lalu tidak bisa mengedit kelas tahun sekarang
        if ($class && $class->academic_year == $academicYear) {
            return $class;
        }

        // Jika class_id ada tapi tahun ajarannya salah, tetap return null
        return null;
    }
    public function employee()
    {
        // user_id adalah foreign key di tabel core_employees
        return $this->hasOne(CoreEmployee::class, 'user_id');
    }
}