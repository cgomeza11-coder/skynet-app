<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Cliente;

new class extends Component
{
    public $cliente_id, $nombre_completo, $correo_electronico, $telefono, $direccion;
    public $isEdit = false;

    public function with(): array
    {
        return [
            'clientes' => Cliente::with('cuentas')->latest()->get()
        ];
    }

    public function resetInput()
    {
        $this->reset(['cliente_id', 'nombre_completo', 'correo_electronico', 'telefono', 'direccion', 'isEdit']);
    }

    public function store()
    {
        $this->validate([
            'nombre_completo' => 'required|string|max:255',
            'correo_electronico' => 'required|email|unique:clientes,correo_electronico,' . $this->cliente_id,
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
        ]);

        Cliente::updateOrCreate(['id' => $this->cliente_id], [
            'nombre_completo' => $this->nombre_completo,
            'correo_electronico' => $this->correo_electronico,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
        ]);

        session()->flash('message', $this->isEdit ? 'Cliente actualizado correctamente.' : 'Cliente registrado correctamente.');
        $this->resetInput();
    }

    public function edit($id)
    {
        $cliente = Cliente::findOrFail($id);
        $this->cliente_id = $id;
        $this->nombre_completo = $cliente->nombre_completo;
        $this->correo_electronico = $cliente->correo_electronico;
        $this->telefono = $cliente->telefono;
        $this->direccion = $cliente->direccion;
        $this->isEdit = true;
    }

    public function delete($id)
    {
        Cliente::find($id)->delete();
        session()->flash('message', 'Cliente eliminado correctamente.');
    }
};
?>

<div>
    <h3 class="mb-4">Gestión de Clientes</h3>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Formulario -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    {{ $isEdit ? 'Editar Cliente' : 'Nuevo Cliente' }}
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="store">
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control" wire:model="nombre_completo">
                            @error('nombre_completo') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Correo Electrónico *</label>
                            <input type="email" class="form-control" wire:model="correo_electronico">
                            @error('correo_electronico') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Teléfono (Opcional)</label>
                            <input type="text" class="form-control" wire:model="telefono">
                            @error('telefono') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dirección (Opcional)</label>
                            <input type="text" class="form-control" wire:model="direccion">
                            @error('direccion') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                {{ $isEdit ? 'Actualizar' : 'Guardar' }}
                            </button>
                            @if($isEdit)
                                <button type="button" wire:click="resetInput" class="btn btn-secondary">Cancelar</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla de Clientes -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Listado de Clientes</div>
                <div class="card-body table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Teléfono</th>
                                <th>Cuentas</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clientes as $cliente)
                                <tr>
                                    <td>{{ $cliente->id }}</td>
                                    <td>
                                        <strong>{{ $cliente->nombre_completo }}</strong><br>
                                        <small class="text-muted">{{ $cliente->direccion ?? 'Sin dirección' }}</small>
                                    </td>
                                    <td>{{ $cliente->correo_electronico }}</td>
                                    <td>{{ $cliente->telefono ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-info text-dark">{{ $cliente->cuentas->count() }} cuenta(s)</span>
                                    </td>
                                    <td>
                                        <button wire:click="edit({{ $cliente->id }})" class="btn btn-sm btn-warning">Editar</button>
                                        <button wire:click="delete({{ $cliente->id }})" wire:confirm="¿Seguro que deseas eliminar este cliente y sus cuentas?" class="btn btn-sm btn-danger">Eliminar</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No hay clientes registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>