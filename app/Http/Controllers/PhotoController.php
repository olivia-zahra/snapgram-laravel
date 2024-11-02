<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\Photo;
use App\Models\Comment; // tambahkan import model comment
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function index(Album $album){
        $album->load('photos');
        return view('photos.index', compact('album'));  
    }

    public function create() {
        $albums = Album::where('userID', Auth::id())->get();
        return view('photos.create', compact('albums'));
    }

    public function store(Request $request){
        $request->validate([
        'photo' => 'required|image|max:10000',
        'judulFoto' => 'required|string|max:255',
        'description' => 'nullable|string|max:255',
        'albumID' => 'required|exists:albums,albumID',
        ]);

        $photo = $request->file('photo');
        $path = $photo->store('photos', 'public');

        Photo::create([
            'userID' => Auth::id(),//
            'lokasiFile' => $path,
            'judulFoto' => $request->judulFoto,
            'deskripsiFoto' => $request->description,
            'tanggalUnggah' =>now(),
            'albumID' => $request->albumID,
            
        ]);
        return redirect()->route('home');
    }

    public function show(Photo $photo) {

    }

    public function edit(Photo $photo)
    {
        if ($photo->userID !== Auth::id()){
            abort(403, 'Unauthorised action.');
        }
        $albums = Album::where('userID', Auth::id())->get();
        return view('photos.edit', compact('photo', 'albums'));
    }

    public function update(Request $request, Photo $photo) {
            // Memastikan hanya pemilik foto yang dapat mengupdate
            if ($photo->userID !== Auth::id()) {
                abort(403, 'Unauthorized action.'); // Menghentikan eksekusi jika pengguna tidak berwenang
            }
         
            // Validasi input dari form
            $request->validate([
                'judulFoto' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
            ]);
         
            // Handle upload file foto jika user mengirim foto baru
            if ($request->hasFile('photo')) {
                // Validasi file foto
                $request->validate(['photo' => 'image|max:2048']);
                
                // Menghapus foto lama
                Storage::delete($photo->lokasiFile);
                
                // Menyimpan foto baru
                $photo->lokasiFile = $request->file('photo')->store('photos', 'public');
            }
         
            // Mengupdate informasi judul dan deskripsi foto
            $photo->judulFoto = $request->judulFoto;
            $photo->deskripsiFoto = $request->description;
            $photo->save();
         
            // Mengirim pengguna kembali ke album setelah berhasil diupdate
            return redirect()->route('albums.photos', $photo->albumID);
         
    }

    public function destroy(Photo $photo) {
        if ($photo->userID !== Auth::id()){
            abort(403, 'Unauthorised action.');
        }
        Storage::delete($photo->lokasiFile);
        $photo->delete();
        return redirect()->route('albums.photos', $photo->albumID);
    }

    public function like(Photo $photo) {
    
    // Memeriksa apakah foto sudah disukai oleh pengguna
    if ($photo->isLikedByAuthUser()) {
        // Jika sudah disukai, hapus like dari database
        $photo->likes()->where('userID', Auth::user()->userID)->delete();
    } else {
        // Jika belum disukai, buat entri like baru di database
        $photo->likes()->create([
            'userID' => Auth::user()->userID,
            'fotoID' => $photo->fotoID,
            'tanggalLike' => now(),
        ]);
    

    // Mengalirhkan pengguna kembali ke halaman utama
    return redirect()->route('home');
}
    }

    public function showComments(Photo $photo) {
// Menampilkan komentar pada foto

    // Memuat foto beserta relasi komentar dan user
    $photo->load('comments.user');
    
    // Mengembalikan tampilan komentar foto
    return view('photos.comment', compact('photo'));


    }
    public function storeComment(Request $request, Photo $photo) {
        
    // Validasi input komentar
    $request->validate([
        // Komentar harus ada dan maksimal 200 karakter
        'isiKomentar' => 'required|string|max:200',
    ]);

    // Membuat entri komentar baru di database
    Comment::create([
        'isiKomentar' => $request->isiKomentar,
        // Mengaitkan komentar dengan foto yang bersangkutan
        'fotoID' => $photo->fotoID,
        // Mengaitkan komentar dengan pengguna yang sedang login
        'userID' => Auth::id(),
    ]);

    // Mengalihkan pengguna kembali ke halaman komentar foto setelah berhasil
    return redirect()->route('photos.comments', $photo);


    }
}