<x-layouts.app title="Create User">
@include('admin.users.form', ['action' => route('admin.users.store'), 'method' => null, 'user' => null, 'isEdit' => false])
</x-layouts.app>
