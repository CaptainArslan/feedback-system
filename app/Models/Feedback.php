<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;
    protected $table='feedback';
    protected $fillable=[
        'title',
        'category_id',
        'user_id',
        'description',
    ];
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function category()
    {
        return $this->belongsTo(category::class,'category_id','id');
    }
}
