@extends('layouts.app')

@section('title', 'Crear Cliente')

@section('content-class', 'full-width')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Crear Nuevo Cliente</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('clientes.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-9">
                                    <label for="rut_numero">RUT:</label>
                                    <input type="text" name="rut_numero" id="rut_numero" class="form-control" value="{{ old('rut_numero') }}" placeholder="Ej: 12345678" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="rut_dv">DV:</label>
                                    <input type="text" name="rut_dv" id="rut_dv" class="form-control" value="{{ old('rut_dv') }}" placeholder="9" maxlength="1" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Razón Social:</label>
                            <input type="text" name="razon_social" class="form-control" value="{{ old('razon_social') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Giro:</label>
                            <input type="text" name="giro" class="form-control" value="{{ old('giro') }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ciudad:</label>
                                    <input type="text" name="ciudad" class="form-control" value="{{ old('ciudad') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Comuna:</label>
                                    <input type="text" name="comuna" class="form-control" value="{{ old('comuna') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Dirección Exacta:</label>
                            <input type="text" name="direccion_exacta" class="form-control" value="{{ old('direccion_exacta') }}">
                        </div>

                        <div class="form-group">
                            <label>Tipo de Vivienda:</label>
                            <select name="tipo_vivienda" class="form-control">
                                <option value="">Seleccione...</option>
                                <option value="Local" {{ old('tipo_vivienda') == 'Local' ? 'selected' : '' }}>Local</option>
                                <option value="Casa" {{ old('tipo_vivienda') == 'Casa' ? 'selected' : '' }}>Casa</option>
                                <option value="Departamento" {{ old('tipo_vivienda') == 'Departamento' ? 'selected' : '' }}>Departamento</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Correo Electrónico <small class="text-muted">(opcional)</small>:</label>
                            <input type="email" name="correo_electronico" class="form-control" value="{{ old('correo_electronico') }}" placeholder="ejemplo@correo.com">
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="tipo_telefono">Tipo de Teléfono:</label>
                                    <select name="tipo_telefono" id="tipo_telefono" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                        <option value="celular" {{ old('tipo_telefono') == 'celular' ? 'selected' : '' }}>Celular</option>
                                        <option value="fijo" {{ old('tipo_telefono') == 'fijo' ? 'selected' : '' }}>Teléfono Fijo</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="numero_telefono">Número:</label>
                                    <input type="text" name="numero_telefono" id="numero_telefono" class="form-control" value="{{ old('numero_telefono') }}" placeholder="Ej: 987654321" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Orden de Atención:</label>
                            <select name="orden_atencion" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <option value="Lunes">Lunes</option>
                                <option value="Martes">Martes</option>
                                <option value="Miercoles">Miércoles</option>
                                <option value="Jueves">Jueves</option>
                                <option value="Viernes">Viernes</option>
                                <option value="Sabado">Sábado</option>
                                <option value="Domingo">Domingo</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Tipo de Atención:</label>
                            <select name="tipo_atencion" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <option value="Presencial Semanal">Presencial Semanal</option>
                                <option value="Presencial Quincenal">Presencial Quincenal</option>
                                <option value="Presencial Mensual">Presencial Mensual</option>
                                <option value="Telefónica">Telefónica</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Lista de Precios:</label>
                            <select name="lista_precios" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <option value="Minorista">Minorista</option>
                                <option value="Especialista">Especialista</option>
                                <option value="Mayorista">Mayorista</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Formas de Pago:</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="formas_pago[]" value="Efectivo" class="form-check-input"
                                            {{ is_array(old('formas_pago')) && in_array('Efectivo', old('formas_pago')) ? 'checked' : '' }}>
                                        <label class="form-check-label">Efectivo</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="formas_pago[]" value="Transferencia" class="form-check-input"
                                            {{ is_array(old('formas_pago')) && in_array('Transferencia', old('formas_pago')) ? 'checked' : '' }}>
                                        <label class="form-check-label">Transferencia</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="formas_pago[]" value="Tarjeta" class="form-check-input"
                                            {{ is_array(old('formas_pago')) && in_array('Tarjeta', old('formas_pago')) ? 'checked' : '' }}>
                                        <label class="form-check-label">Tarjeta</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Condición de Pago:</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" name="condicion_pago[]" value="Por pagar" class="form-check-input"
                                            {{ is_array(old('condicion_pago')) && in_array('Por pagar', old('condicion_pago')) ? 'checked' : '' }}>
                                        <label class="form-check-label">Por pagar</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" name="condicion_pago[]" value="Pagado" class="form-check-input"
                                            {{ is_array(old('condicion_pago')) && in_array('Pagado', old('condicion_pago')) ? 'checked' : '' }}>
                                        <label class="form-check-label">Pagado</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Crear Cliente
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
