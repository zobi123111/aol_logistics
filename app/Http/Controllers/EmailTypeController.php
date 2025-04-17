<?php

namespace App\Http\Controllers;

use App\Models\EmailType;
use Illuminate\Http\Request;

class EmailTypeController extends Controller
{
    public function index()
    {
        $types = EmailType::all();
        return view('email_types.index', compact('types'));
    }

    public function toggle($id)
    {
        $type = EmailType::findOrFail($id);
        $type->is_active = !$type->is_active;
        $type->save();

        return redirect()->route('email-types.index')->with('message',__('messages.email_type_status_updated'));
    }
}
