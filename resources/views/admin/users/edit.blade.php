<x-layouts.app title="Edit User">
@include('admin.users.form', ['action' => route('admin.users.update', $user), 'method' => 'PUT', 'user' => $user, 'isEdit' => true])
</x-layouts.app>
