@php($t = $ticket ?? null)
<h2>Customer</h2>
<div class="row">
    <div><label for="customer_name">Name</label>
        <input id="customer_name" name="customer_name" value="{{ old('customer_name', $t?->customer->name) }}" required></div>
    <div><label for="customer_phone">Phone</label>
        <input id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $t?->customer->phone) }}" required></div>
    <div><label for="customer_email">Email (optional)</label>
        <input id="customer_email" name="customer_email" type="email" value="{{ old('customer_email', $t?->customer->email) }}"></div>
</div>

<h2 style="margin-top:20px">Device</h2>
<div class="row">
    <div><label for="device_type">Device type</label>
        <select id="device_type" name="device_type" required>
            @foreach (['Laptop', 'Desktop', 'Phone', 'Tablet', 'Printer', 'Other'] as $type)
                <option value="{{ $type }}" @selected(old('device_type', $t?->device_type) === $type)>{{ $type }}</option>
            @endforeach
        </select></div>
    <div><label for="brand">Brand</label>
        <input id="brand" name="brand" value="{{ old('brand', $t?->brand) }}" required></div>
    <div><label for="model">Model (optional)</label>
        <input id="model" name="model" value="{{ old('model', $t?->model) }}"></div>
    <div><label for="serial_number">Serial number (optional)</label>
        <input id="serial_number" name="serial_number" value="{{ old('serial_number', $t?->serial_number) }}"></div>
</div>

<label for="problem_description">Problem description</label>
<textarea id="problem_description" name="problem_description" required>{{ old('problem_description', $t?->problem_description) }}</textarea>
