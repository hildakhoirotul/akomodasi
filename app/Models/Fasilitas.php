<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    use HasFactory;
    protected $table = 'fasilitas';
    protected $primaryKey = 'id';
    protected $appends = ['original_name'];

    protected $fillable = [
        'name',
        'jumlah_atribut',
        'jumlah',
    ];

    public function getFormattedTableNameAttribute()
    {
        $formattedName = str_replace(' ', '_', $this->name);
        return strtolower($formattedName);
    }

    public function getOriginalNameAttribute()
    {
        return $this->attributes['name'];
    }
}
