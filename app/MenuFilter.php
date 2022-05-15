<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;

class MenuFilter implements FilterInterface
{
   public function transform($item)
   {
      $userLogin = Auth::user();
      if (isset($item['permission']) && !$userLogin->isAbleTo($item['permission'])) {
         $item['restricted'] = true;
      }

      return $item;
   }
}
