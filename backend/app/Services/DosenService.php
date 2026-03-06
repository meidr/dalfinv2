<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DosenService
{
    /**
     * Get all dosen with optional filters.
     */
    public static function all($offset = null, $limit = null, $search = null, $order = null, $dir = null, $where = null, $whereIn = null)
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::timeout(120)->withHeaders([
            'apikey' => config('services.simkeu.api_key'),
        ])->asForm()->post(config('services.simkeu.base_url') . 'dosen', [
            'offset'   => $offset,
            'limit'    => $limit,
            'search'   => $search,
            'order'    => $order,
            'dir'      => $dir,
            'where'    => $where !== null ? json_encode($where) : null,
            'where_in' => $whereIn !== null ? json_encode($whereIn) : null,
        ]);

        return $response->json('data');
    }

    /**
     * Find a dosen by ID.
     */
    public static function find($id)
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::withHeaders([
            'apikey' => config('services.simkeu.api_key'),
        ])->asForm()->post(config('services.simkeu.base_url') . 'dosen/find', [
            'id' => $id,
        ]);

        return $response->json('data');
    }

    /**
     * Count dosen with optional filters.
     */
    public static function count($offset = null, $limit = null, $search = null, $order = null, $dir = null, $where = null)
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::withHeaders([
            'apikey' => config('services.simkeu.api_key'),
        ])->asForm()->post(config('services.simkeu.base_url') . 'dosen/count', [
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
