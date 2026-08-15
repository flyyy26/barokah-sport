<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class BannerController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $banners = Banner::orderBy(
            'sort_order'
        )
            ->orderByDesc('created_at')
            ->get();


        return view(
            'admin.banners.index',
            compact('banners')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view(
            'admin.banners.create'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request
    ) {

        $validated = $request->validate([

            'title' =>
                [
                    'required',
                    'string',
                    'max:255',
                ],

            'subtitle' =>
                [
                    'nullable',
                    'string',
                    'max:255',
                ],

            'image' =>
                [
                    'required',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:5120',
                ],

            'button_text' =>
                [
                    'nullable',
                    'string',
                    'max:100',
                ],

            'button_url' =>
                [
                    'nullable',
                    'string',
                    'max:500',
                ],

            'sort_order' =>
                [
                    'required',
                    'integer',
                    'min:0',
                ],

            'is_active' =>
                [
                    'nullable',
                    'boolean',
                ],

            'starts_at' =>
                [
                    'nullable',
                    'date',
                ],

            'ends_at' =>
                [
                    'nullable',
                    'date',
                    'after_or_equal:starts_at',
                ],

        ]);


        try {

            /*
            |--------------------------------------------------------------------------
            | UPLOAD GAMBAR
            |--------------------------------------------------------------------------
            */

            $imagePath =
                $request
                    ->file('image')
                    ->store(
                        'banners',
                        'public'
                    );


            /*
            |--------------------------------------------------------------------------
            | TRANSACTION DATABASE
            |--------------------------------------------------------------------------
            */

            DB::transaction(
                function () use (
                    $validated,
                    $imagePath
                ) {

                    Banner::create([

                        'title' =>
                            $validated['title'],

                        'subtitle' =>
                            $validated['subtitle']
                            ?? null,

                        'image' =>
                            $imagePath,

                        'button_text' =>
                            $validated['button_text']
                            ?? null,

                        'button_url' =>
                            $validated['button_url']
                            ?? null,

                        'sort_order' =>
                            $validated['sort_order'],

                        'is_active' =>
                            $validated['is_active']
                            ?? false,

                        'starts_at' =>
                            $validated['starts_at']
                            ?? null,

                        'ends_at' =>
                            $validated['ends_at']
                            ?? null,

                    ]);

                }
            );


            return redirect()
                ->route(
                    'admin.banners.index'
                )
                ->with(
                    'success',
                    'Banner berhasil ditambahkan.'
                );


        } catch (
            Throwable $e
        ) {

            /*
            |--------------------------------------------------------------------------
            | JIKA DATABASE GAGAL
            |--------------------------------------------------------------------------
            |
            | Hapus file yang sudah ter-upload.
            |
            */

            if (
                isset($imagePath) &&
                Storage::disk('public')
                    ->exists($imagePath)
            ) {

                Storage::disk('public')
                    ->delete($imagePath);

            }


            report($e);


            return back()
                ->withInput()
                ->with(
                    'error',
                    'Banner gagal ditambahkan.'
                );

        }
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(
        Banner $banner
    ) {

        return view(
            'admin.banners.edit',
            compact('banner')
        );

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Banner $banner
    ) {

        $validated = $request->validate([

            'title' =>
                [
                    'required',
                    'string',
                    'max:255',
                ],

            'subtitle' =>
                [
                    'nullable',
                    'string',
                    'max:255',
                ],

            'image' =>
                [
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:5120',
                ],

            'button_text' =>
                [
                    'nullable',
                    'string',
                    'max:100',
                ],

            'button_url' =>
                [
                    'nullable',
                    'string',
                    'max:500',
                ],

            'sort_order' =>
                [
                    'required',
                    'integer',
                    'min:0',
                ],

            'is_active' =>
                [
                    'nullable',
                    'boolean',
                ],

            'starts_at' =>
                [
                    'nullable',
                    'date',
                ],

            'ends_at' =>
                [
                    'nullable',
                    'date',
                    'after_or_equal:starts_at',
                ],

        ]);


        $oldImage =
            $banner->image;


        $newImagePath =
            null;


        try {

            /*
            |--------------------------------------------------------------------------
            | UPLOAD GAMBAR BARU
            |--------------------------------------------------------------------------
            */

            if (
                $request->hasFile(
                    'image'
                )
            ) {

                $newImagePath =
                    $request
                        ->file('image')
                        ->store(
                            'banners',
                            'public'
                        );

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE DATABASE
            |--------------------------------------------------------------------------
            */

            DB::transaction(
                function () use (
                    $banner,
                    $validated,
                    $newImagePath
                ) {

                    $banner->update([

                        'title' =>
                            $validated['title'],

                        'subtitle' =>
                            $validated['subtitle']
                            ?? null,

                        'button_text' =>
                            $validated['button_text']
                            ?? null,

                        'button_url' =>
                            $validated['button_url']
                            ?? null,

                        'sort_order' =>
                            $validated['sort_order'],

                        'is_active' =>
                            $validated['is_active']
                            ?? false,

                        'starts_at' =>
                            $validated['starts_at']
                            ?? null,

                        'ends_at' =>
                            $validated['ends_at']
                            ?? null,

                        ...(
                            $newImagePath
                            ? [
                                'image' =>
                                    $newImagePath
                            ]
                            : []
                        ),

                    ]);

                }
            );


            /*
            |--------------------------------------------------------------------------
            | HAPUS GAMBAR LAMA
            |--------------------------------------------------------------------------
            */

            if (
                $newImagePath &&
                $oldImage &&
                Storage::disk('public')
                    ->exists($oldImage)
            ) {

                Storage::disk('public')
                    ->delete($oldImage);

            }


            return redirect()
                ->route(
                    'admin.banners.index'
                )
                ->with(
                    'success',
                    'Banner berhasil diperbarui.'
                );


        } catch (
            Throwable $e
        ) {

            /*
            |--------------------------------------------------------------------------
            | HAPUS GAMBAR BARU
            |--------------------------------------------------------------------------
            */

            if (
                $newImagePath &&
                Storage::disk('public')
                    ->exists($newImagePath)
            ) {

                Storage::disk('public')
                    ->delete($newImagePath);

            }


            report($e);


            return back()
                ->withInput()
                ->with(
                    'error',
                    'Banner gagal diperbarui.'
                );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Banner $banner
    ) {

        $imagePath =
            $banner->image;


        try {

            DB::transaction(
                function () use (
                    $banner
                ) {

                    $banner->delete();

                }
            );


            /*
            |--------------------------------------------------------------------------
            | HAPUS FILE GAMBAR
            |--------------------------------------------------------------------------
            */

            if (
                $imagePath &&
                Storage::disk('public')
                    ->exists($imagePath)
            ) {

                Storage::disk('public')
                    ->delete($imagePath);

            }


            return redirect()
                ->route(
                    'admin.banners.index'
                )
                ->with(
                    'success',
                    'Banner berhasil dihapus.'
                );


        } catch (
            Throwable $e
        ) {

            report($e);


            return back()
                ->with(
                    'error',
                    'Banner gagal dihapus.'
                );

        }

    }
}