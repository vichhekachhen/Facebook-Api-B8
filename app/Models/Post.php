<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'body', 'image'];
    public static function list()
    {
        return self::all();
    }

    public static function store($request, $id=null)
    {
        $data = $request->only('title', 'body', 'image');
        $data = self::updateOrCreate(['id' => $id], $data);
        return $data;
    }
}
