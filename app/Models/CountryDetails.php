<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryDetails extends Model
{
    use HasFactory;

    protected $table = 'queuingdb_countries_detailed'; // Specify the table name

    protected $fillable = [
        'countryName',
        'telephonePrefixes', // Add other fillable fields if needed
    ];
}
