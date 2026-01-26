<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateUserSelfRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * Bejelentkezés és Token generálás
     */
    public function login(LoginUserRequest $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'message' => 'Invalid email or password'
            ], 401);
        }

        $expirationTime = Carbon::now()->addDays(1);
        $role = (int)$user->role; 
        $name = "1day-role:$role";

        // JOGOSULTSÁGOK (Abilities) BEÁLLÍTÁSA
        // Fontos: Az api.php 'ability:admin' middleware-je keresi az 'admin' stringet!
        switch ($role) {
            case 1:
                // Admin - Minden jogot megkap
                $abilities = ['admin', 'customer'];
                break;
            default:
                // Vásárló (role 2 vagy más) - Saját profil és vásárlás
                $abilities = ['customer'];
                break;
        }

        // Token létrehozása
        $token = $user->createToken($name, $abilities, $expirationTime)->plainTextToken;
        
        // A tokent közvetlenül a user objektumhoz fűzzük a válaszhoz
        $user->token = $token;

        return response()->json([
            'message' => 'ok',
            'data' => $user
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Kijelentkezés (Aktuális token törlése)
     */
    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        $personalAccessToken = PersonalAccessToken::findToken($token);

        if ($personalAccessToken) {
            $personalAccessToken->delete();
            $data = ['message' => 'ok', 'data' => []];
        } else {
            $data = ['message' => 'Token not found', 'data' => []];
        }

        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Összes felhasználó listázása (Csak Admin)
     */
    public function index()
    {
        try {
            $rows = User::all();
            return response()->json([
                'message' => 'OK',
                'data' => $rows
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Server error: {$e->getMessage()}",
                'data' => null
            ], 500);
        }
    }

    /**
     * Új felhasználó létrehozása (Regisztráció)
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $data = $request->all();
            $data['password'] = Hash::make($request->password);
            $row = User::create($data);

            return response()->json([
                'message' => 'ok',
                'data' => $row
            ], 201, [], JSON_UNESCAPED_UNICODE);
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                return response()->json([
                    'message' => 'The email or name already exists.',
                    'data' => null
                ], 409);
            }
            throw $e;
        }
    }

    /**
     * Egy felhasználó megtekintése ID alapján
     */
    public function show(int $id)
    {
        $row = User::find($id);
        if ($row) {
            return response()->json(['message' => 'OK', 'data' => $row], 200);
        }
        return response()->json(['message' => "Not found id: $id", 'data' => null], 404);
    }

    /**
     * Felhasználó módosítása (Admin által)
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        $row = User::find($id);
        if (!$row) {
            return response()->json(['message' => "Not found id: $id"], 404);
        }

        $this->authorize('updateAdmin', $row);
        $row->update($request->all());

        return response()->json(['message' => 'OK', 'data' => $row], 200);
    }

    /**
     * Felhasználó törlése (Admin által)
     */
    public function destroy(int $id)
    {
        $row = User::find($id);
        if (!$row) {
            return response()->json(['message' => "Not found id: $id"], 404);
        }

        $this->authorize('deleteAdmin', $row);
        $row->delete();

        return response()->json(['message' => 'OK', 'data' => ['id' => $id]], 200);
    }

    /**
     * Saját profil megtekintése
     */
    public function indexSelf(Request $request)
    {
        $user = $request->user();
        $this->authorize('view', $user);
        return response()->json(['message' => 'OK', 'data' => $user], 200);
    }

    /**
     * Saját profil módosítása
     */
    public function updateSelf(UpdateUserSelfRequest $request)
    {
        $user = $request->user();
        $this->authorize('update', $user);
        $user->update($request->validated());

        return response()->json(['message' => 'OK', 'data' => $user], 200);
    }

    /**
     * Saját jelszó módosítása
     */
    public function updatePassword(UpdateUserPasswordRequest $request)
    {
        $user = $request->user();
        $user->update(['password' => Hash::make($request->newpassword)]);

        return response()->json(['message' => 'Jelszó sikeresen módosítva.'], 200);
    }

    /**
     * Saját fiók törlése
     */
    public function destroySelf(Request $request)
    {
        $user = $request->user();
        $this->authorize('delete', $user);
        
        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => "Sikeresen törölted a fiókodat"], 200);
    }
}