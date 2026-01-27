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
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * LOGIN
     */
    public function login(LoginUserRequest $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'message' => 'Invalid email or password',
                'data' => null
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $expirationTime = Carbon::now()->addDays(1);
        $role = (int) $user->role;
        $name = "1day-role:$role";

        switch ($role) {
            case 1: // ADMIN
                $abilities = ['admin', 'customer'];
                break;
            default: // CUSTOMER
                $abilities = ['customer'];
                break;
        }

        $user->token = $user->createToken(
            $name,
            $abilities,
            $expirationTime
        )->plainTextToken;

        return response()->json([
            'message' => 'ok',
            'data' => $user
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * LOGOUT
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
     * LIST USERS (ADMIN)
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
                'message' => 'Server error',
                'data' => null
            ], 500);
        }
    }

    /**
     * CREATE USER
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
                    'message' => 'Duplicate user',
                    'data' => null
                ], 409);
            }
            throw $e;
        }
    }

    /**
     * SHOW USER
     */
    public function show(int $id)
    {
        $row = User::find($id);

        if (!$row) {
            return response()->json([
                'message' => "Not found id: $id",
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'OK',
            'data' => $row
        ], 200);
    }

    /**
     * UPDATE USER (ADMIN)
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        $row = User::find($id);

        if (!$row) {
            return response()->json([
                'message' => "Patch error. Not found id: $id",
                'data' => null
            ], 404);
        }

        $this->authorize('updateAdmin', $row);

        $row->update($request->all());

        return response()->json([
            'message' => 'OK',
            'data' => $row
        ], 200);
    }

    /**
     * DELETE USER (ADMIN)
     */
    public function destroy(int $id)
    {
        $row = User::find($id);

        if (!$row) {
            return response()->json([
                'message' => "Delete error. Not found id: $id",
                'data' => null
            ], 404);
        }

        $this->authorize('deleteAdmin', $row);

        $row->delete();

        return response()->json([
            'message' => 'OK',
            'data' => ['id' => $id]
        ], 200);
    }

    /**
     * SELF - GET
     */
    public function indexSelf(Request $request)
    {
        $user = $request->user();
        $this->authorize('view', $user);

        return response()->json([
            'message' => 'OK',
            'data' => $user
        ], 200);
    }

    /**
     * SELF - UPDATE
     */
    public function updateSelf(UpdateUserSelfRequest $request)
    {
        $user = $request->user();
        $this->authorize('update', $user);

        $user->update($request->validated());

        return response()->json([
            'message' => 'OK',
            'data' => $user
        ], 200);
    }

    /**
     * SELF - PASSWORD
     */
    public function updatePassword(UpdateUserPasswordRequest $request)
    {
        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->newpassword)
        ]);

        return response()->json([
            'message' => 'Password updated'
        ], 200);
    }

    /**
     * SELF - DELETE
     */
    public function destroySelf(Request $request)
    {
        $user = $request->user();
        $this->authorize('delete', $user);

        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'message' => 'Account deleted'
        ], 200);
    }
}
