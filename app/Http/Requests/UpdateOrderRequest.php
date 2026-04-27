<?php

namespace App\Http\Requests;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Autorisation d'accès à cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation.
     * Tous les champs sont "sometimes" : on valide uniquement s'ils sont présents.
     * Permet les updates partiels (PATCH).
     */
    public function rules(): array
    {
        return [
            'client_name' => ['sometimes', 'required', 'string', 'min:2', 'max:255'],
            'client_phone' => ['sometimes', 'required', 'string', 'min:8', 'max:20', 'regex:/^[\d\s+\-().]+$/'],
            'product_description' => ['sometimes', 'required', 'string', 'min:2', 'max:500'],
            'amount' => ['sometimes', 'required', 'integer', 'min:1', 'max:99999999'],
            'status' => ['sometimes', 'required', Rule::enum(OrderStatus::class)],
            'notes' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Messages d'erreur personnalisés.
     */
    public function messages(): array
    {
        return [
            'client_name.min' => 'Le nom du client est trop court.',
            'client_phone.regex' => 'Le numéro WhatsApp contient des caractères invalides.',
            'amount.integer' => 'Le montant doit être un nombre entier.',
            'amount.min' => 'Le montant doit être supérieur à 0 FCFA.',
            'status' => 'Le statut fourni n\'est pas valide.',
        ];
    }

    /**
     * Normalisation AVANT validation.
     */
    protected function prepareForValidation(): void
    {
        $data = [];// one ne modifie que les champs presents dans la requete pour eviter d'ecraser des valeurs deja en DB avec des chaines vides ou des valeurs par defaut

        if ($this->has('client_name')) {// si le champ client_name est present dans la requete, on le nettoie et on l'ajoute aux donnees a valider
            $data['client_name'] = trim($this->client_name);
        }
        if ($this->has('client_phone')) {
            $data['client_phone'] = trim($this->client_phone);
        }
        if ($this->has('product_description')) {
            $data['product_description'] = trim($this->product_description);
        }

        $this->merge($data);// on merge les donnees nettoyees dans la requette pour qu;elle soit validee avec les valeurs nettoyees et pas les valeurs brutes de la requete
    }
}
