<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }


    public function create()
    {
        return view('admin.categories.create');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:categories,name',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);


        $validated['slug'] = Str::slug(
            $validated['name']
        );


        $validated['is_active'] =
            $request->boolean('is_active');


        if ($request->hasFile('image')) {

            $validated['image'] =
                $request->file('image')
                    ->store('categories', 'public');

        }


        Category::create($validated);


        return redirect()
            ->route('admin.categories.index')
            ->with(
                'success',
                'Kategori berhasil ditambahkan.'
            );
    }


    public function edit(Category $category)
    {
        return view(
            'admin.categories.edit',
            compact('category')
        );
    }


    public function update(
        Request $request,
        Category $category
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:categories,name,' . $category->id,
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);


        $validated['slug'] = Str::slug(
            $validated['name']
        );


        $validated['is_active'] =
            $request->boolean('is_active');


        /*
        |--------------------------------------------------------------------------
        | Upload Gambar Baru
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('image')) {

            // Hapus gambar lama jika ada
            if (
                $category->image &&
                Storage::disk('public')->exists(
                    $category->image
                )
            ) {
                Storage::disk('public')->delete(
                    $category->image
                );
            }


            // Simpan gambar baru
            $validated['image'] =
                $request->file('image')
                    ->store('categories', 'public');
        }


        $category->update($validated);


        return redirect()
            ->route('admin.categories.index')
            ->with(
                'success',
                'Kategori berhasil diperbarui.'
            );
    }


    public function destroy(Category $category)
    {
        if (
            $category->image &&
            Storage::disk('public')->exists(
                $category->image
            )
        ) {
            Storage::disk('public')->delete(
                $category->image
            );
        }

        $category->delete();


        return redirect()
            ->route('admin.categories.index')
            ->with(
                'success',
                'Kategori dan gambar berhasil dihapus.'
            );
    }
}