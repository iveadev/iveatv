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
    // elemeto 'vacio'
    // que funciona como inicio de la cola de reproducción
    private $empty_item = [
        'order' => 0,
        'duration' => 10,
        'sound' => false,
        'event' => [
            'file' => [
                'name' => 'Inicio',
                'type' => 'image',
                'url' => '/banner.png',
            ]
        ]
    ];

    //
    function display(Request $request){
        $props = [
            'config' => $this->getConfig($request),
            'toShow' => $this->getEvent($request->get('id'))
        ];
        return Inertia::render('Display', $props);
    }


    function getEvent($id = null) {
        //Elemento actual a mostrar
        $item = Programation::find($id);
        if(isset($item)){
            return $item;
        } else {
            return $this->empty_item;
        }
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

    //funcion externa para obtener el siguiente banner
    function next(Request $request){
        $order = $request->get('order') ?? 0;
        $date = $request->get('date') ?? date('Y-m-d');

        return response()->json([
            //'config' => $this->getConfig($request),
            'next' => $this->getNext($date, $order)
            ]);
    }


    // Regresa la configuración del player
    function getConfig(Request $request){
        //Fecha actual o solicitada
        $date = $request->get('date') ?? date('Y-m-d');
        // conteo de elementos existentes para mostrar
        $event_count = Programation::where('date', $date)
            ->where('visible',1)
            ->count();

        // modo standby
        $standby_mode = false;
        $h = (int)date('H');
        $m = (int)date('M');
        if($date == date('Y-m-d') && ($h < 7 || $h > 18)){
            $standby_mode = true;
        }

        //conteo de ciclos para mostrar el 'Acerca de'
        $times = (int) $request->get('times') ?? 0;

        $show_about =false;
        if($times == 0 || $times % 50 == 0 || $standby_mode) {
            $show_about =true;
        }
        $times = $times >1000 ? 1 : $times+1; // se incrementa o se reinicia el contador (despues de 1000)

        // objeto de configuración a retornar:
        return [
            'date'=> $date,
            'times' => $times,
            'event_count' => $event_count,
            'show_about' => $show_about,
            'standby_mode' => $standby_mode,
        ];
    }

    // Streaming de video
    function getStreaming($id){
        $file = File::find($id);
        $path = public_path($file->url);
        $stream = new VideoStream($path);
        $stream->start();
    }

}
