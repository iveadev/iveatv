<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Programation;

class programationController extends Controller
{
    //

    function index(Request $request) {
        $today = date('Y-m-d');

        if($request->has('date')){
            $today=$request->get('date');
        }

        $previousDay = date_create($today);
        $nextDay = date_create($today);

        date_add($nextDay, date_interval_create_from_date_string('1 day'));
        date_sub($previousDay, date_interval_create_from_date_string('1 day'));
        
        $programation = Programation::where('date', $today)
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return Inertia::render('Dashboard',[
            'banners' => $programation,
            'today'=> $today,
            'nextDay' => date_format($nextDay,"Y-m-d"),
            'previousDay' => date_format($previousDay,"Y-m-d"),
        ]);
    }

    function update(Request $request, string $id) {
        $prog = Programation::find($id);
        $prog->update([
            'duration' => $request->input('duration'),
            'visible' => $request->input('visible')
        ]);
        
        return redirect()->back();
    }

    function destroy(Request $request, string $id) {
        $prog = Programation::find($id);
        $prog->delete();
        return redirect()->back();
    }

    function reorder(Request $request) {
        //cambia el orden en el que se presenta los items diarios
        foreach ($request->input('newOrder') as $key => $value) {
            Programation::where('id',$value)->update(['order' => $key+1]);
        } ;

        return redirect()->back();
    }


}
