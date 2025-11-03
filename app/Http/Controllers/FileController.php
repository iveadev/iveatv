<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

use App\Models\File;
use App\Models\Event;
use App\Models\Programation;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $files = File::orderBy('updated_at','desc')->get();
        return Inertia::render('File/Index',['files'=> $files]);
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
        $inputs = $request->validate([
            'name' => 'required|unique:files|max:255',
            'file' => 'sometimes|file',
            'type' => 'sometimes|string',
            'filename' => 'required|max:255|unique:files',
            'avalible' => 'boolean',
            'url' => 'sometimes|nullable',
        ]);

        if($request->hasFile('file')){
            $filename = $request->input('filename');
            $file = $request->file('file');
            $path=Storage::putFileAs('',$file, $filename, 'public');
            $inputs['url'] = Storage::url($path);
            File::create($inputs);
            return redirect()->back();
        }
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
        $inputs = $request->validate([
            'name' => 'required|max:255',
            'file' => 'sometimes|nullable|file',
            'filename' => 'sometimes|nullable|max:255',
            'type' => 'sometimes|nullable|string',
            'avalible' => 'boolean',
            'url' => 'sometimes|nullable',
        ]);

        unset($inputs['id']);
        $fileObj = File::find($id);
        if($request->hasFile('file')){    
            // se reemplaza el archivo por el nuevo
            Storage::delete($fileObj->filename);

            // almacenamos el nuevo
            $filename = $inputs['filename'];
            $file = $request->file('file');
            $path=Storage::putFileAs('',$file, $filename, 'public');
            $inputs['url'] = Storage::url($path);
        } else {
            unset($inputs['filename']);
            unset($inputs['type']);
            unset($inputs['url']);
        }
        $fileObj->update($inputs);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $fileObj = File::find($id);
        Storage::delete($fileObj->filename);
        $evts = Event::where('file_id',$id)->get();
        $ids = $evts->select('id')->flatten();

        Programation::whereIn('event_id',$ids)->delete();
        Event::whereIn('id',$ids)->delete();
        $fileObj->delete();

        return redirect()->back();

    }


    /**
     * Retorna la lista de archivos disponibles
     */
    function getList(){
        return File::where('avalible',1)->get();
    }
}
