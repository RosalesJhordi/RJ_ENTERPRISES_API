<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    //  Funcion Registro
    public function registro(Request $request)
    {

        //Validar los datos obtenidos
        $validacion = Validator::make($request->all(), [
            'nombres'   => 'required',
            'apellidos' => 'required',
            'telefono'  => 'required',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'confirmed|required|min:6',
        ]);

        //Retornar errores si hay algun error al validar
        if ($validacion->fails()) {
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

        //Generar token PAT con laravel SANCTUM
        $token = $user->createToken('auth_token')->plainTextToken; //generar token
        $user = User::where('email', $request->email)->first(); //buscar usuario por email
        $userId = $user->id; //id de usuario
        $accessToken = User::find($userId)->tokens()->where('name', 'auth_token')->pluck('token')->first(); //obtner el usuario desde la bd


        //Retornar los datos de usuario y el token de acceso
        return response()->json([
            'user' => $user,
            'token' => $accessToken
        ]);
    }

    //funcion autenticacion de usuarios
    public function login(Request $request)
    {
        // Validar datos
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
            'password'  => 'required|min:6',
        ]);

        // Verificar las fallas del validador y devolver respuesta JSON con errores
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Buscar usuario y obtener el token PAT
        $user = User::where('email', $request->email)->first();
        $userId = $user->id;
        $accessToken = User::find($userId)->tokens()->where('name', 'auth_token')->pluck('token')->first();

        //Retornar los datos de usuario y el token de acceso
        return response()->json([
            "user" => $user,
            "token" => $accessToken
        ], 201);
    }

    //Mostrar todos los usuarios
    public function all()
    {
        $users = User::all();
        return response()->json($users);
    }

    //Obtener datos de usuario con token PAT
    public function readToken(Request $request)
    {
        // Obtener el token PAT del header de autorización
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token no proporcionado'], 401);
        }

        // Buscar el token PAT
        $pat = PersonalAccessToken::where('token', $token)->first();

        // Verificar si se encontró el token
        if ($pat) {
            // Obtener usuaruo con token PAT
            $user = $pat->tokenable;

            // Devolver datos del usuario
            return response()->json(['user' => $user], 200);
        } else {
            // Si no se encuentra un token
            return response()->json(['error' => 'Token PAT inválido'], 401);
        }
    }
}
