<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Medias extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['image'];
    public static function store($request, $id = null){
        // dd($request->file('image'));
        $data = $request->only('image');
        if ($request->hasFile('image')) {

            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $path = $file->storeAs('uploads/images', $filename, 'public');
            $data['image'] = Storage::url($path);
        }
        $data = self::updateOrCreate(['id' => $id], $data);
        return $data;
        
    }
}
