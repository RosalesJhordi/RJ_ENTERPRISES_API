<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //  Funcion Registro
    public function regsitro(Request $request){

        //Validar los datos obtenidos
        $validacion = Validator::make($request->all(),[
            'nombres'   => 'required',
            'apellidos' => 'required',
            'telefono'  => 'required',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'confirmed|required|min:6',
        ]);

        //Retornar errores si hay algun error al validar
        if($validacion->fails()){
            return response()->json($validacion->errors());
        }

        //Crear usuario si pasa la validacion
        $user = User::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        //Respuesta JSON si todo sale coreecto

        return response()->json([
            'user' => $user
        ]);
    }
}
