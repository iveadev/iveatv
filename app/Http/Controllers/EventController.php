<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Programation;
use Inertia\Inertia;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $events = Event::all();
        return Inertia::render('Event/Index',['events' => $events, 'today' => date('Y-m-d')]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'file_id' => 'required|exists:files,id',
            'visible' => 'boolean',
            'duration' => 'integer',
            'visibleFrom' => 'date',
            'visibleTo' => 'date',
        ]);
        $event = Event::create($validated);

        $range = $event->getDateRange();
        foreach ($range as $date) {
            $prog = Programation::firstOrCreate([
                'date' => $date,
                'event_id' => $event->id,
                'visible' => $event->visible,
            ]);
        }
        return redirect()->back();

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $validated = $request->validate([
            'file_id' => 'required|exists:files,id',
            'visible' => 'boolean',
            'duration' => 'integer',
            'visibleFrom' => 'date',
            'visibleTo' => 'date',
        ]);
        
        $event = Event::findOrFail($id);

        $previous_range = $event->getDateRange();
        $event->update($validated);
        $range = $event->getDateRange();

        //Se eliminan las programaciones fuera de las fechas de vigencia
        Programation::where('event_id', $event->id)
            ->whereNotIn('date',$range)
            ->delete();
        
        // se actualizan los que quedan
        Programation::where('event_id', $event->id)
            ->whereIn('date',$range)
            ->update(['visible' => $event->visible]);
        
        // Se crean los eventos faltantes
        $toCreate = array_diff($range, $previous_range);
        foreach ($toCreate as $date) {
            $prog = Programation::firstOrCreate([
                'date' => $date,
                'event_id' => $event->id,
                'visible' => $event->visible,
            ]);
        }

        //Se eliminan las programaciones fuera de las fechas de vigencia
        Programation::where('event_id', $event->id)
            ->whereNotIn('date',$range)
            ->delete();
        
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
