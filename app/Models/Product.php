<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';

    protected $fillable = [
        "name",
        "description",
        "preco",
        "garantia",
        "marca",
        "material",
        "origem",
        "created_by",
        "updated_by"
    ];

    public function creator()
    {
        return $this->belongsTo(User::class,"created_by","id");
    }

    public function updater()
    {
        return $this->belongsTo(User::class,"updated_by","id");
    }
}
