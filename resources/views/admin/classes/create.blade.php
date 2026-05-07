<x-layouts.app title="Create Class">
@php($isEdit = false)
@include('admin.classes.form', ['action' => route('admin.classes.store'), 'method' => null, 'class' => null, 'profiles' => $profiles, 'isEdit' => $isEdit])
</x-layouts.app>
