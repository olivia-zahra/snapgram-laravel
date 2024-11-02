<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
    // Mendefinisikan primary key kustom 'userID'
    // menggantikan primary key default 'id' di tabel users
    protected $primaryKey = 'userID';

    // Kolom-kolom yang bisa diisi secara massal
    // (mass assignable) melalui operasi create atau update
    protected $fillable = [
        'username',     // Menyimpan username pengguna
        'email',        // Menyimpan email pengguna
        'password',     // Menyimpan password pengguna (hashed)
        'namalengkap',  // Menyimpan nama lengkap pengguna
        'alamat'        // Menyimpan alamat pengguna
    ];

    // Menyembunyikan kolom password dan remember_token
    // ketika data model diubah menjadi array atau JSON
    protected $hidden = ['password', 'remember_token'];
}
