<?php

namespace App\Http\Controllers;

use App\Models\Adjunto;
use App\Models\OrdenServicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdjuntoController extends Controller
{
    public function store(Request $request, OrdenServicio $orden): RedirectResponse
    {
        $this->authorize('create', [Adjunto::class, $orden]);

        $request->validate([
            'archivo' => [
                'required', 'file', 'max:10240',
                'mimes:jpg,jpeg,png,webp,pdf,doc,docx',
                'mimetypes:image/jpeg,image/png,image/webp,application/pdf,'
                         .'application/msword,'
                         .'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ],
            'nombre' => ['nullable', 'string', 'max:100'],
        ], [
            'archivo.max'       => 'El archivo no puede superar 10 MB.',
            'archivo.mimes'     => 'Formatos permitidos: JPG, PNG, WEBP, PDF, DOC, DOCX.',
            'archivo.mimetypes' => 'El tipo real del archivo no está permitido.',
        ]);

        $file   = $request->file('archivo');
        $ruta   = $file->store("adjuntos/{$orden->id}", 'public');
        $nombre = $request->nombre ?: $file->getClientOriginalName();

        Adjunto::create([
            'orden_id' => $orden->id,
            'user_id'  => auth()->id(),
            'nombre'   => $nombre,
            'ruta'     => $ruta,
            'tipo'     => $file->getMimeType(),
            'tamano'   => $file->getSize(),
        ]);

        return back()->with('success', 'Archivo adjuntado correctamente.');
    }

    public function download(Adjunto $adjunto): StreamedResponse
    {
        $this->authorize('view', $adjunto);

        abort_unless(Storage::disk('public')->exists($adjunto->ruta), 404);

        return Storage::disk('public')->download($adjunto->ruta, $adjunto->nombre);
    }

    public function destroy(Adjunto $adjunto): RedirectResponse
    {
        $this->authorize('delete', $adjunto);

        Storage::disk('public')->delete($adjunto->ruta);
        $adjunto->delete();

        return back()->with('success', 'Adjunto eliminado.');
    }
}
