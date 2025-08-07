<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rut_numero' => 'required|numeric',
            'rut_dv' => 'required|alpha_num|max:1',
            'razon_social' => 'required|string|max:255',
            'giro' => 'required|string|max:255',
            'ciudad' => 'nullable|string|max:255',
            'comuna' => 'nullable|string|max:255',
            'direccion_exacta' => 'nullable|string|max:255',
            'tipo_vivienda' => 'nullable|in:Local,Casa,Departamento',
            'correo_electronico' => 'nullable|email|max:255',
            'tipo_telefono' => 'required|in:celular,fijo',
            'numero_telefono' => 'required|numeric|digits_between:7,9',
            'orden_atencion' => 'required|string|max:255',
            'tipo_atencion' => 'required|string|max:255',
            'lista_precios' => 'required|string|max:255',
            'formas_pago' => 'required|array|min:1',
            'formas_pago.*' => 'in:Efectivo,Transferencia,Tarjeta',
            'condicion_pago' => 'required|array|min:1',
            'condicion_pago.*' => 'in:Por pagar,Pagado',
        ];
    }

    public function messages(): array
    {
        return [
            'rut_numero.required' => 'El número de RUT es obligatorio.',
            'rut_dv.required' => 'El dígito verificador del RUT es obligatorio.',
            'correo_electronico.email' => 'Debe ingresar un correo electrónico válido.',
            'formas_pago.min' => 'Debe seleccionar al menos una forma de pago.',
            'condicion_pago.min' => 'Debe seleccionar al menos una condición de pago.',
        ];
    }

    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'rut_numero' => $this->rut_numero ? (int) $this->rut_numero : null,
        ]);
    }

    /**
     * Get the validated data with processed fields
     */
    public function getProcessedData(): array
    {
        $validated = $this->validated();
        
        // Formatear teléfono
        $prefijo = $validated['tipo_telefono'] === 'celular' ? '+569' : '+562';
        $validated['telefono'] = $prefijo . $validated['numero_telefono'];
        
        // Formatear RUT
        $validated['rut'] = $validated['rut_numero'] . '-' . strtoupper($validated['rut_dv']);
        
        // Convertir arrays a JSON
        $validated['formas_pago'] = json_encode($validated['formas_pago']);
        $validated['condicion_pago'] = json_encode($validated['condicion_pago']);
        
        // Remover campos temporales
        unset($validated['rut_numero'], $validated['rut_dv'], $validated['tipo_telefono'], $validated['numero_telefono']);
        
        return $validated;
    }
}
