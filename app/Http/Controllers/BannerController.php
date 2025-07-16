<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use Inertia\Inertia;

class BannerController extends Controller
{
    //
    function display(Request $request){
        $id = $request->get('id') ?? null;
        $props = [];

        if(isset($id)){
            $props['banner'] = Banner::find($id);
        } else {
            $props['banner'] = [
                'type' => 'IMAGEN',
                'url' => '/banner.png',
                'duration' => 10,
            ];
            if(Banner::where('visible',1)->count() == 0){
                $props['banner']['duration']= 30;
                $props['empty'] = true;
            }
        }

        $next = $this->getNext($id);
        if(isset($next)){
            $props['next'] = $next->id;
        }
        return Inertia::render('Display', $props);
    }
    function getNext($id = null){
        $now = date("Y-m-d H:i:s");
        $next = Banner::where('visible', 1)
                ->where('visibleFrom','<=', $now)
                ->where('visibleTo','>=', $now);
        if(isset($id)) {
            $next->where('id','>',$id);
        }
        return $next->first();
    }
}
