<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Programation;
use App\Models\Event;
use App\Models\File;
use Inertia\Inertia;
use App\Models\VideoStream;

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
        $order = 0;
        $props = [];
        $toShow = null;

        if(isset($id)){
            $toShow = Programation::find($id);
            $order = $toShow->order;
        }
        if(!isset($toShow)) {
            $toShow = [
                'duration' => 5,
                'event' => [ 
                    'file' => [
                        'name' => 'Inicio',
                        'type' => 'IMAGEN',
                        'url' => '/banner.png',
                    ]
                ]
            ];
        }

        $props['banner'] = $toShow['event'];
        $props['banner']['duration']= $toShow['duration'];
        $have_visibles = Programation::where('date', $date)
                ->where('visible',1)
                ->count();
        if($have_visibles == 0){
            $props['banner']['duration']= 30;
            $props['empty'] = true;
        }
        

        $next = $this->getNext($id, $date, $order);
        if(isset($next)){
            $props['next'] = $next->id;
        }
        
        return $props;
    }

    function getNext($id = null, $date, $order){
        $today =date('y-m-d');
        $next = Programation::where('date', $date)
            ->where('visible',1)
            ->orderBy('order')
            ->orderBy('id');
        
        // se toma el siguiente orden
        if ($order == 0) {
            $next->where('order','>=',$order);
            // si no hay orden se toma el siguente id
            if(isset($id)){
                $next->where('id','>',$id);
            }
        } else {
            $next->where('order','>',$order);

        }

        return $next->first();
    }

    function getStreaming($id){
        $file = File::find($id);
        $path = public_path($file->url);
        $stream = new VideoStream($path);
        $stream->start();
    }
}
