<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    /**
     * Tampilkan daftar banner
     */
    public function index()
    {
        try {
            $banners = Banner::orderBy('id', 'desc')->get();
        } catch (\Exception $e) {
            $banners = [];
        }

        return view('admin.banners.index', [
            'banners' => $banners
        ]);
    }

    /**
     * Tampilkan form untuk membuat banner baru
     */
    public function create()
    {
        return view('admin.banners.create');
    }

    /**
     * Simpan banner baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Upload gambar
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_banner_' . Str::slug($request->title) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
        } else {
            $imageName = 'default-banner.jpg';
        }

        try {
            // Simpan banner dengan query builder untuk debugging
            $banner_id = \DB::table('banners')->insertGetId([
                'image_path' => $imageName,
                'title' => $request->title,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? 1 : 0,
                'status' => $request->has('is_active') ? 1 : 0 // Untuk kompatibilitas dengan kolom lama
            ]);

            // Log untuk debugging
            \Log::info('Banner berhasil disimpan dengan ID: ' . $banner_id);
        } catch (\Exception $e) {
            // Log error
            \Log::error('Error saat menyimpan banner: ' . $e->getMessage());

            return redirect()->route('admin.banners.index')
                ->with('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner berhasil ditambahkan!');
    }

    /**
     * Tampilkan form untuk mengedit banner
     */
    public function edit($id)
    {
        $banner = Banner::findOrFail($id);

        return view('admin.banners.edit', [
            'banner' => $banner
        ]);
    }

    /**
     * Update banner
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Ambil banner yang akan diupdate
            $banner = \DB::table('banners')->where('id', $id)->first();

            if (!$banner) {
                return redirect()->route('admin.banners.index')
                    ->with('error', 'Banner tidak ditemukan');
            }

            // Data yang akan diupdate
            $updateData = [
                'title' => $request->title,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? 1 : 0,
                'status' => $request->has('is_active') ? 1 : 0 // Untuk kompatibilitas dengan kolom lama
            ];

            // Upload gambar baru jika ada
            if ($request->hasFile('image')) {
                // Hapus gambar lama jika bukan default
                if ($banner->image_path != 'default-banner.jpg' && file_exists(public_path('images/' . $banner->image_path))) {
                    unlink(public_path('images/' . $banner->image_path));
                }

                $image = $request->file('image');
                $imageName = time() . '_banner_' . Str::slug($request->title) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $imageName);
                $updateData['image_path'] = $imageName;
            }

            // Update banner
            \DB::table('banners')->where('id', $id)->update($updateData);

            // Log untuk debugging
            \Log::info('Banner berhasil diupdate dengan ID: ' . $id);
        } catch (\Exception $e) {
            // Log error
            \Log::error('Error saat mengupdate banner: ' . $e->getMessage());

            return redirect()->route('admin.banners.index')
                ->with('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner berhasil diperbarui!');
    }

    /**
     * Hapus banner
     */
    public function destroy($id)
    {
        try {
            // Ambil banner yang akan dihapus
            $banner = \DB::table('banners')->where('id', $id)->first();

            if (!$banner) {
                return redirect()->route('admin.banners.index')
                    ->with('error', 'Banner tidak ditemukan');
            }

            // Hapus gambar jika bukan default
            if ($banner->image_path != 'default-banner.jpg' && file_exists(public_path('images/' . $banner->image_path))) {
                unlink(public_path('images/' . $banner->image_path));
            }

            // Hapus banner dari database
            \DB::table('banners')->where('id', $id)->delete();

            // Log untuk debugging
            \Log::info('Banner berhasil dihapus dengan ID: ' . $id);
        } catch (\Exception $e) {
            // Log error
            \Log::error('Error saat menghapus banner: ' . $e->getMessage());

            return redirect()->route('admin.banners.index')
                ->with('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner berhasil dihapus!');
    }
}
