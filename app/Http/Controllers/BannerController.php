<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
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
        $props = [];

        if(isset($id)){
            $props['banner'] = Banner::find($id);
            //$props['banner']['url'] = asset($props['banner']['url']);
        } else {
            $props['banner'] = [
                'type' => 'IMAGEN',
                'url' => '/banner.png',
                'title' => 'Inicio',
                'duration' => 5,
            ];
            $have_visibles = $this->getBaseQuery(date("Y-m-d H:i:s"));
            if($have_visibles->where('visible',1)->count() == 0){
                $props['banner']['duration']= 30;
                $props['empty'] = true;
            }
        }

        $next = $this->getNext($id);
        if(isset($next)){
            $props['next'] = $next->id;
        }
        return $props;
    }

    function getBaseQuery($date) {
        return Banner::where('visible', 1)
                ->where('visibleFrom','<=', $date)
                ->where('visibleTo','>=', $date);
    }


    function getNext($id = null){
        $next = $this->getBaseQuery(date("Y-m-d H:i:s"));
        if(isset($id)) {
            $next->where('id','>',$id);
        }
        return $next->first();
    }


    // adminsitracion
    function index(Request $request) {
        $day = date('Y-m-d');
        if($request->has('date')){
            $day=$request->get('date');
        }
        $query = $this->getBaseQuery($day);
        return Inertia::render('Dashboard',[
            'banners' => $query->get(),
            'today'=> $day,
        ]);
    }
}
