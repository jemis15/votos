<?php

namespace App\Http\Controllers;

use App\Models\Election;
use Illuminate\Http\Request;

class ElectionController extends Controller
{
    function destroy(Election $election) {
        $election->delete();

        return redirect()->back()->with('success', 'Elecccion eliminado con exito');
    }
    
    function toggleStatus(Election $election) {
        $election->status = $election->status === 'closed' || $election->status === 'created' ? 'open' : 'closed';
        $election->save();

        return redirect()->back()->with('success', $election->status === 'open' ? 'Elecccion inicado' : 'Eleccion cerrado');
    }
}
