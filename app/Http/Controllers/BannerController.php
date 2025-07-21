<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Programation;
use App\Models\Event;
use Inertia\Inertia;

class BannerController extends Controller
{
    //
    function display(Request $request){
        $props = $this->getProps($request);
        return Inertia::render('Display', $props);
    }

    function old(Request $request){
        $props = $this->getProps($request);
        return view('old', $props);
    }

    function getProps ($request) {
        $id = $request->get('id') ?? null;
        $date = $request->get('date') ?? date('Y-m-d');
        $props = [];
        $toShow = null;

        if(isset($id)){
            $toShow = Programation::find($id);
        }
        if(!isset($toShow)) {
            $toShow = [
                'duration' => 5,
                'event' => [
                    'title' => 'Inicio',
                    'file' => [
                        'type' => 'IMAGEN',
                        'url' => '/banner.png',
                    ]
                ]
            ];
        }

        $props['banner'] = $toShow['event'];
        $props['banner']['duration']= $toShow['duration'];
        $have_visibles = Programation::where('date', $date)->where('visible',1)->count();
        if($have_visibles == 0){
            $props['banner']['duration']= 30;
            $props['empty'] = true;
        }
        

        $next = $this->getNext($id, $date);
        if(isset($next)){
            $props['next'] = $next->id;
        }
        
        return $props;
    }

    function getNext($id = null, $date){
        $today =date('y-m-d');
        $next = Programation::where('date', $date)->where('visible',1);
        if($today == $date) {
            //agrega la vigencia por hora
        }
        if(isset($id)) {
            $next->where('id','>',$id);
        }
        return $next->first();
    }
}
