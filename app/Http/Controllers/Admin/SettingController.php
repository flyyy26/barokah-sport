<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SettingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | EDIT / SHOW SETTINGS
    |--------------------------------------------------------------------------
    */

    public function edit()
    {
        $setting = Setting::first();

        return view(
            'admin.settings.edit',
            compact('setting')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE SETTINGS
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request
    ) {

        $validated = $request->validate([

            'store_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'store_description' => [
                'nullable',
                'string',
            ],

            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'favicon' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,ico',
                'max:1024',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'whatsapp' => [
                'nullable',
                'string',
                'max:30',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'instagram' => [
                'nullable',
                'string',
                'max:500',
            ],

            'facebook' => [
                'nullable',
                'string',
                'max:500',
            ],

            'tiktok' => [
                'nullable',
                'string',
                'max:500',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | AMBIL SETTING
        |--------------------------------------------------------------------------
        */

        $setting = Setting::first();


        /*
        |--------------------------------------------------------------------------
        | SIMPAN PATH GAMBAR LAMA
        |--------------------------------------------------------------------------
        */

        $oldLogo =
            $setting?->logo;

        $oldFavicon =
            $setting?->favicon;


        /*
        |--------------------------------------------------------------------------
        | PATH GAMBAR BARU
        |--------------------------------------------------------------------------
        */

        $newLogo =
            null;

        $newFavicon =
            null;


        try {

            /*
            |--------------------------------------------------------------------------
            | UPLOAD LOGO
            |--------------------------------------------------------------------------
            */

            if (
                $request->hasFile('logo')
            ) {

                $newLogo =
                    $request
                        ->file('logo')
                        ->store(
                            'settings',
                            'public'
                        );

            }


            /*
            |--------------------------------------------------------------------------
            | UPLOAD FAVICON
            |--------------------------------------------------------------------------
            */

            if (
                $request->hasFile('favicon')
            ) {

                $newFavicon =
                    $request
                        ->file('favicon')
                        ->store(
                            'settings',
                            'public'
                        );

            }


            /*
            |--------------------------------------------------------------------------
            | DATABASE TRANSACTION
            |--------------------------------------------------------------------------
            */

            DB::transaction(
                function () use (
                    $setting,
                    $validated,
                    $newLogo,
                    $newFavicon
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | JIKA BELUM ADA SETTING
                    |--------------------------------------------------------------------------
                    */

                    if (!$setting) {

                        Setting::create([

                            'store_name' =>
                                $validated[
                                    'store_name'
                                ] ?? null,

                            'store_description' =>
                                $validated[
                                    'store_description'
                                ] ?? null,

                            'logo' =>
                                $newLogo,

                            'favicon' =>
                                $newFavicon,

                            'phone' =>
                                $validated[
                                    'phone'
                                ] ?? null,

                            'whatsapp' =>
                                $validated[
                                    'whatsapp'
                                ] ?? null,

                            'email' =>
                                $validated[
                                    'email'
                                ] ?? null,

                            'address' =>
                                $validated[
                                    'address'
                                ] ?? null,

                            'instagram' =>
                                $validated[
                                    'instagram'
                                ] ?? null,

                            'facebook' =>
                                $validated[
                                    'facebook'
                                ] ?? null,

                            'tiktok' =>
                                $validated[
                                    'tiktok'
                                ] ?? null,

                        ]);

                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | UPDATE SETTING
                        |--------------------------------------------------------------------------
                        */

                        $setting->update([

                            'store_name' =>
                                $validated[
                                    'store_name'
                                ] ?? null,

                            'store_description' =>
                                $validated[
                                    'store_description'
                                ] ?? null,

                            'phone' =>
                                $validated[
                                    'phone'
                                ] ?? null,

                            'whatsapp' =>
                                $validated[
                                    'whatsapp'
                                ] ?? null,

                            'email' =>
                                $validated[
                                    'email'
                                ] ?? null,

                            'address' =>
                                $validated[
                                    'address'
                                ] ?? null,

                            'instagram' =>
                                $validated[
                                    'instagram'
                                ] ?? null,

                            'facebook' =>
                                $validated[
                                    'facebook'
                                ] ?? null,

                            'tiktok' =>
                                $validated[
                                    'tiktok'
                                ] ?? null,

                            ...(
                                $newLogo
                                ? [
                                    'logo' =>
                                        $newLogo
                                ]
                                : []
                            ),

                            ...(
                                $newFavicon
                                ? [
                                    'favicon' =>
                                        $newFavicon
                                ]
                                : []
                            ),

                        ]);

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | HAPUS LOGO LAMA
            |--------------------------------------------------------------------------
            */

            if (
                $newLogo &&
                $oldLogo &&
                Storage::disk('public')
                    ->exists($oldLogo)
            ) {

                Storage::disk('public')
                    ->delete($oldLogo);

            }


            /*
            |--------------------------------------------------------------------------
            | HAPUS FAVICON LAMA
            |--------------------------------------------------------------------------
            */

            if (
                $newFavicon &&
                $oldFavicon &&
                Storage::disk('public')
                    ->exists($oldFavicon)
            ) {

                Storage::disk('public')
                    ->delete($oldFavicon);

            }


            return redirect()
                ->route(
                    'admin.settings.edit'
                )
                ->with(
                    'success',
                    'Pengaturan toko berhasil disimpan.'
                );


        } catch (
            Throwable $e
        ) {

            /*
            |--------------------------------------------------------------------------
            | HAPUS FILE BARU JIKA DATABASE GAGAL
            |--------------------------------------------------------------------------
            */

            if (
                $newLogo &&
                Storage::disk('public')
                    ->exists($newLogo)
            ) {

                Storage::disk('public')
                    ->delete($newLogo);

            }


            if (
                $newFavicon &&
                Storage::disk('public')
                    ->exists($newFavicon)
            ) {

                Storage::disk('public')
                    ->delete($newFavicon);

            }


            report($e);


            return back()
                ->withInput()
                ->with(
                    'error',
                    'Pengaturan toko gagal disimpan.'
                );

        }

    }
}