<?php

namespace App\Traits;

use Illuminate\Http\Request;

/**
 * Gender-based mahasiswa filtering for admin/staff controllers.
 * Staff and admin users only see mahasiswa of the same gender (jenis_kelamin).
 * Super admin users bypass the filter and see all data.
 */
trait GenderFilterable
{
    /**
     * Apply gender-based filtering on a query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  Request  $request
     * @param  string   $relation  The relationship path to the mahasiswa model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyGenderFilter($query, Request $request, string $relation = 'mahasiswa')
    {
        $user = $request->user();
        if ($user && $user->role !== 'super_admin' && $user->jenis_kelamin) {
            $gender = $user->jenis_kelamin;
            $query->whereHas($relation, function ($q) use ($gender) {
                $q->where('jenis_kelamin', $gender);
            });
        }
        return $query;
    }

    /**
     * Get the gender filter value for the current user (or null if no filter applies).
     */
    protected function getGenderFilter(Request $request): ?string
    {
        $user = $request->user();
        if ($user && $user->role !== 'super_admin' && $user->jenis_kelamin) {
            return $user->jenis_kelamin;
        }
        return null;
    }
}
