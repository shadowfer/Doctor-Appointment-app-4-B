<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.roles.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Validar que se cree bien
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:1000',]);
        //si pasa la validacion, crear el rol
        Role::create([
            'name' => $request->name,
        ]);

        //vairable para el mensaje de exito
        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Role created successfully',
            'text' => 'The role has been created successfully.',
            'confirmButtonText' => 'OK',
        ]);

        //Redireccionar a la lista de roles con un mensaje de exito
        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
            if ($role->id <=4) {
            //Alerta
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Accion no permitida',
                'text' => 'No se puede editar este rol del sistema.',
                'confirmButtonText' => 'OK',
            ]);

            return redirect()->route('admin.roles.index');
        }
        return view('admin.roles.edit', compact('role'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        //Validar que se inserte bien
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:1000',]);
        
        //Si el nombre no cambio, no es necesario validarlo como unico
        if($role->name == $request->name){
            session()->flash('swal', 
            [
            'icon' => 'info',
            'title' => 'Sin cambios realizados',
            'text' => 'No se detectaron modificaciones.'
        ]);

        return redirect()->route('admin.roles.edit', $role);
        }
            //si pasa la validacion, crear el rol
        
        $role->update([
            'name' => $request->name,
        ]);

        //vairable para el mensaje de exito
        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Rol actualizado con éxito',
            'text' => 'El rol ha sido actualizado con éxito.',
            'confirmButtonText' => 'OK',
        ]);

        //Redireccionar a la lista de roles con un mensaje de exito
        return redirect()->route('admin.roles.index', $role);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if ($role->id <=4) {
            //Alerta
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Accion no permitida',
                'text' => 'No se puede eliminar este rol del sistema.',
                'confirmButtonText' => 'OK',
            ]);

            return redirect()->route('admin.roles.index');
        }

        //Eliminar el rol
        $role->delete();

        //Alerta
        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Rol Eliminado con exito',
            'text' => 'El rol ha sido eliminado con exito.',
            'confirmButtonText' => 'OK',
        ]);

        return redirect()->route('admin.roles.index');
    }
}