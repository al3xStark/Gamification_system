<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DefaultRoomItem;
use Illuminate\Http\Response;

class DefaultRoomItemController extends Controller
{
    public function allowListAction(): Response
    {
        return response(DefaultRoomItem::get());
    }
}
