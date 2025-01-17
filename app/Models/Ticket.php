<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    protected $table = "tickets";
    protected $fillable = ['ticket_id', 'ticket_no', 'price'];

    // public function event()
    // {
    //     return $this->belongsTo(Event::class);
    // }
}
