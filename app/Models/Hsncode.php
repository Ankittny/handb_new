<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Hsncode extends Model
{
    use HasFactory;
    protected $table = 'hsn_code';
    protected $fillable = [
        'hsn_code_under_gst',
        'description',
        'tax',
        'status'
    ];
}