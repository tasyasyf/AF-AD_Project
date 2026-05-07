<x-layouts.app title="Edit Class">
@php($isEdit = true)
@include('admin.classes.form', ['action' => route('admin.classes.update', $class), 'method' => 'PUT', 'class' => $class, 'profiles' => $profiles, 'isEdit' => $isEdit])
</x-layouts.app>
