<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    function physical_person(){
        return User::find($this->physical_person) ;
    }
    function establishments(){
        return Establishment::where('company_id', $this->id)->get();
    }

    function professionals(){
        $pros = Collect();

        foreach ($this->establishments() as $esta){
           foreach ($esta->professionals()->get() as $pro){
               $pros->add($pro);
            }

        }


        return $pros->unique('id');
    }
}
