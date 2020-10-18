<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class GoodsScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
//        Schema::hasColumn($model->getTable(), 'owner_id')
        $user = Auth::user();
        if (isset($user))
        {
            if ($user->hasRole('superadmin'))
            {
                $builder->withTrashed();
            }
            else if ($user->hasRole('admin'))
            {
                $builder->where('owner_id', $user->id);
            }
            else
            {
                $builder->where('owner_id', PHP_INT_MIN);
            }
        }
        else
        {
            $builder->where('owner_id', PHP_INT_MIN);
        }
    }
}