<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SupportTicketEvent extends Model {
 public $timestamps=false; protected $fillable=['support_ticket_id','actor_id','event_type','from_value','to_value','description','metadata','created_at'];
 protected function casts():array{return ['metadata'=>'array','created_at'=>'datetime'];}
 public function ticket(){return $this->belongsTo(SupportTicket::class,'support_ticket_id');} public function actor(){return $this->belongsTo(User::class,'actor_id');}
}
