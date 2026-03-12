<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::latest()->paginate(10);

        return view('pages.service.index', ['services' => $services]);
    }

    public function create()
    {
        return view('pages.service.create');
    }

    public function edit(Service $service)
    {
        return view('pages.service.edit', ['service' => $service]);
    }

    public function update(ServiceRequest $request, Service $service)
    {
        $data = $request->validated();
        $service->update($data);

        return redirect()
            ->route('service.index')
            ->with('success', 'Услуга '.$service['name'].' успешно изменена!');
    }

    public function store(ServiceRequest $request)
    {
        $service = $request->validated();
        Service::create($service);

        return redirect()
            ->route('service.index')
            ->with('success', 'Услуга '.$service['name'].' успешно создана!');
    }

    public function destroy(Service $service)
    {
        Service::destroy($service->id);

        return redirect()
            ->route('service.index')
            ->with('success', 'Услуга '.$service['name'].' успешно удалена!');
    }

    public function show(Service $service)
    {
        return view('pages.service.show', ['service' => $service]);
    }
}
