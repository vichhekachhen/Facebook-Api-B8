<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Like extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['user_id', 'post_id'];

    public static function store($request, $id = null)
    {
        $data = $request->only('user_id', 'post_id');

        if ($id) {
            $like = self::find($id);
            if ($like) {
                $like->update($data);
                return $like;
            } else {
                return null; 
            }
        } else {
            $like = self::create($data);
            return $like;
        }
    }
}