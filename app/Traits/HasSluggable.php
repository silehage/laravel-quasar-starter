<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSluggable
{
   public static function bootHasSluggable()
   {
      static::creating(function($model) {
         $model->slug = Str::slug($model->name);
      });
   }
}