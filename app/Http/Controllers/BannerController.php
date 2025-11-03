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
        $props = [
            'config'=> [
               'date' =>  $request->get('date'),
               'times' => ((int)$request->get('times'))+1,
               'empty' => false,
               'waiting' => 60,
            ]
        ];
        $toShow = null;

        if(isset($id)){
            $toShow = Programation::find($id);
            $order = $toShow->order;            
        }
        if(!isset($toShow)) {
            // banner inicial
            $toShow = [
                'duration' => 5,
                'sound' => true,
                'event' => [ 
                    'file' => [
                        'name' => 'Inicio',
                        'type' => 'image',
                        'url' => '/banner.png',
                    ]
                ]
            ];
        }

        $props['toShow'] = $toShow;
        $have_visibles = Programation::where('date', $date)
                ->where('visible',1)
                ->count();
        if($have_visibles == 0){
            $props['config']['empty'] = true;
        }

        $next = $this->getNext($date, $order);
        if(isset($next)){
            $props['next'] = $next->id;
        }

        if($props['config']['times'] % 100 == 0){
            //pequeña pausa
            $props['config']['empty'] = true;
            $props['config']['waiting'] = 10; // segundos
            if($props['config']['times'] >= 1000){
                // reset del contador
                $props['config']['times'] = 1;
                // Se recarga para limpiar cache
                $props['config']['needsReload'] = 1;
            }
        }

        // modo standby
        $h = (int)date('H');
        if($h < 7 || $h > 18){
            $props['config']['standby'] = true;
            $props['config']['empty'] = true;
            $props['config']['waiting'] = 600; // segundos
            $props['next'] = null;
        }
        
        return $props;
    }

    function getNext($date, $order){
        $today =date('y-m-d');
        $next = Programation::where('date', $date)
            ->where('visible',1)
            ->orderBy('order')
            ->orderBy('id');
        
        // se toma el siguiente orden
        $next->where('order','>',$order);

        return $next->first();
    }

    function getStreaming($id){
        $file = File::find($id);
        $path = public_path($file->url);
        $stream = new VideoStream($path);
        $stream->start();
    }
}
