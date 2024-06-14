<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
<<<<<<< HEAD
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
=======

    protected $fillable = ['title', 'content'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
>>>>>>> main
    }
}
