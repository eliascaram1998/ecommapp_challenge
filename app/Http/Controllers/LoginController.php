<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LoginController extends BaseController
{

    /**
     * Handle the login request.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'required|string',
        ], [
            'password.required' => 'El campo contraseña es obligatorio.',
        ]);

        if ($this->checkPassword($request->input('password'))) {
            $request->session()->put('isAuthenticated', true);
            return redirect()->route('product.index')->with('success', 'Inicio de sesión exitoso.');
        }

        return redirect()->back()->withErrors(['password' => 'Contraseña incorrecta.'])->withInput();
    }

    /**
     * Handle the logout request.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        $request->session()->put('isAuthenticated', false);
        return redirect()->route('product.index')->with('success', 'Se ha cerrado correctamente la sesión.');
    }

    /**
     * Check if the provided password is valid.
     *
     * @param string $password
     * @return bool
     */
    private function checkPassword(string $password): bool
    {
        return $password === 'testing';
    }
}
