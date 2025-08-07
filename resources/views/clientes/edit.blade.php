@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content-class', 'full-width')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Editar Cliente</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('clientes.update', $cliente) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @php
                            $rut_parts = explode('-', $cliente->rut);
                            $rut_numero = $rut_parts[0];
                            $rut_dv = $rut_parts[1] ?? '';
                        @endphp

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-9">
                                    <label for="rut_numero">RUT:</label>
                                    <input type="text" name="rut_numero" id="rut_numero" class="form-control" value="{{ old('rut_numero', $rut_numero) }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="rut_dv">DV:</label>
                                    <input type="text" name="rut_dv" id="rut_dv" class="form-control" value="{{ old('rut_dv', $rut_dv) }}" maxlength="1" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Razón Social:</label>
                            <input type="text" name="razon_social" class="form-control" value="{{ old('razon_social', $cliente->razon_social) }}" required>
                        </div>

                        <div class="form-group">
                            <label>Giro:</label>
                            <input type="text" name="giro" class="form-control" value="{{ old('giro', $cliente->giro) }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ciudad:</label>
                                    <input type="text" name="ciudad" class="form-control" value="{{ old('ciudad', $cliente->ciudad) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Comuna:</label>
                                    <input type="text" name="comuna" class="form-control" value="{{ old('comuna', $cliente->comuna) }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Dirección Exacta:</label>
                            <input type="text" name="direccion_exacta" class="form-control" value="{{ old('direccion_exacta', $cliente->direccion_exacta) }}">
                        </div>

                        <div class="form-group">
                            <label>Tipo de Vivienda:</label>
                            <select name="tipo_vivienda" class="form-control">
                                <option value="">Seleccione...</option>
                                <option value="Local" {{ old('tipo_vivienda', $cliente->tipo_vivienda) == 'Local' ? 'selected' : '' }}>Local</option>
                                <option value="Casa" {{ old('tipo_vivienda', $cliente->tipo_vivienda) == 'Casa' ? 'selected' : '' }}>Casa</option>
                                <option value="Departamento" {{ old('tipo_vivienda', $cliente->tipo_vivienda) == 'Departamento' ? 'selected' : '' }}>Departamento</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Correo Electrónico <small class="text-muted">(opcional)</small>:</label>
                            <input type="email" name="correo_electronico" class="form-control" value="{{ old('correo_electronico', $cliente->correo_electronico) }}" placeholder="ejemplo@correo.com">
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="tipo_telefono">Tipo de Teléfono:</label>
                                    <select name="tipo_telefono" id="tipo_telefono" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                        <option value="celular" {{ (old('tipo_telefono') ?? ($cliente->telefono && str_starts_with($cliente->telefono, '+569') ? 'celular' : '')) == 'celular' ? 'selected' : '' }}>Celular (+56 9)</option>
                                        <option value="fijo" {{ (old('tipo_telefono') ?? ($cliente->telefono && str_starts_with($cliente->telefono, '+562') ? 'fijo' : '')) == 'fijo' ? 'selected' : '' }}>Teléfono Fijo (+56 2)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="numero_telefono">Número:</label>
                                    <input type="text" name="numero_telefono" class="form-control"
                                        value="{{ old('numero_telefono') ?? preg_replace('/^\\+56[92]/', '', $cliente->telefono) }}"
                                        placeholder="Ej: 987654321" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Orden de Atención:</label>
                            <select name="orden_atencion" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <option value="Lunes" {{ old('orden_atencion', $cliente->orden_atencion) == 'Lunes' ? 'selected' : '' }}>Lunes</option>
                                <option value="Martes" {{ old('orden_atencion', $cliente->orden_atencion) == 'Martes' ? 'selected' : '' }}>Martes</option>
                                <option value="Miercoles" {{ old('orden_atencion', $cliente->orden_atencion) == 'Miercoles' ? 'selected' : '' }}>Miércoles</option>
                                <option value="Jueves" {{ old('orden_atencion', $cliente->orden_atencion) == 'Jueves' ? 'selected' : '' }}>Jueves</option>
                                <option value="Viernes" {{ old('orden_atencion', $cliente->orden_atencion) == 'Viernes' ? 'selected' : '' }}>Viernes</option>
                                <option value="Sabado" {{ old('orden_atencion', $cliente->orden_atencion) == 'Sabado' ? 'selected' : '' }}>Sábado</option>
                                <option value="Domingo" {{ old('orden_atencion', $cliente->orden_atencion) == 'Domingo' ? 'selected' : '' }}>Domingo</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Tipo de Atención:</label>
                            <select name="tipo_atencion" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <option value="Presencial Semanal" {{ old('tipo_atencion', $cliente->tipo_atencion) == 'Presencial Semanal' ? 'selected' : '' }}>Presencial Semanal</option>
                                <option value="Presencial Quincenal" {{ old('tipo_atencion', $cliente->tipo_atencion) == 'Presencial Quincenal' ? 'selected' : '' }}>Presencial Quincenal</option>
                                <option value="Presencial Mensual" {{ old('tipo_atencion', $cliente->tipo_atencion) == 'Presencial Mensual' ? 'selected' : '' }}>Presencial Mensual</option>
                                <option value="Telefónica" {{ old('tipo_atencion', $cliente->tipo_atencion) == 'Telefónica' ? 'selected' : '' }}>Telefónica</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Lista de Precios:</label>
                            <select name="lista_precios" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <option value="Minorista" {{ old('lista_precios', $cliente->lista_precios) == 'Minorista' ? 'selected' : '' }}>Minorista</option>
                                <option value="Especialista" {{ old('lista_precios', $cliente->lista_precios) == 'Especialista' ? 'selected' : '' }}>Especialista</option>
                                <option value="Mayorista" {{ old('lista_precios', $cliente->lista_precios) == 'Mayorista' ? 'selected' : '' }}>Mayorista</option>
                            </select>
                        </div>

                        @php
                            $formasPagoGuardadas = json_decode($cliente->formas_pago, true) ?? [];
                        @endphp

                        <div class="form-group">
                            <label>Formas de Pago:</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="formas_pago[]" value="Efectivo" class="form-check-input"
                                            {{ is_array(old('formas_pago', $formasPagoGuardadas)) && in_array('Efectivo', old('formas_pago', $formasPagoGuardadas)) ? 'checked' : '' }}>
                                        <label class="form-check-label">Efectivo</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="formas_pago[]" value="Transferencia" class="form-check-input"
                                            {{ is_array(old('formas_pago', $formasPagoGuardadas)) && in_array('Transferencia', old('formas_pago', $formasPagoGuardadas)) ? 'checked' : '' }}>
                                        <label class="form-check-label">Transferencia</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="formas_pago[]" value="Tarjeta" class="form-check-input"
                                            {{ is_array(old('formas_pago', $formasPagoGuardadas)) && in_array('Tarjeta', old('formas_pago', $formasPagoGuardadas)) ? 'checked' : '' }}>
                                        <label class="form-check-label">Tarjeta</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php
                            $condicionPagoGuardadas = json_decode($cliente->condicion_pago, true) ?? [];
                        @endphp

                        <div class="form-group">
                            <label>Condición de Pago:</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" name="condicion_pago[]" value="Por pagar" class="form-check-input"
                                            {{ is_array(old('condicion_pago', $condicionPagoGuardadas)) && in_array('Por pagar', old('condicion_pago', $condicionPagoGuardadas)) ? 'checked' : '' }}>
                                        <label class="form-check-label">Por pagar</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" name="condicion_pago[]" value="Pagado" class="form-check-input"
                                            {{ is_array(old('condicion_pago', $condicionPagoGuardadas)) && in_array('Pagado', old('condicion_pago', $condicionPagoGuardadas)) ? 'checked' : '' }}>
                                        <label class="form-check-label">Pagado</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Cliente
                            </button>
                            <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
