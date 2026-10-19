<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;

class ManufacturerController extends Controller
{
    public function setCurrent($id)
    {
        Helper::set_current($id);
    }
}
