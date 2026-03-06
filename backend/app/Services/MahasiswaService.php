<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MahasiswaService
{
    /**
     * Get all mahasiswa with optional filters.
     *
     * @param  int|null     $offset
     * @param  int|null     $limit
     * @param  string|null  $search
     * @param  string|null  $order
     * @param  string|null  $dir   asc or desc
     * @param  array|null   $where
     * @return mixed
     */
    public static function all($offset = null, $limit = null, $search = null, $order = null, $dir = null, $where = null)
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::withHeaders([
            'apikey' => config('services.simkeu.api_key'),
        ])->asForm()->post(config('services.simkeu.base_url') . 'mahasiswa', [
            'offset' => $offset,
            'limit'  => $limit,
            'search' => $search,
            'order'  => $order,
            'dir'    => $dir,
            'where'  => $where !== null ? json_encode($where) : null,
        ]);

        return $response->json('data');
    }

    /**
     * Get all mahasiswa with optional filters.
     *
     * @param  int|null     $offset
     * @param  int|null     $limit
     * @param  string|null  $search
     * @param  string|null  $order
     * @param  string|null  $dir   asc or desc
     * @param  array|null   $where
     * @return mixed
     */
    public static function mahasiswaSkripsi($offset = null, $limit = null, $search = null, $order = null, $dir = null, $where = null)
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::timeout(120)->withHeaders([
            'apikey' => config('services.simkeu.api_key'),
        ])->asForm()->post(config('services.simkeu.base_url') . 'mahasiswa/mahasiswaSkripsi', [
            'offset' => $offset,
            'limit'  => $limit,
            'search' => $search,
            'order'  => $order,
            'dir'    => $dir,
            'where'  => $where !== null ? json_encode($where) : null,
        ]);

        return $response->json('data');
    }

    /**
     * Find a mahasiswa by ID.
     */
    public static function find($id)
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::withHeaders([
            'apikey' => config('services.simkeu.api_key'),
        ])->asForm()->post(config('services.simkeu.base_url') . 'mahasiswa/id', [
            'id' => $id,
        ]);

        return $response->json('data');
    }

    /**
     * Find a mahasiswa by NIM.
     */
    public static function nim($nim)
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::withHeaders([
            'apikey' => config('services.simkeu.api_key'),
        ])->asForm()->post(config('services.simkeu.base_url') . 'mahasiswa/nim', [
            'nim' => $nim,
        ]);

        return $response->json('data.data');
    }

    /**
     * Count mahasiswa with optional filters.
     */
    public static function count($offset = null, $limit = null, $search = null, $order = null, $dir = null, $where = null)
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::withHeaders([
            'apikey' => config('services.simkeu.api_key'),
        ])->asForm()->post(config('services.simkeu.base_url') . 'mahasiswa/count', [
            'offset' => $offset,
            'limit'  => $limit,
            'search' => $search,
            'order'  => $order,
            'dir'    => $dir,
            'where'  => $where !== null ? json_encode($where) : null,
        ]);

        return $response->json('data');
    }
}
