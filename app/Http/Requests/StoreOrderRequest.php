<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Autorisation d'accès à cette requête.
     * Authentification déjà vérifiée par le middleware auth:sanctum.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation des champs.
     */
    public function rules(): array
    {
        return [
            'client_name' => ['required', 'string', 'min:2', 'max:255'],
            'client_phone' => ['required', 'string', 'min:8', 'max:20', 'regex:/^[\d\s+\-().]+$/'],
            'product_description' => ['required', 'string', 'min:2', 'max:500'],
            'amount' => ['required', 'integer', 'min:1', 'max:99999999'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Messages d'erreur personnalisés (en français, pour Awa).
     */
    public function messages(): array
    {
        return [
            'client_name.required' => 'Le nom du client est obligatoire.',
            'client_name.min' => 'Le nom du client est trop court.',
            'client_phone.required' => 'Le numéro WhatsApp est obligatoire.',
            'client_phone.regex' => 'Le numéro WhatsApp contient des caractères invalides.',
            'client_phone.min' => 'Le numéro WhatsApp est trop court.',
            'product_description.required' => 'La description du produit est obligatoire.',
            'amount.required' => 'Le montant est obligatoire.',
            'amount.integer' => 'Le montant doit être un nombre entier.',
            'amount.min' => 'Le montant doit être supérieur à 0 FCFA.',
            'amount.max' => 'Le montant est trop élevé.',
        ];
    }

    /**
     * Normalisation des données AVANT validation.
     * Utile pour nettoyer les espaces, standardiser les formats.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'client_name' => trim($this->client_name ?? ''),// Nettoie les espaces en debut/fin de chaine
            'client_phone' => trim($this->client_phone ?? ''),// Nettoie les espaces en debut/fin de chaine
            'product_description' => trim($this->product_description ?? ''),// Nettoie les espaces en debut/fin de chaine
        ]);
    }
}
