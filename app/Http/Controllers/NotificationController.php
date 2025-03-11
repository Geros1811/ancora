<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\Obra;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $obraId = $request->query('obraId');
        $obra = null;

        if ($obraId) {
            $obra = Obra::findOrFail($obraId);
        }

        return view('notifications.index', compact('obraId', 'obra'));
    }
}
