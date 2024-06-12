<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = ['comment'];
    public static function list(){
        return self::all();
    }
    public static function store($request, $id = null){
        $data = $request->only('comment');
        $data = self::updateOrCreate(['id' => $id], $data);
        return $data;
        
    }
}
